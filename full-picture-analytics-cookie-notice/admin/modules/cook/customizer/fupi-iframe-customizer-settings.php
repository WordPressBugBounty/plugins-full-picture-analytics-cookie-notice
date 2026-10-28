<?php

$wp_customize->add_panel( 'fupi_ifrblock', array(
	'priority'           => 161,
	'capability'         => 'edit_theme_options',
	'title'              => esc_html__( 'Iframe Placeholders', 'full-picture-analytics-cookie-notice' ),
	'description'        => '<p id="fupi_ifrblock_no_preview_notice" class="notice notice-warning inline" hidden>' . esc_html__( 'There are no iframe placeholders on this page. Go to a page that displays an iframe placeholder to see your customization changes in real time.', 'full-picture-analytics-cookie-notice' ) . '</p>',
	'description_hidden' => false,
) );

$wp_customize->add_section( 'fupi_ifrblock_placeholder', array(
	'title' => esc_html__( 'Placeholder', 'full-picture-analytics-cookie-notice' ),
	'panel' => 'fupi_ifrblock',
) );

$wp_customize->add_section( 'fupi_ifrblock_notification', array(
	'title' => esc_html__( 'Notification', 'full-picture-analytics-cookie-notice' ),
	'panel' => 'fupi_ifrblock',
) );

$wp_customize->add_section( 'fupi_ifrblock_button', array(
	'title' => esc_html__( 'Consent Button', 'full-picture-analytics-cookie-notice' ),
	'panel' => 'fupi_ifrblock',
) );

$wp_customize->add_section( 'fupi_ifrblock_text', array(
	'title' => esc_html__( 'Text Content', 'full-picture-analytics-cookie-notice' ),
	'panel' => 'fupi_ifrblock',
) );

$wp_customize->add_setting( 'fupi_ifrblock_image', array(
	'default'           => 0,
	'sanitize_callback' => array( $this, 'fupi_customizer_sanitize' ),
) );

$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'fupi_ifrblock_image', array(
	'label'       => esc_html__( 'Default image', 'full-picture-analytics-cookie-notice' ),
	'description' => esc_html__( 'Used only when an iframe-specific image or a YouTube/Vimeo thumbnail is not available.', 'full-picture-analytics-cookie-notice' ),
	'section'     => 'fupi_ifrblock_placeholder',
	'mime_type'   => 'image',
) ) );

$number_settings = array(
	'fupi_ifrblock_border_width' => array(
		'default' => 2,
		'label'   => esc_html__( 'Border width (px)', 'full-picture-analytics-cookie-notice' ),
		'min'     => 0,
		'max'     => 20,
		'section' => 'fupi_ifrblock_placeholder',
	),
	'fupi_ifrblock_border_radius' => array(
		'default' => 3,
		'label'   => esc_html__( 'Rounded corners (px)', 'full-picture-analytics-cookie-notice' ),
		'min'     => 0,
		'max'     => 100,
		'section' => 'fupi_ifrblock_placeholder',
	),
	'fupi_ifrblock_notice_font_size' => array(
		'default' => 16,
		'label'   => esc_html__( 'Notification text size (px)', 'full-picture-analytics-cookie-notice' ),
		'min'     => 10,
		'max'     => 40,
		'section' => 'fupi_ifrblock_notification',
	),
	'fupi_ifrblock_btn_font_size' => array(
		'default' => 16,
		'label'   => esc_html__( 'Button text size (px)', 'full-picture-analytics-cookie-notice' ),
		'min'     => 10,
		'max'     => 40,
		'section' => 'fupi_ifrblock_button',
	),
);

foreach ( $number_settings as $setting_id => $config ) {
	$wp_customize->add_setting( $setting_id, array(
		'default'           => $config['default'],
		'sanitize_callback' => array( $this, 'fupi_customizer_sanitize' ),
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( $setting_id, array(
		'label'       => $config['label'],
		'section'     => $config['section'],
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => $config['min'],
			'max'  => $config['max'],
			'step' => 1,
		),
	) );
}

$wp_customize->add_setting( 'fupi_ifrblock_border_color', array(
	'default'           => '#bcbcbc',
	'sanitize_callback' => 'sanitize_hex_color',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fupi_ifrblock_border_color', array(
	'label'   => esc_html__( 'Border color', 'full-picture-analytics-cookie-notice' ),
	'section' => 'fupi_ifrblock_placeholder',
) ) );

foreach ( array( 'x' => 16, 'y' => 9 ) as $axis => $default ) {
	$setting_id = 'fupi_ifrblock_ratio_' . $axis;
	$wp_customize->add_setting( $setting_id, array(
		'default'           => $default,
		'sanitize_callback' => array( $this, 'fupi_customizer_sanitize' ),
	) );
	$wp_customize->add_control( $setting_id, array(
		'label'       => $axis === 'x' ? esc_html__( 'Default aspect width', 'full-picture-analytics-cookie-notice' ) : esc_html__( 'Default aspect height', 'full-picture-analytics-cookie-notice' ),
		'section'     => 'fupi_ifrblock_placeholder',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 1,
			'max'  => 100,
			'step' => 1,
		),
	) );
}

$color_settings = array(
	'fupi_ifrblock_notice_bg_color' => array( '#fff', esc_html__( 'Background color', 'full-picture-analytics-cookie-notice' ), 'fupi_ifrblock_notification' ),
	'fupi_ifrblock_notice_text_color' => array( '#333', esc_html__( 'Text and link color', 'full-picture-analytics-cookie-notice' ), 'fupi_ifrblock_notification' ),
	'fupi_ifrblock_btn_bg_color' => array( '#222', esc_html__( 'Background color', 'full-picture-analytics-cookie-notice' ), 'fupi_ifrblock_button' ),
	'fupi_ifrblock_btn_bg_color_hover' => array( '#555', esc_html__( 'Hover/focus background color', 'full-picture-analytics-cookie-notice' ), 'fupi_ifrblock_button' ),
	'fupi_ifrblock_btn_text_color' => array( '#fff', esc_html__( 'Text color', 'full-picture-analytics-cookie-notice' ), 'fupi_ifrblock_button' ),
	'fupi_ifrblock_btn_text_color_hover' => array( '#fff', esc_html__( 'Hover/focus text color', 'full-picture-analytics-cookie-notice' ), 'fupi_ifrblock_button' ),
);

foreach ( $color_settings as $setting_id => $config ) {
	$wp_customize->add_setting( $setting_id, array(
		'default'           => $config[0],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(
		'label'   => $config[1],
		'section' => $config[2],
	) ) );
}

$wp_customize->add_setting( 'fupi_ifrblock_btn_size', array(
	'default'           => 'large',
	'sanitize_callback' => array( $this, 'fupi_customizer_sanitize' ),
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'fupi_ifrblock_btn_size', array(
	'label'   => esc_html__( 'Button size', 'full-picture-analytics-cookie-notice' ),
	'section' => 'fupi_ifrblock_button',
	'type'    => 'select',
	'choices' => array(
		'small'  => esc_html__( 'Small', 'full-picture-analytics-cookie-notice' ),
		'medium' => esc_html__( 'Medium', 'full-picture-analytics-cookie-notice' ),
		'large'  => esc_html__( 'Large', 'full-picture-analytics-cookie-notice' ),
	),
) );

$wp_customize->add_setting( 'fupi_ifrblock_btn_border_radius', array(
	'default'           => 8,
	'sanitize_callback' => array( $this, 'fupi_customizer_sanitize' ),
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'fupi_ifrblock_btn_border_radius', array(
	'label'       => esc_html__( 'Rounded corners (px)', 'full-picture-analytics-cookie-notice' ),
	'section'     => 'fupi_ifrblock_button',
	'type'        => 'number',
	'input_attrs' => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	),
) );

$wp_customize->add_setting( 'fupi_ifrblock[caption_txt]', array(
	'type'              => 'option',
	'default'           => esc_html__( 'This content is hosted by [[an external source]]. By loading it, you accept its {{privacy terms}}.', 'full-picture-analytics-cookie-notice' ),
	'sanitize_callback' => 'sanitize_text_field',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'fupi_ifrblock[caption_txt]', array(
	'label'       => esc_html__( 'Text over the placeholder', 'full-picture-analytics-cookie-notice' ),
	'description' => esc_html__( 'Text inside [[double square brackets]] is replaced with the source name. Text inside {{double curly brackets}} becomes a privacy-policy link when one is available.', 'full-picture-analytics-cookie-notice' ),
	'section'     => 'fupi_ifrblock_text',
	'type'        => 'text',
) );

$wp_customize->add_setting( 'fupi_ifrblock[btn_text]', array(
	'type'              => 'option',
	'default'           => esc_html__( 'Load content', 'full-picture-analytics-cookie-notice' ),
	'sanitize_callback' => 'sanitize_text_field',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'fupi_ifrblock[btn_text]', array(
	'label'   => esc_html__( 'Consent button text', 'full-picture-analytics-cookie-notice' ),
	'section' => 'fupi_ifrblock_text',
	'type'    => 'text',
) );
