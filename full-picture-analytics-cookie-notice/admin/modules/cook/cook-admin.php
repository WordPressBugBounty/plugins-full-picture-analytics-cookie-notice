<?php

class Fupi_COOK_admin {

    private $settings;
    private $tools;
    private $main;
    private $versions;
    private $table_name = 'fupi_consents';
    private $cron_hook = 'fupi_consents_backup_cron_event';

    public function __construct(){
        $this->settings = get_option('fupi_cook');
        $this->tools = get_option('fupi_tools');
        $this->main = get_option('fupi_main');
        $this->versions = get_option('fupi_versions');
        $this->add_actions_and_filters();
    }

    private function add_actions_and_filters(){
        add_action( 'fupi_register_setting_cook', array( $this, 'register_module_settings' ) );
        add_filter( 'fupi_cook_add_fields_settings', array( $this, 'add_fields_settings' ), 10, 1 );
        add_filter( 'fupi_cook_get_page_descr', array( $this, 'get_page_descr' ), 10, 2 );
        // add_filter( 'fupi_cook_get_offscreen_content', array( $this, 'get_offscreen_content' ), 10, 1 );

        add_action( 'publish_page', array( $this, 'fupi_listen_to_pp_page_updates' ), 10, 2 );
        add_action( $this->cron_hook, array( $this, 'fupi_backup_and_email_consents') );
        add_filter( 'fupi_process_proofrec_db_save_server_result', array( $this, 'fupi_save_consent_to_db' ), 10, 3 );

        // CUSTOMIZER
		
		// Enable customizer functions if we are NOT using OceanWP theme
		$theme = wp_get_theme();

		if ( $theme->get('Name') != 'OceanWP' ) {
			add_action( 'customize_register', array( $this, 'fupi_customize_register' ) );
			add_action( 'customize_save_after', array( $this, 'fupi_customize_save_after' ) );
			add_action( 'customize_preview_init', array( $this, 'fupi_customizer_preview_scripts' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'fupi_enqueue_customizer_css_js') );
		}
    }

    // HELPER

    private function pp_ok(){
        
        if ( ! empty( $this->settings['pp_id'] ) ) {
            $pp_id = (int) $this->settings['pp_id'];
            return get_post_status( $pp_id ) == 'publish';
        }

        return false;
    }

    // Offscreen content

    // public function get_offscreen_content( $offscreen_content ){
    //     include_once 'cook-offscreen.php';
    //     return $offscreen_content;
    // }

    // CUSTOMIZER

	// Register customizer settings
	public function fupi_customize_register($wp_customize) {	
        if ( ! function_exists('fupi_disable_customizer') ) {
            include_once 'customizer/fupi-customizer-settings.php';
            include_once 'customizer/fupi-iframe-customizer-settings.php';
        }
	}

    private function is_pp_ok( $post_id ) {
        if ( empty( $this->settings['pp_id'] ) ) return false;

        $pp_id = (int) $this->settings['pp_id'];
        return $post_id == $pp_id && get_post_status( $pp_id ) == 'publish';
    }

    public function fupi_listen_to_pp_page_updates( $post_id, $post ) {
        $send_to_email = ! empty( $this->settings['storage_location'] ) && $this->settings['storage_location'] == 'email';
        $send_to_cdb = ! empty( $this->settings['storage_location'] ) && $this->settings['storage_location'] == 'cdb' && ! empty( $this->settings['cdb_key'] );

        if ( ( ! $send_to_email && ! $send_to_cdb ) || ! $this->is_pp_ok( $post_id ) ) return;

        include_once 'cook-proofrec-sender.php';
        $proofrec_sender = new Fupi_PROOFREC_send( $this->settings );

        if ( $send_to_email ) {
            $email_to = ! empty( $this->settings['local_backup_email'] ) ? $this->settings['local_backup_email'] : get_option( 'admin_email' );
            $proofrec_sender->send_privacy_policy_to_email( $email_to );
        } else {
            $proofrec_sender->send_privacy_policy_to_cdb();
        }
    }

    private function create_db_tables() {
        global $wpdb;

        $full_table_name = $wpdb->prefix . $this->table_name;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$full_table_name'" ) == $full_table_name ) return;

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $full_table_name (
            id int(9) NOT NULL AUTO_INCREMENT,
            consent_id varchar(50) NOT NULL,
            consent_date bigint NOT NULL,
            provided_consents longtext NOT NULL,
            extra_data longtext NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    private function schedule_cron( $clean_data ) {
        $freq = ! empty( $clean_data['email_frequency'] ) ? $clean_data['email_frequency'] : 'daily';
        if ( ! in_array( $freq, array( 'hourly', 'twicedaily', 'daily' ), true ) ) $freq = 'daily';

        $this->unschedule_cron();
        wp_schedule_event( time() + 60, $freq, $this->cron_hook );
    }

    private function unschedule_cron() {
        $timestamp = wp_next_scheduled( $this->cron_hook );
        while ( $timestamp ) {
            wp_unschedule_event( $timestamp, $this->cron_hook );
            $timestamp = wp_next_scheduled( $this->cron_hook );
        }
    }

    public function fupi_save_consent_to_db( $result, $payload, $user_ip ) {
        // Store consent locally only when records are configured for email delivery.
        if ( empty( $this->settings['storage_location'] ) || $this->settings['storage_location'] != 'email' ) {
            return [
                'success' => false,
                'saved' => false,
                'skipped' => true,
                'retryable' => false,
                'error' => 'email_storage_disabled',
            ];
        }

        $payload = (array) $payload;
        // Reject malformed identifiers, timestamps, and consent flags before touching the database.
        if (
            empty( $payload['cdbID'] ) || ! is_string( $payload['cdbID'] ) ||
            strlen( $payload['cdbID'] ) > 50 ||
            ! preg_match( '/^[A-Za-z0-9._:-]+$/', $payload['cdbID'] ) ||
            empty( $payload['timestamp'] ) || ! is_numeric( $payload['timestamp'] ) ||
            ! isset( $payload['stats'], $payload['marketing'], $payload['personalisation'] ) ||
            ! is_bool( $payload['stats'] ) || ! is_bool( $payload['marketing'] ) || ! is_bool( $payload['personalisation'] )
        ) {
            return [
                'success' => false,
                'saved' => false,
                'retryable' => false,
                'error' => 'invalid_consent_payload',
            ];
        }

        $this->create_db_tables();

        // Convert enabled boolean flags into the human-readable consent list stored in the record.
        $consents = [];
        if ( $payload['stats'] ) $consents[] = 'statistics';
        if ( $payload['marketing'] ) $consents[] = 'marketing';
        if ( $payload['personalisation'] ) $consents[] = 'personalisation';

        $consent_id = $payload['cdbID'];
        $timestamp = (int) $payload['timestamp'];

        // Bind the proof to the current privacy-policy and tracking-configuration versions.
        $payload['privacyPolicyVersionID'] = ! empty( $this->versions['pp_md5'] ) ? $this->versions['pp_md5'] : ( $payload['pp_pub'] ?? false );
        if ( ! empty( $this->versions['md5'] ) ) $payload['configVersionID'] = $this->versions['md5'];

        // Keep only supplementary proof data in the JSON column; core fields are stored separately.
        unset( 
            $payload['stats'], 
            $payload['marketing'], 
            $payload['personalisation'], 
            $payload['cdbID'], 
            $payload['timestamp'], 
            $payload['disabled'], 
            $payload['tools'], 
            $payload['pp_pub'] 
        );

        global $wpdb;
        $table_name = $wpdb->prefix . $this->table_name;
        
        // Treat retries of the same consent ID and timestamp as successful without inserting duplicates.
        $duplicate_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$table_name} WHERE consent_id = %s AND consent_date = %d LIMIT 1",
                $consent_id,
                $timestamp
            )
        );

        if ( $duplicate_id ) {
            return [
                'success' => true,
                'saved' => true,
                'duplicate' => true,
                'retryable' => false,
            ];
        }

        $result = $wpdb->insert(
            $table_name,
            array(
                'consent_id' => $consent_id,
                'consent_date' => $timestamp,
                'provided_consents' => empty( $consents ) ? 'none' : implode( ', ', $consents ),
                'extra_data' => wp_json_encode( $payload ),
            ),
            array( '%s', '%d', '%s', '%s' )
        );

        if ( $result === false ) {
            return [
                'success' => false,
                'saved' => false,
                'retryable' => true,
                'error' => 'db_error',
            ];
        }

        return [
            'success' => true,
            'saved' => true,
            'retryable' => false,
        ];
    }

    public function fupi_backup_and_email_consents() {
        
        // The scheduled backup is inactive unless email is the selected storage destination.
        
        if ( empty( $this->settings['storage_location'] ) || $this->settings['storage_location'] != 'email' ) return;

        $this->create_db_tables();

        global $wpdb;
        
        $email_to       = ! empty( $this->settings['local_backup_email'] ) ? $this->settings['local_backup_email'] : get_option( 'admin_email' );
        $table          = $wpdb->prefix . $this->table_name;
        $consents       = $wpdb->get_results( "SELECT * FROM $table ORDER BY id ASC", ARRAY_A );

        if ( empty( $email_to ) || empty( $consents ) ) return;

        // Capture the batch boundary so records added during sending are preserved for the next run.
        $last_backup_id = end( $consents )['id'];
        
        // Consent timestamps arrive in milliseconds, while PHP date functions expect seconds.
        $oldest = date( 'd M Y H:i:s', round( $consents[0]['consent_date'] / 1000 ) );
        $latest = date( 'd M Y H:i:s', round( end( $consents )['consent_date'] / 1000 ) );

        // Generate files
        include_once FUPI_PATH . '/admin/common/generate-files.php';
        $file_generator = new Fupi_Generate_Files();
        $file_path = $file_generator->make_file( array(
            'folder' => 'consents',
            'file_name' => 'consents',
            'file_format' => 'txt',
            'file_content' => wp_json_encode( $consents ),
        ) );

        // Send email
        $mail_sent = false;
        if ( $file_path != 'error' ) {
            $subject = sprintf( '[Do not delete] Records of consents from %s', get_bloginfo( 'name' ) );
            $body = sprintf( esc_html__( "This is an automatic message, sent by WP Full Picture plugin version %s. This message contains copies of consents collected on your website.\n\nDo not delete this email.\n\nEvery stored consent contains IDs of privacy policy and tracking configuration that were sent to your email in separate messages. Together they form complete proofs of visitors' consents.\n\nNumber of collected consents: %d\nOldest: %s\nLatest: %s\n", 'full-picture-analytics-cookie-notice' ), FUPI_VERSION, count( $consents ), $oldest, $latest );
            $body .= sprintf( esc_html__( "\n\nLearn how to find consents and combine them with privacy policy and tracking configuration data: %s.", 'full-picture-analytics-cookie-notice' ), 'https://wpfullpicture.com/support/documentation/how-to-find-visitors-consents-stored-in-emails/' );
            $mail_sent = wp_mail( $email_to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ), array( $file_path ) );
            
            // Remove the temporary attachment regardless of whether the email was accepted.
            unlink( $file_path );

            if ( $mail_sent ) {
                // Delete only the delivered batch, leaving newer and failed records for later runs.
                $wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE id <= %d", $last_backup_id ) );
            }
        }

        if ( ! $mail_sent ) {

            // Notify the administrator while retaining all consent records for the next scheduled retry.
            
            $subject = sprintf( esc_html__( 'Records of consents from %s - sending error', 'full-picture-analytics-cookie-notice' ), get_bloginfo( 'name' ) );
            $body = sprintf( esc_html__( "This is an automatic message, sent by WP Full Picture plugin version %1\$s. You are getting this message because there was an error sending records of tracking consent to a defined email account. This will be retried on the next scheduled run.\n\nIf the problem persists, please contact support.", 'full-picture-analytics-cookie-notice' ), FUPI_VERSION );
            wp_mail( $email_to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
        }
    }

	// Sanitize customizer settings
	public function fupi_customizer_sanitize($val, $setting) {
		if ( ! function_exists('fupi_disable_customizer') ) {
			$sanitized = ( include ('customizer/fupi-customizer-sanitize.php') ); // a workaround to get the value returned from the included file
			return $sanitized;
		}
	}

	// Update settings sent to CDB after changes in customizer
	public function fupi_customize_save_after(){
		if ( ! empty ( $this->tools['cook'] ) && $this->pp_ok() ) {
			include_once FUPI_PATH . '/includes/class-fupi-get-gdpr-status.php';
			$gdpr_checker = new Fupi_compliance_status_checker();
            $gdpr_checker->send_and_return_status();
		}
	}

	// Enqueue customizer preview scripts
	public function fupi_customizer_preview_scripts() {
		if ( ! function_exists('fupi_disable_customizer') ) {
			wp_enqueue_script( 'fupi-customizer-preview', plugin_dir_url( __FILE__ ) . 'customizer/js/fupi-customizer-preview.js', array( 'customize-preview', 'jquery' ), FUPI_VERSION, true );
		}
	}

	// Enqueue customizer controls scripts and styles
	public function fupi_enqueue_customizer_css_js(){	
        if ( ! function_exists('fupi_disable_customizer') ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker');
            wp_enqueue_script( 'fupi-customizer-controls', plugin_dir_url( __FILE__ ) . 'customizer/js/fupi-customizer-controls.js', array( 'jquery', 'customize-controls' ), FUPI_VERSION, true );
            wp_enqueue_style( 'fupi-customizer-css', plugin_dir_url( __FILE__ ) . 'customizer/css/fupi-customizer.css', array(), FUPI_VERSION, 'all' );
        }
	}

    // ADMIN PAGE

    public function add_fields_settings( $sections ){
        include_once 'cook-fields.php';
        return $sections;
    }

    public function register_module_settings(){
        register_setting( 'fupi_cook', 'fupi_cook', array( 'sanitize_callback' => array( $this, 'sanitize_fields' ) ) );
    }

    private function register_site_in_cdb( $clean_data ) {
        include_once 'cook-proofrec-sender.php';
        $proofrec_sender = new Fupi_PROOFREC_send( $clean_data );
        $response = $proofrec_sender->register_website_in_cdb( $clean_data['cdb_key'] );

        if ( empty( $response ) || $response->status != 'success' ) {
            unset( $clean_data['cdb_key'] );
            add_settings_error( 'fupi_cook', 'settings_updated', esc_attr__( 'There was an error registering the site to the ConsentsDB. Save the secret key and try again.', 'full-picture-analytics-cookie-notice' ), 'error' );
            return $clean_data;
        }

        $pp_response = $proofrec_sender->send_privacy_policy_to_cdb( $clean_data['cdb_key'], true, false );
        if ( $pp_response === false || $pp_response->status != 'success' ) {
            unset( $clean_data['cdb_key'] );
            add_settings_error( 'fupi_cook', 'settings_updated', esc_attr__( 'There was an error sending privacy policy to the ConsentsDB. Save the secret key and try again.', 'full-picture-analytics-cookie-notice' ), 'error' );
            return $clean_data;
        }

        include_once FUPI_PATH . '/includes/class-fupi-get-gdpr-status.php';
        $gdpr_checker = new Fupi_compliance_status_checker( 'cook', $clean_data, array(
            'is_first_reg' => true,
            'cdb_key' => $clean_data['cdb_key'],
        ) );
        $sending_setup_response = $gdpr_checker->send_and_return_status();

        if ( empty( $sending_setup_response ) || $sending_setup_response->status != 'success' ) {
            unset( $clean_data['cdb_key'] );
            add_settings_error( 'fupi_cook', 'settings_updated', esc_attr__( 'There was an error sending tracking configuration to the ConsentsDB. Save the secret key and try again.', 'full-picture-analytics-cookie-notice' ), 'error' );
        }

        return $clean_data;
    }

    private function update_proofrec_storage( $clean_data ) {
        $pp_ok = ! empty( $clean_data['pp_id'] ) && get_post_status( (int) $clean_data['pp_id'] ) == 'publish';
        $send_to_email = ! empty( $clean_data['storage_location'] ) && $clean_data['storage_location'] == 'email';
        $send_to_cdb = ! empty( $clean_data['storage_location'] ) && $clean_data['storage_location'] == 'cdb' && ! empty( $clean_data['cdb_key'] );

        if ( ( $send_to_email || $send_to_cdb ) && ! $pp_ok ) {
            unset( $clean_data['storage_location'] );
            if ( $send_to_cdb ) unset( $clean_data['cdb_key'] );
            $this->unschedule_cron();
            add_settings_error( 'fupi_cook', 'settings_updated', esc_attr__( 'Records of consent require a published privacy policy page. Publish the page and save its ID before enabling consent records.', 'full-picture-analytics-cookie-notice' ), 'error' );
            return $clean_data;
        }

        if ( $send_to_email ) {
            $email_to = ! empty( $clean_data['local_backup_email'] ) ? $clean_data['local_backup_email'] : get_option( 'admin_email' );
            $was_sending_to_email = ! empty( $this->settings['storage_location'] ) && $this->settings['storage_location'] == 'email';

            $this->create_db_tables();
            $this->schedule_cron( $clean_data );

            include_once 'cook-proofrec-sender.php';
            $proofrec_sender = new Fupi_PROOFREC_send( $clean_data );
            include_once FUPI_PATH . '/includes/class-fupi-get-gdpr-status.php';

            if ( ! $was_sending_to_email ) {
                $proofrec_sender->send_privacy_policy_to_email( $email_to, false );
                $gdpr_checker = new Fupi_compliance_status_checker( 'cook', $clean_data, array( 'is_first_reg' => true, 'email_to' => $email_to ) );
            } else {
                $gdpr_checker = new Fupi_compliance_status_checker( 'cook', $clean_data, array( 'email_to' => $email_to ) );
            }

            $gdpr_checker->send_and_return_status();
            return $clean_data;
        }

        $this->unschedule_cron();
        if ( ! $send_to_cdb ) return $clean_data;

        if ( empty( $this->settings['cdb_key'] ) || $this->settings['cdb_key'] != $clean_data['cdb_key'] ) {
            return $this->register_site_in_cdb( $clean_data );
        }

        include_once FUPI_PATH . '/includes/class-fupi-get-gdpr-status.php';
        $gdpr_checker = new Fupi_compliance_status_checker( 'cook', $clean_data );
        $gdpr_checker->send_and_return_status();
        return $clean_data;
    }

    public function sanitize_fields( $input ){

        include 'cook-sanitize.php';

        if ( apply_filters( 'fupi_updating_many_options', false ) ) return $clean_data;

        $clean_data = $this->update_proofrec_storage( $clean_data );
        
        if ( ! empty ( $this->tools['cook'] ) && ! empty( $clean_data['pp_id'] ) && get_post_status( (int) $clean_data['pp_id'] ) == 'publish' ) {
			include_once FUPI_PATH . '/includes/class-fupi-get-gdpr-status.php';
			$gdpr_checker = new Fupi_compliance_status_checker( 'cook', $clean_data );
            $gdpr_checker->send_and_return_status();
		}

        // Clear cache and save settings
		
		require_once FUPI_PATH . '/admin/common/fupi-clear-cache.php';
		fupi_clear_cache();
		return $clean_data; 
    }

    public function get_page_descr( $section_id, $no_woo_descr_text ){
        include 'cook-descr.php';
        return $ret_text;
    }

}
