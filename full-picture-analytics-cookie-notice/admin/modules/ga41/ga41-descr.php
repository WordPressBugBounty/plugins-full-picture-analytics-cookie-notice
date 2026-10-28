<?php

$ret_text = '';

switch( $section_id ){

	// INSTALLATION

	case 'fupi_ga41_install':

		$ret_text = '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Google Analytics' ) . '<br><span class="fupi_small">' . esc_html__( 'Please fill in the required fields below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'GA 4 is installed', 'full-picture-analytics-cookie-notice' ) . '</p>
		</div>';
	break;

	// DATA COLLECTION

	case 'fupi_ga41_basic':
		$ret_text = '<p>' . esc_html__( 'These settings impact the amount and precision of collected data.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// EVENTS

	case 'fupi_ga41_events':
		$ret_text = '<p>' . esc_html__('Track clicks, form submissions and others events.', 'full-picture-analytics-cookie-notice' ) . ' ' . esc_html__( 'You can send them to GA as either separate events (every action gets a unique event - easy) or single events with parameters that identify actions (advanced).', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// CUSTOM EVENTS
	
	case 'fupi_ga41_custom_events':
		$ret_text = '<p>' . esc_html__( 'Track specific user actions.', 'full-picture-analytics-cookie-notice' ) . ' ' . sprintf( esc_html__( 'Remember to %1$sregister parameters in GA dashboard%2$s to view them in reports.', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/" target="_blank">', '</a>' ) . '</p>';
	break;

	// ENGAGEMENT

	case 'fupi_ga41_vscores':
		$ret_text = '<p>' . esc_html__( 'Use these functions to track how engaged visitors are. Go to the settings of the Engagement Tracking module to set how many points visitors should get for taking specific actions.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// PARAMS
	
	case 'fupi_ga41_wpdata':
		$ret_text = '<p>' . esc_html__( 'Parameters add context to events.', 'full-picture-analytics-cookie-notice') . '</p>
			<p class="fupi_warning_text">' . sprintf( esc_html__( 'In order to see parameters in reports, you need to %1$sregistered them in GA%2$s.', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ) . '</a></p>
			<p class="fupi_warning_text">' . esc_html__( 'Parameter names must contain only lowercase letters, digits and underscores and cannot start with a digit.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// E-COMMERCE

	case 'fupi_ga41_ecomm':

		if ( empty( $no_woo_descr_text ) ) {

			$ret_text = '<p>' . esc_html__( 'You are now automatically tracking WooCommerce events:', 'full-picture-analytics-cookie-notice') . '</p>
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
				<p class="fupi_woo_reqs_info"><strong>' . esc_html__( 'Attention.', 'full-picture-analytics-cookie-notice' ) . '</strong> ' . sprintf( esc_html__( 'WP Full Picture can track stores that use %1$sstandard Woocommerce hooks and HTML%2$s. If your store uses plugins that don\'t use them, tracking may not work.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to test it', 'full-picture-analytics-cookie-notice' ) . '</a>.</p>';
		} else {
			$ret_text = $no_woo_descr_text;
		}

	break;

	// DEPRECATED

	case 'fupi_ga41_deprecated':
		$ret_text = '<h3 class="fupi_title">' . esc_html__( 'These functions are scheduled for removal in future updates', 'full-picture-analytics-cookie-notice' ) . '</h3>';
	break;
};

?>
