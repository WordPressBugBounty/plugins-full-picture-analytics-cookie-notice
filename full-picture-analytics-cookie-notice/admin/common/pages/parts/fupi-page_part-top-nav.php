<?php /*<div style="background: lightyellow; padding: 15px; font-size: 18px; text-align: center;">
    <div>You are now using a Beta version of WP Full Picture. Please <a href="mailto:hello@wpfullpicture.com">report to us any issues</a> you come across. Thanks :)</div>
</div> */ ?>

<div id="fupi_top_bar">
    <?php 
    //
    // TOP NAV
    //
    ?>

    <?php
        
        $top_nav = '';

        $active_class = $module_id == 'tools' ? 'fupi_current' : '';
        $top_nav.= '<a class="fupi_top_nav_link ' . $active_class . '" href="' . admin_url('admin.php?page=full_picture_tools') . '">' . esc_html__('Modules', 'full-picture-analytics-cookie-notice') . '</a>';

        $active_class = $module_id == 'main' ? 'fupi_current' : '';
        $top_nav.= '<a class="fupi_top_nav_link ' . $active_class . '" href="' . admin_url('admin.php?page=full_picture_main') . '">' . esc_html__('General Settings', 'full-picture-analytics-cookie-notice') . '</a>';

        $top_nav.= '<button type="button" class="fupi_top_nav_link fupi_open_offscreen" data-offscreen="fupi_help_offscreen">' . esc_html__('Help', 'full-picture-analytics-cookie-notice') . '</button>';

        if ( ! empty( $top_nav ) ) echo '<div id="fupi_top_nav" class="top_menu_section">' . $top_nav . '</div>';
    ?>

    <?php

    //
    // SETUP HELPER
    //

    // The switch controls hiding, while the stored preference controls showing.

    if ( ! $user_adv_mode ) {
        $adv_mode_checked = 'checked="checked"';
    } else {
        $adv_mode_checked = '';
    }

    if ( empty ( $this->ver['debug'] ) ) {
        $setup_mode_checked = '';
        $hide_setup_notif = 'style="display: none;"';
    } else {
        $setup_mode_checked = 'checked="checked"';
        $hide_setup_notif = '';
    }

    echo '<div id="fupi_top_setup_info">
        
        <script> let fupi_setup_mode_nonce ="' . wp_create_nonce( 'fupi_update_modes_nonce' ) . '";</script>
        
        <div id="fupi_top_setup_info_adv">
            ' . esc_html__( 'Hide advanced settings', 'full-picture-analytics-cookie-notice' ) . '
            <label class="fupi_switch">
                <input type="checkbox" id="adv_mode_checkbox" value="1" ' . $adv_mode_checked . '>
                <span class="fupi_switch_slider"></span>
            </label>
            <button type="button" class="fupi_open_offscreen fupi_open_offscreen_i " data-offscreen="fupi_easy_mode_info_offscreen">?</button>
        </div>

        <div id="fupi_top_setup_info_setup">
            ' . sprintf( esc_html__( 'Setup helper', 'full-picture-analytics-cookie-notice' ), '<strong>', '</strong>' ) . '
            <label class="fupi_switch">
                <input type="checkbox" id="setup_mode_checkbox" value="1" ' . $setup_mode_checked . '>
                <span class="fupi_switch_slider"></span>
            </label>
            <button type="button" class="fupi_open_offscreen fupi_open_offscreen_i " data-offscreen="fupi_setup_mode_info_offscreen">?</button>
        </div>
    </div>';

    //
    // ACCOUNT / GET PRO NAV
    //

    $pro_nav = '';

    if ( fupi_fs()->can_use_premium_code() ) {
        
        // ACCOUNT

        $pro_nav .= '<a class="fupi_top_nav_link" target="_blank" href="https://wpfullpicture.com/account/"><span class="dashicons dashicons-admin-users"></span> ' . esc_html__( 'Account', 'full-picture-analytics-cookie-notice' ) . '</a>';
    }
    
    if ( fupi_fs()->is_not_paying() ) {
        
        // GET PRO
        $pro_nav .= '<a id="fupi_get_pro_link" class="fupi_top_nav_link" href="https://wpfullpicture.com/pricing/" target="_blank">' . esc_html__('Get Pro', 'full-picture-analytics-cookie-notice') . '</a>'; 
    }; 

    if ( ! empty( $pro_nav ) ) {
        echo '<div id="fupi_pro_nav" class="top_menu_section">' . $pro_nav . '</div>';
    }; ?>
</div>

<div id="setup_helper_notif_bar" <?php echo $hide_setup_notif; ?>>
    <span><?php echo sprintf( esc_html__( '%1$sSetup helper is active%2$s. %3$sVisit your site%4$s, click the %5$s icon and test your setup.', 'full-picture-analytics-cookie-notice' ), '<strong>', '</strong>', '<a href="/" target="_blank">', '</a>', '<img src="' . FUPI_URL . 'admin/assets/img/fp-ico.svg"><span class="fupi_srt">WP FP</span>' ); ?></span>
</div>
