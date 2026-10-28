function fupi_footer_helpers(){

    //
    // VIEWS TRIGGERS
    //

    function setIntersectionObserver ( els, trigger_id, trigger_data ) {

        let observed_count = els.length;

        function unobserveTarget( observer_name, observer, target ) {
            observer.unobserve( target ); // stop observing after the first view
            observed_count--;

            if ( observed_count == 0 ) {
                observer.disconnect();
                delete fp.observers[observer_name];
            }
        }

        // make an observer
        const observer = new IntersectionObserver( 
            ( entries, observer ) => {
                entries.forEach( ( { isIntersecting, target } ) => {
                    if ( isIntersecting ) {
                        let hooked_actions = trigger_data.actions;
                        unobserveTarget( observer_name, observer, target );
                        if ( hooked_actions ) hooked_actions.forEach( action => action[0]( target ) );
                    }
                });
            }, { 
                rootMargin: fp.track.intersections 
            } 
        );

        // save observer in array
        const observer_name = trigger_id + '_' + Date.now();
        fp.observers[observer_name] = observer;

        // run observer on every found element
        els.forEach( el => fp.observers[observer_name].observe(el) );
    };

    function findViewTriggerEls( added_els, selector ) {

        let els = [];

        added_els.forEach( el => {
            if ( el.nodeType !== Node.ELEMENT_NODE ) return;
            if ( el.matches( selector ) ) els.push( el );
            els = els.concat( FP.findAll( selector, el ) );
        } );

        return els;
    }

    // Check if there are elements on page that match triggers
    // Set observers for these triggers

    function registerIntersObservers ( newly_added_els = false ) {

        // STOP if there were some dom modification, but no new elements were added
        if ( newly_added_els && newly_added_els.length == 0 ) return;
        if ( ! fp.atrig || ! fp.atrig.view_triggers ) return;

        let triggers_o = fp.atrig.view_triggers;

        for ( const [trigger_id, trigger_data] of Object.entries(triggers_o) ) {
            
            // We do NOT STOP if the trigger is not used. CBs may be added by tools that haven't loaded yet

            let els = [];

            if ( newly_added_els && newly_added_els.length > 0 ) {
                els = findViewTriggerEls( newly_added_els, trigger_data.sel );
            } else {
                els = FP.findAll( trigger_data.sel );
            }

            if ( els.length == 0 ) continue;

            // Set observer for this trigger
            setIntersectionObserver( els, trigger_id, trigger_data );
        };
    };

    //
    // Enable Mutation Observers for Intersection Observers
    //
    
    FP.detectAddedElement = callback => { // also used by consb.js
        
        if ( ! Array.isArray( fp.observers.mutation ) ) {
            fp.observers.mutation = [];
        }

        // Create a unique ID for this observer registration
        const observerId = 'mutation_' + Date.now() + '_' + FP.getRandomStr();

        const observer = new MutationObserver( mutations => {
            mutations.forEach( mutation => {
                if ( mutation.type === 'childList' && mutation.addedNodes.length > 0 ) {
                    callback( mutation.addedNodes );
                }
            } );
        } );

        observer.observe( document.body, {
            childList: true,
            subtree: true
        } );

        // Store this observer registration with its ID
        fp.observers.mutation.push({
            id: observerId,
            observer: observer,
            callback: callback
        });
    }

    // Start tracking views only after the DOM is loaded and later, when it is modified

    function initObservers(){
        
        registerIntersObservers();

        if ( fp.track.use_mutation_observer ) {
            FP.detectAddedElement( addedNodes => {
                let els = FP.nl2Arr( addedNodes );
                if ( els.length > 0 ) FP.doActions( ['dom_modified'], els );
            } );
        }
    }

    FP.doWhenDOMLoaded( initObservers );

    FP.addAction( ['dom_modified'], registerIntersObservers );
    

    FP.actionChecks.form_triggers = trigger_data => {
        return fpdata.submitted_form.element.matches( trigger_data.sel );
    }
    
    function formSubmitEvents( {target} ){
        fpdata.submitted_form = { 'element' : target };
        FP.doTriggerActions( 'form_triggers' );
        FP.doActions( 'form_submit' );
    }

    document.addEventListener('blur', function(e) {
        if ( e.target.nodeType === Node.ELEMENT_NODE && e.target.matches('input, textarea, select') ) {
            if ( e.target.type && e.target.type == 'submit' ) return; // do not track submit btn
            fpdata.last_form_field = e.target;
            FP.doActions( 'form_field_blur' );
        }
    }, true);

    function wait_for_submit_events(){
        setTimeout( ()=>{ document.addEventListener('submit', formSubmitEvents ) }, fp.track.formsubm_trackdelay ? fp.track.formsubm_trackdelay * 1000 : 1000 );
    }

    FP.doWhenDOMLoaded( wait_for_submit_events );

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
    

    

    FP.doWhenDOMLoaded( () => {
        if ( fp.vars.dom_loaded ) return;

        fp.vars.dom_loaded = true;
        FP.doActions( 'dom_loaded' );
    } );

	FP.updateScrollValues = function(){

        fpdata.scrolled = fpdata.scrolled || {};

        fpdata.scrolled.current         = Math.round( document.documentElement.scrollTop / ( document.documentElement.scrollHeight - document.documentElement.clientHeight ) * 100 ) || 0;
        fpdata.scrolled.current_px      = document.documentElement.scrollTop || 0;
        fpdata.scrolled.max             = fpdata.scrolled.max || fpdata.scrolled.current;
        fpdata.scrolled.max_px          = fpdata.scrolled.max_px || fpdata.scrolled.current_px;

        if ( fpdata.scrolled.current > fpdata.scrolled.max ) {
			fpdata.scrolled.max         = fpdata.scrolled.current;
			fpdata.scrolled.max_px      = fpdata.scrolled.current_px;
		}
	}

	FP.actionChecks.scroll_after_wait = ()=>{

		if ( ! fp.events.scroll_after_wait || fp.events.scroll_after_wait.length == 0 ) return false;
		if ( fpdata.activity.total < fp.track.track_scroll_time || fpdata.scrolled.current_px < fp.track.track_scroll_min ) return false;
		
		let reached_point = false,
			max = fp.track.scroll_depths.length;

		// get lowest reached scroll
		for ( var i = 0; i < max; i++ ) {
			if ( fpdata.scrolled.max > fp.track.scroll_depths[i] ) {
				reached_point = fp.track.scroll_depths[i];
			}
		}

		if ( ! reached_point ) return false;

		// remove reached scroll points
		fp.track.scroll_depths = fp.track.scroll_depths.filter( function( point ){ return point > reached_point } );

		// remember in fpdata
		fpdata.scrolled.reached_point = reached_point;

		return true;
	}

	// set base values
	FP.updateScrollValues();

	// listen to scroll
    function on_scroll() {
        FP.startActivityTimer();
        FP.updateScrollValues();
		FP.doActions( ['scroll_after_wait', 'scroll'] );
    }

    document.addEventListener( 'scroll', FP.throttle( on_scroll, 50, { trailing: true } ) );

    // MOUSELEAVE
    document.documentElement.addEventListener('mouseleave', (e) => {
        // Don't fire if the page is losing focus (not a real mouse leave)
        if (document.hidden) return;
        
        // Check if mouse actually left viewport boundaries
        if (e.clientY <= 0 || e.clientX <= 0 || 
            e.clientX >= window.innerWidth || e.clientY >= window.innerHeight) {
            FP.doActions('mouse_leave_window');
        }
    }, false );
    
    // DYNAMIC URL CHANGE && TIME UPDATE IN FPDATA
    // Use History API hooks for immediate URL change detection, with fallback polling
    
    // Track current URL
    let currentUrl = location.href;
    
    // Update time every second regardless of focus
    setInterval( () => { 
        fpdata.date = FP.getFormattedDateTime();
    }, 1000);

    function isVirtualPageview( url ) {

        const rules = fp.track.virtual_pageviews || [];

        if ( rules.length == 0 ) return true; // Treat all URL changes as virtual pageviews

        return rules.some( rule => {
            if ( ! rule.url_part ) return false;
            return rule.compare == 'contains' ? url.includes( rule.url_part ) : ! url.includes( rule.url_part );
        } );
    }
    
    // Function to check and handle URL changes
    function checkUrlChange() {
        if ( location.href !== currentUrl ) {
            currentUrl = location.href;
            FP.doActions( 'url_change' );

            if ( isVirtualPageview( currentUrl ) ) {
                FP.doActions( 'virtual_pageview' );
            }
        }
    }
    
    // Try to use History API for immediate detection
    try {
        // Override pushState
        const originalPushState = history.pushState;
        history.pushState = function() {
            const result = originalPushState.apply(this, arguments);
            // Delay slightly to let SPAs update the page title and content.
            setTimeout(checkUrlChange, 200);
            return result;
        };
        
        // Override replaceState
        const originalReplaceState = history.replaceState;
        history.replaceState = function() {
            const result = originalReplaceState.apply(this, arguments);
            // Delay slightly to let SPAs update the page title and content.
            setTimeout(checkUrlChange, 200);
            return result;
        };
        
        // Listen for popstate events (back/forward buttons)
        window.addEventListener('popstate', () => setTimeout(checkUrlChange, 200));
    } catch (e) {
        // If History API wrapping fails, fall back to polling
        if (fp.main.debug) console.log('[FP] History API wrapping failed, using polling for URL changes');
        setInterval(checkUrlChange, 1000);
    }
    // SHOW "USER NOT TRACKED" ICON AT THE BOTTOM-LEFT CORNER OF THE SCREEN

	function show_eye_icon(){
		document.body.insertAdjacentHTML('afterbegin', '<div id=\'fupi_disabled\' title="Tracking disabled"><svg style="width: 20px;" focusable="false" data-prefix="fas" data-icon="eye-slash" class="svg-inline--fa fa-eye-slash fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path></svg></div><style>#fupi_disabled{position: fixed; left: 0; bottom: 0; color: #fff; background-color: rgba(0,0,0,.2); padding: 5px 5px 0; border-radius: 0 3px 0 0; z-index: 10000;cursor: pointer;}#fupi_disabled:hover{background-color: rgba(0,0,0,.8);}</style>');

		var disabled_ico = FP.findID('fupi_disabled');

		disabled_ico.addEventListener('click', function (e) {
			FP.deleteCookie('fp_tracking_disabled');
			document.location = location.origin;
		})
	}

	if ( fp.main.tracking_disabled && ! fp.main.testing ) FP.doWhenDOMLoaded( show_eye_icon );



	FP.loaded('footer-helpers');
};

FP.load('footer-helpers', 'fupi_footer_helpers')