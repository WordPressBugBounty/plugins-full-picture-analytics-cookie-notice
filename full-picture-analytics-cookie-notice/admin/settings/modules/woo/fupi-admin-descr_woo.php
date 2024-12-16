<?php

switch( $a['id'] ){

	// DEFAULT

	case 'fupi_woo_main':
		return '<p>' . esc_html__('These are global settings which apply to all tools that support WooCommerce Tracking module. Visit their settings pages for tool-specific settings.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	case 'fupi_woo_adv':
		return '<p>' . esc_html__('At the moment advanced order tracking is available only in Meta and Google Analytics. It improves the number of tracked orders by up to 30%, and tracks order refunds and cancellations (in Google Analytics).', 'full-picture-analytics-cookie-notice' ) . ' <button type="button" class="fupi_open_popup button button-secondary" data-popup="fupi_adv_tracking_popup">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice' ) . '</button></p>';
	break;
};

?>
