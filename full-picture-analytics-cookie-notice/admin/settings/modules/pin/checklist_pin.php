<?php

    $tasks = [
        [
            'id' => 'install',
            'title' => esc_html__('How to install Pinterest Tag', 'full-picture-analytics-cookie-notice' ),
        ],
    ];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double
    
    $popups_html = '

    <div id="fupi_install_popup" class="fupi_popup_content">
        <p>' . esc_html__('To install Pinterest Tag you need to provide its ID in the required form field on this page.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('Simply, go to your Pinterest dashboard > "Ads" > "Conversions". Pinterest Tag is in the table in the center of the screen.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>';
?>