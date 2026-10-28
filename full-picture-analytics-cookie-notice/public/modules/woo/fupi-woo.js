function fupi_woo(){

	if ( ! fp.woo ) return;

	fp.loaded.push('woo'); // checked in other functions

	// Helpers

	FP.fns.get_woo_prod_id = prod => { // This must be equivalent to "get_woo_prod_id" in class-fupi-public-woo.php
		if ( prod.type == 'variation' && fp.woo.variable_tracking_method == 'track_parents' ) {
			return fp.woo.sku_is_id ? prod.parent_sku || prod.parent_id : prod.parent_id;
		} else {
			return fp.woo.sku_is_id ? prod.sku || prod.id : prod.id;
		};
	}

	FP.fns.get_woo_prod_name = prod => { // This must be equivalent to "get_woo_prod_name" in class-fupi-public-woo.php
		return prod.type == 'variation' && fp.woo.variable_tracking_method == 'track_parents' ? prod.parent_name || prod.name : prod.name;
	}

	FP.fns.round_woo_money = ( value, precision = fpdata.woo.currency_minor_unit ) => {
		let multiplier = Math.pow( 10, Number.isInteger( precision ) ? precision : 2 );
		return Math.round( ( Number( value ) + Number.EPSILON ) * multiplier ) / multiplier;
	}

	FP.fns.get_woo_currency = source => source?.currency || fpdata.woo.cart?.currency || fpdata.woo.currency;

	FP.fns.minor_to_major = ( value, precision ) => {
		return Number( value ) / Math.pow( 10, Number( precision ) );
	}

	FP.fns.get_woo_precision = source => {
		let precision = source?.currency_minor_unit;
		return Number.isInteger( precision ) ? precision : ( fpdata.woo.cart?.currency_minor_unit ?? fpdata.woo.currency_minor_unit ?? 2 );
	}

	function get_action_data( products, value, source = false ) {
		return {
			'products' : products,
			'value' : FP.fns.round_woo_money( value, FP.fns.get_woo_precision( source ) ),
			'currency' : FP.fns.get_woo_currency( source ),
			'currency_minor_unit' : FP.fns.get_woo_precision( source ),
		};
	}

	function apply_cart_snapshot( snapshot ) {
		
		if ( ! snapshot ) return;
		
		if ( fpdata.woo.cart?.items ) fpdata.woo.cart_old = { ...fpdata.woo.cart, items: { ...fpdata.woo.cart.items } };
		
		fpdata.woo.cart = snapshot;
		fpdata.woo.currency = snapshot.currency || fpdata.woo.currency;
		fpdata.woo.currency_minor_unit = snapshot.currency_minor_unit ?? fpdata.woo.currency_minor_unit;
	}

	function get_current_variant_id(){
		
		// Get fields with the main product ID and the currently selected variation ID
		let product_id_field = FP.findFirst( 'form.variations_form input[name="product_id"]' ),
			variant_id_field = FP.findFirst( 'form.variations_form input[name="variation_id"]' );

		if ( ! ( product_id_field && variant_id_field ) ) return false;

		let product_id = product_id_field.value,
			variant_id = variant_id_field.value;

		// Check if main prod and variant are the same
		if ( product_id == variant_id ) {

			// Mark this variant as viewed and return
			fpdata.woo.viewed_variants.push(variant_id);
			return false;
		};

		// Check if this variant has already been tracked
		if ( fpdata.woo.viewed_variants && fpdata.woo.viewed_variants.includes( variant_id ) ) return false;

		// Mark this variant as viewed
		fpdata.woo.viewed_variants.push(variant_id);

		return variant_id;
	}

	

	// PREPARE GROUPED PRODUCTS

	function prepare_grouped_prods_for_addtocart(){
	
		FP.findAll( 'form.grouped_form' ).forEach( form_el => {
	
			let item_wrap = FP.findAll('.woocommerce-grouped-product-list-item', form_el),
				dataset_info = [],
				added_value = false;
	
			if ( item_wrap.length > 0 ){
	
				// save info on products with qty > 0 in a data property in the form element
				item_wrap.forEach( item => {
					
					let qty = FP.findFirst( 'input.qty', item ),
						id = item.id.replace('product-', '');
	
					if ( id && qty && qty.value && qty.value > 0 ) {
						dataset_info.push( [ id, qty.value ] );
						added_value = true;
					}
				});
			};
			
			form_el.dataset.fupi_products = added_value ? JSON.stringify(dataset_info) : '';
		} )
	};

	setTimeout( prepare_grouped_prods_for_addtocart, 500 );
	document.addEventListener( 'change', prepare_grouped_prods_for_addtocart );
	document.addEventListener( 'click', () => { setTimeout( prepare_grouped_prods_for_addtocart, 100 ); } ); // this makes sure the script will not slow down tracking

	// PREPARE SINGLE PRODUCTS AND TEASERS
	// TRACK IMPRESSIONS OF SINGLE PRODS AND TEASERS

	function get_teaser_list_name( teaser_wrap, el ){

		let list = teaser_wrap.closest("ul") || teaser_wrap.parentElement,
			list_name = el.dataset.list_name || list && list.dataset.fupi_list_name;

		if ( list && ! list_name ) {
				
			if ( list.parentElement.parentElement.classList.contains('wp-block-woocommerce-related-products') ) {
				list_name = 'woo related block';
			} else if ( list.parentElement.classList.contains('wp-block-handpicked-products') ) {
				list_name = 'woo handpicked block';
			} else if ( list.parentElement.classList.contains('wp-block-product-best-sellers') ) {
				list_name = 'woo bestsellers block';
			} else if ( list.parentElement.classList.contains('wp-block-product-new') ) {
				list_name = 'woo new products block';
			} else if ( list.parentElement.classList.contains('wp-block-product-on-sale') ) {
				list_name = 'woo on sale block';
			} else if ( list.parentElement.classList.contains('wp-block-product-top-rated') ) {
				list_name = 'woo top rated block';
			} else if ( list.parentElement.classList.contains('wc-block-products-by-attribute') ) {
				list_name = 'woo products by attribute block';
			} else {
				list_name = 'woo products'; // default
			};
		};

		list.classList.add('fupi_products_list');
		
		// save list name in the list's data attrib
		if ( list && list_name ) list.dataset.fupi_list_name = list_name;

		return list_name;
	}

	function process_new_html_prod_data(){
		
		let prod_data_els = FP.findAll(".fupi_prod_data:not(.fupi_ready)");

		if ( prod_data_els.length == 0 ) return false;

		prod_data_els.forEach( el => {

			let list_name = 'single',
				id = el.dataset.id,
				prod_data = JSON.parse ( el.dataset.data ),
				prod_type = el.dataset.type || prod_data.type,
				prod_wrap = false,
				closest_form_element = el.closest("form.cart, form.wp-block-add-to-cart-with-options");

			// add prod data to fpdata.woo.products
			fpdata.woo.products[id] = fpdata.woo.products[id] ? { ...fpdata.woo.products[id], ...prod_data } : prod_data;
			
			// mark as processed
			el.classList.add("fupi_ready");

			// skip variants
			if ( prod_type == 'variant' ) return;

			// add classes to prod wrapper

			// if is product
			if ( closest_form_element ) {

				prod_wrap = closest_form_element.parentElement.classList.contains("summary") ? closest_form_element.parentElement : closest_form_element.parentElement.parentElement;
				prod_wrap.classList.add('fupi_woo_single_product', 'fupi_woo_product');

			// probably is teaser
			} else {

				let wrapper_selector = 'li';

				if ( fp.woo.teaser_wrapper_sel ) {
					wrapper_selector += ', ' + fp.woo.teaser_wrapper_sel;
				}

				prod_wrap = el.closest( wrapper_selector ) || el.parentElement;
				list_name = get_teaser_list_name( prod_wrap, el );
				prod_wrap.classList.add("fupi_woo_teaser", "fupi_woo_product");
				
				let btns_and_links = FP.findAll('a, button', prod_wrap );
				
				// add class to ATC buttons inside the wrapper
				btns_and_links.forEach( btn => {

					if ( btn.classList.contains('single_add_to_cart') ) return;
					if ( fp.woo.wishlist_btn_sel && btn.classList.contains( fp.woo.wishlist_btn_sel ) ) return;

					let href = btn.getAttribute('href');

					if ( btn.classList.contains('add_to_cart_button') && ( btn.tagName == 'BUTTON' || ( href && href.includes('add-to-cart=') ) ) ) {
						btn.classList.add('fupi_add_to_cart_button');
					}

				} );
			}

			// save product ID in the fpdata.woo.lists
			if ( prod_wrap ) {
				prod_wrap.dataset.fupi_woo_prod_id = id;
				fpdata.woo.lists[list_name] = fpdata.woo.lists[list_name] || [];
				if ( ! fpdata.woo.lists[list_name].includes(id) ) fpdata.woo.lists[list_name].push( id );
			}
		});

		return true;
	}

	function process_new_allprods_block_data(){

		if ( ! fp.woo.products_from_all_products_block || fp.woo.products_from_all_products_block.length == 0 ) return false;
		
		fp.woo.products_from_all_products_block.forEach( prod => {

			// get cats
			let precision = Number( prod.prices.currency_minor_unit ),
				price = FP.fns.minor_to_major( prod.prices.price, precision ),
				categories = [];

			fpdata.woo.currency = prod.prices.currency_code;
			fpdata.woo.currency_minor_unit = precision;

			prod.categories.forEach( cat => categories.push( cat.name ) );

			// get all data
			let data = {
				'categories' : categories,
				'id' : prod.id,
				'name' : prod.name,
				'sku' : prod.sku,
				'price' : price,
				'currency' : prod.prices.currency_code,
				'currency_minor_unit' : precision,
				'type' : prod.type,
				// 'parent_id', parent_sku' and 'brand' are not available in the data obj
			}
			
			// update prod in the fpdata obj
			fpdata.woo.products[prod.id] = fpdata.woo.products[prod.id] ? { ...fpdata.woo.products[prod.id], ...data } : data;

			// mark prod in HTML

			let prod_link = FP.findFirst('div.wp-block-woocommerce-all-products li.wc-block-grid__product:not(.fupi_ready) a[href="' + prod.permalink + '"]');

			if ( prod_link ) {
				
				let prod_wrap = prod_link.closest("li.wc-block-grid__product"),
					list = prod_link.closest('ul');

				if ( ! prod_wrap ) return;
				
				list_name = 'woo all products block';
				list.dataset.fupi_list_name = list_name;
				list.classList.add('fupi_products_list');

				prod_wrap.classList.add("fupi_woo_teaser", "fupi_woo_product");
				prod_wrap.dataset.fupi_woo_prod_id = prod.id;

				let add_to_cart_btns = FP.findAll('.add_to_cart_button, .ajax_add_to_cart', prod_wrap );

				add_to_cart_btns.forEach( btn => btn.classList.add('fupi_add_to_cart_button') );

				// save product data in the fpdata.woo.lists
				fpdata.woo.lists[list_name] = fpdata.woo.lists[list_name] || [];
				if ( ! fpdata.woo.lists[list_name].includes(prod.id) ) fpdata.woo.lists[list_name].push( prod.id );
			};
		});

		return true;
	}

	function prepare_prod_data(){
		
		let new_prods_added = process_new_html_prod_data();
		let new_allprods_blocks_added = process_new_allprods_block_data();
		
		if ( ! new_prods_added && ! new_allprods_blocks_added ) return;
		if ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) return;
		
		FP.doActions( "woo_impress" );
	}

	function track_def_variant_view(){

		if ( fp.woo.variable_tracking_method != 'track_def_var' ) return;
		if ( fp.woo.dont_track_views_after_refresh && fpdata.refreshed ) return;
			
		let variant_id_field = FP.findFirst( 'form.variations_form input[name="variation_id"]' );

		if ( ! variant_id_field ) return;

		let timer = 1000;

		let	variant_check_interval = setInterval( () => {

			if ( variant_id_field.value != '0' ) {
				clearInterval( variant_check_interval );

				let variant_id = get_current_variant_id();
	
				if ( variant_id && fpdata.woo.products[variant_id] && FP.hasActions( 'woo_def_variant_view' ) ) {
					FP.doActions( "woo_def_variant_view", variant_id );
				}
			}

			if ( timer >= 10000 ) {
				clearInterval( variant_check_interval );
				return;
			}

			timer += 1000;

		}, 1000 );
	}

	// Prepare teasers and single products for tracking and init tracking
	if ( document.readyState !== "loading" ) {
		prepare_prod_data();
		track_def_variant_view();
	} else {
		document.addEventListener('DOMContentLoaded', ()=>{
			prepare_prod_data();
			track_def_variant_view();
		} );
	}

	setInterval( ()=>{prepare_prod_data();}, 1000 );

	// TRACKING HELPERS

	function get_teaser_data(){

		let teaser_el = fpdata.clicked.element.closest('.fupi_woo_teaser'),
			prod_id = teaser_el.dataset.fupi_woo_prod_id;
		
		if ( prod_id && fpdata.woo.products[prod_id] ) {

			let prod = { ...fpdata.woo.products[prod_id] },
				teasers_wrapper = teaser_el.closest('.fupi_products_list');

			if ( ! teasers_wrapper ) return false;

			let	list_name = teasers_wrapper.dataset.fupi_list_name || 'woo products',
				index = fpdata.woo.lists[list_name] && fpdata.woo.lists[list_name].includes(prod_id) ? fpdata.woo.lists[list_name].indexOf(prod_id) + 1 : 1;

			prod['list_name'] = list_name;
			prod['index'] = index;

			return prod;
		}

		return false;
	}

	// TRACK CLICKS IN TEASER

	FP.addAction( ['click'], () => {

		if ( ! FP.hasActions( 'woo_teaser_click' ) ) return;

		let selector = '.fupi_woo_teaser a:not(.fupi_add_to_cart_button)' + ( fp.woo.wishlist_btn_sel ? ':not(' + fp.woo.wishlist_btn_sel + ')' : '' );

		if ( fpdata.clicked.element.closest( selector ) ) {
			let teaser_data = get_teaser_data();
			if ( teaser_data ) FP.doActions( 'woo_teaser_click', get_action_data( [[teaser_data, 1]], teaser_data.price, teaser_data ) );
		}
	} );

	// TRACK ADD TO CART FROM A TEASER

	FP.addAction( ['click'], function(){
		
		// Skip if tracking should happen in cart instead
		if ( fp.woo.where_track_addtocart === 'in_cart' ) return;
		
		if ( ! FP.hasActions( 'woo_add_to_cart' ) || ( !! fp.woo.disable_woo_events && fp.woo.disable_woo_events.includes('add_to_cart_teaser') ) ) return;

		// prevent "add to cart" event from being fired twice - 1st time when the Add to cart button is clicked and the other one when cart is updated (tracked by a different script)
		// if ( document.body.classList.contains('woocommerce-cart') ) return;

		if ( fpdata.clicked.element.closest( '.fupi_add_to_cart_button' ) ) {
			let teaser_data = get_teaser_data();
			if ( teaser_data ) FP.doActions( 'woo_add_to_cart', get_action_data( [[teaser_data, 1]], teaser_data.price, teaser_data ) );
		}
	} );

	// TRACK ADD TO CART FOR A SIMPLE, VARIABLE OR GROUPED PRODUCT

	FP.addAction( ['click'], function(){
		
		// Skip if tracking should happen in cart instead
		if ( fp.woo.where_track_addtocart === 'in_cart' ) return;
		
		if ( ! FP.hasActions( 'woo_add_to_cart' ) || ( !! fp.woo.disable_woo_events && fp.woo.disable_woo_events.includes('add_to_cart_full') ) ) return;

		if ( fpdata.clicked.element.closest( '.single_add_to_cart_button:not(.disabled)' ) ) {
			
			let form_el = fpdata.clicked.element.closest('form.cart');
				form_found = false;

			// if no classic form element
			if ( ! form_el ) {

				let atc_plus_opts_form = fpdata.clicked.element.closest('form.wp-block-add-to-cart-with-options')

				// if this is a form element from "Add to cart + options (beta) block"
				if ( atc_plus_opts_form ){

					let atc_single_btn = FP.findFirst('.add_to_cart_button.product_type_simple', atc_plus_opts_form)

					// check what type of product is being clicked
					if ( atc_single_btn ) {
						
						// we can get prod id from the button itself
						let prod_id = atc_single_btn.dataset.product_id,
							prod = prod_id ? fpdata.woo.products[prod_id] : false;
						
						if ( prod ) {

							let qty_el = FP.findFirst( '.qty', atc_plus_opts_form ),
								qty = qty_el ? qty_el.value : 1,
								value = FP.fns.round_woo_money( prod.price * qty, prod.currency_minor_unit );

							FP.doActions( 'woo_add_to_cart', get_action_data( [[prod, qty]], value, prod ) );
						}

					} else {
						if ( fp.main.debug ) console.log('[FP] At the moment only tracking single products is supported in the "Add to cart + Options (beta) block!');
					}

				} else {
					
					// this is a single button
					if ( fpdata.clicked.element?.href && fpdata.clicked.element.href.includes('add-to-cart=') ) {
						
						let prod_id = FP.getUrlParamByName( 'add-to-cart', fpdata.clicked.element.href );
	
						if ( fpdata.woo.products[prod_id] ) {
							
							let qty_from_url = FP.getUrlParamByName( 'quantity', fpdata.clicked.element.href ),
								qty = qty_from_url || 1,
								prod = fpdata.woo.products[prod_id],
								value = FP.fns.round_woo_money( prod.price * qty, prod.currency_minor_unit );
	
							FP.doActions( 'woo_add_to_cart', get_action_data( [[prod, qty]], value, prod ) );
							FP.setCookie( 'fp_last_atc', prod_id, 0 );
						}
					}
				}
			
			// track variable product
			} else if ( form_el.classList.contains('variations_form') ) {

				let prod_id = FP.findFirst( 'input.variation_id', form_el ).value;

				if ( fpdata.woo.products[prod_id] ) {

					let qty_el = FP.findFirst( 'input.qty', form_el ),
						qty = qty_el && qty_el.value && qty_el.value > 0 ? parseInt( qty_el.value ) : 1,
						prod = fpdata.woo.products[prod_id],
						value = FP.fns.round_woo_money( prod.price * qty, prod.currency_minor_unit );

					FP.doActions( 'woo_add_to_cart', get_action_data( [[prod, qty]], value, prod ) );
				}

			// track grouped product
			} else if ( form_el.classList.contains('grouped_form') ) {
				
				let prods_data_arr = form_el.dataset.fupi_products ? JSON.parse( form_el.dataset.fupi_products ) : [],
					items_a = [],
					value = 0;

				if ( prods_data_arr.length > 0 ) {
					
					prods_data_arr.forEach( prod_a => {
						
						let prod_id = prod_a[0],
							qty = parseFloat( prod_a[1] );
	
						if ( fpdata.woo.products[prod_id] ) {
							
							let prod = fpdata.woo.products[prod_id];
							
							value += prod.price * qty;
							items_a.push( [prod, qty] );
						}
					} );
	
					let context_prod = items_a.length > 0 ? items_a[0][0] : false;
					value = FP.fns.round_woo_money( value, FP.fns.get_woo_precision( context_prod ) );
	
					if ( items_a.length > 0 ) FP.doActions( 'woo_add_to_cart', get_action_data( items_a, value, context_prod ) );
				}
				

			// track simple product
			} else if ( form_el ) {

				let atc_btn = FP.findFirst( '.single_add_to_cart_button', form_el ),
					prod_id = atc_btn?.value || atc_btn.dataset?.prod_id || atc_btn.dataset?.prodid || atc_btn.dataset?.product_id || atc_btn.dataset?.pid;

				if ( !! prod_id && fpdata.woo.products[prod_id] ) {

					let qty_el = FP.findFirst( 'input.qty', form_el ),
						qty = qty_el && qty_el.value && qty_el.value > 0 ? parseInt( qty_el.value ) : 1,
						prod = fpdata.woo.products[prod_id],
						value = FP.fns.round_woo_money( prod.price * qty, prod.currency_minor_unit );

					FP.doActions( 'woo_add_to_cart', get_action_data( [[prod, qty]], value, prod ) );
				};
			}
		};
	} );

	// TRACK REMOVE ITEMS FROM CLASSIC MINI-CART

	FP.addAction( ['click'], function(){

		// Skip if tracking should happen in cart instead
		if ( fp.woo.where_track_addtocart === 'in_cart' ) return;

		if ( ! FP.hasActions( 'woo_remove_from_cart' ) ) return;

		if ( fpdata.clicked.element.closest( 'a.remove.remove_from_cart_button' ) ) {

			let product_wrap = fpdata.clicked.element.closest('li'),
				fupi_product_data_el = FP.findFirst('.fupi_cart_item_data', product_wrap),
				cart_item_key = fupi_product_data_el.dataset.cartItemKey;
			
			if ( fpdata.woo.cart.items[cart_item_key] ) {

				let prod = { ...fpdata.woo.cart.items[cart_item_key] },
					value = FP.fns.round_woo_money( prod.price * prod.qty, prod.currency_minor_unit );

				FP.doActions( 'woo_remove_from_cart', get_action_data( [[prod, false]], value, fpdata.woo.cart ) );
			}
		}
	} );

	// TRACK ADD/REMOVE ITEMS IN THE CLASSIC CART

	// PREPARE CLASSIC CART FOR TRACKING QUANTITY CHANGES
	// makes sure that adding and removing products in cart is tracked

	function prepare_classic_cart(){

		let cart_data_el = FP.findFirst('span.fupi_cart_data:not(.fupi_ready)');

		if ( cart_data_el && !! cart_data_el.innerHTML ) {

			let cart_data = JSON.parse( cart_data_el.textContent );

			apply_cart_snapshot( cart_data );
			cart_data_el.classList.add('fupi_ready'); // Mark as processed
		}
	};

	function compare_old_and_new_carts(){
		if ( ! fpdata.woo.cart_old?.items || ! fpdata.woo.cart?.items ) return;
		let currencyChanged = fpdata.woo.cart_old.currency !== fpdata.woo.cart.currency;
		
		let old_cart_items = fpdata.woo.cart_old.items,
			old_cart_item_keys = Object.keys( old_cart_items ),
			new_cart_items = fpdata.woo.cart.items,
			new_cart_item_keys = Object.keys( new_cart_items ),
			removed = [],
			removed_val = 0;
			added = [],
			added_val = 0;

		old_cart_item_keys.forEach( old_item_key => {

			let old_prod = fpdata.woo.cart_old.items[old_item_key];

			if ( new_cart_item_keys.includes( old_item_key ) ) {
				
				let old_qty = old_cart_items[old_item_key].qty,
					new_qty = new_cart_items[old_item_key].qty;

				if ( old_qty > new_qty ) {
					let qty_change = old_qty - new_qty;
					let new_prod = new_cart_items[old_item_key];
					removed.push( [new_prod, qty_change ] );
					removed_val += new_prod.price * qty_change;
				} else if ( old_qty < new_qty ) {
					let qty_change = new_qty - old_qty;
					let new_prod = new_cart_items[old_item_key];
					added.push( [new_prod, qty_change ] );
					added_val += new_prod.price * qty_change;
				};

			} else if ( ! currencyChanged ) {
				removed.push([old_prod, false]);
				removed_val += old_prod.price * old_prod.qty;
			};
		} );

		new_cart_item_keys.forEach( new_item_key => {
			if ( ! old_cart_item_keys.includes( new_item_key ) ) {
				let new_prod = fpdata.woo.cart.items[new_item_key];
				added.push( [new_prod, false ] );
				added_val += new_prod.price * new_prod.qty;
			};
		} );

		if ( added.length > 0 ) {
			FP.doActions( 'woo_add_to_cart', get_action_data( added, added_val, fpdata.woo.cart ) );
		}

		if ( removed.length > 0 ) {
			FP.doActions( 'woo_remove_from_cart', get_action_data( removed, removed_val, fpdata.woo.cart ) );
		}
	};

	// puts <span>cart_data</span> into fpdata.woo.cart. Must run on page load. We add timeout to wait for the cart contents to load
	setTimeout( prepare_classic_cart, 300 );

	// Bind event handler immediately (no timeout)
	if ( jQuery ) jQuery(document).ready(function($) {
		$('body').on('updated_cart_totals wc_fragments_refreshed wc_fragments_loaded', function( event ){
			prepare_classic_cart();
			if ( event.type == 'updated_cart_totals' ) compare_old_and_new_carts();
			update_cart_cookie();
		});
	});

	function update_cart_cookie(){

		// In-cart tracking must retain unreported additions made on other pages.
		if ( fp.woo.where_track_addtocart === 'in_cart' && fpdata.page_type != 'Woo Cart' ) return;

		// Store quantities by cart line so separately configured instances of one product do not overwrite each other.
		let cart_cookie_data = {
			line_items: {},
			currency: fpdata.woo.cart.currency,
			currency_minor_unit: fpdata.woo.cart.currency_minor_unit,
		};

		for ( let line_key in fpdata.woo.cart.items ) {
			cart_cookie_data.line_items[line_key] = fpdata.woo.cart.items[line_key].qty;
		}
		
		FP.setCookie( 'fp_woo_cart', JSON.stringify(cart_cookie_data), 90 );
	}

	// This is used when add to cart events are to be sent only in cart
	// It creates a "fp.woo.cart_to_track" object with all products that should be added to cart > this is later processed by tracking tools when they load
	function do_addToCart_in_cart(){
		
		// check if there is an "fp_woo_cart" cookie with FP.readCookie() function
		let fp_woo_cart_cookie = FP.readCookie('fp_woo_cart'),
			added = [],
			added_val = 0,
			cart_to_track = false;
		
		// if there is a cookie
		if ( fp_woo_cart_cookie ) {

			let saved_cart = JSON.parse(fp_woo_cart_cookie),
				saved_line_items = saved_cart.line_items || false;

			// Legacy cookies cannot identify which configured line their product quantity represented.
			if ( ! saved_line_items ) {
				let product_ids = Object.values( fpdata.woo.cart.items ).map( prod => String(prod.id) );
				if ( new Set(product_ids).size != product_ids.length ) {
					update_cart_cookie();
					return;
				}
			}
			
			// Compare cart-line keys and quantities. Product ID lookup supports cookies written before line keys were introduced.
			for ( let line_key in fpdata.woo.cart.items ) {
				
				let prod = fpdata.woo.cart.items[line_key],
					current_qty = prod.qty,
					saved_qty = saved_line_items ? saved_line_items[line_key] || 0 : saved_cart[prod.id] || 0;
				
				if ( current_qty > saved_qty ) {
					let qty_change = current_qty - saved_qty;
					added.push( [prod, qty_change] );
					added_val += prod.price * qty_change;
				}
			}
			
			// Track added products
			if ( added.length > 0 ) {
				cart_to_track = get_action_data( added, added_val, fpdata.woo.cart );
			}
		
		// if there is no cookie
		} else {
			
			// change object with cart items into an array
			for ( let line_key in fpdata.woo.cart.items ) {
				let prod = fpdata.woo.cart.items[line_key];
				added.push( [prod, prod.qty] );
			}
			
			if ( added.length > 0 ) cart_to_track = get_action_data( added, fpdata.woo.cart.value, fpdata.woo.cart );
		}

		fp.woo.cart_to_track = cart_to_track;
		
		update_cart_cookie();
		if ( added.length > 0 ) FP.doActions( 'woo_add_to_cart', cart_to_track );
	}

	// TRACK ADD TO WISHLIST

	if ( fp.woo.wishlist_btn_sel ){

		FP.addAction( ['click'], function(){

			if ( ! FP.hasActions( 'woo_add_to_wishlist' ) ) return;

			if ( fpdata.clicked.element.closest( fp.woo.wishlist_btn_sel ) ) {

				let product_el = fpdata.clicked.element.closest('.fupi_woo_product');
				
				if ( product_el ) {
					let prod_id = product_el.dataset.fupi_woo_prod_id,
						prod = fpdata.woo.products[prod_id];

				if ( prod ) FP.doActions( 'woo_add_to_wishlist', get_action_data( [[prod, 1]], prod.price, prod ) );
				}
			};
		} );
	};

	// TRACK VIEWED VARIANTS AS PRODUCT VIEWS

	if ( fp.woo.variable_tracking_method != 'track_parents' && fp.woo.track_variant_views ) {

		jQuery(document).ready(function($) {
			
			// Listen for variation change events on the variations form
			$( 'form.variations_form' ).on( 'woocommerce_variation_has_changed' , function() { // !! do NOT change into an arrow function
				
				let variant_id = get_current_variant_id();

				if ( variant_id && fpdata.woo.products[variant_id] && FP.hasActions( 'woo_variant_view' ) ) {
					FP.doActions( 'woo_variant_view', variant_id );
				}
			} )
		} ) 
	}

	function update_fpdata_cart_items( line_key, new_qty ){
		let prod = fpdata.woo.cart.items[line_key];
		if ( prod ) {
			if ( ! new_qty ) { // product removed
				// make sure we have a backup of this prod in "products"
				fpdata.woo.products[prod.id] = structuredClone(prod);
				delete fpdata.woo.cart.items[line_key];
			} else if ( new_qty != prod.qty ) {
				prod.qty = new_qty;
			}
		}
	};

	function add_block_hooks(){

		// "All products" block (deprecated but still used on some sites)
		if ( window.wp && window.wp.hooks ) window.wp.hooks.addAction(
			"experimental__woocommerce_blocks-product-list-render",
			"fupi-tracking",
			( {products, listName } ) => {
				if ( products.length > 0 && listName == 'woocommerce/all-products' ) {
					fp.woo.products_from_all_products_block = products;
				}
			}
		);

		// Use wp.data to subscribe to product changes in the BIG cart block
		if ( window.wp && window.wp.data && window.wp.data.select( 'wc/store/cart' ) ) {
			
			const { subscribe } = wp.data;
			const cartStore = wp.data.select( 'wc/store/cart' );
			
			let initialCartData = cartStore.getCartData();
			let previousCartItems = copy_store_items( initialCartData.items );
			let previousCartSignature = get_store_cart_signature( initialCartData );

			subscribe( () => {
				const newCartData = cartStore.getCartData();
				const newCartItems = newCartData.items;
				const newCartSignature = get_store_cart_signature( newCartData );

				if ( newCartSignature === previousCartSignature ) return;

				const newSnapshot = get_snapshot_from_store_cart( newCartData );
				apply_cart_snapshot( newSnapshot );

				if ( fp.woo.where_track_addtocart === 'in_cart' && fpdata.page_type != 'Woo Cart' ) {
					previousCartItems = copy_store_items( newCartItems );
					previousCartSignature = newCartSignature;
					return;
				}

				// Map items by key for easy comparison
				const prevMap = new Map( previousCartItems.map( i => [ i.key, i ] ) );
				const newMap = new Map( newCartItems.map( i => [ i.key, i ] ) );

				// 1. Check for REMOVED items
				for ( const [ key, prevItem ] of prevMap ) {
					if ( ! newMap.has( key ) ) {
						let prod = get_prod_from_store_item( prevItem );
						let value = FP.fns.round_woo_money( prod.price * prevItem.quantity, prod.currency_minor_unit );
						// Trigger remove action
						FP.doActions( 'woo_remove_from_cart', get_action_data( [[prod, false]], value, prod ) );
					}
				}

				// 2. Check for QUANTITY changes
				for ( const [ key, newItem ] of newMap ) {
					const prevItem = prevMap.get( key );
					
					if ( prevItem && newItem.quantity !== prevItem.quantity ) {
						
						let prod = get_prod_from_store_item( newItem );
						let qty_change = Math.abs( newItem.quantity - prevItem.quantity );
						let value = FP.fns.round_woo_money( prod.price * qty_change, newSnapshot.currency_minor_unit );

						if ( newItem.quantity > prevItem.quantity ) {
							// Quantity Increased
							FP.doActions( 'woo_add_to_cart', get_action_data( [[prod, qty_change]], value, newSnapshot ) );
						} else {
							// Quantity Decreased
							FP.doActions( 'woo_remove_from_cart', get_action_data( [[prod, qty_change]], value, newSnapshot ) );
						}
					}

					if ( ! prevItem && fp.woo.where_track_addtocart === 'in_cart' ) {
						let prod = get_prod_from_store_item( newItem );
						let value = FP.fns.round_woo_money( prod.price * newItem.quantity, newSnapshot.currency_minor_unit );
						FP.doActions( 'woo_add_to_cart', get_action_data( [[prod, false]], value, newSnapshot ) );
					}
				}

				update_cart_cookie();
				// Update previous items for next change
				previousCartItems = copy_store_items( newCartItems );
				previousCartSignature = newCartSignature;
			} );
		}

		function copy_store_items( items ) {
			return items.map( item => ({ ...item, prices: { ...item.prices } }) );
		}

		function get_store_cart_signature( cartData ) {
			return JSON.stringify( {
				items: cartData.items.map( item => [item.key, item.quantity, item.prices.price, item.prices.currency_code, item.prices.currency_minor_unit] ),
				totals: cartData.totals,
			} );
		}

		// Helper function to normalize product data from the store item
		function get_prod_from_store_item( item ) {
			let precision = Number( item.prices.currency_minor_unit );
			let existingProd = fpdata.woo.cart.items?.[item.key] || fpdata.woo.products[item.id] || {};

			return { ...existingProd,
				id: item.id,
				name: item.name,
				sku: item.sku,
				price: FP.fns.minor_to_major( item.prices.price, precision ),
				qty: item.quantity,
				currency: item.prices.currency_code,
				currency_minor_unit: precision,
			};
		}

		function get_snapshot_from_store_cart( cartData ) {
			
			let totals 			= cartData.totals || {},
				precision 		= Number( totals.currency_minor_unit ?? cartData.items?.[0]?.prices?.currency_minor_unit ?? 2 ),
				currency 		= totals.currency_code || cartData.items?.[0]?.prices?.currency_code || fpdata.woo.currency,
				items 			= {},
				joinedItems 	= {};

			cartData.items.forEach( item => {
				let prod = get_prod_from_store_item( item );
				items[item.key] = prod;

				if ( fp.woo.variable_tracking_method != 'track_parents' ) return;
				let joinedKey = prod.parent_id ? 'parent_' + prod.parent_id : 'line_' + item.key;
				if ( ! joinedItems[joinedKey] ) {
					joinedItems[joinedKey] = { ...prod };
					return;
				}

				let oldQty = joinedItems[joinedKey].qty;
				let newQty = oldQty + prod.qty;
				joinedItems[joinedKey].price = FP.fns.round_woo_money(
					( joinedItems[joinedKey].price * oldQty + prod.price * prod.qty ) / newQty,
					precision
				);
				joinedItems[joinedKey].qty = newQty;
			} );

			return {
				...fpdata.woo.cart,
				currency,
				currency_minor_unit: precision,
				items,
				joined_items: fp.woo.variable_tracking_method == 'track_parents' ? joinedItems : items,
				qty: cartData.items_count ?? cartData.items.reduce( ( total, item ) => total + Number( item.quantity ), 0 ),
				value: FP.fns.minor_to_major( totals.total_price || 0, precision ),
			};
		}
	}

	// Use the server snapshot before adapters load or cart updates overwrite the cookie.
	if ( fpdata.page_type == 'Woo Cart' && fpdata.woo.cart?.items && fp.woo.where_track_addtocart === 'in_cart' ) do_addToCart_in_cart();

	add_block_hooks();
	
	FP.loaded('woo'); // fp.loaded.push('woo'); is at the top
};

FP.load('woo', 'fupi_woo', ['footer-helpers']);
