function fupi_tik_footer(){

	// CONTACT

	if ( fp.tik.track_email_tel ) FP.addAction( 'contact', () => {
		let contact = fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel;
		FP.fns.track_tik_evt('Contact', { 'description' : contact } );
	} );

	// DOWNLOADS

	if ( fp.tik.track_downl ) FP.addAction( 'download', () => {
		FP.fns.track_tik_evt('Download', { 'description' : fpdata.clicked.link.filename } );
	} );

	// CLICKS

	if ( fp.tik.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.tik.track_clicks, evt_data => {
		
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		
		if ( evt_data['type'] == 'custom' ) {
			FP.fns.track_tik_evt( 'custom_' + name );
		} else {
			FP.fns.track_tik_evt('ClickButton', { 'description' : name } );
		};
	} );

	// FORMS

	if ( fp.tik.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.tik.track_form_submits, evt_data => {
		
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;

		if ( evt_data['type'] == 'custom' ) {
			FP.fns.track_tik_evt( 'custom_' + name );
		} else if ( evt_data['type'] == 'lead' ) {
			FP.fns.track_tik_evt('Lead', { 'description' : name } );
		} else {
			FP.fns.track_tik_evt('SubmitForm', { 'description' : name } );
		};
	} );

	FP.loaded('tik-footer');
}

FP.load('tik-footer', 'fupi_tik_footer', ['footer-helpers', 'tik']);
