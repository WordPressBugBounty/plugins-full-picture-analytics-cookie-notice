<?php

switch( $a['id'] ){

	// INSTALLATION

	case 'fupi_ga41_install':

		$module_data = get_option('fupi_ga41');
		$ga4_id = ! empty ( $module_data ) && ! empty ( $module_data['id'] ) ? esc_attr( $module_data['id'] ) : '';

		return '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Google Analytics' ) . '<br><span class="fupi_small">' . esc_html__( 'To install it, please fill in the required field below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! GA 4 is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . esc_html__( 'The data is sent to an account with measurement ID ', 'full-picture-analytics-cookie-notice' ) . $ga4_id . '</span>.</p>
		</div>';
	break;

	// LOADING
	
	case 'fupi_ga41_loading':
		return '<p>' . esc_html__( 'If you have consent banner enabled in the opt-in or one of automatic modes, GA will start tracking after visitors consent to using their personal data for statistics. Additional functionality of GA will be enabled if they also consent to using the data for marketing purposes.Google consent mode can be enabled in the settings of the consent banner.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// EVENTS TRACKING

	case 'fupi_ga41_events':
		return '<p>' . esc_html__( 'Track what actions visitors take on your site. you can send them to GA as either different events with no parameters (easy) or single events with parameters (advanced). Click "i" icons next to labels below to learn more.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// CONDITIONAL TRACKING
	
	case 'fupi_ga41_atrig':
		return '<p>' .sprintf( esc_html__( 'Track when visitors behave like potential clients. Learn %1$smore about lead scoring%2$s and %3$show to set it up%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/blog/lead-scoring-in-web-analytics-what-is-it-and-how-to-use-it/">', '</a>', '<a href="https://wpfullpicture.com/support/documentation/how-to-use-lead-scoring/">' ) . '</p>';
	break;

	// WP DATA
	
	case 'fupi_ga41_wpdata':
		return '<p>' . sprintf( esc_html__( 'Event parameters give context to GA events. In order to see them in GA, you need to %1$sregistered them as custom dimensions%2$s and set up custom reports. They will not be visible in standard reports.', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ) . '</p>';
	break;

	// E-COMMERCE

	case 'fupi_ga41_ecomm':

		if ( empty( $this->enable_woo_descr_text ) ) {

			return '<p>' . esc_html__( 'You are now automatically tracking WooCommerce events:', 'full-picture-analytics-cookie-notice') . '</p>
				<ol class="fupi_checked_list">
					<li>' . esc_html__( 'purchase', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'checkout', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'product view', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'product list view', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'list item click', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'add to cart', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'remove from cart', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'add to wishlist (if you set it up on the WooCommerce Tracking page)', 'full-picture-analytics-cookie-notice') . '</li>
				</ol>
				<p>' . esc_html__( 'All these events are sent with product information.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p class="fupi_woo_reqs_info"><strong>' . esc_html__( 'Attention.', 'full-picture-analytics-cookie-notice' ) . '</strong> ' . sprintf( esc_html__( 'WP Full Picture\'s automatic tracking is designed to work with %1$sstandard Woocommerce hooks and HTML%2$s. If your store doesn\'t use them or modifies them, tracking may not work.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to test it', 'full-picture-analytics-cookie-notice' ) . '</a>.</p>';
		} else {
			return $this->enable_woo_descr_text;
		}

	break;

	// DEPRECATED

	case 'fupi_ga41_deprecated':
		return '<h3 class="fupi_title">' . esc_html__( 'These functions are scheduled for removal in future updates', 'full-picture-analytics-cookie-notice' ) . '</h3>';
	break;

    default:
        return '';
    break;
};

?>
