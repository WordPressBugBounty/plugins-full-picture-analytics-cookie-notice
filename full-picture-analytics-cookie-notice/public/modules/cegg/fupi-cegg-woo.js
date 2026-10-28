function fupi_cegg_woo_footer(){	
	// TRACK IMPRESSIONS

	function track_woo_impress( caller_id ) {
		
		if ( ! fpdata.woo.lists.single ) return;
		if ( ! fp.woo.cegg ) fp.woo.cegg = { 'single' : [] };
		
		let item_ids = fpdata.woo.lists.single.filter( id => ! fp.woo.cegg.single.includes(id) ); // track only items that were not tracked before

		if ( item_ids.length > 0 ) FP.fns.add_cegg_tag( 'product view' );

		// prevent double tracking in case the next teasers are added dynamically
		fp.woo.cegg.single.push(...item_ids);
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress( 'cegg' );
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	// TRACK ADD TO CART / REMOVE FROM CART
	// TRACK ADD TO WISHLIST

	FP.addAction( ['woo_add_to_cart'], () =>{
		FP.fns.add_cegg_tag( 'add to cart' );
	} );

	if ( fp.woo.cart_to_track ) FP.fns.add_cegg_tag( 'add to cart' ); // when ATC is tracked in cart

	FP.addAction( ['woo_add_to_wishlist'], data => {
		FP.fns.add_cegg_tag( 'add to wishlist' );
	} );

	FP.addAction( ['woo_remove_from_cart'], data => {
		FP.fns.add_cegg_tag( 'remove from cart' );
	} );

	// TRACK ORDER & CHECKOUT

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		FP.fns.add_cegg_tag( 'purchase' );
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.cegg.woo_checkout_tracked ) {
			fp.cegg.woo_checkout_tracked = true;
			FP.fns.add_cegg_tag( 'checkout' );
		}
	}

	FP.loaded('cegg-woo');
};

FP.load('cegg-woo', 'fupi_cegg_woo_footer', ['cegg-setup', 'footer-helpers', 'woo']);