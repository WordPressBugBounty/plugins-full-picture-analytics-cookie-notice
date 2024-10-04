<?php

// PIXEL 1
if ( $this->fbp1_enabled ) {
    $fbp = get_option( 'fupi_fbp' );
    if ( !empty( $fbp['pixel_id'] ) ) {
        $limited_data_use = ( !empty( $fbp['limit_data_use'] ) ? '&dpo=LDU&dpoco=0&dpost=0' : '' );
        ?>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=<?php 
        esc_attr_e( $fbp['pixel_id'] );
        ?>&ev=PageView&noscript=1<?php 
        echo $limited_data_use;
        ?>"
		/></noscript>
		<!-- End Facebook Pixel #1 Code -->
		<?php 
    }
}
// GTM
if ( $this->gtm_enabled ) {
    $gtm = get_option( 'fupi_gtm' );
    if ( !empty( $gtm['id'] ) ) {
        ?>
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php 
        esc_attr_e( $gtm['id'] );
        ?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
	<?php 
    }
}
// LINKEDIN
if ( $this->linkd_enabled ) {
    $linkd = get_option( 'fupi_linkd' );
    if ( !empty( $linkd['id'] ) ) {
        ?>
		<noscript><img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=<?php 
        esc_attr_e( $linkd['id'] );
        ?>&fmt=gif" /></noscript>
	<?php 
    }
}
// MATOMO
if ( $this->mato_enabled ) {
    $mato = get_option( 'fupi_mato' );
    if ( !empty( $mato['url'] ) && !empty( $mato['id'] ) ) {
        ?>
		<noscript><img referrerpolicy="no-referrer-when-downgrade" src="<?php 
        echo esc_url( $mato['url'] );
        ?>/matomo.php?idsite=<?php 
        esc_attr_e( $mato['id'] );
        ?>&amp;rec=1" style="border:0;display:none;" alt="" /></noscript>
	<?php 
    }
}
// SIMPLE ANALYTICS
if ( $this->simpl_enabled ) {
    $simpl = get_option( 'fupi_simpl' );
    $src = ( !empty( $simpl ) && !empty( $simpl['src'] ) ? esc_url( $simpl['src'] ) . '/noscript.gif' : 'https://queue.simpleanalyticscdn.com/noscript.gif' );
    ?>
	<noscript><img height="1" width="1" style="display:none;"  src="<?php 
    echo $src;
    ?>" alt="" referrerpolicy="no-referrer-when-downgrade" /></noscript>
<?php 
}
// PINTEREST
if ( $this->pin_enabled ) {
    $pin = get_option( 'fupi_pin' );
    if ( !empty( $pin['id'] ) ) {
        ?>
		<noscript><img height="1" width="1" style="display:none;" alt=""src="https://ct.pinterest.com/v3/?event=init&tid=<?php 
        esc_attr_e( $pin['id'] );
        ?>&noscript=1" /></noscript>
	<?php 
    }
}