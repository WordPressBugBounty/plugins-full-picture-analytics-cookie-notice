<?php

$this->get_fupi_options( ['fupi_tools', 'fupi_track', 'fupi_ga41', 'fupi_ga42', 'fupi_cegg', 'fupi_fbp1', 'fupi_clar', 'fupi_gtm', 'fupi_hotj', 'fupi_insp', 'fupi_mads', 'fupi_mato', 'fupi_pla', 'fupi_tik', 'fupi_reactions', 'fupi_atrig', 'fupi_vscor', 'fupi_cook', 'fupi_proofrec', 'fupi_ifrblock'] );

$event_types = [
    ['click_triggers', 'track_clicks', ['track_elems', 'tag_elems', 'track_elems_2']],
    ['aff_click_triggers', 'track_aff', ['track_affiliate', 'tag_affiliate', 'track_affiliate_2']],
    ['form_triggers', 'track_form_submits', ['track_forms', 'tag_forms', 'track_forms_2']],
    ['view_triggers', 'track_impress', ['track_views', 'tag_views']],
];

//
// VALIDATIONS
//

// Validate all inputs before theme-mod or cron side effects.
require FUPI_PATH . '/includes/fupi_modules_data.php';

$fupi_integr_modules = [];

// Collect integration option groups that use the shared event migration.
foreach ( $fupi_modules as $module ) {
    if (
        ! empty( $module['id'] ) &&
        ( $module['type'] ?? '' ) === 'integr' &&
        ! in_array( $module['id'], [ 'track', 'cscr', 'atrig' ], true )
    ) {
        $fupi_integr_modules[] = 'fupi_' . $module['id'];
    }
}

$this->get_fupi_options( $fupi_integr_modules );

// Validate event-related fields before transforming their values.
foreach ( $fupi_integr_modules as $module_id ) {

    $this->validate_fields( $this->o[$module_id], [ 'tag_file_downl', 'track_file_downl_goalname', 'track_scroll', 'track_scroll_method', 'track_elems_method', 'track_affil_method', 'track_forms_method', 'track_views_method' ] );

    if ( $module_id !== 'fupi_mads' ) $this->validate_fields( $this->o[$module_id], [ 'track_file_downl' ] );

    // Validate each legacy event field when it is present.
    foreach ( array_merge( array_merge( ...array_column( $event_types, 2 ) ), array_column( $event_types, 1 ) ) as $field ) {
        if ( array_key_exists( $field, $this->o[$module_id] ) ) $this->validate_rows( $this->o[$module_id][$field], [], [ 'sel', 'val', 'type' ] );
    }

    if ( array_key_exists( 'custom_events', $this->o[$module_id] ) ) $this->validate_rows( $this->o[$module_id]['custom_events'], [ 'atrig_id' ] );
}

if ( array_key_exists( 'track_file_downl', $this->o['fupi_mads'] ) ) $this->validate_fields( $this->o['fupi_mads']['track_file_downl'], [ 'formats', 'val' ] );

// Validate reaction rows that can reference automatic triggers.
foreach ( [ 'js', 'classes' ] as $field ) {
    if ( array_key_exists( $field, $this->o['fupi_reactions'] ) ) $this->validate_rows( $this->o['fupi_reactions'][$field], [ 'atrig_id' ] );
}

// Validate score-bearing triggers and their nested score fields.
if ( array_key_exists( 'triggers', $this->o['fupi_atrig'] ) ) {

    $this->validate_rows( $this->o['fupi_atrig']['triggers'], [], [ 'points' ] );

    // Validate IDs and repeat settings only for scored triggers.
    foreach ( $this->o['fupi_atrig']['triggers'] as $trigger ) {
        if ( ! empty( $trigger['points'] ) ) $this->validate_rows( [ $trigger ], [ 'id', 'score_multi' ] );
    }
}

// Validate trigger references stored under each event type.
foreach ( array_column( $event_types, 0 ) as $field ) {
    if ( array_key_exists( $field, $this->o['fupi_atrig'] ) ) $this->validate_rows( $this->o['fupi_atrig'][$field], [ 'sel', 'id' ] );
}

$proofrec_fields = [ 'storage_location', 'cdb_key', 'consent_access', 'local_backup_email', 'email_frequency', 'save_all_consents' ];

$this->validate_fields( $this->o['fupi_proofrec'], $proofrec_fields );
$this->validate_fields( $this->o['fupi_cook'], array_merge( $proofrec_fields, [ 'pp_id', 'iframe_img', 'iframe_caption_txt', 'iframe_btn_text' ] ) );

//
// PERFORM UPDATES
//

// Move iframe placeholder settings to their Customizer-controlled options.
$iframe_text_fields = [
    'iframe_caption_txt' => 'caption_txt',
    'iframe_btn_text'     => 'btn_text',
];

// Copy each configured legacy text setting to its new option.
foreach ( $iframe_text_fields as $old_field_id => $new_field_id ) {
    if ( ! empty( $this->o['fupi_cook'][$old_field_id] ) ) {
        $this->o['fupi_ifrblock'][$new_field_id] = $this->o['fupi_cook'][$old_field_id];
    }
}

// Persist the legacy iframe image as a Customizer attachment ID.
if ( ! empty( $this->o['fupi_cook']['iframe_img'] ) ) {

    $iframe_image_id = attachment_url_to_postid( $this->o['fupi_cook']['iframe_img'] );

    // Confirm that the image setting was written successfully.
    if ( $iframe_image_id ) {
        set_theme_mod( 'fupi_ifrblock_image', $iframe_image_id );

        if ( get_theme_mod( 'fupi_ifrblock_image' ) !== $iframe_image_id ) {
            throw new RuntimeException( 'Iframe image theme mod update failed.' );
        }
    }
}

// Move Records of Consent settings into Consent Management.
$proofrec_was_enabled = ! empty( $this->o['fupi_tools']['proofrec'] );

// An absent source can mean a previous attempt saved cook and deleted proofrec.
if ( ! empty( $this->o['fupi_proofrec'] ) ) {

    // Copy all legacy proof-record settings into Consent Management.
    foreach ( $proofrec_fields as $field_id ) {
        if ( array_key_exists( $field_id, $this->o['fupi_proofrec'] ) ) {
            $this->o['fupi_cook'][$field_id] = $this->o['fupi_proofrec'][$field_id];
        }
    }

    // Do not enable recording if proofrec module was disabled.
    if ( ! $proofrec_was_enabled ) unset( $this->o['fupi_cook']['storage_location'] );
}

unset( $this->o['fupi_tools']['proofrec'] );

// Do not enable recording if privacy policy is not published.
if ( ! empty( $this->o['fupi_cook']['storage_location'] ) ) {

    $pp_id = ! empty( $this->o['fupi_cook']['pp_id'] ) ? (int) $this->o['fupi_cook']['pp_id'] : 0;

    if (
        ! $pp_id ||
        get_post_status( $pp_id ) != 'publish'
    ) unset( $this->o['fupi_cook']['storage_location'] );
}

// Make sure that CRON is properly scheduled.
if (
    ! empty( $this->o['fupi_cook']['storage_location'] ) &&
    $this->o['fupi_cook']['storage_location'] == 'email'
) {

    $frequency = ! empty( $this->o['fupi_cook']['email_frequency'] ) ? $this->o['fupi_cook']['email_frequency'] : 'daily';

    if ( ! in_array( $frequency, [ 'hourly', 'twicedaily', 'daily' ], true ) ) $frequency = 'daily';

    $this->queue_deferred_work( static function () use ( $frequency ) {
        if ( wp_next_scheduled( 'fupi_consents_backup_cron_event' ) ) return;

        $scheduled = wp_schedule_event( time() + 60, $frequency, 'fupi_consents_backup_cron_event' );

        if ( ! $scheduled || is_wp_error( $scheduled ) ) {
            throw new RuntimeException( 'Consent backup scheduling failed.' );
        }
    } );
}

$this->opts_to_remove[] = 'fupi_proofrec';

// Move automatic-trigger point events to the visitor-scoring module.
if ( ! empty( $this->o['fupi_atrig']['triggers'] ) ) {

    $atrig_triggers_a = $this->o['fupi_atrig']['triggers'];
    $vscor_events_a = [];

    // Convert each scored trigger to its visitor-scoring event format.
    foreach ( $atrig_triggers_a as $trigger_data ) {
        if ( empty( $trigger_data['points'] ) ) continue;

        $vscor_events_a[] = [
            'atrig_id' => $trigger_data['id'],
            'points'   => $trigger_data['points'],
            'repeat'   => $trigger_data['score_multi'],
        ];
    }

    // Enable and populate visitor scoring only when scored events exist.
    if ( ! empty( $vscor_events_a ) ) {

        $this->o['fupi_tools']['vscor'] = true;
        $this->o['fupi_vscor']['custom_events'] = $vscor_events_a;

        if ( ! empty( $this->o['fupi_atrig']['lead_scoring_levels'] ) ) {
            $this->o['fupi_vscor']['lead_scoring_levels'] = $this->o['fupi_atrig']['lead_scoring_levels'];
        }
    }
}

// Divide reactions into event-triggered and visitor-score-triggered settings.
if ( ! empty( $this->o['fupi_reactions']['js'] ) ) {

    $js_a = [];
    $js_leads_a = [];

    // Separate JavaScript reactions by their trigger source.
    foreach ( $this->o['fupi_reactions']['js'] as $i => $event_data ) {
        if ( strpos( $event_data['atrig_id'], 'fp_leadscore_' ) === 0 ) {
            $js_leads_a[] = $event_data;
        } else {
            $js_a[] = $event_data;
        }
    }

    if ( count( $js_leads_a ) > 0 ) $this->o['fupi_reactions']['vscore_js'] = $js_leads_a;
    if ( count( $js_a ) > 0 ) $this->o['fupi_reactions']['triggers_js'] = $js_a;
}

// Divide CSS-class reactions into event-triggered and visitor-score-triggered settings.
if ( ! empty( $this->o['fupi_reactions']['classes'] ) ) {

    $classes_a = [];
    $classes_leads_a = [];

    // Separate CSS-class reactions by their trigger source.
    foreach ( $this->o['fupi_reactions']['classes'] as $i => $event_data ) {
        if ( strpos( $event_data['atrig_id'], 'fp_leadscore_' ) === 0 ) {
            $classes_leads_a[] = $event_data;
        } else {
            $classes_a[] = $event_data;
        }
    }

    if ( count( $classes_leads_a ) > 0 ) $this->o['fupi_reactions']['vscore_html'] = $classes_leads_a;
    if ( count( $classes_a ) > 0 ) $this->o['fupi_reactions']['triggers_html'] = $classes_a;
}

// Consolidate legacy download formats and retain enabled integrations.
$formats_s = '';

if ( ! empty( $this->o['fupi_cegg']['tag_file_downl'] ) ) {
    $this->o['fupi_cegg']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_cegg']['tag_file_downl'];
}

if ( ! empty( $this->o['fupi_clar']['tag_file_downl'] ) ) {
    $this->o['fupi_clar']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_clar']['tag_file_downl'];
}

if ( ! empty( $this->o['fupi_hotj']['tag_file_downl'] ) ) {
    $this->o['fupi_hotj']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_hotj']['tag_file_downl'];
}

if ( ! empty( $this->o['fupi_insp']['tag_file_downl'] ) ) {
    $this->o['fupi_insp']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_insp']['tag_file_downl'];
}

if ( ! empty( $this->o['fupi_fbp1']['track_file_downl'] ) ) {
    $this->o['fupi_fbp1']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_fbp1']['track_file_downl'];
}

if ( ! empty( $this->o['fupi_gtm']['track_file_downl'] ) ) {
    $this->o['fupi_gtm']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_gtm']['track_file_downl'];
}

if ( ! empty( $this->o['fupi_tik']['track_file_downl'] ) ) {
    $this->o['fupi_tik']['track_downl'] = true;
    $formats_s .= ',' . $this->o['fupi_tik']['track_file_downl'];
}

if (
    ! empty( $this->o['fupi_mads']['track_file_downl']['formats'] ) &&
    ! empty( $this->o['fupi_mads']['track_file_downl']['val'] )
) {
    $this->o['fupi_mads']['track_downl'] = $this->o['fupi_mads']['track_file_downl']['val'];
    $formats_s .= ',' . $this->o['fupi_mads']['track_file_downl']['formats'];
}

if (
    ! empty( $this->o['fupi_pla']['track_file_downl_goalname'] ) &&
    ! empty( $this->o['fupi_pla']['track_file_downl'] )
) {
    $this->o['fupi_pla']['track_downl'] = $this->o['fupi_pla']['track_file_downl_goalname'];
    $formats_s .= ',' . $this->o['fupi_pla']['track_file_downl'];
}

// Remove spaces and duplicates from the combined download formats.
$formats_s = str_replace( ' ', '', $formats_s );
$formats_arr = array_unique( array_filter( explode( ',', $formats_s ) ) );
$formats_str_clean = implode( ',', $formats_arr );

if ( ! empty( $formats_str_clean ) ) {
    $this->o['fupi_track']['downl_file_formats'] = $formats_str_clean;
}

// Consolidate legacy scroll depths and retain enabled integrations.
$scrolls_s = '';

if (
    ! empty( $this->o['fupi_ga41']['track_scroll'] ) &&
    ! empty( $this->o['fupi_ga41']['track_scroll_method'] )
) {
    $scrolls_s .= ',' . $this->o['fupi_ga41']['track_scroll'];
}

if ( ! empty( $this->o['fupi_ga42']['track_scroll'] ) ) {
    $this->o['fupi_ga42']['track_scroll_depth'] = true;
    $scrolls_s .= ',' . $this->o['fupi_ga42']['track_scroll'];
}

if ( ! empty( $this->o['fupi_fbp1']['track_scroll'] ) ) {
    $this->o['fupi_fbp1']['track_scroll_depth'] = true;
    $scrolls_s .= ',' . $this->o['fupi_fbp1']['track_scroll'];
}

if ( ! empty( $this->o['fupi_gtm']['track_scroll'] ) ) {
    $this->o['fupi_gtm']['track_scroll_depth'] = true;
    $scrolls_s .= ',' . $this->o['fupi_gtm']['track_scroll'];
}

if ( ! empty( $this->o['fupi_mato']['track_scroll'] ) ) {
    $this->o['fupi_mato']['track_scroll_depth'] = true;
    $scrolls_s .= ',' . $this->o['fupi_mato']['track_scroll'];
}

// Remove spaces and duplicates from the combined scroll depths.
$scrolls_s = str_replace( ' ', '', $scrolls_s );
$scrolls_arr = array_unique( array_filter( explode( ',', $scrolls_s ) ) );
$scrolls_str_clean = implode( ',', $scrolls_arr );

if ( ! empty( $scrolls_str_clean ) ) {
    $this->o['fupi_track']['scroll_depths'] = $scrolls_str_clean;
}

// Create automatic-trigger events from all integration event settings.
$used_selectors = [];
$atrig_triggers = [];

// Migrate every supported event type for each integration.
foreach ( $fupi_integr_modules as $module_id ) {

    // Convert each legacy event type to trigger references.
    foreach ( $event_types as $event_type_opts ) {

        $atrig_field_id = $event_type_opts[0];
        $new_event_field_id = $event_type_opts[1];
        $new_event_field_data = [];
        $old_module_field_ids_a = $event_type_opts[2];

        // Load selectors and triggers once for each automatic-trigger field.
        if (
            ! isset( $used_selectors[$atrig_field_id] ) ||
            ! is_array( $used_selectors[$atrig_field_id] )
        ) {

            $used_selectors[$atrig_field_id] = [];

            // Index already persisted triggers by selector.
            foreach ( $this->o['fupi_atrig'][$atrig_field_id] ?? [] as $trigger ) {
                $used_selectors[$atrig_field_id][trim( $trigger['sel'] )] = $trigger['id'];
            }
        }

        // Restore trigger definitions after a partially completed migration.
        if (
            ! isset( $atrig_triggers[$atrig_field_id] ) ||
            ! is_array( $atrig_triggers[$atrig_field_id] )
        ) {
            $atrig_triggers[$atrig_field_id] = $this->o['fupi_atrig'][$atrig_field_id] ?? [];
        }

        $event_sections_a = [];

        // Use the first populated legacy field for this event type.
        foreach ( $old_module_field_ids_a as $old_module_field_id ) {
            if ( ! empty( $this->o[$module_id][$old_module_field_id] ) ) {
                $event_sections_a = $this->o[$module_id][$old_module_field_id];
                break;
            }
        }

        if ( empty( $event_sections_a ) ) continue;

        // Create module event data and shared trigger definitions.
        foreach ( $event_sections_a as $i => $event_section ) {

            $atrig_id = false;

            if ( empty( $event_section['sel'] ) ) continue;

            $sel = trim( $event_section['sel'] );

            // Reuse an existing trigger when the selector was already migrated.
            if ( ! empty( $used_selectors[$atrig_field_id][$sel] ) ) {
                $atrig_id = $used_selectors[$atrig_field_id][$sel];
            } else {
                $atrig_id = $new_event_field_id . '_' . $module_id . '_' . $i;

                $atrig_triggers[$atrig_field_id][] = [
                    'name' => $sel,
                    'sel'  => $sel,
                    'id'   => $atrig_id,
                ];

                $used_selectors[$atrig_field_id][$sel] = $atrig_id;
            }

            $new_event = true;

            // Avoid multiple module events that reference the same trigger.
            foreach ( $new_event_field_data as $new_evt_data ) {
                if ( $new_evt_data['atrig_id'] == $atrig_id ) $new_event = false;
            }

            // Preserve the event value or create the appropriate fallback.
            if ( $new_event ) {

                $event_section_data = [
                    'atrig_id' => $atrig_id,
                ];

                if ( empty( $event_section['val'] ) ) {
                    if (
                        'click_triggers' === $atrig_field_id ||
                        'view_triggers' === $atrig_field_id
                    ) {
                        $event_section_data['val'] = esc_attr( $sel );
                    } else {
                        $event_section_data['val'] = '_fallback';
                    }
                } else {
                    $event_section_data['val'] = $event_section['val'];
                }

                $new_event_field_data[] = $event_section_data;
            }
        }

        if ( ! empty( $new_event_field_data ) ) {
            $this->o[$module_id][$new_event_field_id] = $new_event_field_data;
        } else {
            unset( $this->o[$module_id][$new_event_field_id] );
        }
    }

    // Move lead-scoring custom events into the visitor-scoring field.
    if ( ! empty( $this->o[$module_id]['custom_events'] ) ) {

        $custom_events_a = $this->o[$module_id]['custom_events'];
        $leadscore_events = [];

        // Retain only events tied to a visitor-score trigger.
        foreach ( $custom_events_a as $custom_event_data ) {

            $atrig_id = $custom_event_data['atrig_id'];

            if (
                empty( $atrig_id ) ||
                strpos( $atrig_id, 'fp_leadscore_' ) !== 0
            ) continue;

            if (
                'fupi_fbp1' === $module_id &&
                ! empty( $custom_event_data['capi'] )
            ) {
                $this->o[$module_id]['track_vscor_capi'] = true;
            }

            $leadscore_events[] = $custom_event_data;
        }

        if ( ! empty( $leadscore_events ) ) $this->o[$module_id]['vscores'] = $leadscore_events;
    }
}

// Persist all automatic-trigger definitions after every module is processed.
if ( ! empty( $atrig_triggers ) ) {
    $this->o['fupi_atrig'] = array_replace( $this->o['fupi_atrig'], $atrig_triggers );
}

// Simplify GA4 and TikTok event fields by moving event types into their data.
if ( ! empty( $this->o['fupi_tik'] ) ) {

    // Add an explicit type to each legacy TikTok click and form event.
    foreach ( ['track_form_submits', 'track_clicks'] as $field_id ) {

        if ( empty( $this->o['fupi_tik'][$field_id] ) ) continue;

        $sections_a = $this->o['fupi_tik'][$field_id];
        $new_sections_a = [];

        // Add an event type only when the migration has not run already.
        foreach ( $sections_a as $section_data ) {
            if ( empty( $section_data['type'] ) ) {
                if ( substr( $section_data['val'] ?? '', 0, 1 ) == '!' ) {
                    $section_data['val'] = substr( $section_data['val'], 1 );
                    $section_data['type'] = 'custom';
                } else {
                    $section_data['type'] = 'event';
                }
            }

            $new_sections_a[] = $section_data;
        }

        $this->o['fupi_tik'][$field_id] = $new_sections_a;
    }
}

// Add the selected GA4 event method to each migrated event row.
foreach ( ['fupi_ga41', 'fupi_ga42'] as $module_id ) {

    if ( empty( $this->o[$module_id] ) ) continue;

    $fields_ids_a = [
        ['track_clicks', 'track_elems_method'],
        ['track_aff', 'track_affil_method'],
        ['track_form_submits', 'track_forms_method'],
        ['track_impress', 'track_views_method'],
    ];

    // Map each event field to the legacy method setting that controls it.
    foreach ( $fields_ids_a as $two_ids_a ) {

        $event_field_id = $two_ids_a[0];
        $method_field_id = $two_ids_a[1];

        if ( empty( $this->o[$module_id][$event_field_id] ) ) continue;

        $sections_a = $this->o[$module_id][$event_field_id];
        $new_sections_a = [];
        $method = 'event';
        $method_val = $this->o[$module_id][$method_field_id] ?? '';

        if (
            ! empty( $method_val ) &&
            strpos( $method_val, 'param' ) !== false
        ) $method = 'param';

        // Give untyped rows the method selected in the legacy field.
        foreach ( $sections_a as $section_data ) {
            if ( empty( $section_data['type'] ) ) $section_data['type'] = $method;

            $new_sections_a[] = $section_data;
        }

        $this->o[$module_id][$event_field_id] = $new_sections_a;
    }
}
