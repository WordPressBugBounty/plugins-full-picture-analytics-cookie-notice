function fupi_pla_footer_woo(){

	function track_pla_event( event_name, event_goal_id, payload ){
		if ( typeof plausible !== 'undefined' ) plausible( event_goal_id, { 'props' : payload } );
		if ( fp.main.debug ) console.log('[FP] Plausible "' + event_name + '" goal (id: ' + event_goal_id + '):', { 'props' : payload } );
	}

	// TRACK ADD TO CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name, event_goal_id ){
		
		let products_data = data.products;
		
		products_data.forEach( prod_a => {

			let prod = prod_a[0],
				qty = prod_a[1] || prod.qty || 1,
				item = {
				'product_id' : FP.fns.get_woo_prod_id( prod ),
				'product_name' : FP.fns.get_woo_prod_name( prod ),
				'product_price' : prod.price,
				'product_quantity' : parseInt(qty),
			};
			
			if ( prod.categories && prod.categories.length > 0 ) {
				item['product_category'] = prod.categories[0];
			};

			track_pla_event( event_name, event_goal_id, item );
		} );
	};

	if ( fp.pla.track_woo_addtocart ) {
		FP.addAction( ['woo_add_to_cart'], data =>{
			track_items( data, 'add to cart', fp.pla.track_woo_addtocart );
		} );

		if ( fp.woo.cart_to_track ) track_items( fp.woo.cart_to_track, 'add to cart', fp.pla.track_woo_addtocart ); // when ATC is tracked in cart
	}
	
	if ( fp.pla.track_woo_addtowishlist ) {
		FP.addAction( ['woo_add_to_wishlist'], data => {
			track_items( data, 'add to wishlist', fp.pla.track_woo_addtowishlist );
		} );
	}

	// TRACK CHECKOUT

	function track_checkout(){

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			cart = fpdata.woo.cart;

		if ( fp.pla.track_woo_checkout_items ) {
			for ( const id in cart[items_type] ) {
	
				let prod = cart[items_type][id],
					item = {
						'product_id' : FP.fns.get_woo_prod_id( prod ),
						'product_name' : FP.fns.get_woo_prod_name( prod ),
						'product_price' : prod.price,
						'product_quantity' : prod.qty,
					};

				if ( prod.categories && prod.categories.length > 0 ) {
					item['product_category'] = prod.categories[0];
				};
				
				track_pla_event( 'items in checkout', fp.pla.track_woo_checkout_items, item );
			}
		}
		
		let payload_o = {
			'checkout_value' : FP.fns.round_woo_money( cart.value, cart.currency_minor_unit ),
			'currency' : FP.fns.get_woo_currency( cart ),
		};

		track_pla_event( 'checkout', fp.pla.track_woo_checkouts, payload_o );
	}

	// track the start of checkout (except when the whole page or its part is refreshed)
	if ( fp.pla.track_woo_checkouts && ! fpdata.refreshed ) {
		if ( fp.woo.checkout_data_ready ) {
			fp.pla.woo_checkout_tracked = true;
			track_checkout();
		} else {
			document.addEventListener( 'fupi_woo_checkout_data_ready', ()=>{
				if ( ! fp.pla.woo_checkout_tracked ) {
					fp.pla.woo_checkout_tracked = true;
					track_checkout();
				}
			})
		};
	}

	// TRACK PURCHASE

	function track_purchase(){ // type can be either "checkout" or "order"

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			order = fpdata.woo.order;

		if ( fp.pla.track_woo_purchased_items ) {
			for ( const id in order[items_type] ) {
	
				let prod = order[items_type][id],
					item = {
						'product_id' : FP.fns.get_woo_prod_id( prod ),
						'product_name' : FP.fns.get_woo_prod_name( prod ),
						'product_price' : prod.price,
						'product_quantity' : prod.qty,
					};
	
				if ( prod.categories && prod.categories.length > 0 ) {
					item['product_category'] = prod.categories[0];
				};
				
				track_pla_event( 'items in checkout', fp.pla.track_woo_purchased_items, item );
			}
		}

		let payload_o = {
			'props' : {
				'order_id' : order.id,
				'order_value' : FP.fns.round_woo_money( order.value, order.currency_minor_unit ),
			},
			'revenue' : {
				'amount' : FP.fns.round_woo_money( order.value, order.currency_minor_unit ),
				'currency' : FP.fns.get_woo_currency( order ),
			}
		};

		if ( fp.woo.incl_tax_in_price && order.tax && order.tax > 0 ) payload_o['order_tax'] = order.tax;
		if ( fp.woo.incl_shipping_in_total && order.shipping && order.shipping > 0 ) payload_o['order_shipping'] = order.shipping;
		if ( order.coupons.length > 0 ) payload_o['order_used_coupon'] = order.coupons[0];
		
		if ( typeof plausible !== 'undefined' ) plausible( fp.pla.track_woo_purchases, payload_o );
		if ( fp.main.debug ) console.log('[FP] Plausible "checkout" goal (id: ' + fp.pla.track_woo_purchases + '):', payload_o );
	}

	if ( fp.pla.track_woo_purchases && fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		track_purchase();
	}

	FP.loaded('pla-woo');
}

FP.load('pla-woo', 'fupi_pla_footer_woo', ['pla','footer-helpers', 'woo']);
