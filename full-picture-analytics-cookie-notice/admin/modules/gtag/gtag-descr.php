<?php

$ret_text = '';

switch( $section_id ){
	case 'fupi_gtag_general':
		$ret_text = '<p>' . esc_html__( 'These settings apply to Google Analytics and Google Ads.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;
};

?>
