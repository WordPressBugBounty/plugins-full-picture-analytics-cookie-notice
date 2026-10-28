<?php

$ret_text = '';

switch( $section_id ){

	// INSTALLATION

	case 'fupi_hotj_install':

		$ret_text = '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Hotjar' ) . '<br><span class="fupi_small">' . esc_html__( 'Please fill in the required fields below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! Hotjar is installed', 'full-picture-analytics-cookie-notice' ) . '</p>
		</div>';
	break;

	// LOADING
	
	case 'fupi_hotj_loading':
		$ret_text = '<p>' . esc_html__( 'Change when and where this tool loads. This is all optional.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// DATA COLLECTION

	case 'fupi_hotj_basic':
		$ret_text = '<p>' . esc_html__( 'These settings impact the amount and precision of collected data.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// SIMPLE EVENTS

	case 'fupi_hotj_tags':
		$ret_text = '<div>
			<p>' . esc_html__( 'Tag session recordings with information about clicks, form submissions and others events.', 'full-picture-analytics-cookie-notice' ) . ' <span class="fupi_warning_text">' . sprintf( esc_html__( '%1$sRead this%2$s before you start.', 'full-picture-analytics-cookie-notice'), '<button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_warning_offscreen">', ' <span class="fupi_open_offscreen_i">?</span></button>' ) . '</span></p>
		</div>';
	break;

	// CUSTOM EVENTS
	
	case 'fupi_hotj_custom_events':
		$ret_text = '<p>' . esc_html__( 'Track specific user actions' , 'full-picture-analytics-cookie-notice' ) . '  <span class="fupi_warning_text">' . sprintf( esc_html__( '%1$sRead this%2$s before you start.', 'full-picture-analytics-cookie-notice'), '<button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_warning_offscreen">', ' <span class="fupi_open_offscreen_i">?</span></button>' ) . '</span></p>';
	break;

	// E-COMMERCE

	case 'fupi_hotj_ecomm':
		
		if ( empty( $no_woo_descr_text ) ) {
			$ret_text = '<p class="fupi_warning_text">' . sprintf( esc_html__( '%1$sRead this%2$s before you start.', 'full-picture-analytics-cookie-notice'), '<button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_warning_offscreen">', ' <span class="fupi_open_offscreen_i">?</span></button>' ) . '</p>';
		} else {
			$ret_text = $no_woo_descr_text;
		}

	break;
};

?>
