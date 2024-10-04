<?php

$footer_scr_fields = array();
$scr_fields = array(
    array(
        'placeholder' => esc_html__( 'Name', 'full-picture-analytics-cookie-notice' ),
        'type'        => 'text',
        'el_class'    => 'fupi_internal_title',
        'field_id'    => 'title',
        'wrap_class'  => 'fupi_col_100',
        'required'    => true,
        'under field' => esc_html__( '(Used on the GDPR setup helper page, data saved with visitors consents and by the "Privacy Policy Extras" module)', 'full-picture-analytics-cookie-notice' ),
    ),
    array(
        'label'       => esc_html__( 'Script\'s privacy policy URL', 'full-picture-analytics-cookie-notice' ),
        'type'        => 'url',
        'field_id'    => 'pp_url',
        'wrap_class'  => 'fupi_col_50_grow',
        'under field' => esc_html__( '(Used by the "Privacy Policy Extras" module)', 'full-picture-analytics-cookie-notice' ),
    ),
    array(
        'label'      => esc_html__( 'Script ID', 'full-picture-analytics-cookie-notice' ),
        'type'       => 'text',
        'field_id'   => 'id',
        'required'   => true,
        'wrap_class' => 'fupi_col_20',
    ),
    array(
        'label'       => esc_html__( '(Required) Script only - no HTML', 'full-picture-analytics-cookie-notice' ),
        'type'        => 'textarea',
        'field_id'    => 'scr',
        'el_class'    => 'fupi_textarea_with_code',
        'required'    => true,
        'format'      => 'htmlentities',
        'under field' => sprintf(
            esc_html__( '%1$s%5$s tags will be automatically removed (or replaced) from the code above so that WP Full Picture can manage it. %6$sLearn more%7$s.%2$s', 'full-picture-analytics-cookie-notice' ),
            '<p>',
            '</p>',
            '<strong>',
            '</strong>',
            '&lt;script>',
            '<a href="https://wpfullpicture.com/support/documentation/how-to-add-custom-scripts-in-a-privacy-respecting-way/?utm_source=fp_admin&utm_medium=referral&utm_campaign=documentation_link" target="_blank">',
            '</a>'
        ),
    )
);
$html_field = array(array(
    'label'       => esc_html__( '(Optional) HTML', 'full-picture-analytics-cookie-notice' ),
    'type'        => 'textarea',
    'field_id'    => 'html',
    'el_class'    => 'fupi_textarea_with_html',
    'wrap_class'  => 'fupi_col_100',
    'format'      => 'htmlentities',
    'under field' => esc_html__( 'HTML added in this field will be loaded on the page before the script. Attention! Make sure you enter HTML without errors. Buggy HTML may break the page.', 'full-picture-analytics-cookie-notice' ),
));
$footer_scr_fields = array_merge( $scr_fields, $html_field );
if ( $this->cook_enabled ) {
    $cook_scr_fields = array(
        array(
            'label'             => esc_html__( 'What is visitor\'s data used for?', 'full-picture-analytics-cookie-notice' ),
            'type'              => 'label',
            'field_id'          => 'types_label',
            'start_sub_section' => true,
            'wrap_class'        => 'fupi_col_40',
        ),
        array(
            'label'      => esc_html__( 'Statistics', 'full-picture-analytics-cookie-notice' ),
            'type'       => 'checkbox',
            'field_id'   => 'stats',
            'wrap_class' => 'fupi_col_20',
        ),
        array(
            'label'      => esc_html__( 'Marketing', 'full-picture-analytics-cookie-notice' ),
            'type'       => 'checkbox',
            'field_id'   => 'market',
            'wrap_class' => 'fupi_col_20',
        ),
        array(
            'label'           => esc_html__( 'Personalisation', 'full-picture-analytics-cookie-notice' ),
            'type'            => 'checkbox',
            'field_id'        => 'pers',
            'wrap_class'      => 'fupi_col_20',
            'end_sub_section' => true,
        )
    );
    $scr_fields = array_merge( $scr_fields, $cook_scr_fields );
    $footer_scr_fields = array_merge( $footer_scr_fields, $cook_scr_fields );
}
$load_fields = array(array(
    'label'      => esc_html__( 'Force load', 'full-picture-analytics-cookie-notice' ),
    'type'       => 'toggle',
    'field_id'   => 'force_load',
    'wrap_class' => 'fupi_col_20',
), array(
    'label'      => esc_html__( 'Disable script', 'full-picture-analytics-cookie-notice' ),
    'type'       => 'toggle',
    'field_id'   => 'disable',
    'wrap_class' => 'fupi_col_20',
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