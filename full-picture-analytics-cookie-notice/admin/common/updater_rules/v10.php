<?php

$this->get_fupi_options( ['fupi_cook', 'fupi_fbp1'] );
$this->validate_fields( $this->o['fupi_fbp1'], [ 'capi_token' ] );

if ( array_key_exists( 'scrblk_manual_rules', $this->o['fupi_cook'] ) ) {
    $this->validate_rows( $this->o['fupi_cook']['scrblk_manual_rules'] );
    foreach ( $this->o['fupi_cook']['scrblk_manual_rules'] as $tool_rule ) {
        if ( array_key_exists( 'rules', $tool_rule ) ) {
            $this->validate_rows( $tool_rule['rules'], [ 'block_by', 'unique' ] );
        } else {
            $this->validate_rows( [ $tool_rule ], [ 'block_by', 'url_part' ] );
        }
    }
}

if ( ! empty( $this->o['fupi_cook']['scrblk_manual_rules'] ) ) {
    foreach( $this->o['fupi_cook']['scrblk_manual_rules'] as $i => $tool_rule ) {
        if ( array_key_exists( 'rules', $tool_rule ) ) continue;
        $this->o['fupi_cook']['scrblk_manual_rules'][$i]['rules'] = array(
            array(
                'block_by' => $tool_rule['block_by'],
                'unique' => $tool_rule['url_part'],
            )
        );
    }
}

if ( ! empty ( $this->o['fupi_fbp1']['capi_token'] ) ) {
    $this->o['fupi_fbp1']['track_pageview_capi'] = true;
}
