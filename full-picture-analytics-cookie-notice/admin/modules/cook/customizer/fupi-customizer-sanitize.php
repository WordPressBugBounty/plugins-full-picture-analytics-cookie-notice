<?php

$setting_id = $setting->id;
$field_type = $setting->manager->get_control( $setting_id )->type;

// error_log('setting choices: '. json_encode($setting->manager->get_control( $setting->id )->choices));

// Sanitize in bulk all fields of the same type
switch ( $field_type ) {
	case 'checkbox':
		return ( $val === true ) ? true : false;
	break;
	case 'multi_checkbox' :
		$multi_values = ! is_array( $val ) ? explode( ',', $val ) : $val;
		return !empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
	break;
	case 'text':
		switch ($setting_id) {
			case 'fupi_cookie_notice[btn_class]':
			case 'fupi_cookie_notice[cta_class]':

				if ( ! empty ( $val ) ) {
					$classes = explode( ' ', $val );
					$clean_classes = array_map( 'sanitize_html_class', $classes );
					$classes_string = implode( ' ', $clean_classes );

					if ( ! is_numeric ( $classes_string ) ) {
						return $classes_string;
					} else {
						return '';
					}
				} else {
					return '';
				}
			break;
		}
	break;
}

// Sanitize specific fields
switch ( $setting_id ) {
	case 'fupi_ifrblock_border_width':
		$min = 0;
		$max = 20;
	break;
	case 'fupi_ifrblock_border_radius':
	case 'fupi_ifrblock_btn_border_radius':
		$min = 0;
		$max = 100;
	break;
	case 'fupi_ifrblock_ratio_x':
	case 'fupi_ifrblock_ratio_y':
		$min = 1;
		$max = 100;
	break;
	case 'fupi_ifrblock_notice_font_size':
	case 'fupi_ifrblock_btn_font_size':
		$min = 10;
		$max = 40;
	break;
	case 'fupi_ifrblock_btn_size':
		return in_array( $val, array( 'small', 'medium', 'large' ), true ) ? $val : null;
	break;
	case 'fupi_ifrblock_image':
		$attachment_id = absint( $val );
		return empty( $attachment_id ) || wp_attachment_is_image( $attachment_id ) ? $attachment_id : null;
	break;

	// Media file ID for use an an icon in the consent banner toggler
	case 'fupi_custom_toggler_img':
		if ( ! is_numeric( $val ) ) return false;
		return $val; 
	break;
}

if ( isset( $min, $max ) ) {
	if ( ! is_numeric( $val ) ) return null;

	$val = (int) $val;
	return min( $max, max( $min, $val ) );
}
