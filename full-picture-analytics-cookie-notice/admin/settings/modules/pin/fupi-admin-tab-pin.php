<?php

$basic_fields = array(
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Pinterest Tag ID', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'id',
		'class'				=> 'fupi_required',
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[id]',
		'under field'		=> '<p>' . esc_html__( 'To get Pinterest Tag ID go to your Pinterest dashboard > "Ads" > "Conversions" > Pinterest Tag is in the table in the center of the screen.', 'full-picture-analytics-cookie-notice') . '</p>',
	),
	array(
		'type'	 			=> 'toggle',
		'label' 			=> esc_html__( 'Enable Enhanced Match for improved conversion tracking', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'track_user_emails',
		'must_have'			=> 'pro',
		'option_arr_id'		=> $option_arr_id,
		'popup3'			=> '<p>' .esc_html__( 'When this settings is enabled, WP Full Picture will send to Pinterest encrypted email addresses of your visitors (when they browse the site while being logged in or when they make a purchase).', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p style="color: red;">' .esc_html__( 'Depending on the privacy laws in the countries where your visitors live (NOT where you are from) you may have to disclose this information in your privacy policy.', 'full-picture-analytics-cookie-notice' ) . '</p>',
	)
);

// LOADING

$loading_fields = array(
	array(
		'type'	 			=> 'toggle',
		'label' 			=> esc_html__( 'Force load', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'force_load',
		'el_class'			=> 'fupi_condition fupi_condition_reverse',
		'el_data_target'	=> 'fupi_load_opts',
		'option_arr_id'		=> $option_arr_id,
		'popup3'			=> '<p>' . sprintf( esc_html__( 'Load tracking script and start tracking all visitors - even administrators, bots, excluded users, people browsing from excluded locations and people that didn\'t agree to tracking. %1$sLearn more%2$s.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/validation-mode/?utm_source=fp_admin&utm_medium=referral&utm_campaign=settings_link">', '</a>' ) . '</p>
			<p style="color: red">' . esc_html__( 'This setting is against GDPR and other laws in countries where users must consent to tracking. Use this option only for testing purposes.', 'full-picture-analytics-cookie-notice' ) . '</p>',
	)
);

$loading_fields = array_merge( $loading_fields, array(
	array(
		'type'	 			=> 'toggle',
		'label' 			=> esc_html__( 'Track without waiting for consent', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'disreg_cookies',
		'class'				=> 'fupi_sub fupi_load_opts',
		'must_have'			=> 'cook',
		'option_arr_id'		=> $option_arr_id,
		'popup2'				=> '<p style="color: #e47d00;">' . esc_html__( 'This option should be only enabled for testing purposes. It is illegal in countries that require visitors to opt-in to tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>
		<p>' . esc_html__( 'When you enable this option, this tracking tool will start tracking without waiting for consent - even in countries where visitors may be tracked only after they agree to tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>
		<p>' . esc_html__( 'Visitors will still be able to turn off tracking by declining tracking / cookies.', 'full-picture-analytics-cookie-notice' ) . '</p>'
	),
) );


$loading_fields = array_merge( $loading_fields, array(
	array(
		'type'	 			=> 'r3',
		'label' 			=> esc_html__('Only track visitors from specific countries', 'full-picture-analytics-cookie-notice'),
		'field_id' 			=> 'limit_country',
		'option_arr_id'		=> $option_arr_id,
		'class'				=> 'fupi_sub fupi_load_opts',
		'must_have'			=> 'pro geo',
		'is_repeater'		=> false,
		'popup'				=> sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by comas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '<br><br>' . esc_html__('If visitor\'s country is not recognized, Pinterest tag will load normally. Location is checked using the method chosen in the Geolocation settings.', 'full-picture-analytics-cookie-notice' ),
		'fields'			=> array(
			array(
				'type'				=> 'select',
				'field_id'			=> 'method',
				'options'			=> array(
					'excl'				=> esc_html__('All except', 'full-picture-analytics-cookie-notice'),
					'incl'				=> esc_html__('Only in', 'full-picture-analytics-cookie-notice'),
				),
				'wrap_class'		=> 'fupi_col_20',
			),
			array(
				'type'				=> 'text',
				'field_id'			=> 'countries',
				'placeholder'		=> esc_html__('e.g. GB, DE, FR, AU, etc.', 'full-picture-analytics-cookie-notice'),
			),
		),
	),
) );

// TRACKING FIELDS

$tracking_fields = array(
	array(
		'type'	 			=> 'toggle',
		'label' 			=> esc_html__( 'Track search', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'track_search',
		'option_arr_id'		=> $option_arr_id,
		'popup'				=> esc_html__( 'Tracks phrases that visitors search on your site (works with standard WP search and WooCommerce product search).', 'full-picture-analytics-cookie-notice')
	),
);

// ALL TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_pin_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => $basic_fields,
	),

	// INSTALLATION

	array(
		'section_id' => 'fupi_pin_loading',
		'section_title' => esc_html__( 'Loading', 'full-picture-analytics-cookie-notice' ),
		'fields' => $loading_fields,
	),

	// TRACKING

	array(
		'section_id' => 'fupi_pin_track',
		'section_title' => esc_html__( 'Tracking events', 'full-picture-analytics-cookie-notice' ),
		'fields' => $tracking_fields,
	),
);

// WOOCOMMERCE

$woo_section = array(
	array(
		'section_id' => 'fupi_pin_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
	),
);

$sections = array_merge( $sections, $woo_section );

?>
