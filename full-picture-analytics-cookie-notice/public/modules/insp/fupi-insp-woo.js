function fupi_insp_woo(){

	function track_woo_impress( caller_id ) {
		
		if ( ! fpdata.woo.lists.single ) return;
		if ( ! fp.woo.insp ) fp.woo.insp = { 'single' : [] };
		
		let item_ids = fpdata.woo.lists.single.filter( id => ! fp.woo.insp.single.includes(id) ); // check if there are any prods that were not tracked before

		if ( item_ids.length > 0 ) {
			__insp.push( [ 'tagSession', 'product view' ] );
			if ( fp.main.debug ) console.log( '[FP] Inspectlet "product view" tag');
		}

		// prevent double tracking in case the next teasers are added dynamically
		fp.woo.insp.single.push(...item_ids);
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress( 'insp' );
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	FP.addAction( ['woo_add_to_cart'], () =>{
		__insp.push( [ 'tagSession', 'add to cart' ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet "add to cart" tag');
	} );
		
	if ( fp.woo.cart_to_track ) {
		__insp.push( [ 'tagSession', 'add to cart' ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet "add to cart" tag');
	}

	FP.addAction( ['woo_add_to_wishlist'], () => {
		__insp.push( [ 'tagSession', 'add to wishlist' ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet "add to wishlist" tag');
	} );

	FP.addAction( ['woo_remove_from_cart'], () => {
		__insp.push( [ 'tagSession', 'remove from cart' ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet "remove from cart" tag');
	} );

	// TRACK ORDER & CHECKOUT

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		__insp.push( [ 'tagSession', 'purchase' ] );
		if ( fp.main.debug ) console.log( '[FP] Inspectlet "purchase" tag');
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.insp.woo_checkout_tracked ) {
			fp.insp.woo_checkout_tracked = true;
			__insp.push( [ 'tagSession', 'checkout' ] );
			if ( fp.main.debug ) console.log( '[FP] Inspectlet "checkout" tag');
		}
	}

	FP.loaded('insp-woo');
}

FP.load('insp-woo','fupi_insp_woo', ['insp', 'footer-helpers', 'woo']);