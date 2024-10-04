<?php

    $tasks = [
        [
            'id' => 'impact',
            'title' => esc_html__('What is the impact of using a consent banner on traffic statistics and marketing', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'check_geo',
            'title' => esc_html__('How to check how consent banner behaves in different countries', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'manage_cook',
            'title' => esc_html__('How to manage tracking tools installed outside WP FP', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'track_excl',
            'title' => esc_html__('How to make sure that you are not tracked by your own website', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'consent_mode_101',
            'title' => esc_html__('How to use consent mode', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/how-to-enable-consent-mode-for-google-ads-analytics-and-gtm/',
        ],
        [
            'id' => 'consent_mode_check',
            'title' => esc_html__('How to check if consent mode works', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/how-to-check-if-googles-consent-mode-is-properly-installed/',
        ],
        [
            'id' => 'saving_consents',
            'title' => esc_html__('All you need to know about saving visitors\' consents', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/saving-user-consents-in-wp-full-picture/',
        ],
        [
            'id' => 'faq',
            'title' => esc_html__('Answers to frequent questions', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/cookie-notice-faq/',
        ],
        [
            'id' => 'cdb_faq',
            'title' => esc_html__('Before you start saving consents in external database', 'full-picture-analytics-cookie-notice' ),
        ],
    ];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double
        
    $popups_html = '
    <div id="fupi_impact_popup" class="fupi_popup_content">
        <p>' . esc_html__('Consent banners affect the amount of data collected by tools that track visitors\' personal information and/or information that can identify them.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('After you enable the consent banner, they will start tracking fewer visitors - the difference can be from 20% to even 80%.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('Tools that do not use cookies and do not track personal information will not be affected.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_check_geo_popup" class="fupi_popup_content">
        <p>' . esc_html__('WP FP lets you easily test how your consent banner behaves in different countries (when you set it to use one of the modes that use geolocation).', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('To do it, simply visit your webiste in incognito mode and add ?fp_set_country=[Country code] at the end of your website address. For example example.com/?fp_set_country=DE', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_manage_cook_popup" class="fupi_popup_content">
        <p>' . esc_html__('To comply with privacy regulations, all the tools that track your visitors need to be managed by WP Full Picture.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Install them using WP Full Picture\'s modules', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Control tools installed with other plugins using the Tracking Tools Manager module.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('If you display on your website content from other sites (e.g. YouTube videos, maps, etc.), then you should enable and set up the Iframes Manager module. This way WP FP’s consent banner will be able to load this content according to different privacy laws.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>

    <div id="fupi_track_excl_popup" class="fupi_popup_content">
        <p>' . esc_html__('WP Full Picture gives you a few methods to prevent being tracked.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <h3>' . esc_html__('Incorrect', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <p>' . esc_html__('Declining cookies in a consent banner. This will only turn off tracking tools that do not collect personal information about visitors.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <h3>' . esc_html__('Correct', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <p>' . esc_html__('Exclude specific users using one of the methods described in the "General Settings" page.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>';
?>