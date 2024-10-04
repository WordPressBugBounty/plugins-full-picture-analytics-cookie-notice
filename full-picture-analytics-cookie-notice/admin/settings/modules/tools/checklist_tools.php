<?php

    if ( is_multisite() ){
        $plugins_page_url = network_home_url() . 'wp-admin/network/plugins.php';
    } else {
        $plugins_page_url = get_admin_url() . 'plugins.php';
    };

    $tasks = [];

    // Do not use IDs below!
    // The code will be copied to a popup and IDs will double

    $popups_html = '';
?>
