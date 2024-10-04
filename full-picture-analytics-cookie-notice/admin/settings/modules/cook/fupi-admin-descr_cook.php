<?php

switch( $a['id'] ){

    case 'fupi_cook_cdb':

        return '<div class="fupi_info_tabs">
            <button type="button" class="fupi_tab fupi_active">' . esc_html__( 'About ConsentsDB', 'full-picture-analytics-cookie-notice' ) . '</button>
            <div class="fupi_tab_content">
                <p>' . sprintf( esc_html__( 'Collect %3$sproofs of visitors\' tracking consents%2$s in %1$sConsentsDB%2$s - our cloud servers in the EU. By keeping consents on our servers you can prove that they were not edited before presenting them in court. Plus, it will keep your WordPress database lighter and more performant.', 'full-picture-analytics-cookie-notice' ), '<a href="https://consentsdb.com" target="_blank">', '</a>', '<a href="https://wpfullpicture.com/sdc_download/20763/?key=uttr6mmij3ssnrpyeo3myg4ys7i0y1">' ) . '</p>
                <p>' . esc_html__( 'Currently, the ConsentsDB service is only available to owners of WP Full Picture Pro, who can store 500 consents daily for free until August 31, 2025. We are working on making the ConsentsDB service available to users of WP Full Picture Free later this year.', 'full-picture-analytics-cookie-notice' ) . '</p>
                <p>' . sprintf( esc_html__( 'Check out %1$sthe planned pricing%2$s for storing consents. The final prices will be announced at the end of November.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/consentsdb-pricing/" target="_blank">', '</a>') . '</p>
            </div>
            <button type="button" class="fupi_tab">' . esc_html__( 'Installation', 'full-picture-analytics-cookie-notice' ) . '</button>
            <div class="fupi_tab_content">
                <p>' . sprintf( esc_html__( 'For a detailed installation guide %1$svisit this page%2$s', 'full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-to-start-collecting-consents-in-the-consentsdb/" target="_blank">', '</a>' ) . '</p>
            </div>
            <button type="button" class="fupi_tab">' . esc_html__( 'Important', 'full-picture-analytics-cookie-notice' ) . '</button>
            <div class="fupi_tab_content">
                <p>' . esc_html__( 'To confirm that your website was GDPR-compliant at the moment of consent, visitors\' consents are saved with a copy of your website\'s tracking configuration, a copy of your privacy policy and additional information.', 'full-picture-analytics-cookie-notice' ) . '</p>
                <p>' . sprintf( esc_html__( '%1$sDownload an example of a proof of consent%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/sdc_download/20763/?key=uttr6mmij3ssnrpyeo3myg4ys7i0y1">', '</a>' ) . '</p>
                <p style="color: red;">' . sprintf( esc_html__( 'If your website does not track visitors according to GDPR, the proof of consent will prove it. %1$sLearn all you need to know%2$s.', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/introduction-to-consentsdb/">', '</a>' ) . '</p>
                <p>' . esc_html__( 'Saving consents will only work if you give your visitors a choice to decline cookies. It will not work, if you set the consent banner to "notify" mode.', 'full-picture-analytics-cookie-notice' ) . '</p>
            </div>
        </div>';

    break;

    case 'fupi_cook_google':
        return '<p>' . sprintf( esc_html__( 'These settings will apply to Google Analytics and Google Ads installed with WP Full Picture %1$sand other plugins or methods%2$s.', 'full-picture-analytics-cookie-notice' ), '<strong>', '</strong>' ) . '</p>
        <p>' . esc_html__('If you use the Google Tag Manager module, WP Full Picture automatically sends to GTM\'s dataLayer information on user consents in the format required by Google.', 'full-picture-analytics-cookie-notice' ) . '</p>';
    break;

    default:
        return '';
    break;
};

?>
