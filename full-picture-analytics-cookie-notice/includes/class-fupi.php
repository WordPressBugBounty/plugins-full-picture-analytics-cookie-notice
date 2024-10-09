<?php

class Fupi {
    protected $loader;

    protected $tools;

    protected $main;

    protected $cook;

    public $fupi_versions;

    protected $woo;

    // public function get_loader() { return $this->loader; }
    protected $plugin_name;

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    protected $version;

    public function get_version() {
        return $this->version;
    }

    public function __construct() {
        if ( defined( 'FUPI_VERSION' ) ) {
            $this->version = FUPI_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'full_picture';
        $this->tools = get_option( 'fupi_tools' );
        $this->cook = get_option( 'fupi_cook' );
        $this->main = get_option( 'fupi_main' );
        $this->fupi_versions = get_option( 'fupi_versions' );
        $this->woo = get_option( 'fupi_woo' );
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        // STANDARD FUPI
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fupi-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fupi-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fupi-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fupi-public.php';
        // WooCommerce
        if ( isset( $this->tools['woo'] ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fupi-public-woo.php';
        }
        $this->loader = new Fupi_Loader();
    }

    private function set_locale() {
        // if ( fupi_fs()->is__premium_only() ) {
        $plugin_i18n = new Fupi_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'fupi_load_plugin_textdomain' );
        $this->loader->add_filter(
            'load_textdomain_mofile',
            $plugin_i18n,
            'fupi_load_textdomain_mofile',
            10,
            2
        );
        // }
    }

    private function define_admin_hooks() {
        $plugin_admin = new Fupi_Admin($this->get_plugin_name(), $this->get_version());
        // Perform updates
        $this->loader->add_action( 'init', $plugin_admin, 'perform_updates' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'fupi_enqueue_scripts' );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'fupi_custom_admin_styles' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'fupi_add_admin_page' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'fupi_add_stats_reports_pages' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'fupi_register_settings' );
        // $this->loader->add_action( 'admin_init', $plugin_admin, 'fupi_activation_redirect' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'fupi_admin_notices' );
        // CUSTOMIZER
        // UseEnable customizer functions if we are NOT using OceanWP theme
        $theme = wp_get_theme();
        if ( $theme->get( 'Name' ) != 'OceanWP' ) {
            $this->loader->add_action( 'customize_register', $plugin_admin, 'fupi_customize_register' );
            $this->loader->add_action( 'customize_save_after', $plugin_admin, 'fupi_customize_save_after' );
            $this->loader->add_action( 'customize_preview_init', $plugin_admin, 'fupi_customizer_preview_scripts' );
            $this->loader->add_action( 'customize_controls_enqueue_scripts', $plugin_admin, 'fupi_enqueue_customizer_css_js' );
        }
        // AJAX USER SEARCH (for the settings field)
        $this->loader->add_action( 'wp_ajax_fupi_search_users', $plugin_admin, 'fupi_search_users_callback' );
        // IMPORT/EXPORT SETTINGS
        $this->loader->add_action( 'wp_ajax_fupi_ajax_download_settings_backup', $plugin_admin, 'fupi_ajax_download_settings_backup' );
        $this->loader->add_action( 'admin_footer', $plugin_admin, 'fupi_print_scripts' );
        $this->loader->add_action( 'wp_ajax_fupi_process_uploaded_settings', $plugin_admin, 'fupi_process_uploaded_settings' );
    }

    private function define_public_hooks() {
        $class_loaded = false;
        if ( isset( $this->tools['woo'] ) ) {
            $plugin_public = new Fupi_Woo($this->get_plugin_name(), $this->get_version());
            $class_loaded = true;
        }
        if ( !$class_loaded ) {
            $plugin_public = new Fupi_Public($this->get_plugin_name(), $this->get_version());
        }
        // CHECK IF WOO IS ENABLED
        // This fires very early. See https://rachievee.com/the-wordpress-hooks-firing-sequence/
        $this->loader->add_action( 'plugins_loaded', $plugin_public, 'fupi_is_woo_enabled' );
        // MODIFIES HTML TO REMOVE 3RD PARTY SCRIPTS
        $this->loader->add_action(
            'template_redirect',
            $plugin_public,
            'fupi_maybe_buffer_output',
            2
        );
        // https://stackoverflow.com/a/71548452
        $this->loader->add_filter( 'fupi_buffer_output', $plugin_public, 'fupi_parse_output' );
        // add "page labels" taxonomy
        if ( !empty( $this->tools ) && isset( $this->tools['labelpages'] ) ) {
            $this->loader->add_action( 'init', $plugin_public, 'fupi_page_labels' );
        }
        $this->loader->add_action( 'init', $plugin_public, 'fupi_add_shortcodes' );
        $this->loader->add_action(
            'wp_head',
            $plugin_public,
            'fupi_get_data',
            -1
        );
        // includes all the "Get" scripts
        $this->loader->add_action(
            'wp_head',
            $plugin_public,
            'fupi_output_in_head',
            50
        );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'fupi_enqueue_public_scripts' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'fupi_output_in_footer' );
        // output notice's HTML
        if ( !empty( $this->tools ) && isset( $this->tools['cook'] ) ) {
            // output after body open
            if ( function_exists( 'wp_body_opens' ) ) {
                $this->loader->add_action( 'wp_body_opens', $plugin_public, 'fupi_output_notice' );
            } else {
                // output before body close
                $this->loader->add_action( 'wp_footer', $plugin_public, 'fupi_output_notice' );
            }
        }
        // Load scripts asynchronously
        if ( !empty( $this->main ) && isset( $this->main['async_scripts'] ) ) {
            $this->loader->add_filter(
                'script_loader_tag',
                $plugin_public,
                'fupi_async_scripts',
                10,
                3
            );
        }
        // WooCommerce
        if ( isset( $this->tools['woo'] ) ) {
            // FIX FOR "GOOGLE FOR WOOCOMMERCE" PLUGIN
            // prevents it from overwriting consents set by WP FP
            add_filter( 'woocommerce_gla_gtag_consent', '__return_empty_string' );
            // Add JS
            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'fupi_enqueue_public_woo_scripts' );
            // Register brand taxonomy
            if ( isset( $this->woo['add_brand_tax'] ) ) {
                $this->loader->add_action( 'init', $plugin_public, 'fupi_woo_brand_tax' );
            }
            //
            // CLASSIC WOO ONLY
            //
            // teasers in product archives
            // teasers in "Related products" and "You may also like" sections on a single product page
            // >>>> EXCEPTION <<< teasers in "You may also like" section on a single product page when FSE is enabled ("related products on the same page use blocks, which have totally different HTML)
            $this->loader->add_action(
                'woocommerce_before_shop_loop_item',
                $plugin_public,
                'fupi_woo_archive_teaser_data',
                50
            );
            // ok
            // teasers in widgets
            $this->loader->add_action(
                'woocommerce_widget_product_item_end',
                $plugin_public,
                'fupi_woo_widget_teaser_data',
                9999
            );
            // ok
            // mini cart
            $this->loader->add_action(
                'woocommerce_after_mini_cart',
                $plugin_public,
                'fupi_classic_mini_cart_data',
                10,
                3
            );
            // cart
            $this->loader->add_action(
                'woocommerce_before_cart_contents',
                $plugin_public,
                'fupi_cart_data',
                10,
                3
            );
            // cart & mini cart
            $this->loader->add_filter(
                'woocommerce_cart_item_name',
                $plugin_public,
                'fupi_classic_cart_item_id',
                10,
                3
            );
            //
            // BLOCKS ONLY
            //
            // teasers in woocommerce/handpicked-products
            // teasers in woocommerce/product-best-sellers
            // teasers in woocommerce/product-new << this one is also used in the cart block and causes problems!
            // teasers in woocommerce/product-on-sale
            // teasers in woocommerce/product-top-rated
            $this->loader->add_filter(
                'woocommerce_blocks_product_grid_item_html',
                $plugin_public,
                'fupi_woo_block_teasers',
                999999,
                3
            );
            // ok
            // teasers in Full Site Edit product archives
            // teasers in Full Site Edit "related products" section on a single product page
            // block woocommerce/mini-cart
            // block woocommerce/cart (except the cross-sells !)
            $this->loader->add_filter(
                'render_block',
                $plugin_public,
                'fupi_woo_block_render_block_mod',
                50,
                2
            );
            //
            // CLASSIC & BLOCKS
            //
            // single product
            $this->loader->add_action(
                'woocommerce_after_add_to_cart_button',
                $plugin_public,
                'fupi_woo_prod_data',
                50
            );
            $this->loader->add_filter(
                'woocommerce_grouped_product_list_column_label',
                $plugin_public,
                'fupi_woo_extra_group_prod_data',
                50,
                2
            );
            // checkout
            $this->loader->add_action(
                'woocommerce_before_checkout_form',
                $plugin_public,
                'fupi_checkout_cart_data',
                50,
                2
            );
            // classic
            $this->loader->add_action(
                'woocommerce_blocks_enqueue_checkout_block_scripts_after',
                $plugin_public,
                'fupi_checkout_cart_data',
                50,
                2
            );
            // block
            // Any page - for adding products to cart with a URL parameter add-to-cart
            $this->loader->add_action(
                'wp_footer',
                $plugin_public,
                'fupi_woo_add_to_cart_from_url',
                999
            );
            // order confirmation page
            $this->loader->add_action(
                'wp_head',
                $plugin_public,
                'fupi_woo_get_order_data',
                5
            );
            // TO DO:
            // CROSS-SELL in cart. No hooks available right now (6.3.0)
            // FEATURED PRODUCT BLOCK. No hooks available right now (6.3.0)
        }
    }

    public function run() {
        $this->loader->run();
    }

}
