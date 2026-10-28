<?php

class Fupi_ATRIG_public {

    private $settings;
    private $tools;
    private $main;

    public function __construct(){

        $this->settings = get_option('fupi_atrig');
        
        if ( ! empty( $this->settings ) ) { // check for ! empty( $this->settings['triggers'] ) added in 8.3.0
            $this->tools = get_option('fupi_tools');
            $this->main = get_option('fupi_main');
            $this->add_actions_and_filters();
        }
    }

    private function add_actions_and_filters(){
        add_filter( 'fupi_modify_fp_object', array($this, 'add_data_to_fp_object'), 10, 1 );
    }

    public function add_data_to_fp_object( $fp ){

        // change arrays into objects with trigger ID as the key and remove trigger name
		foreach ( ['triggers', 'click_triggers', 'aff_click_triggers', 'view_triggers', 'form_triggers'] as $key ) {

            if ( empty ( $this->settings[$key] ) ) continue;
                
			$triggers_a = $this->settings[$key];

            if ( ! empty( $triggers_a ) && is_array( $triggers_a ) ) {

                $new_trigger_o = [];
                
                foreach ( $triggers_a as $trigger_data ) {
                    
                    $trigger_id = $trigger_data['id'];
                    
                    // remove names and IDs
                    unset ( $trigger_data['id'] ); 
                    unset ( $trigger_data['name'] );
                    
                    $new_trigger_o[ $trigger_id ] = $trigger_data;
                };

                $this->settings[$key] = $new_trigger_o;
            }
		}
        
        // add to FP object
		$fp['atrig'] = $this->settings;

        // add "triggered" array

        return $fp;
    }
}