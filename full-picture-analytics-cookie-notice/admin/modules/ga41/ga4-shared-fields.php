<?php

if ( ! function_exists( 'fupi_get_shared_ga4_fields' ) ) {
	function fupi_get_shared_ga4_fields( $option_arr_id ) {
		return array(

			// SECTION: INSTALLATION

			'installation' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'GTAG ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'required'			=> true,
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'placeholder'		=> 'XX-0000000',
				'under_field'		=> '<p>' . sprintf( esc_html__( '%1$sHow to get GTAG ID and Measurement ID and install Google Analytics%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-install-google-analytics-4/">', '</a>') . '</p>',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Measurement ID', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'id2',
					'class'				=> 'fupi_join',
					'required'			=> true,
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[id]',
					'placeholder'		=> 'XX-0000000',
					'under_field'		=> '<p>' . esc_html__( 'Provide Measurement ID if it is different from GTAG ID.', 'full-picture-analytics-cookie-notice') . '</p>',
				),
			),

			// SECTION: DATA COLLECTION

			'basic' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Cross-browser tracking of logged-in users', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'set_user_id',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
			),
			),

			// SECTION: EVENTS

			'events' => array(

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
				'type'	 			=> 'select',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_email_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track with separate events', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track as a single event with parameters (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'offscreen'				=> '<p class="fupi_warning_text">' . esc_html__( 'Due to privacy reasons, this function tracks the last 5 digits of the phone number and the part of the email address before the "@" symbol.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track with separate events" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, WP FP will send to GA a different event for every clicked element. Events will be named according to the format "tel_clicked_[last 5 digits]" and "email_clicked_[email part before @]".', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as a single event with parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, WP FP will send to your GA "email_link_click" and "tel_link_click" events with "contact_click" parameter. Email / tel will be the value of that parameter.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see information on what links were clicked, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$scontact_click%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ) . '</p>',
			),

			// SCROLL

			array(
				'type'	 			=> 'select',
				'label' 			=> esc_html__( 'Track how deep people scroll', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_scroll_method',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'			=> ['track', 'scroll_depths, track_scroll_min, track_scroll_time'],
				'options' 			=> array(
					''					=> esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice'),
					'evt'				=> esc_html__( 'Track with separate events (recommended)', 'full-picture-analytics-cookie-notice'),
					'params'			=> esc_html__( 'Track as a single event with parameters (advanced)', 'full-picture-analytics-cookie-notice'),
				),
				'offscreen'				=> '
					<h3>' . esc_html__( '"Track with separate events" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, when a depth level is reached, WP FP will send to GA an event "scrolled_[depth]", e.g. scrolled_50 ', 'full-picture-analytics-cookie-notice' ) . '</p>
					<h3>' . esc_html__( '"Track as a single event with parameters" option', 'full-picture-analytics-cookie-notice' ) . '</h3>
					<p>' . esc_html__( 'When you choose this option, every time someone reaches a specified scroll depth, WP FP will send to your GA event "scroll" with information about the depth in an event parameter.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . sprintf ( esc_html__( 'To see this parameter in your GA reports, you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$spercent_scrolled%2$s and build a custom report.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ) . '</p>',
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
						'label'				=> esc_html__( 'send this text...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
					array(
						'label'				=> esc_html__( 'as...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'select',
						'field_id'			=> 'type',
						'class'		        => 'fupi_r3_table_row',
						'options'			=> array(
							'event'				=> esc_html__('an event name (recommended)','full-picture-analytics-cookie-notice'),
							'param'				=> sprintf( esc_html__('value of parameter "%1$s" in "%1$s" event','full-picture-analytics-cookie-notice'), 'element_click' ),
						),
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
				'related_opts'			=> ['track', 'link_click_delay'],
				'class'				=> 'fupi_adv',
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
						'label'				=> esc_html__( 'send this text...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
					array(
						'label'				=> esc_html__( 'as...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'select',
						'field_id'			=> 'type',
						'class'		        => 'fupi_r3_table_row',
						'options'			=> array(
							'event'				=> esc_html__('an event name (recommended)','full-picture-analytics-cookie-notice'),
							'param'				=> sprintf( esc_html__('value of parameter "%1$s" in "%1$s" event','full-picture-analytics-cookie-notice'), 'affiliate_link_click' ),
						),
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
						'label'				=> esc_html__( 'send this text...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
					array(
						'label'				=> esc_html__( 'as...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'select',
						'field_id'			=> 'type',
						'class'		        => 'fupi_r3_table_row',
						'options'			=> array(
							'event'				=> esc_html__('an event name (recommended)','full-picture-analytics-cookie-notice'),
							'param'				=> sprintf( esc_html__('value of parameter "%1$s" in "%2$s" event','full-picture-analytics-cookie-notice'), 'submitted_form', 'form_submit' ),
						),
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
						'label'				=> esc_html__( 'send this text...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
					array(
						'label'				=> esc_html__( 'as...', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'select',
						'field_id'			=> 'type',
						'class'		        => 'fupi_r3_table_row',
						'options'			=> array(
							'event'				=> esc_html__('an event name (recommended)','full-picture-analytics-cookie-notice'),
							'param'				=> sprintf( esc_html__('value of parameter "%1$s" in "%2$s" event','full-picture-analytics-cookie-notice'), 'viewed_element', 'element_view' ),
						),
					),
				),
			),
			
			// OTHER
			
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track JavaScript errors', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'js_err_dimens',
				'class'				=> 'fupi_adv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'offscreen_warning'				=> '<p>' .  esc_html__( 'This will send to Google Analytics descriptions of JavaScript errors on your site. Use it with caution! If your site has many errors, the number of events may exceed Google Analytics\' limit. Events descriptions are limited to 100 characters (Google\'s limit).', 'full-picture-analytics-cookie-notice') . '</p><p class="fupi_warning_text">' . sprintf ( esc_html__( 'To see this data in reports you need to %3$sregister a custom dimension in GA%4$s with event parameter %1$sjs_error_details%2$s.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ) . '</p>',
			),
			),

			// SECTION: CUSTOM EVENTS

			'custom_events' => array(
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
						'placeholder'		=> esc_html__( 'event_name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_events_builder_evt',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
					),
					array(
						'type'				=> 'r3',
						'label'				=> esc_html__( '...with event parameters (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'params',
                        'is_repeater'		=> true,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'fields'			=> array(
                            array(
                                'placeholder'		=> esc_html__( 'Parameter name', 'full-picture-analytics-cookie-notice' ),
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
									'path'				=> esc_html__( 'Path to a JS variable', 'full-picture-analytics-cookie-notice' ),
								),
							),
							array(
                                'placeholder'		=> esc_html__( 'Parameter value', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'val',
                                'class'		        => 'fupi_col_30',
                            ),
						)
					),
				),
			)
			),

			// SECTION: ENGAGEMENT

			'engagement' => array(
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
						'placeholder'		=> esc_attr__( 'event_name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_field_180',
						'class'				=> 'fupi_r3_table_row',
						'required'			=> true,
					),
					array(
						'type'				=> 'r3',
						'label'				=> esc_html__( '...with event parameters (optional)', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'params',
                        'is_repeater'		=> true,
						'class'				=> 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_240',
						'fields'			=> array(
                            array(
                                'placeholder'		=> esc_html__( 'Parameter name', 'full-picture-analytics-cookie-notice' ),
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
									'path'				=> esc_html__( 'Path to a JS variable', 'full-picture-analytics-cookie-notice' ),
								),
							),
							array(
                                'placeholder'		=> esc_html__( 'Parameter value', 'full-picture-analytics-cookie-notice' ),
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
				'label' 			=> esc_html__( 'Track how long the user is actively engaged with the content', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_engagement',
				'class'				=> 'fupi_adv fupi_deprecated',
				'related_opts'		=> ['track', 'reset_timer_on_anchor'],
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_engagement]',
				'offscreen_warning'			=> '<p>'. esc_html__( 'Sends "fp_visitorActivityTime" event right before the user closes the page, browser, tab or switches to a different app.', 'full-picture-analytics-cookie-notice') .'</p><p>' . sprintf ( esc_html__( 'To have the engagement time available in GA, you need to %3$sregister a custom metric in GA%4$s with event parameter %1$suser_engagement_time%2$s. Unit of measurement: seconds.', 'full-picture-analytics-cookie-notice') , ' <span style="background: #fdf3ce;">', '</span>', '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ),
			),
			),

			'event_parameters' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track page type', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_type',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_type"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_type]',
				'format'			=> 'key',
				'related_opts'			=> ['track', 'page_labels'],
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track page IDs', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_id',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_id"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_id]',
				'format'			=> 'key',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track the current page number of categories, tags, etc.', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_number',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_number"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_number]',
				'format'			=> 'key',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track post and page publish dates', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'post_date',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "post_date"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[post_date]',
				'format'			=> 'key',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_role',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "user_role"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[user_role]',
				'format'			=> 'key',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track page language', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_lang',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "page_lang"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[page_lang]',
				'format'			=> 'key',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track categories, tags, etc. of a post', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tax_terms',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "taxonomy_terms"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[tax_terms]',
				'related_opts'		=> ['track', 'tracked_taxonomies'],
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_tax_terms_opts',
				'format'			=> 'key',
				'under_field' 		=> esc_html__( 'This applies to posts, pages, products and custom post types. Go to related settings to change what to track.', 'full-picture-analytics-cookie-notice' ),
			),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Add taxonomy information', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'add_tax_term_cat',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_adv fupi_sub fupi_tax_terms_opts fupi_disabled',
					'under_field'		=> esc_html__( 'e.g. "european music (tag)"', 'full-picture-analytics-cookie-notice'),
				),
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Send term names instead of term slugs', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'send_tax_terms_titles',
					'option_arr_id'		=> $option_arr_id,
					'class'				=> 'fupi_adv fupi_sub fupi_tax_terms_opts fupi_disabled',
					'offscreen'				=> '<p>' . esc_html__( 'Enable this to send term names (e.g. product category) instead of their slugs (e.g. product_category). Enabling this feature is not recommended since term names can sometimes be changed while slugs are changed only on very rare occasions.', 'full-picture-analytics-cookie-notice') . '</p>',
				),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track unmodified page titles', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'clean_page_title',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_adv',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_seo_title',
				'offscreen'				=> '<p>' . esc_html__( 'By default, Google Analytics takes page titles from the "title" meta tag. This is not perfect since this tag can often change (e.g. when you tweak it with an SEO plugin). The result is that your Google Analytics can show you reports where one page can have multiple entries - under different titles.', 'full-picture-analytics-cookie-notice') . '</p><p>' . esc_html__( 'When you enable this option, WP Full Picture will send to Google Analytics the default title of your page as used on the page / post / product edit screen. This will make data analysis easier.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Track SEO titles (displayed in Google\'s search results)', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'seo_title',
					'placeholder'		=> esc_html__( 'Parameter name, e.g. "seo_title"', 'full-picture-analytics-cookie-notice' ),
					'class'				=> 'fupi_adv fupi_sub fupi_seo_title fupi_disabled',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[seo_title]',
					'format'			=> 'key',
				),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track author\'s display names', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'post_author',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "post_author"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[post_author]',
				'format'			=> 'key',
				'under_field'		=> esc_html__( 'Tracking personally identifiable information is against Google\'s policy. Make sure that the displayed names are pseudonyms.', 'full-picture-analytics-cookie-notice'),
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track author IDs', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'author_id',
				'class'				=> 'fupi_adv',
				'must_have'			=> 'author_id',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "author_id"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[author_id]',
				'format'			=> 'key',
				'related_opts'			=> ['track', 'show_author_id'],
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track the number of search results', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'search_results_nr',
				'class'				=> 'fupi_adv',
				'placeholder'		=> esc_html__( 'Parameter name, e.g. "search_results_nr"', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[search_results_nr]',
				'format'			=> 'key',
			),
			array(
				'type'	 			=> 'add_metadata',
				'label' 			=> esc_html__( 'Track metadata of posts, users and taxonomy terms', 'full-picture-analytics-cookie-notice' ),
				'class'				=> 'fupi_metadata_tracker fupi_adv',
				'field_id' 			=> 'track_cf',
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
						'placeholder'		=> esc_html__( 'parameter_name' , 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'dimname',
						'required'			=> true,
						'format' 			=> 'key',
					),
				),
			)
			),
		);
	}
}
