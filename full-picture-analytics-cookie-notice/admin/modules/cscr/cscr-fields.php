<?php

$option_arr_id = 'fupi_cscr';
$footer_scr_fields = array();
$scr_fields = array(array(
    'label'       => esc_html__( 'Name*', 'full-picture-analytics-cookie-notice' ),
    'type'        => 'text',
    'el_class'    => 'fupi_internal_title',
    'field_id'    => 'title',
    'class'       => 'fupi_col_100',
    'required'    => true,
    'under_field' => esc_html__( 'This is displayed by the [fp_info] shortcode (deprecated), on the "GDPR setup helper" page and in proofs of visitors\' consents.', 'full-picture-analytics-cookie-notice' ),
), array(
    'type'     => 'hidden',
    'field_id' => 'id',
    'required' => true,
    'class'    => 'fupi_col_20',
    'el_class' => 'fupi_generate_id',
), array(
    'label'       => esc_html__( '(Required) Script only - no HTML', 'full-picture-analytics-cookie-notice' ),
    'type'        => 'textarea',
    'field_id'    => 'scr',
    'class'       => 'fupi_col_100',
    'el_class'    => 'fupi_textarea_with_code',
    'required'    => true,
    'format'      => 'htmlentities',
    'under_field' => sprintf(
        esc_html__( '%1$s%5$s tags will be automatically removed (or replaced) from the code above. %6$sLearn more%7$s.%2$s', 'full-picture-analytics-cookie-notice' ),
        '<p>',
        '</p>',
        '<strong>',
        '</strong>',
        '&lt;script>',
        '<a href="https://wpfullpicture.com/support/documentation/how-to-add-custom-scripts-in-a-privacy-respecting-way/" target="_blank">',
        '</a>'
    ),
));
// ADD HTML FIELD ONLY TO FOOTER
$html_field = array(array(
    'label'       => esc_html__( '(Optional) HTML', 'full-picture-analytics-cookie-notice' ),
    'type'        => 'textarea',
    'field_id'    => 'html',
    'el_class'    => 'fupi_textarea_with_html',
    'class'       => 'fupi_col_100',
    'format'      => 'htmlentities',
    'under_field' => esc_html__( 'HTML is added to the page before the script.', 'full-picture-analytics-cookie-notice' ),
));
$footer_scr_fields = array_merge( $scr_fields, $html_field );
// ADD FPINFO FIELDS TO TOP AND FOOTER FIELDS
$fpinfo_fields = array(array(
    'type'           => 'toggle',
    'label'          => esc_html__( 'This script does not install a tracking tool', 'full-picture-analytics-cookie-notice' ),
    'field_id'       => 'not_installer',
    'class'          => 'fupi_col_50',
    'el_class'       => 'fupi_condition fupi_condition_reverse',
    'el_data_target' => 'fupi_pp_link_opts',
), array(
    'type'     => 'url',
    'label'    => esc_html__( 'Script\'s privacy policy URL (feature deprecated)', 'full-picture-analytics-cookie-notice' ),
    'field_id' => 'pp_url',
    'class'    => 'fupi_pp_link_opts fupi_col_50',
));
$scr_fields = array_merge( $scr_fields, $fpinfo_fields );
$footer_scr_fields = array_merge( $footer_scr_fields, $fpinfo_fields );
if ( isset( $this->tools['cook'] ) ) {
    $cook_scr_fields = array(
        array(
            'label'             => esc_html__( 'Requires consent to:', 'full-picture-analytics-cookie-notice' ),
            'type'              => 'label',
            'field_id'          => 'types_label',
            'start_sub_section' => true,
            'class'             => 'fupi_col_40',
        ),
        array(
            'label'    => esc_html__( 'Statistics', 'full-picture-analytics-cookie-notice' ),
            'type'     => 'checkbox',
            'field_id' => 'stats',
            'class'    => 'fupi_col_20 fupi_inline_label',
        ),
        array(
            'label'    => esc_html__( 'Marketing', 'full-picture-analytics-cookie-notice' ),
            'type'     => 'checkbox',
            'field_id' => 'market',
            'class'    => 'fupi_col_20 fupi_inline_label',
        ),
        array(
            'label'           => esc_html__( 'Personalisation', 'full-picture-analytics-cookie-notice' ),
            'type'            => 'checkbox',
            'field_id'        => 'pers',
            'class'           => 'fupi_col_20 fupi_inline_label',
            'end_sub_section' => true,
        )
    );
    $scr_fields = array_merge( $scr_fields, $cook_scr_fields );
    $footer_scr_fields = array_merge( $footer_scr_fields, $cook_scr_fields );
} else {
    $cook_scr_fields = array(array(
        'label'             => esc_html__( 'Enable the Consent Management module to load this script according to privacy regulations', 'full-picture-analytics-cookie-notice' ),
        'type'              => 'label',
        'field_id'          => 'label_cook_text',
        'class'             => 'fupi_col_100',
        'start_sub_section' => true,
        'end_sub_section'   => true,
    ));
    $scr_fields = array_merge( $scr_fields, $cook_scr_fields );
    $footer_scr_fields = array_merge( $footer_scr_fields, $cook_scr_fields );
}
$geo_fields = array(array(
    'label'             => esc_html__( 'Enable Geolocation in the General Settings to load this script in specific countries', 'full-picture-analytics-cookie-notice' ),
    'type'              => 'label',
    'field_id'          => 'label_geo_text',
    'class'             => 'fupi_col_100',
    'start_sub_section' => true,
    'end_sub_section'   => true,
));
$scr_fields = array_merge( $scr_fields, $geo_fields );
$footer_scr_fields = array_merge( $footer_scr_fields, $geo_fields );
$load_fields = array(array(
    'label'    => esc_html__( 'Force load', 'full-picture-analytics-cookie-notice' ),
    'type'     => 'toggle',
    'field_id' => 'force_load',
    'class'    => 'fupi_col_20 fupi_inline_label',
), array(
    'label'    => esc_html__( 'Disable script', 'full-picture-analytics-cookie-notice' ),
    'type'     => 'toggle',
    'field_id' => 'disable',
    'class'    => 'fupi_col_20 fupi_inline_label fupi_red_switch',
));
$scr_fields = array_merge( $scr_fields, $load_fields );
$footer_scr_fields = array_merge( $footer_scr_fields, $load_fields );
$sections = array(
    // HEAD SCRIPTS
    array(
        'section_id'    => 'fupi_cscr_head',
        'section_title' => esc_html__( 'Head scripts', 'full-picture-analytics-cookie-notice' ),
        'fields'        => array(array(
            'type'          => 'r3',
            'label'         => esc_html__( 'Head scripts', 'full-picture-analytics-cookie-notice' ),
            'field_id'      => 'fupi_head_scripts',
            'option_arr_id' => $option_arr_id,
            'class'         => 'fupi_fullwidth_tr',
            'el_class'      => 'fupi_r3_scr',
            'is_repeater'   => true,
            'fields'        => $scr_fields,
        )),
    ),
    // FOOTER SCRIPTS
    array(
        'section_id'    => 'fupi_cscr_footer',
        'section_title' => esc_html__( 'Footer scripts', 'full-picture-analytics-cookie-notice' ),
        'fields'        => array(array(
            'type'          => 'r3',
            'label'         => esc_html__( 'Footer scripts', 'full-picture-analytics-cookie-notice' ),
            'field_id'      => 'fupi_footer_scripts',
            'option_arr_id' => $option_arr_id,
            'class'         => 'fupi_fullwidth_tr',
            'el_class'      => 'fupi_r3_scr',
            'is_repeater'   => true,
            'fields'        => $footer_scr_fields,
        )),
    ),
);