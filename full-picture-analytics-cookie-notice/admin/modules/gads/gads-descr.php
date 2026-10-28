<?php

$ret_text = '';
$how_to_useit = '<p style="text-align: center;" class="fupi_warning_text"><button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_howtouseit_offscreen">' . esc_html__( 'How NOT to use Google Ads with WP Full Picture.', 'full-picture-analytics-cookie-notice' ) . ' <span class="fupi_open_offscreen_i">?</span></button></p>';

switch( $section_id ){

	// MAIN

	case 'fupi_gads_install':

		$ret_text = '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Google Ads' ) . '<br><span class="fupi_small">' . esc_html__( 'Please fill in the required fields below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>

		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Google Ads is installed', 'full-picture-analytics-cookie-notice' ) . '</p>
		</div>' . $how_to_useit;
	break;

	// PRIVACY

	case 'fupi_gads_basic':
		$ret_text = '<p>' . esc_html__( 'These settings impact the amount and precision of collected data.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;
		
	// EVENTS

	case 'fupi_gads_events':
		$ret_text = '<div>
			<p>' . esc_html__('Track clicks, form submissions and others events.', 'full-picture-analytics-cookie-notice' ) . ' ' . sprintf( esc_html__('%1$sFollow this tutorial%2$s to get conversion labels that you can use below.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/how-to-get-google-ads-tag-id-conversion-id/">', '</a>' ) . '</p>
		</div>';
	break;

	// CUSTOM EVENTS
	
	case 'fupi_gads_custom_events':
		$ret_text = '<div>
			<p>' . esc_html__( 'Track specific user actions.' , 'full-picture-analytics-cookie-notice' ) . ' ' . sprintf( esc_html__('%1$sFollow this tutorial%2$s to get conversion labels that you can use below.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/how-to-get-google-ads-tag-id-conversion-id/" target="_blank">', '</a>' ) . '</p>
		</div>';
	break;

	// ENGAGEMENT

	case 'fupi_gads_vscor':
		$ret_text = '<p>' . esc_html__( 'Use these functions to track how engaged visitors are. Go to the settings of the Engagement Tracking module to set how many points visitors should get for taking specific actions.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// E-COMMERCE

	case 'fupi_gads_ecomm':

		if ( empty( $no_woo_descr_text ) ) {
			$ret_text = '<p>' . sprintf( esc_html__('Provide %1$sconversion labels%2$s in the fields below to track purchases, checkouts and additions to cart as conversions.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/how-to-get-google-ads-tag-id-conversion-id/">', '</a>' ) . '</p>
				<p>' . esc_html__( 'All these events are sent with product information.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p class="fupi_woo_reqs_info"><strong>' . esc_html__( 'Attention.', 'full-picture-analytics-cookie-notice' ) . '</strong> ' . sprintf( esc_html__( 'WP Full Picture can track stores that use %1$sstandard Woocommerce hooks and HTML%2$s. If your store uses plugins that don\'t use them, tracking may not work.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to test it', 'full-picture-analytics-cookie-notice' ) . '</a>.</p>';
		} else {
			$ret_text = $no_woo_descr_text;
		};

	break;
};

?>
