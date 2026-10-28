<?php

$redirect404_url = false;
$fp['track'] = [
    'intersections'         => ( !empty( $this->track['intersections'] ) ? esc_attr( $this->track['intersections'] ) : '-200px 0px -200px 0px' ),
    'track_scroll_min'      => ( !empty( $this->track['track_scroll_min'] ) ? esc_attr( $this->track['track_scroll_min'] ) : 200 ),
    'track_scroll_time'     => ( !empty( $this->track['track_scroll_time'] ) ? esc_attr( $this->track['track_scroll_time'] ) : 5 ),
    'formsubm_trackdelay'   => ( !empty( $this->track['formsubm_trackdelay'] ) ? esc_attr( $this->track['formsubm_trackdelay'] ) : 3 ),
    'link_click_delay'      => isset( $this->track['link_click_delay'] ),
    'reset_timer_on_anchor' => isset( $this->track['reset_timer_on_anchor'] ),
    'track404'              => isset( $this->track['track_404'] ),
    'redirect404_url'       => $redirect404_url,
    'use_mutation_observer' => isset( $this->track['use_mutation_observer'] ),
    'virtual_pageviews'     => ( !empty( $this->track['virtual_pageviews'] ) ? $this->track['virtual_pageviews'] : [] ),
];
// DOWNLOADS
$file_formats_default = [
    'pdf',
    'doc',
    'docx',
    'txt',
    'csv',
    'key',
    'pps',
    'ppt',
    'pptx',
    'xls',
    'xlsx',
    '7z',
    'zip',
    'rar',
    'gz'
];
if ( !empty( $this->track['downl_file_formats'] ) ) {
    if ( is_string( $this->track['downl_file_formats'] ) && strlen( $this->track['downl_file_formats'] ) > 1 ) {
        $formats_a = explode( ',', $this->track['downl_file_formats'] );
        $formats_a = array_map( 'trim', $formats_a );
        $formats_a = array_map( 'strtolower', $formats_a );
        $fp['track']['downl_file_formats'] = ( count( $formats_a ) > 0 ? $formats_a : $file_formats_default );
    }
} else {
    $fp['track']['downl_file_formats'] = $file_formats_default;
}
// SCROLL DEPTHS
$scrolls_default = [50, 90];
if ( !empty( $this->track['scroll_depths'] ) ) {
    if ( is_string( $this->track['scroll_depths'] ) && strlen( $this->track['scroll_depths'] ) > 1 ) {
        $scrolls_a = explode( ',', $this->track['scroll_depths'] );
        $scrolls_a = array_map( 'trim', $scrolls_a );
        $scrolls_a = array_map( 'intval', $scrolls_a );
        $fp['track']['scroll_depths'] = ( count( $scrolls_a ) > 0 ? $scrolls_a : $scrolls_default );
    }
} else {
    $fp['track']['scroll_depths'] = $scrolls_default;
}