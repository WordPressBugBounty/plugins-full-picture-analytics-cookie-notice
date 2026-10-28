
function fupi_hotj_setup(){

	window.hj=window.hj||function(){(hj.q=hj.q||[]).push(arguments)};

	// LOAD HOTJAR

	(function(h,o,t,j,a,r){
		h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
		h._hjSettings={hjid:fp.hotj.id,hjsv:6};
		a=o.getElementsByTagName('head')[0];
		r=o.createElement('script');r.async=1;
		r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
		a.appendChild(r);
	})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');

	// TAG SESSION WITH STATIC DATA

	if ( fp.hotj.tag_user_role && fpdata.user.role ) hj( 'event', 'user role: ' + fpdata.user.role );
	if ( fp.hotj.tag_pagetype && fpdata.page_type) hj( 'event', 'page type: ' + fpdata.page_type );
	if ( fp.hotj.tag_pageauthor && fpdata.author_name ) hj( 'event', 'page author: ' + fpdata.author_name );

	// Add UTM parameters to static tags
	if ( fp.hotj.tag_utm && document.location.href.indexOf( 'utm_source' ) != -1 ) {
		[ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ].forEach( function ( utm_name ) {
			var utm_val = FP.getUrlParamByName( utm_name );
			if ( utm_val && utm_val.length > 1 ) hj( 'event', utm_name + ' ' + decodeURI( utm_val ) );
		} );
	};

	

	// LOAD FOOTER SCRIPTS
	FP.loaded('hotj','hotj','[FP] Hotjar loaded');
};

fupi_hotj_setup();
