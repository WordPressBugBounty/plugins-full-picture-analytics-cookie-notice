FP.fns.add_cegg_tag = ( tag ) => {
	window.CE2.addTag( tag );
	if ( fp.main.debug ) console.log( '[FP] CrazyEgg tag: ' + tag );
}

// THIS GETS FIRED ONCE CE LOADS
( window.CE_API || ( window.CE_API = [] ) ).push( () => {
	
	

	// TAG WITH PAGE TYPE
	if ( fp.cegg.tag_pagetype && fpdata.page_type ) window.CE2.addTag( 'Page type: ' + fpdata.page_type );
	
	FP.loaded('cegg-setup', 'cegg', '[FP] Crazy Egg loaded');
} );

