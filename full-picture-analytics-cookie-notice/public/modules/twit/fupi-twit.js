FP.fns.track_twit_evt = ( event_name, event_id, payload = false ) => {	
	

	twq( 'event', event_id, payload);
	if ( fp.main.debug ) console.log('[FP] X "' + event_name + '" event (event id: ' + event_id + '): ', payload);
};

function fupi_twit(){

	!function(e,t,n,s,u,a){
		e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);},
		s.version='1.1',
		s.queue=[],
		u=t.createElement(n),
		u.async=!0,
		u.src='https://static.ads-twitter.com/uwt.js',
		a=t.getElementsByTagName(n)[0],
		a.parentNode.insertBefore(u,a))
	}(window,document,'script');

	twq('config', fp.twit.id);

	// EVENTS

	// CLICKS

	if ( fp.twit.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.twit.track_clicks, evt_data => {
		FP.fns.track_twit_evt( 'page element click', evt_data.val, {} );
	} );

	// AFFILIATE CLICKS

	if ( fp.twit.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.twit.track_aff, evt_data => {
		FP.fns.track_twit_evt( 'affiliate link click', evt_data.val, {} );
	} );
	
	// FORMS
	
	if ( fp.twit.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.twit.track_form_submits, evt_data => {
		FP.fns.track_twit_evt( 'form submit', evt_data.val, {} );
	} );

	// IMPRESS

	if ( fp.twit.track_impress ) FP.addTriggerActions( 'view_triggers', fp.twit.track_impress, evt_data => {
		FP.fns.track_twit_evt( 'page element visible', evt_data.val, {} );
	} );

	FP.loaded('twit','twit','[FP] X loaded');
};

fupi_twit();