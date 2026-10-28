<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double
    
    // Linked from the checklist and settings form

    $offscreen_content = '

    <div id="fupi_warning_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'Before you start tagging recordings with events you need to know that:', 'full-picture-analytics-cookie-notice') . '</p>
        <ol>
            <li>' . esc_html__( 'Filtering recordings by events is available in Hotjar\'s paid plans.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'One recording can have NO MORE than 50 events associated with it. Events over this limit will be ignored.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'There can be a MAXIMUM of 1000 different events per site.', 'full-picture-analytics-cookie-notice') . '</li>
            <li>' . esc_html__( 'Pay attention to the number of events your site sends. Read descriptions of the settings below for more information regarding possible increase in the number of events that is sent to Hotjar.', 'full-picture-analytics-cookie-notice') . '</li>
        </ol>
    </div>';
?>
