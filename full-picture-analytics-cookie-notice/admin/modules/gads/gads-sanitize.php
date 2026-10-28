<?php

$clean_data = array();

if ( ! empty ( $input ) ) foreach( $input as $key => $value ) {

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

				if ( is_array( $value ) ){

					$j = 0;

					foreach ( $value as $section ) {
						
						if ( empty( $section['atrig_id'] ) || empty( $section['conv_label'] ) ) continue;
						
						$clean_val[$j]['atrig_id'] = sanitize_key( $section['atrig_id'] );
						$clean_val[$j]['conv_label'] = sanitize_text_field( $section['conv_label'] );

						if ( $clean_key == 'custom_events' ) {
							$repeat = empty( $section['repeat'] ) ? 'no' : sanitize_key( $section['repeat'] );
							$clean_val[$j]['repeat'] = in_array( $repeat, [ 'no', 'yes' ], true ) ? $repeat : 'no';
						}

						// Parameters

						// If parameters exist
						if ( ! empty ( $section['params'] ) && is_array( $section['params'] ) ) {
							
							$clean_params = [];
							$k = 0;
							
							foreach ( $section['params'] as $param ) {
								// Check if both name and value exist
								if ( ! empty( $param['name'] ) && ! empty( $param['val'] ) && ! empty( $param['type'] ) ) {
									$clean_param_type = sanitize_key( $param['type'] );
									$clean_param = [
										'name' => sanitize_key( $param['name'] ),
										'type' => $clean_param_type,
									];
									
									switch ( $clean_param_type ) {
										case 'string':
										case 'path':
											$clean_param['val'] = sanitize_text_field( trim( $param['val'] ) );
										break;

										case 'number':
											$clean_param['val'] = (int) $param['val'];
										break;

										case 'bool':
											$clean_param['val'] = $param['val'] === 'true' || $param['val'] === '1' ? 1 : 0;
										break;

										default:
											continue 2;
									}

									$clean_params[$k] = $clean_param;
									
									$k++;
								}
							}
							
							// Only save evt_params if there are valid parameters
							if ( ! empty( $clean_params ) ) {
								$clean_val[$j]['params'] = $clean_params;
							}
						}

						$j++;
					}
				};
				break;

			case 'id':
			case 'id2':
			case 'business_type':
			case 'woo_conv_id':
			case 'woo_checkout_conv_id':
			case 'woo_add_to_cart_conv_id':
			case 'track_tel':
			case 'track_email':
				$clean_val = trim( sanitize_text_field( $value ) );
				break;
			case 'self_host_file_name':
				$clean_val = sanitize_key( $value );
				break;

			default:
				$clean_val = is_bool ( $value ) || is_string ( $value ) ? strip_tags( stripslashes( $value ) ) : false;
				break;
		}

		// error_log('sanitized ' . $clean_key . ' value: ' . json_encode($clean_val) );

		if ( ! empty( $clean_val ) && ! empty ( $clean_key ) ) $clean_data[$clean_key] = $clean_val;
	}
}
