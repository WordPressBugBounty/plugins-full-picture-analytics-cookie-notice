<?php

$ret_text = '';

switch( $section_id ){

	case 'fupi_cook_consent':
		$ret_text = '<p>' . esc_html__( 'Choose how consent is managed for your visitors and provide the ID of the main privacy policy page on your site.', 'full-picture-analytics-cookie-notice' ) . '</p>';
	break;

    case 'fupi_cook_main':
        $ret_text = '<div>
            <p style="text-align: center;"><a href="https://wpfullpicture.com/support/documentation/cookie-notice-faq/" class="secondary-button">' . esc_html__( 'Frequently asked questions', 'full-picture-analytics-cookie-notice') . '</a></p>
        </div>';
    break;

    case 'fupi_cook_proofrec':
        $ret_text = '<div>
            <p>' . esc_html__( 'Keep proofs of your visitors\' consents. This is required by GDPR, protects against potential fines, enables audit trails and helps demonstrate responsible data handling practices.', 'full-picture-analytics-cookie-notice' ) . '</p>';
        /*
        if ( ! empty ( $this->settings['storage_location'] ) && $this->settings['storage_location'] == 'email' ) {
            $ret_text .= '<p><strong>' . esc_html__( 'View latest records of consent:', 'full-picture-analytics-cookie-notice' ) . '</strong> <a href="' . admin_url( 'admin.php?page=full_picture_cook&tab=consents_list' ) . '" class="button button-secondary" style="padding: 4px 18px;">' . esc_html__( 'View records', 'full-picture-analytics-cookie-notice' ) . '</a></p>';
        }*/

        $ret_text .= '</div>';
    break;

    case 'fupi_cook_iframes':
        $ret_text = '<div>
            <p>' . sprintf( esc_html__('YouTube videos, Google maps, social embeds and other content loaded from other platforms %3$scan track your visitors%4$s. Settings on this page let you replace them with placeholders and load them after visitors agree to tracking. %1$sSee examples%2$s.','full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-iframes-manager-works-and-how-to-set-it-up/" target="_blank">', '</a>', '<strong>', '</strong>' ) . '</p>
            <p>' . sprintf ( esc_html__( 'Attention. To use placeholders with unique background images or block content loaded dynamically (after the page loads), you should use %1$sthe shortcode method or the HTML method%2$s', 'full-picture-analytics-cookie-notice' ), '<a href="https://wpfullpicture.com/support/documentation/how-to-block-iframes-manually/" target="_blank">', '</a>' ) . '</p>
        </div>';
    break;

    case 'fupi_cook_scriptblock':
        $ret_text = '<div>
           <p>' . sprintf( esc_html__('Other plugins can %3$strack your visitors%4$s. Use settings on this page to block them or their tracking scripts and load them only after visitors agree to tracking. %1$sSee examples%2$s.','full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-iframes-manager-works-and-how-to-set-it-up/" target="_blank">', '</a>', '<strong>', '</strong>' ) . '</p>
           
           <p>' . sprintf( esc_html__('Attention. If you need help with the setup, please check the "Scan page" function in the Setup Helper (enable it in the top menu). %1$sLearn more%2$s.','full-picture-analytics-cookie-notice'), '<a href="https://wpfullpicture.com/support/documentation/how-iframes-manager-works-and-how-to-set-it-up/" target="_blank">', '</a>', '<strong>', '</strong>' ) . '</p>
        </div>';
    break;
};

?>
