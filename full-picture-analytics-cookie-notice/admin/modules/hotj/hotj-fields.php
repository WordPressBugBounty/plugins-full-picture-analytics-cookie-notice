<?php

$option_arr_id = 'fupi_hotj';

// ALL TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_hotj_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Site ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'under_field'		=> '<p>' . sprintf( esc_html__( '%1$sHow to install Hotjar step-by-step%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-install-hotjar/">', '</a>' ) . '</p>
					<p>' . esc_html__( 'Tip. After you save the ID, enable "Force load" (see "Loading" section) to pass validation. Disable it after your site is validated.', 'full-picture-analytics-cookie-notice') . '</p>'
			),
		),
	),

	// INSTALLATION

	array(
		'section_id' => 'fupi_hotj_loading',
		'section_title' => esc_html__( 'Loading', 'full-picture-analytics-cookie-notice' ),
		'fields' => 		array(
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
				'label' 			=> esc_html__('Use only in specific countries', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'limit_country',
				'must_have'			=> 'pro geo',
				'related_opts'		=> ['main', 'geo, ipdata_api_key, cf_worker_url, remember_geo'],
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_load_opts',
				'is_repeater'		=> false,
				'offscreen'				=> '<p>' . sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by commas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '</p><p>'. esc_html__('Location is checked using the method chosen in the settings of the Geolocation module.', 'full-picture-analytics-cookie-notice' ) . '</p>',
				'fields'			=> array(
					array(
						'type'				=> 'select',
						'field_id'			=> 'method',
						'options'			=> array(
							'excl'				=> esc_html__('All except','full-picture-analytics-cookie-notice'),
							'incl'				=> esc_html__('Only in','full-picture-analytics-cookie-notice'),
						),
						'class'		=> 'fupi_col_20',
					),
					array(
						'type'				=> 'text',
						'field_id'			=> 'countries',
						'placeholder'		=> 'e.g. GB, DE, FR, AU, etc.',
					),
				),
			),
		),
	),

	// DATA COLLECTION

	array(
		'section_id' => 'fupi_hotj_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Data supression', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'data_suppression',
				'class'				=> 'fupi_adv',
				'el_class'			=> 'fupi_condition fupi_condition_reverse',
				'el_data_target'	=> 'fupi_hotj_priv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen_important'			=> '
				<h3 class="fupi_warning_text">' . esc_html__( 'Read it carefully', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'By enabling data supression, you confirm that:', 'full-picture-analytics-cookie-notice' ) . '</p>
					<ol>
						<li>' . esc_html__( 'You understand, that Hotjar will load and start tracking website visitors without asking for consent,', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'You understand, that by using this function, WP FP will disable the user identification feature and stop sending WooCommerce order IDs to Hotjar,', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . sprintf( esc_html__( 'You confirm, that you disabled tracking personally identifiable information in Hotjar\'s dashboard %1$sLearn how%2$s,', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-use-hotjar-without-asking-for-consent/" target="_blank">', '</a>' ) . '</li>
						<li>' . esc_html__( 'You understand, that enabling this function does not stop Hotjar from storing tracking cookies on the devices of your visitors,', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'You consulted with a legal professional, who confirmed that employing these limitations are enough to use Hotjar without asking visitors for consent,', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'In the "Loading" tab you chose countries, in which Hotjar can safely load without breaking the privacy laws.', 'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Enable user identification', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'identif_users',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_hotj_priv fupi_adv',
				'offscreen_warning'			=> '<p>' . esc_html__( 'With user identification you will be able to target specific users with polls, widgets and easily search their session recordings in the Hotjar panel.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__( 'Identification is done with User ID (for logged-in users) or Hotjar ID (for other visitors)', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__( 'To be able to identify users you need to:', 'full-picture-analytics-cookie-notice') . '</p>
					<ol>
						<li class="fupi_warning_text">' . esc_html__( 'If your visitors come from a country where they have to consent before they are tracked, then you should use consent banner in optin or an automatic mode and add to your privacy policy information about sending user information to Hotjar.', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . esc_html__( 'Visit', 'full-picture-analytics-cookie-notice') . ' <a href="https://insights.hotjar.com/settings/user-attributes">' . esc_html__( 'this page', 'full-picture-analytics-cookie-notice') . '</a> ' . esc_html__( 'in your Hotjar dashboard.', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . esc_html__( 'Select a website where you want to start tracking users (Not available on all plans!)', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . esc_html__( 'Return to this settings page', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . esc_html__( 'Set up options in the "User attributes" section', 'full-picture-analytics-cookie-notice') . '</li>
					</ol>',
			),
				array(
					'type'	 			=> 'multi_checkbox',
					'label' 			=> esc_html__( 'Associate users with:', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'user_attr',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_sub fupi_hotj_priv fupi_adv',
					'must_have'			=> 'pro',
					'options' 			=> array(
						'role' => esc_html__( 'User role', 'full-picture-analytics-cookie-notice' ),
						'email'	=> esc_html__( 'User email', 'full-picture-analytics-cookie-notice' ),
					),
				),
		),
	),
	
	array(
		'section_id' => 'fupi_hotj_data',
		'section_title' => esc_html__( 'Data tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_user_role',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page type', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_pagetype',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'			=> ['track', 'page_labels'],
				'under_field'		=> esc_html__( 'For example: Home, Blog, Category, etc.', 'full-picture-analytics-cookie-notice'),
			),

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page author', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_pageauthor',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track UTM campaign parameters', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_utm',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'offscreen'				=> esc_html__( 'Attention! If you conduct many advertising campaigns this may greatly increase the number of events associated with your site!', 'full-picture-analytics-cookie-notice'),
			),
		),
	),

	// SESSION TAGGING

	array(
		'section_id' => 'fupi_hotj_tags',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on outbound links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_outbound',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'			=> ['track', 'link_click_delay'],
			),

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_email_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'Due to privacy reasons, this function tracks the last 5 digits of the phone number and the part of the email address before the "@" symbol.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on file download links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_downl',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_downl]',
				'related_opts'			=> ['track', 'downl_file_formats'],
			),

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks in anchor links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_anchor_clicks',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),

			// CLICKS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'click_triggers',
				'label' 			=> esc_html__( 'Track clicks on page elements', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_clicks',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'related_opts'			=> ['track', 'link_click_delay'],
				'fields'			=> array(
					array(
						'type' 				=> 'hidden',
						'field_id'			=> 'atrig_id',
						'required'			=> true,
					),
					array(
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'When someone clicks', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
				),
			),

			// AFFILIATE CLICKS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'aff_click_triggers',
				'label' 			=> esc_html__( 'Track clicks on affiliate links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_aff',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'related_opts'			=> ['track', 'link_click_delay'],
				'fields'			=> array(
					array(
						'type' 				=> 'hidden',
						'field_id'			=> 'atrig_id',
						'required'			=> true,
					),
					array(
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'When someone clicks', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
				),
			),

			// FORM SUBMISSIONS
			
			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'form_triggers',
				'label' 			=> esc_html__( 'Track form submissions', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_form_submits',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'related_opts'		=> ['track', 'formsubm_trackdelay'],
				'under_field'		=> '<p>' . sprintf( esc_html__( 'There are %1$sfour methods of tracking forms%2$s. Please use the one best suited to your form. ' , 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/how-to-choose-the-best-way-to-track-form-submissions/">', '</a>' ) . '</p>',
				'fields'			=> array(
					array(
						'type' 				=> 'hidden',
						'field_id'			=> 'atrig_id',
						'required'			=> true,
					),
					array(
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'When someone sends form', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
					),
				),
			),
			
			// IMPRESSIONS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'view_triggers',
				'label' 			=> esc_html__( 'Track when page elements show on screen', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_impress',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'related_opts'			=> ['track', 'use_mutation_observer, intersections'],
				'fields'			=> array(
					array(
						'type' 				=> 'hidden',
						'field_id'			=> 'atrig_id',
						'required'			=> true,
					),
					array(
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'When this shows on screen', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
					),
				),
			),
		),
	),

	// SECION: CUSTOM EVENTS

	array(
		'section_id' => 'fupi_hotj_custom_events',
		'section_title' => esc_html__( 'Custom events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'triggers',
				'label' 			=> esc_html__( 'Send events in specific situations', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'custom_events',
				'must_have'			=> 'pro',
				'class'				=> 'fupi_r3_fullwidth fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'fields'			=> array(
					array(
						'type' 				=> 'hidden',
						'field_id'			=> 'atrig_id',
						'required'			=> true,
					),
					array(
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'When this happens', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'type'				=> 'select',
						'label' 			=> esc_html__( '...for', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'repeat',
						'option_arr_id'		=> $option_arr_id,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'options'			=> array(
							'no'				=> esc_html__( 'The first time on page', 'full-picture-analytics-cookie-notice' ),
							'yes'				=> esc_html__( 'Every time', 'full-picture-analytics-cookie-notice' ),
						),
					),
					array(
						'type'				=> 'text',
						'label'				=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'tag_name',
						'el_class'			=> 'fupi_events_builder_evt',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'required'			=> true,
					),
				),
			),
		),
	),
	
	// SECTION: ENGAGEMENT TRACKING

	array(
		'section_id' => 'fupi_hotj_vscor',
		'section_title' => esc_html__( 'Engagement tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'visitor_scoring',
				'label' 			=> esc_html__( 'Track levels of visitor engagement with your website', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'vscores',
				'must_have'			=> 'pro vscor',
				'class'				=> 'fupi_r3_fullwidth fupi_adv fupi_visitor_scoring_builder',
				'option_arr_id'		=> $option_arr_id,
				'fields'			=> array(
					array(
						'type' 				=> 'hidden',
						'field_id'			=> 'atrig_id',
						'required'			=> true,
					),
					array(
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'When', 'full-picture-analytics-cookie-notice' ),
						'after_title'		=> esc_html__( 'in a session', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'type'				=> 'text',
						'label'				=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'tag_name',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
					),
				),
			)
		),
	),

	// WOOCOMMERCE

	array(
		'section_id' => 'fupi_hotj_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track purchases', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_woo_purchases',
				'must_have'			=> 'woo',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'tag_woo_purchases_cond',
				'option_arr_id'		=> $option_arr_id,
			),
				array(
					'type'	 			=> 'multi_checkbox',
					'label' 			=> esc_html__( 'Send extra events:', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'tag_woo_purchases_data',
					'class'				=> 'tag_woo_purchases_cond fupi_sub fupi_disabled',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'options' 			=> array(
						'id'				=> '<span style="color: red">' . esc_html__( 'Order ID (Click "i" icon to learn more.)', 'full-picture-analytics-cookie-notice' ) . '</span>',
						'p_id'				=> esc_html__( 'Product IDs (one event for each product)', 'full-picture-analytics-cookie-notice' ),
						'p_name'			=> esc_html__( 'Product names (one event for each product)', 'full-picture-analytics-cookie-notice' ),
					),
					'offscreen_warning'		=> '<p class="fupi_warning_text">' . esc_html__('Order ID is treated as personal information. It will not be tracked if you enabled the privacy mode in the "Loading" section or if visitors decline using their personal data for statistics.', 'full-picture-analytics-cookie-notice') . '</p>',
						'under_field'		=> esc_html__('Each selected option will trigger a single or multiple events. This may greatly increase the number of events associated with your site!', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track checkouts', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_woo_checkouts',
				'must_have'			=> 'woo',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'tag_woo_checkouts_cond',
				'option_arr_id'		=> $option_arr_id,
			),
				array(
					'type'	 			=> 'multi_checkbox',
					'label' 			=> esc_html__( 'Send extra events:', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'tag_woo_checkouts_data',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'tag_woo_checkouts_cond fupi_sub fupi_disabled',
					'options' 			=> array(
						'p_id'				=> esc_html__( 'Product IDs (one event for each product)', 'full-picture-analytics-cookie-notice' ),
						'p_name'			=> esc_html__( 'Product names (one event for each product)', 'full-picture-analytics-cookie-notice' ),
					),
					'under_field'		=> esc_html__('Each selected option will trigger a single or multiple events. This may greatly increase the number of events associated with your site!', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track when products are added to cart', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_woo_addtocart',
				'must_have'			=> 'woo',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'tag_woo_addtocart_cond',
				'option_arr_id'		=> $option_arr_id,
			),
				array(
					'type'	 			=> 'multi_checkbox',
					'label' 			=> esc_html__( 'Send extra events', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'tag_woo_addtocart_data',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'tag_woo_addtocart_cond fupi_sub fupi_disabled',
					'options' 			=> array(
						'p_id'				=> esc_html__( 'Product IDs (one event for each product)', 'full-picture-analytics-cookie-notice' ),
						'p_name'			=> esc_html__( 'Product names (one event for each product)', 'full-picture-analytics-cookie-notice' ),
					),
					'under_field'		=> esc_html__('Each selected option will trigger a single or multiple events. This may greatly increase the number of events associated with your site!', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track when products are removed from cart', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_woo_removefromcart',
				'must_have'			=> 'woo',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'tag_woo_removefromcart_cond',
				'option_arr_id'		=> $option_arr_id,
			),
				array(
					'type'	 			=> 'multi_checkbox',
					'label' 			=> esc_html__( 'Send extra events', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'tag_woo_removefromcart_data',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'tag_woo_removefromcart_cond fupi_sub fupi_disabled',
					'options' 			=> array(
						'p_id'				=> esc_html__( 'Product IDs (one event for each product)', 'full-picture-analytics-cookie-notice' ),
						'p_name'			=> esc_html__( 'Product names (one event for each product)', 'full-picture-analytics-cookie-notice' ),
					),
					'under_field'		=> esc_html__('Each selected option will trigger a single or multiple events. This may greatly increase the number of events associated with your site!', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track when products are added to a wishlist', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_woo_addtowishlist',
				'must_have'			=> 'woo',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'tag_woo_addtowishlist_cond',
				'option_arr_id'		=> $option_arr_id,
			),
				array(
					'type'	 			=> 'multi_checkbox',
					'label' 			=> esc_html__( 'Send extra events', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'tag_woo_addtowishlist_data',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'tag_woo_addtowishlist_cond fupi_sub fupi_disabled',
					'options' 			=> array(
						'p_id'				=> esc_html__( 'Product IDs (one event for each product)', 'full-picture-analytics-cookie-notice' ),
						'p_name'			=> esc_html__( 'Product names (one event for each product)', 'full-picture-analytics-cookie-notice' ),
					),
					'under_field'		=> esc_html__('Each selected option will trigger a single or multiple events. This may greatly increase the number of events associated with your site!', 'full-picture-analytics-cookie-notice'),
				),
		),
	),
);


?>
