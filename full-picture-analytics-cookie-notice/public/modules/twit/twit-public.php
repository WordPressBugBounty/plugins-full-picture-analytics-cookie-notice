<?php

class Fupi_TWIT_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_twit');
        
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

        $fp['runtime']['twit'] = [
            'reqs'      => [
                'consents'  => ['stats','market'],
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/twit/fupi-twit.js' ),
                    'id'            => 'twit',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['twit']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/twit/fupi-twit-woo.js' ),
                'id'             => 'twit-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['twit'] = $this->settings;
        $fp['tools'][] = 'twit';
        return $fp;
    }
}