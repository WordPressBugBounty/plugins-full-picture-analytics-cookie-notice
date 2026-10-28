function fupi_mads_woo_footer(){

    let mads_page_type = 'other';
			
	switch ( fpdata.page_type ) {
		case 'Woo Product Category':
		case 'Woo Product Tag':
			mads_page_type = 'category';
		break;
		case 'Front Page':
			mads_page_type = 'home';
		break;
		case 'Woo Product':
			mads_page_type = 'product';
		break;
		case 'Woo Checkout':
			mads_page_type = 'cart'; // << there is no "checkout" in MS Ads
		break;
		case 'Woo Order Received':
			mads_page_type = 'purchase';
		break;
	};

	// TRACK IMPRESSIONS

	function track_woo_impress() {

		if ( ! fp.woo.mads ) fp.woo.mads = {};
		
		let teasers_ids_a = [],
			single_ids_a = [],
			teasers_value = 0,
			single_value = 0;

		// for each product list
		for ( let [ list_name, item_ids ] of Object.entries( fpdata.woo.lists ) ) {
			
			// track only items that were not tracked before
			if ( fp.woo.mads[list_name] ) {
				item_ids = item_ids.filter( id => ! fp.woo.mads[list_name].includes(id) );
			} else {
				fp.woo.mads[list_name] = [];
			}

			item_ids.forEach( ( id, i ) => {
				
				let prod = fpdata.woo.products[id];
				
				if ( list_name == 'single' ) {
					single_ids_a.push( FP.fns.get_woo_prod_id(prod) );
					single_value += prod.price;
				} else {
					teasers_ids_a.push( FP.fns.get_woo_prod_id(prod) );
					teasers_value += prod.price;
				}
			});

			// prevent double tracking in case the next teasers are added dynamically
			fp.woo.mads[list_name].push(...item_ids);
		};
		
		if ( teasers_ids_a.length > 0 ) {

			let payload_o = { 
				'currency' : fpdata.woo.currency, 
				'event_value' : FP.fns.round_woo_money( teasers_value ),
				'ecomm_prodid' : teasers_ids_a,
				'ecomm_pagetype' : mads_page_type,
			};
				
			window.uetq.push( 'event', 'woo list item view', payload_o );
			if ( fp.main.debug ) console.log('[FP] MS Ads "woo list item view" event action', payload_o);
		};

		if ( single_ids_a.length > 0 ) {

			let impress_evt_name = 'woo product view';
			if ( fp.woo?.force_item_view_on_url && fp.woo?.force_item_view_on_url.some( url_part => document.location.href.includes(url_part) ) ) {
				impress_evt_name = 'woo list item view';
			}

			let payload_o = { 
				'currency' : fpdata.woo.currency, 
				'event_value' : FP.fns.round_woo_money( single_value ),
				'ecomm_prodid' : single_ids_a,
				'ecomm_pagetype' : mads_page_type,
			};

			if ( fpdata.content_id ) payload_o['ecomm_category'] = fpdata.content_id;
			
			window.uetq.push( 'event', impress_evt_name, payload_o );
			if ( fp.main.debug ) console.log('[FP] MS Ads "' + impress_evt_name + '" event action', payload_o);

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
			'currency': fpdata.woo.currency,
			'event_value': FP.fns.round_woo_money( prod.price, prod.currency_minor_unit ),
			'ecomm_prodid': [FP.fns.get_woo_prod_id(prod)],
			'ecomm_pagetype': mads_page_type
		};

		if ( fpdata.content_id ) payload_o['ecomm_category'] = fpdata.content_id;

		window.uetq.push( 'event', 'woo product view', payload_o );
		if ( fp.main.debug ) console.log('[FP] MS Ads "woo product view" event action', payload_o);
	}

	FP.addAction( ['woo_variant_view'], woo_variant_view );
	FP.addAction( ['woo_def_variant_view'], woo_variant_view );

	// TRACK ADD TO CART

	function track_items( data ){
		
		let products_data = data.products,
			event_value = data.value,
			items_ids_a = [],
			items_a = [];
		
		products_data.forEach( prod_a => {

			let prod = prod_a[0],
				prod_id = FP.fns.get_woo_prod_id( prod_a[0] ),
				qty = prod_a[1] || prod.qty || 1;
			
			items_a.push(
				{
					'id' : prod_id,
					'quantity' : qty,
					'price' : prod.price,
				}
			)
			items_ids_a.push( prod_id );
		} );

		if ( items_ids_a.length == 0 ) return false;

		event_value = FP.fns.round_woo_money( event_value, data.currency_minor_unit );

		let payload_o = {
			'currency' : FP.fns.get_woo_currency( data ),
			'ecomm_prodid' : items_ids_a,
			'ecomm_pagetype' : mads_page_type,
			'ecomm_totalvalue' : event_value,
			'event_value' : event_value,
			'revenue_value' : event_value,
			'items' : items_a, 
		};

		window.uetq.push( 'event', 'woo add to cart', payload_o );
		if ( fp.main.debug ) console.log('[FP] MS Ads "woo add to cart" event action', payload_o);
	}

	FP.addAction( ['woo_add_to_cart'], data =>{
		track_items( data );
	} );

	if ( fp.woo.cart_to_track ) track_items( fp.woo.cart_to_track ); // when ATC is tracked in cart

	// TRACK CHECKOUT
	// TRACK ORDER

	function track_cart( event_name ){ // type can be either "checkout" or "order"

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			items_a = [],
			items_ids_a = [],
			cart = event_name == 'checkout' ? fpdata.woo.cart : fpdata.woo.order;

		for ( const id in cart[items_type] ) {

			let prod = cart[items_type][id],
				prod_id = FP.fns.get_woo_prod_id(prod),
				item = {
				'id' : prod_id,
				'price' : prod.price,
				'quantity' : prod.qty,
			};

			items_ids_a.push( prod_id );
			items_a.push( item );
		}

		if ( items_a.length == 0 ) return false;

		let cart_value = FP.fns.round_woo_money( cart.value, cart.currency_minor_unit );

		let payload_o = {
			'items' : items_a, 
			'ecomm_prodid' : items_ids_a,
			'ecomm_pagetype' : mads_page_type,
			'ecomm_totalvalue' : cart_value,
			'revenue_value' : cart_value,
			'currency' : FP.fns.get_woo_currency( cart ),
		};

		if ( event_name == 'purchase' ) {
			payload_o['transaction_id'] = fpdata.woo.order.id;
		}
		
		window.uetq.push( 'event', 'woo ' + event_name, payload_o );
		if ( fp.main.debug ) console.log('[FP] MS Ads "woo ' + event_name + '" event action', payload_o);
	}

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		track_cart('purchase');
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.mads.woo_checkout_tracked ) {
			fp.mads.woo_checkout_tracked = true;
			track_cart('checkout');
		}
	}

	FP.loaded('mads-woo');
}

FP.load('mads-woo','fupi_mads_woo_footer',['mads','footer-helpers','woo']);
