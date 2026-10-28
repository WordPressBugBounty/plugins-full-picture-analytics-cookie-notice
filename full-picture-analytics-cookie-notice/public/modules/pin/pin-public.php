<?php

class Fupi_PIN_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_pin');
        
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

        $fp['runtime']['pin'] = [
            'reqs'      => [
                'consents'  => ['stats', 'market'],
                // no_cookie_field_id => 'no_cookies'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/pin/fupi-pin.js' ),
                    'id'            => 'pin',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['pin']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/pin/fupi-pin-woo.js' ),
                'id'             => 'pin-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['pin'] = $this->settings;
        $fp['tools'][] = 'pin';
        return $fp;
    }
}