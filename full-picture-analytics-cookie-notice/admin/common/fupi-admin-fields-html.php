<?php

class Fupi_Admin_Fields_Html {
    private $cook;

    private $main;

    private $track;

    private $tools;

    private $is_woo_enabled;

    private $fupi_modules;

    private $fupi_modules_names;

    public function __construct( $context = [] ) {
        $this->cook = ( isset( $context['cook'] ) && is_array( $context['cook'] ) ? $context['cook'] : [] );
        $this->main = ( isset( $context['main'] ) && is_array( $context['main'] ) ? $context['main'] : [] );
        $this->track = ( isset( $context['track'] ) && is_array( $context['track'] ) ? $context['track'] : [] );
        $this->tools = ( isset( $context['tools'] ) && is_array( $context['tools'] ) ? $context['tools'] : [] );
        $this->is_woo_enabled = !empty( $context['is_woo_enabled'] );
        $this->fupi_modules = ( isset( $context['fupi_modules'] ) && is_array( $context['fupi_modules'] ) ? $context['fupi_modules'] : [] );
        $this->fupi_modules_names = ( isset( $context['fupi_modules_names'] ) && is_array( $context['fupi_modules_names'] ) ? $context['fupi_modules_names'] : [] );
    }

    public function fupi_field_html( $opts, $field_id = false, $saved_value = false ) {
        echo $this->render( $opts, $field_id, $saved_value );
    }

    public function render( $opts, $field_id = false, $saved_value = false ) {
        ob_start();
        // fupi_field_html($opts, $values, $arr_id)
        // -	$opts 		array 		no default			opts for setting up a single field
        // -	$field_id 		string  	default: false 		field's name and ID (optional - passed by r3)
        // -	$saved_value 	array 		default: false 		saved value of this field (optional - passed by r3)
        //
        // ! important - remember that this code generates a SINGLE field (or a repeater / multi field) and not ALL fields
        // GET SAVED VALUES
        // if we don't have any - values are passed by r3 repeater
        if ( empty( $field_id ) ) {
            // if id of options array is given (for serialized data)
            if ( isset( $opts['option_arr_id'] ) ) {
                if ( !$saved_value ) {
                    // get the options array (returns false if not exists)
                    $saved_data_arr = get_option( $opts['option_arr_id'] );
                    if ( !empty( $saved_data_arr ) && isset( $saved_data_arr[$opts['field_id']] ) ) {
                        // if options array and field value exist
                        $saved_value = $saved_data_arr[$opts['field_id']];
                        // get the value of the field we are about to generate
                    } else {
                        // if array doesn't exist
                        $saved_value = ( isset( $opts['default'] ) ? $opts['default'] : false );
                        // set default or nothing
                    }
                }
                $field_id = $opts['option_arr_id'] . '[' . $opts['field_id'] . ']';
                // get correct field id
                // for unserialized data (not used anywhere in the plugin)
            } else {
                $saved_value = get_option( $opts['field_id'] );
                // get the current field value (returns false if not exists)
                if ( !$saved_value ) {
                    $value = ( isset( $opts['default'] ) ? $opts['default'] : false );
                }
                // set default or nothing
                $field_id = $opts['field_id'];
            }
        } else {
            $is_r3 = true;
        }
        // CHECK IF THE FIELD IS AVAILABLE
        $must_have_parts = [];
        $must_have_html = '';
        $el_class = '';
        $already_echoed_after_field = false;
        if ( !empty( $opts['must_have'] ) ) {
            $must_have_val = esc_attr( $opts['must_have'] );
            // break must have into array
            $must_have_arr = explode( ' ', $must_have_val );
            foreach ( $must_have_arr as $must_have ) {
                // check for missing licence
                if ( $must_have == 'pro' || $must_have == 'pro_round' ) {
                    $class_suffix = ( $must_have == 'pro_round' ? '_round' : '' );
                    $must_have_html = '<div class="fupi_must_have_pro_ico' . $class_suffix . '"><span class="dashicons dashicons-lock" aria-hidden="true"></span><a class="fupi_get_pro_text" href="https://wpfullpicture.com/pricing/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Get Pro', 'full-picture-analytics-cookie-notice' ) . '</a></div>';
                    $must_have_parts = [];
                    break;
                    // check for missing privacy policy
                } else {
                    if ( $must_have == 'privacy_policy' ) {
                        $pp_is_ok = false;
                        if ( !empty( $this->cook['pp_id'] ) ) {
                            $pp_id = (int) $this->cook['pp_id'];
                            $pp_is_ok = get_post_status( $pp_id ) == 'publish';
                        }
                        if ( !$pp_is_ok ) {
                            $must_have_parts[] = esc_html__( 'Save Privacy Policy page ID', 'full-picture-analytics-cookie-notice' );
                        }
                        // check for missing admin capabilities
                    } else {
                        if ( $must_have == 'admin' ) {
                            $is_admin = current_user_can( 'manage_options' );
                            if ( !$is_admin ) {
                                $must_have_parts[] = esc_html__( 'Administrator role', 'full-picture-analytics-cookie-notice' );
                            }
                        } else {
                            if ( $must_have == 'geo' ) {
                                if ( empty( $this->main['geo'] ) ) {
                                    $must_have_parts[] = esc_html__( 'Enable Geolocation in related settings', 'full-picture-analytics-cookie-notice' );
                                }
                            } else {
                                if ( $must_have == 'author_id' ) {
                                    if ( empty( $this->track['show_author_id'] ) ) {
                                        $must_have_parts[] = esc_html__( 'Enable author IDs in related settings', 'full-picture-analytics-cookie-notice' );
                                    }
                                    // check for disabled woo module or woo plugin
                                } else {
                                    if ( $must_have == 'woo' ) {
                                        if ( !$this->is_woo_enabled ) {
                                            $must_have_parts[] = esc_html__( 'WooCommerce Tracking module', 'full-picture-analytics-cookie-notice' );
                                        }
                                    } else {
                                        if ( str_starts_with( $must_have, 'field' ) ) {
                                            $field_params_a = explode( '|', $must_have );
                                            if ( count( $field_params_a ) == 5 ) {
                                                $other_opt_name = $field_params_a[1];
                                                $other_field_id = $field_params_a[2];
                                                $expected_field_val = $field_params_a[3];
                                                $must_have_text = str_replace( '"', '', $field_params_a[4] );
                                                $must_have_text = str_replace( '_', ' ', $must_have_text );
                                                $other_option_data = get_option( $other_opt_name );
                                                if ( empty( $other_option_data ) || empty( $other_option_data[$other_field_id] ) ) {
                                                    $must_have_parts[] = $must_have_text;
                                                } else {
                                                    $field_value = $other_option_data[$other_field_id];
                                                    $value_matches = $expected_field_val == 'exists' || $expected_field_val == $field_value;
                                                    if ( !$value_matches ) {
                                                        $must_have_parts[] = $must_have_text;
                                                    }
                                                }
                                            }
                                            // check for missing modules
                                        } else {
                                            if ( !isset( $this->tools[$must_have] ) ) {
                                                foreach ( $this->fupi_modules as $module ) {
                                                    if ( $module['id'] == $must_have ) {
                                                        $module_name = ( isset( $this->fupi_modules_names[$module['id']] ) ? $this->fupi_modules_names[$module['id']] : $module['name'] );
                                                        $must_have_parts[] = esc_html__( 'Module', 'full-picture-analytics-cookie-notice' ) . ' "' . $module_name . '"';
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ( count( $must_have_parts ) > 0 ) {
                $must_have_html = '<div class="fupi_must_have_info">';
                if ( count( $must_have_parts ) > 0 ) {
                    $must_have_html .= esc_html__( 'Requires', 'full-picture-analytics-cookie-notice' ) . ': <span class="fupi_req">' . join( '</span> <span class="fupi_req">', $must_have_parts ) . '</span>';
                }
                $must_have_html .= '</div>';
            }
        }
        // GET DATA COMMON TO MOST FIELDS
        $placeholder = ( isset( $opts['placeholder'] ) ? $opts['placeholder'] : '' );
        $el_class .= ( !empty( $opts['el_class'] ) ? $opts['el_class'] : '' );
        if ( isset( $opts['required'] ) ) {
            $el_class .= ' fupi_req';
        }
        $el_data_target = ( !empty( $opts['el_data_target'] ) ? $opts['el_data_target'] : '' );
        $data_format = ( !empty( $opts['format'] ) ? 'data-dataformat="' . $opts['format'] . '"' : '' );
        // BUILD HTML
        // before_field
        if ( isset( $opts['before_field'] ) ) {
            printf( '<span class="fupi_before_field">%s</span>', $opts['before_field'] );
            // Show it
        }
        // Check which type of field we want
        switch ( $opts['type'] ) {
            //
            // @R3
            //
            case 'r3':
                $fields = ( isset( $opts['fields'] ) && is_array( $opts['fields'] ) ? $opts['fields'] : false );
                if ( !$fields ) {
                    return;
                }
                $i = 0;
                $filter_by_field = ( !empty( $opts['filter_by_field'] ) ? $opts['filter_by_field'] : '' );
                $filter = ( !empty( $opts['filter'] ) && is_array( $opts['filter'] ) ? $opts['filter'] : [] );
                $section_classes = ( !empty( $opts['section_classes'] ) ? ' ' . $opts['section_classes'] : '' );
                $repeater = !empty( $opts['is_repeater'] );
                $btn_style = ( !empty( $opts['btn_style'] ) ? $opts['btn_style'] : '' );
                $btns_class = ( !empty( $opts['btns_class'] ) ? $opts['btns_class'] : '' );
                $sections_nr = ( $repeater && is_array( $saved_value ) ? count( $saved_value ) : 0 );
                // classes common to all sections
                if ( empty( $saved_value ) ) {
                    $section_classes .= ' fupi_empty_r3 ';
                }
                if ( $repeater ) {
                    $section_classes .= ' fupi_r3_repeater ';
                }
                // CLASSIC R3 LOOP
                for ($i = 0; $i <= $sections_nr; $i++) {
                    // skip an empty section if it doesn't have any values
                    if ( $sections_nr > 0 && empty( $saved_value[$i] ) ) {
                        continue;
                    }
                    // SECTION START
                    $empty_class = '';
                    // add class "fupi_empty_r3"
                    // JS will remove this section
                    // !! this class is not added to $section_classes because they are set before the loop
                    if ( !empty( $filter_by_field ) && !empty( $saved_value[$i][$filter_by_field] ) ) {
                        if ( in_array( $saved_value[$i][$filter_by_field], $filter ) ) {
                            $empty_class = 'fupi_empty_r3';
                        }
                    }
                    echo '<div class="fupi_r3_section ' . $section_classes . $empty_class . '">';
                    foreach ( $fields as $field_opts ) {
                        $f_id = ( !empty( $field_opts['field_id'] ) ? $field_opts['field_id'] : '' );
                        $f_default = ( !empty( $field_opts['default'] ) ? $field_opts['default'] : '' );
                        $f_label = ( !empty( $field_opts['label'] ) ? $field_opts['label'] : false );
                        $f_type = ( !empty( $field_opts['type'] ) ? $field_opts['type'] : '' );
                        $class = ( !empty( $field_opts['class'] ) ? $field_opts['class'] : '' );
                        $hidden_style = ( $f_type == 'hidden' ? 'style="display: none"' : '' );
                        if ( $repeater ) {
                            $f_name = $field_id . '[' . $i . '][' . $f_id . ']';
                            $f_val = ( isset( $saved_value[$i][$f_id] ) ? $saved_value[$i][$f_id] : $f_default );
                        } else {
                            $f_name = $field_id . '[' . $f_id . ']';
                            $f_val = ( isset( $saved_value[$f_id] ) ? $saved_value[$f_id] : $f_default );
                        }
                        // (optional) start a wrapper around a group of fields
                        if ( isset( $field_opts['start_sub_section'] ) ) {
                            if ( !empty( $field_opts['class'] ) && str_contains( $field_opts['class'], 'fupi_adv_group' ) ) {
                                echo '<div class="fupi_r3_fields_group fupi_adv">';
                            } else {
                                echo '<div class="fupi_r3_fields_group">';
                            }
                        }
                        echo '<div class="fupi_r3_field fupi_field_type_' . $f_type . ' fupi_field_' . $f_id . '_wrap ' . $class . '" ' . $hidden_style . '>';
                        if ( !empty( $f_label ) && $f_type != 'checkbox' ) {
                            if ( $f_type == 'r3' ) {
                                echo '<p class="fupi_r3_section_label">' . esc_attr( $f_label ) . '</p>';
                            } else {
                                echo '<label class="fupi_r3_field_label">' . esc_attr( $f_label ) . '</label>';
                            }
                        }
                        if ( $f_type != 'label' ) {
                            $r3_subfield_class = ( $field_opts['type'] == 'r3' ? ' fupi_r3_subfield_section' : false );
                            echo '<div class="fupi_r3_subfield' . $r3_subfield_class . '">';
                            $this->fupi_field_html( $field_opts, $f_name, $f_val );
                            echo '</div>';
                        }
                        echo '</div>';
                        // (optional) start a new sub section wrapper
                        if ( isset( $field_opts['end_sub_section'] ) ) {
                            echo '</div>';
                        }
                    }
                    // FINAL FIELDS
                    if ( $repeater && $btn_style != 'none' ) {
                        if ( $btn_style == 'confirm' ) {
                            echo '<div class="fupi_r3_btns' . $btns_class . '">
									<button type="button" class="fupi_remove_double_confirm_btn button-secondary" data-default_text="' . esc_html__( 'Delete', 'full-picture-analytics-cookie-notice' ) . '" data-confirm_text="' . esc_html__( 'Are you sure?', 'full-picture-analytics-cookie-notice' ) . '">' . esc_html__( 'Delete', 'full-picture-analytics-cookie-notice' ) . '</button>
								</div>';
                        } else {
                            echo '<div class="fupi_r3_btns' . $btns_class . '">
									<button type="button" class="fupi_r3_btn fupi_r3_remove_btn dashicons dashicons-minus"></button>
									<button type="button" class="fupi_r3_btn fupi_r3_add_btn dashicons dashicons-plus-alt2"></button>
								</div>';
                        }
                    }
                    echo '</div>';
                }
                break;
            // =======
            case 'triggers_builder':
                // MAKE OPTS FOR R3
                $opts_for_r3 = $opts;
                $opts_for_r3['is_repeater'] = true;
                $opts_for_r3['type'] = 'r3';
                $opts_for_r3['btn_style'] = 'confirm';
                $opts_for_r3['section_classes'] = 'fupi_r3_trigger_builder';
                unset($opts_for_r3['must_have']);
                unset($opts_for_r3['before_field']);
                unset($opts_for_r3['after_field']);
                unset($opts_for_r3['under_field']);
                // CREATE FIELDS
                $this->fupi_field_html( $opts_for_r3, false, $saved_value );
                // "ADD" BUTTON
                echo '<div class="fupi_r3_add_end_btn_wrap">
					<button type="button" class="button-secondary fupi_r3_add_end_btn">' . esc_html__( 'Add new', 'full-picture-analytics-cookie-notice' ) . '</button>
				</div>';
                break;
            // =======
            case 'visitor_scoring':
                // if ( empty ( $this->tools['vscor'] ) ) {
                // 	echo '<div class="fupi_required_text">Required: Visitor scoring module</div>';
                // 	return;
                // }
                $vscor_opts = get_option( 'fupi_vscor' );
                // get saved score levels
                $vscor_levels_str = ( !empty( $vscor_opts['lead_scoring_levels'] ) ? esc_attr( $vscor_opts['lead_scoring_levels'] ) : '' );
                $trimmed_string = trim( $vscor_levels_str );
                $score_levels = [
                    10,
                    20,
                    30,
                    40,
                    50
                ];
                if ( !empty( $trimmed_string ) ) {
                    $number_list = preg_replace( '/[^0-9,]/', '', $trimmed_string );
                    $numbers = array_filter( array_map( 'trim', explode( ',', $number_list ) ), 'strlen' );
                    $numbers = array_unique( array_map( 'intval', $numbers ) );
                    sort( $numbers, SORT_NUMERIC );
                    if ( count( $numbers ) > 0 ) {
                        $score_levels = $numbers;
                    }
                }
                // Check which events to show and which to hide (because their triggers no longer exist)
                $filter = [];
                $used_in_evts = [];
                if ( !empty( $saved_value ) ) {
                    foreach ( $saved_value as $i => $value ) {
                        $evt_score_level = ( !empty( $value['atrig_id'] ) ? $value['atrig_id'] : '' );
                        $found_trigger = false;
                        // check if the trigger ID that the event uses, exists
                        foreach ( $score_levels as $score_level ) {
                            if ( 'fp_leadscore_' . $score_level == $evt_score_level ) {
                                $found_trigger = true;
                                array_push( $used_in_evts, $evt_score_level );
                                break;
                            }
                        }
                        // if the event was set using a score level that no longer exists, then we will filter it out
                        if ( !empty( $evt_score_level ) && !$found_trigger ) {
                            array_push( $filter, $evt_score_level );
                        }
                    }
                }
                // MAKE OPTS FOR R3
                $opts_for_r3 = $opts;
                $opts_for_r3['is_repeater'] = true;
                $opts_for_r3['type'] = 'r3';
                $opts_for_r3['btn_style'] = 'none';
                $opts_for_r3['section_classes'] = 'fupi_r3_event_builder fupi_hidden';
                unset($opts_for_r3['related_opts']);
                unset($opts_for_r3['must_have']);
                unset($opts_for_r3['before_field']);
                unset($opts_for_r3['after_field']);
                unset($opts_for_r3['under_field']);
                unset($opts_for_r3['offscreen']);
                unset($opts_for_r3['offscreen_warning']);
                unset($opts_for_r3['offscreen_important']);
                unset($opts_for_r3['offscreen_id']);
                unset($opts_for_r3['offscreen_warning_id']);
                unset($opts_for_r3['offscreen_important_id']);
                if ( count( $filter ) > 0 ) {
                    $opts_for_r3['filter_by_field'] = 'atrig_id';
                    $opts_for_r3['filter'] = $filter;
                }
                // CREATE FIELDS
                $this->fupi_field_html( $opts_for_r3, false, $saved_value );
                // BOTTOM BUTTONS
                foreach ( $score_levels as $score ) {
                    $used_in_evt = in_array( 'fp_leadscore_' . $score, $used_in_evts );
                    $used_in_evt_class = ( $used_in_evt ? ' fupi_trigger_used_in_evt' : '' );
                    echo '<button type="button" class="fupi_r3_builder_evt_settings_btn ' . $used_in_evt_class . '" data-trigger_id="fp_leadscore_' . $score . '">Visitor collects ' . $score . ' points</button>';
                }
                // CHANGE btn
                echo '<button type="button" class="button-secondary fupi_open_dynamic_settings fupi_addEdit_elements_btn" data-module-id="vscor" data-field_ids="lead_scoring_levels">' . esc_html__( 'Change', 'full-picture-analytics-cookie-notice' ) . '</button>';
                break;
            // =======
            case 'events_builder':
                $atrig_opts = get_option( 'fupi_atrig' );
                $atrig_field_id = $opts['for'];
                $triggers_data = ( !empty( $atrig_opts[$atrig_field_id] ) ? $atrig_opts[$atrig_field_id] : '' );
                $has_triggers_data = !empty( $triggers_data );
                // Check which events to show and which to hide (because their triggers no longer exist)
                $filter = [];
                if ( $has_triggers_data && !empty( $saved_value ) ) {
                    foreach ( $saved_value as $i => $value ) {
                        $event_trigger_id = $saved_value[$i]['atrig_id'];
                        $found_trigger = false;
                        // check if the trigger ID that the event uses, exists
                        foreach ( $triggers_data as $trigger_i => $trigger_data ) {
                            if ( $trigger_data['id'] == $event_trigger_id ) {
                                $found_trigger = true;
                                $triggers_data[$trigger_i]['used_in_evt'] = true;
                                break;
                            }
                        }
                        if ( !$found_trigger ) {
                            array_push( $filter, $event_trigger_id );
                        }
                    }
                }
                // MAKE OPTS FOR R3
                $opts_for_r3 = $opts;
                $opts_for_r3['is_repeater'] = true;
                $opts_for_r3['type'] = 'r3';
                $opts_for_r3['btn_style'] = 'none';
                $opts_for_r3['section_classes'] = 'fupi_r3_event_builder fupi_hidden';
                unset($opts_for_r3['related_opts']);
                unset($opts_for_r3['must_have']);
                unset($opts_for_r3['after_field']);
                unset($opts_for_r3['under_field']);
                unset($opts_for_r3['offscreen']);
                unset($opts_for_r3['offscreen_warning']);
                unset($opts_for_r3['offscreen_important']);
                unset($opts_for_r3['offscreen_id']);
                unset($opts_for_r3['offscreen_warning_id']);
                unset($opts_for_r3['offscreen_important_id']);
                if ( count( $filter ) > 0 ) {
                    $opts_for_r3['filter_by_field'] = 'atrig_id';
                    $opts_for_r3['filter'] = $filter;
                }
                // CREATE FIELDS
                $this->fupi_field_html( $opts_for_r3, false, $saved_value );
                // BOTTOM BUTTONS
                if ( $has_triggers_data ) {
                    foreach ( $triggers_data as $trigger_data ) {
                        $used_in_evt = isset( $trigger_data['used_in_evt'] );
                        $used_in_evt_class = ( $used_in_evt ? ' fupi_trigger_used_in_evt' : '' );
                        echo '<button type="button" class="fupi_r3_builder_evt_settings_btn ' . $used_in_evt_class . '" data-trigger_id="' . $trigger_data['id'] . '">' . $trigger_data['name'] . '</button>';
                    }
                }
                // ADD / EDIT button
                echo '<button type="button" class="button-secondary fupi_open_dynamic_settings fupi_addEdit_elements_btn" data-module-id="atrig" data-field_ids="' . $opts['for'] . '">' . esc_html__( 'Add / Edit', 'full-picture-analytics-cookie-notice' ) . '</button>';
                break;
            // =======
            case 'add_metadata':
                // MAKE OPTS FOR R3
                $opts_for_r3 = $opts;
                $opts_for_r3['is_repeater'] = true;
                $opts_for_r3['type'] = 'r3';
                $opts_for_r3['btn_style'] = 'standard';
                $opts_for_r3['section_classes'] = 'fupi_r3_addMetadata_builder';
                unset($opts_for_r3['related_opts']);
                unset($opts_for_r3['must_have']);
                unset($opts_for_r3['after_field']);
                unset($opts_for_r3['under_field']);
                unset($opts_for_r3['offscreen']);
                unset($opts_for_r3['offscreen_warning']);
                unset($opts_for_r3['offscreen_important']);
                unset($opts_for_r3['offscreen_id']);
                unset($opts_for_r3['offscreen_warning_id']);
                unset($opts_for_r3['offscreen_important_id']);
                // CREATE FIELDS
                $this->fupi_field_html( $opts_for_r3, false, $saved_value );
                // ADD / EDIT BTN
                echo '<button type="button" class="button-secondary fupi_open_dynamic_settings fupi_addEdit_metadata_btn fupi_not_builder_btn" data-module-id="track" data-field_ids="custom_data_ids">' . esc_html__( 'Add / Edit metadata', 'full-picture-analytics-cookie-notice' ) . '</button>';
                break;
            // =======
            case 'text':
            case 'number':
            case 'url':
            case 'email':
            case 'password':
                // If it is a text field
                $disabled = ( !empty( $opts['disabled'] ) ? ' disabled="' . esc_attr( $opts['disabled'] ) . '"' : '' );
                $multiple_attr = ( $opts['type'] == 'email' ? 'multiple' : '' );
                $min = '';
                if ( $opts['type'] == 'number' ) {
                    printf( '<div class="fupi_number_field_wrap">
					<button type="button" class="fupi_number_decrease fupi_number_btn"><span class="dashicons dashicons-minus"></span></button>' );
                    if ( isset( $opts['min'] ) ) {
                        $min = 'min="' . (int) $opts['min'] . '"';
                    }
                }
                printf(
                    '<input type="' . $opts['type'] . '" name="%1$s" id="%1$s" placeholder="%2$s" value="%3$s" class="%4$s" data-target="%5$s" %6$s %7$s %8$s %9$s/>',
                    $field_id,
                    $placeholder,
                    esc_attr( $saved_value ),
                    $el_class,
                    $el_data_target,
                    $disabled,
                    $data_format,
                    $multiple_attr,
                    $min
                );
                if ( $opts['type'] == 'number' ) {
                    printf( '<button type="button" class="fupi_number_increase fupi_number_btn"><span class="dashicons dashicons-plus-alt2"></span></button>' );
                    if ( isset( $opts['after_field'] ) && !$already_echoed_after_field ) {
                        $already_echoed_after_field = true;
                        printf( '<span class="fupi_after_field" style="margin-left: 7px;"> %s</span>', $opts['after_field'] );
                    }
                    printf( '</div>' );
                }
                break;
            // =======
            case 'toggle':
                $el_data_name = ( !empty( $opts['el_data_name'] ) ? $opts['el_data_name'] : '' );
                printf(
                    '<label class="fupi_switch"><input type="checkbox" name="%1$s" id="%1$s" value="1" %2$s class="%3$s" data-target="%4$s" data-name="%5$s"/><span class="fupi_switch_slider"></span></label>',
                    $field_id,
                    checked( 1, $saved_value, false ),
                    $el_class,
                    $el_data_target,
                    $el_data_name
                );
                break;
            // =======
            case 'checkbox':
                if ( !empty( $is_r3 ) ) {
                    echo '<label>';
                }
                printf(
                    '<input type="checkbox" name="%1$s" id="%1$s" value="1" %2$s class="%3$s" data-target="%4$s"/>',
                    $field_id,
                    checked( 1, $saved_value, false ),
                    $el_class,
                    $el_data_target
                );
                if ( !empty( $is_r3 ) ) {
                    echo $opts['label'] . '</label>';
                }
                break;
            // =======
            case 'select2':
                if ( !empty( $opts['options'] ) && is_array( $opts['options'] ) ) {
                    $multiple = ( empty( $opts['multiple'] ) ? '' : 'multiple="multiple"' );
                    $save_as_multiple = ( empty( $opts['multiple'] ) ? '' : '[]' );
                    if ( !empty( $saved_value ) ) {
                        // make sure that saved value is an array - for use in multi selects
                        if ( !empty( $opts['multiple'] ) && !is_array( $saved_value ) ) {
                            $saved_value = array($saved_value);
                        }
                    } else {
                        // use default if we have it
                        if ( isset( $opts['default'] ) ) {
                            // make sure the default is saved as array for mul selects
                            if ( empty( $opts['multiple'] ) && !is_array( $opts['default'] ) ) {
                                $saved_value = array($opts['default']);
                            }
                        }
                    }
                    $output = '<select name="' . $field_id . $save_as_multiple . '" class="fupi_select2 ' . $el_class . '" ' . $multiple . '>';
                    foreach ( $opts['options'] as $key => $label ) {
                        // Get html that marks option as selected
                        $selected_html = '';
                        if ( empty( $opts['multiple'] ) ) {
                            $selected_html = selected( $saved_value, $key, false );
                            // multi-select keeps values in an array
                        } else {
                            if ( !empty( $saved_value ) && is_array( $saved_value ) ) {
                                foreach ( $saved_value as $val ) {
                                    if ( $val == $key ) {
                                        $selected_html = 'selected="selected"';
                                        break;
                                    }
                                }
                            }
                        }
                        $output .= '<option value="' . $key . '" ' . $selected_html . '>' . $label . '</option>';
                    }
                    $output .= '</select>';
                    echo $output;
                }
                break;
            // =======
            case 'user_search':
                $selected_users = ( current_user_can( 'manage_options' ) && is_array( $saved_value ) ? $saved_value : array() );
                echo '<select name="' . $field_id . '[]" id="' . $field_id . '" class="fupi_select2 fupi_user_search ' . $el_class . '" multiple="multiple" data-placeholder_text="' . esc_html__( 'Search users...', 'full-picture-analytics-cookie-notice' ) . '">';
                foreach ( $selected_users as $user_id ) {
                    $user = get_userdata( $user_id );
                    if ( $user ) {
                        echo '<option value="' . $user_id . '" selected>' . esc_html( $user->user_login ) . ' (' . esc_html( $user->user_email ) . ')</option>';
                    }
                }
                echo '</select>';
                break;
            // =======
            case 'page_search':
                $selected_pages = ( current_user_can( 'manage_options' ) && is_array( $saved_value ) ? $saved_value : array() );
                echo '<select name="' . $field_id . '[]" id="' . $field_id . '" class="fupi_select2 fupi_page_search ' . $el_class . '" multiple="multiple" data-placeholder_text="' . esc_html__( 'Search pages...', 'full-picture-analytics-cookie-notice' ) . '">';
                foreach ( $selected_pages as $page_id ) {
                    $page = get_post( $page_id );
                    if ( !empty( $page ) ) {
                        echo '<option value="' . $page_id . '" selected>' . esc_html( $page->post_title ) . ' (' . esc_html( $page->post_status ) . ')</option>';
                    }
                }
                echo '</select>';
                break;
            // =======
            case 'multi_checkbox':
                if ( !empty( $opts['options'] ) && is_array( $opts['options'] ) ) {
                    foreach ( $opts['options'] as $key => $label ) {
                        $checked = false;
                        if ( is_array( $saved_value ) ) {
                            if ( in_array( $key, $saved_value ) ) {
                                $checked = true;
                            }
                        } else {
                            if ( $saved_value == $key ) {
                                $checked = true;
                            }
                        }
                        $option_data_target = '';
                        if ( !empty( $opts['options_data_targets'] ) && !empty( $opts['options_data_targets'][$key] ) ) {
                            $option_data_target = $opts['options_data_targets'][$key];
                        }
                        printf(
                            '<label><input type="checkbox" name="%1$s[]" value="%2$s" id="%1$s_%2$s" %4$s class="%5$s" data-target="%6$s">%3$s</label><div class="fupi_spacer"></div>',
                            $field_id,
                            $key,
                            $label,
                            checked( $checked, true, false ),
                            $el_class,
                            $option_data_target
                        );
                    }
                    break;
                } else {
                    break;
                }
                break;
            // =======
            case 'roles_multi_checkbox':
                global $wp_roles;
                $roles = $wp_roles->roles;
                foreach ( $roles as $key => $data_arr ) {
                    if ( $key == 'administrator' ) {
                        continue;
                    }
                    $checked = false;
                    if ( is_array( $saved_value ) ) {
                        if ( in_array( $key, $saved_value ) ) {
                            $checked = true;
                        }
                    } else {
                        if ( $saved_value == $key ) {
                            $checked = true;
                        }
                    }
                    printf(
                        '<label><input type="checkbox" name="%1$s[]" value="%2$s" id="%1$s_%2$s" class="%5$s" %4$s>%3$s</label><div class="fupi_spacer"></div>',
                        $field_id,
                        $key,
                        $data_arr['name'],
                        checked( $checked, true, false ),
                        $el_class
                    );
                }
                break;
            // =======
            case 'taxonomies_multi_checkbox':
                $taxonomies = get_taxonomies();
                // $fupi_main = get_option('fupi_main');
                foreach ( $taxonomies as $tax_slug ) {
                    if ( $tax_slug == 'fupi_page_labels' || $tax_slug == 'category' || $tax_slug == 'post_tag' || $tax_slug == 'post_format' ) {
                        continue;
                    }
                    $checked = false;
                    if ( is_array( $saved_value ) ) {
                        if ( in_array( $tax_slug, $saved_value ) ) {
                            $checked = true;
                        }
                    } else {
                        if ( $saved_value == $tax_slug ) {
                            $checked = true;
                        }
                    }
                    printf(
                        '<label><input type="checkbox" name="%1$s[]" value="%2$s" id="%1$s_%2$s" %3$s class="%4$s">%2$s</label><div class="fupi_spacer"></div>',
                        $field_id,
                        $tax_slug,
                        checked( $checked, true, false ),
                        $el_class
                    );
                }
                break;
            // =======
            case 'woo_order_statuses':
                $statuses = wc_get_order_statuses();
                $output = '<select name="' . $field_id . '[]" class="fupi_select2 fupi_select2_keep_always_enabled ' . $el_class . '" multiple="multiple">';
                foreach ( $statuses as $status_slug => $status_name ) {
                    if ( $status_slug == 'wc-pending' ) {
                        continue;
                    }
                    // Get html that marks option as selected
                    $selected_html = ( !empty( $saved_value ) && in_array( $status_slug, $saved_value ) ? 'selected="selected"' : '' );
                    $output .= '<option value="' . $status_slug . '" ' . $selected_html . '>' . $status_name . '</option>';
                }
                $output .= '</select>';
                echo $output;
                break;
            // =======
            case 'textarea':
                if ( !empty( $opts['format'] ) ) {
                    if ( $opts['format'] == 'htmlentities' ) {
                        $val = esc_textarea( html_entity_decode( $saved_value, ENT_QUOTES ) );
                    } else {
                        $val = esc_textarea( $saved_value );
                    }
                } else {
                    $val = esc_textarea( $saved_value );
                }
                printf(
                    '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50" class="%4$s" data-target="%5$s" %6$s>%3$s</textarea>',
                    $field_id,
                    $placeholder,
                    $val,
                    $el_class,
                    $el_data_target,
                    $data_format
                );
                break;
            // =======
            case 'empty':
                break;
            // =======
            case 'button':
                $button_text = ( !empty( $opts['button_text'] ) ? esc_attr( $opts['button_text'] ) : '' );
                if ( !empty( $opts['custom_content'] ) ) {
                    printf( '<button type="button" %1$s>%2$s</button>', $opts['custom_content'], $button_text );
                } else {
                    $screen_reader_text = ( !empty( $opts['screen_reader_text'] ) ? '<span class="screen-reader-text">' . esc_attr( $opts['screen_reader_text'] ) . '</span>' : '' );
                    $icon = ( !empty( $opts['icon'] ) ? '<span class="' . esc_attr( $opts['icon'] ) . '"></span> ' : '' );
                    $href = ( !empty( $opts['href'] ) ? esc_attr( $opts['href'] ) : '' );
                    $target = ( !empty( $opts['target'] ) ? 'target="' . esc_attr( $opts['target'] ) . '"' : '' );
                    $offscreen_target = ( !empty( $opts['offscreen_target'] ) ? 'data-offscreen="' . esc_attr( $opts['offscreen_target'] ) . '"' : '' );
                    if ( !empty( $href ) ) {
                        printf(
                            '<a href="%1$s" %2$s class="%3$s">%4$s%5$s%6$s</a>',
                            $href,
                            $target,
                            $el_class,
                            $icon,
                            $button_text,
                            $screen_reader_text
                        );
                    } else {
                        printf(
                            '<button type="button" class="%1$s" %5$s>%2$s%3$s%4$s</button>',
                            $el_class,
                            $icon,
                            $button_text,
                            $screen_reader_text,
                            $offscreen_target
                        );
                    }
                }
                break;
            // =======
            case 'select':
                if ( !empty( $opts['options'] ) && is_array( $opts['options'] ) ) {
                    $options_markup = '';
                    foreach ( $opts['options'] as $key => $label ) {
                        $options_markup .= sprintf(
                            '<option value="%s" %s>%s</option>',
                            $key,
                            selected( $saved_value, $key, false ),
                            $label
                        );
                    }
                    printf(
                        '<select name="%1$s" id="%1$s" class="%2$s" data-target="%3$s">%4$s</select>',
                        $field_id,
                        $el_class,
                        $el_data_target,
                        $options_markup
                    );
                }
                break;
            // =======
            case 'custom_meta_select':
                $trackmeta_opts = get_option( 'fupi_track' );
                $options_markup = '<option value=\'\'>' . esc_html__( 'Please select', 'full-picture-analytics-cookie-notice' ) . '</option>';
                if ( !empty( $trackmeta_opts['custom_data_ids'] ) && count( $trackmeta_opts['custom_data_ids'] ) > 0 ) {
                    foreach ( $trackmeta_opts['custom_data_ids'] as $trackmeta ) {
                        // Name
                        $name_type = '(Post meta)';
                        if ( !empty( $trackmeta['meta'] ) ) {
                            if ( $trackmeta['meta'] == 'term' ) {
                                $name_type = '(Term meta)';
                            } else {
                                if ( $trackmeta['meta'] == 'user' ) {
                                    $name_type = '(User meta)';
                                }
                            }
                        }
                        $name = ( empty( $trackmeta['name'] ) ? esc_attr( $trackmeta['id'] ) : esc_attr( $trackmeta['name'] ) );
                        $name = $name_type . ' ' . $name;
                        // Value
                        $field_value = esc_attr( $trackmeta['id'] );
                        if ( !empty( $trackmeta['meta'] ) ) {
                            if ( $trackmeta['meta'] == 'term' ) {
                                $field_value = 'term|' . $field_value;
                            } else {
                                if ( $trackmeta['meta'] == 'user' ) {
                                    $field_value = 'user|' . $field_value;
                                }
                            }
                        }
                        // Option HTML
                        $options_markup .= sprintf(
                            '<option value="%1$s" %2$s>%3$s</option>',
                            $field_value,
                            selected( $saved_value, $field_value, false ),
                            $name
                        );
                    }
                }
                printf(
                    '<select name="%1$s" id="%1$s" class="%2$s" data-target="%3$s">%4$s</select>',
                    $field_id,
                    $el_class,
                    $el_data_target,
                    $options_markup
                );
                break;
            // =======
            case 'radio':
                if ( !empty( $opts['options'] ) && is_array( $opts['options'] ) ) {
                    $options_markup = '';
                    foreach ( $opts['options'] as $key => $label ) {
                        $checked = ( $saved_value == $key ? true : false );
                        printf(
                            '<label><input type="radio" name="%1$s" value="%2$s" id="%1$s_%2$s" %4$s class="%5$s" data-target="%6$s">%3$s</label><div class="fupi_spacer"></div>',
                            $field_id,
                            $key,
                            $label,
                            checked( $checked, true, false ),
                            $el_class,
                            $el_data_target
                        );
                    }
                    break;
                } else {
                    break;
                }
                break;
            // =======
            case 'card_radio':
                if ( !empty( $opts['options'] ) && is_array( $opts['options'] ) ) {
                    $offscreen_target = ( !empty( $opts['offscreen_target'] ) ? $opts['offscreen_target'] : '' );
                    echo '<div class="fupi_card_radio_group">';
                    foreach ( $opts['options'] as $key => $option ) {
                        if ( !is_array( $option ) ) {
                            continue;
                        }
                        $input_id = sanitize_html_class( str_replace( array('[', ']'), '_', $field_id ) . '_' . $key );
                        $is_disabled = !empty( $option['disabled'] );
                        $disabled_class = ( $is_disabled ? ' fupi_card_radio_disabled' : '' );
                        $disabled_attr = ( $is_disabled ? ' disabled' : '' );
                        $icon = ( !empty( $option['icon'] ) ? sanitize_html_class( $option['icon'] ) : 'dashicons-marker' );
                        $label = ( !empty( $option['label'] ) ? $option['label'] : '' );
                        $description = ( !empty( $option['description'] ) ? $option['description'] : '' );
                        $disabled_reason = ( !empty( $option['disabled_reason'] ) ? $option['disabled_reason'] : '' );
                        echo '<div class="fupi_card_radio_wrap' . esc_attr( $disabled_class ) . '">';
                        printf(
                            '<input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s class="screen-reader-text %5$s" data-target="%6$s"%7$s>',
                            esc_attr( $field_id ),
                            esc_attr( $key ),
                            esc_attr( $input_id ),
                            checked( $saved_value, $key, false ),
                            esc_attr( $el_class ),
                            esc_attr( $el_data_target ),
                            $disabled_attr
                        );
                        printf(
                            '<label class="fupi_card_radio" for="%1$s"><span class="dashicons %2$s fupi_card_radio_icon" aria-hidden="true"></span><strong class="fupi_card_radio_title">%3$s</strong><p class="fupi_card_radio_description">%4$s</p></label>',
                            esc_attr( $input_id ),
                            esc_attr( $icon ),
                            esc_html( $label ),
                            esc_html( $description )
                        );
                        if ( $is_disabled && $disabled_reason ) {
                            printf( '<span class="fupi_card_radio_notice"><span class="dashicons dashicons-lock" aria-hidden="true"></span>%s</span>', esc_html( $disabled_reason ) );
                        }
                        /*printf(
                        			'<button type="button" class="fupi_faux_link fupi_open_offscreen fupi_card_radio_more" data-offscreen="%1$s">%2$s</button>',
                        			esc_attr( $offscreen_target ),
                        			esc_html__( 'Learn more', 'full-picture-analytics-cookie-notice' )
                        		);*/
                        echo '</div>';
                    }
                    echo '</div>';
                }
                break;
            // =======
            case 'headline':
                echo '<div class="fupi_field_headline">';
                if ( !empty( $opts['before_title'] ) ) {
                    echo '<div class="fupi_headline_before_title">' . $opts['before_title'] . '</div>';
                }
                $title = ( empty( $opts['title'] ) ? '' : $opts['title'] );
                echo '<div class="fupi_headline_title">' . $title . '</div>';
                if ( !empty( $opts['after_title'] ) ) {
                    echo ' <span class="fupi_headline_after_title">' . $opts['after_title'] . '</span>';
                }
                echo '</div>';
                break;
            // =======
            case 'hidden':
                // Use saved value if available, otherwise use default from opts
                $field_value = ( $saved_value ? $saved_value : (( isset( $opts['default'] ) ? $opts['default'] : '' )) );
                printf(
                    '<input type="hidden" class="%3$s" name="%1$s" id="%1$s" value="%2$s"/>',
                    $field_id,
                    $field_value,
                    $el_class
                );
                break;
        }
        // REQUIRED TEXT
        if ( isset( $opts['required'] ) ) {
            $req_txt = esc_html__( 'Required', 'full-picture-analytics-cookie-notice' );
            echo '<span class="fupi_req_txt">' . $req_txt . '</span>';
        }
        // TOOLTIP
        if ( isset( $opts['tooltip'] ) ) {
            printf( '<div class="fupi_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="fupi_tooltiptext">%s</span></div>', $opts['tooltip'] );
        }
        // TEXT after_field
        if ( isset( $opts['after_field'] ) && !$already_echoed_after_field ) {
            printf( '<span class="fupi_after_field"> %s</span>', $opts['after_field'] );
        }
        // TEXT BELOW FIELD
        if ( isset( $opts['under_field'] ) ) {
            printf( '<div class="fupi_under_field">%s</div>', $opts['under_field'] );
        }
        // OFFSCREEN CONTENT
        if ( isset( $opts['offscreen'] ) ) {
            $clean_id = str_replace( array('[', ']'), '_', $field_id );
            printf( '<div id="' . $clean_id . '_offscreen" class="fupi_offscreen_source">%s</div>', $opts['offscreen'] );
        }
        // OFFSCREEN CONTENT WITH A WARNING
        if ( isset( $opts['offscreen_warning'] ) ) {
            $clean_id = str_replace( array('[', ']'), '_', $field_id );
            printf( '<div id="' . $clean_id . '_offscreen" class="fupi_offscreen_source fupi_offscreen_warning">%s</div>', $opts['offscreen_warning'] );
        }
        // IMPORTANT OFFSCREEN CONTENT
        if ( isset( $opts['offscreen_important'] ) ) {
            $clean_id = str_replace( array('[', ']'), '_', $field_id );
            printf( '<div id="' . $clean_id . '_offscreen" class="fupi_offscreen_source fupi_offscreen_important">%s</div>', $opts['offscreen_important'] );
        }
        // OFFSCREEN CONTENT ID
        if ( isset( $opts['offscreen_id'] ) ) {
            printf( '<span class="fupi_create_offscreen_link" data-offscreen-id="%1$s" aria-hidden="true"></span>', $opts['offscreen_id'] );
        }
        // WARNING OFFSCREEN CONTENT ID
        if ( isset( $opts['offscreen_warning_id'] ) ) {
            printf( '<span class="fupi_create_offscreen_link fupi_offscreen_warning" data-offscreen-id="%1$s" aria-hidden="true"></span>', $opts['offscreen_warning_id'] );
        }
        // IMPORTANT OFFSCREEN CONTENT ID
        if ( isset( $opts['offscreen_important_id'] ) ) {
            printf( '<span class="fupi_create_offscreen_link fupi_offscreen_important" data-offscreen-id="%1$s" aria-hidden="true"></span>', $opts['offscreen_important_id'] );
        }
        // RELATED OPTS BTN
        if ( isset( $opts['related_opts'] ) ) {
            if ( is_array( $opts['related_opts'] ) && count( $opts['related_opts'] ) == 2 ) {
                $dyn_module = $opts['related_opts'][0];
                $dyn_fields = $opts['related_opts'][1];
                printf( '<button type="button" class="fupi_open_dynamic_settings fupi_open_related_opts_btn fupi_not_builder_btn" data-module-id="%1$s" data-field_ids="%2$s"><span class="fupi_cog_btn__related_text">' . esc_html__( 'related', 'full-picture-analytics-cookie-notice' ) . '</span></button>', $dyn_module, $dyn_fields );
            }
        }
        // "MUST HAVE" INFO
        echo $must_have_html;
        return ob_get_clean();
    }

}
