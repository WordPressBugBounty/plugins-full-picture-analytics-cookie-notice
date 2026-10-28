<?php

/**
 * Build the complete list of script-blocking rule definitions.
 *
 * Modules can append their blocking rules with the `fupi_script_blocking_rules` filter so we must sanitize everything.
 *
 * @return array
 */
function fupi_get_script_blocking_rules( $cook_settings, $skip_force_load = false ) {

    $tools_to_block = array();

    if ( ! is_array( $cook_settings ) ) $cook_settings = array();

    if ( ! empty( $cook_settings['scrblk_auto_rules'] ) && is_array( $cook_settings['scrblk_auto_rules'] ) ) {
        foreach ( $cook_settings['scrblk_auto_rules'] as $source ) {
            switch ( $source ) {

                // JETPACK STATS
                case 'jetpack':
                    $tools_to_block[] = array(
                        'id'     => 'auto_jetpack_stats',
                        'rules'  => array(
                            array(
                                'block_by' => 'src',
                                'unique'   => 'stats.wp.com',
                            ),
                        ),
                        'stats'  => true,
                        'market' => false,
                        'pers'   => false,
                    );
                break;

                // PIXELYOURSITE
                case 'pys':
                    $tools_to_block[] = array(
                        'id'     => 'auto_pys',
                        'rules'  => array(
                            array(
                                'block_by' => 'src',
                                'unique'   => 'pixelyoursite',
                            ),
                            array(
                                'block_by' => 'content',
                                'unique'   => 'pysOptions',
                            ),
                        ),
                        'stats'  => true,
                        'market' => true,
                        'pers'   => true,
                    );
                break;

                // STAPE.io
                case 'stape':
                    if ( defined( 'GTM_SERVER_SIDE_DATA_LAYER_CUSTOM_EVENT_NAME' ) && function_exists( 'get_option' ) ) {
                        $stape_url = get_option( 'gtm_server_side_web_container_url', true );
                        if ( ! empty( $stape_url ) ) {
                            $tools_to_block[] = array(
                                'id'       => 'auto_stape_plugin',
                                'rules'    => array(
                                    array(
                                        'block_by' => 'content',
                                        'unique'   => function_exists( 'esc_url' ) ? esc_url( $stape_url ) : $stape_url,
                                    ),
                                ),
                                'url_part' => '',
                                'stats'    => false,
                                'market'   => false,
                                'pers'     => false,
                            );
                        }
                    }
                break;
            }
        }
    }

    // ADD RULES FROM OTHER MODULES
    
    $registered_rules = apply_filters( 'fupi_script_blocking_rules', $tools_to_block );
    
    if ( is_array( $registered_rules ) ) $tools_to_block = $registered_rules;

    if ( ! empty( $cook_settings['control_other_tools'] ) && ! empty( $cook_settings['scrblk_manual_rules'] ) && is_array( $cook_settings['scrblk_manual_rules'] ) ) {
        $tools_to_block = array_merge( $tools_to_block, $cook_settings['scrblk_manual_rules'] );
    }
    
    // SANITIZE & NORMALIZE RULES
    // !! We do NOT trust other modules that they add safe rules

    $normalized_rules = array();
    $supported_methods = array( 'src', 'content', 'link_href', 'img_src' );
    if ( ! empty( $cook_settings['dynamic_blocking'] ) ) $supported_methods[] = 'iframe_src';

    foreach ( $tools_to_block as $tool_data ) {
        
        if ( 
            ! is_array( $tool_data ) || 
            empty( $tool_data['id'] ) || 
            empty( $tool_data['rules'] ) || 
            ! is_array( $tool_data['rules'] ) 
        ) continue;

        $force_load = ! empty( $tool_data['force_load'] );
        if ( $skip_force_load && $force_load ) continue;

        $tool_id = sanitize_key( $tool_data['id'] );
        if ( empty ( $tool_id ) ) continue;
        
        // RULES ARRAY

        $tool_rules = array();

        foreach ( $tool_data['rules'] as $rule ) {
            
            if ( 
                ! is_array( $rule ) || 
                empty( $rule['block_by'] ) || 
                ! isset( $rule['unique'] ) || 
                ! is_scalar( $rule['unique'] ) 
            ) continue;

            $block_by = sanitize_key( $rule['block_by'] );
            $unique = trim( $rule['unique'] );

            if ( ! in_array( $block_by, $supported_methods, true ) || $unique === '' ) continue;

            $tool_rules[] = array(
                'block_by' => $block_by,
                'unique'   => $unique,
            );
        }

        if ( empty( $tool_rules ) ) continue;
        
        // GEO

        $method = ! empty( $tool_data['method'] ) ? sanitize_key( $tool_data['method'] ) : '';
        $countries = ! empty( $tool_data['countries'] ) && is_scalar( $tool_data['countries'] ) ? trim( (string) $tool_data['countries'] ) : '';
        
        // ALL TOGETHER
        
        $normalized_rules[] = array(
            'id'         => $tool_id,
            'rules'      => $tool_rules,
            'stats'      => ! empty( $tool_data['stats'] ),
            'market'     => ! empty( $tool_data['market'] ),
            'pers'       => ! empty( $tool_data['pers'] ),
            'always'     => ! empty( $tool_data['always'] ),
            'force_load' => $force_load,
            'method'     => $method,
            'countries'  => $countries,
        );
    }

    return $normalized_rules;
}
