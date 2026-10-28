<?php

class Fupi_GOTM_public {

    private $settings;
    private $tools;

    public function __construct(){

        $this->settings = get_option('fupi_gtm');
        
        if ( ! empty ( $this->settings['id'] ) ) {
            $this->tools = get_option('fupi_tools');
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
        add_action( 'wp_body_open', array( $this, 'add_gtm_noscript_fallback') );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['gtm'] = [
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gotm/fupi-gotm.js' ),
                    'id'            => 'gtm',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gotm/fupi-gotm-footer.js' ),
                    'id'            => 'gtm-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['gtm']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/gotm/fupi-gotm-woo.js' ),
                'id'             => 'gtm-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['gtm'] = $this->settings;
        $fp['tools'][] = 'gtm';
        return $fp;
    }

    public function add_gtm_noscript_fallback(){
        if ( ! empty( $this->settings ) && ! empty( $this->settings['id'] ) ) {
            echo '<!-- START Google Tag Manager (noscript) -->
                <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $this->settings['id'] ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- END Google Tag Manager (noscript) -->';
        }
    }
}