<?php

$option_arr_id = 'fupi_tools';
$ga42_cond_info_text = '';
// TOOLS
$tools_fields = array(
    // Crazy Egg
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/crazyegg_fav.png" aria-hidden="true">Crazy Egg</div>',
        'field_id'      => 'cegg',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Crazy Egg',
        'offscreen'     => '<p>' . esc_html__( 'Crazy Egg let\'s you:', 'full-picture-analytics-cookie-notice' ) . '</p>
		<ol>
			<li>' . esc_html__( 'record how your users interact with your website,', 'full-picture-analytics-cookie-notice' ) . '</li>
			<li>' . esc_html__( 'see where they click the most and the least,', 'full-picture-analytics-cookie-notice' ) . '</li>
			<li>' . esc_html__( 'and test multiple versions of your content (to see which performs better).', 'full-picture-analytics-cookie-notice' ) . '</li>
		</ol>
		<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/crazy-egg/" class="button-secondary">', '</a>' ) . '</p>
		<p>' . sprintf( esc_html__( '%1$sVisit Crazy Egg website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://www.crazyegg.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Google Ads
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/gads_fav.png" aria-hidden="true">Google Ads</div>',
        'field_id'      => 'gads',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Google Ads',
        'offscreen'     => '<p>' . esc_html__( 'Google Ads integration allows you to track conversions from your Google advertising campaigns.', 'full-picture-analytics-cookie-notice' ) . '</p>
		<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/google-ads/" class="button-secondary">', '</a>' ) . '</p>
		<p>' . sprintf( esc_html__( '%1$sVisit Google Ads website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://ads.google.com/intl/en/home/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // GA 4
    array(
        'type'           => 'toggle',
        'label'          => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/analytics_fav.png" aria-hidden="true">Google Analytics</div>',
        'field_id'       => 'ga41',
        'option_arr_id'  => $option_arr_id,
        'el_class'       => 'fupi_condition',
        'el_data_target' => 'fupi_ga4_subs',
        'el_data_name'   => 'Google Analytics',
        'offscreen'      => '<p>' . esc_html__( 'Google Analytics is the most popular free website analytics software.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'You can use Google Analytics to measure and analyze your traffic, user behavior and performance of your WooCommerce store.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'Google Analytics can also send data to Google Ads, making a separate Google Ads tag no longer needed.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/google-analytics-integration-for-wordpress/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Google Analytics website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://marketingplatform.google.com/about/analytics/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // GA 4 #2
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/analytics_fav.png" aria-hidden="true">Google Analytics #2</div>',
        'field_id'      => 'ga42',
        'option_arr_id' => $option_arr_id,
        'class'         => 'fupi_sub fupi_do_not_hide fupi_ga4_subs fupi_gtag_subs',
        'must_have'     => 'pro_round',
        'under_field'   => $ga42_cond_info_text,
        'el_data_name'  => 'Google Analytics #2',
        'offscreen'     => '<p>' . esc_html__( 'This module adds a 2nd integration of Google Analytics.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/google-analytics-integration-for-wordpress/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // GTM
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/gtm_fav.png" aria-hidden="true">Google Tag Manager</div>',
        'field_id'      => 'gtm',
        'class'         => 'fupi_adv',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'GTM',
        'offscreen'     => '<p>' . esc_html__( 'GTM is an advanced tool for installing custom scripts and tracking tools.', 'full-picture-analytics-cookie-notice' ) . '<p>
			<p>' . esc_html__( 'If you only want to install something, then using the "Custom Integrations" module will be much easier.', 'full-picture-analytics-cookie-notice' ) . '<p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/google-tag-manager/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Google Tag Manager website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://marketingplatform.google.com/about/tag-manager/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Hotjar
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/hotjar_fav.png" aria-hidden="true">Hotjar</div>',
        'field_id'      => 'hotj',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Hotjar',
        'offscreen'     => '<p>' . esc_html__( 'Hotjar is a business-oriented tool that lets you:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'record how your users interact with your website,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'see where they click the most and the least,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and collect user feedback.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/hotjar/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Hotjar website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://www.hotjar.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Inspectlet
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/inspectlet_fav.png" aria-hidden="true">Inspectlet</div>',
        'field_id'      => 'insp',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Inspectlet',
        'offscreen'     => '<p>' . esc_html__( 'Inspectlet allows you to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'record how your users interact with your website,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'see where they click the most and the least,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'collect user feedback,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and test multiple versions of your content to see which performs better.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/inspectlet/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Inspectlet website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://www.inspectlet.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // LinkedIn
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/linkedin-fav.png" aria-hidden="true">LinkedIn Insight</div>',
        'field_id'      => 'linkd',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'LinkedIn',
        'offscreen'     => '<p>' . esc_html__( 'LinkedIn integration allows you to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'build lists of visitors who may be interested in your offer (to show them ads on Linked In)', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and measure the conversions of those ads.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/linkedin-insight-tag/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit LinkedIn Insight website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://business.linkedin.com/marketing-solutions/cx/21/10/insight-tag" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Matomo
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/matomo_fav.png" aria-hidden="true">Matomo</div>',
        'field_id'      => 'mato',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Matomo (basic)',
        'offscreen'     => '<p>' . esc_html__( 'This module lets you install Matomo on your website. It supports Matomo on-premise and cloud versions.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . esc_html__( 'Matomo is a powerful Google Analytics alternative. It is similar to Google Analytics UA but it is slightly easier to use. Matomo can import GA UA\'s data and it can work as its replacement.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/matomo/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Matomo website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://matomo.org" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Meta Pixel
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/meta_fav.png" aria-hidden="true">Meta Pixel</div>',
        'field_id'      => 'fbp1',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Meta Pixel (Facebook / Instagram)',
        'offscreen'     => '<p>' . esc_html__( 'Meta Pixel lets you:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'build lists of visitors who may be interested in your offer (for Facebook and Instagram ads)', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and measure the conversions from Facebook and Instagram advertising campaigns.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/meta-pixel/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Microsoft Advertising
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/microsoft_fav.png" aria-hidden="true">Microsoft Advertising</div>',
        'field_id'      => 'mads',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'MS Ads',
        'offscreen'     => '<p>' . esc_html__( 'Microsoft Advertising integration allows you to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'build lists of visitors who may be interested in your offer - to show them ads on Bing, Yahoo and other search engines', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and measure the conversions of those ads.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/microsoft-advertising/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Microsoft Advertising website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://ads.microsoft.com/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Microsoft Clarity
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/microsoft_fav.png" aria-hidden="true">Microsoft Clarity</div>',
        'field_id'      => 'clar',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'MS Clarity',
        'offscreen'     => '<p>' . esc_html__( 'Microsoft Clarity lets you:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'collect basic traffic statistics,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'record how your users interact with your website (see where they click the most and the least).', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/microsoft-clarity/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Microsoft Clarity website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://clarity.microsoft.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Pinterest
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/pinterest_fav.png" aria-hidden="true">Pinterest Tag</div>',
        'field_id'      => 'pin',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Pinterest',
        'offscreen'     => '<p>' . esc_html__( 'Pinterest Tag integration allows you to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'build lists of visitors who may be interested in your offer (to show them ads on Pinterest)', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and measure the conversions of those ads.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/pinterest-ads/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Pinterest Analytics website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://analytics.pinterest.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Plausible
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/plausible_fav.png" aria-hidden="true">Plausible</div>',
        'field_id'      => 'pla',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Plausible',
        'offscreen'     => '<p>' . esc_html__( 'Plausible is a simple (but not basic) tracking tool. It can:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'track pageviews and events (clicks, form submissions, etc),', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'can be configured to bypass adblockers,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'does not use cookies,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'lets you measure how much traffic is not tracked by other tools,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'lets you view your website statistics directly in your Wordpress admin panel.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/plausible-analytics/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Plausible Analytics website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://plausible.io" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // TikTok Pixel
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/tiktok_fav.png" aria-hidden="true">TikTok Pixel</div>',
        'field_id'      => 'tik',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'TikTok Pixel',
        'offscreen'     => '<p>' . esc_html__( 'TikTok Pixel allows you to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'build lists of visitors who may be interested in your offer - to show them ads on TikTok', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and measure the conversions of those ads.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/tiktok-pixel/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit TikTok for Business website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://getstarted.tiktok.com/eu-go-tiktok?lang=en" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // X / Twitter
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/x_ico.png" aria-hidden="true">X Ads (Twitter Ads)</div>',
        'field_id'      => 'twit',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'X Ads',
        'offscreen'     => '<p>' . esc_html__( 'X Ads (previously Twitter Ads) integration allows you to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'build lists of visitors who may be interested in your offer (to show them ads on X)', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'and measure the conversions of those ads.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/x-ads-twitter-ads/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit X for Business website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://business.twitter.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // PostHog
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/posthog_fav.png" aria-hidden="true">PostHog <span style="font-size: .9em; display: block;">(' . esc_html__( 'basic integration', 'full-picture-analytics-cookie-notice' ) . ')</span></div>',
        'field_id'      => 'posthog',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Posthog',
        'offscreen'     => '<p>' . esc_html__( 'PostHog is a product analytics tool. It is best suited to analyze web applications and platforms. PostHog lets you collect traffic statistics, automatically capture events, record user sessions, do A/B tests and more.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/posthog/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit PostHog website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://posthog.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Simple Analytics
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/simple_analytics-fav.png" aria-hidden="true">Simple Analytics <span style="font-size: .9em; display: block;">(' . esc_html__( 'basic integration', 'full-picture-analytics-cookie-notice' ) . ')</span></div>',
        'field_id'      => 'simpl',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Simple Analytics',
        'offscreen'     => '<p>' . esc_html__( 'Simple Analytics is a basic tracking tool that:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'can import data from Google Analytics UA (Universal Analytics),', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'can be configured to bypass adblockers,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'does not use cookies (and so it does not require a consent banner),', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'lets you measure how much traffic is not tracked by other tools,', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/simple-analytics/" class="button-secondary">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sVisit Simple Analytics website%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://www.simpleanalytics.com" class="button-secondary">', '</a>' ) . '</p>',
    ),
    // Custom integrations
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/custom_scr_ico.png" aria-hidden="true">' . esc_html__( 'Custom Integrations', 'full-picture-analytics-cookie-notice' ) . '</div>',
        'field_id'      => 'cscr',
        'option_arr_id' => $option_arr_id,
        'el_data_name'  => 'Custom Integrations',
        'offscreen'     => '<p>' . esc_html__( 'This module lets you easily install tools that track your visitors and use them in compliance with privacy regulations. You can install analytics tools, live chat applications, scripts from CRM or marketing automation/email platforms and others.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/custom-scripts/" class="button-secondary">', '</a>' ) . '</p>',
    ),
);
$extra_tools_fields = apply_filters( 'fupi_add_integr_module_switch', [] );
// ! ADDON
$tools_fields = array_merge( $tools_fields, $extra_tools_fields );
array_push( $tools_fields, array(
    'type'               => 'button',
    'label'              => '',
    'class'              => 'fupi_addons_row',
    'screen_reader_text' => esc_html__( 'View add-ons', 'full-picture-analytics-cookie-notice' ),
    'href'               => 'https://wpfullpicture.com/addons/',
    'target'             => '_blank',
    'el_class'           => 'fupi_addons_btn',
    'field_id'           => 'customize_notice_btn',
    'option_arr_id'      => $option_arr_id,
) );
//
// PRIVACY
//
$privacy_fields = array(array(
    'type'          => 'toggle',
    'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/cookie_icon.png" aria-hidden="true">' . esc_html__( 'Consent Management', 'full-picture-analytics-cookie-notice' ) . '</div>',
    'field_id'      => 'cook',
    'option_arr_id' => $option_arr_id,
    'offscreen'     => '<p>' . esc_html__( 'This module displays a consent banner, helps you use tracking tools according to privacy regulations and can record proofs of visitors\' consents.', 'full-picture-analytics-cookie-notice' ) . '</p>',
), array(
    'type'              => 'toggle',
    'label'             => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/fonts_ico.png" aria-hidden="true">' . esc_html__( 'Safe Fonts', 'full-picture-analytics-cookie-notice' ) . '</div>',
    'field_id'          => 'safefonts',
    'option_arr_id'     => $option_arr_id,
    'offscreen_warning' => '
			<p class="fupi_warning_text">' . esc_html__( 'This module has no settings page.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( 'Once enabled, it will try to replace links to Google Fonts (which are not GDPR compliant) with links to the same fonts hosted by GDPR compliant %1$sBunny Fonts%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://fonts.bunny.net" target="_blank">', '</a>' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sFollow this guide%2$s, if some Google Fonts still remain unchanged.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-replace-google-fonts-with-gdpr-compliant-fonts/" target="_blank">', '</a>' ) . '</p>
			<h3>' . esc_html__( 'Other information', 'full-picture-analytics-cookie-notice' ) . '</h3>
			<p>' . sprintf( esc_html__( '%1$sHere%2$s you can check if your website uses any Google Fonts.', 'full-picture-analytics-cookie-notice' ), '<a href="https://fontsplugin.com/google-fonts-checker/" target="_blank">', '</a>' ) . '</p>',
));
// $priv_tools_fields = apply_filters( 'fupi_add_privacy_module_switch', [] ); // ! ADDON
// $privacy_fields = array_merge( $privacy_fields, $priv_tools_fields );
// array_push( $privacy_fields, array(
// 	'type'	 			=> 'button',
// 	'label' 			=> '',
// 	'class'				=> 'fupi_addons_row',
// 	'screen_reader_text'=> esc_html__( 'View add-ons', 'full-picture-analytics-cookie-notice' ),
// 	'href'				=> 'https://wpfullpicture.com/addons/',
// 	'target'			=> '_blank',
// 	'el_class'			=> 'fupi_addons_btn',
// 	'field_id' 			=> 'customize_notice_btn',
// 	'option_arr_id'		=> $option_arr_id,
// ) );
//
// EXTENSIONS
//
$woo_disabled_class = ( function_exists( 'WC' ) ? '' : ' fupi_disabled fupi_do_not_hide' );
$woo_disabled_text = ( function_exists( 'WC' ) ? false : esc_html__( 'Requires: WooCommerce plugin', 'full-picture-analytics-cookie-notice' ) );
$extensions_fields = array(
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/reports_ico.png" aria-hidden="true">' . esc_html__( 'Analytics Dashboards', 'full-picture-analytics-cookie-notice' ) . '</div>',
        'field_id'      => 'reports',
        'option_arr_id' => $option_arr_id,
        'offscreen'     => '<p>' . esc_html__( 'This extension lets you add to your WP admin, analytics & marketing dashboards created with Google Looker Studio, Databox or similar platforms.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/analytics-dashboards/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/woo_fav.png" aria-hidden="true">' . esc_html__( 'WooCommerce Tracking', 'full-picture-analytics-cookie-notice' ) . '</div>',
        'field_id'      => 'woo',
        'option_arr_id' => $option_arr_id,
        'class'         => $woo_disabled_class,
        'under_field'   => $woo_disabled_text,
        'offscreen'     => '<p>' . esc_html__( 'This extension lets you track WooCommerce events and product data with your installed tracking tools (all extended integrations and the GTM module support it).', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/woocommerce-tracking/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/engagement_ico.png" aria-hidden="true">' . esc_html__( 'Engagement tracking', 'full-picture-analytics-cookie-notice' ) . '</div>',
        'field_id'      => 'vscor',
        'option_arr_id' => $option_arr_id,
        'class'         => 'fupi_adv fupi_do_not_hide',
        'must_have'     => 'pro_round',
        'offscreen'     => '<p>' . esc_html__( 'Use this module to:', 'full-picture-analytics-cookie-notice' ) . '</p>
			<ol>
				<li>' . esc_html__( 'measure how engaged your visitors are with your website,', 'full-picture-analytics-cookie-notice' ) . '</li>
				<li>' . esc_html__( 'see which traffic sources and ad campaigns bring you the most and the least engaged visitors.', 'full-picture-analytics-cookie-notice' ) . '</li>
			</ol>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/features/lead-scoring/" class="button-secondary">', '</a>' ) . '</p>',
    ),
    array(
        'type'          => 'toggle',
        'label'         => '<div class="fupi_field_title_wrap"><img class="fupi_label_icon" src="' . FUPI_URL . 'admin/assets/img/replace_ico.png" aria-hidden="true">' . esc_html__( 'Reactions', 'full-picture-analytics-cookie-notice' ) . '</div>',
        'field_id'      => 'reactions',
        'option_arr_id' => $option_arr_id,
        'class'         => 'fupi_do_not_hide fupi_adv',
        'must_have'     => 'pro_round',
        'under_field'   => esc_html__( 'Enable Custom Triggers module', 'full-picture-analytics-cookie-notice' ),
        'offscreen'     => '<p>' . esc_html__( 'This module lets you personalize your website to every visitor, by reacting to their actions.', 'full-picture-analytics-cookie-notice' ) . '</p>
		<p>' . esc_html__( 'Technically speaking, it lets you trigger JavaScript functions and change HTML classes after visitors take specific actions.', 'full-picture-analytics-cookie-notice' ) . '</p>
			<p>' . sprintf( esc_html__( '%1$sLearn more about this module%2$s', 'full-picture-analytics-cookie-notice' ), '<a target="_blank" href="https://wpfullpicture.com/module/reactions/" class="button-secondary">', '</a>' ) . '</p>',
    )
);
// $ext_tools_fields = apply_filters( 'fupi_add_ext_module_switch', [] ); // ! ADDON
// $extensions_fields = array_merge( $extensions_fields, $ext_tools_fields );
// array_push( $extensions_fields, array(
// 	'type'	 			=> 'button',
// 	'label' 			=> '',
// 	'class'				=> 'fupi_addons_row',
// 	'screen_reader_text'=> esc_html__( 'View add-ons', 'full-picture-analytics-cookie-notice' ),
// 	'href'				=> 'https://wpfullpicture.com/addons/',
// 	'target'			=> '_blank',
// 	'el_class'			=> 'fupi_addons_btn',
// 	'field_id' 			=> 'customize_notice_btn',
// 	'option_arr_id'		=> $option_arr_id,
// ) );
// ALL TOGETHER
$sections = array(
    // INTEGRATIONS
    array(
        'section_id'    => 'fupi_tools_integrations',
        'section_title' => esc_html__( 'Integrations', 'full-picture-analytics-cookie-notice' ),
        'fields'        => $tools_fields,
    ),
    // PRIVACY
    array(
        'section_id'    => 'fupi_tools_privacy',
        'section_title' => esc_html__( 'Privacy', 'full-picture-analytics-cookie-notice' ),
        'fields'        => $privacy_fields,
    ),
    // EXTENSIONS
    array(
        'section_id'    => 'fupi_tools_ext',
        'section_title' => esc_html__( 'Extensions', 'full-picture-analytics-cookie-notice' ),
        'fields'        => $extensions_fields,
    ),
);