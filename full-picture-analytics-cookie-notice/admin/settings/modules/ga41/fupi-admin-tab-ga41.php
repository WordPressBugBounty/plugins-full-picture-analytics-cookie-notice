<?php

// INSTALLATION

$basic_sect_fields = array(
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Measurement ID', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'id',
		'class'				=> 'fupi_required',
		'required'			=> true,
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[id]',
		'placeholder'		=> 'G-0000000000',
		'under field'		=> sprintf( esc_html__( '%1$sLearn where to find it%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-install-google-analytics-4/">', '</a>'),
	),
	array(
		'type' 				=> 'toggle',
		'label' 			=> esc_html__( 'Avoid issues when using multiple GAs on a single site', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'cookie_prefix',
		'option_arr_id'		=> $option_arr_id,
		'popup2'			=> '<p>' . esc_html__( 'Enable this function if you installed another GA tracking script with a different plugin, the Custom Scripts module or a Google Tag Manager.', 'full-picture-analytics-cookie-notice') . '</p>',
	),
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
			<p style="color: red">' . esc_html__( 'This setting is against GDPR and other laws in countries where users must consent to tracking. Use this option only for testing purposes.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p style="color: #e47d00">' . esc_html__( 'If you have a consent banner enabled, this will NOT start tracking visitors who did not agree to it.', 'full-picture-analytics-cookie-notice' ) . '</p>',
		),
	array(
		'type'	 			=> 'toggle',
		'label' 			=> esc_html__( 'Track without waiting for consent', 'full-picture-analytics-cookie-notice' ),
		'class'				=> 'fupi_sub fupi_load_opts',
		'must_have'			=> 'cook',
		'field_id' 			=> 'disreg_cookies',
		'option_arr_id'		=> $option_arr_id,
		'popup2'			=> '<p style="color: #e47d00;">' . esc_html__( 'This option should be only enabled for testing purposes. It is illegal in countries that require visitors to opt-in to tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'This option only applies when your consent banner is in the opt-in mode or one of automatic modes.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'When you enable this option, GA will disregard settings in your consent banner and load without waiting for user\'s consent.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'Visitors will still be able to turn off tracking by declining tracking / cookies.', 'full-picture-analytics-cookie-notice' ) . '</p>'
	),
	array(
		'type'	 			=> 'r3',
		'label' 			=> esc_html__('Only track visitors from specific countries', 'full-picture-analytics-cookie-notice'),
		'field_id' 			=> 'limit_country',
		'must_have'			=> 'pro geo',
		'option_arr_id'		=> $option_arr_id,
		'class'				=> 'fupi_sub fupi_load_opts',
		'is repeater'		=> false,
		'popup'				=> sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by comas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '<br>' . esc_html__('If visitor\'s country is not recognized GA will load normally.', 'full-picture-analytics-cookie-notice' ),
		'fields'			=> array(
			array(
				'type'				=> 'select',
				'field_id'			=> 'method',
				'options'			=> array(
					'excl'				=> esc_html__('All except','full-picture-analytics-cookie-notice'),
					'incl'				=> esc_html__('Only in','full-picture-analytics-cookie-notice'),
				),
				'wrap_class'		=> 'fupi_col_20',
			),
			array(
				'type'				=> 'text',
				'field_id'			=> 'countries',
				'placeholder'		=> 'e.g. GB, DE, FR, AU, etc.',
			),
		),
	),
);

// WP DATA FIELDS

$wpdata_fields = array(
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track page type', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'page_type',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_type"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[page_type]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track page IDs', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'page_id',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_id"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[page_id]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track the current page number of categories, tags, etc.', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'page_number',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_number"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[page_number]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track post and page publish dates', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'post_date',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "post_date"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[post_date]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'user_role',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "user_role"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[user_role]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track page language', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'page_lang',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_lang"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[page_lang]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track post\'s terms (categories, tags, etc.) ', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'tax_terms',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "taxonomy_terms"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[tax_terms]',
		'el_class'			=> 'fupi_condition',
		'el_data_target'	=> 'fupi_tax_terms_opts',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
		'popup'				=> '<p>' . esc_html__('This will track what categories, tags and formats are associated with posts and pages. You can enable tracking other terms and / or post types in the "General Settings" > "Default Tracking Settings".','full-picture-analytics-cookie-notice') . '</p>',
	),
		array(
			'type'	 			=> 'toggle',
			'label' 			=> esc_html__( 'Add taxonomy slug to term name', 'full-picture-analytics-cookie-notice' ),
			'field_id' 			=> 'add_tax_term_cat',
			'option_arr_id'		=> $option_arr_id,
			'class'				=> 'fupi_sub fupi_tax_terms_opts fupi_disabled',
			'popup'				=> '<p>' . esc_html__( 'Sometimes it can be difficult to say if a term, e.g. "european music" is a tag or a category. Enable this setting and this information will be sent to Google Analytics along with the term itself, e.g. "european music (tag)"', 'full-picture-analytics-cookie-notice') . '</p>',
		),
		array(
			'type'	 			=> 'toggle',
			'label' 			=> esc_html__( 'Send term names instead of term slugs', 'full-picture-analytics-cookie-notice' ),
			'field_id' 			=> 'send_tax_terms_titles',
			'option_arr_id'		=> $option_arr_id,
			'class'				=> 'fupi_sub fupi_tax_terms_opts fupi_disabled',
			'popup'				=> '<p>' . esc_html__( 'Enable this to send term names (e.g. product category) instead of their slugs (e.g. product_category). Enabling this feature is not recommended since term names can sometimes be changed while slugs are changed only on very rare occasions.', 'full-picture-analytics-cookie-notice') . '</p>',
		),
	array(
		'type'	 			=> 'toggle',
		'label' 			=> esc_html__( 'Track unmodified page titles', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'clean_page_title',
		'option_arr_id'		=> $option_arr_id,
		'el_class'			=> 'fupi_condition',
		'el_data_target'	=> 'fupi_seo_title',
		'popup'				=> '<p>' . esc_html__( 'By default, Google Analytics takes page titles from the "title" meta tag. This is not perfect since this tag can often change (e.g. when you tweak it with an SEO plugin). The result is that your Google Analytics can show you reports where one page can have multiple entries - under different titles.', 'full-picture-analytics-cookie-notice') . '</p><p>' . esc_html__( 'When you enable this option, WP Full Picture will send to Google Analytics the default title of your page as used on the page / post / product edit screen. This will make data analysis easier.', 'full-picture-analytics-cookie-notice') . '</p>',
	),
		array(
			'type'	 			=> 'text',
			'label' 			=> esc_html__( 'Track SEO titles (displayed in Google\'s search results)', 'full-picture-analytics-cookie-notice' ),
			'field_id' 			=> 'seo_title',
			'placeholder'		=> esc_html__( 'Parameter name, e.g. "seo_title"', 'full-picture-analytics-cookie-notice' ),
			'class'				=> 'fupi_sub fupi_seo_title fupi_disabled',
			'option_arr_id'		=> $option_arr_id,
			'label_for' 		=> $option_arr_id . '[seo_title]',
			'format'			=> 'key',
			'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
		),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track author\'s display names', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'post_author',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "post_author"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[post_author]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
		'popup2'			=> '<p style="color: #e47d00;">' . esc_html__( 'Tracking personally identifiable information is against Google\'s policy. Make sure that the displayed names are pseudonyms.', 'full-picture-analytics-cookie-notice') . '</p>',
	),
	array(
		'type'	 			=> 'text',
		'label' 			=> esc_html__( 'Track the number of search results', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'search_results_nr',
		'placeholder'		=> esc_html__( 'Parameter name, e.g. "search_results_nr"', 'full-picture-analytics-cookie-notice' ),
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[search_results_nr]',
		'format'			=> 'key',
		'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
	),
);

if ( isset( $this->main['show_author_id'] ) ){
	$auth_id_field = array(
		array(
			'type'	 			=> 'text',
			'label' 			=> esc_html__( 'Track authors IDs', 'full-picture-analytics-cookie-notice' ),
			'field_id' 			=> 'author_id',
			'placeholder'		=> esc_html__( 'Parameter name, e.g. "author_id"', 'full-picture-analytics-cookie-notice' ),
			'option_arr_id'		=> $option_arr_id,
			'label_for' 		=> $option_arr_id . '[author_id]',
			'format'			=> 'key',
			'under field'		=> esc_html__( 'Only lowercase letters, digits and underscores. Parameter name cannot start with a digit.', 'full-picture-analytics-cookie-notice' ),
		)
	);
	$wpdata_fields = array_merge( $wpdata_fields, $auth_id_field );
};

$custom_data_ids_fields = array(
	array(
		'type'	 			=> 'r3',
		'label' 			=> esc_html__( 'Track custom metadata', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'track_cf',
		'must_have'			=> 'pro trackmeta',
		'class'				=> 'fupi_metadata_tracker fupi_simple_r3',
		'option_arr_id'		=> $option_arr_id,
		'is_repeater'		=> true,
		'popup'				=> '<p>' . esc_html__( 'This setting lets you track custom metadata that was previously registerd in the Metadata Tracking page.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'To view data in your Google Analytics reports you also need to register custom dimensions in Google Analytics\' panel using the same event parameter names as you entered in the fields.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'Event parameter names must contain only lowercase letters, digits and underscores and cannot begin with a digit.', 'full-picture-analytics-cookie-notice' ) . '</p>',
		'fields'			=> array(
			array(
				'type'				=> 'custom_meta_select',
				'field_id'			=> 'id',
				'required'			=> true,
			),
			array(
				'type'				=> 'text',
				'placeholder'		=> esc_html__( 'parameter_name' , 'full-picture-analytics-cookie-notice' ),
				'field_id'			=> 'dimname',
				'required'			=> true,
				'format' 			=> 'key',
			),
		),
	)
);

$wpdata_fields = array_merge( $wpdata_fields, $custom_data_ids_fields );

// EVERYTHING TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_ga41_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => $basic_sect_fields,
	),

	// LOADING

	array(
		'section_id' => 'fupi_ga41_loading',
		'section_title' => esc_html__( 'Loading', 'full-picture-analytics-cookie-notice' ),
		'fields' => $loading_fields,
	),

	// Tracking events

	array(
		'section_id' => 'fupi_ga41_events',
		'section_title' => esc_html__( 'Tracking events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Identify logged-in users across devices and browsers', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'set_user_id',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'popup2'			=> '<p>' . esc_html__( 'When you enable this option WP Full Picture will start sending to Google Analytics IDs of logged-in users. These IDs will be used to recognize users when they log in to your site again on different devices or browsers.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . sprintf( esc_html__( 'You can learn more about it from %1$sGoogle\'s documentation%2$s.', 'full-picture-analytics-cookie-notice'), '<a href="https://support.google.com/analytics/answer/9213390?hl=en" target="_blank">', '</a>') . '</p>
					<p style="color:#e47d00">' . esc_html__( 'Attention! You must add to your privacy policy information on your use of this ID.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_email_tel',
				'option_arr_id'		=> $option_arr_id,
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track every link with a different event', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track every link with the same event but different parameter (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'popup'				=> '<p>' . esc_html__( 'It will track the last 5 digits of the phone number and the part of the email address before the "@" symbol.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track every link with a different event" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, every time someone clicks a contact link WP FP will send to GA a different event. Events will be named according to the format "tel_clicked_[last 5 digits]" and "email_clicked_[email part before @]".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you do not have many phone and email links on your website and/or you are not an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as one event with different parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, WP FP will send to your GA events "email_link_click" and "tel_link_click".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see information on what links were clicked, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$scontact_click%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you have many different contact links on the website and you are an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track clicks on affiliate links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_affil_method',
				'option_arr_id'		=> $option_arr_id,
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_affil_cond',
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track every link with a different event', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track every link with the same event but different parameter (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'popup'				=> '<p>' . esc_html__( 'Enable this function to track clicks on affiliate links.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track every link with a different event" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . sprintf( esc_html__( 'When you choose this option, every time someone clicks an affiliate link specified in the fields below, WP FP will send to GA an event with a name specific for this link. The event names must follow %1$sthese naming rules%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://support.google.com/analytics/answer/13316687?hl=en#zippy=%2Cweb" target="_blank">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you do not intend to set many event names and/or you are not an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as one event with different parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, every time someone clicks an affiliate link, WP FP will send to your GA event "affiliate_link_click".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'Information about the names of the links will be sent to GA as event parameters.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see these parameters / names of clicked links in your GA reports, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$saffiliate_link_click%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you have many different affiliate links on the website and you are an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
				array(
					'type'	 			=> 'r3',
					'label' 			=> esc_html__( 'Links', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_affiliate',
					'class'				=> 'fupi_simple_r3 fupi_sub fupi_affil_cond fupi_cond_val_evt fupi_cond_val_params fupi_disabled',
					'option_arr_id'		=> $option_arr_id,
					'is_repeater'		=> true,
					'btns_class'		=> 'fupi_push_right',
					'fields'			=> array(
						array(
							'placeholder'		=> esc_html__( 'Url part, e.g. /go/', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'sel',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
						array(
							'placeholder'		=> esc_html__( 'Event name or link name', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'val',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
					),
					'popup'				=> '<p>' . esc_html__( 'If you chose an option to track clicks with different parameters, then in the second field you can also use a placeholder [name]. It will be replaced with the first 20 characters of the text inside the clicked element. Make sure it has any.', 'full-picture-analytics-cookie-notice' ) . '</p>',
				),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track clicks on page elements', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_elems_method',
				'option_arr_id'		=> $option_arr_id,
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_elems_cond',
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track every element with a different event', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track every element with the same event but different parameter (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'popup'				=> '<p>' . esc_html__( 'Enable this function to track clicks on page elements.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track every element with a different event" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . sprintf( esc_html__( 'When you choose this option, every time someone clicks a page element specified in the fields below, WP FP will send to GA an event with a name specific for this element. The event names must follow %1$sthese naming rules%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://support.google.com/analytics/answer/13316687?hl=en#zippy=%2Cweb" target="_blank">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you do not intend to set many event names and/or you are not an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as one event with different parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, every time someone clicks an element, WP FP will send to your GA event "element_click".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'Information about the names of the clicked elements will be sent to GA as event parameters.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see these parameters / names in your GA reports, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$selement_click%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you want to track clicks on many different elements on the website and you are an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
				array(
					'type'	 			=> 'r3',
					'label' 			=> esc_html__( 'Page elements', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_elems',
					'option_arr_id'		=> $option_arr_id,
					'is_repeater'		=> true,
					'class'				=> 'fupi_simple_r3 fupi_sub fupi_elems_cond fupi_cond_val_evt fupi_cond_val_params fupi_disabled',
					'btns_class'		=> 'fupi_push_right',
					'popup2'			=> '<h3>' . esc_html__( 'How to fill in these fields', 'full-picture-analytics-cookie-notice' ) . '</h3>
						<ol>
							<li>' . esc_html__( 'You can enter more then 1 selector in a "CSS selector" field, e.g. .button, .different-button, .another-button.', 'full-picture-analytics-cookie-notice' ) . '</li>
							<li>' . esc_html__( 'If multiple selectors point at the same element only the first match will be tracked.', 'full-picture-analytics-cookie-notice' ) . '</li>
							<li>' . esc_html__( 'If you are tracking events with a single event name but different parameters, you can add in the "name" field a placeholder [name]. It will be replaced with the first 20 characters of the text inside the clicked element. Make sure it has any.', 'full-picture-analytics-cookie-notice' ) . '</li>
						</ol>
						<h3>' . esc_html__( 'Attention!', 'full-picture-analytics-cookie-notice') . '</h3>
						<p style="color: #e47d00;">' . esc_html__( 'To correctly track clicks in page elements OTHER than links (e.g. buttons), you need to provide CSS selectors of ALL clickable elements inside that element.', 'full-picture-analytics-cookie-notice' ) . '</p>
						<p>' . esc_html__( 'The easiest way to do it is to use the asterisk symbol "*". For example, to track clicks in buttons provide:', 'full-picture-analytics-cookie-notice' ) . ' <code>.my_button, .my_button *</code>.</p>
						<p><a href="https://wpfullpicture.com/support/documentation/how-to-track-clicks-in-page-page-elements/" target="_blank">' . esc_html__( 'Learn more about tracking clicks', 'full-picture-analytics-cookie-notice' ) . '</a></p>',
					'fields'			=> array(
						array(
							'placeholder'		=> esc_html__( 'CSS selector e.g. #sth img', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'sel',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
						array(
							'placeholder'		=> esc_html__( 'Event name or element name', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'val',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
					)
				),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track form submissions', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_forms_method',
				'option_arr_id'		=> $option_arr_id,
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_forms_cond',
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track every form with a different event', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track every form with the same event but different parameter (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'popup2_id'			=> 'fupi_track_forms_popup',
			),
				array(
					'type'	 			=> 'r3',
					'label' 			=> esc_html__( 'Forms', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_forms',
					'option_arr_id'		=> $option_arr_id,
					'is_repeater'		=> true,
					'class'				=> 'fupi_simple_r3 fupi_sub fupi_forms_cond fupi_cond_val_evt fupi_cond_val_params fupi_disabled',
					'btns_class'		=> 'fupi_push_right',
					'fields'			=> array(
						array(
							'placeholder'		=> esc_html__( 'CSS selector e.g. #form_id', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'sel',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
						array(
							'placeholder'		=> esc_html__('Event name or form name','full-picture-analytics-cookie-notice'),
							'type'				=> 'text',
							'field_id'			=> 'val',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
					)
				),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track when visitors see specific page elements', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_views_method',
				'option_arr_id'		=> $option_arr_id,
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_elemview_cond',
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track every element with a different event', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track every element with the same event but different parameter (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'popup2'			=> '<p>' . esc_html__( 'Enable this function to track when specific page elements are visible to the visitor.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'Elements are treated as "visible" when they are 200px inside the screen (you can change it in the General Settings). Each view is counted once per page view.', 'full-picture-analytics-cookie-notice') . '</p>
					<h3>' . esc_html__( '"Track every element with a different event" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . sprintf( esc_html__( 'When you choose this option, every time someone sees a page element you specify, WP FP will send to GA an event with a name for this element. The event names must follow %1$sthese naming rules%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://support.google.com/analytics/answer/13316687?hl=en#zippy=%2Cweb" target="_blank">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you do not intend to set many event names and/or you are not an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as one event with different parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, every time someone sees a page elements, WP FP will send to your GA event "element_view".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'Information about the names of the seen elements will be sent to GA as event parameters.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see these parameters / names in your GA reports, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$sviewed_element%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you want to track clicks on many different elements on the website and you are an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( 'Other information', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p style="color:#e47d00">' . esc_html__( 'This tracks only elements which are present in the HTML at the moment of rendering the page. To track elements added later, enable the "DOM listener" function in the General Settings.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
				array(
					'type'	 			=> 'r3',
					'label' 			=> esc_html__( 'Viewed elements', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_views',
					'option_arr_id'		=> $option_arr_id,
					'is_repeater'		=> true,
					'class'				=> 'fupi_simple_r3 fupi_sub fupi_elemview_cond fupi_cond_val_evt fupi_cond_val_params fupi_disabled',
					'btns_class'		=> 'fupi_push_right',
					'fields'			=> array(
						array(
							'placeholder'		=> esc_html__( 'CSS selector e.g. .side img', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'sel',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
						array(
							'placeholder'		=> esc_html__( 'Event name or element name (required)', 'full-picture-analytics-cookie-notice' ),
							'type'				=> 'text',
							'field_id'			=> 'val',
							'wrap_class'		=> 'fupi_col_35_grow',
							'required'			=> true,
						),
					)
				),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track scroll depths', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_scroll_method',
				'option_arr_id'		=> $option_arr_id,
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_scroll_cond',
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track every depth level with a different event', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track every depth level with one event with parameters', 'full-picture-analytics-cookie-notice'),
				),
				'popup'				=> '<p>' . esc_html__( 'Enable this function to track how deep people scroll your pages.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track every depth level with a different event" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, when a depth level is reached, WP FP will send to GA an event "scrolled_[depth]", e.g. scrolled_50 ', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you do not intend to set many event names and/or you are not an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as one event with different parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, every time someone reaches a specified depthd, WP FP will send to your GA event "scroll".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'Information about the depth will be sent to GA as event parameters.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see these parameters in your GA reports, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$spercent_scrolled%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '</p>
					<p>' . esc_html__( 'This option is recommended if you want to track clicks on many different elements on the website and you are an advanced GA user.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track when visitors scroll to:', 'full-picture-analytics-cookie-notice' ),
					'placeholder'		=> esc_html__( 'e.g. 25, 50, 75', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_scroll',
					'class'				=> 'fupi_sub fupi_scroll_cond fupi_cond_val_evt fupi_cond_val_params fupi_disabled',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_scroll]',
					'after field'		=> esc_html__( '% of page height', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track how long the user was actively engaged with the content', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_engagement',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_engagement]',
				'popup2'			=> '<p style="color: #e47d00">' . sprintf ( esc_html__( 'To have the timer data available in GA, you need to %3$sregister a custom metric in GA%4$s with event parameter %1$suser_engagement_time%2$s. Unit of measurement: seconds.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '
					<p>' . esc_html__ ( 'This feature lets you measure how much time users actively spend on your website (scrolling, reading, etc.).', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__ ( 'This does not, however, use Google Analytics\' method of tracking engagement time. The method used here is more precise but it requires the use of Calculated Metrics feature of GA, which requires certain knowledge and experience.', 'full-picture-analytics-cookie-notice') . '</p>
					<h3>' . esc_html__ ( 'How does it work?', 'full-picture-analytics-cookie-notice') . '</h3>
					<p>' . esc_html__ ( 'Time of engagement starts running when a user focuses a tab with page’s content and pauses whenever a tab loses focus. In other words the timer doesn’t run if a user is not looking at the content.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__ ( 'When a user stops scrolling or moving a mouse, a 15 second countdown starts. If during this time the user doesn’t move the mouse or scroll the window, the timer is paused. It resumes counting the time when the user makes an action.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__ ( 'The time info is sent to GA before the user closes his browser or changes a browser tab.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__ ( 'When a user returns to the page, the timer is cleared and starts counting from zero. This is important for the calculations - as described below.', 'full-picture-analytics-cookie-notice') . '</p>
					<h3>' . esc_html__ ( 'How to get the data', 'full-picture-analytics-cookie-notice') . '</h3>
					<p>' . esc_html__ ( 'All the timer events that WP FP sends to Google Analytics contain information about how long the user was engaged with the content since the last sent event. In other words, if a visitors changes tabs several times during one pageview, WP FP will send several timer events.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__ ( 'This means, that in order to learn how much time the user was engaged with the content, you need to add all these numbers together.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__ ( 'This can be done using Google\'s Calculated Metrics feature. Here\'s a guide with an explainer video showing how to do it. Please mind, that this is for advanced GA users.', 'full-picture-analytics-cookie-notice') . ' <a href="https://www.lovesdata.com/blog/calculated-metrics">' . esc_html__( 'Go to the tutorial', 'full-picture-analytics-cookie-notice' ) . '</a></p>',
			),
		),
	),

	// WP DATA TRACKING

	array(
		'section_id' => 'fupi_ga41_wpdata',
		'section_title' => esc_html__( 'Tracking event parameters', 'full-picture-analytics-cookie-notice' ),
		'fields' => $wpdata_fields,
	),
);

$adv_triggers_section = array(
	array(
		'section_id' => 'fupi_ga41_atrig',
		'section_title' => esc_html__( 'Tracking lead scores and custom events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__( 'Track when specific conditions are met', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'custom_events',
				'class'				=> 'fupi_events_builder fupi_fullwidth_tr',
				'must_have'			=> 'pro atrig',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'label'				=> esc_html__( 'When this happens', 'full-picture-analytics-cookie-notice' ),
						'type' 				=> 'atrig_select',
						'field_id'			=> 'atrig_id',
						'wrap_class'		=> 'fupi_col_30',
						'required'			=> true,
						'format'			=> 'key'
					),
					array(
						'type'	 			=> 'select',
						'label' 			=> esc_html__( '...for...', 'full-picture-analytics-cookie-notice' ),
						'field_id' 			=> 'repeat',
						'option_arr_id'		=> $option_arr_id,
						'wrap_class'		=> 'fupi_col_15',
						'options'			=> array(
							'no'				=> esc_html__( 'The first time', 'full-picture-analytics-cookie-notice' ),
							'yes'				=> esc_html__( 'Every time', 'full-picture-analytics-cookie-notice' ),
						),
					),
					array(
						'type'				=> 'text',
						'label'				=> esc_html__( 'Send to GA event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'event_name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_events_builder_evt',
						'required'			=> true,
						'wrap_class'		=> 'fupi_col_20',
					),
					array(
						'type'				=> 'number',
						'label'				=> esc_html__( 'Value (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_val',
						'required'			=> true,
						'wrap_class'		=> 'fupi_col_20',
					),
				),
			)
		),
	),
);

$sections = array_merge( $sections, $adv_triggers_section );

// OTHER TRACKING SETTINGS

$other_section = array(
	array(
		'section_id' => 'fupi_ga41_other',
		'section_title' => esc_html__( 'Other', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track JavaScript errors', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'js_err_dimens',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'popup2'				=> '<p>' .  esc_html__( 'This will send to Google Analytics descriptions of JavaScript errors on your site. Use it with caution! If your site has many errors, the number of events may exceed Google Analytics\' limit. Events descriptions are limited to 100 characters (Google\'s limit).', 'full-picture-analytics-cookie-notice') . '</p><p>' . sprintf ( esc_html__( 'To see this data in reports you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$sjs_error_details%2$s.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Enable DebugView (for all visitors)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'debug_ga',
				'option_arr_id'		=> $option_arr_id,
				'popup'				=> '<p>' . esc_html__( 'DebugView is specific to Google Analytics 4 and is used for debugging purposes. We recommend you use it together with WP Full Picture\'s debug mode (enable it on the "General settings" page).', 'full-picture-analytics-cookie-notice') . '</p>
				<p>' . esc_html__( 'You can find debug data in your GA4 dashboard > Configure > Debug View.', 'full-picture-analytics-cookie-notice') . '</p>
				<p>' . esc_html__( 'Attention! There are 2 methods to enable DebugView - with this option (it will enable it for all visitors who are NOT excluded from tracking) and with a parameter "?ga4_debug=on" added to this site\'s URL (this will enable it only for the user who used the parameter).', 'full-picture-analytics-cookie-notice') . '</p>
				<p>' . esc_html__( 'If you choose to use the URL parameter option and want to disable the DebugView, simply add "?ga4_debug=off" to your site\'s URL.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		),
	),
);

$sections = array_merge( $sections, $other_section );

	$woo_section = array(
		array(
			'section_id' => 'fupi_ga41_ecomm',
			'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
			'class' => 'any_class_will_appear'
		)
	);

	$sections = array_merge( $sections, $woo_section );

?>
