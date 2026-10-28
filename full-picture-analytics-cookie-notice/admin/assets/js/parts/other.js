(()=>{

	// COPY CURRENT PAGE NAME TO SECTION HEADINGS

	let module_name = FP.findFirst('.fupi_active_title'),
		section_headings = FP.findAll('#fupi_main_form.fupi_settings_form h2');

	if ( module_name ) {
		section_headings.forEach( h => {
			h.insertAdjacentHTML( 'afterbegin', '<span class="fupi_module_name">' + module_name.textContent + '</span>')
		} )
	}
})();

(()=>{

	// TAB NAVIGATION

	document.addEventListener('click', e=>{
		if ( e.target.classList.contains('fupi_tab') && ! e.target.classList.contains('fupi_active') ){
			FP.findFirst( '.fupi_tab.fupi_active', e.target.parentElement ).classList.remove('fupi_active');
			e.target.classList.add('fupi_active');
		}
	})

})();

(()=>{

	// REMEMBER OPENED COG SETTINGS BUTTONS

	const cookie_name = 'fp_admin_checked_related';

	function get_btn_data( btn ) {
		return [
			btn.dataset.moduleId || '',
			( btn.dataset.field_ids || '' ).split(',').map( id => id.trim() ).filter( Boolean )
		];
	}

	function get_saved_btns() {
		try {
			const saved_cookie = FP.readCookie( cookie_name ) || '[]';
			const saved_btns = new Map();

			JSON.parse( saved_cookie ).forEach( saved_btn => {
				if ( ! Array.isArray( saved_btn ) || typeof saved_btn[0] !== 'string' || ! Array.isArray( saved_btn[1] ) ) return;

				const field_ids = saved_btn[1].filter( id => typeof id === 'string' && id );
				saved_btns.set( saved_btn[0], [ ...new Set( [ ...( saved_btns.get( saved_btn[0] ) || [] ), ...field_ids ] ) ] );
			});

			const normalized_cookie = JSON.stringify( [ ...saved_btns ] );
			if ( normalized_cookie !== saved_cookie ) FP.setCookie( cookie_name, normalized_cookie, 7 );

			return saved_btns;
		} catch ( error ) {
			return new Map();
		}
	}

	function mark_saved_btns( saved_btns ) {
		FP.findAll('.fupi_not_builder_btn').forEach( btn => {
			const [ module_id, field_ids ] = get_btn_data( btn );
			const saved_field_ids = saved_btns.get( module_id );

			if ( saved_field_ids && field_ids.every( id => saved_field_ids.includes( id ) ) ) btn.classList.add('fupi_cog_btn__checked');
		});
	}

	const saved_btns = get_saved_btns();
	mark_saved_btns( saved_btns );

	document.addEventListener('click', e => {
		const btn = e.target.closest('.fupi_not_builder_btn');
		if ( ! btn ) return;

		const [ module_id, field_ids ] = get_btn_data( btn );
		const saved_field_ids = saved_btns.get( module_id ) || [];
		const merged_field_ids = [ ...new Set( [ ...saved_field_ids, ...field_ids ] ) ];

		if ( merged_field_ids.length > saved_field_ids.length ) {
			saved_btns.set( module_id, merged_field_ids );
			FP.setCookie( cookie_name, JSON.stringify( [ ...saved_btns ] ), 7 );
		}

		mark_saved_btns( saved_btns );
	});

})();

(()=>{

	// ADD "EXTERNAL" DASHICON TO LINKS AND SET THEM TO OPEN IN A NEW TAB
	window.addEventListener( 'DOMContentLoaded', ()=>{

		let links = FP.findAll('#fupi_main_col a, .fupi_offscreen_source a, #fupi_offscreen a');

		links.forEach( link => {
			if ( ! link.classList.contains('no_external_icon') && ! link.href.includes(document.location.host) && ! link.classList.contains('fupi_vid') && ! link.classList.contains('fupi_vid_btn') && ! link.querySelector('.dashicons-external') ) {
				if ( ! link.target ) link.target = '_blank';
				link.insertAdjacentHTML('beforeend', ' <span class="dashicons dashicons-external"></span>');
			}
		})
	})

	// ADD UTM PARAMS TO LINKS TO WP FP
	if ( ! window.location.href.includes('wpfullpicture') ) {
		window.addEventListener( 'click', e=>{
			let link = e.target.closest('a[href]');
			if ( link && link.matches('#fupi_content a[href]') && link.href.includes('wpfullpicture.com') && fupi_version && fupi_licence ) {
				link.href += '?utm_source=wpfp_plugin&utm_medium=wp_admin&utm_term=' + fupi_licence + '&utm_content=v_' + fupi_version;
			}		
		})
	}

})();

(()=>{
	// PLAUSIBLE > TOGGLE SECTIONS OF SETTINGS PAGE DEPENDING IF A USER WANTS TO USE WP FP TO EXTEND PLAUSIBLE PLUGIN OR NOT

	let install_pla_with_wpfp = FP.findID('fupi_pla[pla_use]_install');

	if ( ! install_pla_with_wpfp ) return;

	let sections_to_hide = [
		'.fupi_sidenav_item.fupi_current button[data-target="hook_pla_1"]',
		'.fupi_sidenav_item.fupi_current button[data-target="hook_pla_3"]',
		'.fupi_sidenav_item.fupi_current button[data-target="hook_pla_7"]',
	]

	function toggle_sections( state ){

		sections_to_hide.forEach( selector => {
			let nav_el = FP.findFirst( selector );
			nav_el.style.display = state == 'hide' ? 'none' : 'block';
		})
	}

	let extend_pla_with_wpfp = FP.findID('fupi_pla[pla_use]_extend');

	// do after load
	
	if ( extend_pla_with_wpfp.checked ) toggle_sections( 'hide' );

	// do on click

	extend_pla_with_wpfp.addEventListener( 'change', ()=>{ if ( extend_pla_with_wpfp.checked ) toggle_sections( 'hide' ) } );
	install_pla_with_wpfp.addEventListener( 'change', ()=>{ if ( install_pla_with_wpfp.checked ) toggle_sections( 'show' ) } );
})();

(()=>{

	let main_form = FP.findID('fupi_main_form');
	if ( ! main_form ) return; // for the dashboard

	// SHOW ALERT ABOUT UNSAVED CHANGES

	window.fupi_unsaved = false;

	function markUnsaved() {
		window.fupi_unsaved = true;
		let cookie_notice_customizer = FP.findFirst('.fupi_customize_notice_btn');
		if ( cookie_notice_customizer ) cookie_notice_customizer.classList.add('fupi_disabled');
	}

	function isInSettingsForm(el) {
		return el.closest && el.closest('.fupi_settings_form');
	}

	function isSettingsForm(el) {
		return el.classList && el.classList.contains('fupi_settings_form');
	}

	main_form.addEventListener('change', function(e) {
		if (isInSettingsForm(e.target)) {
			markUnsaved();
		}
	});

	main_form.addEventListener('input', function(e) {
		if (isInSettingsForm(e.target)) {
			markUnsaved();
		}
	});

	main_form.addEventListener('submit', function(e) {
		if (isSettingsForm(e.target)) {
			window.fupi_unsaved = false;
		}
	});

	window.addEventListener('beforeunload', function(event) {
		if(window.fupi_unsaved){
			event.preventDefault();
			event.returnValue = '';
		}
	});
})();

(()=>{

	// SLIDER with info on PRO features

	function show_random_slide( slide_dots, slides ){
		
		let slide_nr = Math.floor( Math.random() * slides.length );
		
		slides[slide_nr].classList.add('fupi_active');
		slide_dots[slide_nr].classList.add('fupi_active');
	}

	function change_slide_on_click( slider, slide_dots, slides ){
		
		slide_dots.forEach( dot => {

			dot.addEventListener( 'click', ()=>{
				
				let slide_nr = slide_dots.indexOf( dot ),
					active_slide = FP.findFirst( '.fupi_slide.fupi_active', slider ),
					active_slide_dot = FP.findFirst( '.fupi_slider_dot.fupi_active', slider );

				active_slide.classList.remove('fupi_active');
				active_slide_dot.classList.remove('fupi_active');

				slides[slide_nr].classList.add('fupi_active');
				dot.classList.add('fupi_active');
			})
		})
	}

	function make_slider_dots( slider, slides ){

		let slider_dots_nav = FP.findFirst('.fupi_slider_nav', slider ),
			dots = '';

		for ( let i = 0; i < slides.length; i++ ) {
			dots += '<li><button type="button" class="fupi_slider_dot"><span>Show slide ' + ( i + 1 ) + '</span></button></li>';
		}

		slider_dots_nav.innerHTML = dots;
	}

	document.addEventListener('DOMContentLoaded', ()=>{
		
		let slider = FP.findFirst('.fupi_slider');
		if ( ! slider ) return;

		let slides = FP.findAll( '.fupi_slide', slider );

		make_slider_dots( slider, slides );

		let slide_dots = FP.findAll( '.fupi_slider_dot', slider )

		show_random_slide( slide_dots, slides );
		change_slide_on_click( slider, slide_dots, slides );
	})
})();

// UPDATE SETUP AND ADV MODES IN FUPI_MAIN WITH AJAX

(() => {

	const advModeCheckbox = FP.findID('adv_mode_checkbox');
	const setupModeCheckbox = FP.findID('setup_mode_checkbox');

	function handleChange(e) {

		const mode = e.target.id === 'adv_mode_checkbox' ? 'adv_mode' : 'setup_mode';
		const value = mode === 'adv_mode' ? ! e.target.checked : e.target.checked;
		const data = new FormData();

		data.append('action', 'fupi_update_modes');
		data.append('mode', mode);
		data.append('value', value);
		data.append('security', fupi_setup_mode_nonce);

		fetch( ajaxurl, {
			method: 'POST',
			body: data,
			credentials: 'same-origin'
		} )
		.then(response => response.json())
		.then(response => {
			console.log(response.data);

			if ( mode == 'adv_mode' ) {
				document.location.reload();
			}
		});
	}

	function toggleNotifAndSave(e){
		
		handleChange(e);
		
		let notif_bar = FP.findID('setup_helper_notif_bar'),
			show_notice = notif_bar && e.target.checked;

		notif_bar.style.display = show_notice ? '' : 'none';
	}

	if (advModeCheckbox) advModeCheckbox.addEventListener('change', handleChange );
	if (setupModeCheckbox) setupModeCheckbox.addEventListener('change', toggleNotifAndSave);

})();

// ENABLE ENHANCED SELECT FIELDS THAT ARE NOT IN A REPEATER

document.addEventListener('DOMContentLoaded', function() {
	FupiSlimSelect.init( document, '.fupi_select2:not(.fupi_select2_enabled)' );
});

// HIDE WOOCOMMERCE SETTINGS FIELDS (e.g. in the GAds module) when Woo is not enabled

(()=>{
	let woo_not_installed_notice = FP.findFirst('.fupi_enable_woo_notice');

	if ( woo_not_installed_notice ) {
		// get description wrapper
		let descr = woo_not_installed_notice.parentElement;
		// get the next HTML element after description wrapper
		let next_element = descr.nextElementSibling;
		// check if next element is a table
		if ( next_element.tagName === 'TABLE' ) {
			next_element.style.display = 'none';
		}
	}
})();

(()=>{

    // CUSTOM META BUILDER - clear sections that contain not existing (expired) custom meta

    let builder_sections = FP.findAll('.fupi_metadata_tracker .fupi_r3_section');

    builder_sections.forEach( section => {
        
        let metadata_select = FP.findFirst( '.fupi_field_type_custom_meta_select select', section );

        if ( metadata_select && ! metadata_select.value ){
            let minus_button = FP.findFirst( '.fupi_r3_remove_btn', section );
            if ( minus_button ) minus_button.click();
        }
    } );

})();
