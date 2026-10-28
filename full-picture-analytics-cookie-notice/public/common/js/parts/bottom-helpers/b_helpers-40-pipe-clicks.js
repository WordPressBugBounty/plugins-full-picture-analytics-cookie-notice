    // the difference between this Fn and isAnchorLinkToCurrentPage is that this one doesn't check if the link actually works
    function isLinkToCurrentPage (link) {
        let target_url_obj = new URL( link.href );
        return target_url_obj.origin + target_url_obj.pathname == location.origin + location.pathname;
    }

    // checks if the link is an anchor to an EXISTING element on the same page
    function isAnchorLinkToCurrentPage (link){
        let url = new URL( link.href ),
            el;
        
        try {
            el = FP.findFirst( url.hash );
        } catch (e) {
            el = false;
        }
        
        return !! el;
    };

    function isOutboundLink(el){
        return el.href.indexOf(location.host) == -1 && el.href.indexOf('mailto:') != 0 && el.href.indexOf('tel:') != 0 && el.href.indexOf('http') == 0;
    };

    function isDownloadLink( link_el, link_data ){

        if ( ! fp?.track?.downl_file_formats ) return link_data;

        let formats_a = fp.track.downl_file_formats,
            cut_pos = link_el.href.search(/[?#]/),
            clean_href = cut_pos === -1 ? link_el.href : link_el.href.slice(0, cut_pos),
            last_slash_pos = clean_href.lastIndexOf('/'),
            filename = last_slash_pos !== -1 ? clean_href.slice(last_slash_pos + 1) : '',
            lower_filename = filename.toLowerCase();

        for ( let format of formats_a ) {
            if ( lower_filename.endsWith('.' + format ) ) {
                link_data.is_download = true;
                link_data.filename = filename;
                break;
            }
        }

        return link_data;
    }

    function isEmailLink( link_el, link_data ){
        if ( link_el.href.includes('mailto:') ){
            link_data.is_email = true;
            link_data.is_contact = true;
            link_data.safe_email = link_el.href.replace('mailto:', '').split('@')[0];
        }
        return link_data;
    }
    function isPhoneLink( link_el, link_data ){
        if ( link_el.href.includes('tel:') ){
            link_data.is_tel = true;
            link_data.is_contact = true;
            link_data.safe_tel = link_el.href.replace('tel:','').slice(-5);
        }
        return link_data;
    }

    FP.actionChecks.download = ()=>{
        return fp?.events?.download && fpdata?.clicked?.link?.is_download;
    }

    FP.actionChecks.outbound = ()=>{
        return fp?.events?.outbound && fpdata?.clicked?.link?.is_outbound;
    }

    FP.actionChecks.contact = ()=>{
        return fp?.events?.contact && fpdata?.clicked?.link?.is_contact;
    }

    FP.actionChecks.click_triggers = trigger_data => {

        let trigger_selector = trigger_data.sel;

        if ( fpdata.clicked.element.matches( trigger_selector ) ) {
            if ( ! fpdata.clicked.middle || fpdata.clicked.link ) {
                return fpdata.clicked.element;
            }
        } else if ( fpdata.clicked?.link?.element?.matches( trigger_selector ) ) {
            return fpdata.clicked.link.element;
        }

        return false;
    }

    FP.actionChecks.aff_click_triggers = trigger_data => {
        return fpdata.clicked.link && fpdata.clicked.link.href.includes( trigger_data.sel ) ? fpdata.clicked.link.element : false;
    }

    function replayLinkClick( link_el ) {
        setTimeout( ()=>{
            link_el.click();
            link_el.classList.remove('fupi_click_stopped');
            link_el.style.pointerEvents = '';
            if ( fp.main.debug ) console.log('[FP] Replayed click event after all FP functions');
        }, 10 );
    }

    function trackClicks(e, is_middle_click){

        let redirect_stopped = false,
            target = e.target,
            target_class_list = target.classList;

        if ( target_class_list.contains('fupi_click_stopped') ) return;
        
        // Prevent multi-clicks
        var now = Date.now();
        if ( ! ( ! FP.v.lastClick || now - FP.v.lastClick > 300 ) ) return;
        FP.v.lastClick = now;
        
        // Prevent current-page refresh/redirect after link-clicks
        var link_el = e.target.closest('a[href]');
        
        if ( fp.track.link_click_delay && link_el && ! is_middle_click && link_el.target != '_blank' ){
            e.preventDefault(); // prevents redirects
            e.stopPropagation(); // prevents other event listeners from firing
            target_class_list.add('fupi_click_stopped');
            target.style.pointerEvents = 'none';
            redirect_stopped = true;
            if ( fp.main.debug ) console.log('[FP] link-click stopped');
        };

        // GET CLICK DATA

        fpdata.clicked = {
            'element' : target,
            'middle' : is_middle_click,
        };

        if ( link_el && link_el.href.length > 1 ) {
            
            let link_data = {};

            link_data.element			    = link_el;
            link_data.href			        = link_el.href;
            link_data.target_blank 	        = link_el.target == '_blank';
            
            link_data.is_to_current_page    = isLinkToCurrentPage( link_el );
            link_data.is_outbound 	        = isOutboundLink( link_el );
            link_data.is_anchor 		    = isAnchorLinkToCurrentPage( link_el );
            link_data                       = isDownloadLink( link_el, link_data );
            link_data                       = isEmailLink( link_el, link_data );
            link_data                       = isPhoneLink( link_el, link_data );

            fpdata.clicked.link = link_data;
        }

        // RUN QUEUED FNs
        
        FP.doTriggerActions(['click_triggers', 'aff_click_triggers'] );
        FP.doActions( ['download', 'outbound', 'contact', 'click'] );

        // CONTINUE NAVIGATION

        if ( redirect_stopped ) replayLinkClick( link_el );
    };

    // ON LEFT CLICK
    window.addEventListener( 'click', e=>{
        trackClicks(e, false);
    }, true);

    // ON MIDDLE CLICK
    window.addEventListener( 'mouseup', e=>{
        if ( e.button == 1) trackClicks(e, true);
    });
    
