<?php

$option_arr_id = 'fupi_atrig';

$sections = array( 
    array(
        'section_id' => 'fupi_atrig_click',
        'section_title' => esc_html__( 'Clicks', 'full-picture-analytics-cookie-notice' ),
        'fields' => array(
            array(
                'type'	 			=> 'triggers_builder',
                'label' 			=> esc_html__( 'Page elements', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'click_triggers',
                'option_arr_id'		=> $option_arr_id,
                'class'				=> 'fupi_r3_fullwidth',
                'fields'			=> array(
                    array(
                        'placeholder'  		=> esc_html__('Name', 'full-picture-analytics-cookie-notice' ),
                        'type'				=> 'text',
                        'field_id'			=> 'name',
                        'class'		        => 'fupi_col_20_grow',
                        'el_class'			=> 'fupi_internal_title',
                        'required'          => true,
                    ),
                   
                    array(
                        'type'				=> 'text',
                        'label'		        => esc_html__( 'CSS selector', 'full-picture-analytics-cookie-notice' ),
                        'field_id'			=> 'sel',
                        'class'		        => 'fupi_col_40_grow fupi_inline_label',
                        'required'          => true,
                    ),
                    array(
                        'type'              => 'hidden',
                        'field_id'          => 'id',
                        'el_class'          => 'fupi_generate_id'
                    ),
                ),
            ),
        ),
    ),
    
    array(
        'section_id' => 'fupi_atrig_aff_click',
        'section_title' => esc_html__( 'Affiliate links', 'full-picture-analytics-cookie-notice' ),
        'fields' => array(
            array(
                'type'	 			=> 'triggers_builder',
                'label' 			=> esc_html__( 'Affiliate links', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'aff_click_triggers',
                'option_arr_id'		=> $option_arr_id,
                'class'				=> 'fupi_r3_fullwidth',
                'fields'			=> array(
                    array(
                        'placeholder' 		=> esc_html__('Name', 'full-picture-analytics-cookie-notice' ),
                        'type'				=> 'text',
                        'field_id'			=> 'name',
                        'class'		        => 'fupi_col_20_grow',
                        'el_class'			=> 'fupi_internal_title',
                        'required'          => true,
                    ),
                   
                    array(
                        'type'				=> 'text',
                        'label'		        => esc_html__( 'Unique link part', 'full-picture-analytics-cookie-notice' ),
                        'placeholder'       => esc_html__( 'e.g. /go/, ?ref=', 'full-picture-analytics-cookie-notice' ),
                        'field_id'			=> 'sel',
                        'class'		        => 'fupi_col_40_grow fupi_inline_label',
                        'required'          => true,
                    ),
                    array(
                        'type'              => 'hidden',
                        'field_id'          => 'id',
                        'el_class'          => 'fupi_generate_id'
                    ),
                ),
            ),
        ),
    ),
    array(
        'section_id' => 'fupi_atrig_view',
        'section_title' => esc_html__( 'Views of page elements', 'full-picture-analytics-cookie-notice' ),
        'fields' => array(
            array(
                'type'	 			=> 'triggers_builder',
                'label' 			=> esc_html__( 'Page elements', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'view_triggers',
                'option_arr_id'		=> $option_arr_id,
                'class'				=> 'fupi_r3_fullwidth',
                'fields'			=> array(
                    array(
                        'placeholder'  		=> esc_html__('Name', 'full-picture-analytics-cookie-notice' ),
                        'type'				=> 'text',
                        'field_id'			=> 'name',
                        'class'		        => 'fupi_col_20_grow',
                        'el_class'			=> 'fupi_internal_title',
                        'required'          => true,
                    ),
                    array(
                        'type'				=> 'text',
                        'label'		        => esc_html__( 'CSS selector', 'full-picture-analytics-cookie-notice' ),
                        'field_id'			=> 'sel',
                        'class'		        => 'fupi_col_40_grow fupi_inline_label',
                        'required'          => true,
                    ),
                    array(
                        'type'              => 'hidden',
                        'field_id'          => 'id',
                        'el_class'          => 'fupi_generate_id'
                    ),
                ),
                'before_field'       => '<p style="margin-bottom: 10px;">' .esc_html__( 'Important. A selector can match multiple elements. The first impression of every matched element triggers an event.', 'full-picture-analytics-cookie-notice' ) . '</p>',
            ),
        ),
    ),
    array(
        'section_id' => 'fupi_atrig_forms',
        'section_title' => esc_html__( 'Forms', 'full-picture-analytics-cookie-notice' ),
        'fields' => array(
            array(
                'type'	 			=> 'triggers_builder',
                'trigger_builder'   => true,
                'label' 			=> esc_html__( 'Forms', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'form_triggers',
                'option_arr_id'		=> $option_arr_id,
                'class'				=> 'fupi_r3_fullwidth',
                'fields'			=> array(
                    array(
                        'placeholder'  		=> esc_html__('Name', 'full-picture-analytics-cookie-notice' ),
                        'type'				=> 'text',
                        'field_id'			=> 'name',
                        'class'		        => 'fupi_col_20_grow',
                        'el_class'			=> 'fupi_internal_title',
                        'required'          => true,
                    ),
                   
                    array(
                        'type'				=> 'text',
                        'label'		        => esc_html__( 'CSS selector', 'full-picture-analytics-cookie-notice' ),
                        'field_id'			=> 'sel',
                        'class'		        => 'fupi_col_40_grow fupi_inline_label',
                        'required'          => true,
                    ),
                    array(
                        'type'              => 'hidden',
                        'field_id'          => 'id',
                        'el_class'          => 'fupi_generate_id'
                    ),
                ),
            ),
        ),
    ),
    array(
        'section_id' => 'fupi_atrig_custom',
        'section_title' => esc_html__( 'Custom triggers', 'full-picture-analytics-cookie-notice' ),
        'fields' => array(
            array(
                'type'	 			=> 'triggers_builder',
                'label' 			=> esc_html__( 'Define what you want to track', 'full-picture-analytics-cookie-notice' ),
                'field_id' 			=> 'triggers',
                'must_have'         => 'pro',
                'option_arr_id'		=> $option_arr_id,
                'class'				=> 'fupi_fullwidth_tr',
                'el_class'			=> 'advanced_triggers_builder',
                'fields'			=> array(
                    array(
                        'label'     		=> esc_html__('Name', 'full-picture-analytics-cookie-notice' ),
                        'type'				=> 'text',
                        'field_id'			=> 'name',
                        'class'		        => 'fupi_r3_table_row',
                        'el_class'			=> 'fupi_internal_title',
                        'required'          => true,
                    ),
                    array(
                        'type'				=> 'select2',
                        'label'				=> esc_html__( 'When this happens:', 'full-picture-analytics-cookie-notice' ),
                        'field_id'			=> 'events',
                        'class'		        => 'fupi_r3_table_row',
                        'options'			=> array(
                            'click'				    => esc_html__( 'Visitor clicked something', 'full-picture-analytics-cookie-notice' ),
                            'form_submit'		    => esc_html__( 'Visitor submitted a form', 'full-picture-analytics-cookie-notice' ),
                            'scroll'		        => esc_html__( 'Visitor scrolled a page', 'full-picture-analytics-cookie-notice' ),
                            'url_change'		    => esc_html__( 'Page URL changed without refreshing', 'full-picture-analytics-cookie-notice' ),
                            'dom_loaded'		    => esc_html__( 'Page loaded', 'full-picture-analytics-cookie-notice' ),
                            'page_in_blur'		    => esc_html__( 'Page lost focus (no longer viewed but not closed)', 'full-picture-analytics-cookie-notice' ),
                            'mouse_leave_window'    => esc_html__( 'Cursor left browser window', 'full-picture-analytics-cookie-notice' ),
                            'page_in_focus'		    => esc_html__( 'Page regained focus (user returned to viewing an opened page)', 'full-picture-analytics-cookie-notice' ),
                            // 'privacy_consents_changed' => esc_html__( 'Privacy consents changed', 'full-picture-analytics-cookie-notice' ),
                            'active_time_tick'	    => esc_html__( 'Do every second', 'full-picture-analytics-cookie-notice' ),
                            'woo_add_to_cart'	    => esc_html__( '(WooCommerce) A product was added to cart', 'full-picture-analytics-cookie-notice' ),
                            'woo_remove_from_cart'	=> esc_html__( '(WooCommerce) A product was removed from cart', 'full-picture-analytics-cookie-notice' ),
                            'woo_add_to_wishlist'   => esc_html__( '(WooCommerce) A product was added to a wishlist', 'full-picture-analytics-cookie-notice' ),
                        ),
                        'multiple'		=> true,
                        'default'       => 'click'
                    ),
                    array(
                        'type'				=> 'r3',
                        'label'				=> esc_html__( 'Check these conditions:', 'full-picture-analytics-cookie-notice' ),
                        'field_id'			=> 'cond',
                        'class'		        => 'fupi_r3_table_row',
                        'is_repeater'		=> true,
                        'fields'			=> array(
                            array(
                                'placeholder'		=> esc_html__( 'Data path', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'dpath',
                                'class'		        => 'fupi_col_25_grow',
                            ),
                            array(
                                'type'				=> 'select',
                                'field_id'			=> 'comp',
                                'options'			=> array(
                                    'exists'			=> esc_html__( 'exists', 'full-picture-analytics-cookie-notice' ),
                                    'not_exists'		=> esc_html__( 'does not exist', 'full-picture-analytics-cookie-notice' ),
                                    'eq'				=> esc_html__( 'equals', 'full-picture-analytics-cookie-notice' ),
                                    'neq'				=> esc_html__( 'does not equal', 'full-picture-analytics-cookie-notice' ),
                                    'gt'				=> esc_html__( 'greater than', 'full-picture-analytics-cookie-notice' ),
                                    'lt'				=> esc_html__( 'lower than', 'full-picture-analytics-cookie-notice' ),
                                    'arr_len_gt'		=> esc_html__( 'array length is greater than', 'full-picture-analytics-cookie-notice' ),
                                    'arr_len_lt'		=> esc_html__( 'array length is lower than', 'full-picture-analytics-cookie-notice' ),
                                    'cont'				=> esc_html__( 'contains text', 'full-picture-analytics-cookie-notice' ),
                                    'ncont'				=> esc_html__( 'does not contain text', 'full-picture-analytics-cookie-notice' ),
                                    'eq_sel'			=> esc_html__( 'matches CSS selector', 'full-picture-analytics-cookie-notice' ),
                                    'neq_sel'			=> esc_html__( 'does not match CSS selector', 'full-picture-analytics-cookie-notice' ),
                                    'parent'			=> esc_html__( 'has HTML parent', 'full-picture-analytics-cookie-notice' ),
                                    'bool'				=> esc_html__( 'is boolean', 'full-picture-analytics-cookie-notice' ),
                                ),
                                'class'		        => 'fupi_col_20',
                            ),
                            array(
                                'placeholder'		=> esc_html__( 'Value', 'full-picture-analytics-cookie-notice' ),
                                'type'				=> 'text',
                                'field_id'			=> 'val',
                                'class'		        => 'fupi_col_20',
                            ),
                        )
                    ),
                    array(
                        'type'              => 'hidden',
                        'field_id'          => 'id',
                        'el_class'          => 'fupi_generate_id'
                    ),
                    array(
                        'label'				=> esc_html__('Debug this trigger', 'full-picture-analytics-cookie-notice' ),
                        'type'				=> 'toggle',
                        'field_id'			=> 'debug',
                        'class'		        => 'fupi_r3_table_row fupi_col_50',
                    ),
                ),
            ),
        ), // fields
    ), // section
); // sections