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
