// Contains shared Google ecommerce tracking.

function fupi_gtools_woo_footer(){

	fp.woo.gtg = {};

	let gads_enabled = fp.loaded.includes('gads'),
		ga41_enabled = fp.loaded.includes('ga41'),
		ga42_enabled = fp.loaded.includes('ga42');

	ecommerce_event_send_to = [];

	if ( ga41_enabled ) ecommerce_event_send_to.push( fp.ga41.id2 || fp.ga41.id );
	if ( ga42_enabled ) ecommerce_event_send_to.push( fp.ga42.id2 || fp.ga42.id );
	if ( gads_enabled ) ecommerce_event_send_to.push( fp.gads.id2 || fp.gads.id );

	// Helpers

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

		let teasers_arr = [],
			single_arr = [],
			value = 0;

		// for each product list
		for ( let [ list_name, item_ids ] of Object.entries( fpdata.woo.lists ) ) {

			let start_index = 1;

			// track only items that were not tracked before
			if ( fp.woo.gtg[list_name] ) {
				item_ids = item_ids.filter( id => ! fp.woo.gtg[list_name].includes(id) );
				start_index = fp.woo.gtg[list_name].length + 1;
			} else {
				fp.woo.gtg[list_name] = [];
			};

			item_ids.forEach( ( id, i ) => {

				let prod = fpdata.woo.products[id],
					prod_id = FP.fns.get_woo_prod_id( prod ),
					item = {
					'item_id' : prod_id,
					'id' : prod_id,
					'item_name': FP.fns.get_woo_prod_name(prod),
					'item_list_name' : list_name,
					'index' : i + start_index,
					'price' : prod.price,
				};

				if ( gads_enabled ) item['google_business_vertical'] = fp.gads.business_type;

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
			fp.woo.gtg[list_name].push(...item_ids);
		};

		if ( teasers_arr.length > 0 ) {

			let payload_o = {
				'items' : teasers_arr,
				'currency' : fpdata.woo.currency,
			};

			gtag( 'event', 'view_item_list', payload_o );
			if ( fp.main.debug ) console.log( '[FP] GA4/GAds view_item_list event:', payload_o );
		};

		if ( single_arr.length > 0 ) {

			let impress_evt_name = 'view_item';
			if ( fp.woo?.force_item_view_on_url && fp.woo?.force_item_view_on_url.some( url_part => document.location.href.includes(url_part) ) ) {
				impress_evt_name = 'view_item_list';
			}

			let payload_o = {
				'items' : single_arr,
				'currency' : fpdata.woo.currency,
				'value' : FP.fns.round_woo_money( value ),
				// 'send_to' : ecommerce_event_send_to
			};

			gtag( 'event', impress_evt_name, payload_o );
			if ( fp.main.debug ) console.log( '[FP] GA4/GAds ' + impress_evt_name + ' event:', payload_o );
		}
	};

	if ( ! ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) ){
		track_woo_impress();
		FP.addAction( ['woo_impress'], track_woo_impress );
	}

	// TRACK DEFAULT VARIANT VIEW
	// TRACK VARIANT VIEWS

	function woo_variant_view( variant_id ){

		if ( ! fpdata.woo.products[variant_id] ) return;

		let prod = fpdata.woo.products[variant_id],
			prod_id = FP.fns.get_woo_prod_id(prod),
			item = {
				'item_id': prod_id,
				'id' : prod_id,
				'item_name': FP.fns.get_woo_prod_name(prod),
				'item_list_name' : 'single',
				'price': prod.price
			};

		item = add_brand(item, prod);
		item = add_categories(item, prod);

		if ( gads_enabled ) item['google_business_vertical'] = fp.gads.business_type;

		let payload_o = {
			'items': [item],
			'currency': fpdata.woo.currency,
			'value': FP.fns.round_woo_money( prod.price, prod.currency_minor_unit ),
			// 'send_to' : ecommerce_event_send_to
		};

		gtag('event', 'view_item', payload_o);
		if (fp.main.debug) console.log('[FP] GA4/GAds view_item event:', payload_o);
	}

	FP.addAction( ['woo_variant_view'], woo_variant_view );
	FP.addAction( ['woo_def_variant_view'], woo_variant_view );

	// TRACK TEASER CLICKS
	// TRACK ADD TO CART / REMOVE FROM CART
	// TRACK ADD TO WISHLIST

	function track_items( data, event_name ){

		let products_data = data.products,
			event_value = data.value,
			items_a = [],
			send_event_to_gads = event_name == 'add_to_cart' && gads_enabled && fp.gads.woo_add_to_cart_conv_id;

		if ( ! send_event_to_gads && ! ga41_enabled ) return;

		products_data.forEach( prod_a => {

			let prod = prod_a[0],
				prod_id = FP.fns.get_woo_prod_id( prod ),
				qty = prod_a[1] || prod.qty || 1,
				item = {
				'item_id' : prod_id,
				'id' : prod_id,
				'item_name' : FP.fns.get_woo_prod_name( prod ),
				'price' : prod.price,
				'quantity' : qty,
			};

			item = add_index(item, prod);
			item = add_list_name(item, prod);
			item = add_brand(item, prod);
			item = add_categories(item, prod);

			if ( send_event_to_gads ) item['google_business_vertical'] = fp.gads.business_type;

			items_a.push( item );
		} );

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'items' : items_a,
			'value' : FP.fns.round_woo_money( event_value, data.currency_minor_unit ),
			'currency' : FP.fns.get_woo_currency( data ),
			// 'send_to' : ecommerce_event_send_to
		};

		gtag( 'event', event_name, payload_o );
		if ( fp.main.debug ) console.log( '[FP] GA4/GAds ' + event_name + ' event: ', payload_o );

		// GADS Conversion event
		if ( send_event_to_gads ) {
			let payload_gads = {...payload_o};
			payload_gads['send_to'] = ( fp.gads.id2 || fp.gads.id ) + '/' + fp.gads.woo_add_to_cart_conv_id;
			gtag('event', 'conversion', payload_gads );
			if ( fp.main.debug ) console.log('[FP] GAds add_to_cart conversion', payload_gads);
		}
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

		// Early returns

		let ga4_server_tracking = type == 'order' &&
			ga41_enabled &&
			fp.main.is_pro &&
			fp.ga41.server_side &&
			fp.ga41.adv_orders &&
			Array.isArray( fpdata.woo.order.server_tracking_tools ) &&
			fpdata.woo.order.server_tracking_tools.includes('ga4');

		if ( type == 'order' && ga4_server_tracking ) {
			if ( fp.main.debug ) console.log( '[FP] GA4 purchase event is tracked by the server-side script' );
			if ( ! fp.loaded.includes('gads') ) return;
		}

		// Vars

		let items_type = fp.woo.variable_tracking_method == 'track_parents' ? 'joined_items' : 'items',
			items_a = [],
			cart = type == 'checkout' ? fpdata.woo.cart : fpdata.woo.order,
			event_name = type == 'checkout' ? 'begin_checkout' : 'purchase',
			gads_conv_id = false;

		if ( gads_enabled ) {
			if ( type == 'checkout' && fp.gads.woo_checkout_conv_id ) {
				gads_conv_id = fp.gads.woo_checkout_conv_id;
			} else if ( type == 'order' && fp.gads.woo_conv_id ) {
				gads_conv_id = fp.gads.woo_conv_id;
			}
		}

		// Get items

		for ( const id in cart[items_type] ) {

			let prod = cart[items_type][id],
				prod_id = FP.fns.get_woo_prod_id(prod),
				item = {
					'item_id' : prod_id,
					'id' : prod_id,
					'item_name' : FP.fns.get_woo_prod_name(prod),
					'price' : prod.price,
					'quantity' : prod.qty,
				};

			item = add_brand(item, prod);
			item = add_categories(item, prod);

			if ( gads_enabled ) item['google_business_vertical'] = fp.gads.business_type;

			items_a.push( item );
		}

		if ( items_a.length == 0 ) return false;

		let payload_o = {
			'items' : items_a,
			'value' : FP.fns.round_woo_money( cart.value, cart.currency_minor_unit ),
			'currency' : FP.fns.get_woo_currency( cart ),
		};

		let console_text = '[FP] GA4/GAds';

		if ( cart.coupons && cart.coupons.length > 0 ) payload_o['coupon'] = cart.coupons.join(',');

		if ( type == 'order' ) {

			payload_o['transaction_id'] = fpdata.woo.order.id;
			payload_o['tax'] = fpdata.woo.order.tax || 0;
			payload_o['shipping'] = fpdata.woo.order.shipping || 0;

			// If GA4 is tracked server-side then send evt only to GAds
			if ( ga4_server_tracking && fp.loaded.includes('gads') ) {
				payload_o['send_to'] = fp.gads.id2 || fp.gads.id;
				console_text = '[FP] GAds';
			}
		}

		gtag( 'event', event_name, payload_o );
		if ( fp.main.debug ) console.log( console_text + ' ' + event_name + ' event: ', payload_o );

		// GADS Conversion event
		if ( ! gads_enabled || ! gads_conv_id ) return;

		let payload_gads = {...payload_o};

		payload_gads['send_to'] = ( fp.gads.id2 || fp.gads.id ) + '/' + gads_conv_id; // we set it again in case it was not set before
		gtag('event', 'conversion', payload_gads );
		if ( fp.main.debug ) console.log('[FP] GAds ' + event_name + ' conversion', payload_gads);
	}

	// track order
	if ( fpdata.page_type == 'Woo Order Received' && ! fpdata.refreshed ) {
		if ( fpdata.woo.order ) track_cart('order');
	}

	// track cart at the start of checkout (except when the whole page or its part is refreshed)
	if ( fpdata.page_type == 'Woo Checkout' && ! fpdata.refreshed ) {
		if ( fpdata.woo.cart && ! fp.gtag.woo_checkout_tracked ) {
			fp.gtag.woo_checkout_tracked = true;
			track_cart('checkout');
		}
	}

	FP.loaded('gtg-woo');
};

FP.load('gtg-woo', 'fupi_gtools_woo_footer', ['gtg-config', 'woo', 'footer-helpers']);
