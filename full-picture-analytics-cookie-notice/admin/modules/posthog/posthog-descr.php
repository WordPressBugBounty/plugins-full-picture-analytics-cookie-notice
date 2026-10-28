<?php

$ret_text = '';

switch( $section_id ){

	// INSTALLATION

	case 'fupi_posthog_install':
		$ret_text = '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'PostHog' ) . '<br><span class="fupi_small">' . esc_html__( 'Please fill in the required fields below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/assets/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'PostHog is installed', 'full-picture-analytics-cookie-notice' ) . '</p>
		</div>';
	break;

	// LOADING

	case 'fupi_posthog_loading':
		$ret_text = '<p>' . esc_html__( 'Change when and where this tool loads. This is all optional.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;
};

?>
