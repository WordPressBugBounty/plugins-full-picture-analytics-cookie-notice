function fupi_tik_footer_woo(){
	
	function get_item( prod, qty ){

		let category = prod.categories && prod.categories.length > 0 ? prod.categories[0] : 'All products';
		
		return { 
			'content_id' : FP.fns.get_woo_prod_id( prod ),
			'content_name': FP.fns.get_woo_prod_name( prod ),
			'content_category' : category,
			'content_type' : 'product',
			'quantity' : parseInt(qty),
			'price' : prod.price,
		}
	}
	
	// TRACK IMPRESSIONS
	
	function track_woo_impress() {
		
		if ( ! fpdata.woo.lists.single ) return;
		if ( ! fp.woo.tik ) fp.woo.tik = { 'single' : [] };
		
		let items_a = [],
			value = 0,
			item_ids = fpdata.woo.lists.single.filter( id => ! fp.woo.tik.single.includes(id) ); // track only items that were not tracked before
		
		if ( item_ids.length == 0 ) return;

		item_ids.forEach( id => {
			let prod = fpdata.woo.products[id];
			items_a.push( get_item( prod, 1 ) );
			value += prod.price;
		});

		// prevent double tracking in case the next prods are added dynamically
		fp.woo.tik.single.push(...item_ids);

		let payload_o = { 
			'contents' : items_a, 
			'currency' : fpdata.woo.currency, 
			'value' : FP.fns.round_woo_money( value ),
			'content_type' : 'product',
		};

		FP.fns.track_tik_evt( 'ViewContent', payload_o );
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress();
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	// TRACK DEFAULT VARIANT VIEW
	// TRACK VARIANT VIEWS

	function woo_variant_view( variant_id ){

		let prod = fpdata.woo.products[variant_id],
			item = get_item(prod, 1),
			payload_o = {
				'contents': [item],
				'currency': fpdata.woo.currency,
				'value': FP.fns.round_woo_money( prod.price, prod.currency_minor_unit ),
				'content_type': 'product'
			};

		FP.fns.track_tik_evt('ViewContent', payload_o);		
	}

	FP.addAction( ['woo_variant_view'], woo_variant_view );
	FP.addAction( ['woo_def_variant_view'], woo_variant_view );

	// TRACK ADD TO CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name ){
		
		let products_data = data.products,
			items_a = [];

		if ( products_data.length == 0 ) return;
		
		products_data.forEach( prod_a => {
			let prod = prod_a[0],
				qty = prod_a[1] || prod.qty || 1;

			items_a.push( get_item( prod, qty ) );
		} );

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'contents' : items_a, 
			'value' : FP.fns.round_woo_money( data.value, data.currency_minor_unit ),
			'currency' : FP.fns.get_woo_currency( data ),
			'content_type' : 'product',
		};

		FP.fns.track_tik_evt( event_name, payload_o );
	};

	FP.addAction( ['woo_add_to_cart'], data =>{
		track_items( data, 'AddToCart' );
	} );

	if ( fp.woo.cart_to_track ) track_items( fp.woo.cart_to_track, 'AddToCart' ); // when ATC is tracked in cart

	FP.addAction( ['woo_add_to_wishlist'], data => {
		track_items( data, 'AddToWishlist');
	} );

	// TRACK CHECKOUT
	// TRACK ORDER

	function track_cart( type ){ // type can be either "checkout" or "order"

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			items_a = [],
			cart = type == 'checkout' ? fpdata.woo.cart : fpdata.woo.order,
			event_name = 'InitiateCheckout';

		if ( type == 'order' ){
			event_name = ! fp.tik.purchase_evt_name || fp.tik.purchase_evt_name == 'old' ? 'PlaceAnOrder' : 'Purchase'
		}

		for ( const id in cart[items_type] ) {
			let prod = cart[items_type][id];
			items_a.push( get_item( prod, prod.qty ) );
		}

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'contents' : items_a, 
			'value' : FP.fns.round_woo_money( cart.value, cart.currency_minor_unit ),
			'currency' : FP.fns.get_woo_currency( cart ),
			'content_type' : 'product',
		};
		
		FP.fns.track_tik_evt( event_name, payload_o );
	}

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		track_cart('order');
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.tik.woo_checkout_tracked ) {
			fp.tik.woo_checkout_tracked = true;
			track_cart('checkout');
		}
	}

	FP.loaded('tik-woo');
}

FP.load('tik-woo', 'fupi_tik_footer_woo', ['footer-helpers', 'tik', 'woo']);
