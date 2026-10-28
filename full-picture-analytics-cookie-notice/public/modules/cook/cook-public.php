<?php

class Fupi_COOK_public {
    private $settings;

    private $main;

    private $pp_url = false;

    private $pp_update_date = false;

    public function __construct() {
        $this->settings = get_option( 'fupi_cook' );
        if ( $this->settings === false ) {
            $this->settings = [];
        }
        $this->main = get_option( 'fupi_main' );
        $this->add_actions_and_filters();
    }

    private function add_actions_and_filters() {
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_filter(
            'fupi_modify_fp_object',
            array($this, 'add_data_to_fp_object'),
            10,
            1
        );
        add_action(
            'wp_head',
            array($this, 'fupi_add_cook_data_to_head'),
            10,
            1
        );
        if ( !empty( $this->settings['dynamic_blocking'] ) ) {
            add_filter(
                'fupi_add_js_to_head_data',
                array($this, 'get_dynamic_element_blocking_js'),
                1,
                1
            );
        }
        // output after body open
        if ( function_exists( 'wp_body_opens' ) ) {
            add_action( 'wp_body_opens', array($this, 'output_notice_html') );
        } else {
            // output before body close
            add_action( 'wp_footer', array($this, 'output_notice_html') );
        }
        // HTML MODS
        add_action( 'init', array($this, 'register_iframes_shortcodes') );
    }

    public function output_notice_html() {
        include_once FUPI_PATH . '/public/modules/cook/fupi-display-cookie-notice.php';
    }

    public function fupi_add_cook_data_to_head() {
        // !! DO NOT DELETE
        // we get options again because otherwise the translation would not kick in
        $cook_opts_2 = get_option( 'fupi_cook' );
        $iframe_opts = get_option( 'fupi_ifrblock' );
        if ( !is_array( $iframe_opts ) ) {
            $iframe_opts = array();
        }
        // Add consent control for Pixel Manager
        if ( !empty( $cook_opts_2['scrblk_auto_rules'] ) && in_array( 'pixelman', $cook_opts_2['scrblk_auto_rules'] ) ) {
            echo '<script id="fupi_pixelman_compat">
                window._pmwq = window._pmwq || [];
                window._pmwq.push(function() {
                    if ( fpdata.cookies && !! window?.pmw?.consent?.api?.updateSelectively ) {
                        pmw.consent.api.updateSelectively({
                            statistics: fpdata.cookies.stats,
                            marketing: fpdata.cookies.marketing,
                            preferences: fpdata.cookies.personalisation,
                            necessary: true,
                        });
                        console.log("[FP] Updated tracking consents for Pixel Manager");
                    }
                });
            </script>';
        }
        // Add translations to iframe texts
        $iframe_caption_txt = ( empty( $iframe_opts['caption_txt'] ) ? esc_attr__( 'This content is hosted by [[an external source]]. By loading it, you accept its {{privacy terms}}.', 'full-picture-analytics-cookie-notice' ) : esc_attr( $iframe_opts['caption_txt'] ) );
        $iframe_btn_text = ( empty( $iframe_opts['btn_text'] ) ? esc_attr__( 'Load content', 'full-picture-analytics-cookie-notice' ) : esc_attr( $iframe_opts['btn_text'] ) );
        $iframe_texts = [
            'iframe_caption_txt' => $iframe_caption_txt,
            'iframe_btn_text'    => $iframe_btn_text,
        ];
        echo '<script id="fupi_iframe_texts">let fupi_iframe_texts = ' . wp_json_encode( $iframe_texts ) . ';</script>';
    }

    private function get_pp_data() {
        if ( !empty( $this->settings['pp_id'] ) ) {
            $pp_id = (int) $this->settings['pp_id'];
            $pp_post = get_post( $pp_id );
            if ( !empty( $pp_post ) && isset( $pp_post->post_status ) && $pp_post->post_status === 'publish' ) {
                $this->pp_update_date = get_post_modified_time(
                    'U',
                    false,
                    $pp_id,
                    false
                );
                $this->pp_url = get_permalink( $pp_post );
            }
        }
        return null;
    }

    public function add_data_to_fp_object( $fp ) {
        // ADD FILES TO RUNTIME
        $fp['runtime']['notice'] = [
            'files' => [[
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/cook/js/fupi-consb.js' ),
                'id'             => 'cons-banner',
                'cant_be_truthy' => ['fp.main.is_customizer'],
            ], [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/cook/js/fupi-customizer-consb.js' ),
                'id'             => 'cons-banner-customizer',
                'must_be_truthy' => ['fp.main.is_customizer'],
            ], [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/cook/js/fupi-iframes.js' ),
                'id'             => 'iframes-blocker',
                'cant_be_truthy' => ['fp.main.is_bricks_builder'],
            ]],
        ];
        // GET DATA FROM THE CUSTOMIZER
        $notice_data = get_option( 'fupi_cookie_notice' );
        // from customizer
        $this->get_pp_data();
        // check if the banner should be hidden on the current page
        $hide_on_this_page = is_privacy_policy();
        if ( !$hide_on_this_page ) {
            if ( isset( $this->settings['hide_on_pages'] ) && is_array( $this->settings['hide_on_pages'] ) ) {
                $current_id = get_the_ID();
                if ( !empty( $current_id ) && in_array( $current_id, $this->settings['hide_on_pages'] ) ) {
                    $hide_on_this_page = true;
                }
            }
        }
        // BUILD BASIC OBJECT
        $mod_settings = $this->settings;
        // MODIFY VALUES
        $mod_settings['display_notice'] = !$hide_on_this_page;
        $mod_settings['toggle_selector'] = ( !empty( $mod_settings['toggle_selector'] ) ? esc_attr( $mod_settings['toggle_selector'] ) . ' .fupi_show_cookie_notice, .fp_show_cookie_notice' : '.fupi_show_cookie_notice, .fp_show_cookie_notice' );
        // REMOVE VALUES
        unset($mod_settings['enable_scripts_after']);
        // set later
        unset($mod_settings['mode']);
        // set later
        unset($mod_settings['optin']);
        unset($mod_settings['optin_countries']);
        unset($mod_settings['optout']);
        unset($mod_settings['optout_countries']);
        unset($mod_settings['inform']);
        unset($mod_settings['inform_countries']);
        unset($mod_settings['hide_on_pages']);
        unset($mod_settings['customize_notice_btn']);
        unset($mod_settings['storage_location']);
        unset($mod_settings['cdb_key']);
        unset($mod_settings['consent_access']);
        unset($mod_settings['local_backup_email']);
        unset($mod_settings['email_frequency']);
        unset($mod_settings['save_all_consents']);
        unset($mod_settings['iframe_auto_rules']);
        unset($mod_settings['iframe_manual_rules']);
        unset($mod_settings['control_other_iframes']);
        unset($mod_settings['control_other_tools']);
        unset($mod_settings['scrblk_manual_rules']);
        unset($mod_settings['iframe_img']);
        unset($mod_settings['iframe_caption_txt']);
        unset($mod_settings['iframe_btn_text']);
        unset($mod_settings['customize_iframe_btn']);
        // ADD VALUES
        $iframe_image_id = absint( get_theme_mod( 'fupi_ifrblock_image', 0 ) );
        $iframe_image_url = ( $iframe_image_id ? wp_get_attachment_image_url( $iframe_image_id, 'full' ) : false );
        $iframe_ratio_x = get_theme_mod( 'fupi_ifrblock_ratio_x', 16 );
        $iframe_ratio_y = get_theme_mod( 'fupi_ifrblock_ratio_y', 9 );
        $iframe_ratio_x = ( is_numeric( $iframe_ratio_x ) ? (float) $iframe_ratio_x : 0 );
        $iframe_ratio_y = ( is_numeric( $iframe_ratio_y ) ? (float) $iframe_ratio_y : 0 );
        if ( !is_finite( $iframe_ratio_x ) || $iframe_ratio_x < 1 || $iframe_ratio_x > 100 ) {
            $iframe_ratio_x = 16;
        }
        if ( !is_finite( $iframe_ratio_y ) || $iframe_ratio_y < 1 || $iframe_ratio_y > 100 ) {
            $iframe_ratio_y = 9;
        }
        $iframe_default_aspect = round( (float) $iframe_ratio_y / (float) $iframe_ratio_x * 10000 ) / 100 . '%';
        $new_settings = [
            'enabled'                  => true,
            'display_notice'           => !$hide_on_this_page,
            'priv_policy_update'       => $this->pp_update_date,
            'blur_page'                => !empty( $notice_data ) && !empty( $notice_data['blur_page'] ),
            'scroll_lock'              => !empty( $notice_data ) && !empty( $notice_data['scroll_lock'] ),
            'hidden'                   => ( isset( $notice_data['hide'] ) ? $notice_data['hide'] : [] ),
            'shown'                    => ( isset( $notice_data['show'] ) ? $notice_data['show'] : [] ),
            'preselected_switches'     => ( isset( $notice_data['switches_on'] ) ? $notice_data['switches_on'] : [] ),
            'optin_switches'           => !empty( $notice_data['optin_switches'] ),
            'privacy_url'              => $this->pp_url,
            'iframe_placeholder_image' => ( $iframe_image_url ? esc_url( $iframe_image_url ) : false ),
            'iframe_default_aspect'    => $iframe_default_aspect,
        ];
        $notice_settings = array_merge( $new_settings, $mod_settings );
        // ADD TO FP OBJECT
        $fp['notice'] = $notice_settings;
        // DYNAMIC SCRIPT BLOCKING RULES FOR JS - for use with the preemptive blocking
        if ( !empty( $this->settings['dynamic_blocking'] ) ) {
            if ( !function_exists( 'fupi_get_script_blocking_rules' ) ) {
                require_once FUPI_PATH . '/public/modules/cook/blockscr_rules.php';
            }
            $scrblk_js_rules = fupi_get_script_blocking_rules( $this->settings, true );
            if ( !empty( $scrblk_js_rules ) ) {
                $fp['scrblk'] = $scrblk_js_rules;
            }
        }
        /*
                $fp['notice'] = [
        
                    // NEW
                    'enabled' 						=> true,
                    'display_notice'				=> ! $hide_on_this_page,
                    'priv_policy_update'			=> $privacy_policy_update_date,
        
                    // NEW - FROM CUSTOMIZER
                    'blur_page'						=> ! empty( $notice_data ) && ! empty( $notice_data['blur_page'] ),
                    'scroll_lock' 					=> ! empty( $notice_data ) && ! empty( $notice_data['scroll_lock'] ),
                    'hidden'						=> isset( $notice_data['hide'] ) ? $notice_data['hide'] : [],
                    'shown'							=> isset( $notice_data['show'] ) ? $notice_data['show'] : [],
                    'preselected_switches' 			=> isset( $notice_data['switches_on'] ) ? $notice_data['switches_on'] : [],
                    'optin_switches'				=> ! empty( $notice_data['optin_switches'] ),
        
                    // FROM SETTINGS
                    'gtag_no_cookie_mode'			=> isset( $this->settings['gtag_no_cookie_mode'] ),
                    'consent_access'                => isset( $this->settings['consent_access'] ),
                    'url_passthrough'				=> isset( $this->settings['url_passthrough'] ),
                    'ask_for_consent_again'			=> isset( $this->settings['ask_for_consent_again'] ),
                    'save_all_consents'				=> isset( $this->settings['save_all_consents'] ),
        
                    // FROM SETTINGS - MODIFIED
                    'toggle_selector'				=> ! empty( $this->settings['toggle_selector'] ) ? esc_attr( $this->settings['toggle_selector'] ) . ' .fupi_show_cookie_notice, .fp_show_cookie_notice' : '.fupi_show_cookie_notice, .fp_show_cookie_notice',
                ];*/
        // UPDATE OBJECT
        $is_premium = false;
        if ( !$is_premium ) {
            $fp['notice']['mode'] = ( !empty( $this->settings['enable_scripts_after'] ) ? esc_attr( $this->settings['enable_scripts_after'] ) : 'optin' );
        }
        // default
        if ( !is_customize_preview() ) {
            $send_to_email = !empty( $this->settings['storage_location'] ) && $this->settings['storage_location'] == 'email';
            $send_to_cdb = !empty( $this->settings['storage_location'] ) && $this->settings['storage_location'] == 'cdb' && !empty( $this->settings['cdb_key'] );
            $proofrec_settings = array(
                'save_consent' => $send_to_email || $send_to_cdb,
            );
            if ( $send_to_email || $send_to_cdb ) {
                $proofrec_settings['storage_location'] = ( $send_to_email ? 'email' : 'cdb' );
                $proofrec_settings['consent_access'] = !empty( $this->settings['consent_access'] );
                $proofrec_settings['save_all_consents'] = !empty( $this->settings['save_all_consents'] );
            }
            $fp['proofrec'] = $proofrec_settings;
        }
        return $fp;
    }

    public function enqueue_scripts() {
        /* ^ */
        wp_enqueue_style(
            'fupi-consb',
            FUPI_URL . 'public/modules/cook/css/fupi-consb.min.css',
            array(),
            FUPI_VERSION,
            'all'
        );
        // !! also contains some CSS for the iframe manager
    }

    // SHORTCODE FOR BLOCKING IFRAMES
    public function register_iframes_shortcodes() {
        add_shortcode( 'fp_block', array($this, 'fupi_block') );
        add_shortcode( 'fp_block_iframe', array($this, 'fupi_block') );
    }

    public function fupi_block( $atts, $content = null ) {
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
            $output = str_replace( '/iframe>', '/div></div>', $new_content );
            return $output;
        }
        return $content;
        // this returns only iframes - shortcodes are always invisible ( it saves user time removing them if the iframe blocking module was disabled )
    }

    // DYNAMIC BLOCKING JS
    public function get_dynamic_element_blocking_js( $js_str ) {
        if ( !function_exists( 'fupi_get_script_blocking_rules' ) ) {
            require_once FUPI_PATH . '/public/modules/cook/blockscr_rules.php';
        }
        if ( empty( fupi_get_script_blocking_rules( $this->settings, true ) ) ) {
            return $js_str;
        }
        $dynamic_blocking_js = (include FUPI_PATH . '/public/modules/cook/dynamic-element-blocking-js.php');
        return $js_str . $dynamic_blocking_js;
    }

}

// class END