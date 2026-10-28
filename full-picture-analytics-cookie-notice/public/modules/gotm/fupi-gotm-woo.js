function fupi_footer_gtm_woo(){

    // Helpers

    function push_to_gtm_dl( payload ){

        let evt_suffix = fp.gtm.compat && fp.gtm.compat == 'stape' ? '_stape' : '';

        payload.event = payload.event + evt_suffix;

        // (optional) Do not clear the "ecommerce" obj before each push
        if ( ! fp.gtm['clear_woo_data'] ) window[fp.gtm.datalayer].push({ ecommerce: null });

        window[fp.gtm.datalayer].push( payload );
        if ( fp.main.debug ) console.log( '[FP] GTM event ' + payload.event + ':', payload );
    };

	function add_categories( item, prod ){
		if ( prod.categories && prod.categories.length > 0 ) {
			prod.categories.forEach( ( cat, i ) => {
				if ( i == 0) {
					item['item_category'] = cat;
				} else {
					item['item_category' + ( i + 1 )] = cat;
				}
			} );
		};

		return item;
	};

	function add_brand( item, prod ){
		if ( prod.brand && prod.brand.length > 0 ) item['item_brand'] = prod.brand[0];
		return item;
	};

	function add_list_name( item, prod ){
		if ( prod.list_name ) item['item_list_name'] = prod.list_name;
		return item;
	}

	function add_index( item, prod ){
		if ( prod.index ) item['index'] = prod.index;
		return item;
	}

	// TRACK IMPRESSIONS

	function track_woo_impress() {

		if ( ! fp.woo.gtm ) fp.woo.gtm = {};
		
		let teasers_arr = [],
			single_arr = [],
			value = 0;

		// for each product list
		for ( let [ list_name, item_ids ] of Object.entries( fpdata.woo.lists ) ) {

			let start_index = 1;
			
			// track only items that were not tracked before
			if ( fp.woo.gtm[list_name] ) {
				item_ids = item_ids.filter( id => ! fp.woo.gtm[list_name].includes(id) );
				start_index = fp.woo.gtm[list_name].length + 1;
			} else {
				fp.woo.gtm[list_name] = [];
			};

			item_ids.forEach( ( id, i ) => {
				
				let prod = fpdata.woo.products[id],
					item = { 
					'item_id' : FP.fns.get_woo_prod_id(prod),
					'item_name': FP.fns.get_woo_prod_name(prod),
					'item_list_name' : list_name,
					'index' : i + start_index,
					'price' : prod.price,
				};

				item = add_brand( item, prod );
				item = add_categories( item, prod );

				if ( list_name == 'single' ) {
					value += prod.price;
					single_arr.push( item );
				} else {
					teasers_arr.push( item );
				}
			});

			// prevent double tracking in case the next teasers are added dynamically
			fp.woo.gtm[list_name].push(...item_ids);
		};
		
		if ( teasers_arr.length > 0 ) {

			let teasers_payload_o = {
				'event' : 'view_item_list',
				'ecommerce' : {
					'currency' : fpdata.woo.currency,
					'items' : teasers_arr,
				}
				
			};
			
			push_to_gtm_dl( teasers_payload_o );
		};

		if ( single_arr.length > 0 ) {

			let impress_evt_name = 'view_item';
			if ( fp.woo?.force_item_view_on_url && fp.woo?.force_item_view_on_url.some( url_part => document.location.href.includes(url_part) ) ) {
				impress_evt_name = 'view_item_list';
			}

			let single_payload_o = { 
				'event' : impress_evt_name,
				'ecommerce' : {
					'currency' : fpdata.woo.currency,
					'value' : FP.fns.round_woo_money( value ),
					'items' : single_arr,
				}
			};

			push_to_gtm_dl( single_payload_o );
		}
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress();
		FP.addAction( ['woo_impress'], track_woo_impress );
	}
	
	// TRACK DEFAULT VARIANT VIEW
	// TRACK VARIANT VIEWS

	function woo_variant_view( variant_id ){

		let prod = fpdata.woo.products[variant_id],
			item ={ 
				'item_id' : FP.fns.get_woo_prod_id(prod),
				'item_name': FP.fns.get_woo_prod_name(prod),
				'item_list_name' : 'single',
				'price' : prod.price,
			};

			item = add_brand( item, prod );
			item = add_categories( item, prod );

		let payload_o = {
			'event' : 'view_item',
			'ecommerce' : {
				'currency' : fpdata.woo.currency,
				'value' : FP.fns.round_woo_money( prod.price, prod.currency_minor_unit ),
				'items' : [item],
			}
		};

		push_to_gtm_dl( payload_o );
	}

	FP.addAction( ['woo_variant_view'], woo_variant_view );
	FP.addAction( ['woo_def_variant_view'], woo_variant_view );

	// TRACK TEASER CLICKS
	// TRACK ADD TO CART / REMOVE FROM CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name ){
		
		let products_data = data.products,
			event_value = FP.fns.round_woo_money( data.value, data.currency_minor_unit ),
			items_a = [];
		
		products_data.forEach( prod_a => {

			let prod = prod_a[0],
				qty = prod_a[1] || prod.qty || 1,
				item = {
				'item_id' : FP.fns.get_woo_prod_id( prod ),
				'item_name' : FP.fns.get_woo_prod_name( prod ),
				'price' : prod.price,
				'quantity' : qty,
				'currency' : fpdata.woo.currency,
			};

			item = add_index(item, prod);
			item = add_list_name(item, prod);
			item = add_brand(item, prod);
			item = add_categories(item, prod);

			items_a.push( item );
		} );

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'event' : event_name,
			'ecommerce' : {
				'items' : items_a,
				'currency' : FP.fns.get_woo_currency( data ),
				'value' : event_value,
			},
		};

		push_to_gtm_dl( payload_o );
	}

	FP.addAction( ['woo_teaser_click'], data => {
		track_items( data, 'select_item' );
	} );

	FP.addAction( ['woo_add_to_cart'], data =>{
		track_items( data, 'add_to_cart' );
	} );

	if ( fp.woo.cart_to_track ) track_items( fp.woo.cart_to_track, 'add_to_cart' ); // when ATC is tracked in cart

	FP.addAction( ['woo_add_to_wishlist'], data => {
		track_items( data, 'add_to_wishlist');
	} );

	FP.addAction( ['woo_remove_from_cart'], data => {
		track_items( data, 'remove_from_cart' );
	} );

	// TRACK CHECKOUT
	// TRACK ORDER

	function track_cart( type ){ // type can be either "checkout" or "order"

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			items_a = [],
			cart = type == 'checkout' ? fpdata.woo.cart : fpdata.woo.order,
			event_name = type == 'checkout' ? 'begin_checkout' : 'purchase';

		for ( const id in cart[items_type] ) {

			let prod = cart[items_type][id],
				item = {
				'item_id' : FP.fns.get_woo_prod_id(prod),
				'item_name' : FP.fns.get_woo_prod_name(prod),
				'price' : prod.price,
				'quantity' : prod.qty,
			};

			item = add_brand(item, prod);
			item = add_categories(item, prod);

			items_a.push( item );
		}

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'event' : event_name,
			'ecommerce' : {
				'currency' : FP.fns.get_woo_currency( cart ),
				'value' : FP.fns.round_woo_money( cart.value, cart.currency_minor_unit ),
				'items' : items_a,
			},
		};

		if ( cart.coupons && cart.coupons.length > 0 ) payload_o['coupon'] = cart.coupons.join(', ');

		if ( type == 'order' ) {
			payload_o['transaction_id'] = fpdata.woo.order.id;
			payload_o['shipping'] = fpdata.woo.order.shipping;
		}
		
		push_to_gtm_dl( payload_o );
	}

	if ( fpdata.page_type == 'Woo Order Received' && fpdata.woo.order && ! fpdata.refreshed ) {
		track_cart('order');
	}

	if ( fpdata.page_type == 'Woo Checkout' && fpdata.woo.cart && ! fpdata.refreshed ) {
		if ( ! fp.gtm.woo_checkout_tracked ) {
			fp.gtm.woo_checkout_tracked = true;
			track_cart('checkout')
		}
	}

	FP.loaded('gtm-woo');
}

FP.load('gtm-woo', 'fupi_footer_gtm_woo', ['gtm','footer-helpers','woo']);
