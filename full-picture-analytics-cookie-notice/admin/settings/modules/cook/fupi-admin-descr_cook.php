<?php

switch( $a['id'] ){

    case 'fupi_cook_main':
        return '<p>' . sprintf( esc_html__( 'This consent banner manages loading all the tracking tools that are installed or managed by WP Full Picture (with the Tracking Tools Manager and Iframes Manager modules). It does not need cookie scans.', 'full-picture-analytics-cookie-notice' ), '<strong>', '</strong>' ) . '</p>';
    break;

    case 'fupi_cook_cdb':

        return '<p>' . esc_html__( 'Keep records of consents as required by GDPR.', 'full-picture-analytics-cookie-notice' ) . '</p>
            <p style="font-weight: bold">' . esc_html__( 'Under Article 7.1 GDPR, where processing is based on consent, the controller shall be able to demonstrate that the data subject has consented to processing of his or her personal data.', 'full-picture-analytics-cookie-notice' ) . '</p>
            <p style="background: #ffe7e7;padding: 15px;border-radius: 6px;">' . esc_html__( 'Attention. This is a paid service for users of WP Full Picture Free. Owners of WP Full Picture Pro can save up to 500 consents a day for free until August 31, 2025.', 'full-picture-analytics-cookie-notice' ) . '</p>
            <p style="display: flex; gap: 20px;">
                <a href="https://wpfullpicture.com/support/documentation/introduction-to-consentsdb/" class="">'. esc_html__( 'Learn more', 'full-picture-analytics-cookie-notice') .'</a>
                <a href="https://wpfullpicture.com/pricing/#hook_cdb_plans/" class="">'. esc_html__( 'Pricing', 'full-picture-analytics-cookie-notice') .'</a>
                <a href="https://consentsdb.com/signup">'. esc_html__( 'Register an account', 'full-picture-analytics-cookie-notice') .'</a>
                <a href="https://wpfullpicture.com/support/documentation/how-to-start-collecting-consents-in-the-consentsdb/" class="" target="_blank">' . esc_html__( 'Setup guide', 'full-picture-analytics-cookie-notice') . '</a>
            </p>';

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
