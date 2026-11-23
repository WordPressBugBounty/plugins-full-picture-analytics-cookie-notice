<?php

$option_arr_id = 'fupi_gtag';

$sections = array(

	// INSTALLATION

	array(
		'section_id' => 'fupi_gtag_general',
		'section_title' => esc_html__( 'Google tag settings', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
            array(
                'type'	 			=> 'toggle',
                'label' 			=> esc_html__( 'Enhanced Conversions', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'enh_conv',
                'must_have'         => 'pro',
                'class'             => 'fupi_adv',
                'option_arr_id'		=> $option_arr_id,
                'under_field'       => '<p><button type="button" class="fupi_faux_link fupi_open_offscreen" data-offscreen="fupi_gtag_enh_conv__offscreen">' . esc_html__( 'How to set it up', 'full-picture-analytics-cookie-notice' ) . '</button></p>',
                'offscreen'			=> '<p>' . esc_html__( 'Enhanced Conversions improves the accuracy of conversion tracking in Google Analytics #1, Google Analytics #2 and Google Ads by sending to Google your visitors\' personal information, like their email address, first and last name and physical address. This information is later used by Google to better match the conversions with specific users.', 'full-picture-analytics-cookie-notice' ) . '</p>
                <p>' . esc_html__( 'The setting applies to browser tracking and to Status-Based Order Tracking (Measurement Protocol). Personal data is sent only when the visitor consented to both statistics and marketing.', 'full-picture-analytics-cookie-notice' ) . '</p>
                <p>' . esc_html__( 'To implement Enhanced Conversions follow the steps below.', 'full-picture-analytics-cookie-notice' ) . '</p>
                <ol>
                    <li>' . esc_html__('Go to your Google Analytics panel > Admin (settings page) > Data Collection > and enable "User-provided data collection" ', 'full-picture-analytics-cookie-notice') . '</li>
                    <li>' . esc_html__('Enable "Enhanced Conversions" via Google Tag in your Google Ads account. You will find it in "Goals" > "Conversions" > "Settings" > "Enhanced conversions" > and select "Google tag" from the dropdown. If you can\'t see these menu elements, please switch to the new menu using the "Appearance" switch in the top.', 'full-picture-analytics-cookie-notice') . '</li>
                    <li>' . sprintf( esc_html__( 'Make sure you agree and comply with Google\'s %1$sCustomer Data policies%2$s, %3$sGoogle Ads Data Processing Terms%2$s and privacy law in your country.', 'full-picture-analytics-cookie-notice' ), '<a href="https://support.google.com/adspolicy/answer/7475709?sjid=6953114821919544275-EU">', '</a>', '<a href="https://business.safety.google/adsprocessorterms/">' ) . '</li>
                    <li>' . esc_html__('Add information to your privacy policy that you send personal user data to Google.', 'full-picture-analytics-cookie-notice' ) . '</li>
                </ol>',
            ),
            array(
                'type'	 			=> 'toggle',
                'label' 			=> esc_html__( 'Link decoration', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'url_passthrough',
                'option_arr_id'		=> $option_arr_id,
                'offscreen_important'			=> '<p>'.esc_html__('This will enable Google\'s "url_passthrough" feature for link decoration. It will add a Google\'s advertising identifier to all links on your website which will improve conversion tracking.','full-picture-analytics-cookie-notice').'</p>
                <p style="color: #d50000">'.esc_html__('Attention! Using link decoration is a legal grey area and may be illegal in countries where consent before tracking is necessary (opt-in). Use at your own risk.','full-picture-analytics-cookie-notice').'</p>
                <p style="color: #d50000">'.esc_html__('Attention! Link decoration may, in very rare cases, cause problems on a website. To test it, visit your website from a Google advertisement and finish the whole conversion path.','full-picture-analytics-cookie-notice').'</p>
                <p style="color: #d50000">'.esc_html__('Attention! Link decoration will only be used in countries where consent banner lets users decline tracking.','full-picture-analytics-cookie-notice').'</p>',
            ),
            array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Google Tag Gateway measurement path', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'custom_gateway',
                'must_have'         => 'pro',
                'class'             => 'fupi_adv',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[custom_gateway]',
				'placeholder'		=> esc_html__( '/unique_address', 'full-picture-analytics-cookie-notice'),
                'under_field'		=> esc_html__( 'Available only for websites proxied through CloudFlare', 'full-picture-analytics-cookie-notice' ) . ' <a href="https://wpfullpicture.com/support/documentation/how-to-enable-google-tag-gateway/" target="_blank">' . esc_html__( 'Learn how to set it up', 'full-picture-analytics-cookie-notice') . '</a>.',
				'offscreen'	        => '<p>' . esc_html__( 'Google Tag Gateway masks Google\'s tracking scripts to look as if they were loaded from your own domain. As a result, you get:', 'full-picture-analytics-cookie-notice') . '</p>
                <ol>
                    <li>' . sprintf( esc_html__( '%1$sMore data:%2$s This should increase the number of tracked users by up to 5 percent, since scripts loaded this way are less likely to be blocked by ad blockers.', 'full-picture-analytics-cookie-notice'), '<strong>', '</strong>' ) . '</li>
                    <li>' . sprintf( esc_html__( '%1$sBetter ad performance:%2$s you are tracking more conversions, leading to better campaign results.', 'full-picture-analytics-cookie-notice'), '<strong>', '</strong>' ) . '</li>
                </ol>',
			),
		),
	),
);

?>
