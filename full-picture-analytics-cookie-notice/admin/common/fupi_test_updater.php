<?php

class  Fupi_Test_Updater {

    /*
    
    REMEMBER

    To enable a module on the tools list, always set the value to boolean "true", not a digit "1" - it will not work.

    $this->o['fupi_tools']['cook'] = true;
    
    */

    private $prev_version = false;
    private $regenerate_cdb = false;
    private $o = []; // holds all the options
    private $opts_to_remove = [];
    
    public function run(){

        $this->set_default_options();
        $this->check_fupi_versions();
        
        $options_changed = true;

        include_once 'updater_rules/updater_tests.php';
        
        if ( $options_changed ) {

            add_filter( 'fupi_updating_many_options', '__return_true' ); // this is used to stop "sanitize" functions from other modules from generating files, sending data to CDB or clearing cache

            $this->update_wp_options();

            add_filter( 'fupi_updating_many_options', '__return_false' );

            // after every update
            $this->clear_cache();
        }
    }

    private function set_default_options(){
        $tools_o = get_option( 'fupi_tools' );
        if ( empty( $tools_o ) ) add_option( 'fupi_tools', [] );
    }

    // SAFELY MANIPULATE OPTIONS

    private function get_fupi_options( $opt_names_arr ){
        foreach ( $opt_names_arr as $opt_name ) {

            if ( ! is_string( $opt_name ) ) continue;

            $opt_name = sanitize_key( $opt_name );
            
            if ( ! isset( $this->o[$opt_name] ) ) {
                $this->o[$opt_name] = get_option( $opt_name );
                if ( $this->o[$opt_name] === false ) $this->o[$opt_name] = [];
            }
        }
    }

    private function get_options_from_backup( $backup_data ){
        foreach ( $backup_data as $key => $value ) {
            
            if ( $value == 'no_value' ) {
                $this->opts_to_remove[] = $key; // we use it later, to remove the option from the DB
                $this->o[$key] = []; // we save empty arr to prevent errors while processing options
            } else {
                $this->o[$key] = $value;
            }
        }
    }
    
    private function add_fupi_option( $opt_name, $opt_value ){
        $this->o[$opt_name] = $opt_value;
    }

    private function update_wp_options(){
        foreach ( $this->o as $opt_name => $opt_val ) {
            update_option( $opt_name, $opt_val );
        }
    }

    private function check_fupi_versions(){
        
        $this->get_fupi_options( [ 'fupi_versions', 'fupi_tools' ] );

        // Recognize ver 9.0
        // it had a bug which saved fupi_versions to false
        if ( isset( $this->o['fupi_versions'] ) && $this->o['fupi_versions'] === false ){
            $this->o['fupi_versions'][0] = time();
            $this->o['fupi_versions'][1] = '9.0.0';
        }

        // for all other versions

        // is fresh install if the fupi_versions option is an empty array
        if ( count ( $this->o['fupi_versions'] ) === 0 ) {

            // set current time and ver
            $this->o['fupi_versions'][0] = time();
            $this->o['fupi_versions'][1] = FUPI_VERSION;
            $this->prev_version = FUPI_VERSION;

        // is old install if array is not empty
        } else {

            $this->prev_version = $this->o['fupi_versions'][1];
            $this->o['fupi_versions'][1] = FUPI_VERSION;
        }

        // Save changes - must do it here, not later
        update_option( 'fupi_versions', $this->o['fupi_versions'] );
    }

    // CLEAR CACHE
    private function clear_cache(){
        require_once FUPI_PATH . '/admin/common/fupi-clear-cache.php';
        fupi_clear_cache();
    }
}

?>
