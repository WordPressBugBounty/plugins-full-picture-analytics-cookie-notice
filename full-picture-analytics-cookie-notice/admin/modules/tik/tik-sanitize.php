<?php

$clean_data = array();

if ( ! empty( $input ) ) foreach( $input as $key => $value ) {

	$clean_key = sanitize_key( $key );

	if( ! empty( $value ) ) {

		switch ($clean_key) {
			case 'purchase_evt_name':
				$clean_val = sanitize_key( $value );
				break;
			case 'track_clicks':
			case 'track_form_submits':
				
				$clean_val = [];

				if ( is_array( $value ) ){

					$j = 0;

					foreach( $value as $section ){
						if ( empty( $section['atrig_id'] ) || empty( $section['val'] ) ) continue;
						$clean_val[$j]['atrig_id'] = esc_attr( $section['atrig_id'] );
						$clean_val[$j]['val'] = trim( sanitize_text_field( $section['val'] ) );
						$clean_val[$j]['type'] = empty( $section['type'] ) ? 'lead' : sanitize_key( $section['type'] );
						$j++;
					}
				};
				break;
				
			case 'id':
				$clean_val = trim( sanitize_text_field( $value ) );
				break;
			case 'limit_country':

				$clean_val = [];

				if ( is_array( $value ) && ! empty( $value['method'] ) && ! empty( $value['countries'] ) ) {
					$method = sanitize_key( $value['method'] );

					if ( ! in_array( $method, [ 'excl', 'incl' ], true ) ) break;

					$clean_val['method'] = $method;
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
