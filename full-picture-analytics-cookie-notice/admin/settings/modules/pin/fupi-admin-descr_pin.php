<?php

switch( $a['id'] ){

	// BASIC CONFIG

	case 'fupi_pin_install':

		$module_data = get_option('fupi_pin');
		$pin_id = ! empty ( $module_data ) && ! empty ( $module_data['id'] ) ? esc_attr( $module_data['id'] ) : '';

		return '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Pinterest' ) . '<br><span class="fupi_small">' . esc_html__( 'To install it, please fill in the required field below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! Pinterest Tag is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . esc_html__( 'The data is tracked by a tag with an ID ', 'full-picture-analytics-cookie-notice' ) . $pin_id . '</span>.</p>
		</div>';

	break;

	// LOADING

	case 'fupi_pin_loading':
		return '<p>' . esc_html__( 'If you have consent banner enabled in the opt-in or one of automatic modes, Pinterest will start working after visitors consent to using their data for statistics and marketing purposes.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// TRACKING

	case 'fupi_pin_track':
		return '';
	break;

	// E-COMMERCE

	case 'fupi_pin_ecomm':

		if ( empty( $this->enable_woo_descr_text ) ) {
			return '<p>' . esc_html__( 'You are now automatically tracking WooCommerce events:', 'full-picture-analytics-cookie-notice') . '</p>
				<ol class="fupi_checked_list">
					<li>' . esc_html__( 'purchase', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'product view', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'add to cart', 'full-picture-analytics-cookie-notice') . '</li>
				</ol>
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
