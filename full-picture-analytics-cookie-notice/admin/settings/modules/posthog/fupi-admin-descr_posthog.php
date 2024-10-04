<?php

switch( $a['id'] ){

	// INSTALLATION

	case 'fupi_posthog_install':

		$module_data = get_option('fupi_posthog');
		$posthog_api_key = ! empty ( $module_data ) && ! empty ( $module_data['api_key'] ) ? esc_attr( $module_data['api_key'] ) : '';

		return '
		<div id="fupi_not_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/almost_ico.png" aria-hidden="true"> <p>' . sprintf( esc_html__( '%1$s is not installed', 'full-picture-analytics-cookie-notice' ), 'PostHog' ) . '<br><span class="fupi_small">' . esc_html__( 'To install it, please fill in the required field below', 'full-picture-analytics-cookie-notice' ) . '</span>.</p>
		</div>
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Well done! PostHog is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . esc_html__( 'The data is sent to a project with API key ', 'full-picture-analytics-cookie-notice' ) . $posthog_api_key . '</span>.</p>
		</div>';
	break;

	// LOADING

	case 'fupi_posthog_loading':
		return '<p>' . esc_html__( 'If you have consent banner enabled in the opt-in or one of automatic modes, PostHog will start working after visitors consent to using their data for statistics.', 'full-picture-analytics-cookie-notice') . '</p>';
	break;

	case 'fupi_posthog_other':
		return '';
	break;

    default:
        return '';
    break;
};

?>
