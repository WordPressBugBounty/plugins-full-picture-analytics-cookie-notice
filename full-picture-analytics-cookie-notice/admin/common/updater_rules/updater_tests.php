<?php

//
// MAKE ATRIG EVENTS
// - Gets all the events in tracking modules
// - turns them into fupi_atrig events
// - saves events data in tracking modules in new fields and in a different format (with atrig_id that references new atrig events)

$this->get_fupi_options( ['fupi_atrig'] ); // we get this separately to other modules

include FUPI_PATH . '/includes/fupi_modules_data.php';

// limit $fupi_modules to only tracking modules of 'type' => 'integr' that don't have 'id' equal to "track", "cscr" and "atrig"
$fupi_integr_modules = array_filter( $fupi_modules, function ( $module ) {
    return ! empty( $module['id'] )
        && ! empty( $module['type'] )
        && 'integr' === $module['type']
        && ! in_array( $module['id'], [ 'track', 'cscr', 'atrig' ], true );
} );

// re-index to remove empties
$fupi_integr_modules = array_values( $fupi_integr_modules );

// make a new array $fupi_integr_modules that only contains IDs of $fupi_modules, but each of them has a prefix "fupi_", e.g. ['fupi_atrig', 'fupi_fbp1']
$fupi_integr_modules = array_map( function ( $module ) {
    return 'fupi_' . $module['id'];
}, $fupi_integr_modules );

// get all options
$this->get_fupi_options( $fupi_integr_modules );

// make new arrays

$used_selectors = [];
$atrig_triggers = [];

$event_types = [
    ['click_triggers', 'track_clicks', ['track_elems', 'tag_elems', 'track_elems_2']],
    ['aff_click_triggers', 'track_aff', ['track_affiliate', 'tag_affiliate', 'track_affiliate_2']],
    ['form_triggers', 'track_form_submits', ['track_forms', 'tag_forms', 'track_forms_2']],
    ['view_triggers', 'track_impress', ['track_views', 'tag_views']],
];

// for each module...
foreach ( $fupi_integr_modules as $module_id ) {

    // Modify simple events fields

    // for each click, aff click, form and view event type
    foreach ( $event_types as $event_type_opts ) {

        $atrig_field_id       = $event_type_opts[0]; // field_id for every trigger type in fupi_atrig, e.g. "click_triggers"
        $new_event_field_id     = $event_type_opts[1]; // this id the of the new field that will be created in a module
        $new_event_field_data   = [];
        $old_module_field_ids_a     = $event_type_opts[2]; // Array of IDs of events fields in modules, e.g. 'track_elems', 'tag_elems', 'track_elems_2'
        
        if ( ! isset( $used_selectors[$atrig_field_id] ) || ! is_array( $used_selectors[$atrig_field_id] ) ) {
            $used_selectors[$atrig_field_id] = []; // keeps track of used selectors and trigger ids across modules and a single module
        }

        if ( ! isset( $atrig_triggers[$atrig_field_id] ) || ! is_array( $atrig_triggers[$atrig_field_id] ) ) {
            $atrig_triggers[$atrig_field_id] = []; // holds settings of triggers that will be saved after we are done with all modules
        }

        // STOP if this module has no field that we are looking for
    
        $event_sections_a = [];

        foreach ( $old_module_field_ids_a as $old_module_field_id ) {
            if ( ! empty( $this->o[$module_id][$old_module_field_id] ) ) {
                $event_sections_a = $this->o[$module_id][$old_module_field_id];
                break;
            }
        }
        
        if ( empty( $event_sections_a ) ) continue;

        // for each event section
        foreach( $event_sections_a as $i => $event_section ) {

            $atrig_id = false;
            
            if ( empty( $event_section['sel'] ) ) continue;
            
            $sel = trim( $event_section['sel'] );

            // if the selector in this event has already been used by a different module
            if ( ! empty ( $used_selectors[$atrig_field_id][$sel] ) ) {

                // remember the id associated with this selector as atrig_id
                $atrig_id = $used_selectors[$atrig_field_id][$sel];
            
            // if the selector in this event has NOT been used by a different module
            } else {

                // create and remember atrig_id
                $atrig_id = $new_event_field_id . '_' . $module_id . '_' . $i;

                // create a set of trigger data that will be later saved in fupi_atrig
                $atrig_triggers[$atrig_field_id][] = [
                    'name' => $sel,
                    'sel' => $sel,
                    'id' => $atrig_id
                ];

                // remember this selector and ID as already used / defined (for other modules)
                $used_selectors[$atrig_field_id][$sel] = $atrig_id;
            }
            
            // Create a new set of data for use in this module
            
            // Prevent creating multiple events with the same trigger
            $new_event = true;
            foreach ( $new_event_field_data as $new_evt_data ) {
                if ( $new_evt_data['atrig_id'] == $atrig_id ) $new_event = false;
            }

            if ( $new_event ) {
                
                $event_section_data = [
                    'atrig_id' => $atrig_id,
                ];

                // save value only if it exists (not always required)
                if ( empty( $event_section['val'] ) ) {
                    if ( 'click_triggers' === $atrig_field_id ) {
                        $event_section_data['val'] = $sel;
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
            unset( $this->o[$module_id][$new_event_field_id] ); // ?? is it even used ??
        }
    }

    // Move leadscoring events from "triggers" to "vscores" field

    // for custom triggers
    
    if ( ! empty( $this->o[$module_id]['custom_events'] ) ) {
        
        $custom_events_a = $this->o[$module_id]['custom_events'];
        $leadscore_events = [];

        foreach ( $custom_events_a as $custom_event_data ) {

            $atrig_id = $custom_event_data['atrig_id'];
            
            // STOP if atrig_id doesn't start with fp_leadscore_
            if ( empty ( $atrig_id ) || strpos( $atrig_id, 'fp_leadscore_' ) !== 0 ) continue;
            if ( empty( $custom_event_data['points'] ) ) continue;
            $leadscore_events[] = $custom_event_data;
        }

        if ( ! empty( $leadscore_events ) ) $this->o[$module_id]['vscores'] = $leadscore_events;
    };
};

// Update fupi_atrig data after all modules are processed
if ( ! empty( $atrig_triggers ) ) {
    $custom_triggers = ! empty ( $this->o['fupi_atrig']['triggers'] ) ? $this->o['fupi_atrig']['triggers'] : [];
    $this->o['fupi_atrig'] = $atrig_triggers;
    if ( ! empty( $custom_triggers ) ) $this->o['fupi_atrig']['triggers'] = $custom_triggers;
};

//
// WORKS UNTIL THIS POINT
//

//
// SIMPLIFY GA4 and Tik event fields in tracking modules
// Puts the event type option into the event data instead of keeping it outside
//

if ( ! empty ( $this->o['fupi_tik'] ) ) {

    foreach ( ['track_form_submits', 'track_clicks'] as $field_id ) {

        if ( empty ( $this->o['fupi_tik'][$field_id] ) ) continue;

        $sections_a = $this->o['fupi_tik'][$field_id];
        $new_sections_a = [];

        foreach ( $sections_a as $section_data ) {
            
            // if the first char is "!"
            if ( substr( $section_data['val'], 0, 1 ) == '!' ){
                // remvoe it and add type "custom"
                $section_data['val'] = substr( $section_data['val'], 1 );
                $section_data['type'] = 'custom';
            } else {
                // add type "event"
                $section_data['type'] = 'event';
            }

            // save in new array
            $new_sections_a[] = $section_data;
        }

        // assign new array to old key
        $this->o['fupi_tik'][$field_id] = $new_sections_a;
    }
}

foreach ( ['fupi_ga41', 'fupi_ga42'] as $module_id ) {

    if ( empty ( $this->o[$module_id] ) ) continue;

    $fields_ids_a = [
        ['track_clicks', 'track_elems_method'],
        ['track_aff', 'track_affil_method'],
        ['track_form_submits', 'track_forms_method'],
        ['track_impress', 'track_views_method']
    ];

    foreach ( $fields_ids_a as $two_ids_a ) {

        $event_field_id     = $two_ids_a[0];
        $method_field_id    = $two_ids_a[1];

        if ( empty ( $this->o[$module_id][$event_field_id] ) ) continue;
        
        $sections_a         = $this->o[$module_id][$event_field_id];
        $new_sections_a     = [];
        $method             = 'event';
        $method_val         = $this->o[$module_id][$method_field_id];

        if ( ! empty ( $method_val ) && strpos($method_val, 'param') !== false ) $method = 'param';

        foreach ( $sections_a as $section_data ) {
            $section_data['type'] = $method;
            $new_sections_a[] = $section_data;
        }

        $this->o[$module_id][$event_field_id] = $new_sections_a;
    } 
}
