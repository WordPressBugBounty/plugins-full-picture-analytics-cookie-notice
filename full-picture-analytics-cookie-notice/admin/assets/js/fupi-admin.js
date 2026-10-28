(()=>{
    
    // Show notification if the tool is installed

    let required = FP.findAll('tr.fupi_required'),
        installed_info = FP.findID('fupi_installed_info'),
        fupi_not_installed_info = FP.findID('fupi_not_installed_info');

    if ( installed_info ) {
        if ( required.length == 0 ) {
            installed_info.classList.remove('fupi_hidden');
        } else {
            
            let some_required_are_empty = required.some( tr => {
                return ! FP.findFirst('input', tr).value;
            } );

            if ( ! some_required_are_empty ) {
                installed_info.classList.remove('fupi_hidden');
            } else {
                if ( fupi_not_installed_info ) fupi_not_installed_info.classList.remove('fupi_hidden');
            }
        }
    }
    
})();
// CREATE LINKS TO SECTIONS (IN SIDEBAR)

(() => {

	let fupi_settings_form 	= FP.findID('fupi_main_form'),
		fupi_admin_page 	= FP.getUrlParamByName('page'),
		headlines 			= FP.findAll( '#fupi_main_form.fupi_settings_form h2' ),
		hidden_h2			= [],
		visible_h2			= [],
		sub_items_added		= false,
		is_easy_mode		= !! FP.findFirst('#fupi_content.adv_mode_off');

	function mark_all_h2_as_fupi_el(){
		headlines.forEach( h2 => h2.classList.add('fupi_el'));
	}

	function get_next_table_el( h2 ){

		let nextEl = h2.nextElementSibling;

		if ( nextEl ) {

			if ( nextEl.tagName == 'TABLE' ) {
				return nextEl;
			} else {
				let nextNextEl = nextEl.nextElementSibling;
				if ( nextNextEl && nextNextEl.tagName == 'TABLE' ) return nextNextEl;
			}
		}

		return false;
	}

	function check_if_show( h2 ){

		if ( ! is_easy_mode ) return true;

		let table_el = get_next_table_el(h2);

		if ( table_el ) {
			if ( table_el.querySelectorAll('tr:not(.fupi_adv)').length > 0 ) return true;
		} else {
			return true;
		}

		return false;
	}

	function add_submenu_items(){
		
		let current_page_el = FP.findFirst('.fupi_sidenav_item.fupi_current'),
			output = '<div id="fupi_sidenav_sub">';
		
		headlines.forEach( ( h2, i ) => {
			
			let show = check_if_show( h2 );

			if ( ! show ) {
				h2.style.display = 'none';
				hidden_h2.push(h2);
				return;
			};

			visible_h2.push(h2);

			let h_txt = h2.innerText,
				current_class = i == 0  ? 'active' : '',
				active_tab = fupi_settings_form.dataset.activetab;
			
			output += '<button type="button" data-target="hook_' + active_tab + '_' + i + '" class="fupi_sidenav_sub_item ' + current_class + '"><span>' + h_txt + '</span></button>';

			// add hook to section
			h2.setAttribute( 'id', 'hook_' + active_tab + '_' + i );
		});

		// Add submenu items only if there is more than 1 section
		if ( visible_h2.length <= 1 ) return;

		// current_page_el.insertAdjacentHTML('beforebegin', '<button id="fupi_toggle_hidden_menu_items"><span class="dashicons dashicons-menu-alt"></span><span class="fupi_srt">Menu</span></button>');
		current_page_el.insertAdjacentHTML('beforeend', output + '</div>');
		current_page_el.classList.add('fupi_has_subnav');

		sub_items_added = true;
	}

	function remove_highlight_from_active_menu_item(){
		let active_menu_link = FP.findFirst('#fupi_nav_col .fupi_sidenav_sub_item.active');
		if ( active_menu_link ) active_menu_link.classList.remove('active');
	}

	function enable_sections_toggle(){

		let fupi_nav_col_links = FP.findAll('#fupi_nav_col .fupi_sidenav_sub_item');

		// make 1st section visible & unhide form
		if ( visible_h2.length > 1 ) show_section( headlines[0].id ); // show first section

		// add events to links that add a target to url & show sections
		if ( fupi_nav_col_links.length > 1 ) {

			fupi_nav_col_links.forEach( link => {
				link.addEventListener( 'click', () => {
					if ( ! link.classList.contains('active') ) {
						let hook = link.dataset.target;
						show_section( hook );
						FP.setCookie('fp_viewed_section', JSON.stringify( [ hook, document.location ] ) ); // remember chosen section
					}
				} )
			} )
		}
	}

	function show_last_viewed_section(){

		let last_viewed_section = FP.readCookie('fp_viewed_section');

		if ( ! last_viewed_section ) return;

		last_viewed_section = JSON.parse( last_viewed_section );

		// do nothing if the page was reloaded or refreshed
		if ( last_viewed_section[1] == document.location ) return; 

		let section_menu_item = FP.findFirst( '.fupi_sidenav_sub_item[data-target="' + last_viewed_section[0] + '"]' );
		if ( ! section_menu_item ) {
			FP.deleteCookie('fp_viewed_section' );
		} else {
			show_section( last_viewed_section[0] );
		};
	}

	function show_section( hook ) {

		let section_elements 	= FP.findAll( '.fupi_el, .form-table' ),
			pseudo_link 		= FP.findFirst( '.fupi_sidenav_sub_item[data-target="' + hook + '"]' );

		if ( pseudo_link ) {

			remove_highlight_from_active_menu_item();

			// hide all sections
			section_elements.forEach( element => element.classList.add('fupi_hidden'));
	
			// show chosen section
			FP.findAll('#' + hook + ', #' + hook + '+*, #' + hook + '+*+table').forEach( element => element.classList.remove('fupi_hidden'));
	
			// highlight current menu link
			pseudo_link.classList.add('active');
		}
	}

	function hide_all_section_elements(){
		FP.findAll( '.fupi_el, .form-table' ).forEach( element => element.classList.add('fupi_hidden'));
	}

	function show_the_only_visible_section(){
		
		let h2 = FP.findFirst('h2[id*="hook_track_"]'),
			nextEl = h2.nextElementSibling;

		h2.classList.remove('fupi_hidden');

		if ( nextEl && nextEl.classList.contains('fupi_section_descr') ) {
			
			nextEl.classList.remove('fupi_hidden');

			let nextNextEl = nextEl.nextElementSibling;
			if ( nextNextEl && nextNextEl.tagName == 'TABLE' ) {
				nextNextEl.classList.remove('fupi_hidden');
			}
		}
	}

	if ( headlines.length > 1 && fupi_admin_page && fupi_settings_form ) {
		
		if ( fupi_admin_page != 'full_picture_tools' ){

			mark_all_h2_as_fupi_el();
			hide_all_section_elements();
			add_submenu_items();

			if ( visible_h2.length > 1 ) {
				enable_sections_toggle();
			} else {
				show_the_only_visible_section();
			}
		}

		show_last_viewed_section();
	};

})();

(() => {

	// DISABLE SETTINGS NOT AVAILABLE FOR FREE USERS

	let fupi_content_el = FP.findID('fupi_content');

	if ( ! fupi_content_el ) return;
	
	FP.disableFieldsForPlans = () => {
		
		FP.findAll( '.fupi_must_have_info, .fupi_must_have_pro_ico, .fupi_must_have_pro_ico_round' ).forEach( el => {

			let wrap = el.closest('.fupi_r3_field, tr'),
				fields = FP.findAll( 'input, select, textarea', wrap ),
				buttons = FP.findAll( '.fupi_r3_btn', wrap );
			
			fields.forEach( field => field.disabled = true );
			buttons.forEach( btn => btn.classList.add('fupi_disabled') );
			
			wrap.classList.add('fupi_missing_must_have', 'fupi_disabled');
		});
	}

	FP.disableFieldsForPlans();
	
})();
// CONDITIONS
// toggle fields that can be conditionally toggled

FP.initCondFields = ( wrapper_sel )=>{

	let condition_fields = FP.findAll( wrapper_sel + ' .fupi_condition' );
	
	// sync after pageload

	condition_fields.forEach( field => {
		sync_settings( field );
	});
	
	function get_cond_field(e){
		if ( e.target.classList.contains('fupi_condition') ){
			sync_settings(e.target);
		}
	}

	// after click
	document.onclick = (e) => { get_cond_field( e ); };

	// on key up
	document.onkeyup = (e) => { get_cond_field( e ); }

	// on change
	document.onchange = (e) => { get_cond_field( e ); }

	function sync_settings( field ) {

		let field_type = field.getAttribute('type') || field.tagName.toLowerCase(),
			table_rows = FP.findAll('.' + field.dataset.target);

		switch (field_type) {

			case 'checkbox':
				let should_toggle = field.classList.contains('fupi_condition_reverse') ? ! field.checked : field.checked;
				toggle_rows(table_rows, should_toggle);
			break;

			case 'number':
				let val = field.value != 0 ? true : false;
				toggle_rows( table_rows, val );
			break;

			case 'text':
			case 'textarea':
				toggle_rows(table_rows, !!field.value );
			break;

			case 'select' :
				toggle_rows(table_rows, field.value, 'select');
			break;

			case 'radio' :
				if ( field.checked ) toggle_rows(table_rows, field.value, 'radio');
			break;

		}
	}

	function toggle_rows( table_rows, enabled, field_type ) {

		table_rows.forEach( row => {

			let inputs = row.classList.contains('fupi_missing_must_have') ? [] : FP.findAll( 'input, textarea, select', row );
			
			if ( field_type != 'select' && field_type != 'radio' ) {

				// toggle row
				if ( enabled ) {
					if ( row.classList.contains('fupi_missing_must_have') ) { 
						row.classList.remove( 'fupi_hidden' );
					} else {
						row.classList.remove( 'fupi_hidden', 'fupi_disabled' );
					}
				} else {
					if ( row.classList.contains('fupi_missing_must_have') ) { 
						row.classList.add( 'fupi_hidden' );
					} else {
						row.classList.add( 'fupi_hidden', 'fupi_disabled' );
					}
				}

				// disable fields
				inputs.forEach( input => input.disabled = ! enabled );

			} else {

				// toggle row and inputs
				if ( row.classList.contains('fupi_cond_val_' + enabled ) ) {
					
					if ( row.classList.contains('fupi_missing_must_have') ) { 
						row.classList.remove( 'fupi_hidden' );
					} else {
						row.classList.remove( 'fupi_hidden', 'fupi_disabled' );
					}

					inputs.forEach( input => input.disabled = false );

				} else {

					if ( row.classList.contains('fupi_missing_must_have') ) { 
						row.classList.add( 'fupi_hidden' );
					} else {
						row.classList.add( 'fupi_hidden', 'fupi_disabled' );
					}

					inputs.forEach( input => input.disabled = true );
				}
			}
		})
	}
}

FP.initCondFields( '#fupi_main_form' );

(()=>{
	
	// CONSENT BANNER > TOGGLE HIDDEN RADIOS & "MANUAL" FIELDS
	
	let mode_fields = FP.findAll('.fupi_cookie_notice_modes input[type="radio"]');

	if ( ! mode_fields.length ) return;
	
	// first we hide all manual fields
	FP.findID('fupi_main_form').classList.add('fupi_hide_manual_cookie_settings');
	
	// show manual fields if manual radio is checked
	toggle_manual_fields();

	function toggle_manual_fields(){
		let selected_mode = FP.findFirst('.fupi_cookie_notice_modes input[type="radio"]:checked');

		if ( selected_mode && selected_mode.value == 'manual' ) {
			FP.findID('fupi_main_form').classList.remove('fupi_hide_manual_cookie_settings');
		} else {
			FP.findID('fupi_main_form').classList.add('fupi_hide_manual_cookie_settings');
		}
	}

	// then we go over each radio field
	mode_fields.forEach( field => field.addEventListener('change', toggle_manual_fields ) );

})();

window.FupiSlimSelect = (() => {

	function get_search_action( select ) {
		if ( select.classList.contains('fupi_user_search') ) return 'fupi_search_users';
		if ( select.classList.contains('fupi_page_search') ) return 'fupi_search_pages';
		return false;
	}

	function get_config( select ) {
		const placeholder = select.dataset.placeholder_text || '';
		const config = {
			select,
			settings: {
				placeholderText: placeholder,
				searchPlaceholder: placeholder,
			}
		};
		const search_action = get_search_action( select );

		if ( search_action ) {
			config.events = {
				search: search_value => {
					if ( search_value.length < 2 ) return [];
					if ( select.fupi_slim_select_abort_controller ) {
						select.fupi_slim_select_abort_controller.abort();
					}

					const params = new URLSearchParams({
						action: search_action,
						q: search_value,
					});
					const abort_controller = new AbortController();
					select.fupi_slim_select_abort_controller = abort_controller;

					return new Promise( (resolve, reject) => {
						fetch( ajaxurl + '?' + params.toString(), {
							credentials: 'same-origin',
							signal: abort_controller.signal,
						})
							.then( response => {
								if ( ! response.ok ) throw new Error('Search request failed.');
								return response.json();
							})
							.then( results => {
								if ( select.fupi_slim_select_abort_controller !== abort_controller ) return;
								delete select.fupi_slim_select_abort_controller;
								resolve( results.map( result => ({
									text: result.text,
									value: String(result.id),
								}) ) );
							})
							.catch( error => {
								if ( select.fupi_slim_select_abort_controller !== abort_controller ) return;
								delete select.fupi_slim_select_abort_controller;
								if ( error.name === 'AbortError' ) return;
								reject(error);
							} );
					} );
				}
			};
		}

		return config;
	}

	function init( root = document, selector = '.fupi_select2' ) {
		if ( typeof SlimSelect === 'undefined' || ! root ) return;

		root.querySelectorAll(selector).forEach( select => {
			if ( select.tagName !== 'SELECT' || select.slim ) return;

			new SlimSelect( get_config(select) );
			select.classList.add('fupi_select2_enabled');
		} );
	}

	function destroy( root = document, selector = '.fupi_select2_enabled.fupi_select2' ) {
		if ( ! root ) return;

		root.querySelectorAll(selector).forEach( select => {
			if ( select.tagName !== 'SELECT' ) return;

			if ( select.fupi_slim_select_abort_controller ) {
				select.fupi_slim_select_abort_controller.abort();
				delete select.fupi_slim_select_abort_controller;
			}
			if ( select.slim ) {
				select.slim.destroy();
				delete select.slim;
			}
			select.classList.remove('fupi_select2_enabled');
		} );
	}

	function initIn( wrapper_selector = '' ) {
		init( document, wrapper_selector + ' .fupi_select2' );
	}

	function destroyRepeaters() {
		destroy( document, '.fupi_r3_repeater .fupi_select2_enabled.fupi_select2' );
	}

	return { init, destroy, initIn, destroyRepeaters };
})();

(() => {

	const fupi_content = FP.findFirst('#fupi_content');

	if ( ! fupi_content ) return;

	function enable_submit_btn( enable ) { // works only on ajax form in the slideout pannel
		const submit_btn = FP.findFirst( '.fupi_settings_panel_form_submit_btn' );
		if ( submit_btn ) submit_btn.disabled = ! enable;
	}

	function mark_slideout_form_as_unsaved( section ){
		let form = section.closest('#fupi_dynamic_settings_form');
		if ( form ) {
			form.dataset.unsaved = true;
			enable_submit_btn( true );
		}
	}

	function remove_are_you_sure_styling(btn){
		btn.classList.remove('are_you_sure');
		btn.textContent = btn.dataset.default_text;
	}

	function add_are_you_sure_styling(btn){
		btn.classList.add('are_you_sure');
		btn.textContent = btn.dataset.confirm_text;
	}

	function fill_event_headline( section, title ) {
		let title_el = FP.findFirst( '.fupi_headline_title', section );
		if ( title_el ) title_el.textContent = title;
	}

	// Generate random IDs
	
	let generated_id_increment = 0;

	function fill_empty_id_fields_with_random_ids( scope = window.document ){
		let all_hidden_id_fields = FP.findAll('.fupi_field_type_hidden input.fupi_generate_id', scope );
		all_hidden_id_fields.forEach( field => {
			if ( ! field.value ) {
				generated_id_increment++;
				field.value = Date.now() + generated_id_increment;
			}
		} );
	}

	// Remove section or...
	// - if it is the only one
	// -- clear it - if it is not in a builder
	// -- clear and hide it - if it is in a builder

	function smart_remove_section( current_section ){

		let siblingSections = FP.getSiblings( current_section );

		siblingSections = siblingSections.filter(sect => sect.classList.contains('fupi_r3_section')); // get only r3 sections

		// check if this is the only section on this level
		if ( siblingSections.length > 1 ) {
			FP.remove( current_section );
		} else {
			clear_section( current_section );
			mark_req_fields( current_section );
			if ( current_section.classList.contains('fupi_r3_event_builder') ) current_section.classList.add('fupi_hidden');
		}
	}

	function add_section_after( current_section ){
		
		let cloned_section = current_section.cloneNode(true);

		cloned_section = clear_section( cloned_section );
		cloned_section = remove_extra_sub_repeaters(cloned_section);

		fill_empty_id_fields_with_random_ids( cloned_section );
		mark_req_fields( cloned_section );

		current_section.parentElement.insertBefore(cloned_section, current_section.nextSibling );
	}
	
	function make_new_section_after( current_section, trigger_id, trigger_name ){

		let cloned_section = current_section.cloneNode(true);

		cloned_section = clear_section( cloned_section );
		cloned_section = remove_extra_sub_repeaters(cloned_section);

		cloned_section.classList.remove('fupi_hidden');

		let atrig_id_field = FP.findFirst( 'input[name*="atrig_id"]', cloned_section );
		if ( atrig_id_field ) atrig_id_field.value = trigger_id;

		fill_event_headline( cloned_section, trigger_name );

		mark_req_fields( cloned_section );
		
		current_section.parentElement.insertBefore(cloned_section, current_section.nextSibling );
	}

	// after a field has been added or removed this makes sure that field names and IDs use the correct array indexes 
	function get_direct_r3_sections( sections_wrap ) {

		let sibling_sections = FP.nl2Arr( sections_wrap.children ),
			sections = [];

		sibling_sections.forEach( child => {
			if ( child.classList.contains('fupi_r3_section') ) {
				sections.push( child );
			} else if ( child.classList.contains('fupi_r3_subfield_section') ) {
				sections = sections.concat( FP.nl2Arr( child.children ).filter( sub_child => sub_child.classList.contains('fupi_r3_section') ) );
			}
		});

		return sections;
	}

	function order_array_indexes_in_all_sections( sections_wrap, parent_sect_nums = [] ){

		// get all the actual repeater sections that are direct descendants of the section wrap (usually td), also through the r3 subfield wrapper
		let sections = get_direct_r3_sections( sections_wrap );

		sections.forEach( (section, sect_i) => {

			let current_sect_nums = [...parent_sect_nums, sect_i]; // starts as [0]

			// get all the field wrappers that are direct descendants of the section or are inside a fupi_r3_fields_group wrapper (or builder)
			let section_children_arr = FP.nl2Arr( section.children ),
				field_wrappers = [];

			section_children_arr.forEach( child => {
				if ( child.classList.contains( 'fupi_r3_field' ) && ! child.classList.contains('fupi_field_type_label') ) {
					field_wrappers.push( child );
				} else if ( child.classList.contains( 'fupi_r3_fields_group' ) ) {
					let sub_section_children_arr = FP.nl2Arr( child.children );
					sub_section_children_arr.forEach( sub_child => {
						if ( sub_child.classList.contains( 'fupi_r3_field' ) && ! sub_child.classList.contains('fupi_field_type_label') ) {
							field_wrappers.push( sub_child );
						}
					})
				}
			});

			field_wrappers.forEach( ( field_wrap, field_i ) => {

				// if field wrapper contains a repeater run renaming function on its content
				if ( field_wrap.classList.contains('fupi_field_type_r3') ){

					order_array_indexes_in_all_sections( field_wrap, current_sect_nums );

				// if the field wrapper contains form fields then modify their names and ids
				} else {

					let field = FP.findFirst('input, textarea, select', field_wrap);

					// make sure that wrapper has a form field
					if ( field ) {

						let name_parts = field.name.split(/\]\[\d*?\]\[/g) || []; // this gives us strings with square brackets, like '][3]['. We don't need anything more than that. We use it only to count how many occurances of digits appear
	
						if (name_parts.length - 1 <= current_sect_nums.length ){ // there should always be 1 more name part than numbers to fill in
	
							// here we create a name and id
							let new_name = name_parts.shift(); // we take the first name part (this also lowers the number of elements in the array)
							name_parts.forEach( ( part, i ) => { new_name += '][' + current_sect_nums[i] + '][' + part; } );
	
							// console.log('new field name for ' + field.name + ' is ' + new_name);
	
							field.name = new_name;
							field.id = new_name;
						} else {
							console.error('name can\'t be constructed for ', field, ' with: ', name_parts, current_sect_nums );
						}
					}

				};
			} );
		} );
	}
	
	// removes sub repeaters, leaving only the first one
	function remove_extra_sub_repeaters(section){

		let extra_section = FP.findFirst('.fupi_r3_repeater + .fupi_r3_repeater', section);
		while ( extra_section ) {
			FP.remove(extra_section);
			extra_section = FP.findFirst('.fupi_r3_repeater + .fupi_r3_repeater', section);
		}

		return section;
	}

	function remove_r3_section( section ){
		
		let td = section.closest('td'),
			has_select2 = FP.findFirst( '.ss-main', section );

		mark_slideout_form_as_unsaved( section );

		if ( has_select2 ) FupiSlimSelect.destroyRepeaters();

		smart_remove_section( section );
		fill_empty_id_fields_with_random_ids( td );
		order_array_indexes_in_all_sections( td );

		if ( has_select2 ) FupiSlimSelect.initIn();
	}

	function handle_remove_btn_with_confirm( target ){
		
		let	remove_double_confirm_btn 	= target.closest('.fupi_remove_double_confirm_btn'),
			are_you_sure_btn 			= FP.findFirst('.are_you_sure');
			
		// if the user clicked "remove" btn
		if ( remove_double_confirm_btn ) {

			// if this btn has no "Are you sure" text
			if ( ! target.closest('.are_you_sure') ) {

				// remove "AYS" styling from other button (if exists)
				if ( are_you_sure_btn ) {
					remove_are_you_sure_styling(are_you_sure_btn);
				}

				// add "AYS" styling to this button
				add_are_you_sure_styling(remove_double_confirm_btn);

				return true;
			
			// and btn has AYS text
			} else {
				// remove section
				let current_section = remove_double_confirm_btn.closest('.fupi_r3_section');
				remove_r3_section( current_section );
				// enable_submit_btn( true );

				return true;
			}
		
		// if the user clicked sth else
		} else {
			if ( are_you_sure_btn ) remove_are_you_sure_styling(are_you_sure_btn);
		}

		return false;
	}

	function handle_plus_section_button( target ){

		const add_section_btn = target.closest('.fupi_r3_add_btn');

		if ( add_section_btn ) {
			// add section right after the section where "+" was clicked
			let section = target.closest('.fupi_r3_section'),
				td = section.closest('td'),
				has_select2 = FP.findFirst( '.ss-main', section );
			
			mark_slideout_form_as_unsaved( section );
			
			if ( has_select2 ) FupiSlimSelect.destroyRepeaters();
			add_section_after( section );
			order_array_indexes_in_all_sections( td );
			if ( has_select2 ) FupiSlimSelect.initIn();

			return true;
		}
		return false;
	}

	function handle_minus_section_btn( target ){

		const remove_btn = target.closest('.fupi_r3_remove_btn');
			
		if ( remove_btn ) {
			let current_section = target.closest('.fupi_r3_section');
			remove_r3_section( current_section );
			return true;
		}
		return false;
	}

	function handle_add_trigger_btn( target ) { // trigger builder > btn below all sections

		const add_section_at_end_btn = target.closest('.fupi_r3_add_end_btn');
		
		if ( add_section_at_end_btn ) {
			
			let td = target.closest('td'),
				direct_children = FP.nl2Arr( td.children );

			let all_sections = direct_children.filter( child => {
				return child.matches('.fupi_r3_section');
			});
				
			let last_section = all_sections[all_sections.length -1 ];

			// check if there is only one section, is empty and invisible
			let show_section = all_sections.length == 1 && all_sections[0].classList.contains('fupi_empty_r3') && all_sections[0].classList.contains('fupi_hidden');

			if ( show_section ) {
				last_section.classList.add('fupi_section_not_saved');
				last_section.classList.remove('fupi_hidden');
				mark_slideout_form_as_unsaved( last_section );
			} else {

				let has_select2 = FP.findFirst( '.ss-main', last_section );

				mark_slideout_form_as_unsaved( last_section );
				if ( has_select2 ) FupiSlimSelect.destroyRepeaters();
				add_section_after( last_section );
				order_array_indexes_in_all_sections( td );
				if ( has_select2 ) FupiSlimSelect.initIn();
			}

		
			return true;
		}

		return false;
	}

	function handle_show_evt_settings_btn( target ){

		const show_evt_settings_btn = target.closest('.fupi_r3_builder_evt_settings_btn');

		if ( ! show_evt_settings_btn ) return false;
			
		let trigger_id = show_evt_settings_btn.dataset.trigger_id,
			trigger_name = show_evt_settings_btn.textContent,
			td = target.closest('td'),
			first_top_section = FP.findFirst('.fupi_r3_section', td ),
			section_with_trigger_el = FP.findFirst( '.fupi_r3_section:has(input[name*="[atrig_id]"][value="' + trigger_id + '"]', td ),
			all_top_level_siblings = section_with_trigger_el ? FP.getSiblings( section_with_trigger_el ) : FP.getSiblings( first_top_section ),
			hidden_current_section = false;

		// hide all top level sections
		all_top_level_siblings.forEach( top_level_sibling => {
			if ( hidden_current_section ) return;
			if ( top_level_sibling.classList.contains('fupi_r3_section') && ! top_level_sibling.classList.contains('fupi_hidden') ) {
				top_level_sibling.classList.add('fupi_hidden');
				if ( top_level_sibling == section_with_trigger_el ) hidden_current_section = true;
			}
		})

		if ( hidden_current_section ) return true; // this ensure that we only hide a section if the same btn was clicked multiple times
		
		// show section if it already exists ( with same trigger_id as requested) 
		if ( section_with_trigger_el ) {
			section_with_trigger_el.classList.remove('fupi_hidden');
			fill_event_headline( section_with_trigger_el, trigger_name );
			mark_req_fields( section_with_trigger_el );
		
		// or make one if it doesn't exist
		} else {
			
			if ( first_top_section ) {

				// update it with new data if it is empty
				if ( first_top_section.classList.contains('fupi_empty_r3') ) {

					// clear all fields
					clear_section( first_top_section );
					
					// update name field
					fill_event_headline( first_top_section, trigger_name );

					// update trigger field
					let trigger_id_el = FP.findFirst('input[name*="[atrig_id]"]', first_top_section );
					if ( trigger_id_el ) trigger_id_el.value = trigger_id;

					first_top_section.classList.remove('fupi_hidden');
					mark_req_fields( first_top_section );

				// create one after the existing section
				} else {

					let has_select2 = FP.findFirst( '.ss-main', first_top_section );

					if ( has_select2 ) FupiSlimSelect.destroyRepeaters();
					make_new_section_after( first_top_section, trigger_id, trigger_name ); // clears fields > adds trigger data > marks fields as empty or filled
					order_array_indexes_in_all_sections( td );
					if ( has_select2 ) FupiSlimSelect.initIn();
				}
			}
		}
		
		return true;
	}

	function listen_to_clicks() {

		document.addEventListener('click', e => {

			let stop_checking = handle_remove_btn_with_confirm( e.target );

			// if ( ! stop_checking ) stop_checking = handle_untrack_section_button( e.target );

			if ( ! stop_checking ) stop_checking = handle_plus_section_button( e.target );
			if ( ! stop_checking ) stop_checking = handle_add_trigger_btn( e.target ); // trigger builder > btn below all sections
			if ( ! stop_checking ) stop_checking = handle_minus_section_btn( e.target );

			if ( ! stop_checking ) handle_show_evt_settings_btn( e.target ); // event builder > button with trigger data
		} )
	}

	//
	// HANDLING EMPTY AND REQUIRED FIELDS AND SECTIONS
	//

	function clear_section( section ){

		section.classList.add('fupi_empty_r3');

		FP.findAll('.fupi_empty', section).forEach( field => field.classList.remove('fupi_empty') ); // this will be reapplied with "mark_req_fields"

		// clear values
		FP.findAll('textarea', section).forEach( field => field.value = '' );
		FP.findAll('input', section).forEach( field => {
			if ( field.type !== 'checkbox' && field.type !== 'submit' ) field.value = '' 
		} );
		FP.findAll('input[type="checkbox"]', section).forEach( field => field.checked = false );

		FP.findAll('select', section).forEach( sel => {

			let option_el = FP.findFirst('option', sel);

			if ( sel.dataset.default ) {
				
				sel.value = sel.dataset.default;

			} else {
				if ( sel.multiple ) {
					FP.findAll('option', sel).forEach( option => option.selected = false );
				} else if ( option_el ) {
					sel.value = option_el.value;
				}
			}

			if ( sel.slim ) {
				let selected_values = FP.findAll('option:checked', sel).map( option => option.value );
				sel.slim.setSelected( selected_values, false );
			}

		} );

		// unhide hidden elements
		FP.findAll('.fupi_r3_field.fupi_hidden', section).forEach( field => field.classList.remove('fupi_hidden') );
		FP.findAll('.fupi_r3_field.fupi_disabled', section).forEach( field => field.classList.remove('fupi_disabled') );

		return section;
	}

	//
	// MARKS REQUIRED FIELDS AS EMPTY OR FILLED
	// 

	function listen_to_fields_changes( wrapper ){

		// prevent double-listening to the same wrapper
		if ( wrapper.dataset.r3ChangeListener === 'true' ) return;
		wrapper.dataset.r3ChangeListener = 'true';

		let dynamic_form = wrapper.closest('#fupi_dynamic_settings_form');

		let mark_fields_as_unsaved_and_empty = e => {

			let target = e.target;

			// enable "Save" btn in dynamic form after any change
			if ( dynamic_form && dynamic_form.dataset.unsaved !== 'true' ) {
				dynamic_form.dataset.unsaved = 'true';
				enable_submit_btn( true );
			}

			// continue only for required field
			if ( ! target.classList.contains('fupi_req') ) return;
			
			r3_field_wrap = target.closest('.fupi_r3_field');

			if ( ! r3_field_wrap ) return;

			// mark field as empty or not
			if ( target.value ) {
				r3_field_wrap.classList.remove( 'fupi_empty');
			} else {
				r3_field_wrap.classList.add( 'fupi_empty' );
			}

			let evt_builder_section = r3_field_wrap.closest('.fupi_r3_event_builder');

			// mark evt builder section as empty or not
			if ( evt_builder_section ) {

				let has_empty_fields = !! FP.findFirst( '.fupi_empty .fupi_req', evt_builder_section ),
					trigger_id_el = FP.findFirst( 'input[name*="[atrig_id]"]', evt_builder_section );

				evt_builder_section.classList.toggle( 'fupi_empty_r3', has_empty_fields );

				if ( trigger_id_el ) {

					let evt_open_btn = FP.findFirst( '.fupi_r3_builder_evt_settings_btn[data-trigger_id="' + trigger_id_el.value + '"]' );

					if ( evt_open_btn ) {
						evt_open_btn.classList.toggle( 'fupi_trigger_used_in_evt', ! has_empty_fields );
					}
				}
			}
		}

		wrapper.addEventListener('input', mark_fields_as_unsaved_and_empty );
		wrapper.addEventListener('change', mark_fields_as_unsaved_and_empty );
	}

	// this should be run every time some fields are changed AUTOMATICALLY!
	function mark_req_fields( section ) {
		
		let req_fields = FP.findAll( '.fupi_req', section ),
			has_empty_fields = false;

		req_fields.forEach( req_field => {

			let field_wrap = req_field.closest('.fupi_r3_field');

			if ( ! req_field.value && ! req_field.checked ) {
				has_empty_fields = true;
				if ( ! field_wrap.classList.contains('fupi_empty') ) {
					field_wrap.classList.add('fupi_empty');
				}
			} else {
				field_wrap.classList.remove('fupi_empty');
			}
		})
					
		// mark section as non-empty
		// mark corresponding button (which opens the builder) as green or orange
		if ( ! has_empty_fields ) {
			section.classList.remove('fupi_empty_r3');
		} else {
			section.classList.add('fupi_empty_r3');
		}
	}

	//
	// FOR THE BUILDER SLIDEOUT PANEL
	//

	function get_active_triggers_data(){

		const trigger_sections = FP.findAll('#fupi_dynamic_settings_form .fupi_r3_trigger_builder');

		let data = {};

		trigger_sections.forEach( section => {

			let trigger_name_input = FP.findFirst('input[name*="[name]"]', section ),
				trigger_id_input = FP.findFirst('input.fupi_generate_id', section );

			if ( trigger_name_input.value && trigger_id_input.value ) data[trigger_id_input.value] = trigger_name_input.value;
		})

		return data;
	}

	function get_active_visitor_score_levels(){

		const levels_field = FP.findFirst('#fupi_dynamic_settings_form input[name="fupi_vscor[lead_scoring_levels]"]');
		if ( ! levels_field ) return false;

		const levels = levels_field.value
			.replace(/[^0-9,]/g, '')
			.split(',')
			.map( level => parseInt( level.trim(), 10 ) )
			.filter( level => ! Number.isNaN( level ) );

		return levels.length ? [...new Set(levels)].sort((a, b) => a - b) : [10, 20, 30, 40, 50];
	}

	function sync_builder_events_and_triggers(){

		let builder_btn = FP.findFirst('.fupi_atrig_popup_opener:not(.fupi_not_builder_btn)');

		if ( ! builder_btn ) return;
		
		let td = builder_btn.closest('td'),
			event_builder_sections = FP.findAll( '.fupi_r3_event_builder', td );

		const score_levels = builder_btn.dataset.moduleId == 'vscor' ? get_active_visitor_score_levels() : false;

		let triggers_data_o = score_levels
			? Object.fromEntries( score_levels.map( level => [ 'fp_leadscore_' + level, 'Visitor collects ' + level + ' points' ] ) )
			: get_active_triggers_data(); // returns object with elements "trigger_id" : trigger_name from dynamic settings

		event_builder_sections.forEach( event_section => {

			let event_trigger_id_el = FP.findFirst('input[name*="[atrig_id]"]', event_section ),
				event_trigger_id = event_trigger_id_el.value;

			if ( ! event_trigger_id ) {
				if ( ! event_section.classList.contains('fupi_empty_r3') ) remove_r3_section( event_section );
				return;
			}

			// look for a trigger field with the same ID value
			let trigger_name = triggers_data_o[event_trigger_id];

			// if this trigger exists
			if ( trigger_name ) {
				
				// update name
				fill_event_headline( event_section, trigger_name );

			} else {
				remove_r3_section( event_section );
			}
		})

		// update buttons that open event settings

		let open_evt_settings_btns = FP.findAll( '.fupi_r3_builder_evt_settings_btn', td );

		open_evt_settings_btns.forEach( ( open_evt_btn, i ) => {

			let trigger_id = open_evt_btn.dataset.trigger_id;

			// if trigger exists
			if ( triggers_data_o[trigger_id] ) {
				
				// update btn text
				open_evt_btn.textContent = triggers_data_o[trigger_id];
				
				// remove from triggers_data_o (this lets ut see what triggers to add later)
				delete triggers_data_o[trigger_id];

			// remove it
			} else {
				FP.remove(open_evt_btn);
			}
		})

		// add new options to trigger selector
		for ( const id in triggers_data_o ) {

			// do not use insertAdjacentHTML - trigger names with special characters concatenated into the HTML break the btn
			const button = document.createElement('button');
			button.type = 'button';
			button.className = 'fupi_r3_builder_evt_settings_btn';
			button.dataset.trigger_id = id;
			button.textContent = triggers_data_o[id];
			builder_btn.before(button);
		}
	}

	function manage_empty_sections( td ){

		const first_section = FP.findFirst( '.fupi_r3_section', td ),
			is_event_builder = FP.findFirst( '.fupi_r3_event_builder', td );

		let sibling_sections = FP.getSiblings( first_section ),
			all_sections = sibling_sections.filter(sect => sect.classList.contains('fupi_r3_section')),
			empty_sections = all_sections.filter(sect => sect.classList.contains('fupi_empty_r3')); 
			
			// Sections with "fupi_empty_r3" are either:
			// - the first section of a field with no saved data
			// - sections that are not connected to existing triggers

		// if we have many sections and some of them are empty (but not all!), we remove empty ones
		if ( all_sections.length > empty_sections.length ) {
			empty_sections.forEach( section => FP.remove(section));
			return true; // returns that it changed DOM
		}

		if ( is_event_builder ) {
			
			// if we have multiple sections and all of them are empty, we remove all of them except one
			if ( all_sections.length > 1 && all_sections.length == empty_sections.length ) {
				empty_sections.forEach( (section, i) => {
					if ( i == 0 ) {
						clear_section( section ); // this clears fields that may STILL contain data!
						mark_req_fields( section );
						return false; // returns that it did not change DOM
					} else {
						FP.remove(section);
						return true; // returns that it changed DOM
					}
				})
			}

			// if we have only one section and it is empty, then we make it invisible
			if ( all_sections.length == 1 && empty_sections.length == 1 ) {

				let last_section = all_sections[all_sections.length -1 ]

				last_section.classList.add('fupi_hidden');
				return false;
			}
		}
	}

	//
	// INIT
	//

	FP.f.enableRepeatersIn = ( wrapperSelector ) => {
		
		let wrapper = FP.findFirst( wrapperSelector );
		if ( ! wrapper ) return;

		let td_els_with_repeaters = FP.findAll('td:has(.fupi_r3_repeater)', wrapper);

		td_els_with_repeaters.forEach( td => {
			let changed_DOM = manage_empty_sections( td ); // removes/hides/clears fields that still have some content
			if ( changed_DOM ) order_array_indexes_in_all_sections( td );
		})

		fill_empty_id_fields_with_random_ids();
		FupiSlimSelect.initIn( wrapperSelector );
		listen_to_fields_changes( wrapper );
	};

	FP.f.enableRepeatersIn('#fupi_main_form');
	
	listen_to_clicks();

	// AFTER DYNAMIC SETTINGS LOAD FOR THE FIRST TIME
	document.addEventListener( 'fupi_atrig_panel_loaded', ()=>{
		FP.f.enableRepeatersIn('#fupi_dynamic_settings_form');
	})
	
	// AFTER DYNAMIC SETTINGS ARE SAVED
	document.addEventListener( 'fupi_atrig_panel_form_saved', ()=>{
		sync_builder_events_and_triggers();
		FupiSlimSelect.initIn( '#fupi_dynamic_settings_form' );
	})

})();

(()=>{

    const module_id = fupi_content.dataset.page;

	if ( module_id != 'cscr' ) return;

	function get_attributes_from_str( str ){

		// return an object with key-value pairs
		// string example: 'data-param='abc def ghi' async src="https://www.googletagmanager.com/gtag/js?id=UA-11111111-1" data-param2="abc" id="anID" class="class1 class2"'

		let parts = str.trim().split(' '), // we break the string into parts by 'space' (spaces can occur in unexpected places! see example above)
			ok_parts = parts.filter( v => v ), // and remove empty values from array
			ret_val = {};

		for ( var i = 0; i < ok_parts.length; i++ ) {

			let part = ok_parts[i],
				eq_index = part.indexOf('='),
				last_char = part[part.length-1];

			// if we have an equal sign
			// (this means that the value before the equal sign is the key and the one after is the value)
			if ( eq_index != -1 ){

				// we check if the last element of the current part is an apostrophe. If it isn\'t then it means we split the value in two or more pieces and need to join it with the next string parts
				while ( ! ( last_char == '"' || last_char == "'" ) && i+1 <= ok_parts.length ) {
					part += ' ' + ok_parts[i+1];
					last_char = part[part.length-1];
					i++;
				}

				// if we have the key and the whole value
				if ( last_char == '"' || last_char == "'" ) {
					// we save the key and value pair
					let key_val = part.split(/=(.*)/s); // split on the first occurance of "=" sign. They may sometimes show up in values too
					ret_val[key_val[0]]= key_val[1];
				}

			// if we don't have an equal sign this is a single value
			} else {
				ret_val[part] = true;
			}
		}

		return ret_val;
	}

	function reformat_custom_scripts( textarea ){

        let script = textarea.value;

        if ( script.length == 0 ) return;

		// remove all ending "</script>" tags
		script = script.replaceAll('</script>', '');

		// remove all HTML comments including everything between <!-- and -->
		script = script.replaceAll(/<!--/g, '//');
		script = script.replaceAll(/-->/g, '');

		// remove noscript tags with everything in between
		script = script.replaceAll(/<noscript>[\s\S]*?<\/noscript>/g, '');

		// find all <script[something or nothing]> tags
		let start_regex = /<script[\s\S]*?>/gi,
			matches = script.match(start_regex),
			replacements = [];

		// get their attributes
		if ( Array.isArray( matches ) && matches.length > 0 ) matches.forEach( txt => {

			// remove "<script" and final ">"
			txt = txt.replace('<script','');
			txt = txt.slice(0, -1);
			txt = txt.trim();

			if ( txt.length == 0 ) {

				replacements.push('');

			} else {

				// try to transform the "<script [attributes]>" into "getScript()"
				let attributes_obj = get_attributes_from_str(txt),
					src = attributes_obj.src ? attributes_obj.src : false,
					attrs_str = '';

				if ( ! src ) {
					// Remove the whole <script> tag
					replacements.push('');
				} else {
					// Build string with attributes
					for (const [key, val] of Object.entries(attributes_obj)) {
						if ( key != 'src' ) {
							if ( attrs_str.length > 1 ) attrs_str += ", ";
							attrs_str += '"' + key + '": ' + val;
						}
					}
	
					replacements.push([src, attrs_str]);
				}
			}
		} );

		// prepare elements to replace <string [something or nothing]> in the original script
		replacements.forEach( ( repl, i ) =>{
			if ( repl ) replacements[i] = 'FP.getScript(' + repl[0] + ', false, {' + repl[1] + '});'; // repl[0] & repl[1] are already properly formatted
		} )

		// replace elements in string one by one

		let i = 0;

		textarea.value = script.replace(start_regex, ()=>{ return replacements[i++]; });
	}

	// INIT
	
	document.addEventListener( 'focusout', e => {

        let target = e.target,
            is_cscr_textarea = target.tagName == 'TEXTAREA' && target.id && target.id.includes('fupi_cscr') && target.id.includes('[scr]');

        if ( ! is_cscr_textarea ) return;
        
        reformat_custom_scripts( target ); // e.target.value = e.target.value.replaceAll(/<.*?script[\s\S]*?>/g, '');
        
    } )
})();
;(()=>{

	// format: KEY
	// document.addEventListener('focusout', e=>{
	// 	if ( e.target.dataset.dataformat && e.target.dataset.dataformat == 'key' ){
	// 		console.log('focusout !');
	// 	}
	// })

	document.addEventListener('keyup', e=>{
		if ( e.target.dataset.dataformat && e.target.dataset.dataformat == 'key' ){
			let reg = /^\d/, // digit at the beginning
				reg2 = /[^\w]/gi, // not a special char or underscore
				txt = e.target.value;

			txt = txt.replace(reg,'');
			txt = txt.replace(reg2,'');

			e.target.value = txt;
		}
	})
})();


(()=>{

	const fupi_content = FP.findID('fupi_content'),
		offscreen = FP.findID('fupi_offscreen'),
		offscreen_content_el = FP.findID('fupi_offscreen_content'),
		offscreen_close_btn = FP.findID('fupi_offscreen_close_btn'),
		content_els = FP.findAll('table.form-table .fupi_offscreen_source:not(.fupi_do_not_create_offscreen_icon)');

	let current_offscreen_source = false;

	function add_content_to_offscreen() {
		if ( ! current_offscreen_source || ! current_offscreen_source.classList.contains('fupi_offscreen_source') ) return;

		offscreen.dataset.contentId = current_offscreen_source.id;
		offscreen_content_el.innerHTML = current_offscreen_source.innerHTML;
	}

	function show_offscreen() {
		offscreen.classList.add('fupi_active');
		offscreen.dataset.style = current_offscreen_source.dataset.style || '';
	}

	function hide_offscreen() {
		offscreen.dataset.contentId = '';
		offscreen.dataset.style = '';
		offscreen.classList.remove('fupi_active');

		const youtube_iframe = FP.findFirst('.fupi_video iframe');
		if ( youtube_iframe ) {
			youtube_iframe.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*');
		}
	}

	function create_offscreen_icons() {
		content_els.forEach( el => {
			const tr = el.closest('tr'),
				th = FP.findFirst('th', tr),
				status_class = el.classList.contains('fupi_offscreen_warning') ? 'fupi_offscreen_warning' : el.classList.contains('fupi_offscreen_important') ? 'fupi_offscreen_important' : '';

			th.insertAdjacentHTML('beforeend', '<button type="button" class="fupi_open_offscreen fupi_open_offscreen_i ' + status_class + '" data-offscreen="' + el.id + '">?</button>');
		});

		FP.findAll('.fupi_create_offscreen_link').forEach( el => {
			const tr = el.closest('tr'),
				th = FP.findFirst('th', tr),
				status_class = el.classList.contains('fupi_offscreen_warning') ? 'fupi_offscreen_warning' : el.classList.contains('fupi_offscreen_important') ? 'fupi_offscreen_important' : '';

			th.insertAdjacentHTML('beforeend', '<button type="button" class="fupi_open_offscreen fupi_open_offscreen_i ' + status_class + '" data-offscreen="' + el.dataset.offscreenId + '">?</button>');
		});
	}

	create_offscreen_icons();

	document.addEventListener( 'click', e => {
		const offscreen_btn = e.target.closest('.fupi_open_offscreen');
		if ( ! offscreen_btn ) return;

		const offscreen_id = offscreen_btn.dataset.offscreen;
		current_offscreen_source = FP.findID( offscreen_id );
		if ( ! current_offscreen_source ) return;

		if ( offscreen.dataset.contentId !== offscreen_id ) {
			add_content_to_offscreen();
			show_offscreen();
		} else {
			hide_offscreen();
		}
	});

	const cookie_offscreen_id = FP.readCookie('fupi_admin_open_offscreen');
	if ( cookie_offscreen_id ) {
		current_offscreen_source = FP.findID( cookie_offscreen_id );
		if ( current_offscreen_source ) {
			add_content_to_offscreen();
			show_offscreen();
			FP.deleteCookie('fupi_admin_open_offscreen');
		}
	}

	if ( offscreen_close_btn ) offscreen_close_btn.addEventListener( 'click', hide_offscreen );

})();

(()=>{

	// INCREASE / DECREASE VALUE IN THE "NUMBER" FIELD AFTER CLICKS IN "PLUS" AND "MINUS" BUTTONS

	function changeNumber( action, btn ){

		let num_field = FP.findFirst( 'input[type="number"]', btn.parentElement.parentElement ),
			old_val = parseInt( num_field.value || 0 ),
			min = num_field.min ? parseInt( num_field.min ) : -999;

		if ( action == 'plus' ){
			num_field.value = old_val + ( num_field.step || 1 );
		} else {
			let new_val = old_val - ( num_field.step || 1 );
			num_field.value = new_val < min ? min : new_val;
		}

		num_field.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	}

	document.addEventListener('click', e=>{
		
		let btn = e.target;

		if ( btn.closest('.fupi_number_increase') ) {
			changeNumber( 'plus', btn );
		} else if ( btn.closest('.fupi_number_decrease') ) {
			changeNumber( 'minus', btn );
		};
	})
})();

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

/**
 * Copyright Marc J. Schmidt. See the LICENSE file at the top-level
 * directory of this distribution and at
 * https://github.com/marcj/css-element-queries/blob/master/LICENSE.
 */
;
(function() {

    /**
     * Class for dimension change detection.
     *
     * @param {Element|Element[]|Elements|jQuery} element
     * @param {Function} callback
     *
     * @constructor
     */
    var ResizeSensor = function(element, callback) {
        /**
         *
         * @constructor
         */
        function EventQueue() {
            this.q = [];
            this.add = function(ev) {
                this.q.push(ev);
            };

            var i, j;
            this.call = function() {
                for (i = 0, j = this.q.length; i < j; i++) {
                    this.q[i].call();
                }
            };
        }

        /**
         * @param {HTMLElement} element
         * @param {String}      prop
         * @returns {String|Number}
         */
        function getComputedStyle(element, prop) {
            if (element.currentStyle) {
                return element.currentStyle[prop];
            } else if (window.getComputedStyle) {
                return window.getComputedStyle(element, null).getPropertyValue(prop);
            } else {
                return element.style[prop];
            }
        }

        /**
         *
         * @param {HTMLElement} element
         * @param {Function}    resized
         */
        function attachResizeEvent(element, resized) {
            if (!element.resizedAttached) {
                element.resizedAttached = new EventQueue();
                element.resizedAttached.add(resized);
            } else if (element.resizedAttached) {
                element.resizedAttached.add(resized);
                return;
            }

            element.resizeSensor = document.createElement('div');
            element.resizeSensor.className = 'resize-sensor';
            var style = 'position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; z-index: -1; visibility: hidden;';
            var styleChild = 'position: absolute; left: 0; top: 0; transition: 0s;';

            element.resizeSensor.style.cssText = style;
            element.resizeSensor.innerHTML =
                '<div class="resize-sensor-expand" style="' + style + '">' +
                    '<div style="' + styleChild + '"></div>' +
                '</div>' +
                '<div class="resize-sensor-shrink" style="' + style + '">' +
                    '<div style="' + styleChild + ' width: 200%; height: 200%"></div>' +
                '</div>';
            element.appendChild(element.resizeSensor);

            if (!{fixed: 1, absolute: 1}[getComputedStyle(element, 'position')]) {
                element.style.position = 'relative';
            }

            var expand = element.resizeSensor.childNodes[0];
            var expandChild = expand.childNodes[0];
            var shrink = element.resizeSensor.childNodes[1];
            var shrinkChild = shrink.childNodes[0];

            var lastWidth, lastHeight;

            var reset = function() {
                expandChild.style.width = expand.offsetWidth + 10 + 'px';
                expandChild.style.height = expand.offsetHeight + 10 + 'px';
                expand.scrollLeft = expand.scrollWidth;
                expand.scrollTop = expand.scrollHeight;
                shrink.scrollLeft = shrink.scrollWidth;
                shrink.scrollTop = shrink.scrollHeight;
                lastWidth = element.offsetWidth;
                lastHeight = element.offsetHeight;
            };

            reset();

            var changed = function() {
                if (element.resizedAttached) {
                    element.resizedAttached.call();
                }
            };

            var addEvent = function(el, name, cb) {
                if (el.attachEvent) {
                    el.attachEvent('on' + name, cb);
                } else {
                    el.addEventListener(name, cb);
                }
            };

            var onScroll = function() {
              if (element.offsetWidth != lastWidth || element.offsetHeight != lastHeight) {
                  changed();
              }
              reset();
            };

            addEvent(expand, 'scroll', onScroll);
            addEvent(shrink, 'scroll', onScroll);
        }

        var elementType = Object.prototype.toString.call(element);
        var isCollectionTyped = ('[object Array]' === elementType
            || ('[object NodeList]' === elementType)
            || ('[object HTMLCollection]' === elementType)
            || ('undefined' !== typeof jQuery && element instanceof jQuery) //jquery
            || ('undefined' !== typeof Elements && element instanceof Elements) //mootools
        );

        if (isCollectionTyped) {
            var i = 0, j = element.length;
            for (; i < j; i++) {
                attachResizeEvent(element[i], callback);
            }
        } else {
            attachResizeEvent(element, callback);
        }

        this.detach = function() {
            if (isCollectionTyped) {
                var i = 0, j = element.length;
                for (; i < j; i++) {
                    ResizeSensor.detach(element[i]);
                }
            } else {
                ResizeSensor.detach(element);
            }
        };
    };

    ResizeSensor.detach = function(element) {
        if (element.resizeSensor) {
            element.removeChild(element.resizeSensor);
            delete element.resizeSensor;
            delete element.resizedAttached;
        }
    };

    // make available to common module loader
    if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
        module.exports = ResizeSensor;
    }
    else {
        window.ResizeSensor = ResizeSensor;
    }

})();

/*!
 * Theia Sticky Sidebar v1.7.0
 * https://github.com/WeCodePixels/theia-sticky-sidebar
 *
 * Glues your website's sidebars, making them permanently visible while scrolling.
 *
 * Copyright 2013-2016 WeCodePixels and other contributors
 * Released under the MIT license
 */

(function ($) {
    $.fn.theiaStickySidebar = function (options) {
        var defaults = {
            'containerSelector': '',
            'additionalMarginTop': 0,
            'additionalMarginBottom': 0,
            'updateSidebarHeight': true,
            'minWidth': 0,
            'disableOnResponsiveLayouts': true,
            'sidebarBehavior': 'modern',
            'defaultPosition': 'relative',
            'namespace': 'TSS'
        };
        options = $.extend(defaults, options);

        // Validate options
        options.additionalMarginTop = parseInt(options.additionalMarginTop) || 0;
        options.additionalMarginBottom = parseInt(options.additionalMarginBottom) || 0;

        tryInitOrHookIntoEvents(options, this);

        // Try doing init, otherwise hook into window.resize and document.scroll and try again then.
        function tryInitOrHookIntoEvents(options, $that) {
            var success = tryInit(options, $that);

            if (!success) {
                console.log('TSS: Body width smaller than options.minWidth. Init is delayed.');

                $(document).on('scroll.' + options.namespace, function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that));
                $(window).on('resize.' + options.namespace, function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that))
            }
        }

        // Try doing init if proper conditions are met.
        function tryInit(options, $that) {
            if (options.initialized === true) {
                return true;
            }

            if ($('body').width() < options.minWidth) {
                return false;
            }

            init(options, $that);

            return true;
        }

        // Init the sticky sidebar(s).
        function init(options, $that) {
            options.initialized = true;

            // Add CSS
            var existingStylesheet = $('#theia-sticky-sidebar-stylesheet-' + options.namespace);
            if (existingStylesheet.length === 0) {
                $('head').append($('<style id="theia-sticky-sidebar-stylesheet-' + options.namespace + '">.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>'));
            }

            $that.each(function () {
                var o = {};

                o.sidebar = $(this);

                // Save options
                o.options = options || {};

                // Get container
                o.container = $(o.options.containerSelector);
                if (o.container.length == 0) {
                    o.container = o.sidebar.parent();
                }

                // Create sticky sidebar
                o.sidebar.parents().css('-webkit-transform', 'none'); // Fix for WebKit bug - https://code.google.com/p/chromium/issues/detail?id=20574
                o.sidebar.css({
                    'position': o.options.defaultPosition,
                    'overflow': 'visible',
                    // The "box-sizing" must be set to "content-box" because we set a fixed height to this element when the sticky sidebar has a fixed position.
                    '-webkit-box-sizing': 'border-box',
                    '-moz-box-sizing': 'border-box',
                    'box-sizing': 'border-box'
                });

                // Get the sticky sidebar element. If none has been found, then create one.
                o.stickySidebar = o.sidebar.find('.theiaStickySidebar');
                if (o.stickySidebar.length == 0) {
                    // Remove <script> tags, otherwise they will be run again when added to the stickySidebar.
                    var javaScriptMIMETypes = /(?:text|application)\/(?:x-)?(?:javascript|ecmascript)/i;
                    o.sidebar.find('script').filter(function (index, script) {
                        return script.type.length === 0 || script.type.match(javaScriptMIMETypes);
                    }).remove();

                    o.stickySidebar = $('<div>').addClass('theiaStickySidebar').append(o.sidebar.children());
                    o.sidebar.append(o.stickySidebar);
                }

                // Get existing top and bottom margins and paddings
                o.marginBottom = parseInt(o.sidebar.css('margin-bottom'));
                o.paddingTop = parseInt(o.sidebar.css('padding-top'));
                o.paddingBottom = parseInt(o.sidebar.css('padding-bottom'));

                // Add a temporary padding rule to check for collapsable margins.
                var collapsedTopHeight = o.stickySidebar.offset().top;
                var collapsedBottomHeight = o.stickySidebar.outerHeight();
                o.stickySidebar.css('padding-top', 1);
                o.stickySidebar.css('padding-bottom', 1);
                collapsedTopHeight -= o.stickySidebar.offset().top;
                collapsedBottomHeight = o.stickySidebar.outerHeight() - collapsedBottomHeight - collapsedTopHeight;
                if (collapsedTopHeight == 0) {
                    o.stickySidebar.css('padding-top', 0);
                    o.stickySidebarPaddingTop = 0;
                }
                else {
                    o.stickySidebarPaddingTop = 1;
                }

                if (collapsedBottomHeight == 0) {
                    o.stickySidebar.css('padding-bottom', 0);
                    o.stickySidebarPaddingBottom = 0;
                }
                else {
                    o.stickySidebarPaddingBottom = 1;
                }

                // We use this to know whether the user is scrolling up or down.
                o.previousScrollTop = null;

                // Scroll top (value) when the sidebar has fixed position.
                o.fixedScrollTop = 0;

                // Set sidebar to default values.
                resetSidebar();

                o.onScroll = function (o) {
                    // Stop if the sidebar isn't visible.
                    if (!o.stickySidebar.is(":visible")) {
                        return;
                    }

                    // Stop if the window is too small.
                    if ($('body').width() < o.options.minWidth) {
                        resetSidebar();
                        return;
                    }

                    // Stop if the sidebar width is larger than the container width (e.g. the theme is responsive and the sidebar is now below the content)
                    if (o.options.disableOnResponsiveLayouts) {
                        var sidebarWidth = o.sidebar.outerWidth(o.sidebar.css('float') == 'none');

                        if (sidebarWidth + 50 > o.container.width()) {
                            resetSidebar();
                            return;
                        }
                    }

                    var scrollTop = $(document).scrollTop();
                    var position = 'static';

                    // If the user has scrolled down enough for the sidebar to be clipped at the top, then we can consider changing its position.
                    if (scrollTop >= o.sidebar.offset().top + (o.paddingTop - o.options.additionalMarginTop)) {
                        // The top and bottom offsets, used in various calculations.
                        var offsetTop = o.paddingTop + options.additionalMarginTop;
                        var offsetBottom = o.paddingBottom + o.marginBottom + options.additionalMarginBottom;

                        // All top and bottom positions are relative to the window, not to the parent elemnts.
                        var containerTop = o.sidebar.offset().top;
                        var containerBottom = o.sidebar.offset().top + getClearedHeight(o.container);

                        // The top and bottom offsets relative to the window screen top (zero) and bottom (window height).
                        var windowOffsetTop = 0 + options.additionalMarginTop;
                        var windowOffsetBottom;

                        var sidebarSmallerThanWindow = (o.stickySidebar.outerHeight() + offsetTop + offsetBottom) < $(window).height();
                        if (sidebarSmallerThanWindow) {
                            windowOffsetBottom = windowOffsetTop + o.stickySidebar.outerHeight();
                        }
                        else {
                            windowOffsetBottom = $(window).height() - o.marginBottom - o.paddingBottom - options.additionalMarginBottom;
                        }

                        var staticLimitTop = containerTop - scrollTop + o.paddingTop;
                        var staticLimitBottom = containerBottom - scrollTop - o.paddingBottom - o.marginBottom;

                        var top = o.stickySidebar.offset().top - scrollTop;
                        var scrollTopDiff = o.previousScrollTop - scrollTop;

                        // If the sidebar position is fixed, then it won't move up or down by itself. So, we manually adjust the top coordinate.
                        if (o.stickySidebar.css('position') == 'fixed') {
                            if (o.options.sidebarBehavior == 'modern') {
                                top += scrollTopDiff;
                            }
                        }

                        if (o.options.sidebarBehavior == 'stick-to-top') {
                            top = options.additionalMarginTop;
                        }

                        if (o.options.sidebarBehavior == 'stick-to-bottom') {
                            top = windowOffsetBottom - o.stickySidebar.outerHeight();
                        }

                        if (scrollTopDiff > 0) { // If the user is scrolling up.
                            top = Math.min(top, windowOffsetTop);
                        }
                        else { // If the user is scrolling down.
                            top = Math.max(top, windowOffsetBottom - o.stickySidebar.outerHeight());
                        }

                        top = Math.max(top, staticLimitTop);

                        top = Math.min(top, staticLimitBottom - o.stickySidebar.outerHeight());

                        // If the sidebar is the same height as the container, we won't use fixed positioning.
                        var sidebarSameHeightAsContainer = o.container.height() == o.stickySidebar.outerHeight();

                        if (!sidebarSameHeightAsContainer && top == windowOffsetTop) {
                            position = 'fixed';
                        }
                        else if (!sidebarSameHeightAsContainer && top == windowOffsetBottom - o.stickySidebar.outerHeight()) {
                            position = 'fixed';
                        }
                        else if (scrollTop + top - o.sidebar.offset().top - o.paddingTop <= options.additionalMarginTop) {
                            // Stuck to the top of the page. No special behavior.
                            position = 'static';
                        }
                        else {
                            // Stuck to the bottom of the page.
                            position = 'absolute';
                        }
                    }

                    /*
                     * Performance notice: It's OK to set these CSS values at each resize/scroll, even if they don't change.
                     * It's way slower to first check if the values have changed.
                     */
                    if (position == 'fixed') {
                        var scrollLeft = $(document).scrollLeft();

                        o.stickySidebar.css({
                            'position': 'fixed',
                            'width': getWidthForObject(o.stickySidebar) + 'px',
                            'transform': 'translateY(' + top + 'px)',
                            'left': (o.sidebar.offset().left + parseInt(o.sidebar.css('padding-left')) - scrollLeft) + 'px',
                            'top': '0px'
                        });
                    }
                    else if (position == 'absolute') {
                        var css = {};

                        if (o.stickySidebar.css('position') != 'absolute') {
                            css.position = 'absolute';
                            css.transform = 'translateY(' + (scrollTop + top - o.sidebar.offset().top - o.stickySidebarPaddingTop - o.stickySidebarPaddingBottom) + 'px)';
                            css.top = '0px';
                        }

                        css.width = getWidthForObject(o.stickySidebar) + 'px';
                        css.left = '';

                        o.stickySidebar.css(css);
                    }
                    else if (position == 'static') {
                        resetSidebar();
                    }

                    if (position != 'static') {
                        if (o.options.updateSidebarHeight == true) {
                            o.sidebar.css({
                                'min-height': o.stickySidebar.outerHeight() + o.stickySidebar.offset().top - o.sidebar.offset().top + o.paddingBottom
                            });
                        }
                    }

                    o.previousScrollTop = scrollTop;
                };

                // Initialize the sidebar's position.
                o.onScroll(o);

                // Recalculate the sidebar's position on every scroll and resize.
                $(document).on('scroll.' + o.options.namespace, function (o) {
                    return function () {
                        o.onScroll(o);
                    };
                }(o));
                $(window).on('resize.' + o.options.namespace, function (o) {
                    return function () {
                        o.stickySidebar.css({'position': 'static'});
                        o.onScroll(o);
                    };
                }(o));

                // Recalculate the sidebar's position every time the sidebar changes its size.
                if (typeof ResizeSensor !== 'undefined') {
                    new ResizeSensor(o.stickySidebar[0], function (o) {
                        return function () {
                            o.onScroll(o);
                        };
                    }(o));
                }

                // Reset the sidebar to its default state
                function resetSidebar() {
                    o.fixedScrollTop = 0;
                    o.sidebar.css({
                        'min-height': '1px'
                    });
                    o.stickySidebar.css({
                        'position': 'static',
                        'width': '',
                        'transform': 'none'
                    });
                }

                // Get the height of a div as if its floated children were cleared. Note that this function fails if the floats are more than one level deep.
                function getClearedHeight(e) {
                    var height = e.height();

                    e.children().each(function () {
                        height = Math.max(height, $(this).height());
                    });

                    return height;
                }
            });
        }

        function getWidthForObject(object) {
            var width;

            try {
                width = object[0].getBoundingClientRect().width;
            }
            catch (err) {
            }

            if (typeof width === "undefined") {
                width = object.width();
            }

            return width;
        }

        return this;
    }
})(jQuery);

jQuery(document).ready(function() {
    jQuery('#fupi_nav_col').theiaStickySidebar({
        additionalMarginTop: 50,
        additionalMarginBottom : 50,
        minWidth: 1100
    });
});