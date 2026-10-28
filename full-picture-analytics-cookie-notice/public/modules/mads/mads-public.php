<?php

class Fupi_MADS_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_mads');
        
        if ( ! empty ( $this->settings['id'] ) ) {
            $this->tools = get_option('fupi_tools');
            $this->main = get_option('fupi_main');
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['mads'] = [
            'reqs'      => [
                'consents'  => ['stats', 'market'],
                // no_cookie_field_id => 'no_cookies'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/mads/fupi-mads.js' ),
                    'id'            => 'mads',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/mads/fupi-mads-footer.js' ),
                    'id'            => 'mads-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['mads']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/mads/fupi-mads-woo.js' ),
                'id'             => 'mads-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['mads'] = $this->settings;
        $fp['tools'][] = 'mads';
        return $fp;
    }
}