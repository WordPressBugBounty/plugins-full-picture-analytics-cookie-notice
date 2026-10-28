function fupi_cegg_footer(){

	// OUTBOUND

	if ( fp.cegg.tag_outbound ) FP.addAction( ['outbound'], function(){
		FP.fns.add_cegg_tag( 'Outbound click: ' + fpdata.clicked.link.href );
	});

	// CONTACT

	if ( fp.cegg.tag_email_tel ) FP.addAction( 'contact', () => {
		FP.fns.add_cegg_tag( 'Contact click: ' + ( fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel ) );
	});

	// DOWNLOADS

	if ( fp.cegg.track_downl ) FP.addAction( 'download', () => {
		FP.fns.add_cegg_tag( 'File download: ' + fpdata.clicked.link.filename );
	});

	// ANCHOR CLICKS

	if ( fp.cegg.tag_anchor_clicks ) FP.addAction( ['click'], function(){
		if ( fpdata.clicked?.link?.is_anchor ){
			FP.fns.add_cegg_tag( 'Anchor click: ' + fpdata.clicked.link.href );
		}
	});

	// CLICKS

	if ( fp.cegg.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.cegg.track_clicks, ( evt_data, clicked_el ) => {
		let name = FP.getClickName( evt_data );
		if ( name ) FP.fns.add_cegg_tag( 'Element click: ' + name );
	} );
	
	// CLICK AFF

	if ( fp.cegg.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.cegg.track_aff, ( evt_data, clicked_el ) => {
		let name = FP.getAffName( evt_data );
		if ( name ) FP.fns.add_cegg_tag( 'Affiliate click: ' + name );
	});

	// FORMS

	if ( fp.cegg.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.cegg.track_form_submits, evt_data => {
		let name = FP.getFormName( evt_data );
		if ( name ) FP.fns.add_cegg_tag( 'Form submit: ' + name );
	});

	// IMPRESS

	if ( fp.cegg.track_impress ) FP.addTriggerActions( 'view_triggers', fp.cegg.track_impress, ( evt_data, viewed_el ) => {
		FP.fns.add_cegg_tag( 'Element view: ' + evt_data.val );
	});

	

	FP.loaded('cegg-footer');
};

// INIT FOOTER SCRIPTS
FP.load('cegg-footer', 'fupi_cegg_footer', ['cegg-setup', 'footer-helpers']);
