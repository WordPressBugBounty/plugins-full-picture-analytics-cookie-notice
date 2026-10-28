    // INIT

    let cookies             = FP.readCookie('fp_cookie'),
        normally_trackable  = fp.main.track_current_user,
        magic_keyw          = FP.getUrlParamByName( fp.main.magic_keyword ),
        cookies_modified_by_url = false;

    // Get data

    fpdata.cookies = FP.parseJSON( cookies, false );
    if ( cookies && ! FP.isObject( fpdata.cookies ) ) {
        FP.deleteCookie( 'fp_cookie' );
        fpdata.cookies = false;
    }

    // Migrate tracking and diagnostic properties out of the consent record.
    if ( fpdata.cookies && ( Object.prototype.hasOwnProperty.call( fpdata.cookies, 'disabled' ) || Object.prototype.hasOwnProperty.call( fpdata.cookies, 'ga4_debug' ) ) ) {
        if ( fpdata.cookies.disabled === true ) FP.setCookie( 'fp_tracking_disabled', '1', 7300 );
        delete fpdata.cookies.disabled;
        delete fpdata.cookies.ga4_debug;
        FP.setCookie( 'fp_cookie', JSON.stringify(fpdata.cookies), 182 );
    }

    fpdata.new_tab = ! window.sessionStorage['fp_old_session'];
    fpdata.new_session = fpdata.new_tab; // updated with better method if we get permission to set cookies later

    window.sessionStorage['fp_old_session'] = true; // removed on a new tab

    // Reset first so diagnostic values in the same URL are not reset

    switch ( magic_keyw ) {
        case 'reset':
            FP.resetTrackingState();
            fpdata.cookies = false;
            cookies_modified_by_url = true;
            break;

        case 'off':
            FP.setCookie( 'fp_tracking_disabled', '1', 7300 );
            cookies_modified_by_url = true;
            break;

        case 'on':
            FP.deleteCookie( 'fp_tracking_disabled' );
            cookies_modified_by_url = true;
            break;
    }

    fp.main.testing = FP.readCookie('fp_track_me') === '1';
    fp.main.tracking_disabled = FP.readCookie('fp_tracking_disabled') === '1';
    fp.main.track_current_user = fp.main.testing || ( ! fp.main.tracking_disabled && normally_trackable );
    
    // Ask for consent again
    // if pp pub was modified or the website wants to load different tools than the user agreed to
    if ( 
        ! cookies_modified_by_url && 
        fpdata.cookies && 
        fp.notice.enabled && ! fp.notice.dont_ask_again
    ) {

        let pp_pub_changed = fp.notice.priv_policy_update && ( ! fpdata.cookies.pp_pub || fpdata.cookies.pp_pub != fp.notice.priv_policy_update );

        let tools_changed = fp.tools && ( ! fpdata.cookies.tools || ! fp.tools.every( id => fpdata.cookies.tools.includes(id) ) );

        if ( pp_pub_changed || tools_changed ) {
            FP.deleteCookie("fp_cookie");
            fpdata.cookies = false;
        }
    }

    // CONSENT BANNER - apply page blur and lock scroll ASAP

    if ( 
        fp.main.track_current_user && 
        ! fp.main.is_customizer && 
        fp.notice.enabled && 
        ! fpdata.cookies && 
        fp.notice.display_notice 
    ) {
        
        // BLUR BACKGROUND
        if ( fp.notice.blur_page ) document.getElementsByTagName( "html" )[0].classList.add("fupi_blur");
        
        // LOCK PAGESCROLL
        if ( fp.notice.scroll_lock ) document.getElementsByTagName( "html" )[0].classList.add("fupi_scroll_lock");
    }

    let uses_geo = false;

    

    if ( ! uses_geo ) {
        fpdata.country = 'unknown';
        FP.updatePrivacyChoices();
        fp.ready = true;
    }

    // BROKEN LINK TRACKING

    if ( fp.track.track404 && fpdata.page_type == "404" && ! FP.getUrlParamByName("broken_link_location") ){
        const location = fp.track.redirect404_url ? new URL( fp.track.redirect404_url ) : window.location;
        window.location = location + ( location.search ? "&" : "?" ) + "broken_link_location=" + ( document.referrer || "direct_traffic_or_unknown" ) + "&broken_link=" + window.location;
    }

    // LOAD SCRIPTS
    FP.doWhenDOMLoaded( addScriptsToDOM );
    document.addEventListener( 'fp_load_scripts', addScriptsToDOM );
