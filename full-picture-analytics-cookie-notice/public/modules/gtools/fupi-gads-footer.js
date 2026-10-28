function fupi_gads_footer(){

	// CONTACT - EMAIL - OK

	if ( fp.gads.track_email ) FP.addAction( 'contact', ()=>{

		if ( ! fpdata.clicked.link.is_email ) return;

		gtag( 'event', 'conversion', {'send_to': ( fp.gads.id2 || fp.gads.id ) + '/' + fp.gads.track_email } );

		if ( fp.main.debug ) console.log('[FP] GAds conversion event: email link click', ( fp.gads.id2 || fp.gads.id ) + '/' + fp.gads.track_email );
	});

	// CONTACT - TEL - OK

	if ( fp.gads.track_tel ) FP.addAction( 'contact', ()=>{

		if ( fpdata.clicked.link.is_email ) return;

		gtag( 'event', 'conversion', {'send_to': ( fp.gads.id2 || fp.gads.id ) + '/' + fp.gads.track_tel } );

		if ( fp.main.debug ) console.log('[FP] GAds conversion event: email link click', ( fp.gads.id2 || fp.gads.id ) + '/' + fp.gads.track_email );
	});

	// CLICKS - OK

	if ( fp.gads.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.gads.track_clicks, ( evt_data, clicked_el ) => {

		gtag( 'event', 'conversion', { 'send_to' : ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val } );

		if ( fp.main.debug ) console.log('[FP] GAds conversion event: element click', ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val );
	});

	// CLICK AFF - OK

	if ( fp.gads.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.gads.track_aff, ( evt_data, clicked_el ) => {

		gtag( 'event', 'conversion', {'send_to' : ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val });

		if ( fp.main.debug ) console.log('[FP] GAds conversion event: affiliate click', ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val );
	});

	// FORMS - OK

	if ( fp.gads.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.gads.track_form_submits, evt_data => {

		gtag( 'event', 'conversion', {'send_to' : ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val } );

		if ( fp.main.debug ) console.log('[FP] GAds conversion event: form submit', ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val);
	});

	// IMPRESS - OK

	if ( fp.gads.track_impress ) FP.addTriggerActions( 'view_triggers', fp.gads.track_impress, ( evt_data, viewed_el ) => {

		gtag( 'event', 'conversion', {'send_to': ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val } );

		if ( fp.main.debug ) console.log('[FP] GAds conversion event: element view', ( fp.gads.id2 || fp.gads.id ) + '/' + evt_data.val );
	});

	

	FP.loaded('gads-footer');
};

FP.load('gads-footer', 'fupi_gads_footer', ['gtg-config', 'footer-helpers']);
