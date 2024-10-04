<?php

class Fupi_Public {
    public $plugin_name;

    public $version;

    // option data
    public $main;

    public $tools;

    public $cook;

    public $woo;

    public $trackmeta;

    public $track404;

    public $geo;

    public $blockscr;

    // check if enabled
    public $cookie_notice_enabled;

    public $ga41_enabled;

    public $ga42_enabled;

    public $gads_enabled;

    public $mads_enabled;

    public $mato_enabled;

    public $fbp1_enabled;

    public $fbp2_enabled;

    public $gtm_enabled;

    public $twit_enabled;

    public $posthog_enabled;

    public $pin_enabled;

    public $clar_enabled;

    public $linkd_enabled;

    public $tik_enabled;

    public $hotj_enabled;

    public $pla_enabled;

    public $simpl_enabled;

    public $inspectlet_enabled;

    public $crazyegg_enabled;

    public $cscr_enabled;

    public $geo_enabled;

    public $blockscr_enabled;

    public $woo_enabled;

    // other
    public $is_debug;

    public $gtag_id;

    public $ga41_opts;

    public $user_is_logged_in;

    public $track_current_user;

    public $is_customizer;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        // tools & main opts
        $this->main = get_option( 'fupi_main' );
        $this->tools = get_option( 'fupi_tools' );
        if ( empty( $this->tools ) ) {
            return;
        }
        // Consent Banner opts
        $this->cook = get_option( 'fupi_cook' );
        // other opts
        $this->woo = get_option( 'fupi_woo' );
        $this->trackmeta = get_option( 'fupi_trackmeta' );
        $this->track404 = get_option( 'fupi_track404' );
        $this->geo = get_option( 'fupi_geo' );
        $this->blockscr = get_option( 'fupi_blockscr' );
        // Consent Banner
        $this->cookie_notice_enabled = isset( $this->tools['cookie_notice'] ) || isset( $this->tools['cook'] );
        // Integrations
        $this->ga41_enabled = isset( $this->tools['ga41'] );
        $this->ga42_enabled = isset( $this->tools['ga42'] );
        $this->gads_enabled = isset( $this->tools['gads'] );
        $this->mads_enabled = isset( $this->tools['mads'] );
        $this->mato_enabled = isset( $this->tools['mato'] );
        $this->fbp1_enabled = isset( $this->tools['fbp1'] );
        $this->fbp2_enabled = isset( $this->tools['fbp2'] );
        $this->gtm_enabled = isset( $this->tools['gtm'] );
        $this->twit_enabled = isset( $this->tools['twit'] );
        $this->posthog_enabled = isset( $this->tools['posthog'] );
        $this->pin_enabled = isset( $this->tools['pin'] );
        $this->clar_enabled = isset( $this->tools['clar'] );
        $this->linkd_enabled = isset( $this->tools['linkd'] );
        $this->tik_enabled = isset( $this->tools['tik'] );
        $this->hotj_enabled = isset( $this->tools['hotj'] );
        $this->pla_enabled = isset( $this->tools['pla'] );
        $this->simpl_enabled = isset( $this->tools['simpl'] );
        $this->inspectlet_enabled = isset( $this->tools['insp'] );
        $this->crazyegg_enabled = isset( $this->tools['cegg'] );
        $this->cscr_enabled = isset( $this->tools['cscr'] );
        $this->geo_enabled = isset( $this->tools['geo'] );
        $this->blockscr_enabled = isset( $this->tools['blockscr'] );
        // Settings
        $this->is_debug = isset( $this->main['debug'] );
        // Other
        $this->gtag_id = false;
        if ( $this->ga41_enabled ) {
            $ga41_opts = get_option( 'fupi_ga41' );
            $this->gtag_id = ( isset( $ga41_opts['id'] ) ? esc_attr( $ga41_opts['id'] ) : false );
        }
    }

    // CHECK ASAP IF WOO HAS LOADED
    public function fupi_is_woo_enabled() {
        // action: plugin_loaded
        $this->woo_enabled = isset( $this->tools['woo'] ) && function_exists( 'WC' ) && version_compare( WC()->version, '3.7', '>=' );
    }

    public function fupi_get_data() {
        if ( empty( $this->tools ) ) {
            return;
        }
        // GET THE DATA
        global $wp;
        global $post;
        $this->is_customizer = is_customize_preview();
        $this->user_is_logged_in = is_user_logged_in();
        $fp = [
            'loaded'          => [],
            'loading'         => [],
            'blocked_scripts' => [],
            'waitlist'        => [],
            'actions'         => [],
            'observers'       => [],
        ];
        if ( $this->woo_enabled ) {
            $fpdata = [
                'woo' => [
                    'products' => [],
                    'lists'    => [],
                    'cart'     => [],
                    'order'    => [],
                ],
            ];
        } else {
            $fpdata = [];
        }
        $cookie_notice = [];
        include_once dirname( __FILE__ ) . '/modules/in_head/data-main.php';
        include_once dirname( __FILE__ ) . '/modules/in_head/data-wp.php';
        if ( $this->woo_enabled ) {
            include_once dirname( __FILE__ ) . '/modules/in_head/data-woo.php';
        }
        include_once dirname( __FILE__ ) . '/modules/in_head/data-tools.php';
        $fp = apply_filters( 'fupi_modify_fp_object', $fp );
        $fpdata = apply_filters( 'fupi_modify_fpdata_object', $fpdata );
        // OUTPUT THE DATA
        $output = '<!--noptimize--><script id=\'fp_data_js\' type="text/javascript" data-no-optimize="1">
			
			var fp_premium = ' . json_encode( fupi_fs()->can_use_premium_code() ) . ',
				FP = { \'fns\' : {} },
				fp = ' . json_encode( $fp ) . ',
				fpdata = ' . json_encode( $fpdata ) . ';';
        // fp_nonce = "' . wp_create_nonce('wp_rest'). '";'; // It has to be "wp_rest" This is required!
        include_once dirname( __FILE__ ) . '/modules/in_head/head-js.php';
        $output .= '</script><!--/noptimize-->';
        echo $output;
    }

    public function fupi_enqueue_public_scripts() {
        if ( empty( $this->tools ) ) {
            return;
        }
        // JS HELPERS
        $footer_helpers_req = ( isset( $this->main['no_jquery'] ) ? array('fupi-helpers-js') : array('fupi-helpers-js', 'jquery') );
        /* ^ */
        wp_enqueue_script(
            'fupi-helpers-js',
            plugin_dir_url( __FILE__ ) . 'helpers/fupi-helpers.js',
            array(),
            $this->version,
            false
        );
        // can delete fp_cookies when ?tracking=off
        /* _ */
        wp_enqueue_script(
            'fupi-helpers-footer-js',
            plugin_dir_url( __FILE__ ) . 'helpers/fupi-helpers-footer.js',
            $footer_helpers_req,
            $this->version,
            true
        );
        // IFRAME JS
        if ( isset( $this->tools['iframeblock'] ) ) {
            // Load iframe.js only when we are not in the bricks builder editor
            if ( !(function_exists( 'bricks_is_builder' ) && bricks_is_builder()) ) {
                /* ^ */
                wp_enqueue_script(
                    'fupi-iframes-js',
                    plugin_dir_url( __FILE__ ) . 'helpers/fupi-iframes.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
            }
        }
        // Consent Banner
        if ( $this->cookie_notice_enabled ) {
            /* ^ */
            wp_enqueue_style(
                'fupi-consb',
                plugin_dir_url( __FILE__ ) . 'cookie-notice/css/fupi-consb.min.css',
                array(),
                $this->version,
                'all'
            );
            // also contains (little) CSS for the iframe manager
            if ( $this->is_customizer ) {
                /* _ */
                wp_enqueue_script(
                    'fupi-customizer-consb-js',
                    plugin_dir_url( __FILE__ ) . 'cookie-notice/js/fupi-customizer-consb.js',
                    array('fupi-helpers-js', 'jquery'),
                    $this->version,
                    true
                );
            } else {
                /*if ( $this->track_current_user )*/
                /* _ */
                wp_enqueue_script(
                    'fupi-consb-js',
                    plugin_dir_url( __FILE__ ) . 'cookie-notice/js/fupi-consb.js',
                    array('fupi-helpers-js', 'fupi-helpers-footer-js'),
                    $this->version,
                    true
                );
            }
        }
        // Don't load any tracking tools in the customizer
        if ( $this->is_customizer ) {
            return;
        }
        // GTM
        if ( $this->gtm_enabled ) {
            $gtm_opts = get_option( 'fupi_gtm' );
            $footer_req_gth = ( $this->woo_enabled ? array(
                'fupi-helpers-js',
                'fupi-helpers-footer-js',
                'fupi-gtm2-head-js',
                'fupi-woo-js'
            ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-gtm2-head-js') );
            // files below need to have typos! Litespeed cache will NOT combine files with "gtm" in their names
            /* ^ */
            wp_enqueue_script(
                'fupi-gtm2-head-js',
                plugin_dir_url( __FILE__ ) . 'modules/js/fupi-gotm2.js',
                array('fupi-helpers-js'),
                $this->version,
                false
            );
            /* _ */
            wp_enqueue_script(
                'fupi-gtm2-footer-js',
                plugin_dir_url( __FILE__ ) . 'modules/js/fupi-gotm2-footer.js',
                $footer_req_gth,
                $this->version,
                true
            );
        }
        // GTAG / GOOGLE ANALYTICS / GOOGLE ADS
        if ( $this->ga41_enabled || $this->gads_enabled ) {
            $reqs = ( $this->woo_enabled ? array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-woo-js') : array('fupi-helpers-js', 'fupi-helpers-footer-js') );
            // GOOGLE ANALYTICS 4
            if ( $this->ga41_enabled ) {
                $ga41_opts = get_option( 'fupi_ga41' );
                $footer_req_ga4 = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-ga4-head-js',
                    'fupi-woo-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-ga4-head-js') );
                if ( $this->track_current_user || isset( $ga41_opts['force_load'] ) ) {
                    /* ^ */
                    wp_enqueue_script(
                        'fupi-ga4-head-js',
                        plugin_dir_url( __FILE__ ) . 'modules/js/fupi-ga4.js',
                        array('fupi-helpers-js'),
                        $this->version,
                        false
                    );
                    /* _ */
                    wp_enqueue_script(
                        'fupi-ga4-footer-js',
                        plugin_dir_url( __FILE__ ) . 'modules/js/fupi-ga4-footer.js',
                        $footer_req_ga4,
                        $this->version,
                        true
                    );
                    array_push( $reqs, 'fupi-ga4-head-js' );
                }
            }
            // GOOGLE ADS
            if ( $this->gads_enabled ) {
                $gads_opts = get_option( 'fupi_gads' );
                $footer_req_gads = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-gads-head-js',
                    'fupi-woo-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-gads-head-js') );
                if ( $this->track_current_user || !empty( $gads_opts['force_load'] ) ) {
                    /* ^ */
                    wp_enqueue_script(
                        'fupi-gads-head-js',
                        plugin_dir_url( __FILE__ ) . 'modules/js/fupi-gads.js',
                        array('fupi-helpers-js'),
                        $this->version,
                        false
                    );
                    /* _ */
                    wp_enqueue_script(
                        'fupi-gads-footer-js',
                        plugin_dir_url( __FILE__ ) . 'modules/js/fupi-gads-footer.js',
                        $footer_req_gads,
                        $this->version,
                        true
                    );
                    array_push( $reqs, 'fupi-gads-head-js' );
                }
            }
            // GTAG
            /* ^ */
            wp_enqueue_script(
                'fupi-gtg-head-js',
                plugin_dir_url( __FILE__ ) . 'modules/js/fupi-gtg.js',
                $reqs,
                $this->version,
                false
            );
            // the gtg must be spelled like this! Litespeed cache will NOT combine files with "gtag" in its name
        }
        // FB PIXEL
        if ( $this->fbp1_enabled ) {
            $fbp1_opts = get_option( 'fupi_fbp1' );
            if ( $this->track_current_user || isset( $fbp1_opts['force_load'] ) ) {
                $fbp_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-fbp-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-fbp-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-fbp-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-fbp.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-fbp-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-fbp-footer.js',
                    $fbp_reqs,
                    $this->version,
                    true
                );
            }
        }
        // TWITTER
        if ( $this->twit_enabled ) {
            $twit_opts = get_option( 'fupi_twit' );
            if ( $this->track_current_user || isset( $twit_opts['force_load'] ) ) {
                $twit_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-twit-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-twit-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-twit-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-twit.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-twit-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-twit-footer.js',
                    $twit_reqs,
                    $this->version,
                    true
                );
            }
        }
        // MS ADS
        if ( $this->mads_enabled ) {
            $mads_opts = get_option( 'fupi_mads' );
            if ( $this->track_current_user || isset( $mads_opts['force_load'] ) ) {
                $mads_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-mads-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-mads-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-mads-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-mads.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-mads-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-mads-footer.js',
                    $mads_reqs,
                    $this->version,
                    true
                );
            }
        }
        // PINTEREST
        if ( $this->pin_enabled ) {
            $pin_opts = get_option( 'fupi_pin' );
            if ( $this->track_current_user || isset( $pin_opts['force_load'] ) ) {
                $pin_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-pin-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-pin-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-pin-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-pin.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                if ( $this->woo_enabled ) {
                    /* _ */
                    wp_enqueue_script(
                        'fupi-pin-footer-js',
                        plugin_dir_url( __FILE__ ) . 'modules/js/fupi-pin-footer.js',
                        $pin_reqs,
                        $this->version,
                        true
                    );
                }
            }
        }
        // LINKEDIN
        if ( $this->linkd_enabled ) {
            $linkd_opts = get_option( 'fupi_linkd' );
            if ( $this->track_current_user || isset( $linkd_opts['force_load'] ) ) {
                $linkd_reqs = ( $this->woo_enabled ? array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-woo-js') : array('fupi-helpers-js', 'fupi-helpers-footer-js') );
                /* _ */
                wp_enqueue_script(
                    'fupi-linkd-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-linkd-footer.js',
                    $linkd_reqs,
                    $this->version,
                    true
                );
            }
        }
        // TIKTOK
        if ( $this->tik_enabled ) {
            $tik_opts = get_option( 'fupi_tik' );
            if ( $this->track_current_user || isset( $tik_opts['force_load'] ) ) {
                $tiktok_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-tik-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-tik-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-tik-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-tik.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-tik-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-tik-footer.js',
                    $tiktok_reqs,
                    $this->version,
                    true
                );
            }
        }
        // CLARITY
        if ( $this->clar_enabled ) {
            $clar_opts = get_option( 'fupi_clar' );
            if ( $this->track_current_user || isset( $clar_opts['force_load'] ) ) {
                $clar_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-clar-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-clar-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-clar-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-clar.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-clar-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-clar-footer.js',
                    $clar_reqs,
                    $this->version,
                    true
                );
            }
        }
        // HOTJAR
        if ( $this->hotj_enabled ) {
            $hotj_opts = get_option( 'fupi_hotj' );
            if ( $this->track_current_user || isset( $hotj_opts['force_load'] ) ) {
                $hotj_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-hotj-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-hotj-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-hotj-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-hotj.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-hotj-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-hotj-footer.js',
                    $hotj_reqs,
                    $this->version,
                    true
                );
            }
        }
        // INSPECTLET
        if ( $this->inspectlet_enabled ) {
            $insp_opts = get_option( 'fupi_insp' );
            if ( $this->track_current_user || isset( $insp_opts['force_load'] ) ) {
                $insp_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-insp-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-insp-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-insp-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-insp.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-insp-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-insp-footer.js',
                    $insp_reqs,
                    $this->version,
                    true
                );
            }
        }
        // CRAZYEGG
        if ( $this->crazyegg_enabled ) {
            $cegg_opts = get_option( 'fupi_cegg' );
            if ( $this->track_current_user || isset( $cegg_opts['force_load'] ) ) {
                $cegg_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-cegg-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-cegg-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-cegg-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-cegg.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-cegg-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-cegg-footer.js',
                    $cegg_reqs,
                    $this->version,
                    true
                );
            }
        }
        // PLAUSIBLE
        if ( $this->pla_enabled ) {
            $pla_opts = get_option( 'fupi_pla' );
            if ( $this->track_current_user || isset( $pla_opts['force_load'] ) ) {
                $pla_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-pla-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-pla-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-pla-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-pla.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-pla-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-pla-footer.js',
                    $pla_reqs,
                    $this->version,
                    true
                );
            }
        }
        // SIMPLE ANALYTICS
        if ( $this->simpl_enabled ) {
            $simpl_opts = get_option( 'fupi_simpl' );
            if ( $this->track_current_user || isset( $simpl_opts['force_load'] ) ) {
                $simpl_reqs = ( $this->woo_enabled ? array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-woo-js') : array('fupi-helpers-js', 'fupi-helpers-footer-js') );
                /* _ */
                wp_enqueue_script(
                    'fupi-simpl-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-simpl.js',
                    $simpl_reqs,
                    $this->version,
                    false
                );
            }
        }
        // POSTHOG
        if ( $this->posthog_enabled ) {
            $posthog_opts = get_option( 'fupi_posthog' );
            if ( $this->track_current_user || isset( $posthog_opts['force_load'] ) ) {
                $posthog_reqs = ( $this->woo_enabled ? array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-woo-js') : array('fupi-helpers-js', 'fupi-helpers-footer-js') );
                /* _ */
                wp_enqueue_script(
                    'fupi-posthog-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-posthog.js',
                    $posthog_reqs,
                    $this->version,
                    false
                );
            }
        }
        // MATOMO
        if ( $this->mato_enabled ) {
            $mato_opts = get_option( 'fupi_mato' );
            if ( $this->track_current_user || isset( $mato_opts['force_load'] ) ) {
                $mato_reqs = ( $this->woo_enabled ? array(
                    'fupi-helpers-js',
                    'fupi-helpers-footer-js',
                    'fupi-woo-js',
                    'fupi-mato-head-js'
                ) : array('fupi-helpers-js', 'fupi-helpers-footer-js', 'fupi-mato-head-js') );
                /* ^ */
                wp_enqueue_script(
                    'fupi-mato-head-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-mato.js',
                    array('fupi-helpers-js'),
                    $this->version,
                    false
                );
                /* _ */
                wp_enqueue_script(
                    'fupi-mato-footer-js',
                    plugin_dir_url( __FILE__ ) . 'modules/js/fupi-mato-footer.js',
                    $mato_reqs,
                    $this->version,
                    true
                );
            }
        }
    }

    public function fupi_async_scripts( $tag, $handle, $src ) {
        // The handles of the enqueued scripts we want to defer
        $defer_scripts = array(
            // to test later >>> fupi-atrig.js
            'fupi-gtg-head-js',
            'fupi-pla-head-js',
            'fupi-pla-footer-js',
            'fupi-posthog-head-js',
            'fupi-cegg-head-js',
            'fupi-cegg-footer-js',
            'fupi-insp-head-js',
            'fupi-insp-footer-js',
            'fupi-hotj-head-js',
            'fupi-hotj-footer-js',
            'fupi-clar-head-js',
            'fupi-clar-footer-js',
            'fupi-tik-head-js',
            'fupi-tik-footer-js',
            'fupi-linkd-footer-js',
            'fupi-pin-head-js',
            'fupi-twit-head-js',
            'fupi-twit-footer-js',
            'fupi-simpl-head-js',
            'fupi-mads-head-js',
            'fupi-mads-footer-js',
            'fupi-mato-head-js',
            'fupi-mato-footer-js',
            'fupi-fbp-head-js',
            'fupi-fbp-footer-js',
            'fupi-ga4-head-js',
            'fupi-ga4-footer-js',
            'fupi-ga-head-js',
            'fupi-ga-footer-js',
            'fupi-gads-head-js',
            'fupi-gads-footer-js',
            'fupi-gtm-head-js',
            'fupi-gtm-footer-js',
            'fupi-gtm2-head-js',
            'fupi-gtm2-footer-js',
            'fupi-helpers-footer-js',
            'fupi-customizer-consb-js',
        );
        if ( in_array( $handle, $defer_scripts ) ) {
            return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
        }
        return $tag;
    }

    //
    // OUTPUT CUSTOM SCRIPTS IN HEAD
    //
    public function fupi_output_in_head() {
        if ( empty( $this->tools ) ) {
            return;
        }
        // CUSTOM HEAD SCRIPTS
        if ( $this->cscr_enabled && !$this->is_customizer ) {
            include_once dirname( __FILE__ ) . '/modules/in_head/custom-head-scripts.php';
        }
    }

    //
    // OUTPUT CUSTOM SCRIPTS IN FOOTER AND POST META IN CONSOLE
    //
    public function fupi_output_in_footer() {
        if ( empty( $this->tools ) ) {
            return;
        }
        // CUSTOM FOOTER SCRIPTS
        if ( $this->cscr_enabled && !$this->is_customizer ) {
            include_once dirname( __FILE__ ) . '/modules/in_footer/custom-footer-scripts.php';
        }
        // NOSCRIPT TAGS
        if ( $this->track_current_user && !$this->is_customizer ) {
            include_once dirname( __FILE__ ) . '/modules/in_footer/noscript-fallbacks.php';
        }
    }

    //
    // OUTPUT Consent Banner HTML
    //
    public function fupi_output_notice() {
        include_once dirname( __FILE__ ) . '/cookie-notice/fupi-display-cookie-notice.php';
    }

    //
    // BLOCK 3rd-PARTY SCRIPTS
    // https://stackoverflow.com/a/71548452
    //
    public function fupi_return_buffer( $html ) {
        if ( !$html ) {
            return $html;
        }
        return apply_filters( 'fupi_buffer_output', $html );
    }

    public function fupi_maybe_buffer_output() {
        if ( $this->blockscr_enabled || isset( $this->tools['iframeblock'] ) || isset( $this->tools['safefonts'] ) ) {
            ob_start( array($this, 'fupi_return_buffer') );
        }
    }

    public function fupi_parse_output( $orig_html ) {
        $html = $orig_html;
        // block scripts
        if ( $this->blockscr_enabled ) {
            include_once dirname( __FILE__ ) . '/functions/blockscr_parser.php';
        }
        // block iframes
        if ( isset( $this->tools['iframeblock'] ) ) {
            // make sure we do not try to manage iframes while in bricks builder (it breaks)
            $can_load_iframe_parser = !(function_exists( 'bricks_is_builder' ) && bricks_is_builder());
            if ( $can_load_iframe_parser ) {
                include_once dirname( __FILE__ ) . '/functions/iframeblock_parser.php';
            }
        }
        // replace Google fonts
        if ( isset( $this->tools['safefonts'] ) ) {
            include_once dirname( __FILE__ ) . '/functions/safefonts_parser.php';
        }
        if ( !empty( $html ) ) {
            return $html;
        }
        return $orig_html;
    }

    //
    // SHORTCODES
    //
    public function fupi_add_shortcodes() {
        if ( empty( $this->tools ) ) {
            return;
        }
        add_shortcode( 'fp_info', array($this, 'fupi_info') );
        add_shortcode( 'fp_block', array($this, 'fupi_block') );
        add_shortcode( 'fp_block_iframe', array($this, 'fupi_block') );
    }

    public function fupi_block( $atts, $content = null ) {
        if ( $this->cookie_notice_enabled && isset( $this->tools['iframeblock'] ) ) {
            $a = shortcode_atts( array(
                'stats'   => '',
                'market'  => '',
                'pers'    => '',
                'name'    => '',
                'image'   => false,
                'privacy' => '',
            ), $atts );
            if ( empty( $content ) ) {
                return '';
            } else {
                // get the data
                $stats = ( !empty( $a['stats'] ) && $a['stats'] == '1' ? '1' : '0' );
                $market = ( !empty( $a['market'] ) && $a['market'] == '1' ? '1' : '0' );
                $pers = ( !empty( $a['pers'] ) && $a['pers'] == '1' ? '1' : '0' );
                $name = ( !empty( $a['name'] ) ? ' data-name="' . esc_attr( $a['name'] ) . '"' : '' );
                $placeholder = ( !empty( $a['image'] ) ? ' data-placeholder="' . esc_url( $a['image'] ) . '"' : '' );
                $privacy = ( !empty( $a['privacy'] ) ? ' data-privacy="' . esc_url( $a['privacy'] ) . '"' : '' );
                // replace iframe
                $new_content = str_replace( '<iframe', '<div class="fupi_blocked_iframe" data-stats="' . $stats . '" data-market="' . $market . '" data-pers="' . $pers . '" ' . $placeholder . $name . $privacy . '><div class="fupi_iframe_data"', $content );
                $output = str_replace( '/iframe>', '/div></div>', $new_content ) . '<!--noptimize--><script data-no-optimize="1">FP.manageIframes();</script><!--/noptimize-->';
                return $output;
            }
        }
        return $content;
        // this returns only iframes - shortcodes are always invisible ( it saves user time removing them if the iframe blocking module was disabled )
    }

    // GENERATE THE LIST OF ENABLED TOOLS
    public function fupi_info( $atts, $content = null ) {
        if ( !empty( $this->tools['privex'] ) ) {
            $a = shortcode_atts( array(
                'display' => 'list',
            ), $atts );
            include_once FUPI_PATH . '/includes/fupi_modules_data.php';
            include_once dirname( __FILE__ ) . '/functions/privacy_generator.php';
            $fupi_policy_generator = new Fupi_policy_generator($a['display']);
            return $fupi_policy_generator->output();
        }
        return '';
    }

    //
    // CUSTOM TAXONOMY
    //
    public function fupi_page_labels() {
        $labels = array(
            'name'                       => esc_html__( 'Labels', 'Taxonomy General Name', 'full-picture-analytics-cookie-notice' ),
            'singular_name'              => esc_html__( 'Label', 'Taxonomy Singular Name', 'full-picture-analytics-cookie-notice' ),
            'menu_name'                  => esc_html__( 'Labels', 'full-picture-analytics-cookie-notice' ),
            'all_items'                  => esc_html__( 'All labels', 'full-picture-analytics-cookie-notice' ),
            'parent_item'                => esc_html__( 'Parent label', 'full-picture-analytics-cookie-notice' ),
            'parent_item_colon'          => esc_html__( 'Parent label:', 'full-picture-analytics-cookie-notice' ),
            'new_item_name'              => esc_html__( 'New Label Name', 'full-picture-analytics-cookie-notice' ),
            'add_new_item'               => esc_html__( 'Add New Label', 'full-picture-analytics-cookie-notice' ),
            'edit_item'                  => esc_html__( 'Edit Label', 'full-picture-analytics-cookie-notice' ),
            'update_item'                => esc_html__( 'Update Label', 'full-picture-analytics-cookie-notice' ),
            'view_item'                  => esc_html__( 'View Label', 'full-picture-analytics-cookie-notice' ),
            'separate_items_with_commas' => esc_html__( 'Separate labels with commas', 'full-picture-analytics-cookie-notice' ),
            'add_or_remove_items'        => esc_html__( 'Add or remove labels', 'full-picture-analytics-cookie-notice' ),
            'choose_from_most_used'      => esc_html__( 'Choose from the most used', 'full-picture-analytics-cookie-notice' ),
            'popular_items'              => esc_html__( 'Popular labels', 'full-picture-analytics-cookie-notice' ),
            'search_items'               => esc_html__( 'Search labels', 'full-picture-analytics-cookie-notice' ),
            'not_found'                  => esc_html__( 'Not Found', 'full-picture-analytics-cookie-notice' ),
            'no_terms'                   => esc_html__( 'No labels', 'full-picture-analytics-cookie-notice' ),
            'items_list'                 => esc_html__( 'Labels list', 'full-picture-analytics-cookie-notice' ),
            'items_list_navigation'      => esc_html__( 'Labels list navigation', 'full-picture-analytics-cookie-notice' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud'     => false,
            'show_in_rest'      => true,
        );
        register_taxonomy( 'fupi_page_labels', array('page'), $args );
    }

}
