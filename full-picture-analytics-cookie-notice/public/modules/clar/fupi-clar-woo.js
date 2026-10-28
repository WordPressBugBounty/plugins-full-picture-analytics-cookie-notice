function fupi_clar_woo_footer(){

	// TRACK IMPRESSIONS

	function track_woo_impress( caller_id ) {
		
		if ( ! fpdata.woo.lists.single ) return;
		if ( ! fp.woo.clar ) fp.woo.clar = { 'single' : [] };
		
		let item_ids = fpdata.woo.lists.single.filter( id => ! fp.woo.clar.single.includes(id) ), // track only items that were not tracked before
			names_a = [],
			ids_a = [];

		if ( item_ids.length == 0 ) return;

		item_ids.forEach( id => {
			
			let prod = fpdata.woo.products[id];
				
			ids_a.push( FP.fns.get_woo_prod_id(prod) );
			names_a.push( FP.fns.get_woo_prod_name(prod) );
			
		} );

		clarity( 'set', 'product view', 'product view' );

		clarity( 'set', 'product view - item id', ids_a );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: product view - item id', ids_a );

		clarity( 'set', 'product view - item name', names_a );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: product view - item name', names_a );
		
		// prevent double tracking in case the next teasers are added dynamically
		fp.woo.clar.single.push(...item_ids);
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress( 'clar' );
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	// TRACK ADD TO CART / REMOVE FROM CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name ){

		// send general event
		clarity( 'set', event_name, event_name );
		
		let products_data = data.products,
			names_a = [],
			ids_a = [];
		
		products_data.forEach( prod_a => {

			let prod = prod_a[0],
				prod_name = FP.fns.get_woo_prod_name( prod ),
				prod_id = FP.fns.get_woo_prod_id( prod );
			
			if ( ! ids_a.includes( prod_id ) ) ids_a.push( prod_id );
			if ( ! names_a.includes( prod_name ) ) names_a.push( prod_name );
		} );

		clarity( 'set', event_name + ' - item id', ids_a );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: ' + event_name + ' - item id', ids_a );

		clarity( 'set', event_name + ' - item name', names_a );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: ' + event_name + ' - item name', names_a );
	};

	FP.addAction( ['woo_add_to_cart'], data =>{
		track_items( data, 'add to cart' );
	} );

	if ( fp.woo.cart_to_track ) track_items( fp.woo.cart_to_track, 'add to cart' ); // when ATC is tracked in cart

	FP.addAction( ['woo_add_to_wishlist'], data => {
		track_items( data, 'add to wishlist');
	} );

	FP.addAction( ['woo_remove_from_cart'], data => {
		track_items( data, 'remove from cart' );
	} );

	// TRACK CHECKOUT
	// TRACK ORDER

	function track_cart( type ){ // type can be either "checkout" or "order"

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			event_name = type == 'checkout' ? 'checkout' : 'purchase',
			cart = type == 'checkout' ? fpdata.woo.cart : fpdata.woo.order,
			ids_a = [],
			names_a = [];

		for ( const id in cart[items_type] ) {

			let prod = cart[items_type][id];
			
			ids_a.push( FP.fns.get_woo_prod_id(prod) );
			names_a.push( FP.fns.get_woo_prod_name(prod) );
		}

		if ( ids_a.length == 0 ) return false;

		clarity( 'set', event_name, event_name );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: ' + event_name );

		clarity( 'set', event_name + ' - item id', ids_a );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: ' + event_name + ' - item id', ids_a );

		clarity( 'set', event_name + ' - item name', names_a );
		if ( fp.main.debug ) console.log('[FP] MS Clarity tag: ' + event_name + ' - item name', names_a );
	}

	// TRACK ORDER & CHECKOUT

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		track_cart('order');
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.clar.woo_checkout_tracked ) {
			fp.clar.woo_checkout_tracked = true;
			track_cart('checkout');
		}
	}

	FP.loaded('clar-woo');
};

FP.load('clar-woo', 'fupi_clar_woo_footer', ['clar', 'footer-helpers', 'woo']);