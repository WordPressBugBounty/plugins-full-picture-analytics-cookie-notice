<?php

$option_arr_id = 'fupi_fbp1';

$sections = array(

	// BASIC SETUP

	array(
		'section_id' => 'fupi_fbp1_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Pixel ID / Dataset ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'pixel_id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[pixel_id]',
				'under_field'		=> '<p><a href="https://wpfullpicture.com/support/documentation/how-to-install-meta-pixel/">' . esc_html__('See where to find it', 'full-picture-analytics-cookie-notice') . '</a></p>' ,
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Conversion API token (for server-side tracking)', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'capi_token',
					'class'				=> 'fupi_sub',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[capi_token]',
					'offscreen'				=> '<p>' . esc_html__( 'Conversion API lets you track visitors who use ad blockers.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'After you enable Conversion API, you will be able to send data to Meta by both, the tracking pixel and your server. Simply click the "Send event with Conversion API" on the next pages.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'Enabling Conversion API will put a strain on your server. If it is at its limits, use Conversion API only for the most important events.', 'full-picture-analytics-cookie-notice' ) . '</p>',
					'under_field'		=> '<p><a href="https://wpfullpicture.com/support/documentation/how-to-install-meta-pixel/#hook_get_capi_token">' . esc_html__('See where to find it', 'full-picture-analytics-cookie-notice') . '</a></p>' ,
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Install 2nd pixel (optional)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'extra_install',
				'must_have'			=> 'pro',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_2nd_opts',
				'option_arr_id'		=> $option_arr_id,
				'after_field'		=> esc_html__( 'Both pixels will track the same data and use the same settings.', 'full-picture-analytics-cookie-notice' ),
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Pixel ID / Dataset ID #2', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'pixel_id_2',
					'class'				=> 'fupi_sub fupi_2nd_opts fupi_disabled',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[pixel_id_2]',
				),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Conversion API token #2', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'capi_token_2',
					'class'				=> 'fupi_sub fupi_2nd_opts fupi_disabled',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[capi_token_2]',
				),
		),
	),

	// LOADING SETUP

	array(
		'section_id' => 'fupi_fbp1_loading',
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
				'must_have'			=> 'pro geo',
				'related_opts'		=> ['main', 'geo, ipdata_api_key, cf_worker_url, remember_geo'],
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_load_opts fupi_adv',
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
						'placeholder'		=> esc_html__('e.g. GB, DE, FR, AU, etc.','full-picture-analytics-cookie-notice'),
					),
				),
			),
		),
	),

	// DATA COLLECTION

	array(
		'section_id' => 'fupi_fbp1_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Prevent Meta Pixel from tracking unauthorized events','full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'disable_auto_events',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'			=> '<p>' . esc_html__( 'Meta Pixel script can automatically and without authorization track pageviews, button clicks, ecommerce events, microdata and page metadata.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'For example, Meta can automatically track PageView events when the website\'s URL changes without page refresh (using pushState, replaceState or popstate functions). These events do not have Event IDs and cannot be deduplicated with matching Conversion API events. This leads to inacurate data and multi-counting some events.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'When you enable this option, Meta Pixel will be only sending events that you set up in WP Full Picture and Meta\'s own Event Setup tool.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Advanced matching', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'adv_match',
				'must_have'			=> 'pro',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_adv_match_opts',
				'option_arr_id'		=> $option_arr_id,
				'offscreen' 			=> '<p>' . esc_html__( 'Advanced matching improves the accuracy of conversion tracking by sending to Meta personally identifiable information about your users.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'When you enable Advanced Matching, WP Full Picture will start sending to Meta encrypted addresses, email addresses, phone numbers and user identifiers of your visitors.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'The data will be sent on all pages where your visitor is logged in and on the WooCommerce order confirmation page. The type and amount of sent data depends on what is known about the visitors at a given moment.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__('External ID generation', 'full-picture-analytics-cookie-notice'),
					'under_field'		=> esc_html__('Provide comma-separated CSS selectors of form fields, where users are the most likely to leave the same email address as they used to register to this website.', 'full-picture-analytics-cookie-notice'),
					'field_id' 			=> 'user_email_fields',
					'class'				=> 'fupi_adv fupi_adv_match_opts fupi_sub fupi_disabled',
					'option_arr_id'		=> $option_arr_id,
					'offscreen' 			=> '<p>' . esc_html__('This function improves cross-browser conversion attribution and event match quality score.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__('It generates External IDs for users who are not logged-in, based on email addresses they provide in form fields on your site.', 'full-picture-analytics-cookie-notice') . '</p>
					<p><a href="https://wpfullpicture.com/support/documentation/what-is-advanced-external-id/" target="_blank">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice') . '</a></p>',
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Enable "Limited Data Use" for visitors from the US', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'limit_data_use',
				'after_field'		=> esc_html__( 'required for compliance with privacy laws in some US states', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . sprintf( esc_html__( 'WP Full Picture will modify Pixel code to comply with the data processing regulations in Colorado, Connecticut and California (US). This will negatively impact data accuracy, campaign performance and retargeting done on the visitors from these states. Users from other states and countries will not be affected. %1$sLearn more%2$s.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://developers.facebook.com/docs/meta-pixel/implementation/data-processing-options">', '</a>' ) . '</p>',
			),
		),
	),

	// STANDARD EVENTS

	array(
		'section_id' => 'fupi_fbp1_events',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(

			// PAGEVIEWS

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track pageviews with Conversion API', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pageview_capi',
				'class'				=> 'fupi_adv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . esc_html__( 'By default, pageviews are tracked only by the browser pixel.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),

			// OUTBOUND

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on outbound links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_outbound',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'under_field'				=> esc_html__( 'Sends "outbound" event with a parameter "url".', 'full-picture-analytics-cookie-notice' ),
				'related_opts'			=> ['track', 'link_click_delay'],
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_outbound_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
				),
			
			// CONTACT

			array( 
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_email_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> esc_html__( 'Sends "contact" event with parameters "type" (tel or email) and "target". Due to privacy reasons, this function tracks the last 5 digits of the phone number and the part of the email address before the "@" symbol', 'full-picture-analytics-cookie-notice' ),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_email_tel_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
				),
			
			// DOWNLOADS

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on file download links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_downl',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_downl]',
				'under_field'		=> esc_html__( 'Sends "file download" event with a "file" parameter.', 'full-picture-analytics-cookie-notice' ),
				'related_opts'			=> ['track', 'downl_file_formats'],
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_file_downl_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
				),
			
			// SCROLLS

			array(
				'type'	 			=> 'toggle',
				'label' 			=> 'Track how deep people scroll',
				'field_id' 			=> 'track_scroll_depth',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_scroll_depth]',
				'under_field'		=> esc_html__( 'Sends "scroll" event with a "scroll height" parameter', 'full-picture-analytics-cookie-notice' ),
				'related_opts'			=> ['track', 'scroll_depths, track_scroll_min, track_scroll_time'],
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_scroll_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'after_field'		=> '<p>' . esc_html__( 'Not recommended', 'full-picture-analytics-cookie-notice') . '</p>'
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
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'title'				=> esc_html__( '"click on element" with a parameter "name"', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'and parameter\'s value', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Name of the clicked element', 'full-picture-analytics-cookie-notice' ),
						'required'			=> true,
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
					),
				),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_elems_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
				),
			
			// AFFILIATE

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
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'title'				=> esc_html__( '"affiliate" with a parameter "target"', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'and parameter\'s value', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Name of the clicked link', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'required'			=> true,
					),
				),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_affiliate_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
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
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'title'				=> esc_html__( '"form submit" with a parameter "form"', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'and parameter\'s value', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Form\'s name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'required'			=> true,
					),
				),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_forms_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
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
						'type'				=> 'headline',
						'before_title'		=> esc_html__( 'Send event', 'full-picture-analytics-cookie-notice' ),
						'title'				=> esc_html__( '"user viewed" with a parameter "element"', 'full-picture-analytics-cookie-notice' ),
					),
					array(
						'label'				=> esc_html__( 'and parameter\'s value', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Name of the viewed element', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'required'			=> true,
					),
				),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_views_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
				),
		),
	),

	// SECTION: CUSTOM EVENTS

	array(
		'section_id' => 'fupi_fbp1_atrig',
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
						'type'	 			=> 'select',
						'label' 			=> esc_html__( '...send', 'full-picture-analytics-cookie-notice' ),
						'field_id' 			=> 'evt_type',
						'option_arr_id'		=> $option_arr_id,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'options'			=> array(
							'custom'				=> esc_html__( 'Custom event', 'full-picture-analytics-cookie-notice' ),
							'standard'				=> esc_html__( 'Meta standard event', 'full-picture-analytics-cookie-notice' ),
						),
					),
					array(
						'type'				=> 'text',
						'label'				=> esc_html__( '...with event name', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_field_240',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
					),
					array(
						'type'				=> 'r3',
						'label'				=> esc_html__( '...and parameters (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'params',
                        'is_repeater'		=> true,
						'class'		        => 'fupi_r3_table_row',
						'fields'			=> array(
                            array(
                                'placeholder'		=> esc_attr__( 'Parameter name', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'name',
                                'class'		        => 'fupi_col_30',
                            ),
							array(
								'type'	 			=> 'select',
								'field_id' 			=> 'type',
								'option_arr_id'		=> $option_arr_id,
								'class'				=> 'fupi_col_20',
								'options'			=> array(
									''					=> esc_html__( 'Value type', 'full-picture-analytics-cookie-notice' ),
									'string'			=> esc_html__( 'Text', 'full-picture-analytics-cookie-notice' ),
									'number'			=> esc_html__( 'Number', 'full-picture-analytics-cookie-notice' ),
									'bool'				=> esc_html__( 'true/false', 'full-picture-analytics-cookie-notice' ),
									'path'			=> esc_html__( 'Path to a JS variable', 'full-picture-analytics-cookie-notice' ),
								),
							),
							array(
                                'placeholder'		=> esc_attr__( 'Parameter value', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'val',
                                'class'		        => 'fupi_col_30',
                            ),
						)
					),
					array(
						'type'	 			=> 'toggle',
						'label' 			=> esc_html__( 'Also track with CAPI', 'full-picture-analytics-cookie-notice' ),
						'field_id' 			=> 'capi',
						'option_arr_id'		=> $option_arr_id,
						'class'		        => 'fupi_r3_table_row',
					),
				),
			)
		),
	),

	// SECTION: ENGAGEMENT

	array(
		'section_id' => 'fupi_fbp1_vscores',
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
						'placeholder'		=> esc_attr__( 'Event name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_field_240',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
					),
					array(
						'type'				=> 'r3',
						'label'				=> esc_html__( '...with parameters (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'params',
                        'is_repeater'		=> true,
						'class'		        => 'fupi_r3_table_row',
						'fields'			=> array(
                            array(
                                'placeholder'		=> esc_attr__( 'Parameter name', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'name',
                                'class'		        => 'fupi_col_30',
                            ),
							array(
								'type'	 			=> 'select',
								'field_id' 			=> 'type',
								'option_arr_id'		=> $option_arr_id,
								'class'				=> 'fupi_col_20',
								'options'			=> array(
									''					=> esc_html__( 'Value type', 'full-picture-analytics-cookie-notice' ),
									'string'			=> esc_html__( 'Text', 'full-picture-analytics-cookie-notice' ),
									'number'			=> esc_html__( 'Number', 'full-picture-analytics-cookie-notice' ),
									'bool'				=> esc_html__( 'true/false', 'full-picture-analytics-cookie-notice' ),
									'path'			=> esc_html__( 'Path to a JS variable', 'full-picture-analytics-cookie-notice' ),
								),
							),
							array(
                                'placeholder'		=> esc_attr__( 'Parameter value', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'val',
                                'class'		        => 'fupi_col_30',
                            ),
						)
					),
				),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_vscor_capi',
					'class'				=> 'fupi_sub fupi_adv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
				),

			// ENGAGEMENT TIME
				
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track how long the user is actively engaged with the content', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_engagement',
				'class'				=> 'fupi_adv fupi_deprecated',
				'related_opts'		=> ['track', 'reset_timer_on_anchor'],
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> esc_html__( 'Sends "user engagement time" event with a parameter "seconds". The event is sent right before the user closes the page, browser, tab or switches to a different app.', 'full-picture-analytics-cookie-notice' ),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Also track with Conversion API', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_engagement_capi',
					'class'				=> 'fupi_sub fupi_adv fupi_deprecated',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'after_field'		=> esc_html__( 'Not recommended on slow hosting.', 'full-picture-analytics-cookie-notice'),
				),
		),
		
	),

	// EVENT PARAMS

	array(
		'section_id' => 'fupi_fbp1_wpdata',
		'section_title' => esc_html__( 'Event parameters', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page type', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pagetype',
				'class'				=> 'fupi_adv',
				'related_opts'			=> ['track', 'page_labels'],
				'option_arr_id'		=> $option_arr_id,
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
				'label' 			=> esc_html__( 'Track post and page publish dates', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_pobdate',
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
				'label' 			=> esc_html__( 'Track what categories, tags, etc. a post belongs to', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_terms',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'el_class'			=> 'fupi_condition',
				'related_opts'		=> ['track', 'tracked_taxonomies'],
				'el_data_target'	=> 'fupi_track_terms_opts',
				'under_field' 		=> esc_html__( 'This applies to posts, pages, products and custom post types. Go to related settings to change what to track.', 'full-picture-analytics-cookie-notice' ),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Add taxonomy information', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'add_tax_term_cat',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_sub fupi_track_terms_opts fupi_disabled fupi_adv',
					'under_field'		=> esc_html__( 'e.g. "european music (tag)"', 'full-picture-analytics-cookie-notice'),
				),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Send term names instead of term slugs', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'send_tax_terms_titles',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_sub fupi_track_terms_opts fupi_disabled fupi_adv',
					'offscreen'				=> '<p>' . esc_html__( 'This will send term names (e.g. The best of european music) instead of term slugs (e.g. eu_music). Doing this is not recommended since term names can sometimes be changed while slugs are changed only on rare occasions.', 'full-picture-analytics-cookie-notice') . '</p>',
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
				'label' 			=> esc_html__( 'Track searched phrases', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_search',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_user_role',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> esc_html__( 'Tracked as a parameter "user_type" of the "PageView" event', 'full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track visitor\'s browser language', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_lang',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> esc_html__( 'Tracked as a parameter "browser_lang" of the "PageView" event', 'full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'add_metadata',
				'label' 			=> esc_html__( 'Track metadata of posts, users and taxonomy terms', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_cf',
				'must_have'			=> 'pro',
				'class'				=> 'fupi_metadata_tracker fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type'				=> 'custom_meta_select',
						'field_id'			=> 'id',
						'required'			=> true,
					),
				),
			),
		)
	),

	// WOO

	array(
		'section_id' => 'fupi_fbp1_ecomm',
		'section_title' => esc_html__( 'Tracking WooCommerce', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Status-Based Order Tracking', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'adv_orders',
				'must_have'			=> 'pro woo',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . sprintf( esc_html__( 'Status-Based Order Tracking sends purchase events via your server, when order status changes. You can learn more about it %1$son this page%2$s.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/what-you-need-to-know-about-status-based-order-tracking/">', '</a>' ) . '</p><p>' . esc_html__( 'Attention. SBOT requires the use of Conversion API. Enable it on the "Installation" page, if you haven\'t done it yet.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'button',
				'label' 			=> esc_html__( 'Global SBOT settings', 'full-picture-analytics-cookie-notice' ),
				'button_text' 		=> esc_html__( 'Settings', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_sub fupi_adv',
				'must_have'			=> 'pro woo',
				'custom_content'	=> 'class="button-secondary fupi_open_dynamic_settings fupi_not_builder_btn" data-module-id="woo" data-field_ids="server_track_on_statuses, server_cancel_on_statuses, debug_server_calls"',
				'field_id' 			=> 'open_pageview_extras_btn',
				'option_arr_id'		=> $option_arr_id,
			),
		),
	),
);

?>
