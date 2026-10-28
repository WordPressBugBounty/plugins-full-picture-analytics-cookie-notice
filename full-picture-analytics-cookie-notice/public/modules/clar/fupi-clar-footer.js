function fupi_clar_footer(){

	// OUTBOUND

	if ( fp.clar.tag_outbound ) FP.addAction( ['outbound'], ()=>{
		clarity( 'set', 'Outbound link click', fpdata.clicked.link.href );
		if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: outbound click', fpdata.clicked.link.href );
	});

	// CONTACT

	if ( fp.clar.tag_email_tel ) FP.addAction( 'contact', () => {
		let tracked_link_part = fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel;
		clarity( 'set', 'Contact link click', tracked_link_part );
		if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: contact click', tracked_link_part );
	});

	// DOWNLOADS

	if ( fp.clar.track_downl ) FP.addAction( 'download', () => {
		clarity( 'set', 'File download', fpdata.clicked.link.filename );
		if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: download', fpdata.clicked.link.filename );
	});

	// ANCHOR CLICKS

	if ( fp.clar.tag_anchor_clicks ) FP.addAction( ['click'], function(){
		if ( fpdata.clicked?.link?.is_anchor ){
			clarity( 'set', 'Anchor link click', fpdata.clicked.link.href );
			if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: anchor click', fpdata.clicked.link.href );
		}
	});

	// CLICKS

	if ( fp.clar.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.clar.track_clicks, ( evt_data, clicked_el ) => {
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		clarity( 'set', 'Page element click', name );
		if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: element click', name );
	} );

	// CLICK AFF

	if ( fp.clar.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.clar.track_aff, ( evt_data, clicked_el ) => {
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;
		clarity( 'set', 'Affiliate link click', name );
		if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: affiliate click', name );
	});

	// FORMS

	if ( fp.clar.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.clar.track_form_submits, evt_data => {
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;
		clarity( 'set', 'Form submit', name );
		if ( fp.main.debug ) console.log( '[FP] MS Clarity tag: form submit', name );
	});

	// IMPRESS

	if ( fp.clar.track_impress ) FP.addTriggerActions( 'view_triggers', fp.clar.track_impress, ( evt_data, viewed_el ) => {
		clarity( 'set', 'Element view', evt_data.val );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: element view', evt_data.val );
	});

	

	FP.loaded('clar-footer');
};

FP.load('clar-footer', 'fupi_clar_footer', ['clar', 'footer-helpers']);