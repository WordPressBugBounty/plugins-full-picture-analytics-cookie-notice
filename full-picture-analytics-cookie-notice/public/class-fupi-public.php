<?php

class Fupi_Public {
    public $plugin_name;

    public $version;

    public $main;

    public $track;

    public $tools;

    public $woo;

    public $gtm;

    public $proofrec;

    protected $cook;

    public $track_current_user;

    private $ver;

    private $modules = [];

    private $server_max_body_size = 65536;

    private $server_max_batch_size = 20;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->tools = get_option( 'fupi_tools' );
        $this->main = get_option( 'fupi_main' );
        $this->track = get_option( 'fupi_track' );
        $this->cook = get_option( 'fupi_cook' );
        $this->woo = get_option( 'fupi_woo' );
        $this->proofrec = $this->cook;
        $this->gtm = get_option( 'fupi_gtm' );
        $this->ver = get_option( 'fupi_versions' );
    }

    public function load_module( $moduleID, $is_premium = false, $custom_path = '' ) {
        if ( $is_premium && !fupi_fs()->can_use_premium_code() ) {
            return;
        }
        // do not load premium modules
        // do not load a module that is already loaded
        $moduleClass = 'Fupi_' . strtoupper( $moduleID ) . '_public';
        if ( class_exists( $moduleClass ) ) {
            trigger_error( "Module {$moduleID} is already loaded.", E_USER_WARNING );
            return;
        }
        // load file
        if ( is_string( $custom_path ) && $custom_path !== '' ) {
            $modulePath = $custom_path;
        } else {
            if ( $is_premium ) {
                $modulePath = FUPI_PATH . "/public/modules/{$moduleID}__premium_only/{$moduleID}-public.php";
            } else {
                $modulePath = FUPI_PATH . "/public/modules/{$moduleID}/{$moduleID}-public.php";
            }
        }
        if ( !file_exists( $modulePath ) ) {
            return;
        }
        require_once $modulePath;
        // return if the loaded file has no necessary class
        if ( !class_exists( $moduleClass ) ) {
            return;
        }
        // // Check if this module has dependencies
        // if (method_exists($moduleClass, 'getDependencies')) {
        //     $dependencies = $moduleClass::getDependencies();
        //     foreach ($dependencies as $dependency) {
        //         if (!isset($this->modules[$dependency])) {
        //             $this->loadModule($dependency);
        //         }
        //     }
        // }
        // Add the module to the main class
        $this->modules[$moduleID] = new $moduleClass();
        // you can pass $this here or any other vars if needed. Passing $this will let the module access the main class and all of its methods and properties.
    }

    public function __call( $method, $args ) {
        foreach ( $this->modules as $module ) {
            if ( method_exists( $module, $method ) ) {
                return call_user_func_array( [$module, $method], $args );
            }
        }
        throw new Exception("Method {$method} not found.");
    }

    public function fupi_output_fupi_data_in_head() {
        require_once dirname( __FILE__ ) . '/in_head/inline_js.php';
        $inline_js = new Fupi_Public_Inline_JS($this);
        $inline_js->output();
    }

    public function fupi_enqueue_core_js() {
        /* ^ */
        wp_enqueue_script(
            'fupi-core',
            FUPI_URL . 'public/common/js/fupi-core.js',
            array(),
            $this->version,
            [
                'in_footer' => false,
                'strategy'  => 'defer',
            ]
        );
    }

    /**
     * Selectively add optimizer exclusion attributes to Full Picture scripts.
     */
    // DEFERRING EXCLUSIONS
    // Add exclusion attributes for Litespeed cache and some compatible plugins
    public function add_nodefer_to_fupi_scripts( $tag, $handle, $src ) {
        // Check if current script should have no-defer attribute
        if ( strpos( $handle, 'fupi-' ) !== false ) {
            $tag = str_replace( '<script ', '<script data-no-defer="1" data-no-optimize="1" data-no-minify="1" data-cfasync="false" class="fupi_no_defer" data-fp-runtime="1" ', $tag );
        }
        return $tag;
    }

    // For WP Rocket
    // exclude inline scripts
    public function fupi_rocket_exclude_inline_js( $inline_excludes ) {
        if ( !empty( $this->main['wprocket_compat'] ) ) {
            if ( !is_array( $inline_excludes ) ) {
                $inline_excludes = array();
            }
            $inline_excludes[] = 'fupi_no_defer';
            $inline_excludes[] = 'data-fp-runtime';
            $inline_excludes = array_values( array_unique( $inline_excludes ) );
        }
        return $inline_excludes;
    }

    // exclude script files
    public function fupi_rocket_exclude_js_files( $excludes ) {
        if ( !empty( $this->main['wprocket_compat'] ) ) {
            if ( !is_array( $excludes ) ) {
                $excludes = array();
            }
            $excludes[] = 'hooks.js';
            $excludes[] = 'hooks.min.js';
            $excludes[] = '/wp-includes/js/jquery/(.*).js';
            // this is necessary only when WooCommerce is used
            $excludes[] = '/full-picture-analytics-cookie-notice/';
            $excludes[] = '/full-picture-premium/';
            $excludes = array_values( array_unique( $excludes ) );
        }
        return $excludes;
    }

    //
    // SERVER-SIDE TRACKING
    //
    private function get_current_user_ip() {
        // Proxy headers are client-controlled unless the request came through a
        // trusted proxy. Use the socket address by default and let installations
        // with a trusted proxy explicitly provide the restored client address.
        $remote_ip = ( !empty( $_SERVER['REMOTE_ADDR'] ) ? trim( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
        if ( !filter_var( $remote_ip, FILTER_VALIDATE_IP ) ) {
            $remote_ip = '127.0.0.1';
        }
        $user_ip = apply_filters( 'fupi_public_client_ip', $remote_ip, $remote_ip );
        if ( !is_string( $user_ip ) || !filter_var( trim( $user_ip ), FILTER_VALIDATE_IP ) ) {
            return $remote_ip;
        }
        return trim( $user_ip );
    }

    // PREPARE REQUEST TO CDB
    private function prepare_cdb_data( $requests_a, $event_payload ) {
        $visit_info = (object) $event_payload;
        $cdbID = ( isset( $visit_info->cdbID ) ? $visit_info->cdbID : false );
        if ( empty( $cdbID ) ) {
            return $requests_a;
        }
        if ( empty( $this->proofrec['storage_location'] ) || $this->proofrec['storage_location'] != 'cdb' || empty( $this->proofrec['cdb_key'] ) ) {
            return $requests_a;
        }
        // MAKE PAYLOAD
        $gmt_offset = get_option( 'gmt_offset' );
        $timezone = ( $gmt_offset >= 0 ? '+' . $gmt_offset : $gmt_offset . '' );
        $payload = [
            'consentID'       => $cdbID . '_' . $visit_info->timestamp,
            'serverTimezone'  => $timezone,
            'serverTimestamp' => current_time( 'Y-m-d H:i:s' ),
            'visit'           => $visit_info,
        ];
        $payload['installID'] = 999999;
        // RETURN REQUEST DATA
        $requests_a[] = [
            'url'             => 'https://prod-fr.consentsdb.com/api/cookies',
            'headers'         => ['Content-Type: application/json', 'x-api-key: ' . $this->proofrec['cdb_key']],
            'payload'         => $payload,
            'return_response' => 'CDB',
        ];
        return $requests_a;
    }

    // WooCommerce 11.1.2 treats requests with the "?rest_route=" GET parameter as REST API
    // requests and then does not start the WooCommerce client session (skips its "frontend"
    // machinery). Our sender route saves data in the WooCommerce session, so requests to it
    // must be recognized as front-end requests, like WooCommerce handled them before 11.1.2.
    public function fupi_sender_is_frontend_request( $is_rest_api_request ) {
        if ( !$is_rest_api_request ) {
            return false;
        }
        // "/index.php?rest_route=/fupi/v1/sender" form of the endpoint
        if ( !empty( $_GET['rest_route'] ) && is_string( $_GET['rest_route'] ) && str_starts_with( wp_unslash( $_GET['rest_route'] ), '/fupi/v1/sender' ) ) {
            return false;
        }
        // "/wp-json/fupi/v1/sender" form of the endpoint
        if ( !empty( $_SERVER['REQUEST_URI'] ) ) {
            $sender_path = '/' . trailingslashit( rest_get_url_prefix() ) . 'fupi/v1/sender';
            if ( false !== strpos( wp_unslash( $_SERVER['REQUEST_URI'] ), $sender_path ) ) {
                return false;
            }
        }
        return $is_rest_api_request;
    }

    // REST HOOKS
    public function fupi_rest_hooks() {
        register_rest_route( 'fupi/v1', '/sender', [
            'methods'             => 'POST',
            'callback'            => [$this, 'fupi_process_server_calls'],
            'permission_callback' => '__return_true',
        ] );
    }

    // AJAX HOOKS
    public function fupi_ajax_hooks() {
        // Get the payload
        $input = file_get_contents( 'php://input' );
        // Count every request before parsing or validating its payload.
        $user_ip = $this->get_current_user_ip();
        if ( !$this->fupi_server_rate_limit_ok( $user_ip ) ) {
            header( 'Retry-After: 60' );
            wp_send_json( [
                'success'   => false,
                'retryable' => true,
                'error'     => 'rate_limited',
            ], 429 );
        }
        // Check for errors
        if ( strlen( $input ) > $this->server_max_body_size ) {
            wp_send_json( [
                'success'   => false,
                'retryable' => false,
                'error'     => 'request_too_large',
            ], 413 );
        }
        $data_arr = json_decode( $input, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            wp_send_json( [
                'success'   => false,
                'retryable' => false,
                'error'     => 'invalid_json',
            ], 400 );
        }
        $response = $this->fupi_process_server_calls( $data_arr, true, true );
        wp_send_json( $response->get_data(), $response->get_status() );
    }

    // Abuse control
    private function fupi_server_rate_limit_ok( $user_ip ) {
        $limit = (int) apply_filters( 'fupi_public_server_rate_limit', 120 );
        if ( $limit < 1 ) {
            return true;
        }
        $window = floor( time() / MINUTE_IN_SECONDS );
        $key = 'fupi_srv_' . substr( hash( 'sha256', $user_ip . '|' . $window ), 0, 32 );
        $count = (int) get_transient( $key );
        if ( $count >= $limit ) {
            return false;
        }
        set_transient( $key, $count + 1, MINUTE_IN_SECONDS + 10 );
        return true;
    }

    private function fupi_validate_server_event( $event_type, $event_id, $event_payload ) {
        // Define which event IDs each server-side operation may dispatch.
        $allowlist = apply_filters( 'fupi_public_server_event_allowlist', [
            'cdb'     => [],
            'send'    => ['fbp', 'simpl'],
            'process' => ['proofrec_db_save', 'woo_order_tracking'],
        ] );
        // Reject unknown operation types, including malformed filtered allowlists.
        if ( !is_string( $event_type ) || !is_array( $allowlist ) || !isset( $allowlist[$event_type] ) ) {
            return new WP_Error('invalid_event_type', 'Unsupported event type.');
        }
        // CDB events have no event ID; all other operations require an allowlisted ID.
        if ( $event_type !== 'cdb' && (!is_string( $event_id ) || !in_array( $event_id, $allowlist[$event_type], true )) ) {
            return new WP_Error('invalid_event_id', 'Unsupported event ID.');
        }
        // JSON objects are decoded as associative arrays before reaching this validator.
        if ( !is_array( $event_payload ) ) {
            return new WP_Error('invalid_payload', 'Event payload must be an object.');
        }
        // Validate the shared consent-record schema used by CDB and ProofRec.
        if ( $event_type === 'cdb' || $event_id === 'proofrec_db_save' ) {
            if ( empty( $event_payload['cdbID'] ) || !is_string( $event_payload['cdbID'] ) || strlen( $event_payload['cdbID'] ) > 50 || !preg_match( '/^[A-Za-z0-9._:-]+$/', $event_payload['cdbID'] ) || empty( $event_payload['timestamp'] ) || !is_numeric( $event_payload['timestamp'] ) || !isset( $event_payload['stats'], $event_payload['marketing'], $event_payload['personalisation'] ) || !is_bool( $event_payload['stats'] ) || !is_bool( $event_payload['marketing'] ) || !is_bool( $event_payload['personalisation'] ) ) {
                return new WP_Error('invalid_consent_payload', 'Invalid consent payload.');
            }
        }
        // WooCommerce tracking requires a bounded request ID and consent collection.
        if ( $event_id === 'woo_order_tracking' ) {
            if ( empty( $event_payload['request_id'] ) || !is_string( $event_payload['request_id'] ) || strlen( $event_payload['request_id'] ) > 64 || !preg_match( '/^[A-Za-z0-9._:-]+$/', $event_payload['request_id'] ) || empty( $event_payload['consents'] ) || !is_array( $event_payload['consents'] ) ) {
                return new WP_Error('invalid_woo_payload', 'Invalid WooCommerce payload.');
            }
        }
        // Meta server events require identity, timing, and user-data fields.
        if ( $event_id === 'fbp' ) {
            if ( empty( $event_payload['event_name'] ) || !is_string( $event_payload['event_name'] ) || strlen( $event_payload['event_name'] ) > 100 || empty( $event_payload['event_id'] ) || !is_string( $event_payload['event_id'] ) || strlen( $event_payload['event_id'] ) > 200 || empty( $event_payload['event_time'] ) || !is_numeric( $event_payload['event_time'] ) || empty( $event_payload['user_data'] ) || !is_array( $event_payload['user_data'] ) ) {
                return new WP_Error('invalid_fbp_payload', 'Invalid Meta payload.');
            }
        }
        // Simple Analytics forwarding accepts only a valid URL and bounded user agent.
        if ( $event_id === 'simpl' ) {
            if ( empty( $event_payload['url'] ) || !is_string( $event_payload['url'] ) || strlen( $event_payload['url'] ) > 2048 || !filter_var( $event_payload['url'], FILTER_VALIDATE_URL ) || empty( $event_payload['ua'] ) || !is_string( $event_payload['ua'] ) || strlen( $event_payload['ua'] ) > 1000 ) {
                return new WP_Error('invalid_simpl_payload', 'Invalid Simple Analytics payload.');
            }
        }
        return true;
    }

    private function fupi_normalize_process_result( $event_id, $handler_result ) {
        // Convert WordPress errors into the common result shape and retry server-side failures.
        if ( is_wp_error( $handler_result ) ) {
            $error_data = $handler_result->get_error_data();
            $status = ( is_array( $error_data ) && !empty( $error_data['status'] ) ? (int) $error_data['status'] : 500 );
            return [
                'event_id'  => $event_id,
                'success'   => false,
                'saved'     => false,
                'retryable' => $status >= 500,
                'error'     => $handler_result->get_error_code(),
            ];
        }
        // Unwrap REST responses so all successful handlers follow the same array contract.
        if ( $handler_result instanceof WP_REST_Response ) {
            $handler_result = $handler_result->get_data();
        }
        // Treat unsupported return types as permanent handler contract failures.
        if ( !is_array( $handler_result ) ) {
            return [
                'event_id'  => $event_id,
                'success'   => false,
                'saved'     => false,
                'retryable' => false,
                'error'     => 'invalid_handler_result',
            ];
        }
        // Apply safe defaults while preventing handlers from replacing the requested event ID.
        return array_merge( [
            'event_id'  => $event_id,
            'success'   => false,
            'saved'     => false,
            'retryable' => false,
        ], $handler_result, [
            'event_id' => $event_id,
        ] );
    }

    // Process the payload. Both REST and AJAX receive the same response shape.
    public function fupi_process_server_calls( $request, $is_ajax = false, $rate_limit_checked = false ) {
        // REST supplies raw JSON, while AJAX has already parsed the payload into an array.
        $body = ( $is_ajax ? false : $request->get_body() );
        $userIP = $this->get_current_user_ip();
        // AJAX is checked before JSON decoding in its transport handler.
        if ( !$rate_limit_checked && !$this->fupi_server_rate_limit_ok( $userIP ) ) {
            $response = new WP_REST_Response([
                'success'   => false,
                'retryable' => true,
                'error'     => 'rate_limited',
            ], 429);
            $response->header( 'Retry-After', '60' );
            return $response;
        }
        // Reject oversized REST bodies before decoding them
        if ( !$is_ajax && strlen( $body ) > $this->server_max_body_size ) {
            return new WP_REST_Response([
                'success'   => false,
                'retryable' => false,
                'error'     => 'request_too_large',
            ], 413);
        }
        $data_arr = ( $is_ajax ? $request : json_decode( $body, true ) );
        // Reject malformed REST JSON
        if ( !$is_ajax && json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_REST_Response([
                'success'   => false,
                'retryable' => false,
                'error'     => 'invalid_json',
            ], 400);
        }
        // Reject empty arrays
        if ( !is_array( $data_arr ) || empty( $data_arr ) || count( $data_arr ) > $this->server_max_batch_size ) {
            return new WP_REST_Response([
                'success'   => false,
                'retryable' => false,
                'error'     => 'invalid_batch',
            ], 400);
        }
        $requests_a = [];
        $results = [];
        $delivery_ranges = [];
        $valid_events = 0;
        // One-by-one, validate data of every event and process it
        foreach ( $data_arr as $event_data ) {
            // Skip is we don't have exactly 3 event params ['send','fbp',payload]
            if ( !is_array( $event_data ) || count( $event_data ) !== 3 ) {
                $results[] = [
                    'event_id'  => false,
                    'success'   => false,
                    'retryable' => false,
                    'error'     => 'invalid_event',
                ];
                continue;
            }
            $event_type = $event_data[0];
            $event_id = $event_data[1];
            $event_payload = $event_data[2];
            $validation = $this->fupi_validate_server_event( $event_type, $event_id, $event_payload );
            if ( is_wp_error( $validation ) ) {
                $results[] = [
                    'event_id'  => ( is_string( $event_id ) ? $event_id : false ),
                    'success'   => false,
                    'retryable' => false,
                    'error'     => $validation->get_error_code(),
                ];
                continue;
            }
            $valid_events++;
            // Prepare the data to be sent
            // CDB and send events enqueue outbound requests; process events run local handlers.
            if ( $event_type === 'cdb' ) {
                $request_count = count( $requests_a );
                $requests_a = $this->prepare_cdb_data( $requests_a, $event_payload );
                $prepared = count( $requests_a ) > $request_count;
                if ( $prepared ) {
                    $delivery_ranges[count( $results )] = [$request_count, count( $requests_a )];
                }
                $results[] = [
                    'event_id'  => 'cdb',
                    'success'   => $prepared,
                    'sent'      => $prepared,
                    'retryable' => false,
                    'error'     => ( $prepared ? false : 'cdb_storage_unavailable' ),
                ];
            } else {
                if ( $event_type === 'send' ) {
                    $request_count = count( $requests_a );
                    $prepared_requests = apply_filters(
                        'fupi_prepare_' . $event_id . '_server_request_data',
                        $requests_a,
                        $event_payload,
                        $userIP
                    );
                    $prepared = is_array( $prepared_requests ) && count( $prepared_requests ) > $request_count;
                    if ( is_array( $prepared_requests ) ) {
                        $requests_a = $prepared_requests;
                    }
                    if ( $prepared ) {
                        $delivery_ranges[count( $results )] = [$request_count, count( $requests_a )];
                    }
                    $results[] = [
                        'event_id'  => $event_id,
                        'success'   => $prepared,
                        'sent'      => $prepared,
                        'retryable' => false,
                        'error'     => ( $prepared ? false : 'server_request_unavailable' ),
                    ];
                } else {
                    if ( $event_type === 'process' ) {
                        $handler_result = apply_filters(
                            'fupi_process_' . $event_id . '_server_result',
                            null,
                            $event_payload,
                            $userIP
                        );
                        // !! LEGACY !!
                        // Preserve extensions using the legacy action. Persistence-aware
                        // built-in handlers use the result-producing filter above.
                        // if ( $handler_result === null && has_action( 'fupi_do_' . $event_id . '_server_action' ) ) {
                        // 	do_action( 'fupi_do_' . $event_id . '_server_action', $event_payload, $userIP );
                        // 	$handler_result = [
                        // 		'success' => true,
                        // 		'processed' => true,
                        // 		'retryable' => false,
                        // 	];
                        // }
                        $results[] = $this->fupi_normalize_process_result( $event_id, $handler_result );
                    }
                }
            }
        }
        $responses = [];
        // Execute all prepared remote requests together and collect any requested responses.
        if ( !empty( $requests_a ) ) {
            include FUPI_PATH . '/public/common/send-to-remote-server.php';
        }
        $delivery_results_by_index = [];
        foreach ( $fupi_remote_delivery_results ?? [] as $delivery_result ) {
            $delivery_results_by_index[$delivery_result['request_index']] = $delivery_result;
        }
        foreach ( $delivery_ranges as $result_index => $range ) {
            for ($request_index = $range[0]; $request_index < $range[1]; $request_index++) {
                $delivery_result = $delivery_results_by_index[$request_index] ?? false;
                if ( !$delivery_result || $delivery_result['curl_errno'] !== CURLE_OK || $delivery_result['http_code'] < 200 || $delivery_result['http_code'] >= 300 ) {
                    $results[$result_index]['success'] = false;
                    $results[$result_index]['sent'] = false;
                    $results[$result_index]['error'] = 'delivery_failed';
                    break;
                }
            }
        }
        // The batch succeeds only when every per-event result succeeds.
        $success = !empty( $results );
        foreach ( $results as $result ) {
            if ( empty( $result['success'] ) ) {
                $success = false;
                break;
            }
        }
        $response_data = [
            'success' => $success,
            'results' => $results,
        ];
        // Keep response keys such as CDB and debug provider names backwards compatible.
        if ( !empty( $responses ) ) {
            unset($responses['success'], $responses['results']);
            // this prevents "succes" and "results" from $responses from overwriting the $response_data below
            $response_data = array_merge( $response_data, $responses );
        }
        // Return 200 for a syntactically valid batch, even when an individual event fails.
        return new WP_REST_Response($response_data, ( $valid_events > 0 ? 200 : 400 ));
    }

    // HTML MODS
    public function fupi_maybe_buffer_output() {
        ob_start( array($this, 'fupi_return_buffer') );
    }

    public function fupi_return_buffer( $html ) {
        if ( !$html ) {
            return $html;
        }
        // Copy HTML
        $orig_html = $html;
        if ( !empty( $this->tools['cook'] ) && !empty( $this->cook ) ) {
            // SCRIPTS BLOCKER
            if ( !function_exists( 'fupi_get_script_blocking_rules' ) ) {
                require_once FUPI_PATH . '/public/modules/cook/blockscr_rules.php';
            }
            $tools_to_block = fupi_get_script_blocking_rules( $this->cook, true );
            if ( !empty( $tools_to_block ) ) {
                if ( !empty( $this->cook['dynamic_blocking'] ) ) {
                    include_once FUPI_PATH . '/public/modules/cook/blockscr_parser.php';
                } else {
                    include_once FUPI_PATH . '/public/modules/cook/blockscr_parser_old.php';
                }
            }
            // IFRAMES BLOCKER
            $iframeblock_enabled = !empty( $this->cook['iframe_auto_rules'] ) || !empty( $this->cook['control_other_iframes'] ) && !empty( $this->cook['iframe_manual_rules'] );
            if ( $iframeblock_enabled ) {
                // make sure we do not try to manage iframes in the bricks builder editor (it breaks)
                $can_load_iframe_parser = !(function_exists( 'bricks_is_builder' ) && bricks_is_builder());
                if ( $can_load_iframe_parser ) {
                    include_once FUPI_PATH . '/public/modules/cook/iframeblock_parser.php';
                }
            }
        }
        // SAFE FONTS
        if ( isset( $this->tools['safefonts'] ) ) {
            include_once dirname( __FILE__ ) . '/common/safefonts_parser.php';
        }
        if ( !empty( $html ) ) {
            return $html;
        }
        return $orig_html;
    }

}
