<?php

// GET SETTINGS

$fpdata['woo']['currency'] = get_woocommerce_currency();
$fp['woo']['variable_as_simple'] = isset( $this->woo['variable_as_simple'] );
$fp['woo']['incl_tax_in_price'] = isset( $this->woo['incl_tax_in_price'] );
$fp['woo']['incl_shipping_in_total'] = isset( $this->woo['incl_shipping_in_total'] );
$fp['woo']['sku_is_id'] = isset( $this->woo['sku_is_id'] );
$fp['woo']['dont_track_views_after_refresh'] = isset( $this->woo['refresh_no_track_views'] );

if ( isset( $this->woo['wishlist_btn_sel'] ) ) $fp['woo']['wishlist_btn_sel'] = esc_js( $this->woo['wishlist_btn_sel'] );

// GET DATA

// product
if ( is_product() ){ $fpdata['page_type'] = 'Woo Product';

// product category
} else if ( is_product_category() ){ $fpdata['page_type'] = 'Woo Product Category';

// product tag
} else if ( is_product_tag() ){ $fpdata['page_type'] = 'Woo Product Tag';

// customer account
} else if ( is_account_page() ){ $fpdata['page_type'] = 'Woo Customer Account';

// main shop page and product search
} else if ( is_shop() ){

	if ( isset($_GET['post_type']) && $_GET['post_type'] == 'product' ) {
		$fpdata['page_type'] = 'Woo Product Search';

		$search_query = get_search_query();

		if ( $search_query ) $fpdata['search_query'] = $search_query;

		global $wp_query;
		$fpdata['search_results'] = $wp_query->found_posts;
		$fpdata['page_title'] = 'Search results';

	} else {
		$fpdata['page_type'] = 'Woo Shop Page';
	}

// cart page
} else if ( is_cart() ){ $fpdata['page_type'] = 'Woo Cart';

// checkout page
} else if ( is_checkout() && !is_wc_endpoint_url( 'order-received' ) ) { $fpdata['page_type'] = 'Woo Checkout';

// order received page
} else if ( is_wc_endpoint_url( 'order-received' ) ){ $fpdata['page_type'] = 'Woo Order Received'; };

?>
