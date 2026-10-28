<?php

$option_arr_id = 'fupi_twit';

// ALL TOGETHER

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_twit_install',
		'section_title' => esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Pixel ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'id',
				'class'				=> 'fupi_required',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[id]',
				'offscreen_id'			=> 'fupi_install_offscreen',
			),
		),
	),

	// LOADING

	array(
		'section_id' => 'fupi_twit_loading',
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
				'must_have'			=> 'pro geo',
				'class'				=> 'fupi_load_opts',
				'is repeater'		=> false,
				'related_opts'		=> ['main', 'geo, ipdata_api_key, cf_worker_url, remember_geo'],
				'offscreen'				=> '<p>' . sprintf( esc_html__('Enter a list of 2-character %1$scountry codes%2$s separated by commas.', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://www.iban.com/country-codes">', '</a>' ) . '</p><p>'. esc_html__('Location is checked using the method chosen in the settings of the Geolocation module.', 'full-picture-analytics-cookie-notice' ) . '</p>',
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

	// DATA COLLECTION

	array(
		'section_id' => 'fupi_twit_basic',
		'section_title' => esc_html__( 'Data collection', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__( 'Send user email to X / Twitter for improved conversion tracking', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'enhanced_conv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'offscreen'				=> '<p>' . esc_html__( 'When this settings is enabled, WP Full Picture will send to Twitter / X encrypted email addresses of your visitors (when they browse the site while being logged in or when they make a purchase).', 'full-picture-analytics-cookie-notice' ) . '</p>',
			)
		),
	),

	// SINGLE EVENTS

	array(
		'section_id' => 'fupi_twit_single',
		'section_title' => esc_html__( 'Standard events', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(

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
						'label'				=> esc_html__( 'Send an event with ID', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send an event with ID', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send an event with ID', 'full-picture-analytics-cookie-notice' ),
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
						'label'				=> esc_html__( 'Send an event with ID', 'full-picture-analytics-cookie-notice' ),
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
	
	// WOOCOMMERCE
	
	array(
		'section_id' => 'fupi_twit_ecomm',
		'section_title' => esc_html__( 'WooCommerce tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( '"Purchase" event ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_purchase',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__( 'Event ID', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_purchase]',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( '"Checkout initiated" event ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_checkout',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__( 'Event ID', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_checkout]',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( '"Add to cart" event ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_addtocart',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__( 'Event ID', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_addtocart]',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( '"Add to wishlist" event ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_addtowishlist',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__( 'Event ID', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_addtowishlist]',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( '"Content view" event ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_woo_prodview',
				'must_have'			=> 'woo',
				'placeholder'		=> esc_html__( 'Event ID', 'full-picture-analytics-cookie-notice' ),
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[track_prodview]',
				'under_field'		=> esc_html__( '"Content view" is triggered every time your visitor displays a product page or a quick view window (on condition that it displays a standard WooCommerce "Add to cart" button and uses a standard WooCommerce hook.)', 'full-picture-analytics-cookie-notice' ),
			),
		),
	),
);


?>
