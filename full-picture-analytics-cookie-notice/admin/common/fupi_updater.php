<?php

class Fupi_Updater {

    private const LOCK_OPTION = 'fupi_updater_lock';
    private const FAILURE_OPTION = 'fupi_updater_failures';
    private const RESTORE_SAFETY_OPTION = 'fupi_backup_restore_safety';
    private const MAX_FAILURES = 3;
    private const MINIMUM_LOCK_TTL = 300;
    private const UNLIMITED_EXECUTION_LOCK_TTL = 900;
    private $prev_version = false;
    private $regenerate_cdb = false;
    private $o = []; // Holds the options currently being migrated or restored
    private $opts_to_remove = [];
    private $lock_token = false;
    private $migration_failed = false;
    private $deferred_work = [];

    /*
     * Attention! The fupi_tools must use the boolean true value to enable a module.
     * Example: $this->o['fupi_tools']['cook'] = true.
     */

    /**
     * Runs required migrations or restores a supplied option backup.
     *
     * @param bool  $restore_backup True to restore $backup_data; false to run a normal migration.
     * @param array $backup_data Backup values keyed by option name. Use an empty array for a normal migration.
     * @param bool  $force_retry True to clear the failure counter before retrying; false to use the normal retry rules.
     * @return bool True when critical updater work succeeds; false when it fails or another updater owns the lock.
     */
    public function run( $restore_backup = false, $backup_data = [], $force_retry = false ) {

        $this->migration_failed = false;
        $this->o = [];
        $this->opts_to_remove = [];
        $this->regenerate_cdb = false;
        $this->deferred_work = [];
        $attempt_started = false;
        $should_record_failure = false;

        try {

            if ( ! $this->acquire_lock() ) return false;

            // migration_failed = “the migration work attempted in this invocation failed
            // Keep the attempt failed until all critical work succeeds;
            $this->migration_failed = true;

            // if we are restioring data from backup >> set the safety marker
            if ( $restore_backup ) {
                
                if ( ! $this->set_restore_safety() ) throw new RuntimeException( 'Restore safety state could not be saved.' );

            // if we are updating settings >> prepare the failure counter
            } else {

                $failure_record = $this->prepare_failure_record();
                $stored_versions = get_option( 'fupi_versions' );
                $settings_current = is_array( $stored_versions ) && ( $stored_versions[1] ?? null ) === FUPI_VERSION;

                // delete failure record after settings update
                if (
                    $settings_current &&
                    $failure_record
                ) {
                    if ( ! $this->delete_failure_record() ) $this->log_cleanup_failure( 'failure state' );
                    $failure_record = false;
                }

                // Clear a requested retry, or stop when normal migration failures reached the limit.
                if ( $force_retry ) {

                    if ( ! $this->delete_failure_record() ) throw new RuntimeException( 'Failure state could not be reset.' );

                } else if (
                    $failure_record &&
                    $failure_record['failures'] >= self::MAX_FAILURES
                ) {

                    $this->migration_failed = false;
                    return false; // returns from "run()"
                }

                $should_record_failure = true;
            }

            $attempt_started = true;

            $this->run_best_effort( [ $this, 'clear_cache' ] );

            if ( $restore_backup ) {
                if (
                    empty( $backup_data ) ||
                    ! $this->get_options_from_backup( $backup_data )
                ) return false;
                
                // Repair the version marker only when the backup came from before version 9.0.
                $this->set_fupi_versions();
            }

            if ( ! $this->set_default_options() ) return false;

            $is_fresh_install = $this->check_fupi_versions();
            $options_changed = false;

            // Apply each migration whose required starting version is newer than the stored version.
            if ( version_compare( $this->prev_version, FUPI_VERSION ) == -1 ) {

                $options_changed = true;

                foreach ( [
                    'update_to_8_5' => '8.5.0',
                    'update_to_8_5_1' => '8.5.1',
                    'update_to_9_0' => '8.5.3.13',
                    'update_to_9_2' => '9.1.10',
                    'update_to_9_3_0' => '9.2.0.9',
                    'update_to_9_4_0' => '9.3.2.8',
                    'update_to_10_0_0' => '9.4.3.12',
                    'update_to_10_0_1' => '10.0.0.1',
                    'update_to_11' => '10.1.2.34',
                ] as $migration => $version ) {
                    if ( version_compare( $this->prev_version, $version ) !== -1 ) continue;
                    $this->$migration();
                }
            }

            // Save restored or migrated settings after every required migration has completed.
            if (
                $restore_backup ||
                $options_changed
            ) {

                // Put consent settings first without duplicating them in the options array.
                if ( isset( $this->o['fupi_cook'] ) ) $this->o = [ 'fupi_cook' => $this->o['fupi_cook'] ] + $this->o;

                // The consent destination must be written before its legacy source is removed.
                add_filter( 'fupi_updating_many_options', '__return_true' );

                try {

                    if ( $restore_backup ) {
                        $returned_status = $this->restore_options_from_backup();
                    } else {
                        $returned_status = $this->update_wp_options();
                    }

                } finally {
                    remove_filter( 'fupi_updating_many_options', '__return_true' );
                }

                if ( ! $returned_status ) return false;
                if ( ! $this->save_current_version() ) return false;

                $should_record_failure = false;

                return $this->finish_success( $restore_backup );

            }
            // Save the initial version when this is a fresh installation.
            elseif ( $is_fresh_install ) {
                if ( ! $this->save_current_version() ) return false;
                $should_record_failure = false;

                return $this->finish_success( false );

            }
            // Update only the stored version when the site is rolling back to an older plugin release.
            elseif ( $this->prev_version !== FUPI_VERSION ) {
                if ( ! $this->save_current_version() ) return false;
                $should_record_failure = false;

                return $this->finish_success( false );
            }

            $should_record_failure = false;

            return $this->finish_success( $restore_backup );

        } catch ( Throwable $error ) {
            $this->log_failure( $error );
            return false;

        } finally {

            // Record the failed attempt without replacing the original updater error.
            if (
                $attempt_started &&
                $should_record_failure &&
                $this->migration_failed
            ) {

                try {
                    $this->record_failure();
                } catch ( Throwable $error ) {
                    $this->log_failure( $error );
                }
            }

            try {
                $this->release_lock();
            } catch ( Throwable $error ) {
                $this->log_failure( $error );

                return false;
            }
        }
    }

    /**
     * Reports whether the most recent migration attempt failed.
     *
     * @return bool True when critical work failed during the most recent attempt.
     */
    public function has_failed() {
        return $this->migration_failed;
    }

    /**
     * Reports whether normal migrations are exhausted for this plugin version.
     *
     * @return bool True when the current version has reached the maximum failure count.
     */
    public function is_exhausted() {
        $record = $this->read_failure_record();

        return $record &&
            $record['target_version'] === FUPI_VERSION &&
            $record['failures'] >= self::MAX_FAILURES;
    }

    /**
     * Records safe details about an updater failure without exposing secrets.
     *
     * @param Throwable $error The exception or error caught by the updater.
     */
    private function log_failure( Throwable $error ) {
        $this->migration_failed = true;

        // Do not log messages, traces, arguments, or database errors because they may contain secrets.
        error_log( sprintf( 'WP Full Picture updater failed: %s at %s:%d', get_class( $error ), basename( $error->getFile() ), $error->getLine() ) );
    }

    /**
     * Reads failure metadata and treats malformed data as a terminal failure.
     *
     * @return array|false The failure record, or false when no record exists.
     */
    private function read_failure_record() {

        $record = $this->read_private_option( self::FAILURE_OPTION, $exists );

        if ( ! $exists ) return false;

        if (
            ! is_array( $record ) ||
            count( $record ) !== 2 ||
            ! array_key_exists( 'target_version', $record ) ||
            ! array_key_exists( 'failures', $record ) ||
            ! is_string( $record['target_version'] ) ||
            ! is_int( $record['failures'] ) ||
            $record['failures'] < 0
        ) {
            error_log( 'WP Full Picture updater found malformed failure metadata.' );
            return [ 'target_version' => FUPI_VERSION, 'failures' => self::MAX_FAILURES ];
        }

        return $record;
    }

    /**
     * Removes failure metadata belonging to a different plugin release.
     *
     * @return array|false The current failure record, or false after stale data is removed.
     */
    private function prepare_failure_record() {

        $record = $this->read_failure_record();

        if (
            $record &&
            $record['target_version'] !== FUPI_VERSION
        ) {
            if ( ! $this->delete_failure_record() ) throw new RuntimeException( 'Stale failure state could not be cleared.' );
            return false;
        }

        return $record;
    }

    /**
     * Persists the next normal migration failure and sends the terminal notification when needed.
     *
     * @throws RuntimeException When the failure record cannot be saved.
     */
    private function record_failure() {

        $record = $this->read_failure_record();
        $failures = $record && $record['target_version'] === FUPI_VERSION ? $record['failures'] : 0;
        $failures = min( self::MAX_FAILURES, $failures + 1 );

        if ( ! $this->save_private_option( self::FAILURE_OPTION, [
            'target_version' => FUPI_VERSION,
            'failures' => $failures,
        ] ) ) {
            throw new RuntimeException( 'Failure state could not be saved.' );
        }

        if ( self::MAX_FAILURES === $failures ) {
            $this->run_best_effort( [ $this, 'clear_cache' ] );
            $this->run_best_effort( [ $this, 'send_exhaustion_email' ] );
        }
    }

    /**
     * Saves internal state without adding it to WordPress autoloaded options.
     *
     * WordPress add_option() can report that it did not add a value when an option already exists.
     * This method checks for that case and uses update_option() as a safe fallback.
     *
     * @param string $option_name Internal WordPress option name to save.
     * @param mixed  $value Value to store in the option.
     * @return bool True when the value is stored or already matches; false on a database error.
     */
    private function save_private_option( $option_name, $value ) {

        global $wpdb;

        $wpdb->last_error = '';

        $this->read_private_option( $option_name, $exists );

        if ( ! $exists ) {
            if ( add_option( $option_name, $value, '', false ) ) return true;
            if ( ! empty( $wpdb->last_error ) ) return false;
        }

        return $this->update_wp_option( $option_name, $value );
    }

    /**
     * Removes the stored failure metadata when it is no longer needed.
     *
     * @return bool True when the record is absent or was deleted; false on a database error.
     */
    private function delete_failure_record() {
        $this->read_private_option( self::FAILURE_OPTION, $exists );
        return ! $exists || $this->delete_wp_option( self::FAILURE_OPTION );
    }

    /**
     * Records that a backup restore is in progress.
     *
     * @return bool True when the restore safety marker is saved; false on a database error.
     */
    private function set_restore_safety() {
        return $this->save_private_option( self::RESTORE_SAFETY_OPTION, 1 );
    }

    /**
     * Removes the marker that protects an in-progress backup restore.
     *
     * @return bool True when the marker is absent or was deleted; false on a database error.
     */
    private function clear_restore_safety() {
        $this->read_private_option( self::RESTORE_SAFETY_OPTION, $exists );
        return ! $exists || $this->delete_wp_option( self::RESTORE_SAFETY_OPTION );
    }

    /**
     * Reads an internal option and also reports whether the option exists.
     *
     * WordPress get_option() returns false for both a stored false value and a missing option.
     * Passing a unique default object lets this wrapper tell those two cases apart.
     *
     * @param string $option_name Internal WordPress option name to read.
     * @param bool   &$exists Output variable set to true when the option exists, including a stored false value.
     * @return mixed The stored option value, or the unique missing-value object when the option is absent.
     */
    private function read_private_option( $option_name, &$exists ) {

        $missing_option = new stdClass();
        $value = get_option( $option_name, $missing_option );
        $exists = $value !== $missing_option;

        return $value;
    }

    /**
     * Completes internal state and best-effort work after all critical writes succeed.
     *
     * @param bool $restore_backup True when a restore safety marker must be cleared; false for a normal update.
     * @return bool True when cleanup is complete; false when a restore marker could not be removed.
     */
    private function finish_success( $restore_backup ) {

        $this->migration_failed = false;
        $restore_safety_cleared = true;

        // Clear the marker associated with the completed operation.
        if ( $restore_backup ) {
            if ( ! $this->clear_restore_safety() ) {
                $this->log_cleanup_failure( 'restore safety state' );
                $restore_safety_cleared = false;
            }
        } elseif ( ! $this->delete_failure_record() ) {
            $this->log_cleanup_failure( 'failure state' );
        }

        $this->run_best_effort( [ $this, 'send_plugin_settings_to_cdb' ] );

        foreach ( $this->deferred_work as $callback ) {
            $this->run_best_effort( $callback );
        }

        $this->run_best_effort( [ $this, 'clear_cache' ] );

        // A restore marker that remains active must make the administrator-facing result fail.
        if ( ! $restore_safety_cleared ) {
            $this->migration_failed = true;

            return false;
        }

        return true;
    }

    /**
     * Logs failed cleanup without invalidating settings that were already saved.
     *
     * @param string $state Human-readable name of the internal state that could not be cleared.
     */
    private function log_cleanup_failure( $state ) {
        error_log( sprintf( 'WP Full Picture updater could not clear %s after a successful update.', $state ) );
    }

    /**
     * Runs one non-critical operation without changing the migration result.
     *
     * @param callable|array $callback Function or WordPress-style callable to run.
     */
    private function run_best_effort( $callback ) {
        try {
            call_user_func( $callback );
        } catch ( Throwable $error ) {
            error_log( sprintf( 'WP Full Picture updater deferred work failed: %s at %s:%d', get_class( $error ), basename( $error->getFile() ), $error->getLine() ) );
        }
    }

    /**
     * Queues non-critical work until the updater has committed its changes.
     *
     * @param callable|array $callback Function or WordPress-style callable to run later.
     */
    private function queue_deferred_work( $callback ) {
        $this->deferred_work[] = $callback;
    }

    /** Sends the one-time warning after normal updates fail three times. */
    private function send_exhaustion_email() {
        $subject = sprintf( 'WP Full Picture settings update failed on %s', get_bloginfo( 'name' ) );
        $message = sprintf(
            "Hello,\n\nWP Full Picture could not update its settings to version %1\$s after three attempts. As a safety measure, public WP Full Picture functionality is currently disabled, while page caching has resumed. Tracking tools that are not loaded with WP Full Picture may continue to work, but they no longer receive WP Full Picture consent protection.\n\nnPlease log in to your WordPress admin area and use the Retry update button:\n%2\$s\n\nAfter the update succeeds, manually clear your caches (caching plugins, host, reverse-proxy, or CDN caches).\n\nIf the update keeps failing, please contact WP Full Picture support at https://wpfullpicture.com/contact. Include a copy of your WP Full Picture settings backup and, if WordPress debugging is enabled, the relevant information from the WordPress debug log (wp-content/debug.log) so we can investigate.\n\nThis is an automated message sent from %3\$s.",
            FUPI_VERSION,
            wp_login_url(),
            home_url( '/' )
        );

        wp_mail( get_option( 'admin_email' ), $subject, $message );
    }

    /**
     * Acquires an option-based lock for this updater instance.
     *
     * @return bool True when this instance owns the lock; false when another valid lock exists.
     * @throws RuntimeException When WordPress reports a database error while reading or writing the lock.
     */
    private function acquire_lock() {

        global $wpdb;

        // Save this token so release_lock() can remove only this instance's lock.
        $this->lock_token = uniqid( 'fupi_', true );

        $lock = [
            'token' => $this->lock_token,
            'expires' => time() + $this->get_lock_ttl(),
        ];

        $wpdb->last_error = '';

        if ( add_option( self::LOCK_OPTION, $lock, '', false ) ) return true;
        if ( ! empty( $wpdb->last_error ) ) throw new RuntimeException( 'Lock insertion failed.' );

        $existing_lock = get_option( self::LOCK_OPTION );

        if ( ! empty( $wpdb->last_error ) ) throw new RuntimeException( 'Lock lookup failed.' );

        // Leave a lock with a future expiry time untouched because another updater may still be running.
        if (
            is_array( $existing_lock ) &&
            ! empty( $existing_lock['expires'] ) &&
            $existing_lock['expires'] >= time()
        ) {
            $this->lock_token = false;

            return false;
        }

        $wpdb->last_error = '';
        $deleted = $this->delete_lock( $existing_lock );

        if ( ! empty( $wpdb->last_error ) ) throw new RuntimeException( 'Expired lock deletion failed.' );

        if ( $deleted ) {

            // Try to acquire the lock again after removing the expired value.
            $wpdb->last_error = '';

            if ( add_option( self::LOCK_OPTION, $lock, '', false ) ) return true;
            if ( ! empty( $wpdb->last_error ) ) throw new RuntimeException( 'Lock insertion failed.' );
        }

        $this->lock_token = false;

        return false;
    }

    /**
     * Determines a lock lifetime that exceeds the PHP execution limit.
     *
     * @return int Lock lifetime in seconds.
     */
    private function get_lock_ttl() {
        $max_execution_time = (int) ini_get( 'max_execution_time' );

        if ( $max_execution_time <= 0 ) return self::UNLIMITED_EXECUTION_LOCK_TTL;

        return max( self::MINIMUM_LOCK_TTL, $max_execution_time + 60 );
    }

    /** Releases the option lock when this instance owns it. */
    private function release_lock() {

        if ( ! $this->lock_token ) return;

        $lock = get_option( self::LOCK_OPTION );

        if (
            is_array( $lock ) &&
            isset( $lock['token'] ) &&
            $lock['token'] === $this->lock_token
        ) {
            $this->delete_lock( $lock );
        }

        $this->lock_token = false;
    }

    /**
     * Deletes a lock only when the database value still matches the supplied lock.
     *
     * This compare-and-delete query prevents one updater from deleting a newer lock created by another updater.
     *
     * @param mixed $lock Lock value read from the options table, normally an array with token and expiry values.
     * @return bool True when the matching lock was deleted; false when it was absent or did not match.
     */
    private function delete_lock( $lock ) {

        global $wpdb;

        $deleted = $wpdb->delete(
            $wpdb->options,
            [
                'option_name' => self::LOCK_OPTION,
                'option_value' => maybe_serialize( $lock ),
            ],
            [ '%s', '%s' ]
        );

        if ( $deleted ) wp_cache_delete( self::LOCK_OPTION, 'options' );

        return (bool) $deleted;
    }

    /**
     * Ensures the plugin tools option exists before migrations begin.
     *
     * @return bool True when the option exists after this method runs; false on a database error.
     */
    private function set_default_options() {
        $tools_o = get_option( 'fupi_tools' );

        if ( empty( $tools_o ) ) add_option( 'fupi_tools', [] );

        return get_option( 'fupi_tools' ) !== false;
    }

    /** Safely load, validate, and save plugin options used by migrations. */

    /**
     * Loads requested plugin options into the migration state.
     *
     * @param array $opt_names_arr Option names to load, such as fupi_tools or fupi_versions.
     * @throws UnexpectedValueException When an option has an unexpected structure.
     */
    private function get_fupi_options( $opt_names_arr ) {

        // Load and validate every requested option.
        foreach ( $opt_names_arr as $opt_name ) {

            if ( ! is_string( $opt_name ) ) continue;

            $opt_name = sanitize_key( $opt_name );

            // Load each option only once and use an empty array for missing non-version options.
            if ( ! array_key_exists( $opt_name, $this->o ) ) {

                $missing_option = new stdClass();
                $this->o[$opt_name] = get_option( $opt_name, $missing_option );

                if (
                    $this->o[$opt_name] === $missing_option ||
                    ( $this->o[$opt_name] === false && $opt_name !== 'fupi_versions' )
                ) {
                    $this->o[$opt_name] = [];
                }
            }

            // Keep the old false version marker so the dedicated repair code can handle it.
            if (
                $opt_name === 'fupi_versions' &&
                $this->o[$opt_name] === false
            ) continue;

            if ( ! is_array( $this->o[$opt_name] ) ) throw new UnexpectedValueException( 'Invalid option shape.' );
            if ( $opt_name === 'fupi_tools' ) $this->validate_fields( $this->o[$opt_name], array_keys( $this->o[$opt_name] ) );
        }
    }

    /**
     * Validates and loads option values extracted from a backup.
     *
     * @param array $backup_data Backup values keyed by option name. Values may be arrays, no_value, or a legacy false version marker.
     * @return bool True when every backup value is allowed and loaded; false when any value is invalid.
     */
    private function get_options_from_backup( $backup_data ) {

        if ( ! is_array( $backup_data ) ) return false;

        // Validate every backup value before changing migration state.
        foreach ( $backup_data as $key => $value ) {

            // Theme modifications use a separate validation rule from plugin options.
            if ( $key === 'theme_mods' ) {

                if ( ! is_array( $value ) ) return false;

                foreach ( $value as $mod_id => $mod_value ) {
                    if (
                        ! is_string( $mod_id ) ||
                        strpos( $mod_id, 'fupi_' ) !== 0
                    ) return false;
                }

                continue;
            }

            if (
                ! is_string( $key ) ||
                strpos( $key, 'fupi_' ) !== 0 ||
                in_array( $key, [ self::LOCK_OPTION, self::FAILURE_OPTION, self::RESTORE_SAFETY_OPTION ], true )
            ) return false;
            if (
                ! is_array( $value ) &&
                $value !== 'no_value' &&
                ! ( $key === 'fupi_versions' && $value === false )
            ) return false;
        }

        // Represent deleted backup options with empty values while the restore is prepared.
        foreach ( $backup_data as $key => $value ) {

            // Remember options marked no_value so the restore can delete them later.
            if ( $value === 'no_value' ) {
                $this->opts_to_remove[] = $key;
                // Use an empty array during processing to keep later validation safe.
                $this->o[$key] = [];
            } else {
                $this->o[$key] = $value;
            }
        }

        return true;
    }

    /**
     * Confirms that selected option fields contain scalar values.
     *
     * @param array $data Option data to inspect.
     * @param array $fields Field names that must contain scalar values when present.
     * @throws UnexpectedValueException When the data or one of the selected fields is invalid.
     */
    private function validate_fields( $data, $fields ) {

        if ( ! is_array( $data ) ) throw new UnexpectedValueException( 'Invalid settings shape.' );

        foreach ( $fields as $field ) {
            if (
                array_key_exists( $field, $data ) &&
                ! is_scalar( $data[$field] )
            ) {
                throw new UnexpectedValueException( 'Invalid field shape.' );
            }
        }
    }

    /**
     * Confirms that option rows contain required fields and scalar field values.
     *
     * @param array $rows List of row arrays to inspect.
     * @param array $required Field names that every row must contain.
     * @param array $scalar_fields Field names that must contain scalar values when present.
     * @throws UnexpectedValueException When a row is invalid or misses a required field.
     */
    private function validate_rows( $rows, $required = [], $scalar_fields = [] ) {

        if ( ! is_array( $rows ) ) throw new UnexpectedValueException( 'Invalid rows shape.' );

        // Validate the shape and required fields of every row.
        foreach ( $rows as $row ) {
            $this->validate_fields( $row, array_merge( $required, $scalar_fields ) );

            foreach ( $required as $field ) {
                if ( ! array_key_exists( $field, $row ) ) throw new UnexpectedValueException( 'Missing migration field.' );
            }
        }
    }

    /**
     * Adds or replaces an option value in the migration state.
     *
     * @param string $opt_name WordPress option name to add.
     * @param mixed  $opt_value Value that will be saved for the option.
     */
    private function add_fupi_option( $opt_name, $opt_value ) {
        $this->o[$opt_name] = $opt_value;
    }

    /**
     * Persists all migrated WordPress options.
     *
     * @return bool True when every option is saved or deleted successfully; false on a database error.
     */
    private function update_wp_options() {

        // Save or delete each option before persisting the enabled tools list.
        foreach ( $this->o as $opt_name => $opt_val ) {

            if ( in_array( $opt_name, [ 'fupi_versions', 'fupi_tools' ], true ) ) continue;

            if ( in_array( $opt_name, $this->opts_to_remove, true ) ) {
                if ( ! $this->delete_wp_option( $opt_name ) ) return false;
            } else {
                if ( ! $this->update_wp_option( $opt_name, $opt_val ) ) return false;
            }
        }

        // Save or remove the enabled tools list last.
        return $this->save_tools();
    }

    /**
     * Saves or removes the plugin tools option.
     *
     * @return bool True when the tools option is unchanged, saved, or deleted successfully.
     */
    private function save_tools() {
        if ( in_array( 'fupi_tools', $this->opts_to_remove, true ) ) return $this->delete_wp_option( 'fupi_tools' );
        return ! isset( $this->o['fupi_tools'] ) || $this->update_wp_option( 'fupi_tools', $this->o['fupi_tools'] );
    }

    /**
     * Updates an option and distinguishes a no-op from a database error.
     *
     * WordPress update_option() returns false when the value is unchanged, so this method also checks $wpdb->last_error.
     *
     * @param string $opt_name WordPress option name to update.
     * @param mixed  $opt_value New value for the option.
     * @return bool True when the value is updated or already equal; false on a database error.
     */
    private function update_wp_option( $opt_name, $opt_value ) {

        global $wpdb;

        $wpdb->last_error = '';
        $updated = update_option( $opt_name, $opt_value );

        return $updated || empty( $wpdb->last_error );
    }

    /**
     * Deletes an option and distinguishes an absent option from a database error.
     *
     * WordPress delete_option() returns false when the option is already absent, so this method also checks $wpdb->last_error.
     *
     * @param string $opt_name WordPress option name to delete.
     * @return bool True when the option is deleted or already absent; false on a database error.
     */
    private function delete_wp_option( $opt_name ) {

        global $wpdb;

        $wpdb->last_error = '';
        $deleted = delete_option( $opt_name );

        return $deleted || empty( $wpdb->last_error );
    }

    /** Restore validated backup options and decode values that were encoded for storage. */

    /**
     * Restores backup options after validating fields that require decoding.
     *
     * @return bool True when every backup value is restored successfully; false when the backup is empty or a write fails.
     * @throws UnexpectedValueException When a decoded option has an invalid structure.
     */
    private function restore_options_from_backup() {

        if ( empty( $this->o ) ) return false;

        // Validate report fields before any restore writes, rather than partway through the backup.
        if ( isset( $this->o['fupi_reports'] ) ) {
            $this->validate_fields( $this->o['fupi_reports'], [] );

            if ( isset( $this->o['fupi_reports']['dashboards'] ) ) $this->validate_rows( $this->o['fupi_reports']['dashboards'], [ 'iframe' ] );
        }

        // Validate consent script fields before restoring them.
        if ( isset( $this->o['fupi_cscr'] ) ) {
            $this->validate_fields( $this->o['fupi_cscr'], [] );

            foreach ( [ 'fupi_head_scripts', 'fupi_footer_scripts' ] as $placement ) {
                if ( isset( $this->o['fupi_cscr'][$placement] ) ) $this->validate_rows( $this->o['fupi_cscr'][$placement], [ 'scr' ], [ 'html' ] );
            }
        }

        // Restore each eligible option according to the way WordPress stores it.
        foreach ( $this->o as $option_id => $option_value ) {

            if ( in_array( $option_id, [ 'fupi_versions', 'fupi_tools' ], true ) ) continue;

            switch ( $option_id ) {
                case 'theme_mods':

                    // Restore each theme modification or remove values marked no_value.
                    foreach ( $option_value as $mod_id => $value ) {

                        // Remove a missing theme value and verify that WordPress removed it.
                        if ( $value == 'no_value' ) {
                            remove_theme_mod( $mod_id );
                            $missing_value = new stdClass();

                            if ( get_theme_mod( $mod_id, $missing_value ) !== $missing_value ) return false;

                        } else {
                            set_theme_mod( $mod_id, $value );
                            if ( get_theme_mod( $mod_id ) !== $value ) return false;
                        }
                    }
                    break;

                case 'fupi_reports':

                    // Remove the option or decode restored report scripts before saving them.
                    if ( in_array( 'fupi_reports', $this->opts_to_remove ) ) {
                        if ( ! $this->delete_wp_option( 'fupi_reports' ) ) return false;
                    } else {

                        // Scripts are encoded during sanitization and must be decoded before saving.
                        if ( ! empty( $option_value['dashboards'] ) ) {
                            foreach ( $option_value['dashboards'] as $i => $dash ) {
                                $option_value['dashboards'][$i]['iframe'] = html_entity_decode( $option_value['dashboards'][$i]['iframe'], ENT_QUOTES );
                            }
                        }

                        if ( ! $this->update_wp_option( 'fupi_reports', $option_value ) ) return false;
                    }
                    break;

                case 'fupi_cscr':

                    // Remove the option or decode scripts in each restored placement.
                    if ( in_array( 'fupi_cscr', $this->opts_to_remove ) ) {
                        if ( ! $this->delete_wp_option( 'fupi_cscr' ) ) return false;
                    } else {

                        $placements = [ 'fupi_head_scripts', 'fupi_footer_scripts' ];

                        // Decode each script placement before WordPress sanitizes and saves it.
                        foreach ( $placements as $placement_name ) {

                            // Decode scripts only when this placement contains entries.
                            if ( ! empty( $option_value[$placement_name] ) ) {

                                $placement_scripts = $option_value[$placement_name];
                                $i = 0;

                                foreach ( $placement_scripts as $single_script_data ) {

                                    $decoded_val = html_entity_decode( $single_script_data['scr'], ENT_QUOTES );
                                    $option_value[$placement_name][$i]['scr'] = $decoded_val;

                                    if ( ! empty( $option_value[$placement_name][$i]['html'] ) ) {
                                        $decoded_html = html_entity_decode( $single_script_data['html'], ENT_QUOTES );
                                        $option_value[$placement_name][$i]['html'] = $decoded_html;
                                    }

                                    $i++;
                                }
                            }
                        }

                        if ( ! $this->update_wp_option( 'fupi_cscr', $option_value ) ) return false;
                    }
                    break;

                default:

                    if ( in_array( $option_id, $this->opts_to_remove ) ) {
                        if ( ! $this->delete_wp_option( $option_id ) ) return false;
                    } else {
                        if ( ! $this->update_wp_option( $option_id, $option_value ) ) return false;
                    }
                    break;
            }
        }

        return $this->save_tools();
    }

    /** Read and repair the stored plugin version used to choose migrations. */

    /** Adds or repairs the version option in restored backups. */
    private function set_fupi_versions() {

        // Use the oldest supported backup version when the backup has no version marker.
        if ( ! isset( $this->o['fupi_versions'] ) ) {
            $this->o['fupi_versions'] = [
                time(),
                '8.3.2',
            ];
        } elseif ( $this->o['fupi_versions'] === false ) {
            // Version 9.0.0 could save this option as false, so repair it as version 9.0.0.
            $this->o['fupi_versions'] = [
                time(),
                '9.0.0',
            ];
        }
    }

    /**
     * Determines whether the saved versions indicate a fresh installation.
     *
     * @return bool True when no previous version exists; false when an existing version was found.
     * @throws UnexpectedValueException When the stored version marker has an invalid structure.
     */
    private function check_fupi_versions() {

        $this->get_fupi_options( [ 'fupi_versions', 'fupi_tools' ] );

        // Version 9.0.0 could save the version option as false, so repair it before reading the version.
        if (
            isset( $this->o['fupi_versions'] ) &&
            $this->o['fupi_versions'] === false
        ) {
            $this->o['fupi_versions'] = [ time(), '9.0.0' ];
        }

        // Initialize the version option for a fresh installation.
        if ( count( $this->o['fupi_versions'] ) === 0 ) {
            $this->o['fupi_versions'][0] = time();
            $this->o['fupi_versions'][1] = FUPI_VERSION;
            $this->prev_version = FUPI_VERSION;

            return true;
        }

        if (
            ! isset( $this->o['fupi_versions'][1] ) ||
            ! is_string( $this->o['fupi_versions'][1] ) ||
            ! preg_match( '/^\d+(?:\.\d+)*(?:[-+][a-zA-Z0-9.-]+)?$/', $this->o['fupi_versions'][1] )
        ) {
            throw new UnexpectedValueException( 'Invalid version shape.' );
        }

        $this->prev_version = $this->o['fupi_versions'][1];

        return false;
    }

    /**
     * Stores the current plugin version in the versions option.
     *
     * @return bool True when the version option is saved or already current; false on a database error.
     */
    private function save_current_version() {
        $this->o['fupi_versions'][1] = FUPI_VERSION;

        return $this->update_wp_option( 'fupi_versions', $this->o['fupi_versions'] );
    }

    /** Send changed consent settings to the Consent Database when required. */

    /** Sends changed consent settings to the Consent Database when needed. */
    private function send_plugin_settings_to_cdb() {

        if ( ! $this->regenerate_cdb ) return;

        $this->get_fupi_options( [ 'fupi_tools', 'fupi_cook' ] );
        $pp_ok = false;

        if ( ! empty( $this->o['fupi_cook']['pp_id'] ) ) {
            $pp_id = (int) $this->o['fupi_cook']['pp_id'];
            $pp_ok = get_post_status( $pp_id ) == 'publish';
        }

        // Send consent settings only when the consent tool and published policy page are active.
        if (
            isset( $this->o['fupi_tools']['cook'] ) &&
            $pp_ok
        ) {
            include_once FUPI_PATH . '/includes/class-fupi-get-gdpr-status.php';

            $gdpr_checker = new Fupi_compliance_status_checker();

            $gdpr_checker->send_and_return_status();
        }
    }

    /** Clear generated plugin files after settings change. */

    /** Clears generated plugin caches after settings change. */
    private function clear_cache() {
        require_once FUPI_PATH . '/admin/common/fupi-clear-cache.php';
        fupi_clear_cache();
    }

    /** Apply migrations in the order required by the plugin version history. */

    /**
     * Migrates WooCommerce variable-product tracking settings.
     *
     * @throws UnexpectedValueException When the WooCommerce settings have an invalid structure.
     */
    private function update_to_8_5() {

        $this->get_fupi_options( [ 'fupi_woo' ] );
        $this->validate_fields( $this->o['fupi_woo'], [ 'variable_as_simple' ] );

        if ( ! empty( $this->o['fupi_woo']['variable_as_simple'] ) ) {
            $this->o['fupi_woo']['variable_tracking_method'] = 'track_parents';
        }
    }

    /** Marks consent database data for regeneration after version 8.5.1. */
    private function update_to_8_5_1() {
        $this->regenerate_cdb = true;
    }

    /** Loads migrations introduced in version 9.0. */
    private function update_to_9_0() {
        require __DIR__ . '/updater_rules/v9.php';
    }

    /**
     * Enables advanced settings for upgrades through version 9.2.
     *
     * @throws UnexpectedValueException When the stored version option has an invalid structure.
     */
    private function update_to_9_2() {
        $this->get_fupi_options( [ 'fupi_versions' ] );
        // Keep upgraded sites in advanced mode so their existing settings remain visible.
        $this->o['fupi_versions']['use_adv_mode'] = true;
    }

    /** Loads migrations introduced in version 9.3. */
    private function update_to_9_3_0() {
        require __DIR__ . '/updater_rules/v9-3.php';
    }

    /** Loads migrations introduced in version 9.4. */
    private function update_to_9_4_0() {
        require __DIR__ . '/updater_rules/v9-4.php';
    }

    /** Loads migrations introduced in version 10.0. */
    private function update_to_10_0_0() {
        require __DIR__ . '/updater_rules/v10.php';
    }

    /**
     * Normalizes the Google Tag Manager data layer setting.
     *
     * @throws UnexpectedValueException When the data layer setting is not scalar.
     */
    private function update_to_10_0_1() {

        $this->get_fupi_options( [ 'fupi_gtm' ] );
        $datalayer = $this->o['fupi_gtm']['datalayer'] ?? '';

        if ( ! is_scalar( $datalayer ) ) throw new UnexpectedValueException( 'Invalid datalayer shape.' );

        if ( ! in_array( $datalayer, [ 'default', 'fupi_dataLayer' ], true ) ) {
            $this->o['fupi_gtm']['datalayer'] = empty( $datalayer ) ? 'default' : 'fupi_dataLayer';
        }
    }

    /** Loads migrations introduced in version 11. */
    private function update_to_11() {
        require __DIR__ . '/updater_rules/v11.php';
    }
}

?>
