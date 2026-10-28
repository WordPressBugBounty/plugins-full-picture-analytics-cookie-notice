<?php

$clean_data = array();

if ( ! empty( $input ) ) foreach( $input as $key => $value ) {

	$clean_key = sanitize_key( $key );

	if( ! empty( $value ) ) {

		switch ($clean_key) {

			case 'track_clicks':
			case 'track_aff':
			case 'track_form_submits':
			case 'track_impress':
				
				$clean_val = [];
				
				if ( is_array( $value ) ){

					$j = 0;
					
					foreach( $value as $section ){
						if ( empty( $section['atrig_id'] ) || empty( $section['val'] ) ) continue;
						$clean_val[$j]['atrig_id'] = esc_attr( $section['atrig_id'] );
						$clean_val[$j]['val'] = trim( sanitize_text_field( $section['val'] ) );
						$j++;
					}
				};
				break;

			case 'custom_events':
			case 'vscores':

				$clean_val = [];

				if ( is_array( $value ) ) {
					foreach ( $value as $section ) {

						if ( 
							! is_array( $section ) || 
							! isset( $section['atrig_id'], $section['event_action'] ) || 
							! is_scalar( $section['atrig_id'] ) 
						) continue;

						$atrig_id 		= sanitize_key( $section['atrig_id'] );
						$event_action 	= trim( sanitize_text_field( (string) $section['event_action'] ) );

						if ( '' === $atrig_id || false === $event_action || '' === $event_action ) continue;

						$clean_section = array(
							'atrig_id'     => $atrig_id,
							'event_action' => $event_action,
						);

						// for custom events
						if ( 'custom_events' === $clean_key ) {

							$clean_section['repeat'] = empty( $section['repeat'] ) ? 'no' : sanitize_key( $section['repeat'] );
						
						// for engagement tracking
						// skip if has no matching leadscore
						} elseif ( ! preg_match( '/^fp_leadscore_[0-9]+$/', $atrig_id ) ) {
							continue;
						}

						foreach ( array( 'event_category', 'event_label' ) as $text_field ) {

							if ( ! isset( $section[ $text_field ] ) || '' === $section[ $text_field ] ) continue;

							$clean_text = trim( sanitize_text_field( $section[ $text_field ] ) );

							if ( $clean_text !== '' ) $clean_section[ $text_field ] = $clean_text;
						}

						// for custom events
						// the "if" cannot check "! empty" because it can also hold value 0
						if ( 
							'custom_events' === $clean_key && 
							isset ( $section['event_value'] ) && 
							$section['event_value'] !== ''
						) {

							$event_value = trim( (string) $section['event_value'] );
							if ( ! preg_match( '/^(?:[0-9]+(?:\.[0-9]{1,3})?|\.[0-9]{1,3})$/', $event_value ) ) continue;

							$event_value = (float) $event_value;
							if ( $event_value < 0 || $event_value > 9999999 ) continue;
							$clean_section['event_value'] = $event_value;
						}

						// engagement only
						if ( 'vscores' === $clean_key && ! empty( $section['send_score'] ) ) { 
							$clean_section['send_score'] = 1; 
						}

						$clean_val[] = $clean_section;
					}
				}
				break;

			case 'id':
			case 'track_downl':
				$clean_val = trim( sanitize_text_field( $value ) );
				break;
			case 'limit_country':

				$clean_val = [];

				if ( is_array( $value ) && ! empty( $value['method'] ) && ! empty( $value['countries'] ) ) {
					$clean_val['method'] = sanitize_key( $value['method'] );
					$clean_val['countries'] = trim( sanitize_text_field( $value['countries'] ) );
				};

				break;
			default:
				$clean_val = is_bool ( $value ) || is_string ( $value ) ? strip_tags( stripslashes( $value ) ) : false;
				break;
		}

		// error_log('sanitized ' . $clean_key . ' value: ' . json_encode($clean_val) );

		if ( ! empty( $clean_val ) && ! empty ( $clean_key ) ) $clean_data[$clean_key] = $clean_val;
	}
}
?>
