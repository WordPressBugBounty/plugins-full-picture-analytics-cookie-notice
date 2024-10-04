<?php

// these vars will be used in other files too
// check if current user role should be tracked
$this->track_current_user = true;
$disable_for_roles = ( !empty( $this->main ) && isset( $this->main['disable_for_roles'] ) ? $this->main['disable_for_roles'] : ['administrator'] );
$user = wp_get_current_user();
if ( !empty( $user ) ) {
    foreach ( $user->roles as $role ) {
        if ( in_array( $role, $disable_for_roles ) ) {
            $this->track_current_user = false;
        }
    }
}
$fp['vars'] = [
    'url'                   => FUPI_URL,
    'uploads_url'           => trailingslashit( wp_upload_dir()['baseurl'] ),
    'is_customizer'         => $this->is_customizer,
    'debug'                 => $this->is_debug,
    'intersections'         => ( !empty( $this->main['intersections'] ) ? esc_attr( $this->main['intersections'] ) : '-200px 0px -200px 0px' ),
    'track_current_user'    => $this->track_current_user,
    'dblclck_time'          => ( !empty( $this->main['notrack_dblclck'] ) ? esc_attr( $this->main['notrack_dblclck'] ) : 300 ),
    'track_scroll_min'      => ( !empty( $this->main['track_scroll_min'] ) ? esc_attr( $this->main['track_scroll_min'] ) : 200 ),
    'track_scroll_time'     => ( !empty( $this->main['track_scroll_time'] ) ? esc_attr( $this->main['track_scroll_time'] ) : 5 ),
    'formsubm_trackdelay'   => ( !empty( $this->main['formsubm_trackdelay'] ) ? esc_attr( $this->main['formsubm_trackdelay'] ) : 3 ),
    'link_click_delay'      => isset( $this->main['link_click_delay'] ),
    'reset_timer_on_anchor' => isset( $this->main['reset_timer_on_anchor'] ),
    'track404'              => isset( $this->tools['track404'] ),
    'redirect404_url'       => ( isset( $redirect404_url ) ? $redirect404_url : false ),
    'magic_keyword'         => ( !empty( $this->main ) && !empty( $this->main['magic_keyword'] ) ? esc_attr( $this->main['magic_keyword'] ) : 'tracking' ),
    'use_mutation_observer' => isset( $this->main['use_mutation_observer'] ),
    'server_method'         => ( !empty( $this->main['server_method'] ) ? esc_attr( $this->main['server_method'] ) : 'rest' ),
];
// CONSENT BANNER
if ( $this->cookie_notice_enabled ) {
    // GET DATA FROM THE CUSTOMIZER
    $notice_data = get_option( 'fupi_cookie_notice' );
    $priv_policy_url = get_privacy_policy_url();
    // returns empty string if page is not published
    $priv_policy_id = get_option( 'wp_page_for_privacy_policy' );
    // gives ID event when the page is not published
    $privacy_policy_update_date = ( !empty( $priv_policy_url ) ? get_post_modified_time(
        'U',
        false,
        $priv_policy_id,
        false
    ) : null );
    // BUILD BASIC OBJECT
    $fp['notice'] = [
        'enabled'               => true,
        'gtag_no_cookie_mode'   => isset( $this->cook['gtag_no_cookie_mode'] ),
        'url_passthrough'       => isset( $this->cook['url_passthrough'] ),
        'ask_for_consent_again' => isset( $this->cook['ask_for_consent_again'] ),
        'save_in_cdb'           => !empty( $this->cook['cdb_key'] ),
        'save_all_consents'     => isset( $this->cook['save_all_consents'] ),
        'priv_policy_update'    => $privacy_policy_update_date,
        'blur_page'             => !empty( $notice_data ) && !empty( $notice_data['blur_page'] ),
        'scroll_lock'           => !empty( $notice_data ) && !empty( $notice_data['scroll_lock'] ),
        'hidden'                => ( isset( $notice_data['hide'] ) ? $notice_data['hide'] : [] ),
        'shown'                 => ( isset( $notice_data['show'] ) ? $notice_data['show'] : [] ),
        'preselected_switches'  => ( isset( $notice_data['switches_on'] ) ? $notice_data['switches_on'] : [] ),
        'optin_switches'        => !empty( $notice_data['optin_switches'] ),
        'toggle_selector'       => ( !empty( $this->cook['toggle_selector'] ) ? esc_attr( $this->cook['toggle_selector'] ) . ' .fupi_show_cookie_notice, .fp_show_cookie_notice' : '.fupi_show_cookie_notice, .fp_show_cookie_notice' ),
    ];
    // UPDATE OBJECT
    $is_premium = false;
    if ( !$is_premium ) {
        $fp['notice']['mode'] = ( !empty( $this->cook['enable_scripts_after'] ) ? esc_attr( $this->cook['enable_scripts_after'] ) : 'optin' );
    }
    // default
    // if notice is disabled
} else {
    $fp['notice'] = [
        'enabled' => false,
    ];
}