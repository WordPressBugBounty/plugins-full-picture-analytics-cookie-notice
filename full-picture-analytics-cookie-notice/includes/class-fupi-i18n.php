<?php

class Fupi_i18n {
	
	public function fupi_load_plugin_textdomain() {
		load_plugin_textdomain(
			'full-picture-analytics-cookie-notice',
			false,
			FUPI_PATH . '/languages/'
		);
	}

	public function fupi_load_textdomain_mofile( $mofile, $domain ){
		if ( 'full-picture-analytics-cookie-notice' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
			$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
			$mofile = FUPI_PATH . '/languages/' . $domain . '-' . $locale . '.mo';
		}
		return $mofile;
	}
}
