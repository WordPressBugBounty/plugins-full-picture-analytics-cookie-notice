<?php

$ret_text = '';

switch( $section_id ){

    // MAIN

	case 'fupi_linkd_install':

		$ret_text = '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'LinkedIn Insight Tag' ) . '<br><span class="fupi_small">' . esc_html__( 'Please fill in the required fields below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'LinkedIn Insight Tag is installed', 'full-picture-analytics-cookie-notice' ) . '</p>
		</div>';

	break;

	// LOADING
	
	case 'fupi_linkd_loading':
		$ret_text = '<p>' . esc_html__( 'Change when and where this tool loads. This is all optional.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

    // SIMPLE EVENTS

	case 'fupi_linkd_events':
		$ret_text = '<p>' . esc_html__( 'To get conversion IDs you need to register these events in LinkedIn Campaign Manager and paste their IDs in the form below.', 'full-picture-analytics-cookie-notice') . ' <button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_track_convert_offscreen">' . esc_html__('Learn more','full-picture-analytics-cookie-notice') . ' <span class="fupi_open_offscreen_i">?</span></button></p>';
	break;

	case 'fupi_linkd_ecomm':

		if ( empty( $no_woo_descr_text ) ) {
			$ret_text = '<p>' . esc_html__( 'To track conversions you need to register them in LinkedIn Campaign Manager and paste their IDs in the form below.', 'full-picture-analytics-cookie-notice') . ' <button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_track_convert_offscreen">' . esc_html__('Learn more','full-picture-analytics-cookie-notice') . ' <span class="fupi_open_offscreen_i">?</span></button></p>';
		} else {
			$ret_text = $no_woo_descr_text;
		};

	break;
};

?>
