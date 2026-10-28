function fupi_insp_footer() {

	// TAG OUTBOUND LINKS

	if ( fp.insp.tag_outbound ) FP.addAction( 'outbound', ()=>{
		__insp.push( [ 'tagSession', { 'outbound link click' : fpdata.clicked.link.href } ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: outbound click', fpdata.clicked.link.href );
	} );

	// CONTACT

	if ( fp.insp.tag_email_tel ) FP.addAction( 'contact', ()=>{
		let name = fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel;
		__insp.push( [ 'tagSession', { 'contact link click' : name } ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: contact click', name );
	} );

	// DOWNLOADS

	if ( fp.insp.track_downl ) FP.addAction( 'download', ()=>{
		__insp.push( [ 'tagSession', { 'file download' : fpdata.clicked.link.filename } ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: download', fpdata.clicked.link.filename );
	} );

	// TAG CLICKS IN ANCHORS

	if ( fp.insp.tag_anchor_clicks ){
		FP.addAction( ['click'], function(){
			if ( fpdata.clicked.link && fpdata.clicked.link.is_anchor ){
				__insp.push( [ 'tagSession', { 'anchor click' : fpdata.clicked.link.element.textContent } ] );
				if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: anchor click', fpdata.clicked.link.element.textContent );
			}
		})
	}

	// CLICKS

	if ( fp.insp.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.insp.track_clicks, ( evt_data, clicked_el ) => {
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;
		__insp.push( [ 'tagSession', { 'page element click' : name } ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: element click', name );
	} );

	// AFFILIATE CLICKS

	if ( fp.insp.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.insp.track_aff, ( evt_data, clicked_el ) => {
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;
		__insp.push( [ 'tagSession', { 'affiliate link click' :  name } ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: affiliate click', name );
	} );

	// FORM SUBMITS

	if ( fp.insp.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.insp.track_form_submits, evt_data => {
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;
		__insp.push( [ 'tagSession', { 'form submit' : name } ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: form submit', name );
	} );

	// IMPRESS

	if ( fp.insp.track_impress ) FP.addTriggerActions( 'view_triggers', fp.insp.track_impress, evt_data => {
		__insp.push( [ 'tagSession', 'element view: ' + evt_data.val ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet tag: element view', evt_data.val );
	} );

	FP.loaded('insp-footer');
}

FP.load('insp-footer','fupi_insp_footer', ['footer-helpers', 'insp']);
