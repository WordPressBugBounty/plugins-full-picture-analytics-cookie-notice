<?php

class Fupi {
    // public $fupi_versions;
    protected $version;

    protected $loader;

    protected $tools;

    protected $main;

    protected $track;

    protected $modules;

    protected $plugin_name;

    public function __construct() {
        if ( defined( 'FUPI_VERSION' ) ) {
            $this->version = FUPI_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'full_picture';
        $this->tools = get_option( 'fupi_tools' );
        // Enable GTAG module if google ads or analytics are enabled
        if ( !empty( $this->tools['ga41'] ) || !empty( $this->tools['gads'] ) ) {
            $this->tools['gtag'] = true;
        }
        $this->main = get_option( 'fupi_main' );
        $this->track = get_option( 'fupi_track' );
        // $this->fupi_versions = get_option('fupi_versions');
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }

    private function load_dependencies() {
        // MODULES DATA
        require_once FUPI_PATH . '/includes/fupi_modules_data.php';
        $this->modules = $fupi_modules;
        // STANDARD FUPI
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fupi-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fupi-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fupi-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fupi-public.php';
        $this->loader = new Fupi_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Fupi_i18n();
        $this->loader->add_action( 'init', $plugin_i18n, 'fupi_load_plugin_textdomain' );
        $this->loader->add_filter(
            'load_textdomain_mofile',
            $plugin_i18n,
            'fupi_load_textdomain_mofile',
            10,
            2
        );
    }

    //
    // ADMIN HOOKS
    //
    private function define_admin_hooks() {
        $plugin_admin = new Fupi_Admin($this->get_plugin_name(), $this->get_version());
        // Perform updates
        $this->loader->add_action( 'init', $plugin_admin, 'perform_updates' );
        $this->loader->add_action( 'admin_post_fupi_retry_settings_update', $plugin_admin, 'retry_settings_update' );
        // LOAD MODULES
        foreach ( $this->modules as $module ) {
            if ( empty( $this->tools[$module['id']] ) && !isset( $module['always_enabled'] ) ) {
                continue;
            }
            if ( is_customize_preview() && empty( $module['load_in_customizer'] ) ) {
                continue;
            }
            switch ( $module['id'] ) {
                default:
                    $plugin_admin->load_module( $module['id'], $module['is_premium'], $module['admin_module_path'] ?? '' );
                    break;
            }
        }
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'fupi_enqueue_scripts' );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'fupi_custom_admin_styles' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'fupi_add_admin_page_links' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'fupi_add_stats_reports_pages' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'fupi_register_settings' );
        // $this->loader->add_action( 'admin_init', $plugin_admin, 'fupi_activation_redirect' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'fupi_admin_notices' );
        // AJAX USER SEARCH (for the settings field)
        $this->loader->add_action( 'wp_ajax_fupi_search_users', $plugin_admin, 'fupi_search_users_callback' );
        $this->loader->add_action( 'wp_ajax_fupi_search_pages', $plugin_admin, 'fupi_search_pages_callback' );
        // AJAX UPDATES OF GENERAL SETTINGS
        $this->loader->add_action( 'wp_ajax_fupi_update_modes', $plugin_admin, 'fupi_update_main_options_callback' );
        // AJAX SETTINGS PANEL
        $this->loader->add_action( 'wp_ajax_fupi_load_field_form', $plugin_admin, 'fupi_load_field_form_callback' );
        $this->loader->add_action( 'wp_ajax_fupi_save_field_settings', $plugin_admin, 'fupi_save_field_settings_callback' );
        $this->loader->add_action( 'wp_ajax_fupi_load_gdpr_helper', $plugin_admin, 'fupi_load_gdpr_helper_callback' );
    }

    //
    // PUBLIC HOOKS
    //
    private function define_public_hooks() {
        $versions = get_option( 'fupi_versions' );
        $missing_option = new stdClass();
        $failure_record = get_option( 'fupi_updater_failures', $missing_option );
        $has_failure_record = $failure_record !== $missing_option;
        $restore_pending = get_option( 'fupi_backup_restore_safety', $missing_option ) !== $missing_option;
        $is_current = is_array( $versions ) && ($versions[1] ?? null) === FUPI_VERSION;
        $is_exhausted = false;
        if ( !$is_current && $has_failure_record ) {
            $is_valid_record = is_array( $failure_record ) && count( $failure_record ) === 2 && array_key_exists( 'target_version', $failure_record ) && array_key_exists( 'failures', $failure_record ) && is_string( $failure_record['target_version'] ) && is_int( $failure_record['failures'] ) && $failure_record['failures'] >= 0;
            $is_exhausted = !$is_valid_record || FUPI_VERSION === $failure_record['target_version'] && $failure_record['failures'] >= 3;
        }
        if ( $restore_pending || !$is_current && !$is_exhausted ) {
            if ( !defined( 'DONOTCACHEPAGE' ) ) {
                define( 'DONOTCACHEPAGE', true );
            }
            nocache_headers();
        }
        if ( empty( $this->tools ) ) {
            return;
        }
        // Do not run public hooks if data is not current/updated
        if ( !$is_current || $restore_pending ) {
            return;
        }
        $plugin_public = new Fupi_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action(
            'wp_head',
            $plugin_public,
            'fupi_output_fupi_data_in_head',
            -1000
        );
        // includes all the "Get" scripts
        $this->loader->add_filter(
            'script_loader_tag',
            $plugin_public,
            'add_nodefer_to_fupi_scripts',
            10,
            3
        );
        $this->loader->add_filter( 'rocket_defer_inline_exclusions', $plugin_public, 'fupi_rocket_exclude_inline_js' );
        $this->loader->add_filter( 'rocket_exclude_js', $plugin_public, 'fupi_rocket_exclude_js_files' );
        $this->loader->add_filter( 'rocket_delay_js_exclusions', $plugin_public, 'fupi_rocket_exclude_js_files' );
        $this->loader->add_filter( 'rocket_exclude_defer_js', $plugin_public, 'fupi_rocket_exclude_js_files' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'fupi_enqueue_core_js' );
        // LOAD MODULES
        // Load when enabled
        $gtools_loaded = false;
        $html_mods_loaded = false;
        if ( !empty( $this->tools ) ) {
            foreach ( $this->modules as $module ) {
                if ( $module['id'] == 'modules' ) {
                    continue;
                }
                // always enabled
                if ( empty( $this->tools[$module['id']] ) && !isset( $module['always_enabled'] ) ) {
                    continue;
                }
                if ( is_customize_preview() && empty( $module['load_in_customizer'] ) ) {
                    continue;
                }
                switch ( $module['id'] ) {
                    case 'ga41':
                    case 'ga42':
                    case 'gads':
                        if ( !$gtools_loaded ) {
                            $gtools_loaded = true;
                            $plugin_public->load_module( 'gtools', false );
                        }
                        break;
                    case 'gtm':
                        $plugin_public->load_module( 'gotm', false );
                        break;
                    default:
                        $plugin_public->load_module( $module['id'], $module['is_premium'], $module['public_module_path'] ?? '' );
                        break;
                }
            }
        }
        // The sender route writes to the WooCommerce session and must stay front-end
        // classified for WooCommerce 11.1.2+ (see Fupi_Public::fupi_sender_is_frontend_request)
        $this->loader->add_filter( 'woocommerce_is_rest_api_request', $plugin_public, 'fupi_sender_is_frontend_request' );
        // SERVER OPERATIONS
        $this->loader->add_action( 'wp_ajax_nopriv_fupi_ajax', $plugin_public, 'fupi_ajax_hooks' );
        $this->loader->add_action( 'wp_ajax_fupi_ajax', $plugin_public, 'fupi_ajax_hooks' );
        $this->loader->add_action( 'rest_api_init', $plugin_public, 'fupi_rest_hooks' );
        // MODIFY HTML
        $this->loader->add_action(
            'template_redirect',
            $plugin_public,
            'fupi_maybe_buffer_output',
            2
        );
    }

    public function run() {
        $this->loader->run();
    }

}
