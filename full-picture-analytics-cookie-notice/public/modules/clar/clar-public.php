<?php

class Fupi_CLAR_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_clar');
        
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

        $fp['runtime']['clar'] = [
            'reqs'      => [
                'consents'  => ['stats'],
                'no_cookie_field_id' => 'no_cookie'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/clar/fupi-clar.js' ),
                    'id'            => 'clar',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/clar/fupi-clar-footer.js' ),
                    'id'            => 'clar-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['clar']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/clar/fupi-clar-woo.js' ),
                'id'             => 'clar-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['clar'] = $this->settings;
        $fp['tools'][] = 'clar';
        return $fp;
    }
}