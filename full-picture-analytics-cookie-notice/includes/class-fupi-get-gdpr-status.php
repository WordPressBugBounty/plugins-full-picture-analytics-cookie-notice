<?php

class Fupi_compliance_status_checker {
    private $modules_info = [];

    private $tools = [];

    private $data = [];

    private $consent_status = 'alert';

    private $req_consent_banner = 'no';

    private $url_pass_enabled = false;

    private $consb_settings;

    private $format;

    private $cdb_key;

    private $is_first_reg;

    public function __construct( $output_format, $cook_settings = false, $is_first_reg = false ) {
        $this->is_first_reg = $is_first_reg;
        $this->format = $output_format;
        $this->consb_settings = ( empty( $cook_settings ) ? get_option( 'fupi_cook' ) : $cook_settings );
        $this->cdb_key = ( !empty( $this->consb_settings['cdb_key'] ) ? $this->consb_settings['cdb_key'] : false );
        $this->include_modules_datafile();
        $this->get_enabled_modules();
        $this->check_cons_banner_module();
        // goes second
        $this->check_custom_scripts_module();
        $this->check_iframeblock_module();
        $this->check_blockscr_module();
        $this->check_safefonts_module();
        $this->check_woo_module();
        $this->check_other_modules();
        $this->is_cons_banner_req();
        $this->output();
    }

    private function is_cons_banner_req() {
        $cook_data = [];
        if ( !in_array( 'cook', $this->tools ) ) {
            $info = $this->get_module_info( 'cook' );
            switch ( $this->req_consent_banner ) {
                case 'yes':
                    $cook_data = [
                        'module_name' => $info['title'],
                        'setup'       => [['alert', 'Consent banner must be enabled for your setup', 'Please enable it in either opt-in mode or one of automatic modes']],
                    ];
                    break;
                default:
                    $cook_data = [
                        'module_name' => $info['title'],
                        'setup'       => [['warning', 'You may need to use the Consent Banner module on your website.', 'Enable it if any of the tracking tools installed on your website track personaly identifiable information, your website loads content from other sites (YouTube video, maps, etc.) or gives warnings in <a href="https://2gdpr.com" target="_blank">2GDPR.com</a>.']],
                    ];
                    break;
            }
            array_unshift( $this->data, $cook_data );
        }
    }

    private function include_modules_datafile() {
        include FUPI_PATH . '/includes/fupi_modules_data.php';
        $this->modules_info = $fupi_modules;
    }

    private function get_extra_text( $status = false ) {
        if ( empty( $status ) ) {
            $status = $this->consent_status;
        }
        if ( $status == 'alert' ) {
            return 'Fix errors in consent banner settings.';
        } else {
            if ( $status == 'warning' ) {
                return 'Make sure the banner is set up correctly.';
            }
        }
        return '';
    }

    private function get_enabled_modules() {
        $tools = get_option( 'fupi_tools' );
        if ( !empty( $tools ) ) {
            $this->tools = array_keys( $tools );
        }
    }

    private function get_module_info( $id ) {
        foreach ( $this->modules_info as $module_info ) {
            if ( $module_info['id'] == $id ) {
                return $module_info;
            }
        }
    }

    private function fupi_modify_cons_banner_text( $text ) {
        $open_tag_pos = strpos( $text, '{{' );
        $close_tag_pos = strpos( $text, '}}' );
        if ( $open_tag_pos && $close_tag_pos ) {
            // get the content between {{ }}
            $regex = '/\\{\\{(.*?)\\}\\}/';
            // Replace matches with anchor tags using preg_replace
            $text = preg_replace_callback( $regex, function ( $match ) {
                $innerText = $match[1];
                // Capture inner text
                $url = get_privacy_policy_url();
                // get URL and create a link
                if ( strpos( $innerText, '|' ) > 0 ) {
                    $innerText_a = explode( '|', $innerText );
                    if ( !empty( $innerText_a[1] ) ) {
                        $url = $innerText_a[1];
                        $innerText = $innerText_a[0];
                    }
                }
                return "<a href=\"{$url}\">{$innerText}</a>";
            }, $text );
        }
        return do_shortcode( $text );
    }

    private function output() {
        if ( $this->format == 'cdb' ) {
        } else {
            if ( $this->format == 'html' ) {
                $output = '';
                foreach ( $this->data as $module_id => $checked_module_data ) {
                    // TITLE
                    $output .= '<section>
                    <h3>' . $checked_module_data['module_name'] . '</h3>';
                    // TOP COMMENT
                    if ( isset( $checked_module_data['top comments'] ) ) {
                        foreach ( $checked_module_data['top comments'] as $str ) {
                            $output .= '<p style="font-size: 15px;">' . $str . '</p>';
                        }
                    }
                    // PRE SETUP
                    if ( isset( $checked_module_data['pre-setup'] ) ) {
                        // TABLE START
                        $output .= '<table class="fupi_classic_table">
                        <tbody>';
                        foreach ( $checked_module_data['pre-setup'] as $arr ) {
                            $output .= '<tr>
                                <td class="fupi_module_status_ico"><span class="dashicons dashicons-flag" style="color:orange; font-size: 20px;"></span></td>
                                <td>' . $arr[0] . '<p class="fupi_module_status_recommend">' . $arr[1] . '</p></td>
                            </tr>';
                        }
                        $output .= '</tbody>
                        </table>';
                    }
                    if ( !empty( $checked_module_data['setup'] ) || !empty( $checked_module_data['tracked_extra_data'] ) || !empty( $checked_module_data['pp comments'] ) || isset( $checked_module_data['opt-setup'] ) ) {
                        // TABLE START
                        $output .= '<table class="fupi_classic_table">
                            <tbody>';
                        // SETUP INFO
                        if ( !empty( $checked_module_data['setup'] ) ) {
                            foreach ( $checked_module_data['setup'] as $setup_a ) {
                                $descr = '';
                                $icon = '';
                                if ( !empty( $setup_a[0] ) ) {
                                    switch ( $setup_a[0] ) {
                                        case 'warning':
                                            $icon = '<span class="dashicons dashicons-flag" style="color:orange; font-size: 20px;"></span>';
                                            break;
                                        case 'alert':
                                            $icon = '<span class="dashicons dashicons-warning" style="color:red; font-size: 20px;"></span>';
                                            break;
                                        default:
                                            $icon = '<span class="dashicons dashicons-yes-alt" style="color:green; font-size: 20px;"></span>';
                                            break;
                                    }
                                }
                                $recommendation = ( empty( $setup_a[2] ) ? '' : '<p class="fupi_module_status_recommend">' . $setup_a[2] . '</p>' );
                                $output .= '<tr>
                                        <td class="fupi_module_status_ico">' . $icon . '</td>
                                        <td>' . $setup_a[1] . $recommendation . '</td>
                                    </tr>';
                            }
                        }
                        // OPTIONAL SETUP INFO
                        if ( isset( $checked_module_data['opt-setup'] ) ) {
                            foreach ( $checked_module_data['opt-setup'] as $arr ) {
                                $output .= '<tr>
                                        <td class="fupi_module_status_ico"><span class="dashicons dashicons-flag" style="color:orange; font-size: 20px;"></span></td>
                                        <td>' . $arr[0] . '<p class="fupi_module_status_recommend">' . $arr[1] . '</p></td>
                                    </tr>';
                            }
                        }
                        // PP INFO
                        if ( !empty( $checked_module_data['tracked_extra_data'] ) || !empty( $checked_module_data['pp comments'] ) ) {
                            if ( !empty( $checked_module_data['tracked_extra_data'] ) ) {
                                $output .= '<tr>
                                    <td class="fupi_module_status_ico">
                                        <span class="dashicons dashicons-welcome-write-blog" style="font-size: 20px; color: #6d2974"></span>
                                    </td>
                                    <td>
                                        Add to your privacy policy information about additional, personaly identifiable information tracked and sent to the tool:
                                        <ul style="padding-left: 30px; list-style-type: circle;">';
                                foreach ( $checked_module_data['tracked_extra_data'] as $pp_arr ) {
                                    $output .= '<li>' . $pp_arr[0] . '</li>';
                                }
                                $output .= '</ul>
                                        </td>   
                                    </tr>';
                            }
                            if ( !empty( $checked_module_data['pp comments'] ) ) {
                                foreach ( $checked_module_data['pp comments'] as $comment ) {
                                    $output .= '<tr>
                                            <td class="fupi_module_status_ico">
                                                <span class="dashicons dashicons-welcome-write-blog" style="font-size: 20px; color: #6d2974"></span>
                                            </td>
                                            <td>';
                                    if ( gettype( $comment ) == 'array' ) {
                                        $output .= '<p>' . $comment[0] . '</p>';
                                        if ( !empty( $comment[1] ) && gettype( $comment[1] ) == 'array' ) {
                                            $output .= '<ul style="padding-left: 30px; list-style-type: circle;">';
                                            foreach ( $comment[1] as $li ) {
                                                $output .= '<li>' . $li . '</li>';
                                            }
                                        }
                                        $output .= '</ul>';
                                    } else {
                                        $output .= '<p>' . $comment . '</p>';
                                    }
                                    // /**/
                                    // if ( gettype( $comment ) == 'array' ) {
                                    //     $output .= '<ul style="padding-left: 30px; list-style-type: circle;">';
                                    //     foreach ( $comment as $li ) {
                                    //         $output .= '<li>' . $li . '</li>';
                                    //     }
                                    //     $output .= '</ul>';
                                    // } else {
                                    //     $output .= '<p>' . $comment . '</p>';
                                    // }
                                    $output .= '</ul>
                                        </td>   
                                    </tr>';
                                }
                            }
                        }
                        // TABLE END
                        $output .= '</tbody>
                        </table>';
                    }
                    // BOTTOM COMMENTS
                    if ( isset( $checked_module_data['bottom comments'] ) ) {
                        foreach ( $checked_module_data['bottom comments'] as $str ) {
                            $output .= '<p>' . $str . '</p>';
                        }
                    }
                    $output .= '</section>';
                }
                echo $output;
            }
        }
    }

    private function track_metadata_IDs( $id, $settings, $priv = false ) {
        if ( !in_array( 'trackmeta', $this->tools ) ) {
            return;
        }
        $var_name = ( $id == 'clar' ? 'tag_cf' : 'track_cf' );
        $tracks_meta = false;
        if ( isset( $settings[$var_name] ) && is_array( $settings[$var_name] ) ) {
            foreach ( $settings[$var_name] as $tracked_meta ) {
                if ( substr( $tracked_meta['id'], 0, 5 ) == 'user|' ) {
                    $this->data[$id]['tracked_extra_data'][] = ['User metadata with ID: ' . substr( $tracked_meta['id'], 5 )];
                    $tracks_meta = true;
                }
            }
        }
        if ( $priv && $tracks_meta ) {
            $this->data[$id]['pp comments'][] = $priv;
        }
    }

    private function check_url_passthrough( $id ) {
        if ( in_array( 'cook', $this->tools ) ) {
            if ( !empty( $this->consb_settings ) && isset( $this->consb_settings['url_passthrough'] ) ) {
                $this->data[$id]['setup'][] = ['warning', 'Link decoration is enabled in the consent banner settings.', 'This setting is a privacy grey area. Make sure you are not breaking any laws while using it. Otherwise, disable link decoration in the settings of the consent banner.'];
            }
        }
    }

    private function req_data_is_provided( $module_settings ) {
        $has_req = true;
        if ( !empty( $this->modules_info['requires'] ) ) {
            foreach ( $this->modules_info['requires'] as $req_field_id ) {
                if ( empty( $module_settings[$req_field_id] ) ) {
                    $has_req = false;
                    break;
                }
            }
        }
        return $has_req;
    }

    private function set_basic_module_info( $module_id, $module_info ) {
        $this->data[$module_id] = [
            'module_name'        => $module_info['title'],
            'setup'              => [],
            'tracked_extra_data' => [],
        ];
    }

    private function check_other_modules() {
        foreach ( $this->tools as $module_id ) {
            // STOP for consent banner (was checked before)
            if ( $module_id == 'cook' ) {
                continue;
            }
            // STOP if shouldn't be included in status
            $module_info = $this->get_module_info( $module_id );
            if ( !isset( $module_info['check_gdpr'] ) ) {
                continue;
            }
            // STOP if a module has no settings even though has a settings page
            $module_settings = get_option( 'fupi_' . $module_id );
            if ( !empty( $module_settings['has_settings_page'] ) && empty( $module_settings ) ) {
                continue;
            }
            // STOP if required data is not provided
            if ( !$this->req_data_is_provided( $module_settings ) ) {
                continue;
            }
            // Check modules
            switch ( $module_id ) {
                case 'gtm':
                    $this->set_basic_module_info( $module_id, $module_info );
                    $this->get_gtm_status( $module_info, $module_settings );
                    break;
                case 'privex':
                    $this->set_basic_module_info( $module_id, $module_info );
                    $this->get_privex_status( $module_info, $module_settings );
                    break;
                case 'blockscr':
                    break;
                default:
                    if ( $module_info['type'] == 'integr' ) {
                        $this->set_basic_module_info( $module_id, $module_info );
                        $this->get_integr_status( $module_id, $module_info, $module_settings );
                        // remember to include trackmeta data there too!
                    }
                    break;
            }
        }
    }

    //
    // PRIVACY EXTRAS
    //
    private function get_privex_status( $info, $settings ) {
        if ( !empty( $settings['extra_tools'] ) ) {
            $tools = [];
            foreach ( $settings['extra_tools'] as $tool ) {
                $tools[] = $tool['name'];
            }
            $this->data['privex']['setup'][] = ['ok', 'These additional tracking tools are used on the website: ' . join( ', ', $tools )];
            $this->data['privex']['pp comments'][] = 'Your privacy policy must include information about these tracking tools: ' . join( ', ', $tools ) . '. Inform your visitors what data they track, what do you and these tools use it for and who the providers of these tools share this data with or sell it to.';
        } else {
            $this->data['privex']['setup'][] = ['ok', 'No information on additional tracking tools have been provided'];
        }
    }

    //
    // INTEGRATION MODULES STATUS
    //
    private function get_integr_status( $id, $info, $settings ) {
        $req_consent = false;
        if ( $id == 'cegg' ) {
            $req_consent = true;
            if ( isset( $settings['identif_users'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['User ID of a logged in user'];
            }
        }
        if ( $id == 'tik' ) {
            $req_consent = true;
        }
        if ( $id == 'linkd' ) {
            $req_consent = true;
        }
        if ( $id == 'posthog' ) {
            $req_consent = true;
            if ( isset( $settings['data_in_eu'] ) ) {
                $this->data[$id]['setup'][] = ['ok', 'Visitor\'s data is being kept on servers in the EU'];
            } else {
                $this->data[$id]['setup'][] = ['alert', 'Visitor\'s data is not being kept on servers in the EU'];
            }
        }
        if ( $id == 'gads' ) {
            $req_consent = true;
            if ( isset( $settings['enh_conv'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Real name, surname, phone number, email address and physical address of customers and logged-in users (enabled with Enhanced Conversions)'];
            }
            if ( in_array( 'woo', $this->tools ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Purchase order ID'];
            }
        }
        if ( $id == 'ga41' || $id == 'ga42' ) {
            $req_consent = true;
            $this->track_metadata_IDs( $id, $settings );
            if ( in_array( 'woo', $this->tools ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Purchase order ID'];
            }
        }
        if ( $id == 'hotj' ) {
            $req_consent = true;
            if ( isset( $settings['identif_users'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Unique ID (user email email and/or a unique user id)'];
            }
            if ( isset( $settings['tag_woo_purchases_data'] ) && in_array( 'id', $settings['tag_woo_purchases_data'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Purchase order ID'];
            }
        }
        if ( $id == 'insp' ) {
            $req_consent = true;
            if ( isset( $settings['ab_test_script'] ) ) {
                $this->data[$id]['setup'][] = [$this->consent_status, 'The script loads an additional script for A/B testing which requires consent to using visitor\'s data for website personalisation.'];
            }
            if ( isset( $settings['identif_users'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Unique ID (user email email, a unique user id or a username).'];
            }
        }
        if ( $id == 'mato' ) {
            if ( isset( $settings['no_cookies'] ) ) {
                // when privacy mode is enabled, then script loading always follows GDPR
                $this->data[$id]['setup'][0] = ['ok', 'Matomo works in privacy mode and loads irrespective of user tracking consents. Privacy mode prevents from tracking identifiable information before users agree to tracking in the consent banner (if enabled). Only necessary cookies are loaded, user IDs are not used for cross-browser tracking and order IDs are randomized.'];
            } else {
                $req_consent = true;
            }
            if ( isset( $settings['set_user_id'] ) ) {
                if ( isset( $settings['no_cookies'] ) ) {
                    if ( in_array( 'cook', $this->tools ) ) {
                        if ( $this->consent_status !== 'ok' ) {
                            $this->data[$id]['tracked_extra_data'][] = ['User ID - used for cross-browser tracking after visitors agree to cookies.'];
                        } else {
                            $this->data[$id]['tracked_extra_data'][] = ['User ID - for cross-browser tracking.'];
                        }
                    }
                } else {
                    if ( !in_array( 'cook', $this->tools ) ) {
                        $this->data[$id]['tracked_extra_data'][] = ['User ID - for cross-browser tracking.', 'Set up a consent banner'];
                    } else {
                        $this->data[$id]['tracked_extra_data'][] = ['User ID - for cross-browser tracking.', 'Make sure the banner is set up correctly'];
                    }
                }
            }
            if ( in_array( 'woo', $this->tools ) ) {
                if ( in_array( 'cook', $this->tools ) ) {
                    if ( isset( $settings['no_cookies'] ) ) {
                        $this->data[$id]['tracked_extra_data'][] = ['Real order ID is tracked when visitors agree to tracking in a consent banner. Random order ID is sent when they don\'t.'];
                    } else {
                        $this->data[$id]['tracked_extra_data'][] = ['Purchase order ID'];
                    }
                } else {
                    if ( !isset( $settings['no_cookies'] ) ) {
                        $this->data[$id]['tracked_extra_data'][] = ['Purchase order ID'];
                    }
                }
            }
            $priv = ( isset( $settings['no_cookies'] ) ? 'Privacy mode is enabled. Make sure that this tool doesn\'t track information that can identify users.' : false );
            $this->track_metadata_IDs( $id, $settings, $priv );
        }
        if ( $id == 'fbp1' ) {
            $req_consent = true;
            if ( isset( $settings['adv_match'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Advanced Match is enabled. Encrypted addresses, email addresses, phone numbers and user identifiers of your visitors and logged in users are sent to Meta (if known).'];
            }
            if ( isset( $settings['limit_data_use'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Limited Data Use option is enabled for visitors from the USA.'];
            }
            $this->track_metadata_IDs( $id, $settings );
        }
        if ( $id == 'mads' ) {
            $req_consent = true;
            if ( isset( $settings['enhanced_conv'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Enhanced Conversions is enabled and sends to MS Advertising the email addresses of clients or logged-in users (if known).'];
            }
        }
        if ( $id == 'clar' ) {
            $extra = false;
            if ( isset( $settings['no_cookie'] ) ) {
                // when privacy mode is enabled, then script loading always follows GDPR
                $this->data[$id]['setup'][0] = ['ok', 'MS Clarity works in no-cookie mode and loads irrespective of user tracking consents. Only necessary cookies are loaded.'];
                $extra = 'No-cookie mode is enabled. Make sure that this tool doesn\'t track information that can identify users.';
            } else {
                $req_consent = true;
            }
            $this->track_metadata_IDs( $id, $settings, $extra );
        }
        if ( $id == 'pin' ) {
            $req_consent = true;
            if ( isset( $settings['track_user_emails'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Enhanced Match is enabled. Email addresses of clients and logged-in users are sent to Pinterest.'];
            }
        }
        if ( $id == 'simpl' ) {
            $this->data[$id]['setup'][] = ['ok', 'This tool does not track personally identifiable information and does not need consent banner'];
        }
        if ( $id == 'pla' ) {
            $this->data[$id]['setup'][] = ['ok', 'This tool does not track personally identifiable information and does not need consent banner'];
            $this->track_metadata_IDs( $id, $settings, 'Due to the nature of Plausible Analytics and its terms and conditions, you cannot sent to it personally identifiable information. Please make sure that no metadata contains it.' );
        }
        if ( $id == 'twit' ) {
            $req_consent = true;
            if ( isset( $settings['enhanced_conv'] ) ) {
                $this->data[$id]['tracked_extra_data'][] = ['Enhanced Conversions is enabled. Email addresses of clients and logged-in users are sent to Twitter (if known).'];
            }
        }
        // add setup information after we checked all the PPs
        if ( $req_consent ) {
            if ( in_array( 'cook', $this->tools ) ) {
                if ( isset( $settings['disreg_cookies'] ) ) {
                    $this->data[$id]['setup'][] = ['alert', 'The tool is set to overwrite consent banner settings and start tracking without waiting for consent', 'Disable this option in the module\'s settings'];
                } else {
                    $extra = $this->get_extra_text();
                    $main_text = ( $this->consent_status == 'alert' ? 'This tool loads according to incorrectly configured consent banner.' : 'This tool loads according to consent banner settings.' );
                    $this->data[$id]['setup'][] = [$this->consent_status, $main_text, $extra];
                }
            } else {
                $this->data[$id]['setup'][] = ['alert', 'Consent banner is required', 'Enable consent banner'];
            }
            if ( isset( $settings['force_load'] ) ) {
                $this->data[$id]['setup'][] = ['alert', 'The tool is force loaded for all visitors.', 'Disable force loading'];
            }
        }
        if ( $this->req_consent_banner != 'yes' ) {
            $this->req_consent_banner = ( $req_consent ? 'yes' : 'no' );
        }
        if ( $id == 'ga41' || $id == 'ga42' || $id == 'gads' ) {
            $this->check_url_passthrough( $id );
        }
    }

    //
    // SAFE FONTS MODULE
    //
    private function check_safefonts_module() {
        $module_info = $this->get_module_info( 'safefonts' );
        $this->set_basic_module_info( 'safefonts', $module_info );
        $is_module_enabled = in_array( 'safefonts', $this->tools );
        if ( $is_module_enabled ) {
            $this->data['safefonts']['pre-setup'][] = ['Google Fonts are replaced with fonts from Bunny Fonts', 'You enabled replacing Google Fonts with safe fonts from Bunny Fonts but your website can still load them dynamically (after the page loads). Scan your website with <a href="https://fontsplugin.com/google-fonts-checker/" target="_blank">Fonts Checker</a> again. If it finds links to Google Fonts, you need to find the plugin or theme that loads them and disable Google Fonts in their settings.'];
        } else {
            $this->data['safefonts']['pre-setup'][] = ['Check if you need to use the Safe Fonts module', 'Scan your website with <a href="https://fontsplugin.com/google-fonts-checker/" target="_blank">Fonts Checker</a> and check if your website uses Google Fonts. If it does, then either disable them in the settings of your theme or plugin or enable the Safe Fonts module to replace them with GDPR-compliant fonts from Bunny Fonts.'];
        }
    }

    //
    // WOOCOMMERCE MODULE
    //
    private function check_woo_module() {
        if ( in_array( 'woo', $this->tools ) ) {
            $module_info = $this->get_module_info( 'woo' );
            $this->set_basic_module_info( 'woo', $module_info );
            $sourcebuster_warning_text = ['Disable or control the SourceBuster script in WooCommerce', sprintf( 'Recent versions of WooCommerce come with a feature that uses cookies to track the last source of a purchase (order attribution). We recommend you either disable this function in WooCommerce > Settings > Advanced > Features or use the Tracking Tools Manager to load it only to people who agreed to statistical cookies. %1$sLearn more%2$s', '<a href="https://wpfullpicture.com/blog/does-order-attribution-feature-in-woocommerce-8-5-1-break-gdpr-and-what-to-do-about-it/">', '</a>' )];
            if ( in_array( 'blockscr', $this->tools ) ) {
                $blockscr_opts = get_option( 'fupi_blockscr' );
                if ( !empty( $blockscr_opts ) && !empty( $blockscr_opts['auto_rules'] ) && in_array( 'woo_sbjs', $blockscr_opts['auto_rules'] ) ) {
                    if ( in_array( 'cook', $this->tools ) ) {
                        if ( $this->consent_status == 'alert' ) {
                            $this->data['woo']['setup'][] = [$this->consent_status == 'alert', 'SourceBuster script is loaded according to incorrectly set up consent banner', 'Correct consent banner settings'];
                        } else {
                            $this->data['woo']['setup'][] = ['ok', 'SourceBuster script is loaded according to the consent banner settings'];
                        }
                    } else {
                        $this->data['woo']['setup'][] = ['alert', 'Enable consent banner to load SourceBuster according to privacy laws', 'Enable consent banner'];
                    }
                } else {
                    $this->data['woo']['opt-setup'][] = $sourcebuster_warning_text;
                }
            } else {
                $this->data['woo']['opt-setup'][] = $sourcebuster_warning_text;
            }
        }
    }

    //
    // IFRAME MANAGER
    //
    private function check_iframeblock_module() {
        $module_info = $this->get_module_info( 'iframeblock' );
        $this->set_basic_module_info( 'iframeblock', $module_info );
        $is_module_enabled = in_array( 'iframeblock', $this->tools );
        $settings = get_option( 'fupi_iframeblock' );
        if ( $is_module_enabled ) {
            $add_extra_info = false;
            // Automatic iframe rules
            if ( !empty( $settings['auto_rules'] ) && is_array( $settings['auto_rules'] ) ) {
                $this->req_consent_banner = 'yes';
                $add_extra_info = true;
                $delimiter = ( count( $settings['auto_rules'] ) == 2 ? ' and ' : ', ' );
                $extra = $this->get_extra_text();
                $this->data['iframeblock']['setup'][] = [$this->consent_status, 'Iframes loaded by ' . join( $delimiter, $settings['auto_rules'] ) . ' are loaded according to consent banner settings (require consents for statistics and marketing) or when visitors agree to privacy policies of content hosts.', $extra];
            }
            // Add automatic and manual rules together
            if ( !empty( $settings['manual_rules'] ) ) {
                $add_extra_info = true;
                foreach ( $settings['manual_rules'] as $rules ) {
                    $text = 'Iframes loaded by ' . $rules['iframe_url'];
                    $req_consents = [];
                    $delimiter = ( count( $settings['manual_rules'] ) == 2 ? ' and ' : ', ' );
                    $extra = false;
                    if ( !empty( $rules['stats'] ) ) {
                        $req_consents[] = 'statistics';
                    }
                    if ( !empty( $rules['market'] ) ) {
                        $req_consents[] = 'marketing';
                    }
                    if ( !empty( $rules['pers'] ) ) {
                        $req_consents[] = 'personalisation';
                    }
                    if ( count( $req_consents ) > 0 ) {
                        $this->req_consent_banner = 'yes';
                        $entry_status = $this->consent_status;
                        $text .= ' are loaded according to consent banner settings (require consents for ' . join( $delimiter, $req_consents ) . ') or when visitors agree to privacy policies of content hosts.';
                        $extra = $this->get_extra_text();
                    } else {
                        $entry_status = 'warning';
                        $text .= ' are set to load without waiting for consents.';
                    }
                    $this->data['iframeblock']['setup'][] = [$entry_status, $text, $extra];
                }
            }
            if ( $add_extra_info ) {
                $this->data['iframeblock']['pp comments'][] = 'Add information in your privacy policy that your website loads content from other sources and what happens with their data after they agree. You can link to their privacy policies.';
            }
            // If module is disabled
        } else {
            $this->data['iframeblock']['pre-setup'][] = ['Check if you need to enable the Iframes Manager module', 'If you embed on your site any content from other websites (YouTube videos, Google Maps, X/Twitter twits, etc.), please configure the Consent Banner module and Iframes Manager module. This way WP Full Picture will be able to load this content according to provided consents.'];
        }
    }

    //
    // CUSTOM SCRIPTS
    //
    private function check_custom_scripts_module() {
        $module_info = $this->get_module_info( 'cscr' );
        $this->set_basic_module_info( 'cscr', $module_info );
        $is_module_enabled = in_array( 'cscr', $this->tools );
        $settings = get_option( 'fupi_cscr' );
        if ( $is_module_enabled ) {
            if ( $this->req_consent_banner != 'yes' ) {
                $this->req_consent_banner = 'maybe';
            }
            $script_placement = ['fupi_head_scripts', 'fupi_footer_scripts'];
            $adds_scripts = false;
            foreach ( $script_placement as $placement ) {
                if ( !empty( $settings[$placement] ) ) {
                    foreach ( $settings[$placement] as $script_settings ) {
                        if ( !empty( $script_settings['disable'] ) ) {
                            continue;
                        }
                        $adds_scripts = true;
                        $title = ( !empty( $script_settings['title'] ) ? esc_attr( $script_settings['title'] ) : 'Script ' . $script_settings['id'] );
                        // write description text
                        $script_info_text = 'WP Full Picture loads a script: "' . $title . '". ';
                        // if we have a consent banner
                        if ( in_array( 'cook', $this->tools ) ) {
                            $req_consents = [];
                            if ( !empty( $script_settings['stats'] ) ) {
                                $req_consents[] = 'statistics';
                            }
                            if ( !empty( $script_settings['market'] ) ) {
                                $req_consents[] = 'marketing';
                            }
                            if ( !empty( $script_settings['pers'] ) ) {
                                $req_consents[] = 'personalisation';
                            }
                            if ( count( $req_consents ) > 0 ) {
                                $script_status = 'ok';
                                $delimiter = ( count( $req_consents ) == 2 ? ' and ' : ', ' );
                                $script_info_text .= 'It is set to require consents for ' . join( $delimiter, $req_consents );
                                $extra = $this->get_extra_text();
                                if ( isset( $script_settings['force_load'] ) ) {
                                    $script_status = 'alert';
                                    $script_info_text .= ' but it is force-loaded before visitors can make their choices.';
                                    $extra .= ' Do not force-load this script.';
                                } else {
                                    $script_info_text .= ' and loads according to consent banner settings.';
                                }
                            } else {
                                $script_status = 'warning';
                                $script_info_text .= 'The script loads without waiting for tracking consents.';
                                $extra = 'Make sure that this script does not track personaly identifiable information.';
                            }
                            // if we don't have a consent banner
                        } else {
                            $script_status = 'warning';
                            $script_info_text .= 'The script loads without waiting for tracking consents.';
                            $extra = 'Make sure that this script does not track personaly identifiable information.';
                        }
                        $this->data['cscr']['setup'][] = [$script_status, $script_info_text, $extra];
                    }
                }
            }
            if ( !$adds_scripts ) {
                $this->data['cscr']['pre-setup'][] = ['This module does not install any scripts', 'Make sure that all JavaScript snippets that install tracking tools are loaded with the Custom Scripts module. WP Full Picture will load them according to provided consents.'];
            }
            // If module is disabled
        } else {
            $this->data['cscr']['pre-setup'][] = ['Check if you need to enable the Custom Scripts module', 'If you installed any tracking tools with JavaScript snippets, please move these snippets to the "Custom scripts" module (easy) or Google Tag Manager (advanced). This way, WP Full Picture\'s Consent Banner will be able to load these tools according to provided consents.'];
        }
    }

    //
    // TRACKING TOOLS MANAGER
    //
    private function check_blockscr_module() {
        $module_info = $this->get_module_info( 'blockscr' );
        $this->set_basic_module_info( 'blockscr', $module_info );
        $is_module_enabled = in_array( 'blockscr', $this->tools );
        $settings = get_option( 'fupi_blockscr' );
        if ( $is_module_enabled ) {
            $add_extra_info = false;
            if ( !empty( $settings['auto_rules'] ) && is_array( $settings['auto_rules'] ) ) {
                $this->req_consent_banner = 'yes';
                $add_extra_info = true;
                if ( in_array( 'cook', $this->tools ) ) {
                    $extra = $this->get_extra_text();
                    $this->data['blockscr']['setup'][] = [$this->consent_status, 'Tracking plugins loaded according to settings in the consent banner: ' . join( ', ', str_replace( '_', ' ', $settings['auto_rules'] ) ), $extra];
                } else {
                    $this->data['blockscr']['setup'][] = ['alert', 'Tracking plugin(s) ' . join( ', ', str_replace( '_', ' ', $settings['auto_rules'] ) ) . ' must be loaded according to settings in the consent banner but it is not enabled.', 'Enable consent banner'];
                }
            }
            if ( !empty( $settings['blocked_scripts'] ) && is_array( $settings['blocked_scripts'] ) ) {
                $this->req_consent_banner = 'yes';
                $add_extra_info = true;
                foreach ( $settings['blocked_scripts'] as $rules ) {
                    $title = $rules['id'];
                    if ( !empty( $rules['title'] ) ) {
                        $title = $rules['title'];
                    } else {
                        if ( !empty( $rules['name'] ) ) {
                            $title = $rules['name'];
                        } else {
                            $title = 'No name provided';
                        }
                    }
                    $text = 'Tracking tool with ' . $rules['block_by'] . '="' . $rules['url_part'] . '" and title/ID "' . $title . '"';
                    $extra = false;
                    $req_consents = [];
                    if ( !empty( $rules['stats'] ) ) {
                        $req_consents[] = 'statistics';
                    }
                    if ( !empty( $rules['market'] ) ) {
                        $req_consents[] = 'marketing';
                    }
                    if ( !empty( $rules['pers'] ) ) {
                        $req_consents[] = 'personalisation';
                    }
                    $delimiter = ( count( $req_consents ) == 2 ? ' and ' : ', ' );
                    // Don\'t forget to add "force_load" check
                    if ( count( $req_consents ) > 0 ) {
                        $this->req_consent_banner = 'yes';
                        $entry_status = $this->consent_status;
                        $text .= ' is marked as using visitor\'s data for ' . join( $delimiter, $req_consents ) . '.';
                        if ( in_array( 'cook', $this->tools ) ) {
                            $extra = $this->get_extra_text();
                            if ( $this->consent_status == 'alert' ) {
                                $text .= ' The tool is loaded according to incorrectly set up consent banner';
                            } else {
                                $text .= ' The tool is loaded according to consent banner settings';
                            }
                        }
                    } else {
                        $entry_status = 'warning';
                        $text .= ' is set to load without waiting for consents.';
                        $extra = 'Make sure this script does not need consents';
                    }
                    $this->data['blockscr']['setup'][] = [$entry_status, $text, $extra];
                }
            }
            if ( $add_extra_info ) {
                $this->data['blockscr']['pp comments'][] = 'Add information in your privacy policy about additional tracking tools that you use, what data they collect, how is the data used and who is it shared with.';
            } else {
                $this->data['blockscr']['setup'][] = ['ok', 'This module does not manage any tracking tools', 'Are you use this module needs to stay enabled?'];
            }
            // If module is disabled
        } else {
            $this->data['blockscr']['pre-setup'][] = ['Check if you need to enable the Tracking Tools Manager module', 'Tracking Tools Manager let\'s you load tracking tools installed outside WP Full Picture according to visitors\' consents. Use it if you installed any tracking tool with a different plugin. If you are unsure, check your website for cookies of other tracking tools with <a href="https://2gdpr.com">2GDPR.com</a>. If you are unsure how to use it, read these <a href="https://wpfullpicture.com/support/documentation/how-to-use-2gdpr-com-to-track-your-visitors-according-to-gdpr/">short instructions</a>.'];
        }
    }

    //
    // CONSENT BANNER STATUS
    //
    private function check_cons_banner_module() {
        if ( !in_array( 'cook', $this->tools ) ) {
            return;
        }
        $info = $this->get_module_info( 'cook' );
        $settings = $this->consb_settings;
        $notice_opts = get_option( 'fupi_cookie_notice' );
        $priv_policy_url = get_privacy_policy_url();
        $this->data['cook'] = [
            'module_name' => $info['title'],
            'setup'       => [],
        ];
        $status = 'ok';
        // state levels: ok > warning > alert
        $guide_text = 'Check in what countries opt-in, opt-out and notification banners should be used. <a href="https://wpfullpicture.com/support/documentation/countries-that-require-opt-in-or-opt-out-to-cookies/">Read article</a>';
        // texts for the defaults
        $default_geo_texts = [
            ['ok', 'Consent banner uses strict, automatic setup mode - it chooses the correct mode of work depending on visitor\'s location. Strict mode is intended for websites that use visitor\'s data for marketing purposes and / or collect sensitive information.'],
            ['ok', 'Opt-in mode is used for visitors from: AT, BE, BG, CY, CZ, DE, DK, ES, EE, FI, FR, GB, GR, HR, HU, IE, IS, IT, LI, LT, LU, LV, MT, NG, NL, NO, PL, PT, RO, SK, SI, SE, MX, GP, GF, MQ, YT, RE, MF, IC, AR, BR, TR, SG, ZA, AU, CA, CL, CN, CO, HK, IN, ID, JP, MA, RU, KR, CH, TW, TH.'],
            ['ok', 'Opt-out mode is used for visitors from: US (CA), KZ.'],
            ['ok', 'Visitors from other countries are notified that they are tracked.'],
            ['ok', 'Fallback mode when no location is found: Opt-in mode.']
        ];
        // default settings
        if ( empty( $settings ) ) {
            if ( in_array( 'geo', $this->tools ) ) {
                $this->data['cook']['setup'] = array_merge( $this->data['cook']['setup'], $default_geo_texts );
            } else {
                $this->data['cook']['setup'][] = ['ok', 'Consent banner is set to start tracking visitors from all countries only after they consent to tracking (Opt-in mode).'];
            }
            // No asking for consents again
            if ( $status != 'alert' ) {
                $status = 'alert';
            }
            $this->data['cook']['setup'][] = ['alert', 'Visitors are not asked for new consent when the privacy policy text changes and/or when new tracking modules are enabled.', 'Enable it in the consent banner\'s settings'];
            // No saving consents
            $this->data['cook']['setup'][] = [
                'warning',
                'Saving proofs of visitor\'s tracking consents is disabled.',
                'Enable saving proofs of consent in the Consent Banner > Saving Consents (Pro only). You may need it during audits or investigations by authorities or data protection agencies, if a user complains about being tracked without permission, in legal cases where privacy issues are involved.',
                'full-picture-analytics-cookie-notice'
            ];
            // check user's settings
        } else {
            if ( in_array( 'geo', $this->tools ) ) {
                // check if saved after enabling geo
                $use_default_geo = !isset( $settings['mode'] );
                if ( $use_default_geo ) {
                    $this->data['cook']['setup'] = array_merge( $this->data['cook']['setup'], $default_geo_texts );
                } else {
                    switch ( $settings['mode'] ) {
                        case 'optin':
                            $this->data['cook']['setup'][] = ['ok', 'Consent banner is set to start tracking visitors from all countries only after they consent to tracking (Opt-in mode).'];
                            break;
                        case 'optout':
                            if ( $status != 'alert' ) {
                                $status = 'alert';
                            }
                            $this->data['cook']['setup'][] = ['alert', 'Consent banner is set to start tracking visitors from the moment they enter the website but let them decline tracking (Opt-out mode).', 'Change to the opt-in mode or one of automatic modes.'];
                            break;
                        case 'notify':
                            if ( $status != 'alert' ) {
                                $status = 'alert';
                            }
                            $this->data['cook']['setup'][] = ['alert', 'Consent banner is set to track all visitors and only notify them that they are tracked. They cannot decline.', 'Change to the opt-in mode or one of automatic modes.'];
                            break;
                        case 'auto_strict':
                            array_pop( $default_geo_texts );
                            // remove last element of array (text about fallback)
                            $this->data['cook']['setup'] = array_merge( $this->data['cook']['setup'], $default_geo_texts );
                            break;
                        case 'auto_lax':
                            $this->data['cook']['setup'] = [
                                ['ok', 'Consent banner uses lax, automatic setup mode - it chooses the correct mode of work depending on visitor\'s location. Strict mode is intended for websites that neither use visitor\'s data for marketing purposes nor collect sensitive information.'],
                                ['ok', 'Opt-in mode is used for visitors from: AT, BE, BG, CY, CZ, DE, DK, ES, EE, FI, FR, GB, GR, HR, HU, IE, IS, IT, LI, LT, LU, LV, MT, NG, NL, NO, PL, PT, RO, SK, SI, SE, GP, GF, MQ, YT, RE, MF, IC, TR, ZA, AG, BR, CL, CN, CO, ID, MA, RU, KR, TW, TH, CH.'],
                                ['ok', 'Opt-out mode is used for visitors from: US (CA), JP, CA, IN, MX, SG.'],
                                ['ok', 'Visitors from KZ, PH are notified that they are tracked.'],
                                ['ok', 'Visitors from other countries are tracked without notification.']
                            ];
                            break;
                        case 'manual':
                            if ( $status == 'ok' ) {
                                $status = 'warning';
                            }
                            $this->data['cook']['setup'][] = ['warning', 'Consent banner changes the mode of work depending on visitor\'s location. The list of locations was set manually by the user.', 'Make sure your setup is correct'];
                            // Opt-in
                            if ( $settings['optin'] == 'all' ) {
                                $this->data['cook']['tracked_extra_data'][] = ['Opt-in mode is used for visitors from all countries.'];
                            }
                            if ( $settings['optin'] == 'none' ) {
                                if ( $status != 'alert' ) {
                                    $status = 'alert';
                                }
                                $this->data['cook']['setup'][] = ['alert', 'Opt-in mode is not used for visitors from any country.', $guide_text];
                            }
                            if ( $settings['optin'] == 'specific' ) {
                                if ( isset( $settings['optin_countries'] ) ) {
                                    $this->data['cook']['setup'][] = ['warning', 'Opt-in mode is used for visitors from: ' . $settings['optin_countries'] . '.', $guide_text];
                                } else {
                                    if ( $status != 'alert' ) {
                                        $status = 'alert';
                                    }
                                    $this->data['cook']['setup'][] = ['alert', 'Opt-in mode is not used for visitors from any country.', $guide_text];
                                }
                            }
                            // Opt-out
                            if ( $settings['optout'] == 'all' ) {
                                if ( $status != 'alert' ) {
                                    $status = 'alert';
                                }
                                $this->data['cook']['setup'][] = ['alert', 'Opt-out mode is used for visitors from all countries.', $guide_text];
                            }
                            if ( $settings['optout'] == 'none' ) {
                                $this->data['cook']['setup'][] = ['ok', 'Opt-out mode is not used for visitors from any country.'];
                            }
                            if ( $settings['optout'] == 'specific' ) {
                                if ( isset( $settings['optout_countries'] ) ) {
                                    if ( $status == 'ok' ) {
                                        $status = 'warning';
                                    }
                                    $this->data['cook']['setup'][] = ['warning', 'Opt-out mode is used for visitors from: ' . $settings['optout_countries'] . '.', $guide_text];
                                } else {
                                    $this->data['cook']['setup'][] = ['ok', 'Opt-out mode is not used for visitors from any country.'];
                                }
                            }
                            // Notify
                            if ( $settings['inform'] == 'all' ) {
                                if ( $status != 'alert' ) {
                                    $status = 'alert';
                                }
                                $this->data['cook']['setup'][] = ['alert', 'Visitors from all countries are notified about tracking but can\'t opt-out', 'Change to opt-in mode or one of the automatic ones'];
                            }
                            if ( $settings['inform'] == 'none' ) {
                                $this->data['cook']['tracked_extra_data'][] = ['Visitors from no country are only informed that they are tracked.'];
                            }
                            if ( $settings['inform'] == 'specific' ) {
                                if ( isset( $settings['inform_countries'] ) ) {
                                    if ( $status == 'ok' ) {
                                        $status = 'warning';
                                    }
                                    $this->data['cook']['setup'][] = ['warning', 'Visitors from these countries are notified about tracking but can\'t opt-out: ' . $settings['optin_countries'] . '.', $guide_text];
                                } else {
                                    $this->data['cook']['setup'][] = ['ok', 'Visitors from no country are only informed that they are tracked.'];
                                }
                            }
                            // Other
                            $this->data['cook']['setup'][] = ['warning', 'Visitors from other countries are tracked without notification.', $guide_text];
                            break;
                    }
                    // Geo fallback
                    if ( $settings['mode'] == 'auto_strict' || $settings['mode'] == 'auto_lax' || $settings['mode'] == 'manual' ) {
                        if ( !isset( $settings['enable_scripts_after'] ) ) {
                            $settings['enable_scripts_after'] = 'optin';
                        }
                        switch ( $settings['enable_scripts_after'] ) {
                            case 'optin':
                                $this->data['cook']['setup'][] = ['ok', 'When no location is found, consent banner will start tracking visitors from all countries only after they consent to tracking (Opt-in mode).'];
                                break;
                            case 'optout':
                                if ( $status != 'alert' ) {
                                    $status = 'alert';
                                }
                                $this->data['cook']['setup'][] = ['alert', 'When no location is found, consent banner will start tracking visitors from the moment they enter the website but will let them decline tracking (Opt-out mode)', 'Change to opt-in mode.'];
                                break;
                            case 'notify':
                                if ( $status != 'alert' ) {
                                    $status = 'alert';
                                }
                                $this->data['cook']['setup'][] = ['alert', 'When no location is found, visitors will be notified that they are tracked bu they will not be able to decline tracking.', 'Change to opt-in mode.'];
                                break;
                        }
                    }
                }
                // when geo is disabled, the mode is set in the setting "enable_scripts_after"
            } else {
                if ( !isset( $settings['enable_scripts_after'] ) ) {
                    $settings['enable_scripts_after'] = 'optin';
                }
                switch ( $settings['enable_scripts_after'] ) {
                    case 'optin':
                        $this->data['cook']['setup'][] = ['ok', 'Consent banner is set to start tracking visitors from all countries only after they consent to tracking (Opt-in mode).'];
                        break;
                    case 'optout':
                        if ( $status != 'alert' ) {
                            $status = 'alert';
                        }
                        $this->data['cook']['setup'][] = ['alert', 'Consent banner is set to start tracking visitors from the moment they enter the website but will let them decline tracking (Opt-out mode)', 'Change to the opt-in mode or one of automatic modes.'];
                        break;
                    case 'notify':
                        if ( $status != 'alert' ) {
                            $status = 'alert';
                        }
                        $this->data['cook']['setup'][] = ['alert', 'Visitors will be notified that they are tracked but they will not be able to decline tracking.', 'Change to the opt-in mode or one of automatic modes.'];
                        break;
                }
            }
            // reset when modules or PP change
            if ( isset( $settings['ask_for_consent_again'] ) ) {
                $this->data['cook']['setup'][] = ['ok', 'Visitors are asked for consent again, when the privacy policy text changes or when tracking modules are enabled.'];
            } else {
                if ( $status != 'alert' ) {
                    $status = 'alert';
                }
                $this->data['cook']['setup'][] = ['alert', 'Visitors are not asked for new consent when the privacy policy text changes and/or when new tracking modules are enabled.', 'Enable it in the consent banner\'s settings'];
            }
            // URL passthrough (checked later in Google Analytics and Google Ads)
            if ( isset( $settings['url_passthrough'] ) ) {
                $this->url_pass_enabled = true;
            }
        }
        // Privacy policy page
        if ( empty( $priv_policy_url ) ) {
            if ( $status != 'alert' ) {
                $status = 'alert';
            }
            $this->data['cook']['setup'][] = ['alert', 'Privacy policy page is not set or is not published', 'Please set it in "Settings > Privacy" page and make sure that it is published.'];
        }
        // Are consent banner switches pre-selected?
        $styling_options = get_option( 'fupi_cookie_notice' );
        // if there are some pre-selected optin switches and these switches are also used in the optin mode
        if ( isset( $styling_options['switches_on'] ) && is_array( $styling_options['switches_on'] ) && !empty( $styling_options['optin_switches'] ) ) {
            // and we are not hiding the whole section with settings
            if ( isset( $styling_options['hide'] ) && is_array( $styling_options['hide'] ) && !in_array( 'settings_btn', $styling_options['hide'] ) ) {
                if ( $status != 'alert' ) {
                    $status = 'alert';
                }
                $this->data['cook']['setup'][] = ['alert', 'When visitors are asked for tracking consent (opt-in), switches for choosing allowed uses of tracked data are pre-selected.', 'Disable pre-selection of switches'];
            }
        }
        $pp_cookies_info = ['Add to your privacy policy information that WP Full Picture uses the following cookies:', ['fp_cookie - a necessary cookie. It stores information on visitor\'s tracking consents, a list of tracking tools that a user agreed to and the date of the last update of the privacy policy page. Does not expire.', 'fp_current_session - an optional cookie. It requires consent to tracking statistics. In the free version it does not hold any value and is only used to check if a new session has started. In the Pro version it holds the number and type of pages that a visitor viewed in a session, domain of the traffic source, URL parameters of the first landing page in a session and visitor\'s lead score. Expires when a visitor is inactive for 30 minutes.']];
        if ( !empty( $settings ) ) {
            // Saving consents
            if ( isset( $settings['cdb_key'] ) && !empty( $priv_policy_url ) ) {
                $this->data['cook']['setup'][] = ['ok', 'Saving proofs of visitor\'s tracking consents is enabled.'];
            } else {
                if ( $status != 'alert' ) {
                    $status = 'warning';
                }
                $this->data['cook']['setup'][] = [
                    'warning',
                    'Saving proofs of visitor\'s tracking consents is disabled.',
                    'Enable saving proofs of consent in the Consent Banner > Saving Consents (Pro only). You may need it during audits or investigations by authorities or data protection agencies, if a user complains about being tracked without permission, in legal cases where privacy issues are involved.',
                    'full-picture-analytics-cookie-notice'
                ];
            }
            if ( isset( $settings['cdb_key'] ) ) {
                $pp_cookies_info[1][] = 'cdb_id - a necessary cookie. It is saved after visitors set their tracking consents (or decline them) in the consent banner. It stores a random device identifier used to match consents saved in the remote database with the device. Does not expire.';
            }
        }
        $this->data['cook']['pp comments'][] = $pp_cookies_info;
        // Button which toggles consent banner
        $toggle_btn_enabled = !empty( $notice_opts['enable_toggle_btn'] );
        if ( $toggle_btn_enabled ) {
            $this->data['cook']['setup'][] = ['ok', 'Visitors who want to change their tracking preferences can do it in the consent banner which shows after they click an icon in the corner of the screen'];
        } else {
            $priv_policy_id = get_option( 'wp_page_for_privacy_policy' );
            $priv_policy_post = get_post( $priv_policy_id );
            $toggler_found = false;
            if ( !empty( $priv_policy_post ) ) {
                $priv_policy_content = $priv_policy_post->post_content;
                $priv_policy_content = apply_filters( 'the_content', $priv_policy_content );
                $priv_policy_content = do_shortcode( $priv_policy_content );
                $toggle_selectors = ['fp_show_cookie_notice'];
                if ( !empty( $settings['toggle_selector'] ) && strlen( $settings['toggle_selector'] ) > 3 ) {
                    $toggle_selectors[] = ltrim( esc_attr( $settings['toggle_selector'] ), $settings['toggle_selector'][0] );
                }
                foreach ( $toggle_selectors as $sel ) {
                    if ( str_contains( $priv_policy_content, $sel ) ) {
                        $toggler_found = true;
                    }
                }
                if ( $toggler_found ) {
                    $this->data['cook']['setup'][] = ['ok', 'Visitors who want to change their tracking preferences can do it in the consent banner which shows after they click a link/button in the privacy policy.'];
                } else {
                    $toggle_selectors_str = '.fp_show_cookie_notice';
                    if ( !empty( $settings['toggle_selector'] ) && strlen( $settings['toggle_selector'] ) > 3 ) {
                        $toggle_selectors_str = $toggle_selectors_str . ', ' . esc_attr( $settings['toggle_selector'] );
                    }
                    $this->data['cook']['setup'][] = ['warning', 'Make sure your visitors can open the consent banner popup to change their tracking preferences.', 'Please enable a toggle icon in the theme customizer (Appearance > Customize > Consent Banner) or add a button in your privacy policy with the CSS selector(s): ' . $toggle_selectors_str . '.'];
                }
            } else {
                $this->data['cook']['setup'][] = ['alert', 'Privacy policy page is missing or is not marked as such.', 'Create a privacy policy page and mark it as such in in the WordPress admin > Settings menu > Privacy page.'];
            }
        }
        // Position of the consent banner
        $notice_position = ( !empty( $notice_opts['position'] ) ? esc_attr( $notice_opts['position'] ) : 'popup' );
        if ( $notice_position != 'popup' ) {
            $this->data['cook']['opt-setup'][] = ['The notice is not set to display in the central position', 'To collect maximum number of consents we recommend that you place your notice in the central position of the screen. This way people will not be able to navigate the site without making a choice, thus giving you more consents. You will also not lose information on the source of traffic, which can be accessed only on the first page the visitor sees.'];
        }
        // TEXTS & STYLING
        $hidden_elements = ( isset( $notice_opts['hide'] ) && is_array( $notice_opts['hide'] ) ? $notice_opts['hide'] : [] );
        $hidden_descr = [];
        $default_texts = [
            'notif_h'           => '',
            'notif_descr'       => esc_html__( 'We use cookies to provide you with the best browsing experience, personalize content of our site, analyse its traffic and show you relevant ads. See our {{privacy policy}} for more information.', 'full-picture-analytics-cookie-notice' ),
            'agree'             => esc_html__( 'Agree', 'full-picture-analytics-cookie-notice' ),
            'ok'                => esc_html__( 'I understand', 'full-picture-analytics-cookie-notice' ),
            'decline'           => esc_html__( 'Decline', 'full-picture-analytics-cookie-notice' ),
            'cookie_settings'   => esc_html__( 'Settings', 'full-picture-analytics-cookie-notice' ),
            'agree_to_selected' => esc_html__( 'Agree to selected', 'full-picture-analytics-cookie-notice' ),
            'return'            => esc_html__( 'Return', 'full-picture-analytics-cookie-notice' ),
            'necess_h'          => '',
            'necess_descr'      => '',
            'stats_h'           => esc_html__( 'Statistics', 'full-picture-analytics-cookie-notice' ),
            'stats_descr'       => esc_html__( 'I want to help you make this site better so I will provide you with data about my use of this site.', 'full-picture-analytics-cookie-notice' ),
            'pers_h'            => esc_html__( 'Personalisation', 'full-picture-analytics-cookie-notice' ),
            'pers_descr'        => esc_html__( 'I want to have the best experience on this site so I agree to saving my choices, recommending things I may like and modifying the site to my liking', 'full-picture-analytics-cookie-notice' ),
            'market_h'          => esc_html__( 'Marketing', 'full-picture-analytics-cookie-notice' ),
            'market_descr'      => esc_html__( 'I want to see ads with your offers, coupons and exclusive deals rather than random ads from other advertisers.', 'full-picture-analytics-cookie-notice' ),
        ];
        $current_texts = [
            'notification_headline'           => ( !empty( $notice_opts['notif_headline_text'] ) ? esc_html( $notice_opts['notif_headline_text'] ) : $default_texts['notif_h'] ),
            'agree_to_all_cookies_button'     => ( !empty( $notice_opts['agree_text'] ) ? esc_html( $notice_opts['agree_text'] ) : $default_texts['agree'] ),
            'i_understand_button'             => ( !empty( $notice_opts['ok_text'] ) ? esc_html( $notice_opts['ok_text'] ) : $default_texts['ok'] ),
            'decline_button'                  => ( !empty( $notice_opts['decline_text'] ) ? esc_html( $notice_opts['decline_text'] ) : $default_texts['decline'] ),
            'cookie_settings_button'          => ( !empty( $notice_opts['cookie_settings_text'] ) ? esc_html( $notice_opts['cookie_settings_text'] ) : $default_texts['cookie_settings'] ),
            'agree_to_selected_button'        => ( !empty( $notice_opts['agree_to_selected_text'] ) ? esc_html( $notice_opts['agree_to_selected_text'] ) : $default_texts['agree_to_selected'] ),
            'return_button'                   => ( !empty( $notice_opts['return_text'] ) ? esc_html( $notice_opts['return_text'] ) : $default_texts['return'] ),
            'necessary_cookies_headline'      => ( !empty( $notice_opts['necess_headline_text'] ) ? esc_html( $notice_opts['necess_headline_text'] ) : '' ),
            'statistics_hookies_headline'     => ( !empty( $notice_opts['stats_headline_text'] ) ? esc_html( $notice_opts['stats_headline_text'] ) : $default_texts['stats_h'] ),
            'peronalisation_cookies_headline' => ( !empty( $notice_opts['pers_headline_text'] ) ? esc_html( $notice_opts['pers_headline_text'] ) : $default_texts['pers_h'] ),
            'marketing_cookies_headline'      => ( !empty( $notice_opts['marketing_headline_text'] ) ? esc_html( $notice_opts['marketing_headline_text'] ) : $default_texts['market_h'] ),
            'notification_main_descr'         => ( !empty( $notice_opts['notif_text'] ) ? $this->fupi_modify_cons_banner_text( esc_html( $notice_opts['notif_text'] ) ) : $this->fupi_modify_cons_banner_text( $default_texts['notif_descr'] ) ),
            'necessary_cookies_descr'         => ( !empty( $notice_opts['necess_text'] ) ? $this->fupi_modify_cons_banner_text( esc_html( $notice_opts['necess_text'] ) ) : '' ),
            'statistics_cookies_descr'        => ( !empty( $notice_opts['stats_text'] ) ? $this->fupi_modify_cons_banner_text( esc_html( $notice_opts['stats_text'] ) ) : $default_texts['stats_descr'] ),
            'personalisation_cookies_descr'   => ( !empty( $notice_opts['pers_text'] ) ? $this->fupi_modify_cons_banner_text( esc_html( $notice_opts['pers_text'] ) ) : $default_texts['pers_descr'] ),
            'marketing_cookies_descr'         => ( !empty( $notice_opts['marketing_text'] ) ? $this->fupi_modify_cons_banner_text( esc_html( $notice_opts['marketing_text'] ) ) : $default_texts['market_descr'] ),
        ];
        $this->data['cook']['notice_texts'] = $current_texts;
        if ( count( $hidden_descr ) > 0 ) {
            if ( in_array( 'settings_btn', $hidden_elements ) ) {
                $hidden_descr[] = 'button opening panel with cookie settings';
            }
            if ( in_array( 'stats', $hidden_elements ) ) {
                $hidden_descr[] = 'section where users can consent to the use of their data for statistics';
            }
            if ( in_array( 'market', $hidden_elements ) ) {
                $hidden_descr[] = 'section where users can consent to the use of their data for marketing';
            }
            if ( in_array( 'pers', $hidden_elements ) ) {
                $hidden_descr[] = 'section where users can consent to the use of their data for personalisation';
            }
            if ( in_array( 'decline_btn', $hidden_elements ) ) {
                $status = 'alert';
                $hidden_descr[] = 'decline cookies button';
                $this->data['cook']['setup'][] = ['alert', 'The consent banner does not display the "Decline" button', 'Do not hide the decline button'];
            }
            $this->data['cook']['hidden_elements'] = 'Hidden consent baner elements: ' . join( ', ', $hidden_descr ) . '.';
        }
        $this->consent_status = $status;
    }

    //
    // GTM STATUS
    //
    private function get_gtm_status( $info, $settings ) {
        if ( $this->req_consent_banner != 'yes' ) {
            $this->req_consent_banner = 'maybe';
        }
        // PP
        $tracked_priv_info = [];
        if ( isset( $settings['user_id'] ) ) {
            $tracked_priv_info[] = 'User ID';
        }
        if ( isset( $settings['user_realname'] ) ) {
            $tracked_priv_info[] = 'Name and surname of a user or a client';
        }
        if ( isset( $settings['user_email'] ) ) {
            $tracked_priv_info[] = 'User\'s email address and/or an email address of a client (even when not logged in, collected at the time of purchase)';
        }
        if ( isset( $settings['user_phone'] ) ) {
            $tracked_priv_info[] = 'User\'s phone number and/or phone number of a client (even when not logged in, collected at the time of purchase)';
        }
        if ( isset( $settings['user_address'] ) ) {
            $tracked_priv_info[] = 'User\'s physical address and/or address of a client (even when not logged in, collected at the time of purchase)';
        }
        if ( in_array( 'woo', $this->tools ) ) {
            $tracked_priv_info[] = 'Order ID of a purchase (in WooCommerce)';
        }
        if ( isset( $settings['track_cf'] ) && is_array( $settings['track_cf'] ) ) {
            foreach ( $settings['track_cf'] as $tracked_meta ) {
                if ( substr( $tracked_meta['id'], 0, 5 ) == 'user|' ) {
                    $tracked_priv_info[] = 'User metadata with ID ' . substr( $tracked_meta['id'], 5 );
                }
            }
        }
        if ( count( $tracked_priv_info ) > 0 ) {
            foreach ( $tracked_priv_info as $str ) {
                $this->data['gtm']['tracked_extra_data'][] = [$str];
            }
        }
        $this->data['gtm']['pp comments'][] = 'Your privacy policy must include information about tracking tools that are loaded with GTM, what data is tracked, what you and these tools use it for and who the providers of these tools share this data with or sell it to.';
        // Setup (always second)
        if ( count( $tracked_priv_info ) > 0 ) {
            if ( !in_array( 'cook', $this->tools ) ) {
                $this->data['gtm']['setup'][] = ['alert', 'Tracking tools loaded with GTM need to be used with a consent banner', 'Enable and set up the consent banner module and trigger GTM tags after visitors consent to tracking.'];
            } else {
                $gtm_setup_status = ( $this->consent_status == 'alert' ? 'alert' : 'warning' );
                $extra = $this->get_extra_text() . ' Make sure to trigger GTM tags after visitors consent to tracking.';
                $this->data['gtm']['setup'][] = [$gtm_setup_status, 'Tracking tools loaded with GTM need to be loaded after visitors consent to tracking in the consent banner.', $extra];
            }
        } else {
            $this->data['gtm']['setup'][] = ['warning', 'Tracking tools loaded with GTM may require the use of a consent banner', 'Make sure that none of the tools you install with GTM track personaly indentifiable information.'];
        }
    }

}
