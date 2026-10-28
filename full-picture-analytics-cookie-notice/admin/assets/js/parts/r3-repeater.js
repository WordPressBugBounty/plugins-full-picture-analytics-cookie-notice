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
