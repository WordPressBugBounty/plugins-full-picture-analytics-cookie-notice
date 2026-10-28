<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double
    
    $offscreen_content = '<div id="fupi_howtouseit_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__('We highly recommend that you do not install Google\'s tools (Analytics and Ads) using different methods.', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__('You should either install them using WP FP, GTM or a different plugin, but do not mix them. For example, do not install Google Analytics with GTM and Ads with WP Full Picture.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>';
?>
