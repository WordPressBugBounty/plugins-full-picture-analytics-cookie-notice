<?php

$ret_text = '';

switch( $section_id ){

	// DEFAULT

	case 'fupi_woo_main':
		$ret_text = '<p>' . esc_html__('These are global settings which apply to all tools that support WooCommerce Tracking module. Visit their settings pages for tool-specific settings.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	case 'fupi_woo_adv':
		$ret_text = '<p>' . esc_html__('With Advanced Order Tracking (AOT) you can track more orders, more accurately (when they get a specific order status), track refunds and cancellations. At the moment AOT is only available in Meta Pixel and Google Analytics.', 'full-picture-analytics-cookie-notice' ) . ' <button type="button" class="fupi_open_popup button button-secondary" data-popup="fupi_adv_tracking_popup">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice' ) . '</button></p>';
	break;

	default:
		$ret_text = '<p>' . esc_html__('Here you can adjust tracking to work with your individual store setup. You may need it if your store uses custom WooCommerce solutions.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;
};

?>
