<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double

    $offscreen_content = '
    
    <div id="fupi_mpapi_key_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'To get Measurement Protocol API secret key, go to your Google Analytics account and follow these instructions:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__( 'Click "Admin" in the bottom-left corner of the screen', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Click "Data Streams"', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Click the first entry in the table', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Click "Measurement Protocol API secrets"', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'Click "Create"', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>';
?>
