<?php

switch( $a['id'] ){

	// MAIN

	case 'fupi_gads_install':

		$module_data = get_option('fupi_gads');
		$conv_id = ! empty ( $module_data ) && ! empty ( $module_data['id'] ) ? esc_attr( $module_data['id'] ) : '';

		return '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Google Ads' ) . '<br><span class="fupi_small">' . esc_html__( 'To install it, please fill in the required field below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>

		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! Google Ads is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . esc_html__( 'The data is sent to Google Ads conversion ID ', 'full-picture-analytics-cookie-notice' ) . ' ' . $conv_id . '</span>.</p>
		</div>';
	break;

	// LOADING

	case 'fupi_gads_loading':
		return '<p>' . esc_html__( 'If you have consent banner enabled in the opt-in or one of automatic modes, GAds will start tracking after visitors consent to using their personal data for statistics and marketing purposes. Google consent mode can be enabled in the settings of the consent banner.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// CUSTOM EVENTS TRACKING
	
	case 'fupi_gads_atrig':
		return '<p>' .sprintf( esc_html__( 'Track when visitors behave like potential clients. Learn %1$smore about lead scoring%2$s and %3$show to set it up%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/blog/lead-scoring-in-web-analytics-what-is-it-and-how-to-use-it/">', '</a>', '<a href="https://wpfullpicture.com/support/documentation/how-to-use-lead-scoring/">' ) . '</p>';
	break;
		
	// EVENTS TRACKING
	case 'fupi_gads_events':
		return '<p>' . sprintf( esc_html__('Provide %1$sconversion labels%2$s in the fields below to track as conversions visitors\' actions.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.youtube.com/watch?v=HWSk4d-JDMA">', '</a>' ) . '</p>';
	break;

	// E-COMMERCE

	case 'fupi_gads_ecomm':

		if ( empty( $this->enable_woo_descr_text ) ) {
			return '<p>' . sprintf( esc_html__('Provide %1$sconversion labels%2$s in the fields below to track as conversions: purchases, checkouts and additions to cart.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.youtube.com/watch?v=HWSk4d-JDMA">', '</a>' ) . '</p>
				<p>' . esc_html__( 'All these events are sent with product information.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p class="fupi_woo_reqs_info"><strong>' . esc_html__( 'Attention.', 'full-picture-analytics-cookie-notice' ) . '</strong> ' . sprintf( esc_html__( 'WP Full Picture\'s automatic tracking is designed to work with %1$sstandard Woocommerce hooks and HTML%2$s. If your store doesn\'t use them or modifies them, tracking may not work.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to test it', 'full-picture-analytics-cookie-notice' ) . '</a>.</p>';
		} else {
			return $this->enable_woo_descr_text;
		};

	break;

    default:
        return '';
    break;
};

?>
