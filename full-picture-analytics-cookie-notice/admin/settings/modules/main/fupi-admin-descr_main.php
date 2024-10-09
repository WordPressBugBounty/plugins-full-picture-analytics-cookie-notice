<?php

switch( $a['id'] ){

	// DO NOT TRACK

	case 'fupi_main_no_track':

		return '<p>' . esc_html__( 'Here you can specify what users and user groups / roles you don\'t want to track. These settings will work on all the tracking tools installed with WP Full Picture\'s modules (except GTM), and tools managed by the "Tracking Tools Manager" module.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	// CHANGE DEFAULT SETTINGS

	case 'fupi_main_default':
		return '<p>' . esc_html__( 'These settings change how tracking tools integrated with WP Full Picture track data and what they can track.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// Traffic sources modifications

	case 'fupi_main_ref':
		return '<p>' . esc_html__( 'These options will help you improve accuracy of tracking tools installed on your website. It is compatible with tracking tools installed via WP Full Picture and other plugins and methods. The only exception are plugins that install tools using server-side tracking.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// OTHER SETTINGS

	case 'fupi_main_other':
		return '<p>' . esc_html__( 'These settings change various aspects of WP Full Picture.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// IMPORT / EXPORT

	case 'fupi_main_importexport':
		return '<p>' . esc_html__( 'Use these functions to make a backup of your WP Full Picture\'s settings or move them to a different site.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// OTHER SETTINGS

	case 'fupi_main_experim':
		return '<p>' . esc_html__( 'These settings are experimental. They will be introduced into core if no users report issues with them.' , 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

	// DEFAULT

	default:
		return '';
	break;
};

?>
