<?php

class Fupi_GTOOLS_public {
    private $tools;

    private $main;

    private $ga41_enabled;

    private $ga42_enabled;

    private $gads_enabled;

    private $ga41_settings;

    private $ga42_settings;

    private $gads_settings;

    private $gtag_settings;

    private $ga41_id;

    private $gads_id;

    public function __construct() {
        $this->tools = get_option( 'fupi_tools' );
        $this->main = get_option( 'fupi_main' );
        $this->ga41_enabled = isset( $this->tools['ga41'] );
        $this->ga42_enabled = isset( $this->tools['ga42'] );
        $this->gads_enabled = isset( $this->tools['gads'] );
        if ( $this->ga41_enabled ) {
            $this->ga41_settings = get_option( 'fupi_ga41' );
        }
        if ( $this->ga42_enabled ) {
            $this->ga42_settings = get_option( 'fupi_ga42' );
        }
        if ( $this->gads_enabled ) {
            $this->gads_settings = get_option( 'fupi_gads' );
        }
        $this->ga41_id = ( $this->ga41_enabled && !empty( $this->ga41_settings['id'] ) ? esc_attr( $this->ga41_settings['id'] ) : false );
        $this->gads_id = ( $this->gads_enabled && !empty( $this->gads_settings['id'] ) ? esc_attr( $this->gads_settings['id'] ) : false );
        if ( empty( $this->ga41_id ) && empty( $this->gads_id ) ) {
            return;
        }
        if ( $this->ga41_enabled && !empty( $this->ga41_id ) || $this->gads_enabled && !empty( $this->gads_id ) ) {
            $this->gtag_settings = get_option( 'fupi_gtag' );
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters() {
        add_action( 'wp_head', array($this, 'add_gtag_to_head'), 1 );
        add_filter(
            'fupi_modify_fp_object',
            array($this, 'add_data_to_fp_object'),
            10,
            1
        );
        if ( $this->ga41_enabled && !empty( $this->ga41_id ) ) {
            add_filter(
                'fupi_order_server_tracking',
                array($this, 'fupi_ga41_order_server_tracking__premium_only'),
                10,
                2
            );
        }
    }

    public function add_gtag_to_head() {
        $gtag_added = false;
        if ( empty( $this->gtag_settings['custom_gateway'] ) ) {
            // LOAD CLASSIC GTAG SCRIPT
            $script_id = false;
            if ( !empty( $this->ga41_id ) ) {
                $script_id = $this->ga41_id;
            } else {
                // Fix missing AW-
                if ( !empty( $this->gads_id ) && !str_contains( $this->gads_id, 'AW-' ) && !str_contains( $this->gads_id, 'GT-' ) && !str_contains( $this->gads_id, 'G-' ) ) {
                    $script_id = 'AW-' . $this->gads_id;
                } else {
                    $script_id = $this->gads_id;
                }
            }
            if ( !empty( $script_id ) ) {
                // ! Datalayer is already created in JS helpers
                echo '<script id="fupi_gtag_script" async src="https://www.googletagmanager.com/gtag/js?id=' . $script_id . '"></script>';
                $gtag_added = true;
            }
        } else {
        }
        if ( $gtag_added ) {
            // make sure we have gtag
            echo '<script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
            </script>';
        }
    }

    public function add_data_to_fp_object( $fp ) {
        // GTAG Config
        $fp['runtime']['gtg'] = [
            'files' => [[
                'url' => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gtools/fupi-gtg.js' ),
                'id'  => 'gtg-config',
            ]],
        ];
        // GA only
        if ( $this->ga41_enabled && !empty( $this->ga41_id ) ) {
            $fp['runtime']['ga41'] = [
                'reqs'  => [
                    'consents' => ['stats'],
                ],
                'files' => [[
                    'url' => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gtools/fupi-ga-footer.js' ),
                    'id'  => 'ga-footer',
                ]],
            ];
        }
        // GAds only
        if ( $this->gads_enabled && !empty( $this->gads_id ) ) {
            $fp['runtime']['gads'] = [
                'reqs'  => [
                    'consents' => ['stats', 'market'],
                ],
                'files' => [[
                    'url' => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gtools/fupi-gads-footer.js' ),
                    'id'  => 'gads-footer',
                ]],
            ];
        }
        // Woo - shared
        if ( !empty( $this->tools['woo'] ) ) {
            $tool_id = ( $this->ga41_enabled && !empty( $this->ga41_id ) ? 'ga41' : 'gads' );
            // Loaded for either GA or GAds but not both
            $fp['runtime'][$tool_id]['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gtools/fupi-gtg-woo.js' ),
                'id'             => 'gtg-woo',
                'must_be_truthy' => ['fp.woo'],
            ];
        }
        // GTAG
        if ( !empty( $this->gtag_settings ) ) {
            $fp['gtag'] = $this->gtag_settings;
        } else {
            $fp['gtag'] = [];
        }
        // GA4
        if ( !empty( $this->ga41_settings ) ) {
            $fp['ga41'] = $this->ga41_settings;
            $fp['tools'][] = 'ga41';
            $fp['ga41']['server_side'] = false;
            if ( !empty( $fp['ga41']['mp_secret_key'] ) ) {
                unset($fp['ga41']['mp_secret_key']);
            }
        }
        // GADS
        if ( $this->gads_enabled && !empty( $this->gads_settings ) ) {
            $fp['gads'] = $this->gads_settings;
            $fp['tools'][] = 'gads';
        }
        return $fp;
    }

}
