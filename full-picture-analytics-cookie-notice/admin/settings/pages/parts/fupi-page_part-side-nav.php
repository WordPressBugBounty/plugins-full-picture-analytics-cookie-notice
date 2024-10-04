<?php

    $fupi_tools = get_option('fupi_tools');
    $modules_nr = 0;
    $sections_html = [
        'settings' => '',
        'integr' => '',
        'tagman' => '',
        'priv' => '',
        'ext' => ''
    ];

    include_once FUPI_PATH . '/includes/fupi_modules_data.php';

    foreach( $fupi_modules as $module ){

        if ( $module['is_avail'] && $module['has_settings_page'] ) {

            if ( isset( $fupi_tools[$module['id']] ) || $module['id'] == 'tools' || $module['id'] == 'main' || $module['id'] == 'status' ) {
                
                $modules_nr++;
                
                // do not add a link to Woo if the plugin is deactivated
                if ( $module['id'] == 'woo' && empty( $this->is_woo_enabled ) ) continue;

                // if this page is currently displayed
                if ( $module_id == $module['id'] ) {    

                    $el = '<div id="fupi_current_page_sidenav"><span class="fupi_active_title">' . $module['title'] . '</span></div>';
                
                // if this page is not currently displayed
                } else {

                    $el = '<a href="'. get_admin_url() .'admin.php?page=full_picture_' . $module['id'] . '" data-type="'.$module['type'].'">' . $module['title'] . '</a>';
                }

                // if this is the first element in the section
                if ( empty( $sections_html[$module['type']] ) ) {

                    switch ( $module['type'] ) {
                        case 'settings':
                            $sections_html[$module['type']] = '<div id="fupi_sidenav_settings_section" class="fupi_sidenav_section"><span class="fupi_sidenav_title"><span class="dashicons dashicons-admin-settings"></span> ' . esc_html__( 'Settings', 'full-picture-analytics-cookie-notice' ) . '</span><div class="fupi_sidenav_links">';
                            break;
                        case 'integr':
                            $sections_html[$module['type']] = '<div id="fupi_sidenav_integr_section" class="fupi_sidenav_section"><span class="fupi_sidenav_title"><span class="dashicons dashicons-chart-bar"></span> ' . esc_html__( 'Integrations', 'full-picture-analytics-cookie-notice' ) . '</span><div class="fupi_sidenav_links">';
                            break;
                        case 'tagman':
                            $sections_html[$module['type']] = '<div id="fupi_sidenav_tagman_section" class="fupi_sidenav_section"><span class="fupi_sidenav_title"><span class="dashicons dashicons-editor-code"></span> ' . esc_html__( 'Manual integrations', 'full-picture-analytics-cookie-notice' ) . '</span><div class="fupi_sidenav_links">';
                            break;
                        case 'priv':
                            $sections_html[$module['type']] = '<div id="fupi_sidenav_priv_section" class="fupi_sidenav_section"><span class="fupi_sidenav_title"><span class="dashicons dashicons-visibility"></span> ' . esc_html__( 'Privacy solutions', 'full-picture-analytics-cookie-notice' ) . '</span><div class="fupi_sidenav_links">';
                            break;
                        case 'ext':
                            $sections_html[$module['type']] = '<div id="fupi_sidenav_ext_section" class="fupi_sidenav_section"><span class="fupi_sidenav_title"><span class="dashicons dashicons-admin-plugins"></span> ' . esc_html__( 'Extensions', 'full-picture-analytics-cookie-notice' ) . '</span><div class="fupi_sidenav_links">';
                            break;
                    }
                }

                $sections_html[$module['type']] .= $el;
                
            }
        }
    };

    foreach( $sections_html as $name => $val ){
        if ( ! empty( $sections_html[$name] ) ) {
            $sections_html[$name] .= '</div></div>';
        }
    };

    $sections_html[$name] .= '<div id="fupi_sidenav_ext_section" class="fupi_sidenav_section fupi_sidenav_account_section" style="display: none;">
        <span class="fupi_sidenav_title">
            <span class="dashicons dashicons-admin-users"></span> ' . esc_html__( 'Account', 'full-picture-analytics-cookie-notice' ) . '
        </span>
        <div class="fupi_sidenav_links"><a href="" data-type="account">' . esc_html__( 'Account', 'full-picture-analytics-cookie-notice' ) . '</a></div>
    </div>';

    $under_nav_notice = $modules_nr <= 3 ? '<div id="under_nav_notice">' . esc_html__('Please enable some modules. Links to enabled modules will show up here.', 'full-picture-analytics-cookie-notice') . '</div>' : '';

    if ( fupi_fs()->is_not_paying() ) {
        $under_nav_notice = '<div id="sidenav_buy_pro_banner">
            <h3>' . esc_html__('With PRO you can...', 'full-picture-analytics-cookie-notice') . '</h3>
            <div id="fupi_feature_slider" class="fupi_slider">
                <div class="fupi_slides">
                    <div class="fupi_slide">
                        <div class="fupi_slide_main_text">' . esc_html__('See what traffic sources bring you the best traffic', 'full-picture-analytics-cookie-notice') . '</div>
                        <a href="https://wpfullpicture.com/free-vs-pro/#traffic_sources_section" target="_blank" class="small">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice') . '</a>
                    </div>
                    <div class="fupi_slide">
                        <div class="fupi_slide_main_text">' . esc_html__('Learn how many visitors are interested in your offer', 'full-picture-analytics-cookie-notice') . '</div>
                        <a href="https://wpfullpicture.com/free-vs-pro/#visitor_types_section" target="_blank" class="small">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice') . '</a>
                    </div>
                    <div class="fupi_slide">
                        <div class="fupi_slide_main_text">' . esc_html__('Save tracking consents in the cloud*', 'full-picture-analytics-cookie-notice') . '</div>
                        <a href="https://wpfullpicture.com/free-vs-pro/#cdb_section" target="_blank" class="small">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice') . '</a>
                        <p class="small">' . esc_html__('*free until 31st August, 2025.', 'full-picture-analytics-cookie-notice') . '</p>
                    </div>
                    <div class="fupi_slide">
                        <div class="fupi_slide_main_text">' . esc_html__('Increase the number of tracked conversions with Meta Pixel\'s CAPI', 'full-picture-analytics-cookie-notice') . '</div>
                        <a href="https://wpfullpicture.com/free-vs-pro/#server_side_section" target="_blank" class="small">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice') . '</a>
                    </div>
                    <div class="fupi_slide">
                        <div class="fupi_slide_main_text">' . esc_html__('Recognize traffic from popular Android applications', 'full-picture-analytics-cookie-notice') . '</div>
                        <a href="https://wpfullpicture.com/free-vs-pro/#real_traffic_sources_section" target="_blank" class="small">' . esc_html__('Learn more', 'full-picture-analytics-cookie-notice') . '</a>
                    </div>
                </div>
                <ul class="fupi_slider_nav"></ul>
            </div>
            <a href="https://checkout.freemius.com/mode/dialog/plugin/5405/plan/9426/licenses/1/currency/eur/" class="button-primary"><span class="dashicons dashicons-unlock"></span> ' . esc_html__('Get Pro in 2 minutes', 'full-picture-analytics-cookie-notice') . '</a>
        </div>';
    }

    echo '<div id="fupi_page_nav" ><div id="fupi_side_menu" role="link">' . join('', $sections_html) . '</div>' . $under_nav_notice . '</div>';

?>