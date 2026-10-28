
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
