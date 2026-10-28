<?php

$offscreen_content = apply_filters( 'fupi_' . $module_id . '_get_offscreen_content', '' );

echo $offscreen_content . '

    <div id="fupi_easy_mode_info_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'Use this function to hide advanced settings and modules without disabling them.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_setup_mode_info_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'Setup helper lets you:', 'full-picture-analytics-cookie-notice' ) .'</p>
        <ol>
            <li>' . esc_html__( 'test your plugin and tracking configuration,', 'full-picture-analytics-cookie-notice' ) . '</li>
            <li>' . esc_html__( 'scan your website for possible tracking scripts.', 'full-picture-analytics-cookie-notice' ) . '</li>
        </ol>
        <p>' . esc_html__( 'After you enable it, visit your site and click the WP FP icon on the left side of the screen.', 'full-picture-analytics-cookie-notice' ) .' ' . sprintf( esc_html__('%1$sLearn more%2$s.', 'full-picture-analytics-cookie-notice'), ' <a href="https://wpfullpicture.com/support/documentation/debug-mode-features/" target="_blank">', '</a>') . '</p>
    </div>';

?>
