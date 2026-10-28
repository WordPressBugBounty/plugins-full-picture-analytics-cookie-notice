<?php

$opt_names = ['fupi_ga41','fupi_ga42', 'fupi_gtm'];

$this->get_fupi_options( $opt_names );

foreach ( $opt_names as $o_name ) {
    if ( array_key_exists( 'custom_events', $this->o[$o_name] ) ) {
        $this->validate_rows( $this->o[$o_name]['custom_events'], [], [ 'evt_val' ] );
    }
    
    // Save the value of "value" field as a parameter in the sub r3

    if ( ! empty( $this->o[$o_name]['custom_events'] ) && is_array( $this->o[$o_name]['custom_events'] ) ) {

        foreach ( $this->o[$o_name]['custom_events'] as $i => $event ) {
            
            if ( isset( $event['evt_val'] ) ) {

                if ( ! empty( $event['evt_val'] ) ) {
                    $this->o[$o_name]['custom_events'][$i]['params'] = [
                        [
                            'name' => 'fp_event_value',
                            'type' => 'number',
                            'val'  => (float) $event['evt_val'],
                        ]
                    ];
                }

                unset( $this->o[$o_name]['custom_events'][$i]['evt_val'] );
            }
        }
    }
}
