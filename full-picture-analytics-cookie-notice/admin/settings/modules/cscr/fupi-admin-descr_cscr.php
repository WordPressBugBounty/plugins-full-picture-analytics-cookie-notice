<?php

switch( $a['id'] ){

    case 'fupi_cscr_head':
         return '<p>' . esc_html__('Use these fields to add scripts to the document\'s &lt;head&gt;. These scripts cannot contain any HTML. If you want to add a script with HTML, do it in the "Footer Scripts" section.', 'full-picture-analytics-cookie-notice' ) . '</p>';
    break;

    case 'fupi_cscr_footer':
        return '<p>' . esc_html__('Use these fields to add scripts and HTML (optional) before the end of the &lt;/body&gt; tag.', 'full-picture-analytics-cookie-notice' ) . '</p>';
   break;

    default:
        return '';
    break;
};

?>
