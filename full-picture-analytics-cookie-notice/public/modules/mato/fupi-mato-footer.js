function fupi_mato_footer(){

	// SCROLL

	if ( fp.mato.track_scroll_depth ) FP.addAction( 'scroll_after_wait', () => {
		let reached_point = fpdata.scrolled.reached_point;
		_paq.push(['trackEvent', 'Page Scroll Depth', 'Scroll', 'Scrolled to ' + reached_point, reached_point]);
		if ( fp.main.debug ) console.log('[FP] Matomo event: page scrolled to ', reached_point );
	} );

	// CONTACT

	if ( fp.mato.track_email_tel ) FP.addAction( 'contact', () => {
		let action = fpdata.clicked.link.is_email ? 'Email Link Click' : 'Tel Link Click',
			contact = fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel;

		_paq.push(['trackEvent', 'Contact Link Click', action, contact ]);
		if ( fp.main.debug ) console.log('[FP] Matomo event: ' + action + ': ' + contact );
	} );

	// CLICKS

	if ( fp.mato.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.mato.track_clicks, ( evt_data, clicked_el ) => {
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		_paq.push(['trackEvent', 'Page Click', 'Click', name]);
		if ( fp.main.debug ) console.log('[FP] Matomo event: clicked ' + name );
	} );

	// AFF CLICKS

	if ( fp.mato.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.mato.track_aff, evt_data => {
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;
		_paq.push([ 'trackEvent', 'Affiliate Link Click', 'Click', name ]);
		if ( fp.main.debug ) console.log('[FP] Matomo event: clicked affiliate link ', name );
	} );

	// FORM SUBMITS

	if ( fp.mato.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.mato.track_form_submits, evt_data => {
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;
		_paq.push([ 'trackEvent', 'Form Submission', 'Form Submission', name ]);
		if ( fp.main.debug ) console.log('[FP] Matomo event: Submitted form ' + name );
	} );

	// IMPRESS

	if ( fp.mato.track_impress ) FP.addTriggerActions( 'view_triggers', fp.mato.track_impress, evt_data => {
		_paq.push(['trackEvent', 'Page Element View', 'View', evt_data.val]);
		if ( fp.main.debug ) console.log('[FP] Matomo event: viewed page element ', evt_data.val );
	} );

	

	FP.loaded('mato-footer');
}

FP.load('mato-footer','fupi_mato_footer',['mato','footer-helpers']);

