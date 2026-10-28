// WOO EVENTS

function fupi_fbp_footer_woo(){

	// TRACK IMPRESSIONS

	function track_woo_impress() {
		
		if ( ! fpdata.woo.lists.single ) return;
		if ( ! fp.woo.fbp ) fp.woo.fbp = { 'single' : [] };
		
		let items_a = [],
			value = 0,
			is_variable_prod = false,
			item_ids = fpdata.woo.lists.single.filter( id => ! fp.woo.fbp.single.includes(id) ); // track only items that were not tracked before

		item_ids.forEach( id => {
			
			let prod = fpdata.woo.products[id];
			
			if ( prod ) {
				
				let item = { 
					'id' : FP.fns.get_woo_prod_id(prod),
					'quantity' : 1,
				};
	
				if ( prod.type == 'variable' ) is_variable_prod = true;

				value += prod.price;
				items_a.push( item );
			}
		} );

		// prevent double tracking in case the next prods are added dynamically
		fp.woo.fbp.single.push(...item_ids);

		if ( items_a.length > 0 ) {

			let track_var_as_single = fp.woo.variable_tracking_method == 'track_parents',
				content_type = items_a.length == 1 && is_variable_prod && ! track_var_as_single ? 'product_group' : 'product';

			let payload_o = { 
				'contents' : items_a, 
				'currency' : fpdata.woo.currency, 
				'value' : FP.fns.round_woo_money( value ),
				'content_type' : content_type,
			};

			FP.track_fbp_evt( false, 'ViewContent', false, payload_o );
		}
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress();
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	// TRACK DEFAULT VARIANT VIEW
	// TRACK VARIANT VIEWS

	function woo_variant_view( variant_id ){

		let prod = fpdata.woo.products[variant_id];

		let payload_o = {
			'contents' : [ {
				'id' : FP.fns.get_woo_prod_id(prod),
				'quantity' : 1,
			} ],
			'currency' : fpdata.woo.currency,
			'value' : FP.fns.round_woo_money( prod.price, prod.currency_minor_unit ),
			'content_type' : 'product'
		};

		FP.track_fbp_evt( false, 'ViewContent', false, payload_o );
	}

	FP.addAction( ['woo_variant_view'], woo_variant_view );
	FP.addAction( ['woo_def_variant_view'], woo_variant_view );

	// TRACK ADD TO CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name, unload_safe = false ){
		
		let products_data = data.products,
			event_value = data.value,
			items_a = [];
		
		products_data.forEach( prod_a => {

			let prod = prod_a[0],
				qty = prod_a[1] || prod.qty || 1,
				item = {
				'id' : FP.fns.get_woo_prod_id( prod ),
				'item_price' : prod.price,
				'quantity' : parseInt(qty),
			};

			items_a.push( item );
		} );

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'contents' : items_a, 
			'value' : FP.fns.round_woo_money( event_value, data.currency_minor_unit ),
			'currency' : FP.fns.get_woo_currency( data ),
			'content_type' : 'product'
		};

		FP.track_fbp_evt( false, event_name, false, payload_o, true, { 'unloadSafe' : unload_safe } );
	};

	FP.addAction( ['woo_add_to_cart'], data =>{
		let clicked_el = fpdata?.clicked?.element,
			atc_el = clicked_el ? clicked_el.closest('.add_to_cart_button, .single_add_to_cart_button') : false,
			unload_safe = !! atc_el && ! atc_el.classList.contains('ajax_add_to_cart');

		track_items( data, 'AddToCart', unload_safe );
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
			event_name = type == 'checkout' ? 'InitiateCheckout' : 'Purchase';

		for ( const id in cart[items_type] ) {

			let prod = cart[items_type][id],
				item = {
				'id' : FP.fns.get_woo_prod_id(prod),
				'item_price' : prod.price,
				'quantity' : prod.qty,
			};

			items_a.push( item );
		}

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'contents' : items_a, 
			'value' : FP.fns.round_woo_money( cart.value, cart.currency_minor_unit ),
			'num_items': items_a.length,
			'currency' : FP.fns.get_woo_currency( cart ),
			'content_type' : 'product',
		};

		FP.track_fbp_evt( false, event_name, false, payload_o );
	}

	// TRACK ORDER & CHECKOUT

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {

		let sbot_tracking = fp.main.is_pro && 
			fp.fbp.server_side && 
			fp.fbp.adv_orders &&
			Array.isArray( fpdata.woo.order.server_tracking_tools ) &&
			fpdata.woo.order.server_tracking_tools.includes('fbp');

		if ( ! sbot_tracking ) {
			track_cart('order');
		} else {
			if ( fp.main.debug ) console.log( '[FP] Meta Pixel purchase event is tracked by the server-side script' );
		}
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.fbp.woo_checkout_tracked ) {
			fp.fbp.woo_checkout_tracked = true;
			track_cart('checkout');
		}
	}

	FP.loaded('fbp-woo');
};

FP.load('fbp-woo', 'fupi_fbp_footer_woo', ['fbp-setup', 'footer-helpers','woo']);
