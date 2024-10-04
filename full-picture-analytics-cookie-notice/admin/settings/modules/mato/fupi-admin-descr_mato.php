<?php

switch( $a['id'] ){

	// INSTALLATION

	case 'fupi_mato_install':

		$module_data = get_option('fupi_mato');
		$mato_id = ! empty ( $module_data ) && ! empty ( $module_data['id'] ) ? esc_attr( $module_data['id'] ) : '';
		$mato_url = ! empty ( $module_data ) && ! empty ( $module_data['url'] ) ? esc_attr( $module_data['url'] ) : '';
		$mato_src = ! empty ( $module_data ) && ! empty ( $module_data['src'] ) ? esc_attr( $module_data['src'] ) : '';
		$install_details_text = '';

		if ( ! empty( $mato_id ) && ! empty( $mato_url ) ){
			if ( ! empty( $mato_src ) ) { 
				$install_details_text = esc_html__( 'The data is sent to Matomo Cloud.', 'full-picture-analytics-cookie-notice' );
			} else {
				$install_details_text = esc_html__( 'The data is sent to your on-premise installation of Matomo. ', 'full-picture-analytics-cookie-notice' );
			}
		}

		return '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'Matomo' ) . '<br><span class="fupi_small">' . esc_html__( 'To install it, please fill in the required field below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! Matomo is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . $install_details_text . '</span></p>
		</div>';

	break;

	// LOADING
	
	case 'fupi_mato_loading':
		return '<p>' . esc_html__( 'If you have consent banner enabled in the opt-in or one of automatic modes, Matomo will start working after visitors consent to using their data for statistics. This can be avoided if you enable the privacy mode below.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// WP DATA TRACKING

	case 'fupi_mato_wpdata':
		return '<p>' . sprintf( esc_html__( 'Give context to tracked events. To track most of the data below, you will have to %1$sregister custom dimensions in your Matomo\'s admin panel%2$s.', 'full-picture-analytics-cookie-notice'), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-dimensions-in-matomo/">', '</a>' ) . '</p>';
	break;

	// EVENTS TRACKING

	case 'fupi_mato_events':
		return '<p>' . esc_html__( 'Track actions that visitors take on your website.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// CUSTOM EVENTS TRACKING

	case 'fupi_mato_atrig':
		return '<p>' .sprintf( esc_html__( 'Track when visitors behave like potential clients. Learn %1$smore about lead scoring%2$s and %3$show to set it up%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/blog/lead-scoring-in-web-analytics-what-is-it-and-how-to-use-it/">', '</a>', '<a href="https://wpfullpicture.com/support/documentation/how-to-use-lead-scoring/">' ) . '</p>';
	break;

	// WOOCOMMERCE

	case 'fupi_mato_ecomm':

		if ( empty( $this->enable_woo_descr_text ) ) {

			return '<p>' . esc_html__( 'To automatically track WooCommerce events, you need to enable Ecommerce in Matomo.', 'full-picture-analytics-cookie-notice') . '</p>
				<p style="text-align: center;"><button type="button" class="button-secondary fupi_open_popup" data-popup="fupi_enable_ecomm_popup">' . esc_html__( 'Learn how to do it', 'full-picture-analytics-cookie-notice') . '</button></p>
				<p>' . esc_html__( 'After you do this, WP FP will track and send to Matomo these events:', 'full-picture-analytics-cookie-notice') . '</p>
				<ol class="fupi_checked_list">
					<li>' . esc_html__( 'purchase (as an internal and separate event)', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . sprintf( esc_html__( 'product view %1$s(only as an internal Matomo event!)%2$s', 'full-picture-analytics-cookie-notice'), '<strong style="color: #e47d00">', '</strong>' ) . '</li>
					<li>' . esc_html__( 'add to cart (as an internal and separate event)', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'remove from cart (as an internal and separate event)', 'full-picture-analytics-cookie-notice') . '</li>
				</ol>
				<p>' . esc_html__( 'Internal events are used by Matomo to create default Ecommerce reports and are sent with product information, e.g. what products were purchased or added to cart. These events cannot be used for creating goals or in custom reports.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p>' . esc_html__( 'Separate events do not come with extra data but can be used as goals and in custom reports.', 'full-picture-analytics-cookie-notice' ) . '</p>
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
