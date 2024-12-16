<?php

class Fupi_CSCR_public {

    private $settings;

    public function __construct(){

        $this->settings = get_option('fupi_cscr');

        if ( ! empty ( $this->settings ) ) {
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_action( 'wp_head', array( $this, 'fupi_output_in_head' ), 50 );
		add_action( 'wp_footer', array( $this, 'fupi_output_in_footer' ) );
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function fupi_output_in_head () {
        if ( ! is_customize_preview() ) include_once dirname(__FILE__) . '/custom-head-scripts.php';
	}

    public function fupi_output_in_footer () { 
        if ( ! is_customize_preview() ) include_once dirname(__FILE__) . '/custom-footer-scripts.php';
	}

    public function add_data_to_fp_object( $fp ){
        $fp['cscr'] = $this->settings;
        $fp['tools'][] = 'cscr';

        if ( ! empty( $this->settings['fupi_head_scripts'] ) ) {
			foreach ( $this->settings['fupi_head_scripts'] as $head_scr_data ) {
				$fp['tools'][] = $head_scr_data['id'];
			}
		};

		if ( ! empty( $this->settings['fupi_footer_scripts'] ) ) {
			foreach ( $this->settings['fupi_footer_scripts'] as $footer_scr_data ) {
				$fp['tools'][] = $footer_scr_data['id'];
			}
		};
        
        return $fp;
    }
}