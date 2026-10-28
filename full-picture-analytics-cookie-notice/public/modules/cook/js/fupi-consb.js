// TO DO: remove waiting for CDB response if not in debug mode - atm. it unnecesssarily stops the page from reloading until it get the response

function fupi_consb() {

	'use strict';

	let notice = FP.findID('fupi_cookie_notice'),
		toggler = FP.findID('fupi_notice_toggler'),
		panel = {},
		setup_done = false,
		cdb_cookie = [];

	panel.welcome = FP.findID('fupi_welcome_panel');
	panel.settings = FP.findID('fupi_settings_panel');

	let	activePanel = false,
		priorFocus = false,
		focusableElements = [],
		firstTabStop = false,
		lastTabStop = false,
		a11y_buttons_ready = false;

	function setup_notice(){

		// hidden
		if ( fp.notice.mode == 'hide' ){
			
			actions_when_notif_is_hidden();
		
		// optin / optout
		} else if ( fp.notice.mode == 'optin' || fp.notice.mode == 'optout' ) {

			hide_popup_elements();
			show_popup_elements();
			add_events_to_notice_btns();

			if ( fpdata.cookies ) {
				show_toggler();
				show_notice_on_link_click(true); // true == set switches
			} else {
				set_default_switches();
				if ( fp.main.track_current_user ) {
					fp.notice.display_notice ? show_notice() : show_toggler();
				};
				show_notice_on_link_click(true);
			};

		// notify
		} else {

			show_popup_elements();
			transform_notice_into_infobox();

			if ( fp.main.track_current_user && ! fpdata.cookies ) {
				auto_agree_to_all_cookies();
				show_notice();
			};
			
			if ( FP.loadIframes ) FP.loadIframes(); // unblock iframes

			add_events_to_ok_button();
			show_notice_on_link_click();
		}

		setup_done = true;
	}

	// CDB

	// Get a formatted date for display in the proof
	function get_date() {

		const now = new Date();
    
		// Get year, month, and day
		const year = now.getFullYear();
		const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
		const day = String(now.getDate()).padStart(2, '0');
		
		// Get hours, minutes, and seconds
		const hours = String(now.getHours()).padStart(2, '0');
		const minutes = String(now.getMinutes()).padStart(2, '0');
		const seconds = String(now.getSeconds()).padStart(2, '0');
		
		// Format the date and time as YYYY-MM-DD HH:MM:SS
		const formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
		
		return formattedDateTime;
	}

	// Get timezone for display in the proof
	function get_timezone(){
		let timezone_int = ( new Date().getTimezoneOffset() / 60 ) * -1;
		return timezone_int >= 0 ? '+' + timezone_int : timezone_int + '';
	}

	function get_timezoneName(){
		return Intl.DateTimeFormat().resolvedOptions().timeZone;
	}

	function can_save_consents(){
		if ( ! fp?.proofrec?.save_consent ) return false;
		if ( ! fp?.proofrec?.save_all_consents && ( fpdata.is_robot || fpdata.activity.total == 0 ) ) return false;
		return true;
	}

	function is_valid_cdb_cookie_data( cookie_data ) {
		return Array.isArray( cookie_data ) &&
			typeof cookie_data[0] == 'string' &&
			cookie_data[0].length > 0 &&
			Array.isArray( cookie_data[1] ) &&
			cookie_data[1].every( consent_time =>
				Array.isArray( consent_time ) &&
				Number.isFinite( consent_time[0] ) &&
				typeof consent_time[1] == 'boolean'
			);
	}

	function save_consent_id_in_cookie() {

		// creates and saves cdb_id in a cookie
		let cdb_id_cookie = FP.readCookie( 'cdb_id' );
		
		fp.notice.consent_times = [[fp.notice.consent_time, false]];
		fp.notice.cdb_id_confirmed = false;

		// if is 1st consent
		if ( ! cdb_id_cookie ) {	
			
			fp.notice.cdb_id = crypto && crypto.randomUUID ? crypto.randomUUID() : FP.getRandomStr();

		// if is consecutive consent
		} else {

			// if prev consent was prior to 8.5 
			// ( checks if cdb_id_cookie is not a stringified array )
			if ( cdb_id_cookie[0] != '[' ) {
				fp.notice.cdb_id = cdb_id_cookie;

			// if ver 8.5+, we add another consent time
			} else {
				let saved_consent_data = FP.parseJSON( cdb_id_cookie, false );

				if ( is_valid_cdb_cookie_data( saved_consent_data ) ) {
					fp.notice.cdb_id = saved_consent_data[0];
					fp.notice.consent_times.push( ...saved_consent_data[1] );
				} else {
					fp.notice.cdb_id = crypto && crypto.randomUUID ? crypto.randomUUID() : FP.getRandomStr();
				}
			}
		}
		
		cdb_cookie = [ fp.notice.cdb_id, fp.notice.consent_times ];

		FP.setCookie( 'cdb_id', JSON.stringify( cdb_cookie ), 365 );
	};

	function mark_cdb_id_as_confirmed( server_response ){

		let saved = false;

		if ( typeof server_response == 'object' && server_response !== null ) {
			if ( server_response.CDB ) {
				let cdb_response = FP.parseJSON( server_response.CDB, false );
				saved = cdb_response?.status == 'success';
			}

			if ( Array.isArray( server_response.results ) ) {
				let email_result = server_response.results.find( result => result.event_id == 'proofrec_db_save' );
				if ( email_result?.success && email_result?.saved ) saved = true;
			}
		}

		if ( saved ) {
			fp.notice.consent_times[0][1] = true;
			FP.setCookie( 'cdb_id', JSON.stringify( [fp.notice.cdb_id, fp.notice.consent_times ] ), 365 );
			fp.notice.cdb_id_confirmed = true;
			if ( fp.main.debug ) console.log('[FP] Consent proof saved');
		}

		return saved;
	}

	function get_consent_result( server_response ) {
		if ( ! server_response || ! Array.isArray( server_response.results ) ) return false;
		return server_response.results.find( result => result.event_id == 'proofrec_db_save' || result.event_id == 'cdb' ) || false;
	}

	function save_preferences_in_cdb( prev_consents, retry_on_failure = false ) {

		if ( ! can_save_consents() ) {
			return Promise.resolve({
				success: true,
				skipped: true,
				saved: false,
				retryable: false,
			});
		}

		if ( fp.main.debug ) console.log('[FP] Saving consent');

		fp.notice.consent_time = Date.now();
		fp.notice.cdb_waiting_for_response = true;

		save_consent_id_in_cookie();

		let payload = { ...fpdata.cookies };
		
		payload['consentBannerMode'] = fp.notice.mode;
		payload['previousConsents'] = prev_consents;
		payload['geolocatedCountry'] = fp.main.geo && fpdata.country ? fpdata.country : 'Geolocation disabled';
		payload['cdbID'] = fp.notice.cdb_id;
		payload['timestamp'] = fp.notice.consent_time;

		if ( cdb_cookie.length > 0 ) {
			
			let prev_cons_timestamps = [];
			
			cdb_cookie[1].forEach( ( timestamp_arr, i ) => {
				prev_cons_timestamps.push( timestamp_arr[0] );
			});

			if ( prev_cons_timestamps.length > 0  ) payload['previousConsentsTimestamps'] = prev_cons_timestamps;
		}

		// if ( prev_consents['statistics'] || prev_consents['marketing'] || prev_consents['personalisation'] ) payload['cookiesBeforeConsent'] = listCookies();

		let event_type = 'cdb',
			event_id = false;

		if ( fp?.proofrec?.storage_location == 'email' ) {
			event_type = 'process';
			event_id = 'proofrec_db_save';
		}

		let post_proof = () => FP.postToServer(
			[[ event_type, event_id, payload ]],
			{
				keepalive: true,
				expectResponse: true,
			}
		);

		return post_proof()
			.then( server_response => {
				let result = get_consent_result( server_response );

				if (
					retry_on_failure &&
					fp?.proofrec?.storage_location == 'email' &&
					( ! result || ( ! result.success && result.retryable ) )
				) {
					return new Promise( resolve => {
						setTimeout( () => resolve( post_proof() ), 400 );
					});
				}

				return server_response;
			})
			.then( server_response => {
				mark_cdb_id_as_confirmed( server_response );
				return server_response;
			});
	}

	function save_is_confirmed( server_response ) {
		if ( server_response?.skipped ) return true;
		return fp.notice.cdb_id_confirmed;
	}

	function reload_after_consent_save( save_promise ) {
		let confirmed_promise = save_promise.then( server_response => {
			if ( save_is_confirmed( server_response ) ) return true;
			return new Promise( () => {} );
		}).catch( error => {
			if ( fp.main.debug ) console.error('[FP] Consent proof request failed', error);
			return new Promise( () => {} );
		});

		Promise.race( [
			confirmed_promise,
			new Promise( resolve => setTimeout( resolve, 4000 ) ),
		] ).then( () => {
			if ( fp.main.debug ) console.log('[FP] Reloading page after consent update');
			location.reload();
		});
	}

	function prevent_duplicate_consent_submission() {
		[
			FP.findID('fupi_decline_cookies_btn'),
			FP.findID('fupi_agree_to_selected_cookies_btn'),
		].forEach( button => {
			if ( button ) button.disabled = true;
		});
	}

	// SETUP CONSENT BANNER

	// fire on load
	if ( fp.ready ) setup_notice();
	
	// fire after each fp_load_event (can be also triggered by clicking "agree" buttons below) 
	document.addEventListener( 'fp_load_scripts', e=>{
		if ( ! setup_done && fp.ready ) setup_notice();
		// hide the notice if the user agreed or declined cookies in a different tab
		if ( fpdata.cookies && notice.classList.contains('fupi_fadeInUp') ) {
			show_toggler();
			hide_notice_wrapper();
			remove_blur();
			remove_scroll_lock();
		}
	} )

	function hide_popup_elements(){
		if ( fp.notice.hidden.length > 0 ) {
			fp.notice.hidden.forEach( el=> {
				switch ( el ) {
				
					case 'settings_btn':
						FP.findID('fupi_cookie_settings_btn').style.display = 'none';
					break;

					case 'decline_btn':
						if ( ! fp.notice.auto_mode ) FP.findID('fupi_decline_cookies_btn').style.display = 'none';
					break;

					case 'stats': {
						let stats_sect = FP.findID('fupi_stats_section');
						if (stats_sect) stats_sect.style.display = 'none';
					}
					break;

					case 'market': {
						let market_sect = FP.findID('fupi_market_section');
						if ( market_sect ) market_sect.style.display = 'none';
					}
					break;

					case 'pers':{
						let pers_sect = FP.findID('fupi_pers_section');
						if ( pers_sect ) pers_sect.style.display = 'none';
					}
					break;
				}
			} )
		}
	}

	function show_popup_elements(){
		if ( fp.notice.shown.length > 0 ) {
			fp.notice.shown.forEach( el=> {
				switch ( el ) {
					case 'powered_by':
						FP.findAll('.fupi_poweredBy').forEach( el => el.style.display = 'block' );
					break;
					case 'stats_only_btn':
						if ( fp.notice.mode !== 'notify' ) FP.findID('fupi_stats_only_btn').style.display = 'block';
					break;
				}
			} )
		}
	}
	
	function transform_notice_into_infobox(){
		var html_el = document.getElementsByTagName( 'html' )[0];
		html_el.classList.add('fupi_infobox'); // removes blur and doesn't let scroll locking
		notice.classList.add('fupi_notice_infobox');
	}

	function remove_blur() {
		setTimeout( function(){
			var html_el = document.getElementsByTagName( 'html' )[0];
			if( html_el.classList.contains('fupi_blur') ) {
				html_el.classList.remove('fupi_blur');
				html_el.classList.add('fupi_blur_out');
			}
		}, 300 );
	}

	function lock_scroll() {
		if ( fp.notice.scroll_lock ) {
			var html_el = document.getElementsByTagName( 'html' )[0];
			if ( ! html_el.classList.contains('fupi_infobox') ) {
				html_el.classList.add('fupi_scroll_lock');
			}
		}
	}

	function remove_scroll_lock(){
		if ( fp.notice.scroll_lock ) {
			var html_el = document.getElementsByTagName( 'html' )[0];
			html_el.classList.remove('fupi_scroll_lock');
		}
	}

	function show_notice_on_link_click( set_switches = false ){

		let show_notice_links = FP.findAll( fp.notice.toggle_selector );

		show_notice_links.forEach(function(link) {
			
			// Check if we've already attached a listener to this element
			if ( link.dataset.fupiListenerAttached ) {
				return;
			}
			
			link.addEventListener('click', function(e) {
				
				e.preventDefault();
				
				hide_toggler();
				notice.classList.add( 'fupi_changing_preferences' ); // hides "return button" but shows the close button
				
				priorFocus = document.activeElement;
				
				if ( set_switches ) set_switches_state();

				add_consent_id_to_banner();
				
				show_notice_wrapper();
				
				if ( fp.notice.mode == 'notify' ){
					show_panel('welcome');
				} else {
					panel.settings ? show_panel('settings') : show_panel('welcome');
				}

				if ( ! a11y_buttons_ready ) a11y_buttons( true );
			});
			
			// Mark this element as having a listener attached
			link.dataset.fupiListenerAttached = 'true';
		});
	}

	function add_consent_id_to_banner(){

		let cookie_val =  FP.readCookie( 'cdb_id' ),
			cdb_id = false,
			consent_info_boxes = FP.findAll('.fupi_consent_info'); // one on each CB panel

		if ( ! ( fp?.proofrec?.save_consent && cookie_val && consent_info_boxes.length > 0 && fpdata.cookies && fpdata.cookies.time && fpdata.cookies.timezone ) ) return;

		// if consent was prior to 8.5 
		// ( checks if cookie_val is not a STRINGified array )
		if ( cookie_val[0] != '[' ) {
			
			cdb_id = cookie_val;

			// fill in the HTML box
			consent_info_boxes.forEach( box => {
			
				let id_el = FP.findFirst( '.fupi_consent_id', box );
				if ( id_el ) id_el.innerHTML = cdb_id;
	
				let date_el = FP.findFirst( '.fupi_consent_date', box );
				if ( date_el ) date_el.innerHTML = fpdata.cookies.time + ' (' + fpdata.cookies.timezone + ')';
	
				box.classList.remove('fupi_hidden');
			});
			
		// if ver 8.5+, we add another consent time
		} else {
			
			cookie_val = FP.parseJSON( cookie_val, false );
			if ( ! is_valid_cdb_cookie_data( cookie_val ) || ! cookie_val[1][0] ) return;
			cdb_id = cookie_val[0];
			
			let cons_times_a = cookie_val[1];

			// fill in the HTML box

			consent_info_boxes.forEach( box => {
			
				let id_el = FP.findFirst( '.fupi_consent_id', box ),
					is_consent_saved = cons_times_a[0][1],
					consentID = cdb_id + '_' + cons_times_a[0][0];

				if ( id_el ) {

					// maybe add a link to ConsentsDB
					id_el.innerHTML = fp?.proofrec?.consent_access && fp?.proofrec?.storage_location == 'cdb' && is_consent_saved ? '<a href="https://consentsdb.com/public/consent/' + consentID + '" target="_blank">' + consentID + '</a>' : consentID;
				}
	
				let date_el = FP.findFirst( '.fupi_consent_date', box );
				if ( date_el ) date_el.innerHTML = fpdata.cookies.time + ' (' + fpdata.cookies.timezone + ')';
	
				box.classList.remove('fupi_hidden');
			});
		}
	}

	function set_default_switches(){

		if ( fp.notice.preselected_switches.length > 0 && ( fp.notice.mode == 'optout' || fp.notice.optin_switches ) ){
			
			if ( fp.notice.preselected_switches.includes('stats') ){
				let stats_switch = FP.findFirst('#fupi_stats_section .fupi_switch');
				if ( stats_switch ) stats_switch.click();
			};

			if ( fp.notice.preselected_switches.includes('market') ){
				let market_switch = FP.findFirst('#fupi_market_section .fupi_switch');
				if ( market_switch ) market_switch.click();
			};

			if ( fp.notice.preselected_switches.includes('pers') ){
				let pers_switch = FP.findFirst('#fupi_pers_section .fupi_switch');
				if ( pers_switch ) pers_switch.click();
			};
		}
	}

	function set_switches_state() {
		var switches = FP.findAll(".fupi_switch input");

		if(switches.length > 0) {
			switches.forEach(function(the_switch) {
				if( fpdata.cookies && fpdata.cookies[the_switch.value]) the_switch.checked = true;
			});
		}
	}

	function add_events_to_notice_btns() {

		let accept_all_btn = FP.findID('fupi_agree_to_all_cookies_btn'),
			decline_btn = FP.findID('fupi_decline_cookies_btn'),
			cookie_settings_btn = FP.findID('fupi_cookie_settings_btn'),
			accept_stats_btn = FP.findID('fupi_stats_only_btn'),
			accept_chosen_btn = FP.findID('fupi_agree_to_selected_cookies_btn'),
			return_btn = FP.findID('fupi_return_btn'),
			close_banner_btns = FP.findAll('.fupi_close_banner_btn'),
			switches = FP.findAll('.fupi_switch');

		if ( accept_all_btn ) {
			accept_all_btn.addEventListener('click', function(){

				// hide all
				hide_panel('welcome');
				show_toggler();
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();

				// save cookies and fire events
				if ( fp.main.track_current_user ) accept_all_cookies_and_fire_custom_event();
				if ( FP.loadIframes ) FP.loadIframes(); // unblock iframes
			});
		}

		if ( accept_stats_btn ) {
			accept_stats_btn.addEventListener('click', function(){

				// hide all
				hide_panel('welcome');
				show_toggler();
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();

				// save cookies and fire events
				if ( fp.main.track_current_user ) accept_stats_cookies_and_fire_custom_event();
				if ( FP.loadIframes ) FP.loadIframes(); // unblock iframes
			})
		}

		if ( decline_btn ) {
			decline_btn.addEventListener('click', function(){
				if ( decline_btn.disabled ) return;
				prevent_duplicate_consent_submission();

				// hide all
				hide_panel('welcome');
				show_toggler();
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();

				// save cookies and fire events
				decline_all_cookies();
			});
		}

		if ( accept_chosen_btn ) {
			accept_chosen_btn.addEventListener('click', function(){
				if ( accept_chosen_btn.disabled ) return;
				prevent_duplicate_consent_submission();

				// hide all
				hide_panel('settings');
				show_toggler();
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();

				// save cookies and fire events
				if ( fp.main.track_current_user ) accept_chosen_cookies_and_fire_custom_events();
				if ( FP.loadIframes ) FP.loadIframes();
			});
		}

		if ( cookie_settings_btn ) {
			cookie_settings_btn.addEventListener('click', function() {
				hide_panel('welcome');
				show_panel('settings');
			});
		}

		if ( return_btn ) {
			return_btn.addEventListener('click', function() {
				hide_panel('settings');
				show_panel('welcome');
			});
		}
		
		close_banner_btns.forEach( btn => {
			btn.addEventListener('click', function() {
				hide_panel('welcome');
				hide_panel('settings');
				show_toggler();
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();
			});
		}) 
		
		switches.forEach(function(the_switch) {
			the_switch.addEventListener('click', function(e) {
				toggle_switch(e.target);
			}, false);
		});
		
	}

	function get_prev_consents(){
		return {
			'statistics' : fpdata?.cookies?.stats,
			'marketing' : fpdata?.cookies?.marketing,
			'personalisation' : fpdata?.cookies?.personalisation
		}
	}

	function auto_agree_to_all_cookies(){ // auto triggered when the notice is in "notify" mode
		
		fpdata.cookies = {
			'stats': true,
			'personalisation': true,
			'marketing': true,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone(),
			'timezoneName' : get_timezoneName(),
		};

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
	}

	function add_events_to_ok_button(){ // triggered when consent banner only notifies

		let agree_btn = FP.findID('fupi_agree_to_all_cookies_btn');

		if( agree_btn ) {
			agree_btn.addEventListener('click', function(){

				hide_panel('welcome');
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();
			});
		}
	}

	function actions_when_notif_is_hidden(){

		// remove blur & scroll lock

		var html_el = document.getElementsByTagName( 'html' )[0];
		
		html_el.classList.remove('fupi_blur');	
		
		if ( fp.notice.scroll_lock ) {
			html_el.classList.remove('fupi_scroll_lock');
			document.body.style.overflowY = 'auto';
		}

		// save cookies

		if ( ! fpdata.cookies ) {

			fpdata.cookies = {
				'stats': true,
				'personalisation': true,
				'marketing': true,
				'disabled': false,
				'pp_pub' : fp.notice.priv_policy_update,
				'tools' : fp.tools,
				'time' : get_date(),
				'timezone' : get_timezone(),
				'timezoneName' : get_timezoneName(),
			};
	
			FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
			FP.updatePrivacyChoices( false );
			FP.sendEvt( 'fp_load_scripts' );
		}

		if ( FP.loadIframes ) FP.loadIframes(); // unblock iframes
	}

	// ACCESSIBLE SWITCH
	// https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Roles/Switch_role

	function toggle_switch(target) {
		if(target.getAttribute("aria-checked") == "true") {
			target.setAttribute("aria-checked", "false");
		} else {
			target.setAttribute("aria-checked", "true");
		}
	}

	function accept_all_cookies_and_fire_custom_event() {
		
		let prev_consents = get_prev_consents();

		// this is the default when the settings panel is hidden (we set everything to true)
		fpdata.cookies = {
			'stats': true,
			'personalisation': true,
			'marketing': true,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone(),
			'timezoneName' : get_timezoneName(),
		};

		if ( panel.settings ){ // when the settings panel is visible, we set to true only those cookies which settings are not hidden 

			let stats = !!FP.findID('fupi_stats_agree'),
				perso = !!FP.findID('fupi_pers_agree'),
				marke = !!FP.findID('fupi_marketing_agree');

			fpdata.cookies = {
				'stats': stats,
				'personalisation': perso,
				'marketing': marke,
				'disabled': false,
				'pp_pub' : fp.notice.priv_policy_update,
				'tools' : fp.tools,
				'time' : get_date(),
				'timezone' : get_timezone(),
				'timezoneName' : get_timezoneName(),
			};
		}

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );

		save_preferences_in_cdb( prev_consents );
		FP.updatePrivacyChoices( false );
		FP.sendEvt( 'fp_load_scripts' );
	}

	function accept_stats_cookies_and_fire_custom_event(){

		let prev_consents = get_prev_consents();

		// this is the default when the settings panel is hidden (we set everything to true)
		fpdata.cookies = {
			'stats': true,
			'personalisation': false,
			'marketing': false,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone(),
			'timezoneName' : get_timezoneName(),
		};

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
		save_preferences_in_cdb( prev_consents );
		FP.updatePrivacyChoices( false );
		FP.sendEvt( 'fp_load_scripts' );
	}

	function accept_chosen_cookies_and_fire_custom_events() {

		prevent_duplicate_consent_submission();

		let stats_checkbox = FP.findID('fupi_stats_agree'),
			perso_checkbox = FP.findID('fupi_pers_agree'),
			marke_checkbox = FP.findID('fupi_marketing_agree'),
			prev_consents = get_prev_consents(),
			made_choice_previously = !! fpdata.cookies;

		fpdata.cookies = {
			'stats': stats_checkbox ? stats_checkbox.checked : false,
			'personalisation': perso_checkbox ? perso_checkbox.checked : false,
			'marketing': marke_checkbox ? marke_checkbox.checked : false,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone(),
			'timezoneName' : get_timezoneName(),
		};

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
		let save_promise = save_preferences_in_cdb( prev_consents, true );
		FP.updatePrivacyChoices( false );
		
		if ( made_choice_previously ) {
			reload_after_consent_save( save_promise );
		} else {
			if ( fp.notice.mode == 'optout' ) {
				reload_after_consent_save( save_promise );
			} else {
				FP.sendEvt( 'fp_load_scripts' );
			}
		}
	}

	function decline_all_cookies() {

		if ( ! fp.main.track_current_user ) return;

		prevent_duplicate_consent_submission();

		let prev_consents = get_prev_consents(),
			made_choice_previously = !! fpdata.cookies;
		
		fpdata.cookies = {
			'stats': false,
			'personalisation': false,
			'marketing': false,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone(),
			'timezoneName' : get_timezoneName(),
		};

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
		let save_promise = save_preferences_in_cdb( prev_consents, true );
		FP.updatePrivacyChoices( false );
		
		if ( made_choice_previously ) {
			reload_after_consent_save( save_promise );
		} else {
			if ( fp.notice.mode == 'optout' ) {
				reload_after_consent_save( save_promise );
			} else {
				FP.sendEvt( 'fp_load_scripts' );
			}
		}
	}

	function update_buttons_focus(){
		focusableElements = FP.findAll('a[href]:not(.fupi_poweredBy_link), input:not([disabled]), button:not([disabled]), [tabindex="0"], [contenteditable]', panel[activePanel] ),
		firstTabStop = focusableElements[0];
		lastTabStop = focusableElements[focusableElements.length - 1];
		// focus first element
		firstTabStop.focus();
	}

	function show_panel(type) {

		if ( panel[type] ) {

			activePanel = type;

			// focus on the first button
			FP.findFirst('button', panel[type]).focus();

			panel[type].classList.remove('fupi_fadeOutDown');
			panel[type].classList.remove('fupi_hidden');
			panel[type].classList.add('fupi_animated');
			panel[type].classList.add('fupi_fadeInUp');

			update_buttons_focus( panel[type] );
		}
	}
	
	function hide_panel( type ) {

		if ( ! panel[type] ) return;

		panel[type].classList.remove('fupi_fadeInUp');
		panel[type].classList.add('fupi_animated');
		panel[type].classList.add('fupi_fadeOutDown');
	}
	
	function show_toggler(){
		if ( toggler && fp.notice.mode !== 'hidden' && fp.notice.mode !== 'notify' ) {
			toggler.setAttribute("style","");
			toggler.classList.add( 'fupi_active', 'fupi_fadeInUp' );
			toggler.classList.remove( 'fupi_fadeOutDown' );
		}
	}

	function hide_toggler(){
		if ( toggler && toggler.classList.contains('fupi_active') ) {
			toggler.classList.remove( 'fupi_fadeInUp');
			toggler.classList.add( 'fupi_fadeOutDown' );
		}
	}

	function show_notice_wrapper() {
		notice.setAttribute("style","");
		notice.classList.remove('fupi_fadeOutDown');
		notice.classList.remove('fupi_hidden');
		notice.classList.add('fupi_fadeInUp');
		notice.classList.add('fupi_animated');
	}

	function hide_notice_wrapper() {
		notice.classList.remove('fupi_fadeInUp');
		notice.classList.add('fupi_fadeOutDown');
		priorFocus.focus();
	}

	function a11y_buttons( opened_with_link ){ // accessibility buttons

		a11y_buttons_ready = true;

		document.addEventListener("keydown", e => {

			// Escape key returns to welcome panel or closes the modal
			if ( e.keyCode === 27 || e.code === 27 || e.key === 27 ) {
				if ( activePanel == 'welcome' ){
					hide_panel('welcome');
					show_toggler();
					hide_notice_wrapper();
					remove_blur();
					remove_scroll_lock();
					if ( fp.notice.mode != 'notify' && ! opened_with_link ) {
						decline_all_cookies();
					}
				} else if ( activePanel == 'settings' ) {
					hide_panel('settings');
					show_panel('welcome');
				}
			}

			// Keeps focus within panels when tab is clicked
			if ( activePanel && ( e.keyCode === 9 || e.code === 9 || e.key === 9 ) ) {
				// if shift is clicked with tab (reverse tabbing)
				if (e.shiftKey) {
					// do not go any further back if we are on the first element
					if ( firstTabStop && document.activeElement === firstTabStop) e.preventDefault();

				// if next element is to be focused
				} else {
					if ( lastTabStop && document.activeElement === lastTabStop) {
						e.preventDefault();
						lastTabStop.focus();
					}
		        }
			}

		});
	}
	
	function show_notice() {
		
		priorFocus = document.activeElement;
		
		lock_scroll();
		show_notice_wrapper();
		show_panel('welcome');

		if ( ! a11y_buttons_ready ) a11y_buttons();
	}

	FP.loaded('cons-banner');

};

if ( fp.main.is_bricks_builder ) {
	FP.load('cons-banner', 'fupi_consb', ['footer-helpers']);
} else {
	FP.load('cons-banner', 'fupi_consb', ['footer-helpers', 'iframes-blocker']);
}
