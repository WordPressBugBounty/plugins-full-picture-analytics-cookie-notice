<?php

class Fupi_HOTJ_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_hotj');
        
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

        $fp['runtime']['hotj'] = [
            'reqs'      => [
                'consents'  => ['stats'],
                // no_cookie_field_id => 'no_cookies'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/hotj/fupi-hotj.js' ),
                    'id'            => 'hotj',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/hotj/fupi-hotj-footer.js' ),
                    'id'            => 'hotj-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['hotj']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/hotj/fupi-hotj-woo.js' ),
                'id'             => 'hotj-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['hotj'] = $this->settings;
        $fp['tools'][] = 'hotj';
        return $fp;
    }
}