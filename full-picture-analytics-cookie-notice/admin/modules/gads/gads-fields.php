<?php

$option_arr_id = 'fupi_gads';

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_gads_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'GTAG ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'placeholder'		=> 'XX-00000000',
				'under_field'		=> '
					<p>' . sprintf ( esc_html__( '%1$sHow to get GTAG ID and Conversion ID and install Google Ads%2$s.', 'full-picture-analytics-cookie-notice') , '<a href="https://wpfullpicture.com/support/documentation/how-to-install-google-ads/">', '</a>' ) . '</p>
					<p>' . esc_html__( 'Attention. Google cannot properly detect if multiple Google Analytics and Ads tools are installed. If you click "test instalation" in your Google account, disregard its result.', 'full-picture-analytics-cookie-notice' ) . '</p>'
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Conversion ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id2',
				'class'				=> 'fupi_join',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id2]',
				'placeholder'		=> 'AW-00000000',
				'under_field'		=> '<p>' . esc_html__( 'Provide Conversion ID if it is different from GTAG ID.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		),
	),
	
	// Privacy settings

	array(
		'section_id' => 'fupi_gads_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Enhanced Conversions', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'enh_conv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'Enhanced Conversions improve the accuracy of conversion tracking by sending to Google your visitors\' personal information, like their email address, first and last name and physical address. This information is later used by Google to better match the conversions with specific users.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'To implement Enhanced Conversions follow the steps below.', 'full-picture-analytics-cookie-notice') . '</p>
					<ol>
						<li>' . esc_html__('Enable "Enhanced Conversions" via Google Tag in your Google Ads account. You will find it in "Goals" > "Conversions" > "Settings" > "Enhanced conversions" > and select "Google tag" from the dropdown. If you can\'t see these menu elements, please switch to the new menu using the "Appearance" switch in the top.', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . sprintf( esc_html__( 'Make sure you agree and comply with Google\'s %1$sCustomer Data policies%2$s, %3$sGoogle Ads Data Processing Terms%2$s and privacy law in your country.', 'full-picture-analytics-cookie-notice' ), '<a href="https://support.google.com/adspolicy/answer/7475709?sjid=6953114821919544275-EU">', '</a>', '<a href="https://business.safety.google/adsprocessorterms/">' ) . '</li>
						<li>' . esc_html__('Add information to your privacy policy that you send personal user data to Google.', 'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>',
			)
		),
	),

	// EVENT TRACKING

	array(
		'section_id' => 'fupi_gads_events',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(

			// CONTACT

			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track clicks on email links as conversions', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_email',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'placeholder'		=> esc_html__( 'Conversion label (required)', 'full-picture-analytics-cookie-notice' ),
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track clicks on telephone links as conversions', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'placeholder'		=> esc_html__( 'Conversion label (required)', 'full-picture-analytics-cookie-notice' ),
			),

			// CLICKS

			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'click_triggers',
				'label' 			=> esc_html__( 'Track clicks on page elements as conversions', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send conversion event with label', 'full-picture-analytics-cookie-notice' ),
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
				'label' 			=> esc_html__( 'Track clicks on affiliate links as conversions', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send conversion event with label', 'full-picture-analytics-cookie-notice' ),
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
				'label' 			=> esc_html__( 'Track form submissions as conversions', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send conversion event with label', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send conversion event with label', 'full-picture-analytics-cookie-notice' ),
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
		'section_id' => 'fupi_gads_custom_events',
		'section_title' => esc_html__( 'Custom events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'events_builder',
				'for'				=> 'triggers',
				'label' 			=> esc_html__( 'Track conversions when', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( '...send an event with a conversion label', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Conversion label', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'conv_label',
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
									'path'			=> esc_html__( 'Path to a JS variable', 'full-picture-analytics-cookie-notice' ),
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
		'section_id' => 'fupi_gads_vscor',
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
						'label'				=> esc_html__( '...send an event with a conversion label', 'full-picture-analytics-cookie-notice' ),
						'placeholder'		=> esc_html__( 'Conversion label', 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'conv_label',
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
			)
		),
	),

	// WOO

	array(
		'section_id' => 'fupi_gads_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track purchases as conversions', 'full-picture-analytics-cookie-notice' ),
				'placeholder'		=> esc_html__( 'Conversion label (required)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'woo_conv_id',
				'must_have'			=> 'woo',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[woo_conv_id]',
				'offscreen'				=> '<p>' . esc_html__( 'The conversion event will be sent with cart value and IDs of the purchased items.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . sprintf ( esc_html__( 'To get the conversion label, please follow this %1$sshort video tutorial%2$s.', 'full-picture-analytics-cookie-notice') , '<a href="https://wpfullpicture.com/support/documentation/how-to-get-google-ads-tag-id-conversion-id/">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track checkouts as conversions', 'full-picture-analytics-cookie-notice' ),
				'placeholder'		=> esc_html__( 'Conversion label (required)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'woo_checkout_conv_id',
				'must_have'			=> 'woo',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[woo_checkout_conv_id]',
				'offscreen'				=> '<p>' . esc_html__( 'The conversion event will be sent with cart value and IDs of the purchased items.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__('Attention. By default, this event will be sent every time the user visits the checkout page - even if it happens multiple times in one session (except page refreshes). You can disable it by setting this conversion as a single-time conversion in the Google Ads panel (while registering a new conversion type).', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . sprintf ( esc_html__( 'To get the conversion label, please follow this %1$sshort video tutorial%2$s.', 'full-picture-analytics-cookie-notice') , '<a href="https://wpfullpicture.com/support/documentation/how-to-get-google-ads-tag-id-conversion-id/">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track additions to cart as conversions', 'full-picture-analytics-cookie-notice' ),
				'placeholder'		=> esc_html__( 'Conversion label (required)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'woo_add_to_cart_conv_id',
				'must_have'			=> 'woo',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[woo_add_to_cart_conv_id]',
				'offscreen'				=> '<p>' . esc_html__( 'The conversion event will be sent with the IDs of the items added to cart and their total value.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>' . sprintf ( esc_html__( 'To get the conversion label, please follow this %1$sshort video tutorial%2$s.', 'full-picture-analytics-cookie-notice') , '<a href="https://wpfullpicture.com/support/documentation/how-to-get-google-ads-tag-id-conversion-id/">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'select',
				'label' 			=> esc_html__( 'Your business type', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'business_type',
				'must_have'			=> 'woo',
				'option_arr_id'		=> $option_arr_id,
				'options' 			=> array(
					'retail'			=> esc_html__( 'Retail', 'full-picture-analytics-cookie-notice' ),
					'local'				=> esc_html__( 'Local business', 'full-picture-analytics-cookie-notice' ),
					'real_estate'		=> esc_html__( 'Real estate', 'full-picture-analytics-cookie-notice' ),
				),
				'default'			=> 'retail',
			),
		),
	),
);

?>
