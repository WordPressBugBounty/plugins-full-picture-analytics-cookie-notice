FP.fns.generateUUID = async function ( string ) {

	// generate SHA 256
    const encoder = new TextEncoder();
    const data = encoder.encode(string);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    fpdata.user.UUID = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
	FP.setCookie('fp_uuid', fpdata.user.UUID, 365 );
	localStorage.setItem('fp_uuid', fpdata.user.UUID );
}

function fupi_fbp_footer(){
	
	// OUTBOUND

	if ( fp.fbp.track_outbound ) FP.addAction( 'outbound', () => {
		var data = {
			'url' : fpdata.clicked.link.href,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'outbound', false, data, !! fp.fbp.track_outbound_capi, { 'unloadSafe' : true } );
	} );

	// DOWNLOADS

	if ( fp.fbp.track_downl ) FP.addAction( 'download', () => {
		var data = {
			'file' : fpdata.clicked.link.filename,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'file download', false, data, !! fp.fbp.track_file_downl_capi, { 'unloadSafe' : true } );
	});

	// CONTACT

	if ( fp.fbp.track_email_tel ) FP.addAction( 'contact', () => {
		var data = {
			'target' : fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel,
			'type' : fpdata.clicked.link.is_email ? 'email' : 'tel',
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'Contact', false, data, !! fp.fbp.track_email_tel_capi, { 'unloadSafe' : true } );
	});

	// SCROLL

	if ( fp.fbp.track_scroll_depth ) FP.addAction( 'scroll_after_wait', () => {
		var data = {
			'scroll height' : fpdata.scrolled.reached_point,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'scroll', false, data, !! fp.fbp.track_scroll_capi );
	} );

	// CLICKS

	if ( fp.fbp.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.fbp.track_clicks, ( evt_data, clicked_el ) => {
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		let data = {
			'name' : name,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'click on element', false, data, !! fp.fbp.track_elems_capi, { 'unloadSafe' : true } );
	} );

	// CLICK AFF

	if ( fp.fbp.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.fbp.track_aff, ( evt_data, clicked_el ) => {
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;
		let data = {
			'target' : name,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'affiliate', false, data, !! fp.fbp.track_affiliate_capi, { 'unloadSafe' : true } );
	});

	// FORMS

	if ( fp.fbp.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.fbp.track_form_submits, evt_data => {
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;
		let data = {
			'form' : name,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'form submit', false, data, !! fp.fbp.track_forms_capi, { 'unloadSafe' : true } );
	});

	// IMPRESS

	if ( fp.fbp.track_impress ) FP.addTriggerActions( 'view_triggers', fp.fbp.track_impress, ( evt_data, viewed_el ) => {
		var data = {
			'element' : evt_data.val,
			'time on page' : fpdata.activity.total,
		};
		FP.track_fbp_evt( true, 'user viewed', false, data, !! fp.fbp.track_views_capi );
	});

	

	FP.loaded('fbp-footer');
};

FP.load('fbp-footer', 'fupi_fbp_footer', ['fbp-setup', 'footer-helpers']);
