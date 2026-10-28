<?php

$clean_data = array();

if ( ! empty( $input ) ) foreach( $input as $key => $value ) {

	$clean_key = sanitize_key( $key );

	if( ! empty( $value ) ) {

		switch ($clean_key) {
			// number
			case 'id':
			case 'seo_title_dimens':
			case 'page_id_dimens':
			case 'page_type_dimens':
			case 'author_dimens':
			case 'user_role_dimens':
			case 'page_lang_dimens':
			case 'tax_terms_dimens':
			case 'author_id_dimens':
				$clean_val = (int) $value;
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
						if ( empty( $section['atrig_id'] ) || empty( $section['evt_name'] ) ) continue;
						$clean_val[$j]['atrig_id'] = sanitize_key( $section['atrig_id'] );
						$clean_val[$j]['evt_name'] = sanitize_text_field( $section['evt_name'] );

						if ( $clean_key == 'custom_events' ) {
							$repeat = empty( $section['repeat'] ) ? 'no' : sanitize_key( $section['repeat'] );
							$clean_val[$j]['repeat'] = in_array( $repeat, [ 'no', 'yes' ], true ) ? $repeat : 'no';
						}

						if ( isset( $section['evt_val'] ) && $section['evt_val'] !== '' ) $clean_val[$j]['evt_val'] = (int) $section['evt_val'];

						$j++;
					}
				};
				break;
			case 'url':
				$clean_val = sanitize_url( $value );
				break;
			case 'src':
			case 'track_downl_file_formats':
				$clean_val = trim( sanitize_text_field( $value ) );
				break;
			case 'limit_country':
				$clean_val = [];

				if ( is_array( $value ) && ! empty( $value['method'] ) && ! empty( $value['countries'] ) ) {
					$clean_val['method'] = sanitize_key( $value['method'] );
					$clean_val['countries'] = trim( sanitize_text_field( $value['countries'] ) );
				};

				break;
			case 'track_cf':
				$clean_val = [];
				if ( is_array($value) ){
					$i = 0;
					foreach( $value as $section ){
						if ( empty( $section['id'] ) || empty( $section['dim'] ) ) continue;
						$clean_val[$i]['id'] = trim( sanitize_text_field( $section['id'] ) );
						$clean_val[$i]['dim'] = (int) $section['dim'];
						$i++;
					}
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
