<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double
    
    $offscreen_content = '
    <div id="fupi_install_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__('All you need to do to install X Ads is:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
			<li>' . esc_html__( 'Login to', 'full-picture-analytics-cookie-notice') . ' <a href="https://ads.twitter.com" target="_blank">ads.twitter.com</a></li>
			<li>' . esc_html__( 'In the top menu click "Tools" > "Events manager"', 'full-picture-analytics-cookie-notice') . '</li>
			<li>' . esc_html__( 'If you have created a Pixel before you\'ll see it on the left. If not, click "Add event source" in the top right corner.', 'full-picture-analytics-cookie-notice') . '</li>
			<li>' . esc_html__( 'Copy Pixel ID from the "Install Pixel Code" page and enter it here, in the required field.', 'full-picture-analytics-cookie-notice') . '</li>
		</ol>
    </div>

    <div id="fupi_track_actions_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'To track these events you need to register them in X. As a result, you will get event IDs that you should paste in the fields below. This is the process:', 'full-picture-analytics-cookie-notice') . '</p>
        <ol>
            <li>' . esc_html__( 'Log in to ', 'full-picture-analytics-cookie-notice') . '<a href="https://ads.twitter.com" target="_blank">ads.twitter.com</a></li>
            <li>' . esc_html__( 'In the top menu click "Tools" > "Events manager"', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Click "Add events" button.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Give your event a name, e.g. Newsletter signup', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Choose "Lead" for tracking form submisions or "Custom" for everything else.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Set other settings according to your preferences.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'In the next step choose "Define event with code"', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'In the last step click "Tag manager" tab and copy the Event ID.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Paste that ID in the fields in the "Single events" section.', 'full-picture-analytics-cookie-notice') . '</li>
        </ol>
    </div>

    <div id="fupi_woo_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'To track WooCommerce events you need to register them in X. As a result, you will get event IDs that you should paste in the fields below. This is the process:', 'full-picture-analytics-cookie-notice') . '</p>
        <ol>
            <li>' . esc_html__( 'Log in to ', 'full-picture-analytics-cookie-notice') . '<a href="https://ads.twitter.com" target="_blank">ads.twitter.com</a></li>
            <li>' . esc_html__( 'In the top menu click "Tools" > "Events manager"', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Click "Add events" button.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Give your event a name, e.g. Checkout', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Choose event type (WP Full Picture supports: "Add to cart", "Add to wishlist", "Checkout initiated", "Content view" and "Purchase")', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Set other settings according to your preferences.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'In the next step choose "Define event with code"', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'In the last step click "Tag manager" tab and copy the Event ID.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Paste that ID in the field below and save settings.', 'full-picture-analytics-cookie-notice') . '</li>
        </ol>
        <p>' . esc_html__('Please remember, that tracking when products are added to a wishlist requires additional setup of the WooCommerce tracking module.','full-picture-analytics-cookie-notice') . '</p>
    </div>';
?>
