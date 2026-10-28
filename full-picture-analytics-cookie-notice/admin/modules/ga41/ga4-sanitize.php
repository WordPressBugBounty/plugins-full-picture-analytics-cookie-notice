<?php

$clean_data = array();

if ( ! empty ( $input ) ) foreach( $input as $key => $value ) {

	$clean_key = sanitize_key( $key );

	if( ! empty( $value ) ) {

		switch ($clean_key) {
			case 'page_type':
			case 'page_id':
			case 'page_number':
			case 'post_date':
			case 'user_role':
			case 'page_lang':
			case 'tax_terms':
			case 'seo_title':
			case 'post_author':
			case 'search_results_nr':
			case 'author_id':
			case 'track_scroll_method':
			case 'track_views_method':
			case 'track_affil_method':
			case 'track_elems_method':
			case 'track_forms_method':
			case 'track_email_tel':
				$clean_val = sanitize_key( $value );
				break;
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
						$clean_val[$j]['type'] = empty( $section['type'] ) ? 'event' : sanitize_key( $section['type'] );
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

						if ( empty( $section['atrig_id'] ) || empty( $section['evt_name'] ) ) continue;

						$clean_val[$j]['atrig_id'] = sanitize_key( $section['atrig_id'] );
						$clean_val[$j]['evt_name'] = sanitize_text_field( $section['evt_name'] );

						if ( $clean_key == 'custom_events' ) $clean_val[$j]['repeat'] = empty( $section['repeat'] ) ? 'no' : sanitize_key( $section['repeat'] );

						// Parameters

						// If parameters exist
						if ( ! empty ( $section['params'] ) && is_array( $section['params'] ) ) {
							
							$clean_params = [];
							$k = 0;
							
							foreach ( $section['params'] as $param ) {
								// Check if both name and value exist
								if ( ! empty( $param['name'] ) && ! empty( $param['val'] ) && ! empty( $param['type'] ) ) {
									$clean_params[$k]['name'] = sanitize_key( $param['name'] );
									$clean_params[$k]['type'] = sanitize_key( $param['type'] );
									
									switch ( $clean_params[$k]['type'] ) {
										case 'string':
										case 'path':
											$clean_params[$k]['val'] = sanitize_text_field( trim( $param['val'] ) );
										break;

										case 'number':
											$clean_params[$k]['val'] = (int) $param['val'];
										break;

										case 'bool':
											$clean_params[$k]['val'] = $param['val'] === 'true' || $param['val'] === '1' ? 1 : 0;
										break;
									}
									
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

			case 'track_cf':

				$clean_val = [];

				if ( is_array($value) ){

					$j = 0;

					foreach( $value as $section ){

						if ( empty( $section['id'] ) || empty( $section['dimname'] ) ) continue;

						$clean_val[$j]['id'] = trim( sanitize_text_field( $section['id'] ) );
						$clean_val[$j]['dimname'] = sanitize_key( $section['dimname'] );

						$j++;
					}
				};

				break;

			case 'purchase_custom_meta':
				
				$clean_val = [];

				if ( is_array($value) ){

					$i = 0;
					$j = 0;
					$max = count( $value );

					for ( ; $i < $max; $i++) { 
						$section = $value[$i];
						if ( empty( $section['key'] ) || empty( $section['dimname'] ) ) continue;
						$clean_val[$j]['key'] = sanitize_key( $section['key'] );
						$clean_val[$j]['type'] = empty( $section['type'] ) ? 'string' : sanitize_key( $section['type'] );
						$clean_val[$j]['dimname'] = sanitize_key( $section['dimname'] );
						$j++;
					}
				};
				break;
			case 'mp_secret_key':
			case 'id':
			case 'id2':
				$clean_val = trim( sanitize_text_field( $value ) );
				break;
			default:
				$clean_val = is_bool ( $value ) || is_string ( $value ) ? strip_tags( stripslashes( $value ) ) : false;
			break;
		}

		// error_log('sanitized ' . $clean_key . ' value: ' . json_encode($clean_val) );

		if ( ! empty( $clean_val ) && ! empty ( $clean_key ) ) $clean_data[$clean_key] = $clean_val;
	}
}
