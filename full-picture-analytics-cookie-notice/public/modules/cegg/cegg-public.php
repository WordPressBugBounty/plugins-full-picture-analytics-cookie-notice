<?php

class Fupi_CEGG_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_cegg');
        
        if ( ! empty ( $this->settings ) && ! empty ( $this->settings['script_src'] ) ) {
            $this->tools = get_option('fupi_tools');
            $this->main = get_option('fupi_main');
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['cegg'] = [
            'reqs'      => [
                'consents'  => ['stats', 'pers'],
                // no_cookie_field_id => 'no_cookies'
            ],
            'files' => [
                [
                    'path'           => 'fp.cegg.script_src',
                    'id'            => 'cegg-script',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/cegg/fupi-cegg.js' ),
                    'id'            => 'cegg-setup',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/cegg/fupi-cegg-footer.js' ),
                    'id'            => 'cegg-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['cegg']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/cegg/fupi-cegg-woo.js' ),
                'id'             => 'cegg-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['cegg'] = $this->settings;
        $fp['tools'][] = 'cegg';
        return $fp;
    }
}