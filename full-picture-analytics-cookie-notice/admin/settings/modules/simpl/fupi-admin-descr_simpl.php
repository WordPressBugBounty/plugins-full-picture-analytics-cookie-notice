<?php

switch( $a['id'] ){

	// INSTALLATION

	case 'fupi_simpl_install':

		return '
		<div id="fupi_installed_info" class="fupi_installation_status fupi_hidden">
			<img src="' . FUPI_URL . 'admin/settings/img/success_ico.png" aria-hidden="true"> <p>' . esc_html__( 'Simple Analytics is installed', 'full-picture-analytics-cookie-notice' ) . '<br><span class="fupi_small">' . esc_html__( 'All settings below are optional.', 'full-picture-analytics-cookie-notice' ) . '</span></p>
		</div>';

	break;

    default:
        return '';
    break;
};

?>