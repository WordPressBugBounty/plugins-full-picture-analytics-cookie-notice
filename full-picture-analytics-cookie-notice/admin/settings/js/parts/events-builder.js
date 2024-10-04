(()=>{

    // clear sections that contain not existing (expired) trigger

    let builder_sections = FP.findAll('.fupi_events_builder .fupi_r3_section');

    builder_sections.forEach( section => {
        
        let atrig_select = FP.findFirst( '.fupi_field_type_atrig_select select', section );

        if ( ! atrig_select.value ){
            let minus_button = FP.findFirst( '.fupi_btn_remove', section );
            if ( minus_button ) minus_button.click();
        }
    } );

})();

(()=>{

    // clear sections that contain not existing (expired) custom meta

    let builder_sections = FP.findAll('.fupi_metadata_tracker .fupi_r3_section');

    builder_sections.forEach( section => {
        
        let metadata_select = FP.findFirst( '.fupi_field_type_custom_meta_select select', section );

        if ( ! metadata_select.value ){
            let minus_button = FP.findFirst( '.fupi_btn_remove', section );
            if ( minus_button ) minus_button.click();
        }
    } );

})();