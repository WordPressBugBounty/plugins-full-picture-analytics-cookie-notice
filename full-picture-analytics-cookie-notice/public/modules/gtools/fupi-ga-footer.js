FP.fns.send_ga4_evt = ( nr, evt_name, data )=>{
	if ( ! evt_name ) return;
	// add fp_ prefix if the first char is a number
	if ( !isNaN( evt_name.charAt(0) ) ) evt_name = 'fp_' + evt_name;
	// trim the name to max 40 chars
	evt_name = evt_name.substring(0, 40);
	gtag( 'event', evt_name, data);
	if ( fp.main.debug ) console.log('[FP] GA4 #' + nr + ' event: ' + evt_name, data);
};

function fupi_ga4_footer(nr){

	let _ga = fp['ga4' + nr];

	// SCROLL - OK

	if ( _ga.track_scroll_method ) FP.addAction( 'scroll_after_wait', ()=>{

		let name = 'scrolled_' + fpdata.scrolled.reached_point,
			evt_name = name,
			data = { 'send_to': fp['ga4' + nr].id2 || fp['ga4' + nr].id };

		if ( _ga.track_scroll_method && _ga.track_scroll_method == 'params' ) {
			evt_name = 'scroll';
			data['percent_scrolled'] = name;
		};

		FP.fns.send_ga4_evt( nr, evt_name, data );
	});

	// CONTACT - OK

	if ( _ga.track_email_tel ) FP.addAction( 'contact', ()=>{

		var link_type = fpdata.clicked.link.is_email ? 'email' : 'tel',
			safe_contact_data = fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel,
			evt_name = link_type + '_click_' + safe_contact_data,
			data = { 'send_to': fp['ga4' + nr].id2 || fp['ga4' + nr].id };

		if ( _ga.track_email_tel == 'params' ){
			evt_name = link_type + '_link_click'
			data['contact_click'] = safe_contact_data;
		}

		FP.fns.send_ga4_evt( nr, evt_name, data );
	});

	// CLICKS - OK

	if ( _ga.track_clicks ) FP.addTriggerActions( 'click_triggers', _ga.track_clicks, ( evt_data, clicked_el ) => {

		let name = FP.getClickName( evt_data ),
			evt_name = name,
			data = { 'send_to': fp['ga4' + nr].id2 || fp['ga4' + nr].id };

		if ( evt_data['type'] == 'param' ) {
			evt_name = 'element_click',
			data['element_click'] = name;
		}

		FP.fns.send_ga4_evt( nr, evt_name, data );
	});

	// CLICK AFF - OK

	if ( _ga.track_aff ) FP.addTriggerActions( 'aff_click_triggers', _ga.track_aff, ( evt_data, clicked_el ) => {

		let name = FP.getAffName( evt_data );
		if ( ! name ) return;

		let evt_name = name;
			data = { 'send_to': fp['ga4' + nr].id2 || fp['ga4' + nr].id };

		if ( evt_data['type'] == 'param' ) {
			evt_name = 'affiliate_link_click';
			data['affiliate_link_click'] = name;
		};

		FP.fns.send_ga4_evt( nr, evt_name, data );
	});

	// FORMS - OK

	if ( _ga.track_form_submits ) FP.addTriggerActions( 'form_triggers', _ga.track_form_submits, evt_data => {

		let name = FP.getFormName( evt_data ),
			evt_name = name,
			data = { 'send_to': fp['ga4' + nr].id2 || fp['ga4' + nr].id };

		if ( evt_data['type'] == 'param' ) {
			evt_name = 'form_submit';
			data['submitted_form'] = name;
		}

		FP.fns.send_ga4_evt( nr, evt_name, data );
	});

	// IMPRESS - OK

	if ( _ga.track_impress ) FP.addTriggerActions( 'view_triggers', _ga.track_impress, ( evt_data, viewed_el ) => {

		let evt_name = evt_data.val,
			data = { 'send_to': fp['ga4' + nr].id2 || fp['ga4' + nr].id };

		if ( evt_data['type'] == 'param' ) {
			evt_name = 'element_view';
			data['viewed_element'] = evt_data.val;
		};

		FP.fns.send_ga4_evt( nr, evt_name, data );
	});

	
};

function fupi_ga_footer(){
	if ( fp.ga41?.id ) fupi_ga4_footer(1);
	
	FP.loaded('ga-footer');
};

FP.load('ga-footer', 'fupi_ga_footer', ['gtg-config', 'footer-helpers']);
