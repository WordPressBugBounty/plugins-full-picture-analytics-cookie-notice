    FP.getConsentValues = ( cookies = {} ) => {

        let stats = !! cookies?.stats,
            pers = !! cookies?.personalisation,
            market = !! cookies?.marketing;

        let cons_vals = {
            "cookies" : {
                "stats" : stats,
                "personalisation" : pers,
                "marketing" : market,
            },
            "consents" : {
                "can_track_stats" : stats,
                "can_track_pers" : pers,
                "can_track_market" : market,
            },
            "gtag" : {
                "ad_storage": market ? "granted" : "denied",
                "ad_user_data" : market ? "granted" : "denied",
                "ad_personalization" : market ? "granted" : "denied",
                "analytics_storage": stats ? "granted" : "denied",
                "personalization_storage": pers ? "granted" : "denied",
                "functionality_storage": pers || stats || market ? "granted" : "denied",
                "security_storage": "granted",
            },
        };

        return cons_vals;
    };

    FP.updateConsents = ( cookies = {}, run_actions = false ) => {

        let cons_vals = FP.getConsentValues( cookies ),
            type = "update";
        
        fpdata.consents = cons_vals.consents;

        // GTAG
        ["gtag", "fupi_gtm_gtag"].forEach( tag_name => {

            if ( typeof window[tag_name] == "function" ) {

                window[tag_name]( "consent", type, cons_vals.gtag );

                if ( type == "update" ) {
                    if ( tag_name == "gtag" ) {
                        window.dataLayer.push( {
                            "event" : "fp_privacyPreferencesChanged",
                            "fp_visitorPrivacyPreferences" : cons_vals.cookies,
                        } );
                    } else {
                        window.fupi_dataLayer.push( {
                            "event" : "fp_privacyPreferencesChanged",
                            "fp_visitorPrivacyPreferences" : cons_vals.cookies,
                        } );
                    }
                }
            }
        });

        // MS ADS
        if ( window.uetq ) {
            window.uetq.push( "consent", type, {
                "ad_storage": cons_vals.gtag.ad_storage
            });
        }

        // Pixel Manager
        if ( fp.notice.scrblk_auto_rules && fp.notice.scrblk_auto_rules.includes("pixelman") ){
            window._pmwq = window._pmwq || [];
            window._pmwq.push(function() {
                if ( fpdata.cookies && !! window?.pmw?.consent?.api?.updateSelectively ) {
                    pmw.consent.api.updateSelectively({
                        statistics: cons_vals.cookies.stats,
                        marketing: cons_vals.cookies.marketing,
                        preferences: cons_vals.cookies.personalisation,
                        necessary: true,
                    });
                    console.log("[FP] Updated tracking consents for Pixel Manager");
                }
            });
        }

        if ( run_actions && typeof FP.doActions == "function" ) {
            FP.doActions(["privacy_consents_changed"], cons_vals);
        }

        if ( fp.main.debug ) console.log("[FP] Consents updated", cons_vals.consents);

        return cons_vals;
    };
    
    FP.updatePrivacyChoices = ( update_pageviews_count = true, run_actions = true )=>{

        let cookies = {},
            update_tools = false;
        
        if ( ! fp.main.track_current_user ) {

            if ( fp.main.debug ) console.log("[FP] Current user cannot be tracked");

        // if the user made a choice in the past
        } else if ( fpdata.cookies ){

            cookies = fpdata.cookies;
            update_tools = true;

        // if no choice was made in the past
        } else {
            
            // agree to all if consent banner is disabled or we are in optout or notification mode
            if ( ! ( fp.notice.enabled && fp.notice.mode == "optin" ) ) {
                cookies = {
                    'stats' : true,
                    'personalisation' : true,
                    'marketing' : true,
                };
                update_tools = true;
            }
        }

        if ( ! fp.main.is_customizer && update_tools ) {
            FP.updateConsents( cookies, run_actions );
        } else {
            let cons_vals = FP.getConsentValues( cookies );
            fpdata.consents = cons_vals.consents;
        }

        FP.setSessionData( update_pageviews_count );
    }
