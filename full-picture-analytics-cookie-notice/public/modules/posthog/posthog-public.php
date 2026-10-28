<?php

class Fupi_POSTHOG_public {

    private $settings;

    public function __construct(){

        $this->settings = get_option('fupi_posthog');

        if ( ! empty ( $this->settings['api_key'] ) ) {
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['posthog'] = [
            'reqs'      => [
                'consents'  => ['stats'],
                // no_cookie_field_id => 'no_cookies'
            ],
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/posthog/fupi-posthog.js' ),
                    'id'            => 'posthog',
                ],
            ]
        ];

        $fp['posthog'] = $this->settings;
        $fp['tools'][] = 'posthog';
     
        return $fp;
    }
}