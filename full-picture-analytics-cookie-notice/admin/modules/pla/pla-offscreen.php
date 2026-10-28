<?php
    
    // Do not use IDs inside the "fupi_offscreen_source" wrappers
    // The wrapped HTML will be copied to the offscreen panel and IDs will double
    
    $offscreen_content = '

    <div id="fupi_installation_modes_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__( 'If you choose to install Plausible with WP Full Picture, you will be able to use unique functions of WP FP and track more things, e.g. user actions and data types. However, some ad blockers may stop Plausible from tracking visitors (unless you use a self-hosted Plausible).', 'full-picture-analytics-cookie-notice' ) . '</p>
        <p>' . esc_html__( 'If you choose to extend Plausible\'s own plugin, most ad blockers will not block tracking, but you will no longer be able to track that many things.', 'full-picture-analytics-cookie-notice')  . '</p>
    </div>

    <div id="fupi_differences_offscreen" class="fupi_offscreen_source">

        <p>' . esc_html__('Plausible gives you two ways to track user actions - as separate events (default) or single events with multiple properties.', 'full-picture-analytics-cookie-notice' ) . '</p>

        <p class="fupi_warning_text">' . esc_html__('The first method is simpler, can be used in funnels and does not require a Business plan. The second method is useful when you are tracking multiple events of the same type, but on different page elements.', 'full-picture-analytics-cookie-notice' ) . '</p>
        
        <p><strong>' . esc_html__('Example', 'full-picture-analytics-cookie-notice' ) . '</strong></p>

        <p>' . esc_html__('Let\'s say you want to track click on three different buttons - A, B and C.', 'full-picture-analytics-cookie-notice' ) . '</p>

        <p>' . esc_html__('If you track them with separate events, then you will see in Plausible three different events, like "Clicked button A", "Clicked button B" and "Clicked button C".', 'full-picture-analytics-cookie-notice' ) . '</p>

        <p>' . esc_html__('If you track them with a single event with multiple properties, then you will see in Plausible one event "Button Clicked" with a property "page_element" and value "Button A", "Button B" or "Button C".', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>

    <div id="fupi_setup_offscreen" class="fupi_offscreen_source">
        <p>' . esc_html__('To register events and their properties in Plausible you first need to register them in WP FP, then, you need to take all the actions you want to track on your website (only then, will they be sent to Plausible) and, finally, register them in Plausible\'s panel.', 'full-picture-analytics-cookie-notice' ) . '</p>
    </div>';
?>
