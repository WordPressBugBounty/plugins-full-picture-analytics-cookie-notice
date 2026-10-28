<?php

$clean_data = array();

if ( ! empty( $input ) ) foreach( $input as $key => $value ) {

	$clean_key = sanitize_key( $key );

	if( ! empty( $value ) ) {

		switch ($clean_key) {
			
			case 'wishlist_btn_sel':
			case 'teaser_wrapper_sel':
				$clean_val = trim( sanitize_text_field( $value ) );
				break;

			case 'brand_tax':
			case 'variable_tracking_method':
			case 'where_track_addtocart':
				$clean_val = sanitize_key( $value );
				break;

			case 'force_item_view_on_url':
				$clean_val = [];
				if ( is_array( $value ) ){
					$j = 0;
					foreach( $value as $section ){
						if ( ! empty( $section['url_part'] ) ) {
							$clean_val[$j]['url_part'] = trim( sanitize_text_field( $section['url_part'] ) );
							$j++;
						}
					}
				};
				break;

			case 'disable_woo_events':
				$clean_val = array_map( 'sanitize_key', $value );
				break;

			case 'server_track_on_statuses':
			case 'server_cancel_on_statuses';
				$clean_val = array_map( 'sanitize_key', $value );
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
