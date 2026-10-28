<?php

$this->regenerate_cdb = true;
        
$this->get_fupi_options( ['fupi_tools', 'fupi_main', 'fupi_cook', 'fupi_trackmeta', 'fupi_track404', 'fupi_privex', 'fupi_iframeblock', 'fupi_blockscr', 'fupi_woo', 'fupi_hotj'] );

$this->validate_fields( $this->o['fupi_cook'], [ 'cdb_key', 'save_all_consents', 'consent_access', 'url_passthrough' ] );

$this->validate_fields( $this->o['fupi_iframeblock'], [ 'btn_text', 'caption_txt', 'iframe_img', 'iframe_lazy' ] );

$this->validate_fields( $this->o['fupi_hotj'], [ 'no_pii' ] );

foreach ( [ 
    [ 'fupi_cook', 'scrblk_auto_rules' ], 
    [ 'fupi_blockscr', 'auto_rules' ], 
    [ 'fupi_iframeblock', 'auto_rules' ] 
] as $field ) {
    if ( array_key_exists( $field[1], $this->o[$field[0]] ) && ! is_array( $this->o[$field[0]][$field[1]] ) ) {
        throw new UnexpectedValueException( 'Invalid consent rules shape.' );
    }
}

foreach ( [ 
    [ 'fupi_cook', 'scrblk_manual_rules' ], 
    [ 'fupi_blockscr', 'blocked_scripts' ], 
    [ 'fupi_iframeblock', 'manual_rules' ] 
] as $field ) {
    if ( array_key_exists( $field[1], $this->o[$field[0]] ) ) $this->validate_rows( $this->o[$field[0]][$field[1]] );
}

// MAKE FUPI_TRACK OPTS
// fupi_main + fupi_trackmeta + fupi_track404 + labelpages (setting of fupi_tools)

// JOIN options (this must go first)

$track_opts = array_merge( $this->o['fupi_main'], $this->o['fupi_trackmeta'], $this->o['fupi_track404'] );

// ADD options

// enable 404 tracking
if ( isset ( $this->o['fupi_tools']['track404'] ) ) $track_opts['track_404'] = true;

// enable page labels
if ( isset ( $this->o['fupi_tools']['labelpages'] ) ) $track_opts['page_labels'] = true;

// Remember option data
$this->add_fupi_option( 'fupi_track', $track_opts );

// ADD PRIVEX (fp_info shortcode) TO FUPI_MAIN

if ( ! empty( $this->o['fupi_tools']['privex'] ) && count( $this->o['fupi_privex'] ) > 0 ) {
    $this->o['fupi_main'] = array_merge( $this->o['fupi_main'], $this->o['fupi_privex'] );
}

// (everything below requires fupi_cook)

// COPY FUPI_IFRAMEBLOCK AND FUPI_BLOCKSCR TO FUPI_COOK

// iframeblock to fupi_cook
if ( isset( $this->o['fupi_tools']['iframeblock'] ) ) {

    if ( ! empty( $this->o['fupi_iframeblock']['auto_rules'] ) ) $this->o['fupi_cook']['iframe_auto_rules'] = $this->o['fupi_iframeblock']['auto_rules'];

    if ( ! empty( $this->o['fupi_iframeblock']['manual_rules'] ) ) {
        $this->o['fupi_cook']['iframe_manual_rules'] = $this->o['fupi_iframeblock']['manual_rules'];
        $this->o['fupi_cook']['control_other_iframes'] = true;
    }

    if ( ! empty( $this->o['fupi_iframeblock']['btn_text'] ) ) $this->o['fupi_cook']['iframe_btn_text'] = $this->o['fupi_iframeblock']['btn_text'];
    
    if ( ! empty( $this->o['fupi_iframeblock']['caption_txt'] ) ) $this->o['fupi_cook']['iframe_caption_txt'] = $this->o['fupi_iframeblock']['caption_txt'];

    if ( ! empty( $this->o['fupi_iframeblock']['iframe_img'] ) ) $this->o['fupi_cook']['iframe_img'] = $this->o['fupi_iframeblock']['iframe_img'];

    if ( ! empty( $this->o['fupi_iframeblock']['iframe_lazy'] ) ) $this->o['fupi_cook']['iframe_lazy'] = $this->o['fupi_iframeblock']['iframe_lazy'];
}

// blockscr to fupi_cook
if ( isset( $this->o['fupi_tools']['blockscr'] ) ) {

    if ( ! empty( $this->o['fupi_blockscr']['auto_rules'] ) ) $this->o['fupi_cook']['scrblk_auto_rules'] = $this->o['fupi_blockscr']['auto_rules'];

    if ( ! empty( $this->o['fupi_blockscr']['blocked_scripts'] ) ) {
        $this->o['fupi_cook']['scrblk_manual_rules'] = $this->o['fupi_blockscr']['blocked_scripts'];
        $this->o['fupi_cook']['control_other_tools'] = true;
    }
}

// Make a new Proofrec option

if ( isset( $this->o['fupi_cook']['cdb_key'] ) ) {

    // Enable proofrec module
    $this->o['fupi_tools']['proofrec'] = true;

    $fupi_proofrec_opts = [
        'cdb_key' => $this->o['fupi_cook']['cdb_key'],
    ];
    
    if ( ! empty( $this->o['fupi_cook']['save_all_consents'] ) ) $fupi_proofrec_opts['save_all_consents'] = $this->o['fupi_cook']['save_all_consents'];

    if ( ! empty( $this->o['fupi_cook']['consent_access'] ) ) $fupi_proofrec_opts['consent_access'] = $this->o['fupi_cook']['consent_access'];
    
    $this->add_fupi_option( 'fupi_proofrec', $fupi_proofrec_opts );
}

// Make a new Google Tag option

$gtag_opts = [];

if ( ! empty( $this->o['fupi_cook']['url_passthrough'] ) ) {
    $gtag_opts['url_passthrough'] = true;
    $this->add_fupi_option( 'fupi_gtag', $gtag_opts );
}

// Add blocking Sourcebuster.js to fupi_woo

if ( ! empty( $this->o['fupi_cook']['scrblk_auto_rules'] ) && in_array( 'woo_sbjs', $this->o['fupi_cook']['scrblk_auto_rules'] ) ) {
    $this->o['fupi_woo']['block_sbjs'] = true;
}

// Send email to users of Hotjar who use a consent banner and Privacy mode in Hotjar

if ( ! empty( $this->o['fupi_tools']['cook'] ) && ! empty( $this->o['fupi_tools']['hotj'] ) ) {

    if ( ! empty( $this->o['fupi_hotj']['no_pii'] ) ) {

        $email_to = get_option( 'admin_email' );
        $sitename = get_bloginfo('name');

        $subject = sprintf( esc_attr__( 'Important changes to privacy settings of Hotjar integration on %1$s', 'full-picture-analytics-cookie-notice' ), $sitename );

        $content = sprintf( esc_attr__( "This email was sent automatically from %1\$s by WP Full Picture plugin.\n\nWe would like to inform you that WP Full Picture plugin has been updated to version 9.0 and comes with an important, privacy-related change to Hotjar integration.\n\nDue to privacy laws, we decided to disable privacy mode in Hotjar. As a result, Hotjar will no longer load without asking for consent. Instead, we have introduced \"Data suppression\" option, which you can now find in the settings of the Hotjar module. It will allow you to use Hotjar without asking for consent in some regions.\n\nKind regards\nWP Full Picture's team", 'full-picture-analytics-cookie-notice' ), get_bloginfo('url') );

        $this->queue_deferred_work( static function () use ( $email_to, $subject, $content ) {
            wp_mail( $email_to, $subject, $content );
        } );
    }
}
