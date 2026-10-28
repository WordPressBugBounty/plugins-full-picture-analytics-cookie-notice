<?php

$option_arr_id = 'fupi_mato';

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_mato_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => 		array(
			array(
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Site ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'required'			=> true,
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'under_field'		=> '<p><a href="https://wpfullpicture.com/support/documentation/how-to-install-matomo-on-premise-and-cloud/">' . esc_html__( 'How to install Matomo', 'full-picture-analytics-cookie-notice' ) . '</a></p>',
			),
			array(
				'type'	 			=> 'url',
				'label' 			=> esc_html__( 'Matomo dashboard URL', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'url',
				'class'				=> 'fupi_sub fupi_required',
				'required'			=> true,
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[url]',
				'placeholder'		=> 'https://....com/',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Tracking script\'s URL (only Matomo Cloud)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'src',
				'required'			=> true,
				'class'				=> 'fupi_sub',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[src]',
				'placeholder'		=> '//cdn.matomo.../matomo.js',
			),
		),
	),

	// LOADING

	array(
		'section_id' => 'fupi_mato_loading',
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
				'label' 			=> esc_html__('Only track visitors from specific countries', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'limit_country',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_load_opts',
				'must_have'			=> 'pro geo',
				'related_opts'		=> ['main', 'geo, ipdata_api_key, cf_worker_url, remember_geo'],
				'is repeater'		=> false,
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
				'offscreen'				=> '<p>' . sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by commas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '</p><p>'. esc_html__('Location is checked using the method chosen in the settings of the Geolocation module.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
		),
	),

	// Privacy settings

	array(
		'section_id' => 'fupi_mato_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Privacy mode', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'no_cookies',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'Enables anonymous tracking.' ,'full-picture-analytics-cookie-notice' ) . '</p>
					<ol>
						<li>' . esc_html__( 'Matomo will not use cookies that can identify visitors,' ,'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'Matomo will not track visitors across different devices and browsers,' ,'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'WP Full Picture will randomize order IDs (for WooCommerce orders).' ,'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>
					<p>' . esc_html__( 'If you use this function together with a Consent Management module, then Matomo will switch to standard tracking after visitors agree to cookies.' ,'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track across subdomains', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_subdomains',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_subdomains]',
				'offscreen'				=> '<p>' . esc_html__( 'Use this to track users across the main domain and any of its subdomains, e.g. store.example.com.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p>' . esc_html__( 'Attention. You must install Matomo with the same site ID and enable this setting on all subdomains.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track logged-in users across devices and browsers', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'set_user_id',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
			),
		),
	),

	// SIMPLE EVENTS

	array(
		'section_id' => 'fupi_mato_events',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => 		array(

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
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_email_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'Due to privacy reasons, this function tracks the last 5 digits of the phone number and the part of the email address before the "@" symbol.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),

			// SCROLL

			array(
				'type'	 			=> 'toggle',
				'label' 			=> 'Track how deep people scroll',
				'field_id' 			=> 'track_scroll_depth',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_scroll_depth]',
				'related_opts'			=> ['track', 'scroll_depths, track_scroll_min, track_scroll_time'],
			),

			// DOWNLOADS

			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Change what file downloads Matomo should track', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_downl_file_formats',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__('e.g. pdf, doc, docx, xls, xlsx, txt', 'full-picture-analytics-cookie-notice'),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_downl_file_formats]',
				'offscreen'				=> '<p>' . sprintf( esc_html__( 'By default, Matomo tracks downloads of %1$sthese file formats%2$s. Use this function to change it. Enter comma-separated list of formats that you want to track instead.', 'full-picture-analytics-cookie-notice' ), '<a href="https://matomo.org/faq/new-to-piwik/faq_47/">', '</a>' ) . '</p>
					<p class="fupi_warning_text">' . esc_html__( 'Attention! Download events will not be visible in the browser console (after you enable WP FP\'s setup helper) since they are tracked internally by Matomo\'s script, not by WP Full Picture.', 'full-picture-analytics-cookie-notice' ) . '</p>',
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
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
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
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
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
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
					),
				),
				'under_field'		=> '<p>' . sprintf( esc_html__( 'There are %1$sfour methods of tracking forms%2$s. Please use the one best suited to your form. ' , 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/how-to-choose-the-best-way-to-track-form-submissions/">', '</a>' ) . '</p>'
			),

			// IMPRESSIONS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'view_triggers',
				'label' 			=> esc_html__( 'Track when page elements show on screen', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_impress',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'			=> ['track', 'use_mutation_observer, intersections'],
				'class'				=> 'fupi_adv',
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
						'placeholder'		=> esc_html__( 'Event name', 'full-picture-analytics-cookie-notice' ),
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

	// SECTION: CUSTOM EVENTS

	array(
		'section_id' => 'fupi_mato_atrig',
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
					array(
						'type'				=> 'number',
						'label'				=> esc_html__( '...with value (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_val',
						'class'				=> 'fupi_r3_table_row',
					),
				),
			)
		),
	),

	// SECTION: ENGAGEMENT TRACKING

	array(
		'section_id' => 'fupi_mato_vscor',
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
					array(
						'type'				=> 'number',
						'label'				=> esc_html__( '...with value (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_val',
						'class'				=> 'fupi_r3_table_row',
					),
				),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Measure user activity time', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'enable_hearbeat',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'This will set Matomo\'s enableHeartBeatTimer for calculating user activity time.', 'full-picture-analytics-cookie-notice') . '</p>
				<p>' . esc_html__( 'The time of visitor\'s engagement is sent to Matomo after:', 'full-picture-analytics-cookie-notice') . '</p>
				<ol>
					<li>' . esc_html__( 'switching to another browser tab after the current tab was active for at least 15 seconds.', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'navigating to another page within the same tab.', 'full-picture-analytics-cookie-notice') . '</li>
					<li>' . esc_html__( 'closing the tab.', 'full-picture-analytics-cookie-notice') . '</li>
				</ol>',
			),
		),
	),

	// SECTION: EVENT PARAMETERS

	array(
		'section_id' => 'fupi_mato_wpdata',
		'section_title' => esc_html__( 'Event parameters', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array( // ok
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track unmodified page titles', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'clean_page_title',
				'class'				=> 'fupi_adv',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_page_title_cond',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'By default, Matomo shows in reports page titles copied from the "title" meta tag. This is not perfect since this tag can often change (e.g. when you tweak it with an SEO plugin). The result is that your Matomo can show you reports where one page can have multiple entries, under different titles.', 'full-picture-analytics-cookie-notice') . '</p><p>' . esc_html__( 'When you enable this option, WP Full Picture will send to Matomo the default title of your page as used on the page / post / product edit screen. This will make data analysis easier.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track SEO titles', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'seo_title_dimens',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[seo_title_dimens]',
				'class'				=> 'fupi_adv fupi_sub fupi_page_title_cond fupi_disabled',
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track page IDs', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_adv',
				'field_id' 			=> 'page_id_dimens',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_id_dimens]',
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track page types', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_adv',
				'field_id' 			=> 'page_type_dimens',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_type_dimens]',
				'related_opts'			=> ['track', 'page_labels'],
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track author\'s display names', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_adv',
				'field_id' 			=> 'author_dimens',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[author_dimens]',
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
			),
			array(
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track author IDs', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'author_id_dimens',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[author_id_dimens]',
				'must_have'			=> 'author_id',
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
				'related_opts'		=> ['track', 'show_author_id'],
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_role_dimens',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[user_role_dimens]',
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Visit" scope.', 'full-picture-analytics-cookie-notice'),
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track page language', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_lang_dimens',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_lang_dimens]',
				'under_field'		=> esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
			),
			array( // ok
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Track categories, tags, etc. of a post', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tax_terms_dimens',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[tax_terms_dimens]',
				'el_class'			=> 'fupi_condition',
				'related_opts'		=> ['track', 'tracked_taxonomies'],
				'el_data_target'	=> 'fupi_tax_terms_opts',
				'under_field' 		=> esc_html__( 'This applies to posts, pages, products and custom post types. Go to related settings to change what to track.', 'full-picture-analytics-cookie-notice' ) . ' ' . esc_html__( 'Provide number of a custom dimension with "Action" scope.', 'full-picture-analytics-cookie-notice'),
			),
				array( // ok
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Add taxonomy information', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'add_tax_term_cat',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_adv fupi_sub fupi_tax_terms_opts fupi_disabled',
					'under_field'		=> esc_html__( 'e.g. "european music (tag)"', 'full-picture-analytics-cookie-notice'),
				),
				array( // ok
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Track term names instead of term slugs', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'send_tax_terms_titles',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_adv fupi_sub fupi_tax_terms_opts fupi_disabled',
					'under_field'		=> esc_html__('Send term titles (e.g. product category) instead of their slugs (e.g. product_category). Enabling this feature is not recommended since term names are changed more often then slugs.', 'full-picture-analytics-cookie-notice'),
				),
			array(
				'type'	 			=> 'add_metadata',
				'label' 			=> esc_html__( 'Track metadata of posts, users and taxonomy terms', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_metadata_tracker fupi_adv',
				'field_id' 			=> 'track_cf',
				'el_class'			=> 'fupi_ga_cf_ids',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type'				=> 'custom_meta_select',
						'field_id'			=> 'id',
						'class'				=> 'fupi_col_50',
						'required'			=> true,
					),
					array(
						'type'				=> 'number',
						'field_id'			=> 'dim',
						'class'				=> 'fupi_col_30_grow',
						'required'			=> true,
					),
				),
			),
		),
	),

	// WOO

	array(
		'section_id' => 'fupi_mato_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
	),

);

?>
