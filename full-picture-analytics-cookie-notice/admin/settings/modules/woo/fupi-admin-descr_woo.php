<?php

switch( $a['id'] ){

	// DEFAULT

	default:
		return '<p>' . esc_html__('These are global settings which apply to all tools that support WooCommerce Tracking module. Visit their settings pages for tool-specific settings.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;
};

?>
