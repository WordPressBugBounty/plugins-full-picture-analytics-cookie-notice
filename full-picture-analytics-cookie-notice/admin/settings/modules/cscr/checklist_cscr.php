<?php

    $tasks = [
        [
            'id' => 'setup',
            'title' => esc_html__('How to install tools with this module', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'privacy',
            'title' => esc_html__('How to load tracking tools in compliance with privacy laws', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'impr_track',
            'p_id' => 'main',
            'title' => esc_html__('How to improve tracking accuracy', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'geo',
            'title' => esc_html__('How to load tracking tools only in specific countries', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'priv_policy_extras',
            'title' => esc_html__('How to list these tracking tools in your privacy policy', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'custom_data',
            'title' => esc_html__('How to use variables in scripts', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'testing',
            'title' => esc_html__('How to test if everything works fine', 'full-picture-analytics-cookie-notice' ),
        ]
    ];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double    

    $popups_html = '
    <div id="fupi_setup_popup" class="fupi_popup_content">
        <p>' . esc_html__('To install additional tools:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('log in to the dashboard of the tool you want to install,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('find its installation script,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('paste the script in the correct field,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('give it a name, a simple "Script ID" and fill in other fields (if they are available).', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
        <p>' . esc_html__('Attention! Paste only one script in every "Script" field. Click "+" button to add more fields where you can paste other scripts.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_privacy_popup" class="fupi_popup_content">
        <p>' . esc_html__('To load tracking tools in compliance with privacy laws:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('enable and set up the Consent Banner module,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('refresh this page. You will see new checkboxes under the "Script" field.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
        <p> ' . esc_html__('If you don\'t select any checkbox, the script will load with the pageload.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_geo_popup" class="fupi_popup_content">
        <p>' . esc_html__('To load tracking tools only in specific countries:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('enable the geolocation module,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('refresh this page. You will see new fields where you will be able to choose where the scripts should load.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>

    <div id="fupi_impr_track_popup" class="fupi_popup_content">
        <p>' . esc_html__('To improve tracking accuracy of all the tools installed with this module:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('go to the "General Settings" page > "Tracking accuracy tweaks" section,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('set up the options you find there. They will help you get better data.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>

    <div id="fupi_priv_policy_extras_popup" class="fupi_popup_content">
        <p>' . esc_html__('To include in your privacy policy an always-current list of tracking tools:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Enable and set up the "Privacy policy extras" module', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Return to this page and look for a field labeled "Name displayed by the [fp_info] shortcode"', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Enter there the name that you would like to display in your privacy policy.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>

    <div id="fupi_custom_data_popup" class="fupi_popup_content">
        <p>' . esc_html__('You can include in your custom scripts 3 types of data:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Static - like page titles, user login status, user country, page author names, etc.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Dynamic - last clicked element, submitted form, time on page, etc.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('WooCommerce - static and dynamic', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
        <p>' . esc_html__('You can find all this data in a JS object "fpdata". You can check the values it holds in browser console.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . sprintf( esc_html__('Dynamic data is accessible only after a visitor makes an action (like clicking a button) so all scripts that use this data need to be wrapped in %1$sWP FP\'s custom events functions%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/custom-events-available-in-full-picture/">', '</a>' ) . '</p>
    </div>
    
    <div id="fupi_testing_popup" class="fupi_popup_content">
        <p>' . esc_html__('To test if your tools are installed correctly', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Open incognito mode in your browser', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Turn off your ad blocker (if you have any)', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Turn off your VPN (if you have any)', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('(Optional) If you have a Consent Banner enabled, agree to cookies.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('(Optional) If you have geolocation enabled, make sure that you are not in a country excluded from tracking.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Open browser console (Click right mouse button anywhere on the page > Choose "Inspect element" > Click "Console" tab in the popup)', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Enter "fp.loaded" and press enter', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('If you can\'t see the Script ID name, click the "Force load" checkbox on this page and try again.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>';
?>