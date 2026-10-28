<?php

$clean_data = array();

if ( ! empty( $input ) ) foreach( $input as $key => $value ) {

    $clean_key = sanitize_key( $key );

    switch ($clean_key) {
        case 'click_triggers':
        case 'aff_click_triggers':
        case 'view_triggers':
        case 'form_triggers':

            if ( ! is_array( $value ) ) break;

            $empty_val = [];
            $clean_val = [];
            $i = 0;

            foreach( $value as $trigger ){

                // CHECK REQUIRED FIELDS

                if ( empty( $trigger['name'] ) || empty( $trigger['sel'] ) || empty( $trigger['id'] ) ) continue;

                // NAME

                $clean_val[$i]['name'] = sanitize_text_field( $trigger['name'] );

                // CSS Selector

                $clean_val[$i]['sel'] = sanitize_text_field( $trigger['sel'] );

                // ID

                $clean_val[$i]['id'] = sanitize_key( $trigger['id'] );

                $i++;
            };
        break;
        
        case 'triggers':

            if ( ! is_array( $value ) ) break;

            $empty_val = [];
            $clean_val = [];
            $i = 0;

            foreach( $value as $trigger ){

                // CHECK REQUIRED FIELDS

                if ( empty( $trigger['name'] ) || empty( $trigger['events'] ) || empty( $trigger['id'] ) ) continue;

                // NAME

                $clean_val[$i]['name'] = sanitize_text_field( $trigger['name'] );

                // EVENTS

                foreach ( $trigger['events'] as $action ) {
                    $clean_val[$i]['events'][] = sanitize_key( $action );
                }

                // CONDITIONS
                
                $clean_val[$i]['cond'] = [];
                $c_i = 0;

                foreach ( is_array( $trigger['cond'] ?? null ) ? $trigger['cond'] : [] as $cond ) {

                    // save only full conditions
                    if ( ! empty( $cond['dpath'] ) && ! empty( $cond['comp'] ) ) {
                        $clean_val[$i]['cond'][$c_i]['dpath'] = preg_replace( '/[^a-zA-Z0-9\. >_]/i', '', $cond['dpath'] );
                        $clean_val[$i]['cond'][$c_i]['comp'] = sanitize_key( $cond['comp'] );
                        $clean_val[$i]['cond'][$c_i]['val'] = sanitize_text_field( $cond['val'] );
                    }

                    $c_i++;
                }

                // DEBUG

                if ( isset ( $trigger['debug'] ) ) $clean_val[$i]['debug'] = 1;

                // ID

                $clean_val[$i]['id'] = sanitize_key( $trigger['id'] );

                $i++;
            };
        break;
        default:
            $clean_val = is_bool ( $value ) || is_string ( $value ) ? strip_tags( stripslashes( $value ) ) : false;
        break;
    }

    if ( ! empty( $clean_val ) && ! empty( $clean_key ) ) {
        $clean_data[$clean_key] = $clean_val;
    } else {
        // if the user deleted data, then overwrite old value with an empty value (we cannot delete it here)
        $clean_data[$clean_key] = $empty_val;
    }
}
?>
