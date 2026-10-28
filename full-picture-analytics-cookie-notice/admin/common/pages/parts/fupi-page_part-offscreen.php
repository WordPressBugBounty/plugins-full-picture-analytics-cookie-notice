<?php
$fupi_help_licence = fupi_fs()->can_use_premium_code() ? 'pro' : 'free';
$fupi_help_support_href = fupi_fs()->is_not_paying()
    ? 'https://wordpress.org/support/plugin/full-picture-analytics-cookie-notice/'
    : 'https://wpfullpicture.com/contact/';
?>

<div id="fupi_help_offscreen" class="fupi_offscreen_source">
    <div class="fupi_help_sections">

        <div class="fupi_help_section">
            <strong style="display: block; text-align: center;"><?php esc_html_e( 'Video tutorials', 'full-picture-analytics-cookie-notice' ); ?></strong>
            <a class="button button-primary" style="margin-top: 10px;" href="https://wpfullpicture.com/support/documentation/tutorial-videos-for-wp-full-picture-11/" target="_blank"><?php esc_attr_e( 'Go to video tutorials', 'full-picture-analytics-cookie-notice' ); ?></a>
        </div>
        
        <div class="fupi_help_section">
            <strong style="display: block; text-align: center;"><?php esc_html_e( 'GDPR Setup Helper', 'full-picture-analytics-cookie-notice' ); ?></strong>
            <?php if ( ! empty( $this->tools['cook'] ) ) {  ?>
                <button type="button" class="button button-primary fupi_open_gdpr_helper" style="margin-top: 10px;">
                    <?php esc_html_e( 'Show GDPR status and advice', 'full-picture-analytics-cookie-notice' ); ?>
                </button>
            <?php } else { ?>
                <span style="display: block; font-size: .8em; line-height: 1.3em;"><?php esc_html_e( 'Enable the Consent Management module to access GDPR Setup Helper', 'full-picture-analytics-cookie-notice' ); ?></span>
            <?php };?>
        </div>

        <p><a href="https://wpfullpicture.com/support/documentation/tips-and-tricks/"><?php esc_html_e( 'Tips and tricks', 'full-picture-analytics-cookie-notice' ); ?></a></p>

        <p><a href="https://wpfullpicture.com/support/documentation/recommended-settings-for-wp-full-picture/" target="_blank"><?php esc_html_e( 'Recommended general settings', 'full-picture-analytics-cookie-notice' ); ?></a></p>

        <p><a href="https://wpfullpicture.com/support/documentation/how-to-set-up-woocommerce-tracking-like-a-pro/" target="_blank"><?php esc_html_e( 'Recommended WooCommerce settings', 'full-picture-analytics-cookie-notice' ); ?></a></p>
        
        <p><a href="https://wpfullpicture.com/support/documentation/troubleshooting/" target="_blank"><?php esc_html_e( 'Solutions to common problems', 'full-picture-analytics-cookie-notice' ); ?></a></p>
        
        <p><a href="https://wpfullpicture.com/support/documentation/" target="_blank"><?php esc_html_e( 'Documentation', 'full-picture-analytics-cookie-notice' ); ?></a></p>
        
        <p><a href="<?php echo esc_url( $fupi_help_support_href ); ?>" target="_blank"><?php esc_html_e( 'Support', 'full-picture-analytics-cookie-notice' ); ?></a></p>
        
        <?php if ( 'free' === $fupi_help_licence ) { ?>
            <p><a href="https://wordpress.org/support/plugin/full-picture-analytics-cookie-notice/" target="_blank"><?php esc_html_e( 'Community Forum', 'full-picture-analytics-cookie-notice' ); ?></a></p>
        <?php } ?>
    </div>
</div>

<div id="fupi_offscreen">
    <div id="fupi_offscreen_overlay"></div>
    <div id="fupi_offscreen_content_wrap">
        <button type="button" id="fupi_offscreen_close_btn"><span class="dashicons dashicons-no-alt"></span></button>
        <div id="fupi_offscreen_content"></div>
        <div id="fupi_offscreen_feedback_link"><?php echo sprintf( esc_html__('Is this text outdated or unclear? %1$sLet us know, please.%2$s', 'full-picture-analytics-cookie-notice'), '<br><a href="https://wpfullpicture.com/contact/">', '</a>' ); ?></div>
    </div>
</div>
