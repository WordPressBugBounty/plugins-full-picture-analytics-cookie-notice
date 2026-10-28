<?php

$siteURL = get_bloginfo('url') . '/';
$track_opts = get_option('fupi_track');
$magic_keyword = ! empty ( $track_opts['magic_keyword'] ) ? $track_opts['magic_keyword'] : 'tracking';
$option_arr_id = 'fupi_track';

$sections = array(

	// CLICK TRACKING

	array(

		'section_id' => 'fupi_track_click',
		'section_title' => esc_html__( 'Tracking clicks', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__('Wait for tracking to finish before redirecting to another page', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'link_click_delay',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . sprintf( esc_html__('Recommended for very fast websites that use multiple tracking tools. Attention. This may cause double-clicks on galleries or sliders that %1$sincorrectly use links as buttons%2$s.', 'full-picture-analytics-cookie-notice'), '<strong>', '</strong>' ) . '</p>',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__('Treat links to these files as download links', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'downl_file_formats',
				'placeholder'		=> 'e.g. pdf, doc, docx, xls, xlsx, txt',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[downl_file_formats]',
				'under_field'		=> '<p>' . sprintf( esc_html__('Enter comma-separated list of file formats (extensions) you want to track. Leave empty to use default: %1$s.', 'full-picture-analytics-cookie-notice'), 'pdf, doc, docx, txt, csv, key, pps, ppt, pptx, xls, xlsx, 7z, zip, rar, gz' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__('Reset engagement time counter after clicks in anchors', 'full-picture-analytics-cookie-notice'),
				'must_have'			=> 'pro',
				'class'				=> 'fupi_adv fupi_deprecated',
				'field_id' 			=> 'reset_timer_on_anchor',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>'. esc_html__('For single-page websites that use anchors for navigation', 'full-picture-analytics-cookie-notice') . '</p>',
				'offscreen'				=> '<p>'. esc_html__('This function works only in Google Analytics and Meta Pixel modules.', 'full-picture-analytics-cookie-notice') . '</p>
					<p>'. esc_html__('Engagement time will be tracked when users click navigation anchors. If this remains disabled, engagement time will be calculated for the whole page.', 'full-picture-analytics-cookie-notice') . '</p>
					<p><a href="https://wpfullpicture.com/support/documentation/how-engagement-time-is-calculated/">'. esc_html__('Learn more about engagement time', 'full-picture-analytics-cookie-notice') . '</a></p>',
			),
		),
	),

	// TRACKING FORM SUBMISSIONS

	array(
		'section_id' => 'fupi_track_forms',
		'section_title' => esc_html__( 'Tracking form submissions', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'number',
				'label' 			=> esc_html__('Do not track form submissions if they happen within:', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'formsubm_trackdelay',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[formsubm_trackdelay]',
				'default'			=> '3',
				'after_field' 		=> esc_html__('seconds after the page loads', 'full-picture-analytics-cookie-notice' ),
				'under_field'		=> '<p>' . esc_html__('This prevents tracking accidental clicks and submittions from bots. Default is 3 seconds.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		),
	),

	// TRACKING ELEMENT VIEWS

	array(
		'section_id' => 'fupi_track_views',
		'section_title' => esc_html__( 'Tracking when things show on screen', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__('Track dynamic page elements', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'use_mutation_observer',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . esc_html__('This lets you track content that is added to the page after it has loaded, e.g. popups, products or gallery images.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Set when elements are considered to be visible', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'intersections',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[intersections]',
				'placeholder'		=> 'Top Right Bottom Left',
				'under_field'		=> esc_html__( 'By default, elements are considered visible, when their top or bottom edge is 200 pixels inside the screen. Leave empty to use default values', 'full-picture-analytics-cookie-notice' ) . ' -200px 0px -200px 0px',
			),
		),
	),

	// TRACKING SCROLLS

	array(
		'section_id' => 'fupi_track_scrolls',
		'section_title' => esc_html__( 'Tracking scroll depth', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__('Track scrolls to:', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'scroll_depths',
				'placeholder'		=> 'e.g. 50,90',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[scroll_depths]',
				'after_field'		=> esc_html__('% of page height', 'full-picture-analytics-cookie-notice'),
				'under_field'		=> '<p>' . sprintf( esc_html__('Enter comma-separated list of scroll depths you want to track (in percent). Do not use a percentage symbol. Leave empty to use default: %1$s.', 'full-picture-analytics-cookie-notice'), '50, 90' ) . '</p>',
			),
				array(
					'type'	 			=> 'number',
					'label' 			=> esc_html__( 'Do not track scrolls smaller than:', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_scroll_min',
					'class'				=> 'fupi_adv fupi_sub',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_scroll_min]',
					'default'			=> '200',
					'after_field'		=> esc_html__( 'pixels.', 'full-picture-analytics-cookie-notice'),
					'under_field'		=> '<p>' . esc_html__( 'This prevents tracking scrolls on short pages, when even a very small scroll can reach the bottom of the page. Default is 200px.', 'full-picture-analytics-cookie-notice') . '</p>',
				),
				array(
					'type'	 			=> 'number',
					'label' 			=> esc_html__( 'Do not track scrolls in the first:', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'track_scroll_time',
					'class'				=> 'fupi_adv fupi_sub',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[track_scroll_time]',
					'default'			=> '5',
					'after_field'		=> esc_html__( 'seconds', 'full-picture-analytics-cookie-notice' ),
					'under_field'		=> '<p>' . esc_html__( 'This prevents tracking quick scrolls performed by visitors who want to see the contents of the page before devoting more time to read it.','full-picture-analytics-cookie-notice') . ' ' . esc_html__('Default is 5 seconds.', 'full-picture-analytics-cookie-notice') . '</p>',
				),
		),
	),

	// TRACKING VIRTUAL PAGEVIEWS

	array(
		'section_id' => 'fupi_track_virtualviews',
		'section_title' => esc_html__( 'Tracking virtual pageviews', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__('Treat URL change as a virtual pageview when:', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'virtual_pageviews',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[virtual_pageviews]',
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type' => 'select',
						'field_id' => 'compare',
						'options' => array(
							'contains' => esc_html__( 'URL contains', 'full-picture-analytics-cookie-notice' ),
							'does_not_contain' => esc_html__( 'URL does not contain', 'full-picture-analytics-cookie-notice' ),
						),
					),
					array(
						'type' => 'text',
						'placeholder' => esc_html__( 'URL part', 'full-picture-analytics-cookie-notice' ),
						'field_id' => 'url_part',
					),
				),
			),
		),
	),

	// PAGEVIEWS

	array(
		'section_id' => 'fupi_track_ref',
		'section_title' => esc_html__( 'Tracking Pageviews', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__('Track locations of broken links', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'track_404',
				'class'				=> 'fupi_adv',
				'el_class'			=> 'fupi_condition',
				'el_data_target'	=> 'fupi_redirect_404_opt',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . sprintf( esc_html__( 'Every time someone lands on a 404 page, WP Full Picture will add extra parameter to its URL with the location of the broken link. %1$sLearn more%2$s','full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-track-broken-links/" target="_blank">', '</a>' ) . '</p>',
			),
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__( 'Prevent attributing conversions to payment gateways', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'switch_ref',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type'				=> 'text',
						'label'				=> esc_html__('When this page is visited','full-picture-analytics-cookie-notice'),
						'placeholder'		=> esc_html__('e.g. Order confirmation page URL','full-picture-analytics-cookie-notice'),
						'field_id'			=> 'search',
						'required'			=> true,
					),
					array(
						'type'				=> 'text',
						'label'				=> esc_html__('Always report as traffic from','full-picture-analytics-cookie-notice'),
						'placeholder'		=> sprintf( esc_html__('e.g. Checkout page URL (has to start with %1$s)','full-picture-analytics-cookie-notice'), 'https://' ),
						'field_id'			=> 'replace',
						'required'			=> true,
					),
				),
				'under_field'		=> '<p><a href="https://wpfullpicture.com/support/documentation/how-to-get-better-traffic-sources-information/">' . esc_html__( 'How to set it up', 'full-picture-analytics-cookie-notice' ) . '</a></p>',
			),
			array(
				'type' 				=> 'multi_checkbox',
				'label' 			=> esc_html__( 'Combine similar URLs of referring domains', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'join_ref',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'options'			=> array(
					'fb'		=> 'Facebook',
					'insta'		=> 'Instagram',
					'pin'		=> 'Pinterest',
				),
				'under_field'		=> '<p><a href="https://wpfullpicture.com/support/documentation/how-to-get-better-traffic-sources-information/">' . esc_html__( 'Learn more', 'full-picture-analytics-cookie-notice' ) . '</a></p>',
			),
			array(
				'type'	 			=> 'multi_checkbox',
				'label' 			=> esc_html__( 'Try to recognize traffic from Android applications', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'auto_track_non_http',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'options' 			=> array(
					'google'				=> 'Google Search Bar',
					'facebook'				=> 'Facebook App',
					'pinterest'				=> 'Pinterest App',
					'telegram'				=> 'Telegram App',
					'slack'					=> 'Slack App',
					'tinder'		 		=> 'Tinder App',
					'linkedin'		 		=> 'LinkedIn App',
					'youtube'				=> 'YouTube App',
					'line'					=> 'Line Communicator App (JP)',
				),
				'under_field'		=> '<p>' . esc_html__('This function tries to recognize traffic from Android applications (that is often labeled as "Direct") and report it as coming from websites, e.g. Google Search Bar > Google.com, Facebook App > Facebook.com, etc.' , 'full-picture-analytics-cookie-notice') . '</p>
				<p>' . sprintf( esc_html__('Traffic from Line app will be marked as coming from %1$s.' , 'full-picture-analytics-cookie-notice'), 'https://line-android-app.jp/' ) . '</p>',
			),
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__( 'Make tracking tools correctly recognize traffic from other Android applications', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'track_non_http',
				'class'				=> 'fupi_r3_fullwidth fupi_adv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'type'				=> 'select',
						'label'				=> esc_html__('When traffic source','full-picture-analytics-cookie-notice'),
						'field_id'			=> 'compare',
						'options'			=> array(
							'start'				=> esc_html__('Starts with','full-picture-analytics-cookie-notice'),
							'eq'				=> esc_html__('Equals','full-picture-analytics-cookie-notice'),
							'incl'				=> esc_html__('Includes','full-picture-analytics-cookie-notice'),
						),
						'class'				=> 'fupi_col_20',
					),
					array(
						'type'				=> 'text',
						'label'				=> '',
						'placeholder'		=> 'e.g. android-app://com.google.android.googlequicksearchbox/',
						'field_id'			=> 'search',
						'required'			=> true,
					),
					array(
						'type'				=> 'text',
						'label'				=> esc_html__('Report as traffic from this address','full-picture-analytics-cookie-notice'),
						'placeholder'		=> esc_html__('e.g. android-g-search-box.com','full-picture-analytics-cookie-notice'),
						'field_id'			=> 'replace',
						'required'			=> true,
					),
				),
				'under_field'		=> '<p><a href="https://wpfullpicture.com/support/documentation/how-to-get-better-traffic-sources-information/">' . esc_html__( 'How to set it up', 'full-picture-analytics-cookie-notice' ) . '</a></p>',
			),
		),
	),

	// DATA TRACKING

	array(
		'section_id' => 'fupi_track_opt',
		'section_title' => esc_html__( 'Data tracking', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type'	 			=> 'r3',
				'label' 			=> esc_html__('Register metadata that you want to track', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'custom_data_ids',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'class'				=> 'fupi_fullwidth_tr fupi_adv',
				'is_repeater'		=> true,
				'fields'			=> array(
					array(
						'label'				=> esc_html__( 'Name', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'name',
						'required'			=> true,
						'el_class'			=> 'fupi_internal_title',
						'class'				=> 'fupi_col_25_grow',
					),
					array(
						'label'				=> esc_html__( 'Type', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'select',
						'field_id'			=> 'meta',
						'options'			=> array(
							'post'				=> esc_html__('Post meta', 'full-picture-analytics-cookie-notice'),
							'user'				=> esc_html__('User meta', 'full-picture-analytics-cookie-notice'),
							'term'				=> esc_html__('Term meta', 'full-picture-analytics-cookie-notice'),
						),
						'class'				=> 'fupi_col_15_grow',
					),
					array(
						'label'				=> esc_html__( 'ID', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'text',
						'field_id'			=> 'id',
						'placeholder'		=> esc_html__('Metadata ID', 'full-picture-analytics-cookie-notice'),
						'required'			=> true,
						'class'				=> 'fupi_col_20_grow',
					),
					array(
						'label'				=> esc_html__( 'Type of data', 'full-picture-analytics-cookie-notice' ),
						'type'				=> 'select',
						'field_id'			=> 'type',
						'options'			=> array(
							''					=> esc_html__('Select data type', 'full-picture-analytics-cookie-notice'),
							'str'				=> esc_html__('String', 'full-picture-analytics-cookie-notice'),
							'bool'				=> esc_html__('Boolean', 'full-picture-analytics-cookie-notice'),
							'int'				=> esc_html__('Integer', 'full-picture-analytics-cookie-notice'),
							'float'				=> esc_html__('Float num', 'full-picture-analytics-cookie-notice'),
						),
						'required'			=> true,
						'class'				=> 'fupi_col_20_grow',
					),
				),
				'under_field'			=> '<p>' . sprintf( esc_html__( 'Learn %1$show to track metadata%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-track-custom-meta-data-custom-fields/">', '</a>' ) . '</p><p style="color: red">' . esc_html__( 'Attention. Do not track information that is personal and/or can identify users.', 'full-picture-analytics-cookie-notice' ) . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__('Add "page label" field to page edit screen','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'page_labels',
				'class'				=> 'fupi_adv',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . sprintf( esc_html__('This lets you add "page type" label to pages of unknown purpose (other than a home page or a blog page) a type, e.g. offer, contact, deal, landing, etc. %1$sLearn more%2$s.', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/what-is-page-labeling-and-how-to-use-it/" target="_blank">', '</a>') . '</p>',
			),
			array(
				'type'	 			=> 'toggle',
				'label' 			=> esc_html__('Allow tracking IDs of content authors', 'full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'show_author_id',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'under_field'		=> '<p>' . esc_html__('Supported by: Google Analytics, Matomo and GTM.','full-picture-analytics-cookie-notice') . '</p>
				<p style="color: red">' . esc_html__('Do not enable this option if authors of content of your site have administrator rights. This will expose their IDs making attacks easier. Don\'t help attackers hack your site.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
				array(
					'type' 				=> 'text',
					'label' 			=> esc_html__( 'Use custom 404 page:', 'full-picture-analytics-cookie-notice' ),
					'field_id' 			=> 'redirect_404',
					'class'				=> 'fupi_sub fupi_disabled fupi_redirect_404_opt fupi_adv',
					'placeholder'		=> 'URL of a custom 404 page',
					'must_have'			=> 'pro',
					'option_arr_id'		=> $option_arr_id,
					'label_for' 		=> $option_arr_id . '[redirect_404]',
					'offscreen_warning'			=> '<p>' . esc_html__('Remember to noindex your custom 404 page from Google search results with your SEO plugin.','full-picture-analytics-cookie-notice') . '</li>
						</ol>',
				),
			array(
				'type'	 			=> 'taxonomies_multi_checkbox',
				'label' 			=> esc_html__( 'Track terms of these taxonomies', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'tracked_taxonomies',
				'class'				=> 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[tracked_taxonomies]',
				'under_field'		=> '<label><input type="checkbox" checked disabled>' . esc_html__('Post categories','full-picture-analytics-cookie-notice') . '</label>
				<div class="fupi_spacer"></div>
				<label><input type="checkbox" checked disabled>' . esc_html__('Post tags','full-picture-analytics-cookie-notice') . '</label>
				<div class="fupi_spacer"></div>
				<label><input type="checkbox" checked disabled>' . esc_html__('WooCommerce product categories','full-picture-analytics-cookie-notice') . '*</label>
				<p>* ' . esc_html__('WooCommerce product categories are tracked automatically as parameters to ecommerce events, like purchases, additions to cart, etc.', 'full-picture-analytics-cookie-notice') . '</p>'
			),
		)
	),
);

?>
