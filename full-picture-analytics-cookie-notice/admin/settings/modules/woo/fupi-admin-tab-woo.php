<?php

$sections = array(

	array(
		'section_id' => 'fupi_woo_main',
		'section_title' => esc_html__( 'WooCommerce', 'full-picture-analytics-cookie-notice' ),
		'fields' => array(
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Use SKU as a product id','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'sku_is_id',
				'option_arr_id'		=> $option_arr_id,
				'under field'		=> esc_html__('If SKU is not set, a default product id will be used. This value must be the same as the one in your product feeds, e.g. Merchant Center feed or Facebook catalog (if you use it).','full-picture-analytics-cookie-notice'),
			),
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Track prices with tax','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'incl_tax_in_price',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Include shipping costs in order total','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'incl_shipping_in_total',
				'option_arr_id'		=> $option_arr_id,
			),
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Add to the "Orders" page an icon indicating whether the client saw the "confirmation" page','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'show_order_column',
				'must_have'			=> 'pro',
				'option_arr_id'		=> $option_arr_id,
				'popup'				=> '<h3>' . esc_html__( 'How does it work?', 'full-picture-analytics-cookie-notice' ) . '</h3>
				<p>' . esc_html__('Purchases are tracked on the "order confirmation" page. If a customer does not see it (e.g. when they do not return to your website after they make the payment) then the order will not be tracked.','full-picture-analytics-cookie-notice') . '</p>
				<p>' . esc_html__('When you enable this function, you will see a small icon next to each order on the "Orders" admin page. The icon indicates whether the order confirmation page was seen or not.','full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Track product variants as if they were parent products','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'variable_as_simple',
				'option_arr_id'		=> $option_arr_id,
				'popup2'			=> '
					<p>' . esc_html__('Some tracking tools allow you to track what products are viewed, added and removed from cart and purchased. This works great with simple, single products but it is a problem with variable products.','full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__('The moment one of the variants is added to the cart, this particular variant will be tracked in the checkout process and not the "parent" product. In other words, a "parent" product is tracked on category lists and the product details pages, but then only its variant is tracked when it is added to the cart and in the checkout process.','full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__('The result is that in your reports you will see that product X was seen in category pages and product pages but never purchased (because one of its variants was).','full-picture-analytics-cookie-notice') . '</p>
					<p>' . esc_html__('If you enable this option, WP Full Picture will track parent product instead of its variant - from the moment it was viewed until it was purchased.','full-picture-analytics-cookie-notice') . '</p>
					<h3>' . esc_html__('Read this if you run dynamic ad campaigns!','full-picture-analytics-cookie-notice') . '</h3>
					<p>' . esc_html__('If you enable this option, then your tracking tools will report that a parent product was purchased - and not its variant. If you run dynamic ad campaigns (with ads dynamically generated with a product feed), you need to make sure, that the main product data is in your product feeds, and not the data of the variants.','full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Do not track product views after a page is refreshed','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'refresh_no_track_views',
				'option_arr_id'		=> $option_arr_id,
				'after field'		=> esc_html__('Recommended for most stores','full-picture-analytics-cookie-notice'),
				'popup'				=> '<p>' . esc_html__('This option is recommended for all stores where product pages are refreshed after a product is added to cart.','full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type' 				=> 'toggle',
				'label' 			=> esc_html__('Add "brand" field to "edit product" pages','full-picture-analytics-cookie-notice'),
				'field_id' 			=> 'add_brand_tax',
				'el_class'			=> 'fupi_condition fupi_condition_reverse',
				'el_data_target'	=> 'fupi_add_brand_tax_cond',
				'option_arr_id'		=> $option_arr_id,
				'popup'				=> '<p>' . esc_html__('Brands are often required in product feeds (catalogs) made for advertising platforms, like Meta Business or Google Merchant Center.','full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type' 				=> 'taxonomies select',
				'label' 			=> esc_html__('Select a taxonomy that contains product brand','full-picture-analytics-cookie-notice'),
				'class'				=> 'fupi_add_brand_tax_cond',
				'field_id' 			=> 'brand_tax',
				'option_arr_id'		=> $option_arr_id,
				'under field'		=> '<p>' . esc_html__('Use this field if you added a "brand" field to product pages using a different plugin.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
			array(
				'type'	 			=> 'text',
				'label' 			=> esc_html__( 'Track clicks on "Add to wishlist" button', 'full-picture-analytics-cookie-notice' ),
				'under field'		=> esc_html__( 'Enter CSS class or ID', 'full-picture-analytics-cookie-notice' ),
				'field_id' 			=> 'wishlist_btn_sel',
				'option_arr_id'		=> $option_arr_id,
				'label_for' 		=> $option_arr_id . '[wishlist_btn_sel]',
				'placeholder'		=> esc_html__('e.g. .my-add-to-wishlist', 'full-picture-analytics-cookie-notice' ),
				'popup'		 		=> '<p>' . esc_html__('Enter CSS selector of an "add to wishlist" button used on your site (if it uses any). The wishlist button needs to be positioned in your HTML right after the "Add to cart" button to work. Other placements may not work correctly. The tracking doesn\'t work with grouped products.', 'full-picture-analytics-cookie-notice') . '</p>',
			),
		),
	),
);

?>