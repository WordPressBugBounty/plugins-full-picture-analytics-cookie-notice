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

				if ( is_array( $value ) ){

					$j = 0;

					foreach( $value as $section ){
						// Require both atrig_id and tag_name
						if ( empty( $section['atrig_id'] ) || empty( $section['tag_name'] ) ) continue;
						
						$clean_val[$j]['atrig_id'] = sanitize_key( $section['atrig_id'] );
						$clean_val[$j]['tag_name'] = trim( sanitize_text_field( $section['tag_name'] ) );
						
						if ( $clean_key == 'custom_events' ) {
							$repeat = empty( $section['repeat'] ) ? 'no' : sanitize_key( $section['repeat'] );
							$clean_val[$j]['repeat'] = in_array( $repeat, [ 'no', 'yes' ], true ) ? $repeat : 'no';
						}

						$j++;
					}
				};
				break;


			case 'id':
				$clean_val = trim( sanitize_text_field( $value )) ;
				break;
			case 'user_attr':
			case 'tag_woo_purchases_data':
			case 'tag_woo_checkouts_data':
			case 'tag_woo_addtocart_data':
			case 'tag_woo_removefromcart_data':
			case 'tag_woo_addtowishlist_data':
				$clean_val = array_map( 'sanitize_key', $value );
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
