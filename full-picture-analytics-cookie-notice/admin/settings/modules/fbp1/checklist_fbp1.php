<?php

    $tasks = [
        [
            'id' => 'install_pixel',
            'title' => esc_html__('How to install Meta Pixel', 'full-picture-analytics-cookie-notice' ),
            'url'   => 'https://wpfullpicture.com/support/documentation/how-to-install-meta-pixel/'
        ],
        [
            'id' => 'custom_events',
            'title' => esc_html__('How to create powerful retargeting lists with custom events', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/how-to-use-advanced-triggers-to-measure-the-quality-of-traffic-and-traffic-sources/',
        ],
        [
            'id' => 'track_forms',
            'title' => esc_html__('How to track forms the right way', 'full-picture-analytics-cookie-notice' ),
            'url'   => 'https://wpfullpicture.com/support/documentation/how-to-choose-the-best-way-to-track-form-submissions/'
        ],
        [
            'id' => 'testing',
            'title' => esc_html__('How to test and debug your Meta Pixel setup', 'full-picture-analytics-cookie-notice' ),
            'url' => 'https://wpfullpicture.com/support/documentation/3-ways-to-test-and-debug-meta-pixel-integration/',
        ],
    ];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double
    
    // Linked from the checklist and settings form

    $popups_html = '

    <div id="fupi_install_popup" class="fupi_popup_content">
        <p>' . esc_html__( 'To install Meta Pixel you simply have to provide its pixel ID in the form. This is how you do it:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . sprintf( esc_html__( '%1$sVisit Meta\'s event\'s manager%2$s,', 'full-picture-analytics-cookie-notice' ), '<a href="https://www.facebook.com/events_manager2/" target="_blank">', '</a>') . '</li>
            <li>' . esc_html__( 'click green "+" icon on the left,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'choose "Web"', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'choose "Do it yourself" (when you see this option).', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'On the next screen choose "Conversion API and Meta Pixel" (for WP Full Picture PRO) or "Only Meta Pixel" (for the free version).', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Chooose "Set up manually" in the final screen.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Paste Pixel ID and Conversion API (for WP FP Pro) token in the form.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>';

    // Linked only from the checklist

    // Linked only from the plugin settings form

    $popups_html .= '

    <div id="fupi_servertrack_info_popup" class="fupi_popup_content">
        <p>' . esc_html__( 'Server tracking will increase the usage of your server. If you have issues with your server performance do not track less important events with Conversion API.', 'full-picture-analytics-cookie-notice') . '</p>
    </div>
    ';
?>