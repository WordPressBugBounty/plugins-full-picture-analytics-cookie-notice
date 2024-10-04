<?php

switch( $a['id'] ){

	// LOADING

	case 'fupi_pla_loading':
		return '<p>' . esc_html__( 'Plausible does not collect personaly identifiable information and does not require consent banner.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// STATS

	case 'fupi_pla_stats':
		return '<p>' . esc_html__( 'To show Plausible statistics in your WP admin, please fill in the field below. You will see your statistics in the "Reports" menu item. If the stats don\'t show up, do NOT set a password while generating the link.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// WP DATA

	case 'fupi_pla_wpdata':
		return '<p>' . esc_html__( 'This data will be sent to Plausible as properties of visited pages. To view it in your reports, you need to register it in your Plausible\'s panel.' , 'full-picture-analytics-cookie-notice' ) . ' <button type="button" class="button-secondary fupi_open_popup" data-popup="fupi_setup_popup">' . esc_html__('Learn how', 'full-picture-analytics-cookie-notice') . '</button></p>
		<p style="color:#e47d00; font-weight: bold;">' . esc_html__('Attention! Only users of Plausible Business plan can view event properties in the reports.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// EVENT TRACKING

	case 'fupi_pla_events_2':
		return '<p>' . esc_html__( 'To track user actions in Plausible, you first need to register them in your Plausible\'s panel.' , 'full-picture-analytics-cookie-notice' ) . ' <button type="button" class="button-secondary fupi_open_popup" data-popup="fupi_setup_popup">' . esc_html__('Learn how', 'full-picture-analytics-cookie-notice') . '</button></p>
		<p style="color:#e47d00; font-weight: bold;">' . esc_html__('Attention! Only users of Plausible Business plan can view event properties in the reports.', 'full-picture-analytics-cookie-notice' ) . '</p>
		<p style="color:#e47d00; font-weight: bold;">' . esc_html__('Attention! Properties cannot be used to create funnels.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// CONDITIONAL TRACKING
	
	case 'fupi_pla_cond':
		return '<p>' .sprintf( esc_html__( 'Track when visitors behave like potential clients. Learn %1$smore about lead scoring%2$s and %3$show to set it up%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/blog/lead-scoring-in-web-analytics-what-is-it-and-how-to-use-it/">', '</a>', '<a href="https://wpfullpicture.com/support/documentation/how-to-use-lead-scoring/">' ) . '</p>';
	break;

	// USER INFO

	case 'fupi_pla_user':
		return '';
	break;

	// E-COMMERCE TRACKING

	case 'fupi_pla_ecomm':

		if ( empty( $this->enable_woo_descr_text ) ) {
			return '<p>' . sprintf( esc_html__( 'To see WooCommere data in your Plausible reports you need to %1$sregister goal names and properties%2$s in your Plausible account.', 'full-picture-analytics-cookie-notice'), '<button type="button" class="fupi_faux_link fupi_open_popup" data-popup="fupi_setup_popup">', '</button>' ) . '</p>
				
				<p>' . esc_html__( 'Please note, that if you use hosted Plausible, every event sent to Plausible will count towards your pageview limit.', 'full-picture-analytics-cookie-notice' ) . '</p>

				<p class="fupi_woo_reqs_info">
					<strong>' . esc_html__( 'Attention.', 'full-picture-analytics-cookie-notice' ) . '</strong> ' . sprintf( esc_html__( 'WP Full Picture\'s automatic tracking is designed to work with %1$sstandard Woocommerce hooks and HTML%2$s. If your store doesn\'t use them or modifies them, tracking may not work.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to test it', 'full-picture-analytics-cookie-notice' ) . '</a>.
				</p>';
		} else {
			return $this->enable_woo_descr_text;
		} 

	break;

    default:
        return '';
    break;
};

?>