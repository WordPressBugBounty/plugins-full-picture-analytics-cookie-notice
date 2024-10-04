<?php

class Fupi_Woo extends Fupi_Public {
    private $sku_is_id = false;

    private $incl_tax = false;

    private $variable_as_simple = false;

    private $incl_shipping_in_total = false;

    public function __construct( $plugin_name, $version ) {
        parent::__construct( $plugin_name, $version );
        $this->incl_shipping_in_total = isset( $this->woo ) && !empty( $this->woo['incl_shipping_in_total'] );
        $this->sku_is_id = isset( $this->woo ) && !empty( $this->woo['sku_is_id'] );
        $this->incl_tax = isset( $this->woo ) && isset( $this->woo['incl_tax_in_price'] );
        $this->variable_as_simple = isset( $this->woo ) && !empty( $this->woo['variable_as_simple'] );
    }

    // ENQUEUE SCRIPTS
    public function fupi_enqueue_public_woo_scripts() {
        if ( $this->woo_enabled ) {
            /* _ */
            wp_enqueue_script(
                'fupi-woo-js',
                plugin_dir_url( __FILE__ ) . 'modules/js/fupi-woo.js',
                array('fupi-helpers-js', 'jquery', 'wp-hooks'),
                $this->version,
                true
            );
        }
    }

    // REGISTER BRAND TAXONOMY
    public function fupi_woo_brand_tax() {
        $labels = array(
            'name'                       => _x( 'Brands', 'Taxonomy General Name', 'full-picture-analytics-cookie-notice' ),
            'singular_name'              => _x( 'Brand', 'Taxonomy Singular Name', 'full-picture-analytics-cookie-notice' ),
            'menu_name'                  => __( 'Brands', 'full-picture-analytics-cookie-notice' ),
            'all_items'                  => __( 'All brands', 'full-picture-analytics-cookie-notice' ),
            'parent_item'                => __( 'Parent brand', 'full-picture-analytics-cookie-notice' ),
            'parent_item_colon'          => __( 'Parent Brand:', 'full-picture-analytics-cookie-notice' ),
            'new_item_name'              => __( 'New Brand Name', 'full-picture-analytics-cookie-notice' ),
            'add_new_item'               => __( 'Add New Brand', 'full-picture-analytics-cookie-notice' ),
            'edit_item'                  => __( 'Edit Brand', 'full-picture-analytics-cookie-notice' ),
            'update_item'                => __( 'Update Brand', 'full-picture-analytics-cookie-notice' ),
            'view_item'                  => __( 'View Brand', 'full-picture-analytics-cookie-notice' ),
            'separate_items_with_commas' => __( 'Separate brands with commas', 'full-picture-analytics-cookie-notice' ),
            'add_or_remove_items'        => __( 'Add or remove brands', 'full-picture-analytics-cookie-notice' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'full-picture-analytics-cookie-notice' ),
            'popular_items'              => __( 'Popular brands', 'full-picture-analytics-cookie-notice' ),
            'search_items'               => __( 'Search brands', 'full-picture-analytics-cookie-notice' ),
            'not_found'                  => __( 'Not Found', 'full-picture-analytics-cookie-notice' ),
            'no_terms'                   => __( 'No brands', 'full-picture-analytics-cookie-notice' ),
            'items_list'                 => __( 'Brands list', 'full-picture-analytics-cookie-notice' ),
            'items_list_navigation'      => __( 'Brands list navigation', 'full-picture-analytics-cookie-notice' ),
        );
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => false,
            'show_in_rest'      => true,
        );
        register_taxonomy( 'fupi_woo_brand', array('product'), $args );
    }

    private function get_brands( $postID ) {
        $brands_a = [];
        $brands = false;
        if ( isset( $this->woo['add_brand_tax'] ) ) {
            $brands = get_the_terms( $postID, 'fupi_woo_brand' );
        } else {
            if ( isset( $this->woo['brand_tax'] ) ) {
                $brands = get_the_terms( $postID, $this->woo['brand_tax'] );
            }
        }
        if ( $brands !== false && !is_wp_error( $brands ) && !empty( $brands ) ) {
            foreach ( $brands as $brand ) {
                $brands_a[] = $brand->name;
            }
        }
        return $brands_a;
    }

    private function get_prod_data(
        $product,
        $id,
        $parent_product = false,
        $parent_id = false
    ) {
        return array(
            'id'          => $id,
            'parent_id'   => ( !empty( $parent_id ) && $parent_id != 0 ? $parent_id : false ),
            'sku'         => $product->get_sku(),
            'parent_sku'  => ( !empty( $parent_product ) ? $parent_product->get_sku() : false ),
            'name'        => str_replace( ';', '', strip_tags( $product->get_title() ) ),
            'parent_name' => ( !empty( $parent_product ) ? str_replace( ';', '', strip_tags( $parent_product->get_title() ) ) : false ),
            'type'        => $product->get_type(),
            'price'       => ( $this->incl_tax ? round( wc_get_price_including_tax( $product ), 2 ) : round( wc_get_price_excluding_tax( $product ), 2 ) ),
            'categories'  => ( !empty( $parent_product ) ? $this->get_cats( $parent_product ) : $this->get_cats( $product ) ),
            'brand'       => ( !empty( $parent_id ) ? $this->get_brands( $parent_id ) : $this->get_brands( $id ) ),
        );
    }

    private function get_cats( $prod ) {
        $cat_ids_a = $prod->get_category_ids();
        $woo_cats = [];
        foreach ( $cat_ids_a as $id ) {
            $woo_term = get_term( $id );
            if ( !empty( $woo_term ) ) {
                $woo_cats[] = str_replace( ';', '', $woo_term->name );
            }
        }
        return $woo_cats;
    }

    private function get_cart_items_data( $cart_items, $are_order_items = false ) {
        $cart_data = array(
            'qty'          => 0,
            'subtotal_tax' => 0,
        );
        foreach ( $cart_items as $item ) {
            $product = ( $are_order_items ? $item->get_product() : $item['data'] );
            if ( empty( $product ) ) {
                return;
            }
            $product_id = $product->get_id();
            $item_qty = (float) $item['quantity'];
            $item_price = ( $this->incl_tax ? round( wc_get_price_including_tax( $product ), 2 ) : round( wc_get_price_excluding_tax( $product ), 2 ) );
            $cart_data['qty'] += $item_qty;
            // we take the original data of the product
            $parent_id = $product->get_parent_id();
            $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
            $cart_data['items'][$product_id] = $this->get_prod_data(
                $product,
                $product_id,
                $parent_product,
                $parent_id
            );
            // parent data is necessary to get proper categories. Variants do not have them attached
            $cart_data['items'][$product_id]['qty'] = $item_qty;
            $cart_data['items'][$product_id]['parent_id'] = $parent_id;
            $cart_data['subtotal_tax'] += (wc_get_price_including_tax( $product ) - wc_get_price_excluding_tax( $product )) * $item_qty;
            // if we are dealing with a variant BUT we are tracking it as simple products
            // we need to take data of cart with merged variants too
            if ( $this->variable_as_simple ) {
                // we join variable products
                if ( !empty( $parent_id ) ) {
                    // if we have product with this ID already in the cart
                    if ( !empty( $cart_data['joined_items'][$parent_id] ) ) {
                        // calc qty
                        $old_qty = $cart_data['joined_items'][$parent_id]['qty'];
                        $new_qty = $old_qty + $item_qty;
                        $cart_data['joined_items'][$parent_id]['qty'] = $new_qty;
                        // calc aver price per item
                        $old_aver_price = $cart_data['joined_items'][$parent_id]['price'];
                        $new_aver_price = ($old_aver_price * $old_qty + $item_price * $item_qty) / $new_qty;
                        $cart_data['joined_items'][$parent_id]['price'] = round( $new_aver_price, 2 );
                        // if this is a new product
                    } else {
                        // get all the data (most from the parent product)
                        $cart_data['joined_items'][$parent_id] = array(
                            'id'         => $parent_id,
                            'sku'        => $parent_product->get_sku(),
                            'name'       => str_replace( ';', '', strip_tags( $parent_product->get_title() ) ),
                            'type'       => $parent_product->get_type(),
                            'categories' => $this->get_cats( $parent_product ),
                            'brand'      => $this->get_brands( $parent_id ),
                            'price'      => $item_price,
                            'qty'        => $item_qty,
                        );
                    }
                    // and we just add all the simple ones
                } else {
                    $cart_data['joined_items'][$product_id] = $cart_data['items'][$product_id];
                    $cart_data['joined_items'][$product_id]['qty'] = $item_qty;
                }
            }
        }
        $cart_data['subtotal_tax'] = round( $cart_data['subtotal_tax'], 2 );
        return $cart_data;
    }

    // GET CART DATA (FOR CART & CHECKOUT)
    // Code reference: https://woocommerce.github.io/code-reference/classes/WC-Cart.html#method_get_shipping_total
    public function get_cart_data( $cart, $is_checkout = false ) {
        $cart_data = array(
            'fees'     => $cart->get_fees(),
            'subtotal' => ( $this->incl_tax ? round( (float) $cart->get_subtotal_tax() + (float) $cart->get_subtotal(), 2 ) : round( (float) $cart->get_subtotal(), 2 ) ),
            'discount' => ( $this->incl_tax ? round( (float) $cart->get_discount_total() + (float) $cart->get_cart_discount_tax_total(), 2 ) : round( (float) $cart->get_discount_total(), 2 ) ),
        );
        $cart_data['value'] = round( $cart_data['subtotal'] - $cart_data['discount'], 2 );
        if ( $is_checkout ) {
            $checkout_data = array(
                'coupons'         => $cart->get_applied_coupons(),
                'subtotal_no_tax' => round( (float) $cart->get_subtotal(), 2 ),
                'shipping_no_tax' => round( (float) $cart->get_shipping_total(), 2 ),
                'shipping_tax'    => round( (float) $cart->get_shipping_tax(), 2 ),
                'shipping'        => ( $this->incl_tax ? round( (float) $cart->get_shipping_tax() + (float) $cart->get_shipping_total(), 2 ) : round( (float) $cart->get_shipping_total(), 2 ) ),
                'discount_no_tax' => round( (float) $cart->get_discount_total(), 2 ),
                'discount_tax'    => round( (float) $cart->get_cart_discount_tax_total(), 2 ),
            );
            $cart_data = array_merge( $cart_data, $checkout_data );
            // add shipping to total if required
            if ( $this->incl_shipping_in_total ) {
                $cart_data['value'] += $checkout_data['shipping'];
            }
        }
        $cart_data['value'] = round( $cart_data['value'], 2 );
        $cart_items_data = $this->get_cart_items_data( $cart->get_cart() );
        $cart_data = array_merge( $cart_data, $cart_items_data );
        return $cart_data;
    }

    // GET AND OUTPUT ORDER DATA
    public function fupi_woo_get_order_data() {
        if ( !$this->woo_enabled ) {
            return;
        }
        if ( !(function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' )) ) {
            return;
        }
        global $wp;
        $order_id = ( isset( $wp->query_vars['order-received'] ) ? $wp->query_vars['order-received'] : false );
        if ( $order_id === false ) {
            return;
        }
        $order = new WC_Order($order_id);
        if ( $order->has_status( 'failed' ) ) {
            return;
        }
        // Mark order as tracked
        $thank_you_viewed = get_post_meta( $order_id, 'fupi_thankyou_viewed', true );
        if ( !(empty( $thank_you_viewed ) || isset( $_GET["trackit"] )) ) {
            return;
        }
        update_post_meta( $order_id, 'fupi_thankyou_viewed', '1' );
        // Get data
        $shipping_cost = ( $this->incl_tax ? (float) $order->get_total_shipping() + (float) $order->get_shipping_tax() : (float) $order->get_total_shipping() );
        $order_data = [
            'id'              => $order->get_order_number(),
            'fees'            => $order->get_fees(),
            'coupons'         => $order->get_coupon_codes(),
            'discount_no_tax' => round( (float) $order->get_discount_total(), 2 ),
            'discount_tax'    => round( (float) $order->get_discount_tax(), 2 ),
            'discount'        => ( $this->incl_tax ? round( (float) $order->get_discount_total() + (float) $order->get_discount_tax(), 2 ) : round( (float) $order->get_discount_total(), 2 ) ),
            'shipping_no_tax' => round( (float) $order->get_total_shipping(), 2 ),
            'shipping_tax'    => round( (float) $order->get_shipping_tax(), 2 ),
            'shipping'        => round( $shipping_cost, 2 ),
            'subtotal_no_tax' => round( (float) $order->get_subtotal(), 2 ),
            'tax'             => round( (float) $order->get_total_tax(), 2 ),
            'tracked'         => $thank_you_viewed,
        ];
        // get items
        $cart_items = $order->get_items();
        $order_data = array_merge( $order_data, $this->get_cart_items_data( $cart_items, true ) );
        // calculate subtotal
        // ( it needs to go after the get_cart_items_data() above because it calculates the subtotal_tax value )
        $order_data['subtotal'] = ( $this->incl_tax ? round( $order_data['subtotal_no_tax'] + $order_data['subtotal_tax'], 2 ) : $order_data['subtotal_no_tax'] );
        // calculate total
        $order_data['value'] = $order_data['subtotal'] - $order_data['discount'];
        if ( $this->incl_shipping_in_total ) {
            $order_data['value'] += $order_data['shipping'];
        }
        $order_data['value'] = round( $order_data['value'], 2 );
        // get user data and put it all together
        $json_order_data = json_encode( $order_data );
        $output = "fpdata['woo']['order']={$json_order_data};";
        // output customer data (premium)
        if ( method_exists( $this, 'get_customer_data__premium_only' ) ) {
            $is_premium = true;
            $customer_data = $this->get_customer_data__premium_only( $order );
            $customer_data_json = json_encode( $customer_data );
            $output .= "fpdata.user = {...fpdata.user, ...{$customer_data_json}};";
        }
        echo '<!--noptimize--><script data-no-optimize="1" >
			' . $output . ';
			fp.woo.order_data_ready = true;
		</script><!--/noptimize-->';
    }

    // CLASSIC CART ( block cart is handled by fupi_woo_block_render_mod() )
    // (we can't use <script>, because the output is filtered and removed)
    public function fupi_cart_data() {
        // Action
        $cart = WC()->cart;
        if ( !empty( $cart ) && !$cart->is_empty() ) {
            $cart_data = json_encode( $this->get_cart_data( $cart ) );
            echo "<span class='fupi_cart_data' style='display: none;'>{$cart_data}</span>";
        }
    }

    // CLASSIC CHECKOUT
    // BLOCK CHECKOUT
    // (we can't use <script>, because the output is filtered and removed)
    public function fupi_checkout_cart_data() {
        $cart = WC()->cart;
        if ( !empty( $cart ) && !$cart->is_empty() ) {
            $cart_data = json_encode( $this->get_cart_data( $cart, true ) );
            echo "<span class='fupi_cart_data' style='display: none;'>{$cart_data}</span>";
        }
    }

    // CLASSIC MINI CART
    public function fupi_classic_mini_cart_data() {
        $cart = WC()->cart;
        if ( !empty( $cart ) && !$cart->is_empty() ) {
            $cart_data = json_encode( $this->get_cart_data( $cart ) );
            echo "<!--noptimize--><script data-no-optimize='1' >\r\n\t\t\t\tif ( fpdata.woo.cart.value ) fpdata.woo.cart_old = { ...fpdata.woo.cart };\r\n\t\t\t\tfpdata.woo.cart = {$cart_data};\r\n\t\t\t</script><!--/noptimize-->";
        }
    }

    // CLASSIC MINI-CART ITEM
    // ( also added to all cart tables, but we use it only for the mini cart )
    public function fupi_classic_cart_item_id( $item_name_html, $cart_item, $cart_item_key ) {
        $product_id = ( !empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'] );
        if ( !empty( $product_id ) ) {
            return $item_name_html . "<span class='fupi_cart_item_data' style='display: none !important' data-product_id='{$product_id}'></span>";
        }
        return $item_name_html;
    }

    // ARCHIVE TEASERS
    public function fupi_woo_archive_teaser_data() {
        global $product;
        if ( empty( $product ) ) {
            return;
        }
        $id = $product->get_id();
        $parent_id = $product->get_parent_id();
        $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
        $json_data = json_encode( $this->get_prod_data(
            $product,
            $id,
            $parent_product,
            $parent_id
        ) );
        // List position and name
        global $woocommerce_loop;
        $list_name = '';
        if ( !empty( $woocommerce_loop ) ) {
            // ATTENTION! Shortcode [products] also returns teasers that have loop name "products". Hence "woo product list" can list names of typical product teasers as well as products displayed by the said shortcode
            $list_name = ( empty( $woocommerce_loop['name'] ) ? ( empty( $woocommerce_loop['is_search'] ) ? 'woo products' : 'woo search' ) : 'woo ' . $woocommerce_loop['name'] );
        }
        echo "<!--noptimize--><script data-no-optimize='1' class='fupi_prod_data' data-id='{$id}' data-list_name='{$list_name}' data-type='teaser'>FP.prepareProduct( 'teaser', {$id}, {$json_data} );</script><!--/noptimize-->";
    }

    // WOO BLOCKS
    // Filters the output of woo blocks
    // more info: https://docs.wpdebuglog.com/plugin/woocommerce/5.0.0/file/woocommerce--packages--woocommerce-blocks--src--BlockTypes--AbstractProductGrid.php/#
    // HTML for a product in a block is <li class=\"wc-block-grid__product\">...</li>
    public function fupi_woo_block_teasers( $html, $data, $product ) {
        // FILTER
        if ( is_admin() ) {
            return $html;
        }
        if ( substr( $html, -5 ) == '</li>' ) {
            $id = $product->get_id();
            $parent_id = $product->get_parent_id();
            $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
            $json_data = json_encode( $this->get_prod_data(
                $product,
                $id,
                $parent_product,
                $parent_id
            ) );
            $script = "<!--noptimize--><script data-no-optimize='1' class='fupi_prod_data' data-id='{$id}' data-type='teaser'>FP.prepareProduct( 'teaser', {$id}, {$json_data} );</script><!--/noptimize-->";
            // $script = "<!-- some text --><span class='fupi_prod_data_html' style='display: none !important' data-id='{$id}' data-type='teaser'>{$json_data}</span>";
            return substr( $html, 0, -5 ) . $script . '</li>';
        }
        return $html;
    }

    // BLOCK CART/MINI-CART
    // FSE PRODUCT ARCHIVES
    // RELATED PRODUCTS (ON SINGLE)
    public function fupi_woo_block_render_block_mod( $block_content, $block_settings ) {
        if ( !$this->woo_enabled || is_admin() ) {
            return $block_content;
        }
        // FSE product archives and related products on single product page
        if ( $block_settings['blockName'] == 'woocommerce/product-button' ) {
            global $product;
            if ( !empty( $product ) ) {
                $id = $product->get_id();
                $parent_id = $product->get_parent_id();
                $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
                $prod_data = json_encode( $this->get_prod_data(
                    $product,
                    $id,
                    $parent_product,
                    $parent_id
                ) );
                return $block_content . "<script data-no-optimize='1' class='fupi_prod_data' data-id='{$id}' data-type='teaser'>FP.prepareProduct( 'teaser', {$id}, {$prod_data} );</script>";
                // <!--noptimize--> comment removed in 7.5.1
            }
            // cart & mini cart blocks
            // data output on the cart page may double with data added by "fupi_classic_cart_data" fn above.
        } else {
            if ( $block_settings['blockName'] == 'woocommerce/mini-cart' || $block_settings['blockName'] == 'woocommerce/cart' ) {
                // get cart
                $cart = WC()->cart;
                // get products if not empty
                if ( !empty( $cart ) && !$cart->is_empty() ) {
                    $cart_data = json_encode( $this->get_cart_data( $cart ) );
                    return "<!--noptimize--><script data-no-optimize='1' >\r\n\t\t\t\t\tif ( fpdata.woo.cart.value ) fpdata.woo.cart_old = { ...fpdata.woo.cart };\r\n\t\t\t\t\tfpdata.woo.cart = {$cart_data};\r\n\t\t\t\t</script><!--/noptimize-->" . $block_content;
                }
            }
        }
        return $block_content;
    }

    // WIDGETS
    public function fupi_woo_widget_teaser_data( $args ) {
        $id = get_the_ID();
        $product = wc_get_product( $id );
        if ( empty( $product ) ) {
            return;
        }
        $parent_id = $product->get_parent_id();
        $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
        $json_data = json_encode( $this->get_prod_data(
            $product,
            $id,
            $parent_product,
            $parent_id
        ) );
        $list_name = $args['widget_id'];
        if ( str_contains( $list_name, 'recently_viewed_products' ) ) {
            $list_name = 'woo recently viewed widget';
        } else {
            if ( str_contains( $list_name, 'woocommerce_products-' ) ) {
                $list_name = 'woo products list widget';
            } else {
                if ( str_contains( $list_name, 'top_rated_products-' ) ) {
                    $list_name = 'woo top rated products widget';
                }
            }
        }
        echo "<!--noptimize--><script data-no-optimize='1' class='fupi_prod_data' data-id='{$id}' data-list_name='{$list_name}' data-type='teaser'>FP.prepareProduct( 'teaser', {$id}, {$json_data} );</script><!--/noptimize-->";
    }

    // SINGLE PRODUCTS - SIMPLE AND VARIABLE
    public function fupi_woo_prod_data() {
        global $product;
        if ( empty( $product ) ) {
            return;
        }
        // single or parent prod data
        $id = $product->get_id();
        $parent_id = $product->get_parent_id();
        $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
        $prod_data = json_encode( $this->get_prod_data(
            $product,
            $id,
            $parent_product,
            $parent_id
        ) );
        $output = "FP.prepareProduct( 'single', {$id}, {$prod_data} );";
        // variants
        $variation_ids = $product->get_children();
        foreach ( $variation_ids as $variation_id ) {
            $variant_prod = wc_get_product( $variation_id );
            if ( empty( $variant_prod ) ) {
                continue;
            }
            $variant_data = json_encode( $this->get_prod_data(
                $variant_prod,
                $variation_id,
                $product,
                $id
            ) );
            $output .= "FP.prepareProduct( 'variant', {$variation_id}, {$variant_data} );";
        }
        echo "<!--noptimize--><script data-no-optimize='1' class='fupi_prod_data' data-id='{$id}' data-type='single'>{$output}</script><!--/noptimize-->";
    }

    // SINGLE PRODUCTS - GROUPED
    // ! adds product data next to the name of each sub-product
    public function fupi_woo_extra_group_prod_data( $html, $product ) {
        // Filter
        if ( is_admin() ) {
            return $html;
        }
        // do not modify anything while editing
        $id = $product->get_id();
        $parent_id = $product->get_parent_id();
        $parent_product = ( !empty( $parent_id ) ? new WC_Product($parent_id) : false );
        $prod_data = json_encode( $this->get_prod_data(
            $product,
            $id,
            $parent_product,
            $parent_id
        ) );
        return "<!--noptimize--><script data-no-optimize='1' >FP.prepareProduct( 'group_item', {$id}, {$prod_data} );</script><!--/noptimize-->" . $html;
    }

    function fupi_woo_add_to_cart_from_url() {
        if ( !$this->woo_enabled ) {
            return;
        }
        if ( isset( $_GET['add-to-cart'] ) ) {
            $id = (int) $_GET['add-to-cart'];
            $product = wc_get_product( $id );
            if ( empty( $product ) ) {
                return;
            }
            $prod_data = json_encode( $this->get_prod_data( $product, $id ) );
            $qty = ( isset( $_GET['quantity'] ) ? (int) $_GET['quantity'] : 1 );
            echo "<!--noptimize--><script data-no-optimize='1' class='fupi_add_to_cart_prod_data'>\r\n\t\t\t\t\r\n\t\t\t\tlet prod = {$prod_data},\r\n\t\t\t\t\tqty = {$qty},\r\n\t\t\t\t\tvalue = Math.round( prod.price * qty * 100 ) / 100;\r\n\r\n\t\t\t\tsetTimeout( \r\n\t\t\t\t\t()=>{\r\n\r\n\t\t\t\t\t\tFP.doActions( \r\n\t\t\t\t\t\t\t'woo_add_to_cart', \r\n\t\t\t\t\t\t\t{ \r\n\t\t\t\t\t\t\t\t'products' : [[prod, qty]],\r\n\t\t\t\t\t\t\t\t'value' : value\r\n\t\t\t\t\t\t \t}\r\n\t\t\t\t\t\t);\r\n\t\t\t\t\t}, 200 // a little bit of extra time in case any of the footer scripts did not load\r\n\t\t\t\t);\r\n\t\t\t\t</script><!--/noptimize-->";
        }
    }

}
