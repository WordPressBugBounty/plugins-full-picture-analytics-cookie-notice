<?php

$option_arr_id = 'fupi_gtm';

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_gtm_main',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Container ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'placeholder'		=> 'GTM-0000000',
				'offscreen'				=> '<p>' . sprintf ( esc_html__('To install Google Tag Manager\'s container on this site, please paste the Container ID in the form. Please %1$sfollow this guide%2$s if you do not know where to find this ID.', 'full-picture-analytics-cookie-notice' ), '<a href="https://www.optimizesmart.com/how-to-get-google-tag-manager-container-id/" target="_blank">','</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Do not load GTM container (only push dataLayer events)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'no_container',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'offscreen_warning'			=> '<p>' . esc_html__( 'Enable this function if you want to use sGTM via Stape.io or a different service.', 'full-picture-analytics-cookie-notice') . '</p>
				<p>' . sprintf( esc_html__( 'Learn %1$show to use sGTM hosted by Stape.io%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-use-sgtm-from-stape-io/" target="_blank">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'select',
				'label' 			=> esc_html__( 'Push data to DL in format compatible with:', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'compat',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'options'			=> array(
					''					=> esc_html__( 'Keep default', 'full-picture-analytics-cookie-notice' ),
					'stape'				=> esc_html__( 'Stape.io', 'full-picture-analytics-cookie-notice' ),
				),
			),
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'DataLayer name', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'datalayer',
				'options'			=> array(
					'fupi_datalayer'	=> esc_html__( 'fupi_dataLayer (recommended)', 'full-picture-analytics-cookie-notice' ),
					'default'			=> esc_html__( 'dataLayer', 'full-picture-analytics-cookie-notice' ),
				),
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'Google Analytics, Ads and Tag Manager use the same datalayer which may lead to duplicate events. Prevent it by renaming it to fupi_datalayer.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		),
	),

	// LOADING & PRIVACY

	array(
		'section_id' => 'fupi_gtm_loading',
		'section_title' => esc_html__( 'Loading & privacy', 'full-picture-analytics-cookie-notice' ),
	),
	
	// SIMPLE EVENTS

	array(
		'section_id' => 'fupi_gtm_events',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(

			// PAGEVIEWS

			array(
				'type'	 			=> 'button',
				'label' 			=> esc_html__( 'Tracking pageviews', 'full-picture-analytics-cookie-notice' ),
				'button_text' 		=> esc_html__( 'Optional modifications', 'full-picture-analytics-cookie-notice' ),
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
				'option_arr_id'		=> $option_arr_id,
			),
			
			// DOWNLOAD

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on file download links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_downl',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_downl]',
				'related_opts'			=> ['track', 'downl_file_formats'],
			),

			// OUTBOUND

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on outbound links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_outbound',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'			=> ['track', 'link_click_delay'],
			),
			
			// FOCUS / BLUR

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track when the window visibility state changes', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_focus',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> esc_html__( 'This will send an event every time the visitor starts viewing a webpage (focus) or moves to a different browser tab or changes applications (blur).', 'full-picture-analytics-cookie-notice' ),
			),

			// SCROLL
			
			array(
				'type'	 			=> 'toggle',
				'label' 			=> 'Track how deep people scroll',
				'field_id' 			=> 'track_scroll_depth',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_scroll_depth]',
				'related_opts'			=> ['track', 'scroll_depths, track_scroll_min, track_scroll_time'],
			),
			
			// VIRTUAL PAGEVIEW

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track virtual pageviews', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_dynamic_urls',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'			=> ['track', 'virtual_pageviews'],
			),

			// ANCHOR CLICKS

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on anchors', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_anchor_clicks',
				'option_arr_id'		=> $option_arr_id,
			),

			// CLICKS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'click_triggers',
				'label' 			=> esc_html__( 'Track clicks on page elements', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_clicks',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'		=> ['track', 'link_click_delay'],
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
						'label'				=> esc_html__( 'Send an event with a parameter', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'parameter_name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
				),
			),

			// AFF CLICKS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'aff_click_triggers',
				'label' 			=> esc_html__( 'Track clicks on affiliate links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_aff',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'		=> ['track', 'link_click_delay'],
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
						'label'				=> esc_html__( 'Send an event with a parameter', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'parameter_name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'val',
						'required'			=> true,
						'class'		        => 'fupi_r3_table_row',
						'el_class'			=> 'fupi_field_180',
					),
				),
			),
			
			// FORMS
			
			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'form_triggers',
				'label' 			=> esc_html__( 'Track form submissions', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_form_submits',
				'option_arr_id'		=> $option_arr_id,
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
						'label'				=> esc_html__( 'Send an event with a parameter', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'parameter_name', 'full-picture-analytics-cookie-notice' ),
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
				'related_opts'			=> ['track', 'use_mutation_observer, intersections'],
				'option_arr_id'		=> $option_arr_id,
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
						'label'				=> esc_html__( 'Send an event with a parameter', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_attr__( 'parameter_name', 'full-picture-analytics-cookie-notice' ),
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
		'section_id' => 'fupi_gtm_atrig',
		'section_title' => esc_html__( 'Custom events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'triggers',
				'label' 			=> esc_html__( 'Track when specific conditions are met', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'custom_events',
				'must_have'			=> 'pro',
				'class'				=> 'fupi_r3_fullwidth',
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
						'label'				=> esc_html__( '...send dataLayer event', 'full-picture-analytics-cookie-notice' ),
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
	),

	// SECTION: ENGAGEMENT

	array(
		'section_id' => 'fupi_gtm_vscor',
		'section_title' => esc_html__( 'Engagement tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'visitor_scoring',
				'label' 			=> esc_html__( 'Track levels of visitor engagement with your website', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'vscores',
				'must_have'			=> 'pro vscor',
				'class'				=> 'fupi_r3_fullwidth fupi_visitor_scoring_builder',
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
						'label'				=> esc_html__( '...send dataLayer event', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'event_name', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'evt_name',
						'el_class'			=> 'fupi_field_180',
						'required'			=> true,
						'class'				=> 'fupi_r3_table_row',
					),
					array(
						'type'				=> 'r3',
						'label'				=> esc_html__( '...with event parameters (optional)', 'full-picture-analytics-cookie-notice' ),
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
									'path'				=> esc_html__( 'Path to a JS variable', 'full-picture-analytics-cookie-notice' ),
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
				'label' 			=> esc_html__( 'Track how long visitors are actively engaged with the website\'s content', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_engagement',
				'must_have'			=> 'pro',
				'related_opts'		=> ['track', 'reset_timer_on_anchor'],
				'class'				=> 'fupi_deprecated',
				'option_arr_id'		=> $option_arr_id,
			),
		),
	),

	// SECTION: PARAMETERS

	array(
		'section_id' => 'fupi_gtm_wpdata',
		'section_title' => esc_html__( 'Event parameters', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page language', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_lang',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page titles', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_title',
				'option_arr_id'		=> $option_arr_id,
				'after_field'		=> esc_html__( 'Sends both, default post/page titles and meta titles (SEO titles).', 'full-picture-analytics-cookie-notice'),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page type', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_type',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> esc_html__( 'For example: Home, Blog, Category, etc.', 'full-picture-analytics-cookie-notice'),
				'related_opts'			=> ['track', 'page_labels'],
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page IDs', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_id',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track page numbers and archive page numbers', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'page_num',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track post and page publish dates', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'post_date',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track categories, tags, etc. of a post', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'terms',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'		=> ['track', 'tracked_taxonomies'],
				'under_field' 		=> esc_html__( 'This applies to posts, pages, products and custom post types. Go to related settings to change what to track.', 'full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track search terms', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'search_terms',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track the number of search results', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'search_results',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track author\'s display names', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'author',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> esc_html__( 'Enable only if displayed names are not real names and cannot be used for identifying living persons (it is against Google\'s policy)', 'full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track author IDs', 'full-picture-analytics-cookie-notice' ),
				'must_have'			=> 'author_id',
				'field_id' 			=> 'author_id',
				'option_arr_id'		=> $option_arr_id,
				'related_opts'		=> ['track', 'show_author_id'],
			),
			array(
				'type'	 			=> 'add_metadata',
				'label' 			=> esc_html__( 'Track metadata of posts, users and taxonomy terms', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_cf',
				'must_have'			=> 'pro',
				'class'				=> 'fupi_metadata_tracker',
				'option_arr_id'		=> $option_arr_id,
				'fields'			=> array(
					array(
						'type'				=> 'custom_meta_select',
						'field_id'			=> 'id',
						'class'				=> 'fupi_col_50_grow',
						'required'			=> true,
					),
				),
			),
		),
	),

	// USER DATA TRACKING

	array(
		'section_id' => 'fupi_gtm_users',
		'section_title' => esc_html__( 'Tracking user info', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track visitor\'s location', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_user_country',
				'must_have'			=> 'pro geo',
				'related_opts'		=> ['main', 'geo, ipdata_api_key, cf_worker_url, remember_geo'],
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track user IDs', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_id',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'after_field'		=> esc_html__( 'User ID is sent in an encoded format','full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track user\'s login status and role', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_role',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track visitor\'s browser language', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'browser_lang',
				'option_arr_id'		=> $option_arr_id,
			),
		),
	),

	// WOO

	array(
		'section_id' => 'fupi_gtm_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Do NOT clear the ecommerce object in the dataLayer before each push', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'clear_woo_data',
				'must_have'			=> 'woo',
				'option_arr_id'		=> $option_arr_id,
				'after_field'		=> sprintf( esc_html__( 'Not recommended. %1$sLearn more%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/why-full-picture-clears-product-arrays-in-the-datalayer-before-pushing-new-ones/" target="_blank">', '</a>' ),
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track customer\'s name and surname', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_realname',
				'must_have'			=> 'pro woo',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track customer\'s email address', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_email',
				'must_have'			=> 'pro woo',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track customer\'s phone number', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_phone',
				'must_have'			=> 'pro woo',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track customer\'s physical address', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'user_address',
				'must_have'			=> 'pro woo',
				'option_arr_id'		=> $option_arr_id,
			),
		),
	),
);

?>
