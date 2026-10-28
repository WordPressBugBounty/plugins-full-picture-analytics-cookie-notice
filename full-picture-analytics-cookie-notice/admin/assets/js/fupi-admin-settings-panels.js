(() => {
	
	const panel_class = 'fupi_settings_panel';
	const panel_active_class = 'fupi_settings_panel_active';

	let active_panel = false;
	let btn_orig_text = '';

	function get_dynamic_field_ids( button ) {
		return ( button.dataset.field_ids || '' ).split(',').map( field_id => field_id.trim() ).filter( Boolean );
	}

	function get_saved_metadata( panel_content ) {

		const metadata = new Map();
		const id_fields = panel_content.querySelectorAll('input[name^="fupi_track[custom_data_ids]"][name$="[id]"]');

		id_fields.forEach( id_field => {

			const section = id_field.closest('.fupi_r3_section');
			if ( ! section || ! id_field.value ) return;

			const name_field = section.querySelector('input[name$="[name]"]');
			const meta_field = section.querySelector('select[name$="[meta]"]');
			const meta_type = meta_field ? meta_field.value : 'post';
			const value = meta_type == 'user' || meta_type == 'term' ? meta_type + '|' + id_field.value : id_field.value;
			const type_label = meta_type == 'user' ? 'User meta' : meta_type == 'term' ? 'Term meta' : 'Post meta';
			const name = name_field && name_field.value ? name_field.value : id_field.value;

			metadata.set( value, '(' + type_label + ') ' + name );
		});

		return metadata;
	}

	function disable_selected_metadata_options( row ) {

		const selects = FP.findAll('.fupi_r3_addMetadata_builder select', row);
		const selected_values = new Set();

		selects.forEach( select => {
			if ( ! select.value ) return;
			if ( selected_values.has( select.value ) ) {
				select.value = '';
			} else {
				selected_values.add( select.value );
			}
		});

		selects.forEach( select => {
			FP.findAll('option', select).forEach( option => {
				option.disabled = !! option.value && option.value !== select.value && selected_values.has( option.value );
			});
		});
	}

	function disable_all_selected_metadata_options() {
		const rows = new Set();

		FP.findAll('.fupi_r3_addMetadata_builder').forEach( section => {
			const row = section.closest('tr');
			if ( row ) rows.add( row );
		});

		rows.forEach( disable_selected_metadata_options );
	}

	function sync_metadata_fields( panel_content ) {

		const panel = panel_content.closest( '.' + panel_class );
		if ( ! panel || panel.dataset.moduleId !== 'track' || ! ( panel.dataset.fieldIds || '' ).split(',').map( id => id.trim() ).includes('custom_data_ids') ) return;

		const open_button = FP.findFirst('.fupi_addEdit_metadata_btn.fupi_atrig_popup_opener');
		const row = open_button ? open_button.closest('tr') : false;
		if ( ! row ) return;

		const metadata = get_saved_metadata( panel_content );

		FP.findAll('.fupi_r3_addMetadata_builder', row).forEach( section => {
			const select = FP.findFirst('select', section);
			if ( ! select || ! select.value || metadata.has( select.value ) ) return;

			const remove_button = FP.findFirst('.fupi_r3_remove_btn', section);
			if ( remove_button ) remove_button.click();
		});

		FP.findAll('.fupi_r3_addMetadata_builder select', row).forEach( select => {

			const selected_value = select.value;
			const empty_option = FP.findFirst('option[value=""]', select);

			select.replaceChildren();
			if ( empty_option ) select.appendChild( empty_option );

			metadata.forEach( ( label, value ) => {
				const option = document.createElement('option');
				option.value = value;
				option.textContent = label;
				option.selected = value === selected_value;
				select.appendChild( option );
			});
		});

		disable_selected_metadata_options( row );
	}

	function sync_dependent_fields( panel_content ) {

		const panel = panel_content.closest( '.' + panel_class );
		if ( ! panel ) return;

		[
			['main','geo'],
			['track','show_author_id']
		].forEach( dependency_fields => {

			const dependency_module_id = dependency_fields[0],
				dependency_field_id = dependency_fields[1];

			const important_field = panel_content.querySelector('[name="fupi_' +  dependency_module_id + '[' + dependency_field_id + ']"]');
			if ( ! important_field ) return;
			
			let fn_enabled = false;
			if ( important_field.tagName == 'INPUT' && important_field.type == 'checkbox' ) {
				fn_enabled = important_field.checked;
			} else {
				fn_enabled = !! important_field.value;
			}

			FP.findAll('.fupi_open_dynamic_settings').forEach( button => {
				
				if ( button.dataset.moduleId !== dependency_module_id || ! get_dynamic_field_ids( button ).includes( dependency_field_id ) ) return;

				const field_wrap = button.closest('.fupi_r3_field, tr');
				if ( ! field_wrap ) return;
				
				field_wrap.classList.toggle('fupi_missing_must_have', ! fn_enabled);
				field_wrap.classList.toggle('fupi_disabled', ! fn_enabled);
				FP.findAll('input, select, textarea', field_wrap).forEach( field => field.disabled = ! fn_enabled );

				// remove must have info
				let must_have_info = FP.findFirst('.fupi_must_have_info', field_wrap);
				if ( must_have_info ) FP.remove( must_have_info );
			});
			
		} )
	}

	//
	// LOAD FORM INTO THE PANEL'S CONTENT
	//
	
	async function load_field_form( module_id, field_ids, panel_content, successMessage, is_after_save = false ) {

		const body = new URLSearchParams({
			action: 'fupi_load_field_form',
			security: fupi_ajax_obj.nonce,
			module_id: module_id,
			field_ids: field_ids
		});

		try {
			const response = await fetch(fupi_ajax_obj.ajax_url, {
				method: 'POST',
				body
			});

			if ( ! response.ok ) throw new Error('Failed to load settings.');

			panel_content.innerHTML = await response.text();

			if ( successMessage ) {
				const message = document.createElement('div');
				message.className = 'fupi_atrig_panel_message fupi_message_success';
				message.innerHTML = '<p>' + successMessage + '</p>';
				const form = panel_content.querySelector('form');
				if (form) form.insertBefore(message, form.firstChild);
			}

			enable_ajax_form_submission(panel_content);

			if ( is_after_save ) {
				
				let submit_btn = FP.findFirst('.fupi_settings_panel_form_submit_btn' );
				submit_btn.disabled = true;

				sync_dependent_fields( panel_content );
				sync_metadata_fields( panel_content );
				
				FP.initCondFields( '#fupi_dynamic_settings_form' );
				FP.disableFieldsForPlans();

				FP.sendEvt('fupi_atrig_panel_form_saved'); // triggers event builder actions in r3-repeater.js

			} else {
				
				FP.initCondFields( '#fupi_dynamic_settings_form' );
				FP.disableFieldsForPlans();

				FP.sendEvt( 'fupi_atrig_panel_loaded' ); // initiates event builder functions in r3-repeater.js
			}
		} catch ( error ) {
			panel_content.innerHTML = '<p>Error loading settings.</p>';
		}
	}

	async function load_gdpr_helper( panel_content ) {
		const body = new URLSearchParams({
			action: 'fupi_load_gdpr_helper',
			security: fupi_ajax_obj.nonce
		});

		try {
			const response = await fetch(fupi_ajax_obj.ajax_url, {
				method: 'POST',
				body
			});

			if ( ! response.ok ) throw new Error('Failed to load GDPR setup helper.');

			panel_content.innerHTML = await response.text();
		} catch ( error ) {
			panel_content.innerHTML = '<p>Error loading GDPR setup helper.</p>';
		}
	}

	function show_loading_spinner_above_form( form ){
		form.style.opacity = '0.5';
		form.style.position = 'relative';
		const spinner = document.createElement('div');
		spinner.className = 'fupi_form_loading_spinner';
		spinner.innerHTML = '<div class="fupi_loading_spinner"><div></div></div>';
		form.appendChild(spinner);
		return spinner;
	}

	function enable_ajax_form_submission( panel_content ) {
		
		const form = panel_content.querySelector('form');
		if ( ! form ) return;

		form.addEventListener('submit', async function(e) {
			
			e.preventDefault();
			
			// Get form data and update it
			const formData 	= new FormData(form);
			
			formData.append('action', 'fupi_save_field_settings');
			formData.append('security', fupi_ajax_obj.nonce);
			
			// Change text in the submit button 

			const submitBtn = form.querySelector('.fupi_settings_panel_form_submit_btn');

			if ( submitBtn ) {
				submitBtn.disabled = true;
				btn_orig_text = submitBtn.textContent;
				submitBtn.textContent = 'Saving...';
			}

			const spinner = show_loading_spinner_above_form(form);

			// Save form
			try {
				
				const response = await fetch(fupi_ajax_obj.ajax_url, {
					method: 'POST',
					body: formData
				});

				const data = await response.json();

				// On success
				if ( data.success ) {
				
					// Reload form content (message will come from server response)
					const panel = form.closest( '.' + panel_class );
					if ( panel ) {
						const moduleId = panel.dataset.moduleId;
						const fieldIds = panel.dataset.fieldIds;
						const content = panel.querySelector( '.fupi_settings_panel_content' );
						if ( moduleId && fieldIds && content ) {
							load_field_form( moduleId, fieldIds, content, data.data.message, true );
						}
					}

				// on error
				} else {

					// Server returned error - throw to trigger catch block
					throw new Error(data.data?.message || 'Save failed');
				}
			
			// On error
			} catch (error) {

				// Remove loading overlay and restore opacity
				form.style.opacity = '1';
				if (spinner.parentNode) spinner.parentNode.removeChild(spinner);

				// Show error notice on network or server failure
				
				const message = document.createElement('div');
				message.className = 'notice notice-error is-dismissible';
				message.innerHTML = '<p>' + ( error.message || 'Save failed' ) + '</p>';
				
				form.insertBefore( message, form.firstChild );
				
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = btn_orig_text;
				}
			}
		});
	}

	//
	// CREATE / DESTROY PANEL
	//

	function create_panel_html( module_id, field_ids ) {

		// make panel
		const panel = document.createElement('div');
		panel.className = panel_class;
		panel.dataset.moduleId = module_id;
		panel.dataset.fieldIds = field_ids || '';

		// make overlay
		const overlay = document.createElement('div');
		overlay.className = 'fupi_settings_panel_overlay';

		// make wrap
		const wrap = document.createElement('div');
		wrap.className = 'fupi_settings_panel_wrap';

		// make close btn (x in the corder)
		const closeBtn = document.createElement('button');
		closeBtn.type = 'button';
		closeBtn.className = 'fupi_settings_panel_x_btn fupi_settings_panel_close_btn';
		closeBtn.innerHTML = '<span class="dashicons dashicons-no-alt"></span>';

		// make content wrap
		const content = document.createElement('div');
		content.className = 'fupi_settings_panel_content';

		// piece it together
		wrap.appendChild(closeBtn);
		wrap.appendChild(content);
		panel.appendChild(overlay);
		panel.appendChild(wrap);

		// add to #fupi_content if available, otherwise to body
		const fupi_content = document.getElementById('fupi_content');
		if ( fupi_content ) {
			fupi_content.appendChild(panel);
		} else {
			document.body.appendChild(panel);
		}

		// Load form fields
		if ( field_ids ) {
			content.innerHTML = '<div class="fupi_loading_spinner"><div></div></div>';
			load_field_form( module_id, field_ids, content );
		}

		return panel;
	}

	// Closes the active panel with optional unsaved changes confirmation, then removes it from DOM
	function destroy_panel(panel) {

		if ( ! panel || ! panel.classList.contains( panel_class ) ) return false;

		let form = FP.findFirst('form', panel);

		// Prompt user if there are unsaved changes
		if ( form && form.dataset.unsaved === 'true' ) { // added in r3-repeater.js
			const confirmation = confirm(fupi_ajax_obj.i18n.unsaved_confirm);
			if ( ! confirmation ) return false;
		}

		panel.classList.remove(panel_active_class); // for the slideout animation
		if ( active_panel === panel ) active_panel = false;

		// remove class that marks btn which opened panel
		let btn_which_opened_opup = FP.findFirst( '.fupi_atrig_popup_opener' );
		if ( btn_which_opened_opup ) btn_which_opened_opup.classList.remove( 'fupi_atrig_popup_opener' );

		// Remove after animation completes
		setTimeout(() => {
			if (panel.parentNode) {
				panel.parentNode.removeChild(panel);
			}
		}, 220);
		
		return true;
	}

	function create_panel( module_id, field_ids ) {

		if ( active_panel && ! destroy_panel(active_panel) ) return;

		const panel = create_panel_html(module_id, field_ids);
		active_panel = panel;

		// Use rAF to ensure CSS transition triggers after class is added
		requestAnimationFrame(() => {
			panel.classList.add(panel_active_class);
		});
	}

	function create_gdpr_helper_panel() {

		if ( active_panel && ! destroy_panel(active_panel) ) return;

		const panel = create_panel_html( 'gdpr-helper', '' );
		const content = panel.querySelector( '.fupi_settings_panel_content' );
		active_panel = panel;

		content.innerHTML = '<div class="fupi_loading_spinner"><div></div></div>';
		load_gdpr_helper( content );

		requestAnimationFrame(() => {
			panel.classList.add(panel_active_class);
		});
	}

	//
	// INIT ON CLICK
	//

	document.addEventListener('click', e => {
		const metadata_section = e.target.closest('.fupi_r3_addMetadata_builder');
		if ( ! metadata_section || ! e.target.closest('.fupi_r3_add_btn, .fupi_r3_remove_btn') ) return;

		const row = metadata_section.closest('tr');
		if ( row ) setTimeout( () => disable_selected_metadata_options( row ) );
	}, true);

	document.addEventListener('click', e => {

		const target = e.target;

		const gdpr_helper_button = target.closest('.fupi_open_gdpr_helper');

		if ( gdpr_helper_button ) {
			e.preventDefault();
			create_gdpr_helper_panel();
			return;
		}

		// Click in the button opening a slide-out panel

		const open_button = target.closest('.fupi_open_dynamic_settings');

		if ( open_button ) {

			const module_id = open_button.dataset.moduleId || 'atrig';

			// get what atrig fields to show in panel
			const field_ids = open_button.dataset.field_ids;
			
			// mark the button to remember to update fields in its section
			open_button.classList.add('fupi_atrig_popup_opener'); 
			
			create_panel( module_id, field_ids );
			return;
		}

		// Click in "Close panel" button
		const close_button = target.closest('.fupi_settings_panel_close_btn');

		if ( close_button ) {
			destroy_panel( close_button.closest( '.' + panel_class ) );
			return;
		}

		// Click in overlay also closes the panel
		const overlay = target.classList && target.classList.contains('fupi_settings_panel_overlay') ? target : false;
		if ( overlay) destroy_panel( overlay.closest( '.' + panel_class ) );
	});

	document.addEventListener('change', e => {
		if ( ! e.target.matches('.fupi_r3_addMetadata_builder select') ) return;

		const row = e.target.closest('tr');
		if ( row ) disable_selected_metadata_options( row );
	});

	if ( document.readyState === 'loading' ) {
		document.addEventListener('DOMContentLoaded', disable_all_selected_metadata_options);
	} else {
		disable_all_selected_metadata_options();
	}
})();
