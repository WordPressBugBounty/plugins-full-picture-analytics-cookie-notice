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
    </div>
    
    <div id="fupi_adv_tracking_popup" class="fupi_popup_content">
        <h3>' . esc_html__('Advanced Order Tracking vs Standard Tracking', 'full-picture-analytics-cookie-notice' ) . '</h3>    
        <p>' . esc_html__( 'Typically, orders are tracked on the order confirmation page (a.k.a "Thank you" page). However, up to 30% of customers do not view this page, which leads to incomplete conversions data.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('Advanced Order Tracking (AOT) solves this problem by tracking orders based on their status. By default, WP Full Picture tracks orders with the statuses "Processing" and "On Hold."', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Processing - this is the status that orders get after they are succesfully paid for through gateways like PayPal, Stripe, and others.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('On hold - this is the default status for orders where customers select manual bank transfer as the payment method.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
        <p>' . esc_html__( 'In addition to tracking orders, AOT can also track refunds and cancellations in compatible tools. Currently, this feature is supported in Google Analytics.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <h3>' . esc_html__('Supported Tools', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <p>' . esc_html__( 'AOT is supported in Google Analytics and Meta Pixel.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <h3>' . esc_html__('How to Enable Advanced Order Tracking', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <ol>
            <li>' . esc_html__('Open the settings for the Google Analytics or Meta Pixel module.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Provide a server tracking key in the in the relevant field in the "Installation" section.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Enable AOT in the "WooCommerce Tracking" section of these modules.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
        <h3>' . esc_html__('Limitations', 'full-picture-analytics-cookie-notice' ) . '</h3>
        <p>' . esc_html__( 'AOT does not track orders added manually in the WooCommerce admin panel.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__( 'Partial refunds are not tracked. Only full refunds are tracked when the order status changes to "Refunded".', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>';
?>