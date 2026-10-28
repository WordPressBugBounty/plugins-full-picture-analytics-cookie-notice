function fupi_hotj_footer() {

	// OUTBOUND

	if ( fp.hotj.tag_outbound ) FP.addAction( 'outbound', () => {
		if ( ! window.hj ) return;
		hj( 'event', 'outbound link click' );
		hj( 'event', 'outbound: ' + fpdata.clicked.link.href );
		if ( fp.main.debug ) console.log( '[FP] Hotjar "outbound click" event:', fpdata.clicked.link.href );
	} );

	// CONTACT

	if ( fp.hotj.tag_email_tel ) FP.addAction( 'contact', () => {
		if ( ! window.hj ) return;
		hj( 'event', 'contact link click' );
		if ( fpdata.clicked.link.safe_email ) {
			hj( 'event', 'mail to ' + fpdata.clicked.link.safe_email );
		} else if ( fpdata.clicked.link.safe_tel ) {
			hj( 'event', 'tel to ...' + fpdata.clicked.link.safe_tel );
		};
		if ( fp.main.debug ) console.log( '[FP] Hotjar "contact click" event' );
	} );

	// DOWNL

	if ( fp.hotj.track_downl ) FP.addAction( 'download', () => {
		if ( ! window.hj ) return;
		hj( 'event', 'file download' );
		hj( 'event', 'download: ' + fpdata.clicked.link.filename );
		if ( fp.main.debug ) console.log( '[FP] Hotjar "download" event:', fpdata.clicked.link.filename );
	} );

	// ANCHOR CLICKS

	if ( fp.hotj.tag_anchor_clicks ) FP.addAction( ['click'], function(){
		if ( ! window.hj ) return;
		if ( fpdata.clicked?.link?.is_anchor ){
			hj( 'event', 'anchor link click' );
			hj( 'event', 'anchor click:' + fpdata.clicked.link.href );
			if ( fp.main.debug ) console.log('[FP] Hotjar "anchor click" event:', fpdata.clicked.link.href );
		}
	});

	// CLICKS

	if ( fp.hotj.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.hotj.track_clicks, ( evt_data, clicked_el ) => {
		if ( ! window.hj ) return;
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;

		hj( 'event', 'page element click: ' + name );
		if ( fp.main.debug ) console.log('[FP] Hotjar "element click" event:', name);
	} );

	// CLICK AFF

	if ( fp.hotj.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.hotj.track_aff, ( evt_data, clicked_el ) => {
		if ( ! window.hj ) return;
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;

		hj( 'event', 'affiliate link click' );
		hj( 'event', 'affiliate: ' + name );
		if ( fp.main.debug ) console.log( '[FP] Hotjar "affiliate click" event', name );
	});

	// FORMS

	if ( fp.hotj.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.hotj.track_form_submits, evt_data => {
		if ( ! window.hj ) return;
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;

		hj( 'event', 'form submit' );
		hj( 'event', 'form submit: ' + name );
		if ( fp.main.debug ) console.log('[FP] Hotjar "form submit" event:', name);
	});

	// IMPRESS

	if ( fp.hotj.track_impress ) FP.addTriggerActions( 'view_triggers', fp.hotj.track_impress, ( evt_data, viewed_el ) => {
		if ( ! window.hj ) return;
		hj( 'event', 'element view' );
		hj( 'event', evt_data.val + ' viewed' );
		if ( fp.main.debug ) console.log('[FP] Hotjar "element view" event:', evt_data.val );
	});

	

	FP.loaded('hotj-footer');
}

FP.load('hotj-footer','fupi_hotj_footer', ['hotj','footer-helpers']);