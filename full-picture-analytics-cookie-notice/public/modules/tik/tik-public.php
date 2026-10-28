<?php

class Fupi_TIK_public {

    private $settings;
    private $tools;

    public function __construct(){

        $this->settings = get_option('fupi_tik');
        
        if ( ! empty ( $this->settings['id'] ) ) {
            $this->tools = get_option('fupi_tools');
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['tik'] = [
            'reqs'      => [
                'consents'  => ['stats', 'market'],
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/tik/fupi-tik.js' ),
                    'id'            => 'tik',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/tik/fupi-tik-footer.js' ),
                    'id'            => 'tik-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['tik']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/tik/fupi-tik-woo.js' ),
                'id'             => 'tik-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['tik'] = $this->settings;
        $fp['tools'][] = 'tik';
        return $fp;
    }
}