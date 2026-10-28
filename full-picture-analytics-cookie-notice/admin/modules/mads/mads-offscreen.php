<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double
    
    $offscreen_content = '
    <div id="fupi_dynamic_remarket_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__('To use dynamic remarketing on your website you need to:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Enable "WooCommerce Tracking" module', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . sprintf( esc_html__('Create and send to Microsoft Advertising a feed with your products. There are many plugins that can do that. We found %1$sCTX Feed%2$s to be easy to use and reliable.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wordpress.org/plugins/webappick-product-feed-for-woocommerce/" target="_blank">', '</a>' ) . '</li>
        </ol>
        <p>' . esc_html__('The "WooCommerce Tracking" module will automatically collect and send to MS Ads information about products that your visitors viewed, added to cart, had in cart while checking out and purchased.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('This information, combined with product data from the product feed, will let you use Dynamic Ads for remarketing purposes.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_woo_track_conv_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__('To track WooCommerce events as conversions, please follow the steps below.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__( 'Enable and set up the WooCommerce Tracking module.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Log in to your Microsoft Advertising panel and create a conversion goal.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'As the "Goal Category" choose either "Purchase", "Add to cart", "Begin Checkout" or "Other".', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'As the "Goal type" choose "Event" .', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Specify a "conversion action":', 'full-picture-analytics-cookie-notice') . '
                <ol>
                    <li>' . esc_html__( '"woo purchase" for "Purchase" event,', 'full-picture-analytics-cookie-notice') . '</li>
                    <li>' . esc_html__( '"woo checkout" for "Begin checkout" event,', 'full-picture-analytics-cookie-notice') . '</li>
                    <li>' . esc_html__( '"woo add to cart" for "Add to cart" event,', 'full-picture-analytics-cookie-notice') . '</li>
                    <li>' . esc_html__( '"woo product view" or "woo list item view" for "Other" event.', 'full-picture-analytics-cookie-notice') . '</li>
                </ol>
            </li>
            <li>' . esc_html__( 'For events "Purchase", "Checkout" and "Add to cart" choose "Conversion value may vary" in the "Revenue" field.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'In the "Set up tagging" section choose "Yes, the UET tag was already installed[...]".', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Your choice in the "Install event tag" does not matter.', 'full-picture-analytics-cookie-notice') . '</li>
        </ol>
    </div>';
?>
