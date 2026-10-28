<?php

$option_arr_id = 'fupi_clar';

// ALL TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_clar_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Clarity Project ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'under_field'		=> sprintf( esc_html__( 'To get the Project ID visit %3$sclarity\'s dashboard%4$s and create or open a project. You will find the ID in the page’s URL (clarity.microsoft.com/projects/view/%1$sPROJECT_ID%2$s/...).', 'full-picture-analytics-cookie-notice' ), '<strong>', '</strong>', '<a href="https://clarity.microsoft.com/projects/" target="_blank">', '</a>' ),
			),
		),
	),

	// INSTALLATION & LOADING

	array(
		'section_id' => 'fupi_clar_loading',
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
				'is repeater'		=> false,
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

	// INSTALLATION

	array(
		'section_id' => 'fupi_clar_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Consent mode', 'full-picture-analytics-cookie-notice' ),
				'after_field'		=> esc_html__( 'Recommended', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'no_cookie',
				'option_arr_id'		=> $option_arr_id,
				'offscreen_warning'			=> '<p>' . esc_html__( 'If you enable this function together with a Consent Management module, then Clarity will track visitors in an anonymized way until they agree to tracking. After they do this, Clarity will switch to standard tracking.' ,'full-picture-analytics-cookie-notice' ) . '</p>
				<p class="fupi_warning_text">' . esc_html__( 'To use this mode, you need to turn off the use of cookies in Microsoft Clarity\'s dashboard (Settings > Setup > Advanced Settings > Cookies).', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
		),
	),

	// TAGGING

	array(
		'section_id' => 'fupi_clar_tags',
		'section_title' => esc_html__( 'Tagging recordings', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_email_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . esc_html__( 'For privacy reasons, it will track the last 5 digits of the phone number and the part of the email address before the "@" symbol.', 'full-picture-analytics-cookie-notice' ) . '</p>',
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
				'label' 			=> esc_html__( 'Track clicks on anchor links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_anchor_clicks',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),

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
				'label' 			=> esc_html__( 'Track types of visited pages', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_pagetype',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> esc_html__( 'For example: Home, Blog, Category, etc.', 'full-picture-analytics-cookie-notice'),
			),
			
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track authors of visited pages', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_pageauthor',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tag_user_role',
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
						'label'				=> esc_html__( 'Tag the recording with', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Tag name', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Tag the recording with', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Tag name', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Tag the recording with', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Tag name', 'full-picture-analytics-cookie-notice' ),
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
				'label' 			=> esc_html__( 'Tag session recordings when page elements show on screen', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Tag the recording with', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Tag name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
					),
				),
			),

			// OTHER
			
			array(
				'type'	 			=> 'add_metadata',
				'label' 			=> esc_html__( 'Track metadata of posts, users and taxonomy terms', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_metadata_tracker fupi_adv',
				'field_id' 			=> 'tag_cf',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'related_opts'			=> ['track', 'custom_data_ids'],
				'fields'			=> array(
					array(
						'type'				=> 'custom_meta_select',
						'field_id'			=> 'id',
						'placeholder'		=> 'meta id',
						'required'			=> true,
					),
					array(
						'type'				=> 'text',
						'field_id'			=> 'name',
						'placeholder'		=> 'Tag name',
						'required'			=> true,
					),
				),
			),
		)
	),

	// SECTION: CUSTOM EVENTS

	array(
		'section_id' => 'fupi_clar_custom_events',
		'section_title' => esc_html__( 'Tagging with custom events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'triggers',
				'label' 			=> esc_html__( 'Track when specific conditions are met', 'full-picture-analytics-cookie-notice' ),
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
						'type'	 			=> 'select',
						'label' 			=> esc_html__( '...for', 'full-picture-analytics-cookie-notice' ),
						'field_id' 			=> 'repeat',
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
						'label'				=> esc_html__( 'Tag recording with', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Tag name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_events_builder_evt',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
					),
				),
			)
		),
	),

	// SECTION: ENGAGEMENT TRACKING
	
	array(
		'section_id' => 'fupi_clar_vscores',
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
						'label'				=> esc_html__( 'Tag recording with', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Tag name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_events_builder_evt',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
					),
				),
			)
		),
	),

	// WOOCOMMERCE

	array(
		'section_id' => 'fupi_clar_ecomm',
		'section_title' => esc_html__( 'Tagging recordings with WooCommerce events', 'full-picture-analytics-cookie-notice' ),
	),
);

?>
