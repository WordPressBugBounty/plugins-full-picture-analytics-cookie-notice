<?php

switch( $a['id'] ){

	case 'fupi_twit_install':

		$module_data = get_option('fupi_twit');
		$twit_id = ! empty ( $module_data ) && ! empty ( $module_data['id'] ) ? esc_attr( $module_data['id'] ) : '';

		return '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'X Ads pixel' ) . '<br><span class="fupi_small">' . esc_html__( 'To install it, please fill in the required field below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! X Ads tracking pixel is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . esc_html__( 'The data is tracked by a tag with an ID ', 'full-picture-analytics-cookie-notice' ) . $twit_id . '</span>.</p>
		</div>';

	break;

	// LOADING

	case 'fupi_twit_loading':
		return '<p>' . esc_html__( 'If you have consent banner enabled in the opt-in or one of automatic modes, X / Twitter Ads will start working after visitors consent to using their data for statistics and marketing purposes.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	case 'fupi_twit_single':

		return '<p>' . sprintf( esc_html__( 'Here you can set up tracking events that happen on your site. Learn %1$show to fill in the fields below%2$s.', 'full-picture-analytics-cookie-notice'), '<button type="button" class="fupi_open_popup fupi_faux_link" data-popup="fupi_track_actions_popup">', '</button>' ) . '</p>';

	break;

	// E-COMMERCE TRACKING

	case 'fupi_twit_ecomm':

		if ( empty( $this->enable_woo_descr_text ) ) {
			
			return '<p>' . esc_html__( 'WP Full Picture automatically tracks WooCommerce events:', 'full-picture-analytics-cookie-notice') . '</p>
				<ol class="fupi_checked_list">
					<li>' . esc_html__( 'purchase', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'checkout', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'product view', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'add to cart', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'add to wishlist (if you set it up on the WooCommerce Tracking page)', 'full-picture-analytics-cookie-notice') . '</li>
				</ol>
				<p>' . esc_html__( 'Fill in the fields below to choose which of these events will be sent to X / Twitter.', 'full-picture-analytics-cookie-notice') . '<p>
				<p><button type="button" class="button-secondary fupi_open_popup" data-popup="fupi_woo_popup">' . esc_html__( 'How to fill in the fields below', 'full-picture-analytics-cookie-notice' ) . '</button></p>
				<p>' . esc_html__( 'All these events are sent with product information.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p class="fupi_woo_reqs_info"><strong>' . esc_html__( 'Attention.', 'full-picture-analytics-cookie-notice' ) . '</strong> ' . sprintf( esc_html__( 'WP Full Picture\'s automatic tracking is designed to work with %1$sstandard Woocommerce hooks and HTML%2$s. If your store doesn\'t use them or modifies them, tracking may not work.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to test it', 'full-picture-analytics-cookie-notice' ) . '</a>.</p>';
		} else {
			return $this->enable_woo_descr_text;
		}

	break;

    default:
        return '';
    break;
};

?>