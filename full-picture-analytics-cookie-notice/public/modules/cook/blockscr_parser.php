<?php

// ---------------------------------------------------------------------------
// Load attribute-parsing and element-blocking helpers.
// Separated into a standalone file so the CLI test suite can include them
// without requiring the full WordPress runtime.
// ---------------------------------------------------------------------------
if ( ! function_exists( 'fupi_parse_tag_attributes' ) ) {
    require_once dirname( __FILE__ ) . '/blockscr_parser_helpers.php';
}

// ---------------------------------------------------------------------------
// Go over all the rules and block what needs to be blocked
// ---------------------------------------------------------------------------

$content_rules = array();
$src_rules     = array();
$link_rules    = array();
$img_rules     = array();
$iframe_rules  = array();
$match_counts  = array();

foreach ( $tools_to_block as $tool_data ) {
    
    // do not make any changes if the script is in "force load" mode
    if ( ! empty( $tool_data['force_load'] ) ) continue;

    // prepare base script id
    $base_scr_id = 'fp_' . esc_attr( $tool_data['id'] );

    // check if always block scr
    $always_block = ! empty( $tool_data['always'] );

    // prepare cookie permissions
    $permissions = array();
    if ( ! empty( $tool_data['stats'] ) )  $permissions[] = 'stats';
    if ( ! empty( $tool_data['pers'] ) )   $permissions[] = 'personalisation';
    if ( ! empty( $tool_data['market'] ) ) $permissions[] = 'marketing';
    $permissions_s = implode( ' ', $permissions );

    // prepare force load
    $force_load = empty( $tool_data['force_load'] ) ? '0' : '1';

    // prepare geo
    $geo = '';
    if ( ! empty( $tool_data['method'] ) && ! empty( $tool_data['countries'] ) ) {
        $geo = esc_attr( $tool_data['method'] ) . ',' . esc_attr( str_replace( ',', ' ', $tool_data['countries'] ) );
    }

    // Track match count for script ID suffix (shared across rules for the same tool)
    if ( ! isset( $match_counts[ $base_scr_id ] ) ) {
        $match_counts[ $base_scr_id ] = 0;
    }

    foreach ( $tool_data['rules'] as $rule ) {

        if ( empty( $rule['block_by'] ) || empty( $rule['unique'] ) ) continue;

        $grouped_rule = array(
            'search_phrase' => esc_attr( $rule['unique'] ),
            'base_scr_id'   => $base_scr_id,
            'always_block'  => $always_block,
            'force_load'    => $force_load,
            'permissions_s' => $permissions_s,
            'geo'           => $geo,
            'match_count'   => &$match_counts[ $base_scr_id ],
        );

        if ( $rule['block_by'] === 'content' ) {
            $content_rules[] = $grouped_rule;
        } elseif ( $rule['block_by'] === 'src' ) {
            $grouped_rule['type'] = 'script';
            $src_rules[] = $grouped_rule;
        } elseif ( $rule['block_by'] === 'link_href' ) {
            $grouped_rule['type'] = 'link';
            $link_rules[] = $grouped_rule;
        } elseif ( $rule['block_by'] === 'img_src' ) {
            $grouped_rule['type'] = 'img';
            $img_rules[] = $grouped_rule;
        } elseif ( $rule['block_by'] === 'iframe_src' ) {
            $grouped_rule['type'] = 'iframe';
            $iframe_rules[] = $grouped_rule;
        }

        unset( $grouped_rule );
    }
}

fupi_block_elements_by_url_rules( $html, 'script', 'src', $src_rules );
fupi_block_inline_scripts_by_rules( $html, $content_rules );
fupi_block_elements_by_url_rules( $html, 'link', 'href', $link_rules );
fupi_block_elements_by_url_rules( $html, 'img', 'src', $img_rules );
fupi_block_elements_by_url_rules( $html, 'iframe', 'src', $iframe_rules );
