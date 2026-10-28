function fupi_hotj_footer_woo(){

	// TRACK ADD TO CART / REMOVE FROM CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name, action ){
		
		hj( 'event', event_name );
		if ( fp.main.debug ) console.log( '[FP] Hotjar "' + event_name + '" event' );

		let track_params = fp.hotj[ 'tag_woo_' + action + '_data' ];
		
		if( track_params && track_params.length > 0 ) data.products.forEach( prod_a => {

			let prod = prod_a[0];

			if ( track_params.includes('p_id') ) hj( 'event', event_name + ' prod id: ' + FP.fns.get_woo_prod_id( prod ) );
			if ( track_params.includes('p_name') ) hj( 'event', event_name + ' prod: ' + FP.fns.get_woo_prod_name( prod ) );
		} );
		
	}

	if ( fp.hotj['tag_woo_addtocart'] ){
		FP.addAction( ['woo_add_to_cart'], data => {
			track_items( data, 'woo add to cart', 'addtocart' );
		});

		if ( fp.woo.cart_to_track ) track_items( fp.woo.cart_to_track, 'woo add to cart', 'addtocart' ); // when ATC is tracked in cart
	}

	if ( fp.hotj['tag_woo_removefromcart'] ){
		FP.addAction( ['woo_remove_from_cart'], data => {
			track_items( data, 'woo remove from cart', 'removefromcart' );
		} );
	}

	if ( fp.hotj['tag_woo_addtowishlist'] ){
		FP.addAction( ['woo_add_to_wishlist'], data => {
			track_items( data, 'woo add to wishlist', 'addtowishlist' );
		} );
	}

	// TRACK CHECKOUT
	// TRACK ORDER

	function track_cart( type ){ // type can be either "checkout" or "order"
		
		let track_params = type == 'checkout' ? fp.hotj[ 'tag_woo_checkouts_data' ] : fp.hotj[ 'tag_woo_purchases_data' ],
			event_name = 'woo ' + type;
		
		hj( 'event', event_name );
		if ( fp.main.debug ) console.log( '[FP] Hotjar "' + event_name + '" event');

		if ( ! track_params ) return;
		
		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			cart = type == 'checkout' ? fpdata.woo.cart : fpdata.woo.order;

		if ( track_params && track_params.length > 0 ) {
			for ( const id in cart[items_type] ) {
				let prod = cart[items_type][id];

				if ( track_params.includes('p_id') ) hj( 'event', event_name + ' prod id: ' + FP.fns.get_woo_prod_id( prod ) );
				if ( track_params.includes('p_name') ) hj( 'event', event_name + ' prod: ' + FP.fns.get_woo_prod_name( prod ) );
			}
		}

		if ( type == 'order' && track_params.includes('id') ) {
			// track order IDs if hotjar is NOT in privacy mode (this means that it is loaded after required consents)
			if ( ! fp.hotj.data_suppression ) hj( 'event', 'order ' + fpdata.woo.order.id )
		};
	}

	// track order
	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && fp.hotj.tag_woo_purchases && ! fpdata.refreshed ) {
		track_cart('order');
	}

	// track cart at the start of checkout (except when the whole page or its part is refreshed)
	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && fp.hotj.tag_woo_checkouts && ! fpdata.refreshed ) {
		if (! fp.hotj.woo_checkout_tracked ) {
			fp.hotj.woo_checkout_tracked = true;
			track_cart('checkout');
		}
	}

	FP.loaded('hotj-woo');
};

FP.load('hotj-woo','fupi_hotj_footer_woo', ['hotj','footer-helpers','woo']);