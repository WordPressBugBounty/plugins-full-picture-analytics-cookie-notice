<?php

class Fupi_PLA_public {

    private $settings;
    private $tools;

    public function __construct(){

        $this->settings = get_option('fupi_pla');

        if ( ! empty ( $this->settings ) ) {
            
            $install = empty ( $this->settings['pla_use'] ) || ( $this->settings['pla_use'] === 'install' && ! empty ( $this->settings['domain'] ) && ! empty ( $this->settings['custom_domain'] ) );
            $extend = ! empty ( $this->settings['pla_use'] ) && $this->settings['pla_use'] === 'extend';

            if ( $install || $extend ) {
                $this->tools = get_option('fupi_tools');
                $this->add_actions_and_filters();
            }
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function add_data_to_fp_object( $fp ){

        $fp['runtime']['pla'] = [
            'reqs' => [], // will still be checked for force load, location and whether to tack current user"
            'files' => [
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/pla/fupi-pla.js' ),
                    'id'            => 'pla',
                ],
                [
                    'url'           => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/pla/fupi-pla-footer.js' ),
                    'id'            => 'pla-footer',
                ],
            ]
        ];

        if ( ! empty ( $this->tools['woo'] ) ) {
            $fp['runtime']['pla']['files'][] = [
                'url'            => add_query_arg( 'ver', FUPI_VERSION, FUPI_URL . 'public/modules/pla/fupi-pla-woo.js' ),
                'id'             => 'pla-woo',
                'must_be_truthy' => [ 'fp.woo' ]
            ];  
        };

        $fp['pla'] = ! empty( $this->settings ) ? $this->settings : array(); // Plausible does not require any setup to run
        $fp['tools'][] = 'pla';
        return $fp;
    }
}