<?php

$option_arr_id = 'fupi_tik';

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_tik_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' =>  array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'TikTok Pixel ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'under_field'		=> '<p>' . sprintf( esc_html__('Learn %1$swhere to get pixel ID from%2$s.','full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-install-tiktok-pixel/" target="_blank">', '</a>') . '</p>',
			),
		),
	),

	// LOADING

	array(
		'section_id' => 'fupi_tik_loading',
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
						'placeholder'		=> esc_html__('e.g. GB, DE, FR, AU, etc.','full-picture-analytics-cookie-notice'),
					),
				),
			),
		),
	),

	// EVENT TRACKING

	array(
		'section_id' => 'fupi_tik_events',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
					
			// CONTACT
		
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on email and tel. links', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_email_tel',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'offscreen_warning'			=> '<p class="fupi_warning_text">' . esc_html__( 'When this option is enabled, all clicks on email and phone links will be tracked and sent to TikTok. However, due to TikToks tracking limitations, the event will NOT contain information WHICH link was clicked.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),

			// DOWNLOADS

			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Track clicks on file download links', 'full-picture-analytics-cookie-notice' ),
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
				'related_opts'		=> ['track', 'link_click_delay'],
				'option_arr_id'		=> $option_arr_id,
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
						'placeholder'		=> esc_html__( 'your_text', 'full-picture-analytics-cookie-notice' ),
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
							'custom'			=> esc_html__('a custom event (sent as event "custom_" + your_name)','full-picture-analytics-cookie-notice'),
							'event'				=> sprintf( esc_html__('a parameter of "%1$s" event','full-picture-analytics-cookie-notice'), 'ClickButton' ) . ' (' . esc_html__('supported until 2027','full-picture-analytics-cookie-notice') . ')',
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
						'placeholder'		=> esc_html__( 'your_text', 'full-picture-analytics-cookie-notice' ),
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
							'lead'				=> sprintf( esc_html__('a parameter of "%1$s" event','full-picture-analytics-cookie-notice'), 'Lead' ),
							'event'				=> sprintf( esc_html__('a parameter of "%1$s" event','full-picture-analytics-cookie-notice'), 'SubmitForm' ) . ' (' . esc_html__('supported until 2027','full-picture-analytics-cookie-notice') . ')',
							'custom'			=> esc_html__('a custom event (sent as event "custom_" + your_name)','full-picture-analytics-cookie-notice'),
						),
					),
				),
			),
		),
	),
	
	// WOOCOMMERCE

	array(
		'section_id' => 'fupi_tik_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'radio',
				'label' 			=> esc_html__( 'Track purchases with event', 
				'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'purchase_evt_name',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[use_purch_evt_name]',
				'options'			=> array(
					'old' 				=> '<span style="color: red;">PlaceAnOrder - ' .esc_html__( 'deprecated, supported until 2027', 'full-picture-analytics-cookie-notice' ) . '</span>',
					'new' 				=> 'Purchase',
				),
				'offscreen'			=> esc_html__( 'WP Full Picture will start tracking purchases with the new "Purchase" event automatically, following its December update. We highly recommend you switch to "Purchase" event right now, to avoid surprises.', 'full-picture-analytics-cookie-notice' ),
			),
		)
	)
);

?>
