"use strict";

(function() {

	'use strict';

	let notice = FP.findID('fupi_cookie_notice'),
		panel = {},
		setup_done = false;

	panel.welcome = FP.findID('fupi_welcome_panel');
	panel.settings = FP.findID('fupi_settings_panel');

	let	activePanel = false,
		priorFocus = false,
		focusableElements = [],
		firstTabStop = false,
		lastTabStop = false,
		a11y_buttons_ready = false;

	function listCookies() {

		let theCookies = document.cookie.split(';'),
			cookieNames = [];

		for (var i = 0; i < theCookies.length; i++) {
			let cookieName = theCookies[i].split('=')[0].trim();
			cookieNames.push(cookieName);
		}
		return cookieNames;
	};

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
				if ( fp.vars.track_current_user ) show_notice();
				show_notice_on_link_click(true);
			};

		// notify
		} else {

			show_popup_elements();
			transform_notice_into_infobox();

			if ( fp.vars.track_current_user && ! fpdata.cookies ) {
				auto_agree_to_all_cookies();
				show_notice();
			};
			
			if ( FP.manageIframes ) FP.manageIframes(); // unblock iframes

			add_events_to_ok_button();
			show_notice_on_link_click();
		}

		setup_done = true;
	}

	// CDB

	function set_cdb_id() {
		let cdb_id = FP.readCookie( 'cdb_id' );

		if ( ! cdb_id ) cdb_id = crypto && crypto.randomUUID ? crypto.randomUUID() : FP.getRandomStr();
		
		fp.vars.cdb_id = cdb_id;
		FP.setCookie( 'cdb_id', cdb_id, 365 );
	};

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

		// This gives date from "0" timezone
		// let date_o = new Date(),
		// isoString = date_o.toISOString(); // 2022-09-28T15:42:51.000Z

		// return isoString.replaceAll('T', ' ').split('.')[0]; // 2022-09-28 15:42:51
	}

	function get_timezone(){
		let timezone_int = ( new Date().getTimezoneOffset() / 60 ) * -1;
		return timezone_int >= 0 ? '+' + timezone_int : timezone_int + '';
	}

	function save_preferences_in_cdb( prev_consents ){

		if ( ! fp.notice.save_in_cdb ) return;
		if ( ! fp.notice.save_all_consents && ( fpdata.is_robot || fpdata.activity.total == 0 ) ) return;

		set_cdb_id()
		
		let newCookieVal = fpdata.cookies;

		delete newCookieVal.disabled;
		delete newCookieVal.tools;

		let visit_info = newCookieVal;
		
		visit_info['consentBannerMode'] = fp.notice.mode;
		visit_info['previousConsents'] = prev_consents;
		visit_info['geolocatedCountry'] = fp.geo && fpdata.country ? fpdata.country : 'Geolocation disabled';
		visit_info['cdbID'] = fp.vars.cdb_id;
		// visit_info['timezone'] = timezome_str;
		// visit_info['time'] = get_date();

		if ( ! prev_consents.cookies ) visit_info['cookiesBeforeConsent'] = listCookies();

		// console.log( 'visit_info', visit_info );
		FP.postToServer( 'cdb', false, visit_info );
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

	// FUNCTIONS

	function update_tools_consents(){
		
		let permissions = {};
		permissions['analytics_storage'] = fpdata.cookies.stats ? 'granted' : 'denied';
		permissions['personalization_storage'] = fpdata.cookies.personalisation ? 'granted' : 'denied';
		permissions['ad_storage'] = fpdata.cookies.marketing ? 'granted' : 'denied';
		permissions['ad_user_data'] = fpdata.cookies.marketing ? 'granted' : 'denied',
		permissions['ad_personalization'] = fpdata.cookies.marketing ? 'granted' : 'denied',

		// Google
		window.gtag( 'consent', 'update', permissions );
		if ( fp.gtm && fp.gtm.datalayer == 'fupi_dataLayer' && window.fupi_gtm_gtag ) window.fupi_gtm_gtag( 'consent', 'update', permissions );

		// MS
		if ( window.clarity ) {
			fpdata.cookies.stats ? window.clarity('consent') : window.clarity('consent', false);
		}

		if ( fp.vars.debug ) console.log('[FP] Consents updated', permissions);
	}	

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

					case 'stats':
						let stats_sect = FP.findID('fupi_stats_section');
						if (stats_sect) stats_sect.style.display = 'none';
					break;

					case 'market':
						let market_sect = FP.findID('fupi_market_section');
						if ( market_sect ) market_sect.style.display = 'none';
					break;

					case 'pers':
						let pers_sect = FP.findID('fupi_pers_section');
						if ( pers_sect ) pers_sect.style.display = 'none';
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
				document.body.style.overflowY = 'hidden';
			}
		}
	}

	function remove_scroll_lock(){
		if ( fp.notice.scroll_lock ) {
			var html_el = document.getElementsByTagName( 'html' )[0];
			html_el.classList.remove('fupi_scroll_lock');
			document.body.style.overflowY = 'auto';
		}
	}

	function show_notice_on_link_click( set_switches = false ){

		let show_notice_links = FP.findAll( fp.notice.toggle_selector );

		show_notice_links.forEach(function(link) {
			
			link.addEventListener('click', function(e) {
				
				e.preventDefault();
				
				hide_toggler();
				notice.classList.add( 'fupi_changing_preferences' ); // hides "return button" but shows the close button
				
				priorFocus = document.activeElement;
				
				if ( set_switches ) set_switches_state();

				add_cdb_data();
				
				show_notice_wrapper();
				
				if ( fp.notice.mode == 'notify' ){
					show_panel('welcome');
				} else {
					panel.settings ? show_panel('settings') : show_panel('welcome');
				}

				if ( ! a11y_buttons_ready ) a11y_buttons( true );
			});
		});
	}

	function add_cdb_data(){

		let cdb_id =  FP.readCookie( 'cdb_id' ),
			inactive_fupi_cdb_info_boxes = FP.findAll('.fupi_cdb_info.fupi_hidden');

		if ( inactive_fupi_cdb_info_boxes.length > 0 && fp.notice.save_in_cdb && fpdata.cookies && fpdata.cookies.time && cdb_id && fpdata.cookies.timezone ) {
			inactive_fupi_cdb_info_boxes.forEach( box => {
				
				let id_el = FP.findFirst( '.fupi_cdb_info_id', box );
				if ( id_el ) id_el.innerHTML = cdb_id;

				let date_el = FP.findFirst( '.fupi_cdb_info_date', box );
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
				if ( fp.vars.track_current_user ) accept_all_cookies_and_fire_custom_event();
				if ( FP.manageIframes ) FP.manageIframes(); // unblock iframes
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
				if ( fp.vars.track_current_user ) accept_stats_cookies_and_fire_custom_event();
				if ( FP.manageIframes ) FP.manageIframes(); // unblock iframes
			})
		}

		if ( decline_btn ) {
			decline_btn.addEventListener('click', function(){

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

				// hide all
				hide_panel('settings');
				hide_notice_wrapper();
				remove_blur();
				remove_scroll_lock();

				// save cookies and fire events
				if ( fp.vars.track_current_user ) accept_chosen_cookies_and_fire_custom_events();
				if ( FP.manageIframes ) FP.manageIframes();
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

	function auto_agree_to_all_cookies(){ // auto triggered when the notice is in "notify" mode
		
		fpdata.cookies = {
			'stats': true,
			'personalisation': true,
			'marketing': true,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone()
		};

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 730 );
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
				'timezone' : get_timezone()
			};
	
			FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 730 );
			update_tools_consents();
		}

		if ( FP.manageIframes ) FP.manageIframes(); // unblock iframes
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
		
		let prev_consents = fpdata.cookies;

		// this is the default when the settings panel is hidden (we set everything to true)
		fpdata.cookies = {
			'stats': true,
			'personalisation': true,
			'marketing': true,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone()
		},
		dataLayer = window.dataLayer || window.fupi_dataLayer;

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
				'timezone' : get_timezone()
			};
		}

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );

		save_preferences_in_cdb( prev_consents );

		FP.update_session_data();

		FP.sendEvt( 'fp_load_scripts' );
		
		if ( dataLayer ) dataLayer.push( {
			'event' : 'fp_privacyPreferencesChanged',
			'fp_visitorPrivacyPreferences' : fpdata.cookies,
		} );

		FP.sendEvt( 'fupi_consents_changed', 'all accepted') ;
		update_tools_consents();
	}

	function accept_stats_cookies_and_fire_custom_event(){

		let prev_consents = fpdata.cookies;

		// this is the default when the settings panel is hidden (we set everything to true)
		fpdata.cookies = {
			'stats': true,
			'personalisation': false,
			'marketing': false,
			'disabled': false,
			'pp_pub' : fp.notice.priv_policy_update,
			'tools' : fp.tools,
			'time' : get_date(),
			'timezone' : get_timezone()
		},
		dataLayer = window.dataLayer || window.fupi_dataLayer;

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
		
		save_preferences_in_cdb( prev_consents );

		FP.update_session_data();

		FP.sendEvt( 'fp_load_scripts' );

		if ( dataLayer ) dataLayer.push( {
			'event' : 'fp_privacyPreferencesChanged',
			'fp_visitorPrivacyPreferences' : fpdata.cookies,
		} );

		FP.sendEvt( 'fupi_consents_changed', 'stats accepted') ;
		update_tools_consents();
	}

	function accept_chosen_cookies_and_fire_custom_events() {

		let stats_checkbox = FP.findID('fupi_stats_agree'),
			perso_checkbox = FP.findID('fupi_pers_agree'),
			marke_checkbox = FP.findID('fupi_marketing_agree'),
			some_scripts_are_loaded = fp.loaded.some( el => el !== 'woo' && el !== 'gtm' && el !== 'gtg' && el !== 'ga41' && el !== 'ga42' && el !== 'gads'), // checks for scripts that do not adjust its behavior depending on consents
			prev_consents = fpdata.cookies;
		
		fpdata.cookies = {
				'stats': stats_checkbox ? stats_checkbox.checked : false,
				'personalisation': perso_checkbox ? perso_checkbox.checked : false,
				'marketing': marke_checkbox ? marke_checkbox.checked : false,
				'disabled': false,
				'pp_pub' : fp.notice.priv_policy_update,
				'tools' : fp.tools,
				'time' : get_date(),
				'timezone' : get_timezone()
			},
			dataLayer = window.dataLayer || window.fupi_dataLayer;

		FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
		
		save_preferences_in_cdb( prev_consents );

		FP.update_session_data();

		if ( some_scripts_are_loaded ) {
			location.reload();
		} else {
			FP.sendEvt( 'fp_load_scripts' );
			FP.sendEvt( 'fupi_consents_changed', 'some accepted' );
			update_tools_consents();
		}

		if ( dataLayer ) dataLayer.push( {
			'event' : 'fp_privacyPreferencesChanged',
			'fp_visitorPrivacyPreferences' : fpdata.cookies,
		} );
	}

	function decline_all_cookies() {

		if ( fp.vars.track_current_user ) {

			let some_scripts_are_loaded = fp.loaded.length > 0,
			prev_consents = fpdata.cookies;
			
			fpdata.cookies = {
					'stats': false,
					'personalisation': false,
					'marketing': false,
					'disabled': false,
					'pp_pub' : fp.notice.priv_policy_update,
					'tools' : fp.tools,
					'time' : get_date(),
					'timezone' : get_timezone()
				},
				dataLayer = window.dataLayer || window.fupi_dataLayer;

			FP.setCookie('fp_cookie', JSON.stringify(fpdata.cookies), 182 );
			
			save_preferences_in_cdb( prev_consents );

			FP.update_session_data();

			update_tools_consents();

			if ( dataLayer ) dataLayer.push( {
				'event' : 'fp_privacyPreferencesChanged',
				'fp_visitorPrivacyPreferences' : fpdata.cookies,
			} );

			FP.sendEvt('fupi_consents_changed', 'declined');

			some_scripts_are_loaded ? location.reload() : update_tools_consents();
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
		if( panel[type] ) {

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

		if ( ! type ) type = FP.findFirst('#fupi_welcome_panel.fupi_animated') ? 'welcome' : 'settings';
		
		activePanel = false;
		panel[type].classList.remove('fupi_fadeInUp');
		panel[type].classList.add('fupi_animated');
		panel[type].classList.add('fupi_fadeOutDown');
	}
	
	function show_toggler(){
		let toggler = FP.findID('fupi_notice_toggler');
		if ( toggler && fp.notice.mode !== 'hidden' && fp.notice.mode !== 'notify' ) {
			toggler.classList.add( 'fupi_active', 'fupi_fadeInUp' );
			toggler.classList.remove( 'fupi_fadeOutDown' );
		}
	}

	function hide_toggler(){
		let toggler = FP.findID('fupi_notice_toggler');
		if ( toggler && toggler.classList.contains('fupi_active') ) {
			toggler.classList.remove( 'fupi_fadeInUp');
			toggler.classList.add( 'fupi_fadeOutDown' );
		}
	}

	function show_notice_wrapper() {
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

})();
