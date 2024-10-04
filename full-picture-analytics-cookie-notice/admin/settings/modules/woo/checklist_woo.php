<?php

    $tasks = [
        [
            'id' => 'requirements',
            'title' => esc_html__('Technical requirements to track WooCommerce', 'full-picture-analytics-cookie-notice' ),
        ],
    ];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double
    
    $popups_html = '
    <div id="fupi_requirements_popup" class="fupi_popup_content">
        <p>' . sprintf( esc_html__( 'To track WooCommerce events and data, your theme and plugins need to use standard %1$sWoocommerce hooks and HTML%2$s. Tracking will not work without them.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . '</p>
        <p>' . esc_html__('WP Full Picture uses WooCommerce hooks to output product data in the form that you want (e.g. products with or without tax). WooCommerce HTML classes help WP Full Picture recognize store elements that visitors can interact with.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . sprintf( esc_html__( 'All tracked WooCommerce events can be viewed and debugged in browser\'s console.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/woocommerce-tracking-requirements/">', '</a>' ) . ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/">' . esc_html__( 'Learn how to do it', 'full-picture-analytics-cookie-notice' ) . '</a>.</p>
    </div>';
?>