<?php

$fp['tools'] = [];
// GOOGLE ANALYTICS 4
if ( $this->ga41_enabled ) {
    $ga41_data = get_option( 'fupi_ga41' );
    if ( !empty( $ga41_data ) ) {
        $fp['ga41'] = $ga41_data;
        $fp['tools'][] = 'ga41';
    }
}
// GTM
if ( $this->gtm_enabled ) {
    $gtm_data = get_option( 'fupi_gtm' );
    if ( !empty( $gtm_data ) ) {
        $fp['gtm'] = $gtm_data;
        $fp['tools'][] = 'gtm';
    }
}
// FB PIXEL DATA
if ( $this->fbp1_enabled ) {
    $fbp_data = get_option( 'fupi_fbp1' );
    if ( !empty( $fbp_data ) ) {
        if ( !empty( $fbp_data['capi_token'] ) ) {
            unset($fbp_data['capi_token']);
            $fbp_data['server_side'] = true;
        }
        if ( !empty( $fbp_data['capi_token_2'] ) ) {
            unset($fbp_data['capi_token_2']);
            $fbp_data['server_side_2'] = true;
        }
        if ( !empty( $fbp_data['test_code'] ) ) {
            unset($fbp_data['test_code']);
        }
        if ( !empty( $fbp_data['test_code_2'] ) ) {
            unset($fbp_data['test_code_2']);
        }
        $fp['fbp'] = $fbp_data;
        $fp['tools'][] = 'fbp';
    }
}
// MS CLARITY
if ( $this->clar_enabled ) {
    $clar_data = get_option( 'fupi_clar' );
    if ( !empty( $clar_data ) ) {
        $fp['clar'] = $clar_data;
        $fp['tools'][] = 'clar';
    }
}
// HOTJAR
if ( $this->hotj_enabled ) {
    $hotj_data = get_option( 'fupi_hotj' );
    if ( !empty( $hotj_data ) ) {
        $fp['hotj'] = $hotj_data;
        $fp['tools'][] = 'hotj';
    }
}
// INSPECTLET
if ( $this->inspectlet_enabled ) {
    $insp_data = get_option( 'fupi_insp' );
    if ( !empty( $insp_data ) ) {
        $fp['insp'] = $insp_data;
        $fp['tools'][] = 'insp';
    }
}
// CRAZYEGG
if ( $this->crazyegg_enabled ) {
    $cegg_data = get_option( 'fupi_cegg' );
    if ( !empty( $cegg_data ) ) {
        $fp['cegg'] = $cegg_data;
        $fp['tools'][] = 'cegg';
    }
}
// LINKEDIN INSIGHT
if ( $this->linkd_enabled ) {
    $linkd_data = get_option( 'fupi_linkd' );
    if ( !empty( $linkd_data ) ) {
        $fp['linkd'] = $linkd_data;
        $fp['tools'][] = 'linkd';
    }
}
// TIKTOK
if ( $this->tik_enabled ) {
    $tik_data = get_option( 'fupi_tik' );
    if ( !empty( $tik_data ) ) {
        $fp['tik'] = $tik_data;
        $fp['tools'][] = 'tik';
    }
}
// GOOGLE ADS
if ( $this->gads_enabled ) {
    $gads_data = get_option( 'fupi_gads' );
    if ( !empty( $gads_data ) ) {
        $fp['gads'] = $gads_data;
        $fp['tools'][] = 'gads';
    }
}
// X ADS
if ( $this->twit_enabled ) {
    $twit_data = get_option( 'fupi_twit' );
    if ( !empty( $twit_data ) ) {
        $fp['twit'] = $twit_data;
        $fp['tools'][] = 'twit';
    }
}
// PINTEREST ADS
if ( $this->pin_enabled ) {
    $pin_data = get_option( 'fupi_pin' );
    if ( !empty( $pin_data ) ) {
        $fp['pin'] = $pin_data;
        $fp['tools'][] = 'pin';
    }
}
// MICROSOFT ADS
if ( $this->mads_enabled ) {
    $mads_data = get_option( 'fupi_mads' );
    if ( !empty( $mads_data ) ) {
        $fp['mads'] = $mads_data;
        $fp['tools'][] = 'mads';
    }
}
// MATOMO
if ( $this->mato_enabled ) {
    $mato_data = get_option( 'fupi_mato' );
    if ( !empty( $mato_data ) ) {
        $fp['mato'] = $mato_data;
        $fp['tools'][] = 'mato';
    }
}
// POSTHOG
if ( $this->posthog_enabled ) {
    $posthog_data = get_option( 'fupi_posthog' );
    if ( !empty( $posthog_data ) ) {
        $fp['posthog'] = $posthog_data;
        $fp['tools'][] = 'posthog';
    }
}
// PLAUSIBLE
if ( $this->pla_enabled ) {
    $pla_data = get_option( 'fupi_pla' );
    $fp['pla'] = ( !empty( $pla_data ) ? $pla_data : array() );
    // plausible does not require any setup to run
    $fp['tools'][] = 'pla';
}
// SIMPLE ANALYTICS
if ( $this->simpl_enabled ) {
    $simpl_data = get_option( 'fupi_simpl' );
    $fp['simpl'] = ( !empty( $simpl_data ) ? $simpl_data : array() );
    // plausible does not require any setup to run
    $fp['tools'][] = 'simpl';
}
// IFRAME MANAGER
if ( isset( $this->tools['iframeblock'] ) ) {
    $iframeblock = get_option( 'fupi_iframeblock' );
    if ( empty( $iframeblock ) ) {
        $iframeblock = [];
    }
    $iframeblock['privacy_url'] = get_privacy_policy_url();
    if ( empty( $iframeblock['btn_text'] ) ) {
        $iframeblock['btn_text'] = esc_html__( 'Load content', 'full-picture-analytics-cookie-notice' );
    }
    if ( empty( $iframeblock['caption_txt'] ) ) {
        $iframeblock['caption_txt'] = esc_html__( 'This content is hosted by [[an external source]]. By loading it, you accept its {{privacy terms}}.', 'full-picture-analytics-cookie-notice' );
    }
    $fp['iframeblock'] = $iframeblock;
}
// ADVANCED TRIGGERS
if ( isset( $this->tools['atrig'] ) ) {
    $atrig_opts = get_option( 'fupi_atrig' );
    if ( !empty( $atrig_opts ) ) {
        if ( !empty( $atrig_opts['lead_scoring_levels'] ) ) {
            $trimmed_string = trim( $atrig_opts['lead_scoring_levels'] );
            $split_array = explode( ',', $trimmed_string );
            $score_levels_str = array_map( 'trim', $split_array );
            $score_levels = array_map( 'intVal', $score_levels_str );
            if ( count( $score_levels ) > 0 ) {
                $atrig_opts['lead_scoring_levels'] = $score_levels;
            }
        }
        if ( !empty( $atrig_opts['triggers'] ) && is_array( $atrig_opts['triggers'] ) ) {
            // remove names
            for ($i = 0; $i < count( $atrig_opts['triggers'] ); $i++) {
                unset($atrig_opts['triggers'][$i]['name']);
            }
            $fp['atrig'] = $atrig_opts;
        }
    }
}
// CUSTOM SCRIPTS
if ( $this->cscr_enabled ) {
    $cscr_data = get_option( 'fupi_cscr' );
    if ( !empty( $cscr_data ) ) {
        if ( !empty( $cscr_data['fupi_head_scripts'] ) ) {
            foreach ( $cscr_data['fupi_head_scripts'] as $head_scr_data ) {
                $fp['tools'][] = $head_scr_data['id'];
            }
        }
        if ( !empty( $cscr_data['fupi_footer_scripts'] ) ) {
            foreach ( $cscr_data['fupi_footer_scripts'] as $footer_scr_data ) {
                $fp['tools'][] = $footer_scr_data['id'];
            }
        }
    }
}