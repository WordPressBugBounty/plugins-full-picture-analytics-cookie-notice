<?php

$option_arr_id = 'fupi_main';

$siteURL = get_bloginfo('url') . '/';
$main_opts = get_option('fupi_main');
$magic_keyword = ! empty ( $main_opts['magic_keyword'] ) ? $main_opts['magic_keyword'] : 'tracking';

// MAIN SETTINGS

$other_settings = array(
	array(
		'type' 				=> 'email',
		'label' 			=> esc_html__( 'Send email notification when the plugin is deactivated', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'deactiv_email',
		'option_arr_id'		=> $option_arr_id,
		'placeholder'		=> sprintf( esc_html__( 'e.g. %1$s, etc.', 'full-picture-analytics-cookie-notice' ), 'john@example.com, peter@example.com' ),
		'under_field' 		=> esc_html__('Enter a comma separated list of email addresses where you want to send the email.','full-picture-analytics-cookie-notice'),
	),
	array(
		'type' 				=> 'text',
		'label' 			=> esc_html__( 'Change "WP Full Picture" menu item text to:', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'custom_menu_title',
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[custom_menu_title]',
	),
	array(
		'type' 				=> 'toggle',
		'label' 			=> esc_html__( 'Remove all WP Full Picture\'s settings on deactivation', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'clean_all',
		'option_arr_id'		=> $option_arr_id,
	),
);

if ( current_user_can('manage_options') ){
	array_push( $other_settings, 
		array(
			'type' 				=> 'user_search',
			'field_id' 			=> 'extra_users_2',
			'label' 			=> esc_html__( 'Let non-admin users view and save WP Full Picture\'s settings', 'full-picture-analytics-cookie-notice' ),
			'must_have'			=> 'pro',
			'option_arr_id' 	=> $option_arr_id,
			'offscreen_warning'			=> '<p class="fupi_warning_text">' . esc_html__( 'As a security measure, the edit rights cannot be given to users without the right to at least edit posts.', 'full-picture-analytics-cookie-notice' ) . '</p>',
		),
	);
}

array_push( $other_settings, 
	array(
		'type'	 			=> 'select',
		'label' 			=> esc_html__( 'Bot detection list', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'bot_list',
		'class'				=> 'fupi_adv',
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[bot_list]',
		'options'				=> array(
			'basic'			=> esc_html__( 'Basic list', 'full-picture-analytics-cookie-notice' ),
			'big'			=> esc_html__( 'Big list (slower, can identify common AI bots)', 'full-picture-analytics-cookie-notice' ),
			'none'			=> esc_html__( 'None', 'full-picture-analytics-cookie-notice' ),
		),
		'default'			=> 'basic',
		'under_field'		=> esc_html__( 'This makes sure that only humans are tracked. The basic list only checks for common indexing and content scraping bots. The big list also identifies common AI bots. Do not enable it if you want to track AI traffic (e.g. with AI Bot Activity feature in MS Clarity).','full-picture-analytics-cookie-notice' ),
	),
	array(
		'type'	 			=> 'select',
		'label' 			=> esc_html__( 'Method of communication with the server', 'full-picture-analytics-cookie-notice' ),
		'field_id' 			=> 'server_method',
		'class'				=> 'fupi_adv',
		'el_class'			=> 'fupi_condition',
		'el_data_target'	=> 'fupi_restpi_opts',
		'option_arr_id'		=> $option_arr_id,
		'label_for' 		=> $option_arr_id . '[server_method]',
		'options'				=> array(
			'rest'			=> esc_html__( 'Rest API (default)', 'full-picture-analytics-cookie-notice' ),
			'ajax'			=> esc_html__( 'AJAX', 'full-picture-analytics-cookie-notice' ),
		),
		'offscreen'				=> '<p>' . esc_html__( 'The method you choose here will be used to send data from the visitors\'s browser to your server. This is used for server-side tracking and for sending visitor consents.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'Rest API is generally faster and less resource-intensive.', 'full-picture-analytics-cookie-notice' ) . '</p>',
	)
);

// SECTIONS

$sections = array(

	// TRACKING EXCLUSIONS
	
	array(
		'section_id' => 'fupi_main_no_track',
		'section_title' => esc_html__( 'Do not track', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Do not track visitors who enter the site from this link:', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'magic_keyword',
				'el_class'			=> 'fupi_narrow_text_field',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[magic_keyword]',
				'before_field'		=> $siteURL . '?',
				'after_field'		=> '=off',
				'placeholder'		=> 'tracking',
				'format'			=> 'key',
				'default'			=> 'tracking',
				'under_field'		=> '<p><a href="' . get_bloginfo('url') . '/?' . $magic_keyword . '=off"><strong>' . get_bloginfo('url') . '/?' . $magic_keyword . '=off</strong></a></p>',
				'offscreen' 			=> '<p><strong>' . $magic_keyword . '=off</strong>: ' . esc_html__( 'disables tracking for this visitor until they turn it on again or reset tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p><strong>' . $magic_keyword . '=on</strong>: ' . esc_html__( 'brings tracking back to the state before it was turned off.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p><strong>' . $magic_keyword . '=reset</strong>: ' . esc_html__( 'clears visitor\'s consent choices and tracking-related cookies.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'roles_multi_checkbox',
				'label' 			=> esc_html__( 'Do not track logged-in users with these roles:', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'disable_for_roles',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[disable_for_roles]',
				'default'			=> 'administrator',
				'under_field'		=> esc_html__( 'Site administrators are never tracked. To make them temporarily trackable, please enable the Setup Helper in the top menu and enable testing in the front-end of your site.', 'full-picture-analytics-cookie-notice' ),
			),
		)
	),

	// GEOLOCATION
	
	array(
		'section_id' => 'fupi_main_geo',
		'section_title' => esc_html__( 'Geolocation', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'select',
				'label' 			=> esc_html__( 'Geolocation provider', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'geo',
				'option_arr_id'		=> $option_arr_id,
				'must_have'			=> 'pro',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_geo_cond',
				'options' 			=> array(
					''					=> esc_html__( 'Disabled', 'full-picture-analytics-cookie-notice'),
					'cf_non_user'		=> esc_html__( 'Cloudflare (for not-registered users)', 'full-picture-analytics-cookie-notice'),
					'cf_default'		=> esc_html__( 'Cloudflare (for registered users)', 'full-picture-analytics-cookie-notice'),
					'cf_worker'			=> esc_html__( 'Cloudflare (Worker method)', 'full-picture-analytics-cookie-notice'),
					'ip_api'			=> 'ipapi',
					'ipdata'			=> 'ipdata',
					'db_ip'				=> 'DB-IP',
					'cloudways'			=> 'Cloudways Geo IP',
				),
				'default'			=> 'none',
				'under_field'		=> '<p>' . esc_html__( 'All providers on this list offer free services but some of them require registration or extra setup.','full-picture-analytics-cookie-notice') . '</p>
				<ul>
					<li><a href="https://www.cloudflare.com/en-gb/">Cloudflare</a> ' . esc_html__( '(default) - Geolocation of visitor\'s country, free, registration not required', 'full-picture-analytics-cookie-notice') . '</li>
					<li><a href="https://www.cloudflare.com/en-gb/">Cloudflare</a> '. esc_html__( '(for registered users) - Geolocation of visitor\'s country, free, registration required', 'full-picture-analytics-cookie-notice') . '</li>
					<li><a href="https://www.cloudflare.com/en-gb/">Cloudflare</a> '. esc_html__( '(Worker method) - Geolocation of visitor\'s country, free, registration and extra setup required', 'full-picture-analytics-cookie-notice') . '</li>
					<li><a href="https://ipapi.co/">ipapi</a> - '. esc_html__( 'Geolocation of visitor\'s country & region, free and paid, free is limited to 1000 checks/day, registration not required', 'full-picture-analytics-cookie-notice') . '</li>
					<li><a href="http://ipdata.co/">ipdata</a> - '. esc_html__( 'Geolocation of visitor\'s country & region, free and paid, registration is always required, the free plan is limited to 1500 checks/day and non-commercial use', 'full-picture-analytics-cookie-notice') . '</li>
					<li><a href="https://db-ip.com/">DB_IP</a> - '. esc_html__( 'Geolocation of visitor\'s country, does not require registration (paid plans are not yet supported)', 'full-picture-analytics-cookie-notice') . '</li>
					<li><a href="https://www.cloudways.com/en/">Cloudways</a> - '. esc_html__( 'Geolocation of visitor\'s country. Only for Cloudways customers. Extra setup required. Attention! Some caching configurations may cache geo lookup result!', 'full-picture-analytics-cookie-notice') . '</li>
				</ul>',
			),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'Cloudflare Worker URL', 'full-picture-analytics-cookie-notice' ),
					'class'				=> 'fupi_sub fupi_geo_cond fupi_cond_val_cf_worker fupi_disabled',
					'must_have'			=> 'pro',
					'field_id' 			=> 'cf_worker_url',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[cf_worker_url]',
					'placeholder'		=> '',
					'under_field' 		=> sprintf(esc_html__('Enter worker script URL. %1s Learn how to find it%2s.', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/geolocation/" target="_blank">', '</a>'),
				),
				array(
					'type'	 			=> 'text',
					'label' 			=> esc_html__( 'ipdata API key', 'full-picture-analytics-cookie-notice' ),
					'class'				=> 'fupi_sub fupi_geo_cond fupi_cond_val_ipdata fupi_disabled',
					'must_have'			=> 'pro',
					'field_id' 			=> 'ipdata_api_key',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[ipdata_api_key]',
				),
			array(
				'type'	 			=> 'number',
				'label' 			=> esc_html__( 'Remember visitor\'s location for', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'remember_geo',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[show_to_countries]',
				'default'			=> '30',
				'after_field'		=> 'days',
				'offscreen' 			=> '<p>' . esc_html__( 'Default is 30 days. Set to a lower number if your visitors often browse from different countries. If set to 0, location will be checked at the beginning of each session. If a location wasn\'t found it will be checked again at the beginning of the next session and remembered for the specified number of days.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		)
	),

	// META TAGS

	array(
		'section_id' => 'fupi_main_meta',
		'section_title' => esc_html__( 'Meta tags', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__('Meta tags','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'meta_tags',
				'is_repeater'		=> true,
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . esc_html__('One tag per line. For security reasons, meta tags can only have parameters: "name", "content", "property", "charset" and "http-equiv".', 'full-picture-analytics-cookie-notice' ) . '</p>',
				'fields'			=> array(
					array(
						'type'				=> 'text',
						'field_id'			=> 'name',
						'class'				=> 'fupi_col_20_grow',
						'placeholder'		=> esc_html__('Name (for your information)', 'full-picture-analytics-cookie-notice'),
						'required'			=> true,
					),
					array(
						'type'				=> 'text',
						'field_id'			=> 'tag',
						'placeholder'		=> esc_html__('Tag <meta....>', 'full-picture-analytics-cookie-notice'),
						'required'			=> true,
					),
				),
			),
		),
	),

	// IMPORT/EXPORT SETTINGS
	array(
		'section_id' => 'fupi_main_importexport',
		'section_title' => esc_html__( 'Backups', 'full-picture-analytics-cookie-notice' ),
	),

	// PERFORMANCE

	array(
		'section_id' => 'fupi_main_perf',
		'section_title' => esc_html__( 'Compatibility', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__( 'Enable WP Rocket compatibility settings', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'wprocket_compat',
				'option_arr_id'		=> $option_arr_id,
			),
		),
	),

	// OTHER SETTINGS
	array(
		'section_id' => 'fupi_main_other',
		'section_title' => esc_html__( 'Other settings', 'full-picture-analytics-cookie-notice' ),
		'fields' => $other_settings
	),

	// FUPI SHORTCODE

	array(
		'section_id' => 'fupi_main_shortcode',
		'section_title' => esc_html__( '[fp_info] shortcode - deprecated', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__('Add more tracking tools to the list', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'extra_tools',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type'				=> 'text',
						'field_id'			=> 'name',
						'placeholder'		=> 'Tool (required)',
						'required'			=> true,
					),
					array(
						'type'				=> 'url',
						'field_id'			=> 'url',
						'placeholder'		=> 'Privacy policy link (optional)',
					),
				),
			),
		)
	),
);

?>
