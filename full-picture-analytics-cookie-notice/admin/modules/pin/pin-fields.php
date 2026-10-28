<?php

$option_arr_id = 'fupi_pin';

// ALL TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_pin_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Pinterest Tag ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'under_field'		=> '<p>' . esc_html__( 'To get Pinterest Tag ID go to your Pinterest dashboard > "Ads" > "Conversions" > Pinterest Tag is in the table in the center of the screen.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		),
	),

	// LOADING

	array(
		'section_id' => 'fupi_pin_loading',
		'section_title' => esc_html__( 'Loading', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Force load', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'force_load',
				'class'				=> 'fupi_deprecated',
				'el_class'			=> 'fupi_condition fupi_condition_reverse',
				'el_data_target'	=> 'fupi_load_opts',
				'option_arr_id'		=> $option_arr_id,
				'after_field'		=> esc_html__( 'Replaced with the test mode in the Setup Helper', 'full-picture-analytics-cookie-notice' ),
				'offscreen_important'			=> '<p style="color: red">' . esc_html__( 'Use only for installation verification or testing. It breaks GDPR and similar laws.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p>' . sprintf( esc_html__( 'This will load the tracking script for administrators, bots, excluded users, people browsing from excluded locations and people who didn\'t agree to tracking. %1$sLearn more%2$s.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/validation-mode/">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__('Only track visitors from specific countries', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'limit_country',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_load_opts',
				'must_have'			=> 'pro geo',
				'related_opts'		=> ['main', 'geo, ipdata_api_key, cf_worker_url, remember_geo'],
				'is_repeater'		=> false,
				'offscreen'				=> sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by commas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '<br><br>' . esc_html__('If visitor\'s country is not recognized, Pinterest tag will load normally. Location is checked using the method chosen in the Geolocation settings.', 'full-picture-analytics-cookie-notice' ),
				'fields'			=> array(
					array(
						'type'				=> 'select',
						'field_id'			=> 'method',
						'options'			=> array(
							'excl'				=> esc_html__('All except', 'full-picture-analytics-cookie-notice'),
							'incl'				=> esc_html__('Only in', 'full-picture-analytics-cookie-notice'),
						),
						'class'		=> 'fupi_col_20',
					),
					array(
						'type'				=> 'text',
						'field_id'			=> 'countries',
						'placeholder'		=> esc_html__('e.g. GB, DE, FR, AU, etc.', 'full-picture-analytics-cookie-notice'),
					),
				),
			),
		),
	),

	// DATA COLLECTION

	array(
		'section_id' => 'fupi_pin_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Enable Enhanced Match for improved conversion tracking', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_user_emails',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' .esc_html__( 'When this settings is enabled, WP Full Picture will send to Pinterest encrypted email addresses of your visitors (when they browse the site while being logged in or when they make a purchase).', 'full-picture-analytics-cookie-notice' ) . '</p>',
			)
		),
	),

	// TRACKING

	array(
		'section_id' => 'fupi_pin_track',
		'section_title' => esc_html__( 'Tracking events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track search', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_search',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> esc_html__( 'Tracks phrases that visitors search on your site (works with standard WP search and WooCommerce product search).', 'full-picture-analytics-cookie-notice')
			),
		),
	),

	// WOOCOMMERCE

	array(
		'section_id' => 'fupi_pin_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
	),
);

?>
