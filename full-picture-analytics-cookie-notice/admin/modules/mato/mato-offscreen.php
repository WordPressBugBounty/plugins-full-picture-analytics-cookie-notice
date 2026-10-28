<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double
    
    $offscreen_content = '

    <div id="fupi_enable_ecomm_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__('To enable Ecommerce tracking in Matomo:', 'full-picture-analytics-cookie-notice' ) . '</p>
        <ol>
            <li>' . esc_html__('Log in to your Matomo panel.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Click the "Settings" icon (cog) in the top right corner.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('From the left menu choose "Websites" > "Manage".', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Click the "Edit" icon (pencil) next to the section with the website.', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__('Find the "Ecommerce" setting, choose "Ecommerce enabled" and save the settings.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
    </div>';
?>
