fp.clar.fns = {};
fp.clar.fns.changeConsents = ( ads, analytics ) => {
	console.log('Clarity consents set to ', ads, analytics );
	window.clarity("consentv2", {
		ad_Storage: ads ? "granted" : "denied", 
		analytics_Storage: analytics ? "granted" : "denied"
	});
}

function fupi_clar(){	

	(function(c,l,a,r,i,t,y){
		c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
		t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
		y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
	})(window, document, "clarity", "script", fp.clar.id);

	// CONSENTS

	if ( ! fp.vars.use_other_cmp ) {

		// if the user did not make a choice in the past
		if ( ! fpdata.cookies ){
	
			// agree if consent banner is disabled
			if ( ! fp.notice.enabled ) {
				fp.clar.fns.changeConsents( true, true );
			
			// if consent banner is enabled
			} else {
				// but it only notifies
				if ( fp.notice.mode == 'notify' || fp.notice.mode == 'optout' ) {
					fp.clar.fns.changeConsents( true, true );
				} else {
					fp.clar.fns.changeConsents( false, false );
				}
			}

		// if made choice in the past
		} else {
			fp.clar.fns.changeConsents( fpdata.cookies.marketing, fpdata.cookies.stats );
		}

		// Update on change
		FP.addAction( ["privacy_consents_changed"], ()=>{
			fp.clar.fns.changeConsents( fpdata.consents.can_track_market, fpdata.consents.can_track_stats );
		})
	}
	
	// TAG WITH USER ROLE AND STATUS
	if ( fp.clar.tag_user_role && fpdata.user.role ) clarity( 'set', 'User role', fpdata.user.role );

	// TAG WITH PAGE AUTHOR
	if ( fp.clar.tag_pageauthor && fpdata.author_name ) clarity( 'set', 'Author', fpdata.author_name );
	
	// TAG WITH PAGE TYPE
	if ( fp.clar.tag_pagetype && fpdata.page_type ) clarity( 'set', 'Page type', fpdata.page_type );
	
	

	FP.loaded('clar', 'clar','[FP] MS Clarity loaded');
};

FP.load('clar', 'fupi_clar')