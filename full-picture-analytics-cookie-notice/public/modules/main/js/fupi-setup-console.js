function fupi_setup_console(){

    'use strict';
    
    let console_toggle_btns = FP.findAll('.fupi_console_toggle_btn'),
        tracking_console = FP.findID('fupi_console');
    
    if ( ! tracking_console || console_toggle_btns.length == 0 ) {
        FP.loaded('setup-console');
        if ( fpdata.user.role !== 'administrator' ) {
            console.warn('[FP] Please log in as an administrator to use setup console.');
        } else {
            console.warn('[FP] Setup console script loaded but the console\'s HTML is missing. Refresh check your cache.');
        }
        return;
    }

    let fixed_btn = FP.findID('fupi_console_fixed_btn');

    // PANEL NAVIGATION

    let console_panels = FP.findAll( '.fupi_console_panel', tracking_console ),
        active_panel = null,
        panel_layer = 1;

    function show_panel( panel_id, go_back = false ){
        let target_panel = FP.findID( panel_id ),
            move_focus = active_panel !== null;

        if ( ! target_panel || target_panel === active_panel ) return;

        if ( ! active_panel ) {
            console_panels.forEach( panel => {
                panel.classList.remove( 'fupi_panel_visible', 'fupi_panel_active' );
                panel.setAttribute( 'aria-hidden', 'true' );
                panel.inert = true;
            } );
            target_panel.style.zIndex = panel_layer;
        } else if ( go_back ) {
            let target_layer = panel_id === 'fupi_console_home' ? 1 : Math.max( 1, panel_layer - 1 );
            console_panels.forEach( panel => {
                if ( panel !== active_panel && panel !== target_panel ) panel.classList.remove( 'fupi_panel_visible' );
            } );
            target_panel.style.zIndex = target_layer;
            active_panel.classList.remove( 'fupi_panel_visible' );
            panel_layer = target_layer;
        } else {
            panel_layer++;
            target_panel.style.zIndex = panel_layer;
        }

        if ( active_panel ) {
            active_panel.classList.remove( 'fupi_panel_active' );
            active_panel.setAttribute( 'aria-hidden', 'true' );
            active_panel.inert = true;
        }

        target_panel.classList.add( 'fupi_panel_visible', 'fupi_panel_active' );
        target_panel.setAttribute( 'aria-hidden', 'false' );
        target_panel.inert = false;
        target_panel.scrollTop = 0;
        active_panel = target_panel;

        if ( move_focus ) {
            let heading = target_panel.querySelector( 'h2' );
            if ( heading ) {
                heading.tabIndex = -1;
                heading.focus( { preventScroll: true } );
                target_panel.parentElement.scrollLeft = 0;
            }
        }
    }
    
    // TOGGLE CONSOLE
    console_toggle_btns.forEach( btn => {
        btn.addEventListener('click', ()=>{
            tracking_console.classList.toggle('fupi_hidden');
            
            if ( tracking_console.classList.contains('fupi_hidden') ) {
                fixed_btn.classList.remove('fupi_hidden');
            } else {
                fixed_btn.classList.add('fupi_hidden');
            }
        });
    });

    // CHECK IF TRACKABLE
    let is_trackable = fp.main.testing,
        start_test_btn = FP.findID('fupi_start_test'),
        reset_tracking_btn = FP.findID('fupi_reset_tracking'),
        end_test_btn = FP.findID('fupi_end_test'),
        show_banner_btn = FP.findID('fupi_show_banner');

    // IF IS TRACKABLE CHANGE CONSOLE CONTENT

    if ( is_trackable ) {
        tracking_console.classList.add('fupi_trackable');
        show_panel( 'fupi_tracking_active' );
    } else {
        tracking_console.classList.add('fupi_not_trackable');
        show_panel( 'fupi_console_home' );
    }

    window.requestAnimationFrame( () => tracking_console.classList.add( 'fupi_panels_ready' ) );

    FP.findAll( '[data-fupi-panel]', tracking_console ).forEach( btn => {
        if ( btn.id === 'fupi_debug_modes_toggle' ) return;
        btn.addEventListener( 'click', () => show_panel( btn.dataset.fupiPanel, btn.classList.contains( 'fupi_panel_back' ) ) );
    } );

    // ACTION CONFIRMATIONS

    FP.findAll( '[data-fupi-confirmation]', tracking_console ).forEach( btn => {
        btn.addEventListener( 'click', () => {
            let confirmation = FP.findID( btn.dataset.fupiConfirmation );

            if ( ! confirmation ) return;

            btn.classList.add( 'fupi_hidden' );
            btn.setAttribute( 'aria-expanded', 'true' );
            confirmation.classList.remove( 'fupi_hidden' );

            let notice = confirmation.querySelector( '.fupi_console_notice' );
            if ( notice ) notice.focus( { preventScroll: true } );
        } );
    } );

    // CURRENT-PAGE HTML SCANNER

    let scan_btn = FP.findID('fupi_start_scan');

    if (
        ! is_trackable &&
        scan_btn &&
        FP.findID('fupi_scanner_ignore_safe') &&
        FP.findID('fupi_scanner_keep_version_ids') &&
        FP.findID('fupi_scanner_status') &&
        FP.findID('fupi_scanner_error') &&
        FP.findID('fupi_scanner_downloads') &&
        FP.findID('fupi_blocked_report_box') &&
        FP.findID('fupi_download_blocked_report') &&
        FP.findID('fupi_download_candidates_report') &&
        FP.findID('fupi_scanner_overlay')
    ) {
        let ignore_safe_field = FP.findID('fupi_scanner_ignore_safe'),
            keep_version_ids_field = FP.findID('fupi_scanner_keep_version_ids'),
            scanner_status = FP.findID('fupi_scanner_status'),
            scanner_error = FP.findID('fupi_scanner_error'),
            scanner_downloads = FP.findID('fupi_scanner_downloads'),
            blocked_report_box = FP.findID('fupi_blocked_report_box'),
            blocked_report_btn = FP.findID('fupi_download_blocked_report'),
            candidates_report_btn = FP.findID('fupi_download_candidates_report'),
            scanner_overlay = FP.findID('fupi_scanner_overlay'),
            scanner_url = scan_btn.dataset.scannerUrl,
            latest_result = null,
            scan_number = 0;

		if ( scanner_overlay.parentElement !== document.body ) document.body.appendChild( scanner_overlay );

        function load_scanner(){
            if ( window.FupiHtmlScanner ) return Promise.resolve( window.FupiHtmlScanner );

            return new Promise( ( resolve, reject ) => {
                FP.getScript(
                    scanner_url,
                    () => window.FupiHtmlScanner ? resolve( window.FupiHtmlScanner ) : reject( new Error( 'The scanner loaded without registering its interface.' ) ),
                    {
                        'data-fp-runtime': 'scanner',
                        'data-no-defer': '1',
                        'data-no-optimize': '1',
                    },
                    'script',
                    false,
                    () => reject( new Error( 'The scanner file could not be loaded.' ) )
                );
            } );
        }

        async function load_metadata( signal ){
            let request_data = new FormData();
            request_data.append( 'action', 'fupi_get_scanner_metadata' );
            request_data.append( 'security', scan_btn.dataset.security );

            let response = await fetch( fp.main.ajax_url, {
                    method: 'POST',
                    body: request_data,
                    credentials: 'same-origin',
                    signal,
                } ),
                payload;

            try {
                payload = await response.json();
            } catch ( error ) {
                throw new Error( 'The scanner metadata response was invalid.', { cause: error } );
            }

            if ( ! response.ok || ! payload.success ) {
                throw new Error( payload?.data?.message || 'The scanner metadata request failed.' );
            }

            return payload.data;
        }

        function set_downloads_available( available ){
            blocked_report_btn.disabled = ! available;
            candidates_report_btn.disabled = ! available;
            scanner_downloads.classList.toggle( 'fupi_hidden', ! available );
        }

		function get_page_bottom(){
			let root = document.documentElement,
				viewport_height = window.innerHeight || root.clientHeight;

			return Math.max( root.scrollHeight, document.body.scrollHeight ) - viewport_height;
		}

		function scroll_to_page_bottom( duration ){
			let start_position = window.scrollY || window.pageYOffset || 0,
				start_time = null;

			return new Promise( resolve => {
				function scroll_step( timestamp ){
					if ( start_time === null ) start_time = timestamp;

					let progress = Math.min( ( timestamp - start_time ) / duration, 1 ),
						eased_progress = progress < 0.5 ? 2 * progress * progress : 1 - Math.pow( -2 * progress + 2, 2 ) / 2,
						page_bottom = Math.max( 0, get_page_bottom() );

					window.scrollTo( 0, start_position + ( page_bottom - start_position ) * eased_progress );

					if ( progress < 1 ) {
						window.requestAnimationFrame( scroll_step );
					} else {
						window.scrollTo( 0, Math.max( 0, get_page_bottom() ) );
						resolve();
					}
				}

				window.requestAnimationFrame( scroll_step );
			} );
		}

		function wait( duration ){
			return new Promise( resolve => window.setTimeout( resolve, duration ) );
		}

        function start_busy_state(){
            let previous_focus = document.activeElement,
				console_was_busy = tracking_console.getAttribute( 'aria-busy' ),
                inert_states = [];

            Array.from( document.body.children ).forEach( element => {
                if ( element === scanner_overlay ) return;
                inert_states.push( [ element, element.inert ] );
                element.inert = true;
            } );
			tracking_console.setAttribute( 'aria-busy', 'true' );
            scanner_overlay.classList.remove('fupi_hidden');
            scanner_overlay.focus();

            return () => {
                inert_states.forEach( state => { state[0].inert = state[1]; } );
				if ( console_was_busy === null ) {
					tracking_console.removeAttribute( 'aria-busy' );
                } else {
					tracking_console.setAttribute( 'aria-busy', console_was_busy );
                }
                scanner_overlay.classList.add('fupi_hidden');
                if ( previous_focus && document.contains( previous_focus ) && ! previous_focus.closest( '[inert]' ) ) previous_focus.focus();
            };
        }

        function download_file( contents, filename, type = 'text/plain;charset=utf-8' ){
            let url = URL.createObjectURL( new Blob( [ contents ], { type } ) ),
                link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.className = 'fupi_hidden';
            document.body.appendChild( link );
            link.click();
            link.remove();
            window.setTimeout( () => URL.revokeObjectURL( url ), 1000 );
        }

        function download_filename( type ){
            return 'wp-full-picture-' + type + '-' + new Date().toISOString().replace( /[:.]/g, '-' ) + '.txt';
        }

        scan_btn.addEventListener( 'click', async () => {
            let this_scan = ++scan_number,
                cleanup_busy = start_busy_state(),
				controller,
				timeout,
				timeout_promise;

            latest_result = null;
            set_downloads_available( false );
            scanner_status.classList.add('fupi_hidden');
            scanner_error.classList.add('fupi_hidden');
            scan_btn.disabled = true;

            try {
				await scroll_to_page_bottom( 2000 );
				await wait( 200 );

				controller = new AbortController();
				timeout_promise = new Promise( ( resolve, reject ) => {
					timeout = window.setTimeout( () => {
						controller.abort();
						reject( new DOMException( 'The scan timed out.', 'AbortError' ) );
					}, 15000 );
				} );

                let scanner_promise = load_scanner(),
                    metadata_promise = load_metadata( controller.signal ),
					dependencies = await Promise.race( [
						Promise.all( [ scanner_promise, metadata_promise ] ),
						timeout_promise,
					] );

                if ( this_scan !== scan_number ) return;
                latest_result = dependencies[0].scan( document, dependencies[1], {
                    ignoreLikelySafe: ignore_safe_field.checked,
                    sanitizeVersionIds: ! keep_version_ids_field.checked,
					authorizationValues: [ scan_btn.dataset.security, fp.main.rest_nonce ],
                } );
                scanner_status.textContent = 'Scan results: ' + latest_result.candidates.length + ' candidates for blocking, ' + latest_result.already_blocked.length + ' already blocked, ' + latest_result.ignored.length + ' ignored.';
                scanner_status.classList.remove('fupi_hidden');
                blocked_report_box.classList.toggle( 'fupi_hidden', latest_result.already_blocked.length === 0 );
                set_downloads_available( true );
                show_panel( 'fupi_scan_results' );
            } catch ( error ) {
                if ( this_scan !== scan_number ) return;
                latest_result = null;
                scanner_error.textContent = error.name === 'AbortError' ? 'The scan timed out. Try again or reload the page.' : error.message || 'The page could not be scanned.';
                scanner_error.classList.remove('fupi_hidden');
            } finally {
                window.clearTimeout( timeout );
                cleanup_busy();
				window.scrollTo( 0, 0 );
                scan_btn.disabled = false;
            }
        } );

        blocked_report_btn.addEventListener( 'click', () => {
            if ( ! latest_result ) return;
            download_file( window.FupiHtmlScanner.buildBlockedReport( latest_result ), download_filename( 'blocked-elements' ) );
        } );

        candidates_report_btn.addEventListener( 'click', () => {
            if ( ! latest_result ) return;
            download_file( window.FupiHtmlScanner.buildCandidatesReport( latest_result ), download_filename( 'blocking-candidates' ).replace( /\.txt$/, '.json' ), 'application/json;charset=utf-8' );
        } );

        set_downloads_available( false );
    }

    start_test_btn.addEventListener('click', ()=>{
        FP.resetTrackingState();
        FP.setCookie('fp_track_me', '1', 0, 360 );
        window.location.reload();
    });

    reset_tracking_btn.addEventListener('click', ()=>{
        FP.resetTrackingState();
        window.location.reload();
    });

    // SHOW BANNER IF NOT ALREADY VISIBLE

    if ( show_banner_btn ) {
        show_banner_btn.addEventListener('click', ()=>{

            let consent_banner = FP.findID('fupi_cookie_notice');

            if ( consent_banner && ! consent_banner.classList.contains('fupi_fadeInUp') ) {

                // click hidden button
                let hidden_show_banner_btn = FP.findID( 'fupi_hidden_show_banner_btn' );
                hidden_show_banner_btn.click();
            }
        });
    }

    end_test_btn.addEventListener('click', ()=>{
        [ 'fp_track_me', 'fp_ga4_debug', 'fp_fbp_test_code', 'fp_fbp_test_code_2' ].forEach( FP.deleteCookie );
        window.location.reload();
    });

    // DIAGNOSTIC MODES

    let debug_toggle_btn = FP.findID('fupi_debug_modes_toggle'),
        debug_fields_wrap = FP.findID('fupi_debug_fields'),
        save_debug_btn = FP.findID('fupi_save_debug_modes'),
        diagnostic_fields_enabled = false;

    function init_debug_fields(){

        if ( diagnostic_fields_enabled ) return;
        diagnostic_fields_enabled = true;

        if ( fp.ga41 ) {
            let input = FP.findID('fupi_ga4_debug');
            input.checked = fp.main.ga4_debug;
        }
    
        if ( fp.fbp ) {
            let input = FP.findID('fupi_fbp_test_code');
            input.value = fp.main?.fbp_test_codes?.[0] || '';
        }
    
        if ( fp?.fbp?.pixel_id_2 ) {
            let input = FP.findID('fupi_fbp_test_code_2');
            input.value = fp.main?.fbp_test_codes?.[1] || '';
        }

        save_debug_btn.addEventListener('click', () => {

            let fields = FP.findAll( 'input', debug_fields_wrap );

            fields.forEach( ( field ) => {
                let value,
                    cookie_name = field.dataset.cookie_name;

                if ( field.type === 'checkbox' ) {
                    value = field.checked ? '1' : false;
                } else {
                    value = field.value || false;
                }

                if ( ! value ) {
                    FP.deleteCookie( cookie_name );
                } else {
                    FP.setCookie( cookie_name, value, 0, 360 );
                }
            } );
            window.location.reload();
        } );
    }

    if ( is_trackable && ( fp.ga41 || fp.fbp ) ) {
        debug_toggle_btn.classList.remove('fupi_hidden');
        debug_toggle_btn.addEventListener('click', () => {
            show_panel( debug_toggle_btn.dataset.fupiPanel );
            init_debug_fields();
        } );
    }

    FP.loaded('setup-console');
    
};

FP.load( 'setup-console', 'fupi_setup_console' );
