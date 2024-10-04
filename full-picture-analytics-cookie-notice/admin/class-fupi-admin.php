<?php

class Fupi_Admin {
    private $plugin_name;

    private $version;

    private $versions;

    private $tools;

    private $main;

    private $cook_enabled;

    private $geo_enabled;

    private $cook;

    private $user_cap;

    private $trackmeta;

    private $is_woo_enabled;

    private $licence;

    private $reports;

    private $enable_woo_descr_text;

    private $sync_run;

    private $fupi_report_pages = [];

    private $run_cdb;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->versions = get_option( 'fupi_versions' );
        $this->tools = get_option( 'fupi_tools' );
        $this->reports = get_option( 'fupi_reports' );
        $this->main = get_option( 'fupi_main' );
        $this->cook_enabled = !empty( $this->tools ) && isset( $this->tools['cook'] );
        // makes 2 checks for backwards compat.
        $this->geo_enabled = !empty( $this->tools ) && isset( $this->tools['geo'] );
        $this->cook = get_option( 'fupi_cook' );
        $this->run_cdb = $this->cook_enabled && !empty( $this->cook ) && !empty( $this->cook['cdb_key'] );
        $this->trackmeta = get_option( 'fupi_trackmeta' );
        $this->user_cap = 'manage_options';
        $this->is_woo_enabled = false;
        $this->enable_woo_descr_text = '<div class="fupi_enable_woo_notice">' . esc_html__( 'Enable WooCommerce plugin and WooCommerce Tracking module.', 'full-picture-analytics-cookie-notice' ) . '</div>';
        $this->licence = 'free';
        // changed below
        $this->sync_run = false;
        // Test to see if WooCommerce is active (including network activated).
        // https://woocommerce.com/document/create-a-plugin/#section-1
        if ( isset( $this->tools['woo'] ) ) {
            $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';
            if ( function_exists( 'wp_get_active_and_valid_plugins' ) && in_array( $plugin_path, wp_get_active_and_valid_plugins() ) || function_exists( 'wp_get_active_network_plugins' ) && in_array( $plugin_path, wp_get_active_network_plugins() ) ) {
                $this->is_woo_enabled = true;
                $this->enable_woo_descr_text = '';
            }
        }
    }

    public function string_settings_to_array( $opt_name, $settings_names = [], $simple_arr_settings_names = [] ) {
        $opt = get_option( $opt_name );
        if ( isset( $opt ) ) {
            if ( count( $settings_names ) > 0 ) {
                foreach ( $settings_names as $set_name ) {
                    if ( isset( $opt[$set_name] ) && is_string( $opt[$set_name] ) ) {
                        $set_val = $opt[$set_name];
                        $lines_arr = explode( PHP_EOL, $set_val );
                        $ret_arr = [];
                        foreach ( $lines_arr as $line ) {
                            $line = trim( $line );
                            $sub_arr = [];
                            if ( str_contains( $line, '@' ) ) {
                                $vals = explode( '@', $line );
                                $sub_arr['sel'] = trim( $vals[0] );
                                $sub_arr['val'] = trim( $vals[1] );
                                array_push( $ret_arr, $sub_arr );
                            } else {
                                $sub_arr['sel'] = $line;
                                array_push( $ret_arr, $sub_arr );
                            }
                        }
                        $opt[$set_name] = $ret_arr;
                    }
                }
            }
            if ( count( $simple_arr_settings_names ) > 0 ) {
                foreach ( $simple_arr_settings_names as $simp_name ) {
                    if ( isset( $opt[$simp_name] ) && is_string( $opt[$simp_name] ) ) {
                        $ret_arr = [];
                        $vals = explode( '@', $opt[$simp_name] );
                        $ret_arr['sel'] = trim( $vals[0] );
                        $ret_arr['val'] = trim( $vals[1] );
                        $opt[$simp_name] = $ret_arr;
                    }
                }
            }
            update_option( $opt_name, $opt );
        }
    }

    public function perform_updates() {
        // clears cache at the end
        require_once FUPI_PATH . '/includes/fupi_updater.php';
    }

    // Plugin activation callback. Registers option to redirect on next admin load.
    // Saves user ID to ensure it only redirects for the user who activated the plugin.
    // public function fupi_activation_redirect() {
    // 	// Make sure it's the correct user
    // 	if ( !wp_doing_ajax() && intval( get_option( 'fupi_activation_redirect' ) ) === wp_get_current_user()->ID ) {
    // 		// Make sure we don't redirect again after this one
    // 		delete_option( 'fupi_activation_redirect' );
    // 		wp_safe_redirect( admin_url( '/admin.php?page=full_picture_tools' ) );
    // 		exit;
    // 	}
    // }
    public function fupi_custom_admin_styles() {
        echo '<style>
			.column-fupi_order_data{
				width: 30px !important;
				max-width: 30px !important;
				box-sizing: border-box;
				text-align: center;
			}
		</style>';
    }

    public function fupi_enqueue_scripts( $hook ) {
        // everything that is not customizer
        if ( !is_customize_preview() ) {
            wp_enqueue_script(
                'fupi-whole_admin-js',
                plugin_dir_url( __FILE__ ) . 'settings/js/fupi-whole-admin.js',
                array(),
                $this->version,
                true
            );
            wp_register_style(
                'fupi-select2-css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                array(),
                '4.1.0-rc.0'
            );
            wp_register_script(
                'fupi-select2-js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                array('jquery', 'fupi-admin-helpers-js'),
                '4.1.0-rc.0'
            );
        }
        // SETTINGS PAGE
        if ( strrpos( $hook, 'full_picture_' ) !== false ) {
            // for top level page use "toplevel_page_fupi"
            $req = array();
            // Add WP Internal Pointers...
            // https://stackoverflow.com/questions/30945793/how-do-you-create-a-basic-wordpress-admin-pointer
            // wp_enqueue_style( 'wp-pointer' );
            // wp_enqueue_script( 'wp-pointer' );
            array_push( $req, 'jquery', 'fupi-admin-helpers-js' );
            wp_enqueue_style(
                'fupi-admin',
                plugin_dir_url( __FILE__ ) . 'settings/css/fupi-admin.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_script(
                'fupi-admin-helpers-js',
                plugin_dir_url( __FILE__ ) . 'settings/js/fupi-admin-helpers.js',
                array(),
                $this->version,
                true
            );
            wp_enqueue_script(
                'fupi-admin-js',
                plugin_dir_url( __FILE__ ) . 'settings/js/fupi-admin.js',
                $req,
                $this->version,
                true
            );
        }
        // REPORTS PAGE
        if ( strrpos( $hook, 'fp_reports' ) !== false ) {
            wp_enqueue_style(
                'fupi-admin-reports',
                plugin_dir_url( __FILE__ ) . 'settings/pages/fupi_reports.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_script(
                'fupi-admin-helpers-js',
                plugin_dir_url( __FILE__ ) . 'settings/js/fupi-admin-helpers.js',
                array(),
                $this->version,
                true
            );
        }
    }

    public function fupi_search_users_callback() {
        // Check if the current user is an administrator
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'full-picture-analytics-cookie-notice' ) );
        }
        $search = ( isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '' );
        $users = get_users( array(
            'search'         => "*{$search}*",
            'search_columns' => array('user_login', 'user_email'),
            'number'         => 20,
        ) );
        $results = array();
        foreach ( $users as $user ) {
            $results[] = array(
                'id'   => $user->ID,
                'text' => sprintf( '%s (%s)', $user->user_login, $user->user_email ),
            );
        }
        wp_send_json( $results );
    }

    // CUSTOMIZER
    // Register customizer settings
    public function fupi_customize_register( $wp_customize ) {
        if ( $this->cook_enabled ) {
            if ( !function_exists( 'fupi_disable_customizer' ) ) {
                include_once 'customizer/fupi-customizer-settings.php';
            }
        }
    }

    // Sanitize customizer settings
    public function fupi_customizer_sanitize( $val, $setting ) {
        if ( !function_exists( 'fupi_disable_customizer' ) ) {
            $sanitized = (include 'customizer/fupi-customizer-sanitize.php');
            // a workaround to get the value returned from the included file
            return $sanitized;
        }
    }

    // Send customizer settings to CDB
    public function fupi_customize_save_after() {
    }

    // Enqueue customizer preview scripts
    public function fupi_customizer_preview_scripts() {
        if ( !function_exists( 'fupi_disable_customizer' ) && $this->cook_enabled ) {
            wp_enqueue_script(
                'fupi-customizer-preview',
                plugin_dir_url( __FILE__ ) . 'customizer/js/fupi-customizer-preview.js',
                array('customize-preview', 'jquery'),
                $this->version,
                true
            );
        }
    }

    // Enqueue customizer controls scripts and styles
    public function fupi_enqueue_customizer_css_js() {
        if ( $this->cook_enabled ) {
            if ( !function_exists( 'fupi_disable_customizer' ) ) {
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                wp_enqueue_script(
                    'fupi-customizer-controls',
                    plugin_dir_url( __FILE__ ) . 'customizer/js/fupi-customizer-controls.js',
                    array('jquery', 'customize-controls'),
                    $this->version,
                    true
                );
                wp_enqueue_style(
                    'fupi-customizer-css',
                    plugin_dir_url( __FILE__ ) . 'customizer/css/fupi-customizer.css',
                    array(),
                    $this->version,
                    'all'
                );
            }
        }
    }

    // ADMIN SETTINGS
    public function fupi_add_admin_page() {
        include FUPI_PATH . '/includes/fupi_modules_data.php';
        $fupi_page_title = ( !empty( $this->main ) && isset( $this->main['custom_menu_title'] ) ? esc_attr( $this->main['custom_menu_title'] ) : "WP Full Picture" );
        $show_main = false;
        add_menu_page(
            'WP Full Picture',
            // page title
            $fupi_page_title,
            // menu title
            $this->user_cap,
            // capability
            'full_picture_tools',
            // menu slug
            array($this, 'fupi_display_admin_page'),
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDU5NSA2MzciIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM6c2VyaWY9Imh0dHA6Ly93d3cuc2VyaWYuY29tLyIgc3R5bGU9ImZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO3N0cm9rZS1saW5lam9pbjpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDoyOyI+CiAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgyLjk5NTM2LDAsMCwyLjk5NTM2LC01NTcuMzQ0LC04NjYuNTA0KSI+CiAgICAgICAgPHBhdGggZD0iTTM2My43ODQsNDk4LjQ5MkwzMzkuOTI5LDQ3NC42NzJDMzM5LjMzNCw0NzQuMDcgMzM4LjgxMSw0NzMuNDMyIDMzOC4zODgsNDcyLjc1MkMzMjMuMDYsNDgyLjQxOCAzMDQuOTAyLDQ4OC4wMjIgMjg1LjQzOSw0ODguMDIyQzIzMC41NzcsNDg4LjAyMiAxODYuMDQ4LDQ0My41IDE4Ni4wNjksMzg4LjY2NkMxODYuMDU1LDMzMy43OTcgMjMwLjU4NCwyODkuMjgyIDI4NS40MjUsMjg5LjI4MkMzNDAuMjgsMjg5LjI3NSAzODQuODA5LDMzMy43OTcgMzg0LjgwMiwzODguNjY2QzM4NC44MDksNDE1Ljg0IDM3My44ODEsNDQwLjQ3NiAzNTYuMTc0LDQ1OC40MzRMMzgwLjAzNyw0ODIuMjU0QzM4NC40ODcsNDg2Ljc1NCAzODQuNTA4LDQ5NC4wMiAzODAuMDIyLDQ5OC40OTJDMzc1LjU0NCw1MDIuOTkyIDM2OC4yNjMsNTAyLjk5MiAzNjMuNzg0LDQ5OC40OTJaTTM2NC4wMzUsMzg4LjY1MkMzNjQuMDM1LDM0NS43NjQgMzI5LjI0NCwzMTAuOTk1IDI4Ni4zODUsMzEwLjk5NUMyNDMuNTExLDMxMC45OTUgMjA4LjcxMywzNDUuNzcxIDIwOC43MTMsMzg4LjY2NkMyMDguNzEzLDQzMS41MjYgMjQzLjUwNCw0NjYuMzE3IDI4Ni4zNjMsNDY2LjMxN0MzMjkuMjQ0LDQ2Ni4zMTcgMzY0LjA0Miw0MzEuNTI2IDM2NC4wMzUsMzg4LjY1MloiLz4KICAgIDwvZz4KICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDIuOTk1MzYsMCwwLDIuOTk1MzYsLTU1Ny4zNDQsLTc1Ni4yOTYpIj4KICAgICAgICA8cGF0aCBkPSJNMjM2Ljc5NywzNjkuNDk3TDIzNi43OTcsMzg1Ljk3N0MyMzYuNzk3LDM5NC42NjUgMjQxLjk4OCw0MDEuNzE5IDI0OC4zODEsNDAxLjcxOUwyNDguMzg0LDQwMS43MTlDMjU0Ljc3Nyw0MDEuNzE5IDI1OS45NjgsMzk0LjY2NSAyNTkuOTY4LDM4NS45NzdMMjU5Ljk2OCwzNjkuNDk3QzI1OS45NjgsMzYwLjgwOSAyNTQuNzc3LDM1My43NTUgMjQ4LjM4NCwzNTMuNzU1TDI0OC4zODEsMzUzLjc1NUMyNDEuOTg4LDM1My43NTUgMjM2Ljc5NywzNjAuODA5IDIzNi43OTcsMzY5LjQ5N1oiLz4KICAgIDwvZz4KICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDIuOTk1MzYsMCwwLDIuOTk1MzYsLTU1Ny4zNDQsLTgyMy4zNSkiPgogICAgICAgIDxwYXRoIGQ9Ik0yNzMuODI3LDM2OS41MDJMMjczLjgyNyw0MDguMzU4QzI3My44MjcsNDE3LjA0NiAyNzkuMDE4LDQyNC4xIDI4NS40MTQsNDI0LjFMMjg1LjQxOCw0MjQuMUMyOTEuODE0LDQyNC4xIDI5Ny4wMDUsNDE3LjA0NiAyOTcuMDA1LDQwOC4zNThMMjk3LjAwNSwzNjkuNTAyQzI5Ny4wMDUsMzYwLjgxNCAyOTEuODE0LDM1My43NiAyODUuNDE4LDM1My43NkwyODUuNDE0LDM1My43NkMyNzkuMDE4LDM1My43NiAyNzMuODI3LDM2MC44MTQgMjczLjgyNywzNjkuNTAyWiIvPgogICAgPC9nPgogICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMi45OTUzNiwwLDAsMi45OTUzNiwtNTU3LjM0NCwtODk5LjkzOCkiPgogICAgICAgIDxwYXRoIGQ9Ik0zMTAuODg1LDM2OS41MDFMMzEwLjg4NSw0MzMuOTI4QzMxMC44ODUsNDQyLjYxNiAzMTYuMDc3LDQ0OS42NyAzMjIuNDY5LDQ0OS42N0wzMjIuNDczLDQ0OS42N0MzMjguODY1LDQ0OS42NyAzMzQuMDU3LDQ0Mi42MTYgMzM0LjA1Nyw0MzMuOTI4TDMzNC4wNTcsMzY5LjUwMUMzMzQuMDU3LDM2MC44MTMgMzI4Ljg2NSwzNTMuNzU5IDMyMi40NzMsMzUzLjc1OUwzMjIuNDY5LDM1My43NTlDMzE2LjA3NywzNTMuNzU5IDMxMC44ODUsMzYwLjgxMyAzMTAuODg1LDM2OS41MDFaIi8+CiAgICA8L2c+Cjwvc3ZnPgo=',
            90
        );
        // MODULES
        $modules_opts = [];
        // TOOLS, GENERAL SETTINGS
        foreach ( $fupi_modules as $module ) {
            if ( $module['is_avail'] && $module['has_settings_page'] ) {
                if ( $module['id'] == 'main' || $module['id'] == 'tools' || $module['id'] == 'status' || isset( $this->tools[$module['id']] ) ) {
                    // do not add a page for Woo settings if the plugin is deactivated
                    if ( $module['id'] == 'woo' && empty( $this->is_woo_enabled ) ) {
                        continue;
                    }
                    array_push( $modules_opts, [
                        'full_picture_tools',
                        // parent slug
                        $module['title'],
                        // page title
                        $module['title'],
                        // menu title
                        $this->user_cap,
                        // capability
                        'full_picture_' . $module['id'],
                        // menu slug
                        array($this, 'fupi_display_admin_page'),
                    ] );
                }
            }
        }
        foreach ( $modules_opts as $mod_opts ) {
            add_submenu_page( ...$mod_opts );
        }
    }

    public function fupi_settings_permissions( $cap ) {
        return $this->user_cap;
    }

    public function fupi_add_stats_reports_pages() {
        $current_user_id = get_current_user_id();
        $user_is_admin = current_user_can( 'manage_options' );
        $capability = ( $user_is_admin ? 'manage_options' : 'edit_posts' );
        $is_allowed_user = !empty( $this->main ) && !empty( $this->main['extra_users_2'] ) && in_array( $current_user_id, $this->main['extra_users_2'] );
        // check if current user is allowed to modify settings
        // Report from the Plausible module
        // at the moment the report is shown only to admins
        if ( isset( $this->tools['pla'] ) ) {
            $pla_opts = get_option( 'fupi_pla' );
            // Get dashboard data
            if ( !empty( $pla_opts ) && !empty( $pla_opts['shared_link_url'] ) ) {
                $show_to_current_user = false;
                if ( $user_is_admin || $is_allowed_user || $show_to_current_user ) {
                    $this->fupi_report_pages[] = array(
                        'type'   => 'module',
                        'id'     => 'module_pla',
                        'iframe' => '<iframe plausible-embed="" src="' . esc_url( $pla_opts['shared_link_url'] ) . '&embed=true&theme=light&background=transparent" scrolling="no" frameborder="0" loading="lazy"></iframe>',
                        'title'  => 'Plausible',
                        'width'  => 1200,
                        'height' => 2000,
                    );
                }
            }
        }
        // Reports from the "Reports & Statistics" module
        if ( isset( $this->tools['reports'] ) && !empty( $this->reports ) && !empty( $this->reports['dashboards'] ) ) {
            $show_to_current_user = false;
            // check if current user is allowed to view all reports
            if ( !$user_is_admin && !empty( $this->reports['selected_users'] ) ) {
                $show_to_current_user = in_array( $current_user_id, $this->reports['selected_users'] );
            }
            // go through all dashboards
            foreach ( $this->reports['dashboards'] as $dash ) {
                if ( $user_is_admin || $is_allowed_user || $show_to_current_user ) {
                    $this->fupi_report_pages[] = $dash;
                    $show_to_current_user = false;
                }
            }
        }
        // STOP if there are no reports to show to the current user
        if ( count( $this->fupi_report_pages ) == 0 ) {
            return;
        }
        // GET menu position
        $menu_position = ( isset( $this->tools['reports'] ) && !empty( $this->reports ) && !empty( $this->reports['menu_pos'] ) ? (int) $this->reports['menu_pos'] : 10 );
        // ADD menu page
        add_menu_page(
            $this->fupi_report_pages[0]['title'],
            // page title
            esc_html__( 'Reports', 'full-picture-analytics-cookie-notice' ),
            // menu title
            $capability,
            // capability
            'fp_reports_' . $this->fupi_report_pages[0]['id'],
            // menu slug
            array($this, 'fupi_display_reports_page'),
            'dashicons-chart-pie',
            $menu_position
        );
        // ADD subpages
        foreach ( $this->fupi_report_pages as $i => $db ) {
            add_submenu_page(
                'fp_reports_' . $this->fupi_report_pages[0]['id'],
                // parent slug
                $db['title'],
                // page title
                $db['title'],
                // menu title
                $capability,
                // capability
                'fp_reports_' . $db['id'],
                // menu slug
                array($this, 'fupi_display_reports_page')
            );
        }
    }

    public function fupi_display_admin_page() {
        include_once 'settings/pages/fupi-admin-page-display.php';
    }

    public function fupi_display_reports_page() {
        include_once 'settings/pages/fupi-reports-page-display.php';
    }

    public function fupi_field_html( $recipe, $field_id = false, $saved_value = false ) {
        include 'settings/fupi-admin-fields-html.php';
    }

    public function fupi_clear_cache() {
        include 'settings/fupi-clear-cache.php';
    }

    public function fupi_register_settings() {
        include FUPI_PATH . '/includes/fupi_modules_data.php';
        $active_slug = false;
        $active_page = ( isset( $_GET['page'] ) ? sanitize_html_class( $_GET['page'] ) : false );
        // $disable_for_free_class > $extra_classes
        // find active slug
        if ( $active_page !== false && strpos( $active_page, 'full_picture_' ) === 0 ) {
            $active_slug = str_replace( 'full_picture_', '', $active_page );
        }
        // register all options and display the requested page
        foreach ( $fupi_modules as $module ) {
            if ( !isset( $module['content'] ) ) {
                $module['content'] = 'settings';
            }
            if ( !$module['is_avail'] || $module['content'] == 'info' ) {
                continue;
            }
            $option_group_name = 'fupi_' . $module['id'];
            $option_arr_id = 'fupi_' . $module['id'];
            $slug_part = ( $module['is_premium'] ? $module['id'] . '__premium_only' : $module['id'] );
            $sections = array();
            if ( !$module['is_addon'] ) {
                register_setting( $option_group_name, $option_arr_id, array(
                    'sanitize_callback' => array($this, 'fupi_sanitize_fields_' . $slug_part),
                ) );
            } else {
                do_action( 'fupi_register_setting_' . $module['id'] );
            }
            if ( $active_slug !== false && $active_slug == $module['id'] ) {
                if ( !$module['is_addon'] ) {
                    include_once 'settings/modules/' . $slug_part . '/fupi-admin-tab-' . $slug_part . '.php';
                }
                $sections = apply_filters( 'fupi_' . $module['id'] . '_settings', $sections, $option_arr_id );
                foreach ( $sections as $section ) {
                    // trigger_error('Serialized sections' .  serialize($section));
                    add_settings_section(
                        $section['section_id'],
                        esc_html( $section['section_title'] ),
                        array($this, 'fupi_sections_descriptions'),
                        $option_arr_id
                    );
                    $section_fields = ( isset( $section['fields'] ) ? $section['fields'] : array() );
                    $fields = apply_filters( 'mod_fields_in_section_' . $section['section_id'], $section_fields, $option_arr_id );
                    if ( isset( $fields ) ) {
                        foreach ( $fields as $field ) {
                            add_settings_field(
                                $field['field_id'],
                                $field['label'],
                                array($this, 'fupi_field_html'),
                                $option_arr_id,
                                $section['section_id'],
                                $field
                            );
                        }
                    }
                }
            }
        }
    }

    private function generate_rand_filename( $id ) {
        $chr = 'abcdefghijklmnopqrstuvwxyz';
        $name = $id . '_';
        for ($i = 0; $i < 6; $i++) {
            $j = mt_rand( 0, strlen( $chr ) - 1 );
            $name .= $chr[$j];
        }
        return $name;
    }

    // SANITIZATION
    public function fupi_sanitize_fields_tools( $input ) {
        include 'settings/modules/tools/fupi-admin-sanitize-tools.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_main( $input ) {
        include 'settings/modules/main/fupi-admin-sanitize-main.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_cook( $input ) {
        include 'settings/modules/cook/fupi-admin-sanitize-cook.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_ga41( $input ) {
        include 'settings/modules/ga41/fupi-admin-sanitize-ga4.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_gads( $input ) {
        include 'settings/modules/gads/fupi-admin-sanitize-gads.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_pla( $input ) {
        $pla_opts = get_option( 'fupi_pla' );
        include 'settings/modules/pla/fupi-admin-sanitize-pla.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_clar( $input ) {
        include 'settings/modules/clar/fupi-admin-sanitize-clar.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_cegg( $input ) {
        include 'settings/modules/cegg/fupi-admin-sanitize-cegg.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_woo( $input ) {
        include 'settings/modules/woo/fupi-admin-sanitize-woo.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_mato( $input ) {
        include 'settings/modules/mato/fupi-admin-sanitize-mato.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_mads( $input ) {
        include 'settings/modules/mads/fupi-admin-sanitize-mads.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_fbp1( $input ) {
        include 'settings/modules/fbp1/fupi-admin-sanitize-fb.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_gtm( $input ) {
        include 'settings/modules/gtm/fupi-admin-sanitize-gtm.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_twit( $input ) {
        include 'settings/modules/twit/fupi-admin-sanitize-twit.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_pin( $input ) {
        include 'settings/modules/pin/fupi-admin-sanitize-pin.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_hotj( $input ) {
        include 'settings/modules/hotj/fupi-admin-sanitize-hotj.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_linkd( $input ) {
        include 'settings/modules/linkd/fupi-admin-sanitize-linkd.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_tik( $input ) {
        include 'settings/modules/tik/fupi-admin-sanitize-tik.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_posthog( $input ) {
        include 'settings/modules/posthog/fupi-admin-sanitize-posthog.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_simpl( $input ) {
        include 'settings/modules/simpl/fupi-admin-sanitize-simpl.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_insp( $input ) {
        include 'settings/modules/insp/fupi-admin-sanitize-insp.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_cscr( $input ) {
        include 'settings/modules/cscr/fupi-admin-sanitize-cscr.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_privex( $input ) {
        include 'settings/modules/privex/fupi-admin-sanitize-privex.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_track404( $input ) {
        include 'settings/modules/track404/fupi-admin-sanitize-track404.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_iframeblock( $input ) {
        include 'settings/modules/iframeblock/fupi-admin-sanitize-iframeblock.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_blockscr( $input ) {
        include 'settings/modules/blockscr/fupi-admin-sanitize-blockscr.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    public function fupi_sanitize_fields_reports( $input ) {
        include 'settings/modules/reports/fupi-admin-sanitize-reports.php';
        include 'settings/fupi-clear-cache.php';
        return $clean_data;
    }

    // DESCRIPTIONS
    public function fupi_sections_descriptions( $a ) {
        include FUPI_PATH . '/includes/fupi_modules_data.php';
        $arr = explode( '_', $a['id'] );
        $tab_slug = $arr[1];
        foreach ( $fupi_modules as $module ) {
            if ( $module['id'] == $tab_slug ) {
                if ( !$module['is_addon'] ) {
                    if ( $module['is_premium'] ) {
                        $tab_slug = $tab_slug . '__premium_only';
                    }
                    // get a description
                    $ret_txt = (include 'settings/modules/' . $tab_slug . '/fupi-admin-descr_' . $tab_slug . '.php');
                } else {
                    $ret_txt = apply_filters( 'fupi_get_setting_descr', $a['id'] );
                }
            }
        }
        // wrap the text in descr HTML and echo
        if ( !empty( $ret_txt ) ) {
            echo '<div class="fupi_section_descr fupi_el">' . $ret_txt . '</div>';
        }
    }

    //
    // ADMIN NOTICES #1
    //
    public function fupi_admin_notices() {
        // show notices only to admins
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        // load class
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fupi-notices.php';
        $fupi_notices = new \FUPI\FUPI_Notices();
        // USED IDS:
        // fupi_fth_nosupport_notice
        // fupi_fth_delete_notice
        // fupi_pla_featureremoval_notice
        // fupi_update_to_2-0-0_notice
        // fupi_update_to_2-4-0_notice
        // fupi_updated_to_3-0-0_notice
        // fupi_author_ids
        // fupi_custom_notice_removal
        // fupi_fresh_install_tutorials
        // fupi_autoupdate_reminder3
        // fupi_remind_to_move_to_ga4
        // fupi_changes_in_free
        // fupi_gtm_v1_deprecation
        // fupi_review_14_days
        // fupi_gtm_v2_deprecation
        // fupi_gtm_v2_deprecation_2
        // if ( is_multisite() ){
        // 	$plugins_page_url = network_home_url() . 'wp-admin/network/plugins.php';
        // } else {
        // 	$plugins_page_url = get_admin_url() . 'plugins.php';
        // };
        // REMINDER TO LEAVE REVIEW
        // show only if the plugin was installed at least 14 days ago
        // $fupi_version = get_option('fupi_versions');
        // $date = new DateTime();
        // if ( ! empty( $fupi_version ) && ! empty( $fupi_version[0] ) && $date->getTimestamp() - $fupi_version[0] > ( 14 * 24 * 60 * 60 ) ) {
        // 	$fupi_notices->add(
        // 		'fupi_review_14_days',
        // 		'',
        // 		sprintf( esc_html__('It took 4100+ hours to build WP Full Picture? Please, take 5 minutes of your time to %1$srate it ★★★★★%2$s. Thank you!','full-picture-analytics-cookie-notice'),'<a href="https://wordpress.org/support/plugin/full-picture-analytics-cookie-notice/reviews/">','</a>' ),
        // 		array(
        // 			'type'  => 'warning',
        // 			'scope' => 'user',
        // 		)
        // 	);
        // }
        $theme = wp_get_theme();
        if ( $this->cook_enabled && $theme->get( 'Name' ) == 'OceanWP' ) {
            $fupi_notices->add(
                'fupi_uses_oceanwp_theme',
                '',
                sprintf( esc_html__( 'WP Full Picture plugin has detected that you are using OceanWP theme. This theme breaks the controls for styling Consent Banner in the WordPress theme customizer. %1$sLearn what to do about it%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-go-around-the-incompatibility-issues-with-oceanwp-theme/" target="_blank">', '</a>' ),
                array(
                    'type'  => 'error',
                    'scope' => 'user',
                )
            );
        }
        // init
        $fupi_notices->boot();
    }

    //
    // ADD CUSTOM COLUMNS TO WOOCOMMERCE ORDERS
    //
    // register
    public function fupi_register_woo_admin_columns( $defaults ) {
        $defaults['fupi_order_data'] = '<span class="screen-reader-text">' . esc_html__( 'Order conf. seen', 'full-picture-analytics-cookie-notice' ) . '</span><img title="Order confirmation page seen" src="' . FUPI_URL . '/admin/settings/img/fp-ico.svg" style="width: 24px; height: 24px; opacity: .8; display: block; margin: 0 auto;">';
        return $defaults;
    }

    // fill the columns with values
    public function fupi_add_values_to_woo_admin_columns( $column_name, $post_id ) {
        if ( $column_name == 'fupi_order_data' ) {
            $order_confirmation_page_seen = get_post_meta( $post_id, 'fupi_thankyou_viewed', false );
            echo ( !empty( $order_confirmation_page_seen ) ? '<img src="' . FUPI_URL . '/admin/settings/img/success_ico.png" style="width: 20px; height: 20px" title="' . esc_html__( 'Order confirmation page seen', 'full-picture-analytics-cookie-notice' ) . '">' : '<img src="' . FUPI_URL . '/admin/settings/img/almost_ico.png" style="width: 20px; height: 20px" title="' . esc_html__( 'Order confirmation page not seen', 'full-picture-analytics-cookie-notice' ) . '">' );
        }
    }

}
