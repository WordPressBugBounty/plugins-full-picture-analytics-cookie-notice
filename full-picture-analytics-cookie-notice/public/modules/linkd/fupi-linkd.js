function fupi_linkd_woo(){

	// TRACK ADD TO CART

	if ( fp.linkd.woo_add_to_cart_id ) {
		FP.addAction( ['woo_add_to_cart'], () =>{
			window.lintrk( 'track', { 'conversion_id': fp.linkd.woo_add_to_cart_id } );
			if ( fp.main.debug ) console.log( '[FP] LinkedIn "add to cart" event. ID:', fp.linkd.woo_add_to_cart_id );
		} );

		if ( fp.woo.cart_to_track ) {
			window.lintrk( 'track', { 'conversion_id': fp.linkd.woo_add_to_cart_id } );
			if ( fp.main.debug ) console.log( '[FP] LinkedIn "add to cart" event. ID:', fp.linkd.woo_add_to_cart_id );
		}
	}

	// TRACK ORDER & CHECKOUT

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && fp.linkd.woo_purchase_id && ! fpdata.refreshed ) {
		window.lintrk( 'track', { 'conversion_id': fp.linkd.woo_purchase_id } );
		if ( fp.main.debug ) console.log( '[FP] LinkedIn "purchase" event. ID:', fp.linkd.woo_purchase_id);
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && fp.linkd.woo_checkout_start_id && ! fpdata.refreshed ) {
		if ( ! fp.linkd.woo_checkout_tracked ) {
			fp.linkd.woo_checkout_tracked = true;
			window.lintrk( 'track', { 'conversion_id': fp.linkd.woo_checkout_start_id } );
			if ( fp.main.debug ) console.log( '[FP] LinkedIn "checkout" event. ID:', fp.linkd.woo_checkout_start_id);
		}
	}
}

function fupi_linkd_events(){

	// EMAIL CLICKS

	if ( fp.linkd.track_email ) {
		FP.addAction( 'contact', function(){
			if ( ! fpdata.clicked.link.is_email ) return;
			window.lintrk('track', { conversion_id: fp.linkd.track_email });
			if ( fp.main.debug ) console.log('[FP] LinkedIn "email link click" conversion. ID:', fp.linkd.track_email );
		} );
	}

	// TEL CLICKS

	if ( fp.linkd.track_tel ) {
		FP.addAction( 'contact', function(){
			if ( ! fpdata.clicked.link.is_tel ) return;
			window.lintrk('track', { conversion_id: fp.linkd.track_tel });
			if ( fp.main.debug ) console.log('[FP] LinkedIn "tel link click" conversion. ID:', fp.linkd.track_tel );
		} );
	}

	// CLICKS

	if ( fp.linkd.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.linkd.track_clicks, evt_data => {
		window.lintrk('track', { conversion_id: evt_data.val });
		if ( fp.main.debug ) console.log('[FP] LinkedIn "element click" conversion. ID:', evt_data.val);
	} );

	// AFFILIATE CLICKS

	if ( fp.linkd.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.linkd.track_aff, evt_data => {
		window.lintrk('track', { conversion_id: evt_data.val });
		if ( fp.main.debug ) console.log('[FP] LinkedIn "affiliate link click" conversion. ID:', evt_data.val);
	} );

	// VIEWS

	if ( fp.linkd.track_impress ) FP.addTriggerActions( 'view_triggers', fp.linkd.track_impress, evt_data => {
		window.lintrk('track', { conversion_id: evt_data.val });
		if ( fp.main.debug ) console.log('[FP] LinkedIn "element visibility" conversion. ID:', evt_data.val );
	} );

	// FORM SUBMITS

	if ( fp.linkd.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.linkd.track_form_submits, evt_data => {
		window.lintrk('track', { conversion_id: evt_data.val });
		if ( fp.main.debug ) console.log('[FP] LinkedIn "form submit" conversion. ID:', evt_data.val);
	} );
}
	
function fupi_load_linkd(){
	
	_linkedin_partner_id = fp.linkd.id;
	window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
	window._linkedin_data_partner_ids.push(_linkedin_partner_id);

	window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
	window.lintrk.q=[];

	FP.getScript("https://snap.licdn.com/li.lms-analytics/insight.min.js");
	
	fupi_linkd_events();

	if ( fp.woo ) fupi_linkd_woo();
	
	FP.loaded('linkd','linkd','[FP] LinkedIn loaded');
};

fupi_load_linkd();


