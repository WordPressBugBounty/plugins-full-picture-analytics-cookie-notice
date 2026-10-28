<?php

class Fupi_ATRIG_admin {

    private $settings;
    private $main;

    public function __construct(){
        
        $this->settings = get_option( 'fupi_atrig' );
        $this->main = get_option('fupi_main');

        $this->add_actions_and_filters();
    }

    private function add_actions_and_filters(){
        add_action( 'fupi_register_setting_atrig', array( $this, 'register_module_settings' ) );
        add_filter( 'fupi_atrig_add_fields_settings', array( $this, 'add_fields_settings' ), 10, 1 );
        add_filter( 'fupi_atrig_get_page_descr', array( $this, 'get_page_descr' ), 10, 2 );
    }

    public function add_fields_settings( $sections ){
        include_once 'atrig-fields.php';
        return $sections;
    }

    public function register_module_settings(){
        register_setting( 'fupi_atrig', 'fupi_atrig', array( 'sanitize_callback' => array( $this, 'sanitize_fields' ) ) );
    }

    public function sanitize_fields( $input ){
        include 'atrig-sanitize.php';
        
        if ( apply_filters( 'fupi_updating_many_options', false ) ) return $clean_data;

		require_once FUPI_PATH . '/admin/common/fupi-clear-cache.php';
		fupi_clear_cache();
		return $clean_data; 
    }

    public function get_page_descr( $section_id, $no_woo_descr_text ){
        return ;
    }
}
