    // ACTIVITY TIMER START
    document.addEventListener( 'mousemove', FP.throttle( FP.startActivityTimer, 500 ) ); // This starts the user's activity timer

    // PAGE SHOW/HIDE

    function sync_cookies_and_data(){

        let fp_cookie = FP.readCookie( 'fp_cookie' ),
            fp_cookie_data = FP.parseJSON( fp_cookie, false ),
            had_consets_before = !! fpdata.cookies;

        // if we have fp_cookie data
        if ( fp_cookie ) {

            // Delete the data if it is empty or is incorrect...
            if ( ! fp_cookie_data || ! FP.isObject( fp_cookie_data ) ) {
            
                FP.deleteCookie( 'fp_cookie' );
            
                // and reload if some consents were given before
                if ( had_consets_before ) {
                    fp.vars.will_reload = true;
                    window.location.reload();
                    return;
                }

            // if cookie data is correct
            } else {

                // and some consents were given before
                if ( had_consets_before ) {

                    // reload if they changed
                    if ( JSON.stringify( fp_cookie_data ) !== JSON.stringify( fpdata.cookies ) ) {
                        fp.vars.will_reload = true;
                        window.location.reload();
                        return;
                    
                    // or just update session data if they are the same
                    } else {
                        FP.setSessionData( false );
                    }
                
                // and no consent were given before
                } else {
                    
                    // instruct the CMS to hide the banner
                    fp.notice.hide_consent_banner = true;
                    FP.sendEvt('fupi_hide_cookie_banner');
                    
                    // update all data and add scr to DOM
                    fpdata.cookies = fp_cookie_data;
                    fp.ready.cmp = true;
                    fupi_run('tab_switch');
                }
            }

        // if we don't have fp_cookie
        } else {
            
            // reload the page if some consents were given before
            if ( had_consets_before ) {
                fp.vars.will_reload = true;
                window.location.reload();
                return;
            
            // or just update session data
            } else {
                FP.setSessionData( false );
            }
        }
    }

    fpdata.doc_in_focus = false;

    if ( ! document.hidden) {
        fpdata.doc_in_focus = true; // skips "show" actions on initial pageload
        FP.startActivityTimer();
    };

    function showPageActions() {
        if ( fpdata.doc_in_focus ) return;

        fpdata.doc_in_focus = true;
        fpdata.activity.current = 0;
        FP.startActivityTimer();
        sync_cookies_and_data();

        if ( ! fp.vars.will_reload ) {
            FP.doActions( ['page_show', 'page_in_focus'] );
        }
    }

    function hidePageActions() {
        if ( ! fpdata.doc_in_focus ) return;

        fpdata.doc_in_focus = false;
        FP.stopActivityTimer();
        FP.doActions( ['page_hide', 'page_in_blur'] );
    }

    window.addEventListener( 'pagehide', hidePageActions, false );

    window.addEventListener( 'pageshow', ()=>{
        // Gives visibilitychange priority because it sees updated cookies/storage.
        setTimeout( showPageActions, 0 );
    }, false );

    document.addEventListener( 'visibilitychange', ()=>{
        document.hidden ? hidePageActions() : showPageActions();
    }, false );