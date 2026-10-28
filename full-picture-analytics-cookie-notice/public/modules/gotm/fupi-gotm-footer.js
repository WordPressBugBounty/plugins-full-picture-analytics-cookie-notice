function fupi_footer_gtm(){

	// OUTBOUND - OK

	if ( fp.gtm.track_outbound ) FP.addAction( 'outbound', ()=>{

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_outboundLinkClick',
			'fp_clickedOutboundLink' : fpdata.clicked.link.href,
		} );

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_outboundLinkClick. Clicked link: ', fpdata.clicked.link.href );
	} );

	// CONTACT - OK

	if ( fp.gtm.track_email_tel ) FP.addAction( 'contact', ()=>{

		let contact_type = fpdata.clicked.link.is_email ? 'email' : 'tel';

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_contactLinkClick',
			'fp_clickedContactLink' :  fpdata.clicked.link.href,
			'fp_clickedSafeContactLink' :  fpdata.clicked.link.safe_email || fpdata.clicked.link.safe_tel,
			'fp_clickedContactLinkType' : contact_type + ' click',
		} );

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_contactLinkClick. Clicked element: ', fpdata.clicked.link.href );
	});

	// DOWNLOADS - OK

	if ( fp.gtm.track_downl ) FP.addAction( 'download', ()=>{

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_fileDownload',
			'fp_downloadedFile' : fpdata.clicked.link.filename,
		} );

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_fileDownload. File: ', fpdata.clicked.link.filename );
	});

	// SCROLL - OK

	if ( fp.gtm.track_scroll_depth ) FP.addAction( 'scroll_after_wait', ()=>{

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_scrollDepth',
			'fp_scrollDepth' : fpdata.scrolled.reached_point,
		});

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_scrollDepth. Reached depth: ', fpdata.scrolled.reached_point );
	});

	// DYNAMIC URL CHANGES (history.state != null)

	if ( fp.gtm.track_dynamic_urls ) FP.addAction( ['virtual_pageview'], ()=>{	

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_virtualPageview',
			'fp_virtualPageviewURL' : location.host + location.pathname,
		});

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_virtualPageview' );
	})

	// PAGE BLUG & FOCUS - OK

	if ( fp.gtm.track_focus ) FP.addAction( ['page_in_blur', 'page_in_focus'], ()=>{

		window[fp.gtm.datalayer].push( {
			'event' : 'windowVisibilityChange',
			'fp_pageInFocus' : fpdata.doc_in_focus,
		} );

		if ( fp.main.debug ) console.log( '[FP] GTM event windowVisibilityChange. Page in focus: ', fpdata.doc_in_focus );
	});

	// CLICKS IN ANCHORS - OK

	if ( fp.gtm.track_anchor_clicks ) FP.addAction( ['click'], ()=>{

		if ( ! fpdata.clicked?.link?.is_anchor ) return;
		
		window[fp.gtm.datalayer].push( {
			'event' : 'fp_anchorClick',
			'fp_clickedAnchorLink' : fpdata.clicked.link.href,
		});

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_anchorClick. Anchor: ', fpdata.clicked.link.href );
	})

	// CLICKS - OK

	if ( fp.gtm.track_clicks ) FP.addTriggerActions( 'click_triggers', fp.gtm.track_clicks, ( evt_data, clicked_el ) => {
		
		let name = FP.getClickName( evt_data );
		if ( ! name ) return;

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_elementClick',
			'fp_clickedElement' : name,
		});

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_elementClick. Clicked element: ', name );
	});

	// CLICK AFF - OK

	if ( fp.gtm.track_aff ) FP.addTriggerActions( 'aff_click_triggers', fp.gtm.track_aff, ( evt_data, clicked_el ) => {
		
		let name = FP.getAffName( evt_data );
		if ( ! name ) return;

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_affiliateLinkClick',
			'fp_clickedAffiliateLink' : name,
		});

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_affiliateLinkClick. Clicked link: ', name );
	});

	// IMPRESS - OK

	if ( fp.gtm.track_impress ) FP.addTriggerActions( 'view_triggers', fp.gtm.track_impress, ( evt_data, viewed_el ) => {

		window[fp.gtm.datalayer].push( {
			'event' : 'fp_elementView',
			'fp_viewedElement' : evt_data.val,
		} );

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_elementView. Viewed element: ', evt_data.val );
	});

	// FORMS - OK

	if ( fp.gtm.track_form_submits ) FP.addTriggerActions( 'form_triggers', fp.gtm.track_form_submits, evt_data => {
		
		let name = FP.getFormName( evt_data );
		if ( ! name ) return;
		
		window[fp.gtm.datalayer].push( {
			'event' : 'fp_formSubmit',
			'fp_submittedForm' : name,
		} );

		if ( fp.main.debug ) console.log( '[FP] GTM event fp_formSubmit. Form: ', name );
	});
	
	
	
	FP.loaded('gtm-footer');
}

FP.load('gtm-footer', 'fupi_footer_gtm', ['gtm','footer-helpers']);
