<?php

$option_arr_id = 'fupi_pla';

// ALL TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_pla_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Use this module to', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'pla_use',
				'option_arr_id'		=> $option_arr_id,
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_pla_use_cond',
				'options' 			=> array(
					'install'			=> esc_html__( 'Install Plausible and track user actions and WP data', 'full-picture-analytics-cookie-notice'),
					'extend'			=> sprintf( esc_html__( 'Extend the %1$sPlausible plugin%2$s with extra tracking features', 'full-picture-analytics-cookie-notice'), '<a href="https://wordpress.org/plugins/plausible-analytics/" target="_blank">', '</a>' ),
				),
				'default'			=> 'install',
				'under_field'		=> '<button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_installation_modes_offscreen">' . esc_html__( 'How do these modes differ', 'full-picture-analytics-cookie-notice' ) . '</button>',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Your website domain', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'domain',
				'class'				=> 'fupi_sub fupi_pla_use_cond fupi_cond_val_install fupi_disabled',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[domain]',
				'placeholder'		=> $_SERVER['HTTP_HOST'],
				'default'			=> $_SERVER['HTTP_HOST'],
				'offscreen'				=> '<p>' . esc_html__('This field was automatically filled with the domain of your site. Change it if it is incorrect for any reason.', 'full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Script source domain', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'custom_domain',
				'class'				=> 'fupi_sub fupi_pla_use_cond fupi_cond_val_install fupi_disabled',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[custom_domain]',
				'default'			=> 'plausible.io',
				'offscreen'				=> '<p>'. esc_html__('This field should contain the domain where the Plausible script is hosted. It can be:','full-picture-analytics-cookie-notice' ) . '</p>
					<ol>
						<li>' . esc_html__('Plausible\'s own domain (plausible.io - default)','full-picture-analytics-cookie-notice') . '</li>
						<li>' . sprintf( esc_html__('or the domain of your self-hosted plausible installation. %1$sLearn more (advanced)%2$s','full-picture-analytics-cookie-notice'), '<a href="https://github.com/plausible/community-edition/" target="_blank">', '</a>' ) . '</li>
					</ol>',
				'under_field'		=> esc_html__('Default:','full-picture-analytics-cookie-notice') . ' plausible.io',
			),
		),
	),

	// LOADING

	array(
		'section_id' => 'fupi_pla_loading',
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
				'offscreen_warning'			=> sprintf( esc_html__( 'When this option is enabled, it will load Plausible\'s tracking script for all visitors - even for administrators, bots, excluded users and locations. %1$sLearn more%2$s.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/validation-mode/">', '</a>' ),
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
				'offscreen'				=> sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by commas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '<br><br>' . esc_html__('If visitor\'s country is not recognized Plausible will load normally. Location is checked using the method chosen in the settings of the Geolocation module.', 'full-picture-analytics-cookie-notice' ),
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
	
	// EVENT TRACKING

	array(
		'section_id' => 'fupi_pla_events_2',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(

			// PAGEVIEWS

			array(
				'type'	 			=> 'button',
				'label' 			=> esc_html__( 'Tracking pageviews', 'full-picture-analytics-cookie-notice' ),
				'button_text' 		=> esc_html__( 'Optional modifications', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_adv',
				'custom_content'	=> 'class="button-secondary fupi_open_dynamic_settings fupi_not_builder_btn" data-module-id="track" data-field_ids="track_404, switch_ref, join_ref, auto_track_non_http, track_non_http"',
				'field_id' 			=> 'open_pageview_extras_btn',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> esc_html__( 'Pageviews are tracked by default. All changes are optional.', 'full-picture-analytics-cookie-notice' ),
			),

			// CONTACT

			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track clicks in contact links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_contact_links',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_contact_links]',
				'offscreen_warning'			=> '
					<h3>' . esc_html__( 'What you need to know', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<ol class="fupi_warning_text">
						<li>' . esc_html__( 'In order to use this option, you need to use Plausible Business plan. This is because clicks in contact links are tracked as goals with parameters, which can be viewed only in the Business plan.', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'If you do not use a Plausible Business plan, and you still want to track clicks in contact links, then set up tracking clicks in page elements (see options below).', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'When you enable this option, WP FP will track the last 5 digits of the phone number and the part of the email address before the "@" symbol. Tracking full numbers and addresses is against Plausible\'s Terms of service.', 'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>',
			),

			// DOWNLOADS

			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track clicks on file download links', 'full-picture-analytics-cookie-notice' ),
				'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'track_downl',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_downl]',
				'related_opts'			=> ['track', 'downl_file_formats'],
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
						'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
				),
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track as a single event with properties','full-picture-analytics-cookie-notice' ),
					'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
					'field_id' 			=> 'track_elems_goalname',
					'class'				=> 'fupi_sub fupi_adv',
					'el_class'			=> 'fupi_field_180',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_elems_goalname]',
					'offscreen_warning_id'			=> 'fupi_differences_offscreen',
					'after_field'		=> esc_html__('Usually not recommended','full-picture-analytics-cookie-notice'),
				),

			// AFF CLICKS

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
						'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
				),
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track as a single event with properties', 'full-picture-analytics-cookie-notice' ),
					'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
					'field_id' 			=> 'track_affiliate_goalname',
					'class'				=> 'fupi_sub fupi_adv',
					'el_class'			=> 'fupi_field_180',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_affiliate_goalname]',
					'offscreen_warning_id'			=> 'fupi_differences_offscreen',
					'after_field'		=> esc_html__('Usually not recommended','full-picture-analytics-cookie-notice'),
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
						'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
					),
				),
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track as a single event with properties', 'full-picture-analytics-cookie-notice' ),
					'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
					'field_id' 			=> 'track_forms_goalname',
					'class'				=> 'fupi_sub fupi_adv',
					'el_class'			=> 'fupi_field_180',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_forms_goalname]',
					'offscreen_warning_id'			=> 'fupi_differences_offscreen',
					'after_field'		=> esc_html__('Usually not recommended','full-picture-analytics-cookie-notice'),
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
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track as a single event with properties', 'full-picture-analytics-cookie-notice' ),
					'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
					'field_id' 			=> 'track_visib_goalname',
					'class'				=> 'fupi_sub fupi_adv',
					'el_class'			=> 'fupi_field_180',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_visib_goalname]',
					'offscreen_warning_id'			=> 'fupi_differences_offscreen',
					'after_field'		=> esc_html__('Usually not recommended','full-picture-analytics-cookie-notice'),
				),
			
			// OTHER

			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track how many people decline cookies', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_cookie_decline_2',
				'class'				=> 'fupi_adv',
				'el_class'			=> 'fupi_field_180',
				'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_cookie_decline_2]',
			),
		),
	),
	
	// SECTION: CUSTOM EVENTS

	array(
		'section_id' => 'fupi_pla_cond',
		'section_title' => esc_html__( 'Custom events', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( '...send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_events_builder_evt',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
					),
				),
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track as a single event with properties', 'full-picture-analytics-cookie-notice' ),
				'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'custom_track_goalname',
				'must_have'			=> 'pro',
				'class'				=> 'fupi_sub fupi_adv',
				'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[custom_track_goalname]',
				'el_class'			=> 'fupi_field_180',
				'offscreen_warning_id'			=> 'fupi_differences_offscreen',
				'after_field'		=> esc_html__('Usually not recommended','full-picture-analytics-cookie-notice'),
			),
		)
	),

	// SECTION: ENGAGEMENT

	array(
		'section_id' => 'fupi_pla_vscor',
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
						'label'				=> esc_html__( '...send event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
					),
				),
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track as a single event with properties', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_vscores_goalname',
					'must_have'			=> 'pro',
					'class'				=> 'fupi_sub fupi_adv',
					'el_class'			=> 'fupi_field_180',
					'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[custom_track_goalname]',
					'offscreen_warning_id'			=> 'fupi_differences_offscreen',
					'after_field'		=> esc_html__('Usually not recommended','full-picture-analytics-cookie-notice'),
				),
		),
	),

	// SECTION: EVENT PROPERTIES

	array(
		'section_id' => 'fupi_pla_wpdata',
		'section_title' => esc_html__( 'Properties of the pageview event', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page type', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pagetype',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'		=> ['track', 'page_labels'],
				'under_field'		=> esc_html__( 'For example: Home, Blog, Category, etc.', 'full-picture-analytics-cookie-notice'),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page title', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pagetitle',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track archive and page numbers', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pagenum',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page language', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_lang',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page id', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pageid',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track author\'s display names', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_author',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_user_role',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track searched phrases', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_search',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'add_metadata',
				'label' 			=> esc_html__( 'Track metadata of posts, users and taxonomy terms', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_cf',
				'class'				=> 'fupi_metadata_tracker fupi_adv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type'				=> 'custom_meta_select',
						'field_id'			=> 'id',
						'required'			=> true,
					),
					array(
						'type'				=> 'text',
						'field_id'			=> 'param_name',
						'required'			=> true,
						'format'			=> 'key',
						'placeholder'		=> esc_html__('parameter name','full-picture-analytics-cookie-notice'),
					),
				),
			),
		),
	),

	// WOOCOMMERCE

	array(
		'section_id' => 'fupi_pla_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track purchases', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_purchases',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_purchases]',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_track_woo_purchases',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track purchased items', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_woo_purchased_items',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_purchased_items]',
					'placeholder'		=> esc_html__('Name, e.g. "Purchased item"', 'full-picture-analytics-cookie-notice'),
					'class'				=> 'fupi_sub fupi_track_woo_purchases fupi_disabled',
				),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track when a customer begins checkout', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_checkouts',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_checkouts]',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_track_woo_checkouts',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track items in cart during checkout', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_woo_checkout_items',
					'must_have'			=> 'woo',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_checkout_items]',
					'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
					'class'				=> 'fupi_sub fupi_track_woo_checkouts fupi_disabled',
					'under_field'		=> esc_html__( 'Attention! Each item in cart is tracked with a separate event!', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track adding products to cart', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_addtocart',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__('Event name', 'full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_addtocart]',
				'under_field'		=> esc_html__( 'Attention! If a grouped product is added to cart, each item in the group will be tracked with a separate event!', 'full-picture-analytics-cookie-notice'),
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track adding products to a wishlist', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_addtowishlist',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__('Event name','full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_addtowishlist]',
			),
		),
	),

	// STATISTICS IN ADMIN

	array(
		'section_id' => 'fupi_pla_stats',
		'section_title' => esc_html__( 'Statistics in WP admin', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Shared link URL', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'shared_link_url',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[shared_link_url]',
				'placeholder'		=> esc_html__('Your shared URL', 'full-picture-analytics-cookie-notice'),
				'under_field'		=> '<p>' . sprintf( esc_html__( '%1$sGet your shared link URL%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://plausible.io/docs/shared-links" target="_blank">', '</a>' ) . '</p>'
			),
			array(
				'type' 				=> 'user_search',
				'field_id' 			=> 'selected_users',
				'label' 			=> esc_html__( 'Let specific users enter the "Reports" page', 'full-picture-analytics-cookie-notice' ),
				'must_have'			=> 'pro admin',
				'option_arr_id' 	=> $option_arr_id,
				'offscreen_warning'			=> '<p>' . esc_html__( 'By default, only administrators can access the "Reports" page.', 'full-picture-analytics-cookie-notice') . '</p>
				<p class="fupi_warning_text">' . esc_html__( 'As a security measure, the "Reports" page will NOT be accessible to users without the right to edit posts.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
		),
	),
);

?>
