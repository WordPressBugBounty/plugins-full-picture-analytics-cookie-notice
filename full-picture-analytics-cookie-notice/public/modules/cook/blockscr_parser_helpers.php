<?php
/**
 * Attribute-parsing and element-blocking helpers for blockscr_parser.php.
 *
 * Extracted into a separate file so the CLI test suite (test_regex.php) can
 * load the functions without executing the WordPress-context parser logic.
 *
 * Do NOT add WordPress-specific calls (get_option, esc_*, etc.) to this file.
 * Those belong in blockscr_parser.php which runs inside the WP runtime.
 */

// ---------------------------------------------------------------------------
// Helper: parse all attributes of an HTML tag into an ordered array.
// Input : raw attribute string (everything between the tag name and ">").
// Output: array of [ 'name' => string, 'value' => string|null, 'raw' => string ]
//         'value' is the unquoted inner value; null for boolean attributes.
// ---------------------------------------------------------------------------
function fupi_parse_tag_attributes( $attr_string ) {
    $attrs = array();
    // Matches: name  |  name="value"  |  name='value'  |  name=value (unquoted)
    // Unquoted values stop at whitespace or ">" but allow "/" so URLs are captured.
    // Uses ~ as delimiter to avoid escaping / inside the pattern.
    $pattern = '~
        (?P<name>[a-zA-Z_:][a-zA-Z0-9_:\-.]*)   # attribute name
        (?:
            \s*=\s*
            (?P<value>
                "(?:[^"]*)"                       # double-quoted value
                | \'(?:[^\']*)\'                  # single-quoted value
                | (?:[^\s>"\'=]|/(?!>))+          # unquoted value (URLs allowed; / ok unless before >)
            )
        )?
    ~x';
    preg_match_all( $pattern, $attr_string, $matches, PREG_SET_ORDER );
    foreach ( $matches as $m ) {
        $name  = $m['name'];
        $raw   = $m[0];
        $value = isset( $m['value'] ) && $m['value'] !== '' ? $m['value'] : null;
        // Strip surrounding quotes from value
        if ( $value !== null ) {
            $inner = $value;
            if ( ( $inner[0] === '"' && substr( $inner, -1 ) === '"' ) ||
                 ( $inner[0] === "'" && substr( $inner, -1 ) === "'" ) ) {
                $inner = substr( $inner, 1, strlen( $inner ) - 2 );
            }
            $attrs[] = array( 'name' => $name, 'value' => $inner, 'raw' => $raw );
        } else {
            $attrs[] = array( 'name' => $name, 'value' => null, 'raw' => $raw );
        }
    }
    return $attrs;
}

// ---------------------------------------------------------------------------
// Helper: get a specific attribute value by name (case-insensitive lookup).
// Returns the unquoted value string, or null if the attribute is absent.
// For boolean attributes (e.g. "async") null is also returned.
// ---------------------------------------------------------------------------
function fupi_get_attr_value( $attrs, $attr_name ) {
    $attr_name_lc = strtolower( $attr_name );
    foreach ( $attrs as $a ) {
        if ( strtolower( $a['name'] ) === $attr_name_lc ) {
            return $a['value'];
        }
    }
    return null;
}

// ---------------------------------------------------------------------------
// Helper: rebuild an attribute string, omitting a specific attribute.
// Returns a string suitable for direct inclusion in a tag (leading space added
// when the result is non-empty).
// ---------------------------------------------------------------------------
function fupi_rebuild_attrs_without( $attrs, $drop_attr_name ) {
    $drop_lc = strtolower( $drop_attr_name );
    $parts   = array();
    foreach ( $attrs as $a ) {
        if ( strtolower( $a['name'] ) === $drop_lc ) continue;
        $parts[] = $a['raw'];
    }
    $rebuilt = implode( ' ', $parts );
    return $rebuilt !== '' ? ' ' . $rebuilt : '';
}

// Locate an opening tag's closing bracket without treating quoted values as markup.
function fupi_find_tag_open_end( $html, $offset ) {
    $quote = '';
    $html_len = strlen( $html );

    for ( $i = $offset; $i < $html_len; $i++ ) {
        $char = $html[ $i ];

        if ( $quote !== '' ) {
            if ( $char === $quote ) $quote = '';
            continue;
        }

        if ( $char === '"' || $char === "'" ) {
            $quote = $char;
        } elseif ( $char === '>' ) {
            return $i;
        }
    }

    return false;
}

// ---------------------------------------------------------------------------
// Helper: block <script src="...">, <link href="...">, or <img src="..."> tags
// whose URL attribute value contains $search_phrase (case-insensitive).
//
// Uses forward string scanning and explicit attribute parsing — no broad regex.
//
// Parameters:
//   &$html          – full HTML string (modified in place)
//   $tag_name       – 'script', 'link', or 'img'
//   $url_attr       – 'src' or 'href'
//   $search_phrase  – literal string to look for in the URL value (case-insensitive)
//   $type           – placeholder type label: 'script', 'link', or 'img'
//   $base_scr_id    – placeholder element ID prefix
//   $always_block   – true = remove element; false = make inert
//   $force_load     – '0' or '1'
//   $permissions_s  – space-separated consent tokens
//   $geo            – geo restriction string, or ''
//   &$match_count   – shared int counter
// ---------------------------------------------------------------------------
function fupi_block_elements_by_url( &$html, $tag_name, $url_attr, $search_phrase, $type, $base_scr_id, $always_block, $force_load, $permissions_s, $geo, &$match_count ) {

    $offset   = 0;
    $html_len = strlen( $html );
    $open_tag = '<' . $tag_name;
    $open_len = strlen( $open_tag );

    while ( $offset < $html_len ) {

        // Locate the next opening tag
        $tag_open_pos = stripos( $html, $open_tag, $offset );
        if ( $tag_open_pos === false ) break;

        // Guard: the character immediately after the tag name must be whitespace,
        // ">", or "/" to avoid matching e.g. "<scripts>" when looking for "<script"
        $char_after = isset( $html[ $tag_open_pos + $open_len ] ) ? $html[ $tag_open_pos + $open_len ] : '';
        if ( $char_after !== '>' && $char_after !== ' ' && $char_after !== "\t"
             && $char_after !== "\n" && $char_after !== "\r" && $char_after !== '/' ) {
            $offset = $tag_open_pos + $open_len;
            continue;
        }

        // Locate the end of the opening tag (">" or "/>")
        $tag_open_end = fupi_find_tag_open_end( $html, $tag_open_pos + $open_len );
        if ( $tag_open_end === false ) break;

        // Parse attributes
        $attr_raw = substr( $html, $tag_open_pos + $open_len, $tag_open_end - $tag_open_pos - $open_len );
        $attrs    = fupi_parse_tag_attributes( $attr_raw );
        $url_val  = fupi_get_attr_value( $attrs, $url_attr );

        // Skip if URL attribute missing or doesn't contain the search phrase
        if ( $url_val === null || stripos( $url_val, $search_phrase ) === false ) {
            $offset = $tag_open_end + 1;
            continue;
        }

        // Paired elements must be consumed completely to avoid orphan closing tags.
        if ( in_array( strtolower( $tag_name ), array( 'script', 'iframe' ), true ) ) {
            $close_tag = '</' . $tag_name . '>';
            $close_pos = stripos( $html, $close_tag, $tag_open_end + 1 );
            if ( $close_pos === false ) {
                $offset = $tag_open_end + 1;
                continue;
            }
            $full_tag_end = $close_pos + strlen( $close_tag );
        } else {
            // <link> and <img> are void elements — the opening tag is the whole element
            $full_tag_end = $tag_open_end + 1;
        }

        $full_tag_len = $full_tag_end - $tag_open_pos;

        // Build replacement
        $match_count++;
        $scr_id = ( $match_count === 1 ) ? $base_scr_id : $base_scr_id . '_' . $match_count;

        if ( $always_block ) {
            $replacement = '';
        } else {
            // Rebuild attribute string without the URL attribute
            $other_attrs = fupi_rebuild_attrs_without( $attrs, $url_attr );
            // Strip any trailing " /" left over from self-closing void elements
            $other_attrs = rtrim( $other_attrs, '/ ' );
            if ( $other_attrs !== '' ) $other_attrs = ' ' . ltrim( $other_attrs );

            // Note: esc_attr() is available in WP context; the test shim provides
            // an equivalent. We call it on $url_val only for the data attribute.
            $safe_url = function_exists('esc_attr') ? esc_attr( $url_val ) : htmlspecialchars( $url_val, ENT_QUOTES, 'UTF-8' );

            $replacement = '<!--noptimize--><script type="text/plain" data-fupi_type="' . $type . '" data-fupi_id="' . $scr_id . '" data-fupi_class="fupi_blocked_script fupi_no_defer" data-fupi_force="' . $force_load . '" data-fupi_permiss="' . $permissions_s . '" data-fupi_geo="' . $geo . '" data-fupi_url="' . $safe_url . '"' . $other_attrs . ' data-no-optimize="1" data-noptimize nowprocket></script><!--/noptimize-->';
        }

        $html     = substr_replace( $html, $replacement, $tag_open_pos, $full_tag_len );
        $html_len = strlen( $html );
        $offset   = $tag_open_pos + strlen( $replacement );
    }
}

// ---------------------------------------------------------------------------
// Helper: block inline <script> tags using grouped rules in a single pass.
// ---------------------------------------------------------------------------
function fupi_block_inline_scripts_by_rules( &$html, &$rules ) {
    if ( empty( $rules ) ) return;
    $offset = 0; $html_len = strlen( $html );
    while ( $offset < $html_len ) {
        $tag_open_pos = stripos( $html, '<script', $offset );
        if ( $tag_open_pos === false ) break;
        $tag_open_end = strpos( $html, '>', $tag_open_pos + 7 );
        if ( $tag_open_end === false ) break;
        $attr_raw = substr( $html, $tag_open_pos + 7, $tag_open_end - $tag_open_pos - 7 );
        $close_tag = '</script>'; $close_pos = stripos( $html, $close_tag, $tag_open_end + 1 );
        if ( $close_pos === false ) break;
        $close_end = $close_pos + strlen( $close_tag );
        $attrs = fupi_parse_tag_attributes( $attr_raw );
        if ( fupi_get_attr_value( $attrs, 'src' ) !== null || fupi_get_attr_value( $attrs, 'id' ) === 'fp_data_js' ) { $offset = $close_end; continue; }
        $script_content = substr( $html, $tag_open_end + 1, $close_pos - $tag_open_end - 1 );
        $matched_key = null;
        foreach ( $rules as $rule_key => $rule ) { if ( strpos( $script_content, $rule['search_phrase'] ) !== false ) { $matched_key = $rule_key; break; } }
        if ( $matched_key === null ) { $offset = $close_end; continue; }
        $rule = &$rules[ $matched_key ];
        $full_tag_len = $close_end - $tag_open_pos; $rule['match_count']++;
        $scr_id = ( $rule['match_count'] === 1 ) ? $rule['base_scr_id'] : $rule['base_scr_id'] . '_' . $rule['match_count'];
        $replacement = $rule['always_block'] ? '' : '<!--noptimize-->
                        <script type="text/plain" data-fupi_type="inline" data-fupi_id="' . $scr_id . '" data-fupi_class="fupi_blocked_script" data-fupi_force="' . $rule['force_load'] . '" data-fupi_permiss="' . $rule['permissions_s'] . '" data-fupi_geo="' . $rule['geo'] . '"  data-noptimize data-no-optimize="1" nowprocket>' . $script_content . '</script>
                        <!--/noptimize-->';
        unset( $rule );
        $html = substr_replace( $html, $replacement, $tag_open_pos, $full_tag_len );
        $html_len = strlen( $html ); $offset = $tag_open_pos + strlen( $replacement );
    }
}

// ---------------------------------------------------------------------------
// Helper: block URL-based elements using grouped rules in a single tag pass.
// ---------------------------------------------------------------------------
function fupi_block_elements_by_url_rules( &$html, $tag_name, $url_attr, &$rules ) {
    if ( empty( $rules ) ) return;
    $offset = 0; $html_len = strlen( $html ); $open_tag = '<' . $tag_name; $open_len = strlen( $open_tag );
    while ( $offset < $html_len ) {
        $tag_open_pos = stripos( $html, $open_tag, $offset );
        if ( $tag_open_pos === false ) break;
        $char_after = isset( $html[ $tag_open_pos + $open_len ] ) ? $html[ $tag_open_pos + $open_len ] : '';
        if ( $char_after !== '>' && $char_after !== ' ' && $char_after !== "\t" && $char_after !== "\n" && $char_after !== "\r" && $char_after !== '/' ) { $offset = $tag_open_pos + $open_len; continue; }
        $tag_open_end = fupi_find_tag_open_end( $html, $tag_open_pos + $open_len );
        if ( $tag_open_end === false ) break;
        $attr_raw = substr( $html, $tag_open_pos + $open_len, $tag_open_end - $tag_open_pos - $open_len );
        $attrs = fupi_parse_tag_attributes( $attr_raw ); $url_val = fupi_get_attr_value( $attrs, $url_attr );
        if ( $url_val === null ) { $offset = $tag_open_end + 1; continue; }
        $matched_key = null;
        foreach ( $rules as $rule_key => $rule ) { if ( stripos( $url_val, $rule['search_phrase'] ) !== false ) { $matched_key = $rule_key; break; } }
        if ( $matched_key === null ) { $offset = $tag_open_end + 1; continue; }
        if ( in_array( strtolower( $tag_name ), array( 'script', 'iframe' ), true ) ) { $close_tag = '</' . $tag_name . '>'; $close_pos = stripos( $html, $close_tag, $tag_open_end + 1 ); if ( $close_pos === false ) { $offset = $tag_open_end + 1; continue; } $full_tag_end = $close_pos + strlen( $close_tag ); } else { $full_tag_end = $tag_open_end + 1; }
        $rule = &$rules[ $matched_key ];
        $full_tag_len = $full_tag_end - $tag_open_pos; $rule['match_count']++;
        $scr_id = ( $rule['match_count'] === 1 ) ? $rule['base_scr_id'] : $rule['base_scr_id'] . '_' . $rule['match_count'];
        if ( $rule['always_block'] ) { $replacement = ''; } else { $other_attrs = fupi_rebuild_attrs_without( $attrs, $url_attr ); $other_attrs = rtrim( $other_attrs, '/ ' ); if ( $other_attrs !== '' ) $other_attrs = ' ' . ltrim( $other_attrs ); $safe_url = function_exists('esc_attr') ? esc_attr( $url_val ) : htmlspecialchars( $url_val, ENT_QUOTES, 'UTF-8' ); $replacement = '<!--noptimize--><script type="text/plain" data-fupi_type="' . $rule['type'] . '" data-fupi_id="' . $scr_id . '" data-fupi_class="fupi_blocked_script fupi_no_defer" data-fupi_force="' . $rule['force_load'] . '" data-fupi_permiss="' . $rule['permissions_s'] . '" data-fupi_geo="' . $rule['geo'] . '" data-fupi_url="' . $safe_url . '"' . $other_attrs . ' data-no-optimize="1" data-noptimize nowprocket></script><!--/noptimize-->'; }
        unset( $rule );
        $html = substr_replace( $html, $replacement, $tag_open_pos, $full_tag_len );
        $html_len = strlen( $html ); $offset = $tag_open_pos + strlen( $replacement );
    }
}
