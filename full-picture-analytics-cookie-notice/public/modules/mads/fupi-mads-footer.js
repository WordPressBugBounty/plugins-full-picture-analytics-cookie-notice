function fupi_mads_footer(){

	function send_mads_event( event_data, event_value ) {

		if ( typeof event_data.event_action !== 'string' || ! event_data.event_action.length ) return;

		let payload = {};

		if ( Object.prototype.hasOwnProperty.call( event_data, 'event_category' ) ) payload.event_category = event_data.event_category;
		if ( Object.prototype.hasOwnProperty.call( event_data, 'event_label' ) ) payload.event_label = event_data.event_label;

		if ( typeof event_value !== 'undefined' ) {
			payload.event_value = event_value;
		} else if ( Object.prototype.hasOwnProperty.call( event_data, 'event_value' ) ) {
			payload.event_value = event_data.event_value;
		}

		window.uetq.push( 'event', event_data.event_action, payload );
		if ( fp.main.debug ) console.log( '[FP] MS Ads event action: ' + event_data.event_action, payload );
	}

	// DOWNLOADS

	if ( fp.mads.track_downl ) FP.addAction( 'download', () => {
		window.uetq.push( 'event', fp.mads.track_downl, {} );
		if ( fp.main.debug ) console.log( '[FP] MS Ads event action: ' + fp.mads.track_downl );
	} );

	// CLICKS

	if ( fp.mads.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.mads.track_clicks, ( evt_data, clicked_el ) => {
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		window.uetq.push( 'event', name, {} );
		if ( fp.main.debug ) console.log( '[FP] MS Ads event action: ' + name );
	} );	

	// AFFILIATE CLICKS

	if ( fp.mads.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.mads.track_aff, evt_data => {
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;
		window.uetq.push( 'event', name, {} );
		if ( fp.main.debug ) console.log( '[FP] MS Ads event action: ' + name );
	} );

	// FORMS

	if ( fp.mads.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.mads.track_form_submits, evt_data => {
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;
		window.uetq.push( 'event', name, {} );
		if ( fp.main.debug ) console.log( '[FP] MS Ads event action: ' + name );
	} );

	// IMPRESS

	if ( fp.mads.track_impress ) FP.addTriggerActions( 'view_triggers', fp.mads.track_impress, evt_data => {
		window.uetq.push( 'event', evt_data.val, {} );
		if ( fp.main.debug ) console.log( '[FP] MS Ads event action: ' + evt_data.val );
	} );

	

	FP.loaded('mads-footer');
}

FP.load('mads-footer','fupi_mads_footer',['mads','footer-helpers']);
