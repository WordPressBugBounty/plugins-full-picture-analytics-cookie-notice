<?php

// Get vars

global $wpdb;
$this->get_fupi_options( ['fupi_main', 'fupi_geo', 'fupi_tools', 'fupi_cook'] );
$this->validate_fields( $this->o['fupi_geo'], [ 'geo', 'remember_geo' ] );

// Move settings of the Geo module to fupi_main

if ( ! empty( $this->o['fupi_tools']['geo'] ) ) {
    if ( ! empty( $this->o['fupi_geo']['geo'] ) ) {
        $this->o['fupi_main']['geo'] = $this->o['fupi_geo']['geo'];
        $this->o['fupi_main']['remember_geo'] = ! empty ( $this->o['fupi_geo']['remember_geo'] ) ? $this->o['fupi_geo']['remember_geo'] : 30;
    } else {
        $this->o['fupi_main']['geo'] = 'cf_non_user';
    }

    $this->regenerate_cdb = true;
}

// Save the ID of the main privacy policy page in the fupi_cook[pp_id]

$options_table = is_multisite() ? $wpdb->get_blog_prefix() . 'options' : $wpdb->options;

$wpdb->last_error = '';
$pp_id = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT option_value FROM {$options_table} WHERE option_name = %s",
        'wp_page_for_privacy_policy'
    )
);

if ( ! empty( $wpdb->last_error ) ) {
    throw new RuntimeException( 'Privacy policy lookup failed.' );
}

if ( ! empty ( $pp_id ) && is_numeric( $pp_id ) ) $this->o['fupi_cook']['pp_id'] = (int) $pp_id;
