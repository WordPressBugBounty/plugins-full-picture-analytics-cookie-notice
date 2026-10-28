function fupi_pla_footer(){

	// CONTACT

	if ( fp.pla.track_contact_links ) FP.addAction( 'contact', () => {

		if ( typeof plausible == 'undefined' ) return;

		let props = {
			'target' : fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel,
			'type' : fpdata.clicked.link.is_email ? 'email' : 'tel',
		};
		plausible( fp.pla.track_contact_links, {'props': props} );
		if ( fp.main.debug ) console.log( '[FP] Plausible goal: "' + fp.pla.track_contact_links + '" with properties ', props );
	} );

	// DOWNLOADS

	if ( fp.pla.track_downl ) FP.addAction( 'download', () => {
		if ( typeof plausible == 'undefined' ) return;
		plausible( fp.pla.track_downl, { 'props': { 'file' : fpdata.clicked.link.filename } } );
		if ( fp.main.debug ) console.log( '[FP] Plausible goal: "' + fp.pla.track_downl + '" with a property "file: ' + fpdata.clicked.link.filename + '"' );
	} );

	// CLICKS

	if ( fp.pla.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.pla.track_clicks, evt_data => {
		if ( typeof plausible == 'undefined' ) return;
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		if ( fp.pla.track_elems_goalname ) {
			plausible( fp.pla.track_elems_goalname, { 'props': { 'page element' : name } } );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal: "' + fp.pla.track_elems_goalname + '" with a property "page element: ' + name + '"' );
		} else {
			plausible( name );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal: "' + name + '"');
		}
	} );
	
	// AFF CLICKS

	if ( fp.pla.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.pla.track_aff, evt_data => {

		if ( typeof plausible == 'undefined' ) return;

		let name = FP.getAffName( evt_data );
		if ( ! name ) return;
		
		if ( fp.pla.track_affiliate_goalname ) {
			plausible( fp.pla.track_affiliate_goalname, { 'props': { 'link' : name } } );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal "' + fp.pla.track_affiliate_goalname + '" with a property "link: ' + name + '"' );
		} else {
			plausible( name );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal "' + name + '"' );
		}
	} );

	// FORM SUBMITS

	if ( fp.pla.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.pla.track_form_submits, evt_data => {
		
		if ( typeof plausible == 'undefined' ) return;
		
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;

		if ( fp.pla.track_forms_goalname ) {
			plausible( fp.pla.track_forms_goalname, { 'props': { 'form' : name } } );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal: "' + fp.pla.track_forms_goalname + '" with a property "form: ' + name + '"' );
		} else {
			plausible( name );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal: "' + name + '"' );
		}
	} );

	// IMPRESS

	if ( fp.pla.track_impress ) FP.addTriggerActions( 'view_triggers', fp.pla.track_impress, evt_data => {

		if ( typeof plausible == 'undefined' ) return;

		let name = evt_data.val;

		if ( fp.pla.track_visib_goalname ) {
			plausible( fp.pla.track_visib_goalname, { 'props' : { 'name' : name } } );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal "' + fp.pla.track_visib_goalname + '" with a property "name: ' + name + '"' );
		} else {
			plausible( name );
			if ( fp.main.debug ) console.log( '[FP] Plausible goal "' + name + '"' );
		}
	} );

	

	FP.loaded('pla-footer');
}

FP.load('pla-footer', 'fupi_pla_footer', ['pla','footer-helpers']);

