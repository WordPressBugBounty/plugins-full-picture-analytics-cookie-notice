<?php

$option_arr_id = 'fupi_ga41';
$ga41_data = get_option('fupi_ga41');
$gads_data = get_option('fupi_gads');

$is_gads_installed = ! empty( $this->tools['gads'] );
$gads_gtag_id = $is_gads_installed && ! empty( $gads_data['id'] ) ? $gads_data['id'] : false;

require_once 'ga4-shared-fields.php';

$shared_fields = fupi_get_shared_ga4_fields( $option_arr_id );

$sections = array(
	array(
		'section_id' => 'fupi_ga41_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array_merge(
			$shared_fields['installation'],
			array(
				array(
					'type' 				=> 'toggle',
					'label' 			=> esc_html__( 'Avoid conflicts with other Google Analytics installations', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'cookie_prefix',
					'option_arr_id'		=> $option_arr_id,
					'under_field'		=> '<p>' . esc_html__( 'Enable it if you also installed GA with a different plugin or GTM.', 'full-picture-analytics-cookie-notice' ) . '</p><p>' . esc_html__('Attention. You shouldn\'t install Google\'s tools with different methods, for example, one tool with GTM and another with a plugin. However, if you do, then enable this function. It will change the name of GA\'s cookie to try to avoid conflicts.', 'full-picture-analytics-cookie-notice') . '</p>',
				),
			)
		),
	),
	array(
		'section_id' => 'fupi_ga41_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array_merge(
			array(
				array(
					'type'	 			=> 'toggle',
					'label' 			=> esc_html__( 'Enhanced Conversions for linked Google Ads', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'enh_conv',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'under_field'		=> esc_html__( 'Use it if you linked your Google Analytics with your Google Ads', 'full-picture-analytics-cookie-notice' ),
					'offscreen'			=> '<p>' . esc_html__( 'Enhanced Conversions improves the accuracy of conversion tracking in Google Ads when it is linked with Google Analytics. This option sends to Google your visitors\' personal information, like their email address, first and last name and physical address. This information is later used by Google to better match the conversions with specific users.', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'To implement Enhanced Conversions in Google Ads via Google Analytics follow the steps below.', 'full-picture-analytics-cookie-notice') . '</p>
					<ol>
						<li>' . esc_html__('Go to your Google Analytics panel > Admin (settings page) > Data Collection > and enable "User-provided data collection" ', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . esc_html__('Go back to the Admin page (settings page) > and link your GA with G Ads in Google Ads links section" ', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . esc_html__('Enable "Enhanced Conversions" via Google Tag in your Google Ads account. You will find it in "Goals" > "Conversions" > "Settings" > "Enhanced conversions" > and select "Google tag" from the dropdown. If you can\'t see these menu elements, please switch to the new menu using the "Appearance" switch in the top.', 'full-picture-analytics-cookie-notice') . '</li>
						<li>' . sprintf( esc_html__( 'Make sure you agree and comply with Google\'s %1$sCustomer Data policies%2$s, %3$sGoogle Ads Data Processing Terms%2$s and privacy law in your country.', 'full-picture-analytics-cookie-notice' ), '<a href="https://support.google.com/adspolicy/answer/7475709?sjid=6953114821919544275-EU">', '</a>', '<a href="https://business.safety.google/adsprocessorterms/">' ) . '</li>
						<li>' . esc_html__('Add information to your privacy policy that you send personal user data to Google.', 'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>',
				),
			),
			$shared_fields['basic']
		),
	),
	array(
		'section_id' => 'fupi_ga41_events',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => $shared_fields['events'],
	),
	array(
		'section_id' => 'fupi_ga41_custom_events',
		'section_title' => esc_html__( 'Custom events', 'full-picture-analytics-cookie-notice' ),
		'fields' => $shared_fields['custom_events'],
	),
	array(
		'section_id' => 'fupi_ga41_vscores',
		'section_title' => esc_html__( 'Engagement tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => $shared_fields['engagement'],
	),
	array(
		'section_id' => 'fupi_ga41_wpdata',
		'section_title' => esc_html__( 'Event parameters', 'full-picture-analytics-cookie-notice' ),
		'fields' => $shared_fields['event_parameters'],
	),
	array(
		'section_id' => 'fupi_ga41_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Status-Based Order Tracking', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'adv_orders',
				'must_have'			=> 'pro woo',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . sprintf( esc_html__( 'Status-Based Order Tracking sends purchase events via your server, when order status changes. It tracks orders, refunds and cancellations. You can learn more about it %1$son this page%2$s.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/support/documentation/what-you-need-to-know-about-status-based-order-tracking/">', '</a>' ) . '</p><p>' . esc_html__( 'Attention. Google Analytics will not track orders tracked more than 3 days after their visit.', 'full-picture-analytics-cookie-notice' ) . '</p>',
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
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Measurement Protocol API secret key (required)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'mp_secret_key',
				'class'				=> 'fupi_sub fupi_adv',
				'must_have'			=> 'pro woo',
				'required'			=> true,
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[mp_secret_key]',
				'under_field'		=> sprintf( esc_html__( '%1$sWhere to find the MP key%2$s', 'full-picture-analytics-cookie-notice'), '<button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_mpapi_key_offscreen">', '</button>'),
			),
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__( 'Custom metadata for purchase event (optional)', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'purchase_custom_meta',
				'must_have'			=> 'pro woo',
				'class'				=> 'fupi_sub fupi_adv fupi_metadata_tracker fupi_fullwidth_tr',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'offscreen'				=> '<p>' . esc_html__( 'This setting lets you add custom metadata to your purchase event. The data will be sent only server-side, with the Status-Based Order Tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>
						<p>' . sprintf( esc_html__( 'Remember, that you need to register this data as a custom dimension, to see it in the Google Analytics reports.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-custom-definitions-in-google-analytics-4/">', '</a>' ) . '</p>',
				'fields'			=> array(
					array(
						'type'				=> 'text',
						'placeholder'		=> esc_html__( 'Metadata key' , 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'key',
						'required'			=> true,
						'class'				=> 'fupi_col_30',
					),
					array(
						'type'				=> 'select',
						'field_id'			=> 'type',
						'options'			=> array(
							'string'			=> esc_html__('Text','full-picture-analytics-cookie-notice'),
							'int'				=> esc_html__('Integer','full-picture-analytics-cookie-notice'),
							'float'				=> esc_html__('Number with decimals','full-picture-analytics-cookie-notice'),
							'bool'				=> esc_html__('true/false','full-picture-analytics-cookie-notice'),
						),
						'class'				=> 'fupi_col_20',
					),
					array(
						'type'				=> 'text',
						'placeholder'		=> esc_html__( 'parameter_name' , 'full-picture-analytics-cookie-notice' ),
						'field_id'			=> 'dimname',
						'required'			=> true,
						'format' 			=> 'key',
						'class'				=> 'fupi_col_30',
					),
				),
			),
		),
	)
);
