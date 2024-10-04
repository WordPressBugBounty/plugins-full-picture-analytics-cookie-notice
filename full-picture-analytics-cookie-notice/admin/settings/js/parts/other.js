(()=>{

	// TOGGLE FILTERS SECTION

	let toggle_btn = FP.findID('fupi_toggle_filters_section'),
		filters_section = FP.findID('fupi_tools_filters');

	if ( toggle_btn && filters_section ) toggle_btn.addEventListener( 'click', ()=> filters_section.classList.toggle('fupi_active') );
})();

(()=>{

	// COPY CURRENT PAGE NAME TO SECTION HEADINGS

	let module_name = FP.findFirst('.fupi_active_title'),
		section_headings = FP.findAll('#fupi_settings_form h2');

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

	// ADD "EXTERNAL" DASHICON TO LINKS AND SET THEM TO OPEN IN A NEW TAB

	window.addEventListener( 'DOMContentLoaded', ()=>{

		let links = FP.findAll('#fupi_main_col a');

		links.forEach( link => {
			if ( ! link.href.includes(document.location.host) && ! link.classList.contains('fupi_vid') && ! link.classList.contains('fupi_vid_btn') ) {
				if ( ! link.target ) link.target = '_blank';
				link.insertAdjacentHTML('beforeend', ' <span class="dashicons dashicons-external"></span>');
			}
		})

	})
})();

(()=>{
	// PLAUSIBLE > TOGGLE SECTIONS OF SETTINGS PAGE DEPENDING IF A USER WANTS TO USE WP FP TO EXTEND PLAUSIBLE PLUGIN OR NOT

	let install_pla_with_wpfp = FP.findID('fupi_pla[pla_use]_install');

	if ( ! install_pla_with_wpfp ) return;

	let sections_to_hide = [
		'#fupi_current_page_sidenav button[data-target="hook_pla_1"]',
		'#fupi_current_page_sidenav button[data-target="hook_pla_3"]',
		'#fupi_current_page_sidenav button[data-target="hook_pla_6"]',
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

	// SHOW ALERT ABOUT UNSAVED CHANGES

	window.fupi_unsaved = false;

	document.addEventListener("DOMContentLoaded", function() { 

		var els = document.querySelectorAll('#fupi_settings_form textarea, #fupi_settings_form input, #fupi_settings_form select');
		
		els.forEach( function(el) {
			el.addEventListener('change', function() {
				window.fupi_unsaved = true;
				// disable a button linking to consent banner customizer
				let cookie_notice_customizer = FP.findFirst('.fupi_customize_notice_btn');
				if ( cookie_notice_customizer ) cookie_notice_customizer.classList.add('fupi_disabled');
			});
		});  
			
		window.addEventListener('beforeunload', function(event) {
			if(window.fupi_unsaved){
				event.returnValue = "string";
			}
		});

		var forms = document.querySelectorAll('form');
		forms.forEach( function(form) {
			form.addEventListener('submit', function() {
				window.fupi_unsaved = false;
			});
		});  

	});
})();

(()=>{

	// ADD "ACCOUNT" LINK TO SIDE NAV

	let sidebar_account_link = FP.findFirst('#toplevel_page_full_picture_tools a[href="https://wpfullpicture.com/account/"]'),
		sidenav_section = FP.findFirst('.fupi_sidenav_account_section');

	if ( sidebar_account_link && sidenav_section ) {
		sidenav_section.style.display = 'block';
		FP.findFirst( 'a', sidenav_section ).href = sidebar_account_link.href;
	}
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