<?php

class Fupi_FBP1_public {
    private $settings;

    private $tools;

    private $main;

    private $ver;

    public function __construct() {
        $this->settings = get_option( 'fupi_fbp1' );
        if ( !empty( $this->settings ) && !empty( $this->settings['pixel_id'] ) ) {
            $this->tools = get_option( 'fupi_tools' );
            $this->main = get_option( 'fupi_main' );
            $this->ver = get_option( 'fupi_versions' );
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters() {
        add_filter(
            'fupi_modify_fp_object',
            array($this, 'add_data_to_fp_object'),
            10,
            1
        );
    }

    public function add_data_to_fp_object( $fp ) {
        $fp['runtime']['fbp'] = [
            'reqs'  => [
                'consents' => ['stats', 'market'],
            ],
            'files' => [[
                'url' => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/fbp1/fupi-fbp.js' ),
                'id'  => 'fbp-setup',
            ], [
                'url' => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/fbp1/fupi-fbp-footer.js' ),
                'id'  => 'fbp-footer',
            ]],
        ];
        if ( !empty( $this->tools['woo'] ) ) {
            $fp['runtime']['fbp']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/fbp1/fupi-fbp-woo.js' ),
                'id'             => 'fbp-woo',
                'must_be_truthy' => ['fp.woo'],
            ];
        }
        // server data
        $fbp_data = $this->settings;
        // ! we must make a copy to safely modify
        $fbp_data['server_side'] = false;
        $fbp_data['server_side_2'] = false;
        if ( !empty( $fbp_data['capi_token'] ) ) {
            unset($fbp_data['capi_token']);
        }
        if ( !empty( $fbp_data['capi_token_2'] ) ) {
            unset($fbp_data['capi_token_2']);
        }
        unset($fbp_data['test_code'], $fbp_data['test_code_2']);
        $fp['fbp'] = $fbp_data;
        $fp['tools'][] = 'fbp';
        return $fp;
    }

    private function validate_test_codes( $test_codes ) {
        $validated = [];
        foreach ( (array) $test_codes as $pixel => $test_code ) {
            $pixel = (int) $pixel;
            if ( !in_array( $pixel, [1, 2], true ) || !is_string( $test_code ) ) {
                continue;
            }
            $test_code = trim( $test_code );
            if ( preg_match( '/^[A-Za-z0-9_-]{1,64}$/', $test_code ) ) {
                $validated[$pixel] = $test_code;
            }
        }
        return $validated;
    }

}
