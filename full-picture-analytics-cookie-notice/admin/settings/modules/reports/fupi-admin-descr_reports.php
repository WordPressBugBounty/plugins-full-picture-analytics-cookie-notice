<?php

switch( $a['id'] ){

	// INSTALLATION

	case 'fupi_reports_main':
		return '<p>' . esc_html__('Use this module to display in your WP admin reports created in Google Looker Studio, Databox or similar platforms. The page "Reports" will show up in the menu when at least one report is added on this page or with the Plausible Analytics module.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

    default:
        return '';
    break;
};

?>
