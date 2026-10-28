FP.fns.mato_remember_cart_changes = ( data, should_remove, return_val = true ) => {

	// get old cart data
	var cart = FP.readCookie('fp_matomo_cart__tmp'),
		removed = [];

	cart = cart ? JSON.parse( cart ) : {};

	// update old cart data with data of newly added products
	data.products.forEach( prod_a => {

		let prod = prod_a[0],
			prod_id = FP.fns.get_woo_prod_id(prod),
			qty = prod_a[1] !== false ? (prod_a[1] || prod.qty || 1) : false;

		// if this product is already in cart
		if ( cart[prod_id] ) {

			// fix qty count (can sometimes be negative)
			if ( cart[prod_id]['qty'] < 0 ) cart[prod_id]['qty'] = 0;

			if ( ! should_remove ) {
				// increase quantity
				cart[prod_id]['qty'] += qty;
			} else {
				// remove the whole product if the quantity does not exist or is false (remove all)
				if ( qty === false || ! qty || cart[prod_id]['qty'] <= 0 ) { 
					removed.push( prod_id );
					delete cart[prod_id];
				// otherwise decrease quantity
				} else {
					// delete product if we try to remove more then there is in cart
					if ( cart[prod_id]['qty'] <= qty ) {
						removed.push( prod_id );
						delete cart[prod_id];
					} else {
						cart[prod_id]['qty'] -= qty;
					}
				}
			}

		// if it is a new product and we're adding (not removing)
		} else {
			if ( ! should_remove ) {
				let prod_cat = prod.categories && prod.categories.length > 0 ? prod.categories.slice(0,5) : [];
				
				cart[prod_id] = { 
					'name' : FP.fns.get_woo_prod_name(prod),
					'cat' : prod_cat,
					'price' : prod.price,
					'qty' : qty
				};
			}
			// If it's a new product and we're removing, do nothing (can't remove what's not there)
		}
	} );

	// Clean up cart - remove any products with qty <= 0
	Object.keys(cart).forEach(prod_id => {
		if ( cart[prod_id]['qty'] <= 0 ) {
			removed.push(prod_id);
			delete cart[prod_id];
		}
	});

	// save updated cart
	FP.setCookie('fp_matomo_cart__tmp', JSON.stringify(cart) );

	if ( return_val ) return [cart, removed];

	// Remove cart cookie when an order is made
	if ( fp.woo.order_data_ready ) FP.deleteCookie('fp_matomo_cart__tmp');
}

FP.addAction( ['woo_add_to_cart'], data =>{
	if ( ! fp.loaded.includes('mato') ) {
		if ( fp.mato.store_currency === FP.fns.get_woo_currency( data ) ) {
			FP.fns.mato_remember_cart_changes( data, false, false );
		} else {
			FP.deleteCookie('fp_matomo_cart__tmp');
		}
	}
} );

FP.addAction( ['woo_remove_from_cart'], data =>{
	if ( ! fp.loaded.includes('mato') ) {
		if ( fp.mato.store_currency === FP.fns.get_woo_currency( data ) ) {
			FP.fns.mato_remember_cart_changes( data, true, false );
		} else {
			FP.deleteCookie('fp_matomo_cart__tmp');
		}
	}
} );

function fupi_mato_footer_woo(){

	// HELPERS

	function evt_uses_store_currency( source, eventName ) {
		let currency = FP.fns.get_woo_currency( source );
		if ( fp.mato.store_currency === currency ) return true;
		if ( eventName && fp.main.debug ) console.warn( '[FP] Matomo ecommerce event "' + eventName + '" skipped. Event currency (' + currency + ') is different than store currency (' + fp.mato.store_currency + ').');
		return false;
	}

	function add_products_to_cart( cart ){
		
		let cart_value = 0;

		for (const id in cart) {
			if (Object.hasOwnProperty.call(cart, id)) {
				cart_value += cart[id]['qty'] * cart[id]['price'];
				_paq.push( [ 'addEcommerceItem', id, cart[id]['name'], cart[id]['cat'], cart[id]['price'], cart[id]['qty'] ] );
				if ( fp.main.debug ) console.log( '[FP] Matomo "addEcommerceItem" event: ', [ id, cart[id]['name'], cart[id]['cat'], cart[id]['price'], cart[id]['qty'] ] );
			}
		};

		return FP.fns.round_woo_money( cart_value );
	}

	function remove_products_from_cart( removed ) {
		removed.forEach( id => {
			_paq.push( [ 'removeEcommerceItem', id ] );
			if ( fp.main.debug ) console.log( '[FP] Matomo "removeEcommerceItem" by id:', id );
		});
	};

	function send_cart_update( cart_value ){
		_paq.push(['trackEcommerceCartUpdate', cart_value]); 
		if ( fp.main.debug ) console.log( '[FP] Matomo "trackEcommerceCartUpdate" event: ', cart_value );
	}
	
	// TRACK IMPRESSIONS

	function track_woo_impress() {
		
		if ( ! fpdata.woo.lists.single ) return;
		if ( ! fp.woo.mato ) fp.woo.mato = { 'single' : [] };
		
		let item_ids = fpdata.woo.lists.single.filter( id => ! fp.woo.mato.single.includes(id) ); // track only items that were not tracked before

		if ( item_ids.length == 0 ) return;
		
		let prod_id = item_ids[0],
			prod = fpdata.woo.products[prod_id],
			prod_tracked_id = FP.fns.get_woo_prod_id(prod),
			prod_name = FP.fns.get_woo_prod_name(prod),
			prod_cat = prod.categories && prod.categories.length > 0 ? prod.categories.slice(0,5) : [],
			prod_price = prod.price;

		if ( ! evt_uses_store_currency( prod, 'view_item' ) ) {
			_paq.push(['trackPageView']);
			return;
		}

		_paq.push( ['setEcommerceView',
			prod_tracked_id,
			prod_name,
			prod_cat,
			prod_price
		] );

		_paq.push(['trackPageView']);
		if ( fp.main.debug ) console.log( '[FP] Matomo "setEcommerceView" event: ', [ prod_tracked_id, prod_name, prod_cat, prod_price] );
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress();
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	// TRACK DEFAULT VARIANT VIEW
	// TRACK VARIANT VIEWS

	function woo_variant_view( variant_id ){
		
		let prod = fpdata.woo.products[variant_id],
		 	prod_id = FP.fns.get_woo_prod_id(prod),
			prod_name = FP.fns.get_woo_prod_name(prod),
			prod_cat = prod.categories && prod.categories.length > 0 ? prod.categories.slice(0,5) : [];

		if ( ! evt_uses_store_currency( prod, 'view_item' ) ) {
			return;
		}

		_paq.push(['setEcommerceView',
			prod_id,
			prod_name, 
			prod_cat,
			prod.price
		]);
		
		if ( fp.main.debug ) console.log('[FP] Matomo "setEcommerceView" event: ', [prod_id, prod_name, prod_cat, prod.price]);
	}

	FP.addAction( ['woo_variant_view'], woo_variant_view );
	FP.addAction( ['woo_def_variant_view'], woo_variant_view );

	// TRACK ADD TO CART

	function track_cart_changes( data, should_remove ){
		if ( ! evt_uses_store_currency( data, should_remove ? 'remove_from_cart' : 'add_to_cart' ) ) {
			return;
		}

		let [cart, removed] = FP.fns.mato_remember_cart_changes( data, should_remove ),
			cart_value = add_products_to_cart( cart );
		
		if ( removed.length > 0 ) remove_products_from_cart( removed );

		send_cart_update( cart_value );
	};

	FP.addAction( ['woo_add_to_cart'], data =>{
		_paq.push(['trackEvent', 'Ecommerce', 'Add to cart', 'Add to cart']);
		track_cart_changes( data, false );
	} );

	if ( fp.woo.cart_to_track ) {
		_paq.push(['trackEvent', 'Ecommerce', 'Add to cart', 'Add to cart']);
		track_cart_changes( fp.woo.cart_to_track, false );
	};

	FP.addAction( ['woo_remove_from_cart'], data =>{
		_paq.push(['trackEvent', 'Ecommerce', 'Remove from cart', 'Remove from cart']);
		track_cart_changes( data, true );
	} );

	// TRACK CART CONTENTS IF USER ADDED SOMETHING TO CART BEFORE AGREEING TO COOKIES

	function add_old_products(){
		if ( ! evt_uses_store_currency( fpdata.woo.cart ) ) return;
		let cart = FP.readCookie('fp_matomo_cart__tmp');
		if ( ! cart ) return;		
		cart = JSON.parse( cart );
		let cart_value = add_products_to_cart( cart );
		send_cart_update( cart_value );
	};

	add_old_products();

	// TRACK PURCHASE

	function track_purchase(){

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			order = fpdata.woo.order;

		if ( ! evt_uses_store_currency( order, 'purchase' ) ) {
			_paq.push(['trackEvent', 'Ecommerce', 'Purchase', 'Purchase']);
			FP.deleteCookie('fp_matomo_cart__tmp');
			return;
		}

		for ( const id in order[items_type] ) {

			let prod = order[items_type][id],			
				prod_tracked_id = FP.fns.get_woo_prod_id(prod),
				prod_name = FP.fns.get_woo_prod_name(prod),
				prod_cat = prod.categories && prod.categories.length > 0 ? prod.categories.slice(0,5) : [],
				prod_price = prod.price;

			_paq.push( [ 'addEcommerceItem',
				prod_tracked_id, 
				prod_name,
				prod_cat,	
				prod_price,
				prod.qty
			] );

			if ( fp.main.debug ) console.log( '[FP] Matomo "addEcommerceItem" event: ', [ prod_tracked_id, prod_name, prod_cat, prod_price, prod.qty ] );
		}

		let track_real_order_id = false;

		if ( ! fp.notice.enabled ) {
			if ( ! fp.mato.no_cookies ) {
				track_real_order_id = true;
			}
		} else {
			if ( ! ( fp.notice.mode == 'optin' || fp.notice.mode == 'optout' ) ) {
				track_real_order_id = true;
			} else {
				if ( fpdata.cookies.stats ) track_real_order_id = true;
			}
		}

		let order_id = track_real_order_id ? order.id : FP.getRandomStr(),
			order_payload = [ 'trackEcommerceOrder', 
				order_id,
				order.value,
				order.subtotal,
				fp.woo.incl_tax_in_price ? order.tax : 0,
				fp.woo.incl_shipping_in_total ? order.shipping : 0,
				order.discount
			];
		
		_paq.push( order_payload );

		_paq.push(['trackEvent', 'Ecommerce', 'Purchase', 'Purchase']);
		
		if ( fp.main.debug ) console.log( '[FP] Matomo "trackEcommerceOrder" event: ', order_payload );

		FP.deleteCookie('fp_matomo_cart__tmp');
	}

	// track order
	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		track_purchase();
	}

	FP.loaded('mato-woo');
}

FP.load('mato-woo','fupi_mato_footer_woo',['mato','footer-helpers','woo']);
