<?php

class Fupi_LINKD_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_linkd');
        
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

        $fp['runtime']['linkd'] = [
            'reqs'      => [
                'consents'  => ['stats', 'market'],
                // no_cookie_field_id => 'no_cookies'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/linkd/fupi-linkd.js' ),
                    'id'            => 'linkd',
                ],
            ]
        ];

        $fp['linkd'] = $this->settings;
        $fp['tools'][] = 'linkd';
        return $fp;
    }
}