<?php

    $tasks = [
        [
            'id' => 'install',
            'title' => esc_html__('How to install MS Clarity?', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'chrome_extens',
            'title' => esc_html__('How to use “Microsoft Clarity Live” Chrome extension', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/how-to-use-microsoft-clarity-live-chrome-extension/',
        ],
        [
            'id' => 'privacy',
            'title' => esc_html__('How to use MS Clarity in compliance with privacy laws?', 'full-picture-analytics-cookie-notice' ),
        ],
        [
            'id' => 'track_forms',
            'title' => esc_html__('How to correctly track forms?', 'full-picture-analytics-cookie-notice' ),
            'url'   => 'https://wpfullpicture.com/support/documentation/how-to-choose-the-best-way-to-track-form-submissions/'
        ],
    ];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double
    
    // Linked from the checklist and settings form

    $popups_html = '

    <div id="fupi_install_popup" class="fupi_popup_content">
        <p><strong>' . esc_html__( 'To install MS Clarity, fill in the project ID field. To get the Project ID:', 'full-picture-analytics-cookie-notice') . '</strong></p>
        <ol>
            <li>' . sprintf( esc_html__( 'visit %1$sclarity\'s dashboard%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://clarity.microsoft.com/projects/" target="_blank">', '</a>' ) . '</li>
            <li>' . esc_html__( 'create or open a project.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'You will find the ID in the page’s URL', 'full-picture-analytics-cookie-notice') . ' <code style="word-wrap: break-word;">(clarity.microsoft.com/projects/view/<strong>PROJECT_ID%</strong>/...)</code></li>
        </ol>
    </div>';

    // Linked only from the checklist

    $popups_html .= '

    <div id="fupi_privacy_popup" class="fupi_popup_content">
        <p>' . esc_html__('There are 2 ways to make MS Clarity comply with privacy laws.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <h3>' . esc_html__('Method #1. With the Consent Banner', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <p>' . esc_html__('Advantages: you get higher quality data', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('Disadvantages: you get less data because you can track only visitors that agree to cookies.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p><strong>' . esc_html__('How to use it', 'full-picture-analytics-cookie-notice' ) . '</strong></p>
        <p>' . esc_html__('Enable and set up the Consent Banner module.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <h3>' . esc_html__('Method #2. With consent mode', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <p>' . esc_html__('Advantages: you get more data because you can track visitors who agreed to cookies and those who did not.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('Disadvantages: your data is of worse quality', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p><strong>' . esc_html__('How to use it', 'full-picture-analytics-cookie-notice' ) . '</strong></p>
        <ol>
            <li><a href="https://clarity.microsoft.com/projects/">' . esc_html__( 'Visit Microsoft Clarity dashboard', 'full-picture-analytics-cookie-notice') . '</a></li>
            <li>' . esc_html__( 'Choose your project', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Go to "Settings" > "Setup" > "Advanced Settings" > "Cookies"', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Turn off cookies', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Enable the "Consent mode" on this page (it\'s in the "Installation" section)', 'full-picture-analytics-cookie-notice') . '</li>
        </ol>
    </div>';

    // Linked only from the plugin settings form

    $popups_html .= '';
?>