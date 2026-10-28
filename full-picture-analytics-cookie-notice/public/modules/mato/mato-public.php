<?php

class Fupi_MATO_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_mato');
        
        if ( ! empty ( $this->settings['id'] ) && ! empty ( $this->settings['url'] ) ) {
            $this->tools = get_option('fupi_tools');
            $this->main = get_option('fupi_main');
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
        add_action( 'wp_head', array($this, 'add_inline_script_to_head'), 10 );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['mato'] = [
            'reqs'      => [
                'consents'  => ['stats'],
                'no_cookie_field_id' => 'no_cookies'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/mato/fupi-mato.js' ),
                    'id'            => 'mato',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/mato/fupi-mato-footer.js' ),
                    'id'            => 'mato-footer',
                ],
            ]
        ];

        // add trailing slash if is missing
        $this->settings['url'] = trailingslashit( $this->settings['url'] );

        $fp['mato'] = $this->settings;

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['mato']['store_currency'] = get_option( 'woocommerce_currency', false );
            $fp['runtime']['mato']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/mato/fupi-mato-woo.js' ),
                'id'             => 'mato-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['tools'][] = 'mato';
        return $fp;
    }

    public function add_inline_script_to_head(){
        echo '<script>window._paq = window._paq || [];
            window._paq.push([\'setTrackerUrl\', fp.mato.url + \'matomo.php\']);
            window._paq.push([\'setSiteId\', fp.mato.id]);
            window._paq.push([\'enableLinkTracking\']);
        </script>';
    }
}
