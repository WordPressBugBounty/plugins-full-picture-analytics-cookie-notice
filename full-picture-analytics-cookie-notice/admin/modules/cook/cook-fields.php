<?php

$option_arr_id = 'fupi_cook';
$field_disabled = 'fupi_disabled';
$must_have_html = '<div class="fupi_must_have_pro_ico_round"><span class="dashicons dashicons-lock" aria-hidden="true"></span><a class="fupi_get_pro_text" href="https://wpfullpicture.com/pricing/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Get Pro', 'full-picture-analytics-cookie-notice' ) . '</a></div>';
// EXTRA MESSAGE IF THE PP IS NOT PUBLISHED
$priv_policy_url_text = '<p style="color: red;">' . esc_html__( 'Attention. Your privacy policy page is either not published or its ID is not set in the settings of the Consent Banner section', 'full-picture-analytics-cookie-notice' ) . '</p>';
if ( !empty( $this->settings['pp_id'] ) ) {
    $pp_id = (int) $this->settings['pp_id'];
    if ( get_post_status( $pp_id ) == 'publish' ) {
        $priv_policy_url_text = '';
    }
}
$current_theme = wp_get_theme();
$is_oceanWP_theme = $current_theme->get( 'Name' ) == 'OceanWP';
$has_geolocation = fupi_fs()->can_use_premium_code__premium_only() && !empty( $this->main ) && isset( $this->main['geo'] );
$geo_disabled_reason = esc_html__( 'Requires Geolocation', 'full-picture-analytics-cookie-notice' );
$consent_mode_options = array(
    'optin'       => array(
        'icon'        => 'dashicons-exit',
        'label'       => esc_html__( 'Opt-In Mode', 'full-picture-analytics-cookie-notice' ),
        'description' => esc_html__( 'Blocks tracking scripts and cookies before visitor\'s consent. This behavior is required in 80+ countries, including EU (GDPR), UK and Brazil and accepted in all others.', 'full-picture-analytics-cookie-notice' ),
    ),
    'optout'      => array(
        'icon'        => 'dashicons-undo',
        'label'       => esc_html__( 'Opt-Out Mode', 'full-picture-analytics-cookie-notice' ),
        'description' => esc_html__( 'Loads all tracking tools by default while giving visitors a clear way to decline tracking. Recommended for US states, like California (CPRA).', 'full-picture-analytics-cookie-notice' ),
    ),
    'notify'      => array(
        'icon'        => 'dashicons-megaphone',
        'label'       => esc_html__( 'Notification Only', 'full-picture-analytics-cookie-notice' ),
        'description' => esc_html__( 'Displays an informational banner informing visitors that cookies are in use. Use only in countries with minimal privacy regulations.', 'full-picture-analytics-cookie-notice' ),
    ),
    'auto_strict' => array(
        'icon'            => 'dashicons-shield',
        'label'           => esc_html__( 'Automatic Strict', 'full-picture-analytics-cookie-notice' ),
        'description'     => esc_html__( 'Geolocates visitors and automatically uses Opt-In, Opt-Out, or Notification mode that is required in local regulations. Designed for sites running marketing pixels.', 'full-picture-analytics-cookie-notice' ),
        'disabled'        => !$has_geolocation,
        'disabled_reason' => $geo_disabled_reason,
    ),
    'auto_lax'    => array(
        'icon'            => 'dashicons-shield-alt',
        'label'           => esc_html__( 'Automatic Lax', 'full-picture-analytics-cookie-notice' ),
        'description'     => esc_html__( 'Similar to Automatic Strict. Designed for sites that don\'t use their visitors\' data for ad targetting.', 'full-picture-analytics-cookie-notice' ),
        'disabled'        => !$has_geolocation,
        'disabled_reason' => $geo_disabled_reason,
    ),
    'manual'      => array(
        'icon'            => 'dashicons-location-alt',
        'label'           => esc_html__( 'Manual Geo-Targeting', 'full-picture-analytics-cookie-notice' ),
        'description'     => esc_html__( 'Lets you manually set Opt-In, Opt-Out, or Notification modes for individual countries and regions.', 'full-picture-analytics-cookie-notice' ),
        'disabled'        => !$has_geolocation,
        'disabled_reason' => $geo_disabled_reason,
    ),
);
// CONSENT BANNER FIELDS
$consent_management_fields = array(array(
    'type'           => 'card_radio',
    'label'          => esc_html__( 'How to manage tracking on this site', 'full-picture-analytics-cookie-notice' ),
    'field_id'       => ( $has_geolocation ? 'mode' : 'enable_scripts_after' ),
    'option_arr_id'  => $option_arr_id,
    'options'        => $consent_mode_options,
    'default'        => ( $has_geolocation ? 'auto_strict' : 'optin' ),
    'class'          => 'fupi_required fupi_fullwidth_tr fupi_cookie_notice_modes fupi_consent_mode_cards_row',
    'el_class'       => ( $has_geolocation ? 'fupi_condition' : '' ),
    'el_data_target' => ( $has_geolocation ? 'fupi_mode_cond' : '' ),
    'under_field'    => ( $has_geolocation ? '' : '<strong style="margin-top: 12px; display: block; text-align: center;">' . esc_html__( 'Enable Geolocation in the General Settings to access disabled modes.', 'full-picture-analytics-cookie-notice' ) . '</strong>' ),
));
$cook_fields = array();
if ( $has_geolocation ) {
    $consent_management_fields = array_merge( $consent_management_fields, array(
        array(
            'type'          => 'select',
            'label'         => esc_html__( 'Which mode to use if geolocation fails?', 'full-picture-analytics-cookie-notice' ),
            'field_id'      => 'enable_scripts_after',
            'class'         => 'fupi_sub fupi_mode_cond fupi_cond_val_auto_strict fupi_cond_val_auto_lax fupi_cond_val_manual fupi_disabled',
            'option_arr_id' => $option_arr_id,
            'options'       => array(
                'optin'  => esc_html__( 'Opt-in mode (recommended)', 'full-picture-analytics-cookie-notice' ),
                'optout' => esc_html__( 'Opt-out mode', 'full-picture-analytics-cookie-notice' ),
                'notify' => esc_html__( 'Notification mode', 'full-picture-analytics-cookie-notice' ),
            ),
            'default'       => 'optin',
        ),
        array(
            'type'           => 'select',
            'label'          => esc_html__( 'Use Opt-in mode in', 'full-picture-analytics-cookie-notice' ),
            'field_id'       => 'optin',
            'class'          => 'fupi_manual_mode',
            'el_class'       => 'fupi_condition',
            'el_data_target' => 'fupi_optin_cond',
            'option_arr_id'  => $option_arr_id,
            'options'        => array(
                'all'      => esc_html__( 'In all countries', 'full-picture-analytics-cookie-notice' ),
                'none'     => esc_html__( 'Nowhere', 'full-picture-analytics-cookie-notice' ),
                'specific' => esc_html__( 'In specific countries and regions', 'full-picture-analytics-cookie-notice' ),
            ),
            'default'        => 'all',
            'under_field'    => sprintf( esc_html__( 'This is required in %1$s60+ countries%2$s and is accepted in the rest. ', 'full-picture-analytics-cookie-notice' ), ' <a target="_blank" href="https://wpfullpicture.com/support/documentation/countries-that-require-opt-in-or-opt-out-to-cookies/">', '</a>' ),
        ),
        array(
            'type'          => 'text',
            'label'         => esc_html__( 'Countries and regions', 'full-picture-analytics-cookie-notice' ),
            'class'         => 'fupi_sub fupi_optin_cond fupi_cond_val_specific fupi_disabled fupi_manual_mode',
            'field_id'      => 'optin_countries',
            'placeholder'   => sprintf( esc_html__( 'e.g. %1$s', 'full-picture-analytics-cookie-notice' ), 'GB, FR, ES, US (CA, TX)' ),
            'option_arr_id' => $option_arr_id,
            'label_for'     => $option_arr_id . '[optin_countries]',
            'under_field'   => esc_html__( 'Attention. To use region rules, you must use geolocation service that provides region information. ', 'full-picture-analytics-cookie-notice' ),
        ),
        array(
            'type'           => 'select',
            'label'          => sprintf( esc_html__( 'Use opt-out mode in', 'full-picture-analytics-cookie-notice' ), '<em>', '</em>' ),
            'field_id'       => 'optout',
            'class'          => 'fupi_manual_mode',
            'el_class'       => 'fupi_condition',
            'el_data_target' => 'fupi_optout_cond',
            'option_arr_id'  => $option_arr_id,
            'options'        => array(
                'rest'     => esc_html__( 'In all the countries and regions not mentioned above', 'full-picture-analytics-cookie-notice' ),
                'none'     => esc_html__( 'Nowhere', 'full-picture-analytics-cookie-notice' ),
                'specific' => esc_html__( 'In specific countries and regions', 'full-picture-analytics-cookie-notice' ),
            ),
            'default'        => 'rest',
            'under_field'    => sprintf( esc_html__( 'See %1$sin which countries%2$s you can use this setting.', 'full-picture-analytics-cookie-notice' ), ' <a target="_blank" href="https://wpfullpicture.com/support/documentation/countries-that-require-opt-in-or-opt-out-to-cookies/">', '</a>' ),
        ),
        array(
            'type'          => 'text',
            'label'         => esc_html__( 'Countries and regions', 'full-picture-analytics-cookie-notice' ),
            'class'         => 'fupi_sub fupi_optout_cond fupi_cond_val_specific fupi_disabled fupi_manual_mode',
            'field_id'      => 'optout_countries',
            'placeholder'   => sprintf( esc_html__( 'e.g. %1$s', 'full-picture-analytics-cookie-notice' ), 'GB, FR, ES, US (CA, TX)' ),
            'option_arr_id' => $option_arr_id,
            'label_for'     => $option_arr_id . '[optout_countries]',
            'under_field'   => esc_html__( 'Attention. To use region rules, you must use geolocation service that provides region information. ', 'full-picture-analytics-cookie-notice' ),
        ),
        array(
            'type'           => 'select',
            'label'          => sprintf( esc_html__( 'Use notification mode in', 'full-picture-analytics-cookie-notice' ), '<em>', '</em>' ),
            'field_id'       => 'inform',
            'class'          => 'fupi_manual_mode',
            'el_class'       => 'fupi_condition',
            'el_data_target' => 'fupi_inform_cond',
            'option_arr_id'  => $option_arr_id,
            'options'        => array(
                'rest'     => esc_html__( 'In all the countries and regions not mentioned above', 'full-picture-analytics-cookie-notice' ),
                'none'     => esc_html__( 'Nowhere', 'full-picture-analytics-cookie-notice' ),
                'specific' => esc_html__( 'In specific countries and regions', 'full-picture-analytics-cookie-notice' ),
            ),
            'default'        => 'rest',
            'under_field'    => sprintf( esc_html__( 'See %1$sin which countries%2$s you can use this setting.', 'full-picture-analytics-cookie-notice' ), ' <a target="_blank" href="https://wpfullpicture.com/support/documentation/countries-that-require-opt-in-or-opt-out-to-cookies/">', '</a>' ),
        ),
        array(
            'type'          => 'text',
            'label'         => esc_html__( 'Countries and regions', 'full-picture-analytics-cookie-notice' ),
            'class'         => 'fupi_sub fupi_inform_cond fupi_cond_val_specific fupi_disabled fupi_manual_mode',
            'field_id'      => 'inform_countries',
            'placeholder'   => sprintf( esc_html__( 'e.g. %1$s', 'full-picture-analytics-cookie-notice' ), 'GB, FR, ES, US (CA, TX)' ),
            'option_arr_id' => $option_arr_id,
            'label_for'     => $option_arr_id . '[inform_countries]',
            'under_field'   => esc_html__( 'Attention. To use region rules, you must use geolocation service that provides region information. ', 'full-picture-analytics-cookie-notice' ),
        )
    ) );
}
$consent_management_fields = array_merge( $consent_management_fields, array(array(
    'type'          => 'number',
    'label'         => esc_html__( 'Privacy policy page ID', 'full-picture-analytics-cookie-notice' ),
    'field_id'      => 'pp_id',
    'option_arr_id' => $option_arr_id,
    'class'         => 'fupi_required',
    'offscreen'     => '<p>' . esc_html__( 'Privacy policy ID is used to keep track of changes to the privacy policy text, remember what version of the text visitors agree to and ask them for consent again when it changes.', 'full-picture-analytics-cookie-notice' ) . '</p>',
    'under_field'   => '<p>' . sprintf( esc_html__( 'Provide the ID of the privacy policy page that is published and chosen %1$son this page%2$s. If you have a multilingual site, this must be the ID of the page in the main language. To find the ID, start editing the page with your privacy policy. The ID will be in its URL.', 'full-picture-analytics-cookie-notice' ), '<a href="/wp-admin/options-privacy.php" target="_blank">', '</a>' ) . '</p>',
)) );
$theme_compat_notice = '';
if ( $is_oceanWP_theme ) {
    $theme_compat_notice = '<p style="color: red;">' . sprintf( esc_html__( 'Attention. OceanWP theme breaks the controls for styling the consent banner. They will not be available when using OceanWP. %1$sLearn what to do about it%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-go-around-the-incompatibility-issues-with-oceanwp-theme/" target="_blank">', '</a>' ) . '</p>';
}
$cook_fields = array_merge( $cook_fields, array(array(
    'type'          => 'button',
    'label'         => esc_html__( 'Banner settings', 'full-picture-analytics-cookie-notice' ),
    'button_text'   => esc_html__( 'Configure the banner', 'full-picture-analytics-cookie-notice' ),
    'icon'          => 'dashicons dashicons-admin-appearance',
    'href'          => wp_customize_url() . '?autofocus[panel]=fupi_notice',
    'class'         => 'fupi_customizer_link_wrap',
    'el_class'      => 'button button-secondary fupi_customize_notice_btn',
    'field_id'      => 'customize_notice_btn',
    'option_arr_id' => $option_arr_id,
    'after_field'   => $theme_compat_notice,
    'under_field'   => '<p>' . esc_html__( 'Save changes before customizing', 'full-picture-analytics-cookie-notice' ) . '</p>',
), array(
    'type'          => 'page_search',
    'field_id'      => 'hide_on_pages',
    'label'         => esc_html__( 'Hide the banner on these pages', 'full-picture-analytics-cookie-notice' ),
    'option_arr_id' => $option_arr_id,
    'under_field'   => '<p>' . esc_html__( 'WP Full Picture hides consent banner on the privacy policy page. Here you can add more pages. Hiding the banner only hides it visually. It does not automatically give consent to tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>',
), array(
    'type'          => 'text',
    'label'         => esc_html__( 'Show consent banner when visitors click this page element', 'full-picture-analytics-cookie-notice' ),
    'field_id'      => 'toggle_selector',
    'class'         => 'fupi_join fupi_adv',
    'option_arr_id' => $option_arr_id,
    'label_for'     => $option_arr_id . '[toggle_selector]',
    'placeholder'   => esc_html__( 'e.g. #some_button', 'full-picture-analytics-cookie-notice' ),
    'under_field'   => esc_html__( 'Leave empty to use the default .fp_show_cookie_notice. Alternatively, you can enable a toggle icon in the styling options above.', 'full-picture-analytics-cookie-notice' ),
)) );
// SCRIPT BLOCKING
$scr_fields = array(
    array(
        'type'     => 'hidden',
        'field_id' => 'id',
        'el_class' => 'fupi_generate_id',
        'required' => true,
    ),
    array(
        'type'        => 'text',
        'field_id'    => 'title',
        'el_class'    => 'fupi_internal_title',
        'label'       => esc_html__( 'Name of a tracking tool or plugin', 'full-picture-analytics-cookie-notice' ),
        'placeholder' => esc_html__( 'Name', 'full-picture-analytics-cookie-notice' ),
        'class'       => 'fupi_col_100',
        'required'    => true,
    ),
    array(
        'label'    => esc_html__( 'Requires consent to:', 'full-picture-analytics-cookie-notice' ),
        'type'     => 'label',
        'field_id' => 'types_label',
        'class'    => 'fupi_col_40',
    ),
    array(
        'type'     => 'checkbox',
        'field_id' => 'stats',
        'label'    => esc_html__( 'Statistics', 'full-picture-analytics-cookie-notice' ),
        'class'    => 'fupi_col_20 fupi_inline_label',
    ),
    array(
        'type'     => 'checkbox',
        'field_id' => 'market',
        'label'    => esc_html__( 'Marketing', 'full-picture-analytics-cookie-notice' ),
        'class'    => 'fupi_col_20 fupi_inline_label',
    ),
    array(
        'type'     => 'checkbox',
        'field_id' => 'pers',
        'label'    => esc_html__( 'Personalisation', 'full-picture-analytics-cookie-notice' ),
        'class'    => 'fupi_col_20 fupi_inline_label',
    ),
    array(
        'label'         => esc_html__( 'Block these trackers before consent', 'full-picture-analytics-cookie-notice' ),
        'type'          => 'r3',
        'field_id'      => 'rules',
        'option_arr_id' => $option_arr_id,
        'is_repeater'   => true,
        'class'         => 'fupi_fullwidth_tr ',
        'fields'        => array(array(
            'type'     => 'select',
            'field_id' => 'block_by',
            'options'  => array(
                'src'        => esc_html__( '<script> with a URL that contains...', 'full-picture-analytics-cookie-notice' ),
                'content'    => esc_html__( 'inline <script> with code that contains...', 'full-picture-analytics-cookie-notice' ),
                'link_href'  => esc_html__( '<link> with a URL that contains...', 'full-picture-analytics-cookie-notice' ),
                'img_src'    => esc_html__( '<img> with a URL that contains', 'full-picture-analytics-cookie-notice' ),
                'iframe_src' => esc_html__( '<iframe> with a URL that contains... (new blocker only)', 'full-picture-analytics-cookie-notice' ),
            ),
            'class'    => 'fupi_col_40',
        ), array(
            'type'        => 'text',
            'field_id'    => 'unique',
            'placeholder' => esc_html__( 'unique value', 'full-picture-analytics-cookie-notice' ),
            'required'    => true,
            'class'       => 'fupi_col_60',
        )),
    )
);
$geo_fields = array(array(
    'label'             => esc_html__( 'Enable Geolocation in the General Settings to load this tool in specific countries', 'full-picture-analytics-cookie-notice' ),
    'type'              => 'label',
    'field_id'          => 'label_geo_text',
    'class'             => 'fupi_col_100',
    'start_sub_section' => true,
    'end_sub_section'   => true,
));
$scr_fields = array_merge( $scr_fields, $geo_fields, array(array(
    'label'    => esc_html__( 'Tool\'s privacy policy URL (deprecated)', 'full-picture-analytics-cookie-notice' ),
    'type'     => 'url',
    'field_id' => 'pp_url',
    'el_class' => 'fupi_field_300',
    'class'    => 'fupi_r3_table_row',
), array(
    'type'        => 'toggle',
    'field_id'    => 'always',
    'label'       => esc_html__( 'Never load', 'full-picture-analytics-cookie-notice' ),
    'after_field' => esc_html__( 'Removes all the above elements from the HTML', 'full-picture-analytics-cookie-notice' ),
    'class'       => 'fupi_r3_table_row fupi_inline_label',
), array(
    'label'       => esc_html__( 'Disable', 'full-picture-analytics-cookie-notice' ),
    'type'        => 'toggle',
    'after_field' => esc_html__( 'Temporarily stop controling this tool', 'full-picture-analytics-cookie-notice' ),
    'field_id'    => 'force_load',
    'class'       => 'fupi_r3_table_row fupi_inline_label fupi_red_switch',
)) );
$sections = array(
    // Consent management
    array(
        'section_id'    => 'fupi_cook_consent',
        'section_title' => esc_html__( 'Main consent settings', 'full-picture-analytics-cookie-notice' ),
        'fields'        => $consent_management_fields,
    ),
    // Consent Banner
    array(
        'section_id'    => 'fupi_cook_main',
        'section_title' => esc_html__( 'Consent banner', 'full-picture-analytics-cookie-notice' ),
        'fields'        => $cook_fields,
    ),
    // IFRAME PLACEHOLDERS
    array(
        'section_id'    => 'fupi_cook_iframes',
        'section_title' => esc_html__( 'Iframe placeholders', 'full-picture-analytics-cookie-notice' ),
        'fields'        => array(
            array(
                'type'          => 'button',
                'label'         => esc_html__( 'Placeholder settings', 'full-picture-analytics-cookie-notice' ),
                'button_text'   => esc_html__( 'Customize placeholders', 'full-picture-analytics-cookie-notice' ),
                'icon'          => 'dashicons dashicons-admin-appearance',
                'href'          => wp_customize_url() . '?autofocus[panel]=fupi_ifrblock',
                'class'         => 'fupi_customizer_link_wrap',
                'el_class'      => 'button button-secondary fupi_customize_notice_btn',
                'field_id'      => 'customize_iframe_btn',
                'option_arr_id' => $option_arr_id,
                'after_field'   => $theme_compat_notice,
                'under_field'   => '<p>' . esc_html__( 'Save changes before customizing', 'full-picture-analytics-cookie-notice' ) . '</p>',
            ),
            array(
                'type'          => 'toggle',
                'label'         => esc_html__( 'Lazy load all embedded content', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'iframe_lazy',
                'class'         => 'fupi_join',
                'option_arr_id' => $option_arr_id,
                'after_field'   => esc_html__( 'Recommended for improved page-load times', 'full-picture-analytics-cookie-notice' ),
            ),
            array(
                'type'          => 'multi_checkbox',
                'label'         => esc_html__( 'Display placeholders for:', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'iframe_auto_rules',
                'option_arr_id' => $option_arr_id,
                'options'       => array(
                    'youtube' => esc_html__( 'YouTube', 'full-picture-analytics-cookie-notice' ),
                    'vimeo'   => esc_html__( 'Vimeo', 'full-picture-analytics-cookie-notice' ),
                ),
            ),
            array(
                'type'           => 'toggle',
                'label'          => esc_html__( 'Display placeholders for content from other sources', 'full-picture-analytics-cookie-notice' ),
                'field_id'       => 'control_other_iframes',
                'el_class'       => 'fupi_condition',
                'el_data_target' => 'fupi_manual_iframes_cond',
                'option_arr_id'  => $option_arr_id,
                'under_field'    => '<a href="https://wpfullpicture.com/support/documentation/how-to-set-up-automatic-iframe-blocking/" target="_blank">' . esc_html__( 'Learn how to set it up', 'full-picture-analytics-cookie-notice' ) . '</a>',
            ),
            array(
                'type'          => 'r3',
                'label'         => '',
                'field_id'      => 'iframe_manual_rules',
                'option_arr_id' => $option_arr_id,
                'is_repeater'   => true,
                'class'         => 'fupi_join fupi_fullwidth_tr fupi_disabled fupi_manual_iframes_cond',
                'fields'        => array(
                    array(
                        'label'       => esc_html__( 'Name of the service*', 'full-picture-analytics-cookie-notice' ),
                        'type'        => 'text',
                        'placeholder' => esc_html__( 'e.g. YouTube', 'full-picture-analytics-cookie-notice' ),
                        'field_id'    => 'name',
                        'el_class'    => 'fupi_internal_title',
                        'class'       => 'fupi_col_100',
                        'required'    => true,
                    ),
                    array(
                        'type'        => 'text',
                        'label'       => esc_html__( 'Iframe\'s domain URL*', 'full-picture-analytics-cookie-notice' ),
                        'field_id'    => 'iframe_url',
                        'placeholder' => esc_html__( 'e.g. youtube.com', 'full-picture-analytics-cookie-notice' ),
                        'class'       => 'fupi_col_30',
                        'required'    => true,
                    ),
                    array(
                        'type'     => 'url',
                        'label'    => esc_html__( 'Service\'s privacy policy', 'full-picture-analytics-cookie-notice' ),
                        'field_id' => 'privacy_url',
                        'class'    => 'fupi_col_40',
                    ),
                    array(
                        'type'     => 'url',
                        'label'    => esc_html__( 'Placeholder image URL', 'full-picture-analytics-cookie-notice' ),
                        'field_id' => 'image_url',
                        'class'    => 'fupi_col_30',
                    ),
                    array(
                        'type'              => 'label',
                        'label'             => esc_html__( 'Requires consent to:', 'full-picture-analytics-cookie-notice' ),
                        'field_id'          => 'types_label',
                        'start_sub_section' => true,
                        'class'             => 'fupi_col_25',
                    ),
                    array(
                        'type'     => 'toggle',
                        'label'    => esc_html__( 'Statistics', 'full-picture-analytics-cookie-notice' ),
                        'field_id' => 'stats',
                        'class'    => 'fupi_col_25 fupi_inline_label',
                    ),
                    array(
                        'type'     => 'toggle',
                        'label'    => esc_html__( 'Marketing', 'full-picture-analytics-cookie-notice' ),
                        'field_id' => 'market',
                        'class'    => 'fupi_col_25 fupi_inline_label',
                    ),
                    array(
                        'type'            => 'toggle',
                        'label'           => esc_html__( 'Personalisation', 'full-picture-analytics-cookie-notice' ),
                        'field_id'        => 'pers',
                        'class'           => 'fupi_col_25 fupi_inline_label',
                        'end_sub_section' => true,
                    )
                ),
            )
        ),
    ),
    // SCRIPT BLOCKING
    array(
        'section_id'    => 'fupi_cook_scriptblock',
        'section_title' => esc_html__( 'Tracking blocker', 'full-picture-analytics-cookie-notice' ),
        'fields'        => array(
            array(
                'type'          => 'toggle',
                'label'         => esc_html__( 'Use the new tracking blocker (beta)', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'dynamic_blocking',
                'option_arr_id' => $option_arr_id,
                'under_field'   => sprintf( esc_html__( 'This blocker will become the default one in march 2027. It offers better accuracy, can block tracking iframes and dynamically added content. Please %1$slet us know%2$s if you encounter any issues.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/contact/">', '</a>' ),
            ),
            array(
                'type'              => 'multi_checkbox',
                'label'             => esc_html__( 'Ready configurations', 'full-picture-analytics-cookie-notice' ),
                'field_id'          => 'scrblk_auto_rules',
                'option_arr_id'     => $option_arr_id,
                'options'           => array(
                    'stape'    => esc_html__( 'Stape Conversion Tracking plugin', 'full-picture-analytics-cookie-notice' ),
                    'pys'      => sprintf( esc_html__( 'Tools loaded by PixelYourSite plugin %1$s(read info)%2$s', 'full-picture-analytics-cookie-notice' ), '<span style="color: red;">', '</span>' ),
                    'pixelman' => esc_html__( 'Tools loaded by Pixel Manager for WooCommerce plugin (read info)', 'full-picture-analytics-cookie-notice' ),
                    'jetpack'  => esc_html__( 'Jetpack Stats (from the Jetpack plugin)', 'full-picture-analytics-cookie-notice' ),
                ),
                'under_field'       => '<label><input type="checkbox" checked disabled>' . esc_html__( 'All tracking tools installed with WP Full Picture\'s modules (always enabled)', 'full-picture-analytics-cookie-notice' ) . '</label>
				<div class="fupi_spacer"></div>
				<label><input type="checkbox" checked disabled>' . esc_html__( 'Google Analytics (always enabled, via Consent Mode v2)', 'full-picture-analytics-cookie-notice' ) . '</label>
					<div class="fupi_spacer"></div>
					<input type="checkbox" checked disabled>' . esc_html__( 'Google Ads (always enabled, via Consent Mode v2)', 'full-picture-analytics-cookie-notice' ) . '</label>
					<div class="fupi_spacer"></div>
					<input type="checkbox" checked disabled>' . esc_html__( 'Microsoft Advertising (always enabled, via MS EUT Consent Mode)', 'full-picture-analytics-cookie-notice' ) . '</label>',
                'offscreen_warning' => '<h3>PixelYourSite</h3>
					<p>' . esc_html__( 'WP Full Picture loads tracking tools integrated via PixelYourSite when the visitor agrees to ALL uses of their personal data (statistics, marketing and personalisation).', 'full-picture-analytics-cookie-notice' ) . '</p>
					<p>' . esc_html__( 'To control PYS, go to PixelYourSite settings > "Google Tag Settings" page and do the following changes:', 'full-picture-analytics-cookie-notice' ) . '</p>
					<ol>
						<li>' . esc_html__( 'change the default dataLayer name to "dataLayer",', 'full-picture-analytics-cookie-notice' ) . '</li>
						<li>' . esc_html__( 'disable option "Fire Google tags with consent mode granted".', 'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>
					<h3>Pixel Manager for WooCommerce</h3>
					<p>' . esc_html__( 'Consent Management features in PMW conflict with the ones built in WP FP. Disable them for correct tracking. Go to Pixel Manager settings > Consent Management > disable Consent Mode v2.', 'full-picture-analytics-cookie-notice' ) . '</p>',
            ),
            array(
                'type'           => 'toggle',
                'label'          => esc_html__( 'Block other tracking tools', 'full-picture-analytics-cookie-notice' ),
                'field_id'       => 'control_other_tools',
                'el_class'       => 'fupi_condition',
                'el_data_target' => 'fupi_manual_blockscr_cond',
                'option_arr_id'  => $option_arr_id,
                'under_field'    => sprintf(
                    esc_html__( 'Read %1$show to set it up%2$s or %3$sask us for help in the support forum%2$s.', 'full-picture-analytics-cookie-notice' ),
                    '<a href="https://wpfullpicture.com/support/documentation/manual-setup-guide-for-the-tracking-tools-manager-module/" target="_blank">',
                    '</a>',
                    '<a href="https://wordpress.org/support/plugin/full-picture-analytics-cookie-notice/" target="_blank">'
                ),
            ),
            array(
                'type'          => 'r3',
                'label'         => '',
                'field_id'      => 'scrblk_manual_rules',
                'option_arr_id' => $option_arr_id,
                'class'         => 'fupi_join fupi_fullwidth_tr fupi_disabled fupi_manual_blockscr_cond',
                'is_repeater'   => true,
                'fields'        => $scr_fields,
            )
        ),
    ),
    // RECORDS OF CONSENT
    array(
        'section_id'    => 'fupi_cook_proofrec',
        'section_title' => esc_html__( 'Records of consent', 'full-picture-analytics-cookie-notice' ),
        'fields'        => array(
            array(
                'type'           => 'radio',
                'label'          => esc_html__( 'Where to store proofs of consent', 'full-picture-analytics-cookie-notice' ),
                'field_id'       => 'storage_location',
                'option_arr_id'  => $option_arr_id,
                'el_class'       => 'fupi_condition',
                'el_data_target' => 'fupi_cdb_cond',
                'options'        => array(
                    ''      => esc_html__( 'Disabled', 'full-picture-analytics-cookie-notice' ),
                    'email' => esc_html__( 'In an email account', 'full-picture-analytics-cookie-notice' ),
                    'cdb'   => esc_html__( 'In the cloud (service available until 03-27)', 'full-picture-analytics-cookie-notice' ),
                ),
                'under_field'    => '<p style="margin-top: 10px;">' . sprintf( esc_html__( '%1$sWhat are the differences?%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/records-of-consents-in-inbox-vs-the-cloud/">', '</a>' ) . '</p>',
            ),
            // FOR THE CDB OPTION
            array(
                'type'          => 'text',
                'label'         => esc_html__( 'ConsentsDB secret key', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'cdb_key',
                'class'         => 'fupi_sub fupi_cdb_cond fupi_cond_val_cdb fupi_disabled',
                'option_arr_id' => $option_arr_id,
                'after_field'   => '
					<p><strong>' . esc_html__( 'To start saving consents:', 'full-picture-analytics-cookie-notice' ) . '</strong></p>
					<ol>
						<li>' . sprintf( esc_html__( 'Make sure your site is set up correctly %1$sOpen GDPR setup helper%2$s.', 'full-picture-analytics-cookie-notice' ), '<br><button type="button" class="button-secondary fupi_open_gdpr_helper" style="margin: 10px 0">', '</button>' ) . '</li>
						<li>' . sprintf( esc_html__( 'Purchase %1$sone of the packages%2$s (start at €10/year).', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/addons/consentsdb" target="_blank">', '</a>' ) . '</li>
						<li><a href="https://consentsdb.com/">' . esc_html__( 'Register your website at ConsentsDB.com.', 'full-picture-analytics-cookie-notice' ) . '</a></li>
						<li>' . esc_html__( 'Follow the instructions in the ConsentsDB.', 'full-picture-analytics-cookie-notice' ) . '</li>
					</ol>
					<p><strong>' . esc_html__( 'Learn more:', 'full-picture-analytics-cookie-notice' ) . '</strong></p>
					<ol>
						<li><a href="https://wpfullpicture.com/addons/consentsdb/">' . esc_html__( 'Pricing', 'full-picture-analytics-cookie-notice' ) . '</a></li>
						<li><a href="https://wpfullpicture.com/support/documentation/introduction-to-consentsdb/">' . esc_html__( 'About ConsentsDB', 'full-picture-analytics-cookie-notice' ) . '</a></li>
						<li><a href="https://wpfullpicture.com/support/documentation/how-to-start-collecting-consents-in-the-consentsdb/" target="_blank">' . esc_html__( 'Detailed setup guide', 'full-picture-analytics-cookie-notice' ) . '</a></li>
					</ol>',
            ),
            array(
                'type'              => 'toggle',
                'label'             => esc_html__( 'Allow site visitors to view consent data', 'full-picture-analytics-cookie-notice' ),
                'field_id'          => 'consent_access',
                'option_arr_id'     => $option_arr_id,
                'class'             => 'fupi_join fupi_disabled',
                'class'             => 'fupi_sub fupi_cdb_cond fupi_cond_val_cdb fupi_disabled',
                'offscreen_warning' => '<p>' . esc_html__( 'This will display a link at the bottom of the consent banner. When clicked, it will open a new browser tab with the visitor\'s consent details as registered by the ConsentsDB.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<h3>' . esc_html__( 'How to enable it', 'full-picture-analytics-cookie-notice' ) . '</h3>
				<p class="fupi_warning_text">' . esc_html__( 'To use this feature, you need to:', 'full-picture-analytics-cookie-notice' ) . '</p>
				<ol class="fupi_warning_text">
					<li>' . esc_html__( 'enable this option', 'full-picture-analytics-cookie-notice' ) . '</li>
					<li>' . esc_html__( 'go to your ConsentsDB panel > Website settings > enable the option "Allow site visitors to view consent data"', 'full-picture-analytics-cookie-notice' ) . '</li>
				</ol>',
            ),
            // FOR THE EMAIL OPTION
            array(
                'type'          => 'email',
                'label'         => esc_html__( 'Email address', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'local_backup_email',
                'class'         => 'fupi_sub fupi_cdb_cond fupi_cond_val_email fupi_disabled',
                'option_arr_id' => $option_arr_id,
                'under_field'   => '<p>' . esc_html__( 'Leave empty, to send to the administration email address', 'full-picture-analytics-cookie-notice' ) . '</p>',
            ),
            array(
                'type'          => 'select',
                'label'         => esc_html__( 'Sending frequency', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'email_frequency',
                'option_arr_id' => $option_arr_id,
                'class'         => 'fupi_sub fupi_cdb_cond fupi_cond_val_email fupi_disabled',
                'options'       => array(
                    'daily'      => esc_html__( 'Every day', 'full-picture-analytics-cookie-notice' ),
                    'twicedaily' => esc_html__( 'Twice a day', 'full-picture-analytics-cookie-notice' ),
                    'hourly'     => esc_html__( 'Every hour (for large traffic sites)', 'full-picture-analytics-cookie-notice' ),
                ),
                'default'       => 'daily',
            ),
            array(
                'type'          => 'button',
                'label'         => esc_html__( 'View latest records', 'full-picture-analytics-cookie-notice' ),
                'button_text'   => esc_html__( 'View latest records', 'full-picture-analytics-cookie-notice' ),
                'href'          => admin_url( 'admin.php?page=full_picture_cook&tab=consents_list' ),
                'el_class'      => 'button button-secondary',
                'class'         => 'fupi_sub fupi_cdb_cond fupi_cond_val_email fupi_disabled',
                'field_id'      => 'view_proofs_btn',
                'option_arr_id' => $option_arr_id,
                'after_field'   => $theme_compat_notice,
            ),
            array(
                'type'          => 'toggle',
                'label'         => esc_html__( 'Do not filter bot traffic', 'full-picture-analytics-cookie-notice' ),
                'field_id'      => 'save_all_consents',
                'option_arr_id' => $option_arr_id,
                'related_opts'  => ['main', 'bot_list'],
                'class'         => 'fupi_cdb_cond fupi_cond_val_cdb fupi_cond_val_email fupi_disabled',
                'offscreen'     => '<p>' . esc_html__( 'By default, WP Full Picture does not save consents of visitors recognized as bots and those who consented within 1 second from the moment the page has loaded.', 'full-picture-analytics-cookie-notice' ) . '</p>
				<p>' . esc_html__( 'If this filters too much traffic, enable this option or change the "Bot detection list" to less restrictive (see the "Related settings" button).', 'full-picture-analytics-cookie-notice' ) . '</p>',
            ),
        ),
    ),
    // OTHER
    array(
        'section_id'    => 'fupi_cook_other',
        'section_title' => esc_html__( 'Other', 'full-picture-analytics-cookie-notice' ),
        'fields'        => array(array(
            'type'                => 'toggle',
            'label'               => esc_html__( 'Do NOT ask visitors for consent again', 'full-picture-analytics-cookie-notice' ),
            'field_id'            => 'dont_ask_again',
            'class'               => 'fupi_adv',
            'option_arr_id'       => $option_arr_id,
            'after_field'         => esc_html__( 'Breaks GDPR. Best used on dev sites.', 'full-picture-analytics-cookie-notice' ),
            'offscreen_important' => '<p>' . esc_html__( 'When you enable this setting, visitors who already consented, will not be asked for consent every time you enable new tracking tools or change your privacy policy. This will break GDPR, so we recommend to use it only on development sites or while setting up tracking.', 'full-picture-analytics-cookie-notice' ) . '</p>',
        )),
    ),
);