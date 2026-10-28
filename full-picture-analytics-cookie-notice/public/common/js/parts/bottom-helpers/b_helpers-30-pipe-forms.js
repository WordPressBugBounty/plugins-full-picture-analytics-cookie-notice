    FP.actionChecks.form_triggers = trigger_data => {
        return fpdata.submitted_form.element.matches( trigger_data.sel );
    }
    
    function formSubmitEvents( {target} ){
        fpdata.submitted_form = { 'element' : target };
        FP.doTriggerActions( 'form_triggers' );
        FP.doActions( 'form_submit' );
    }

    document.addEventListener('blur', function(e) {
        if ( e.target.nodeType === Node.ELEMENT_NODE && e.target.matches('input, textarea, select') ) {
            if ( e.target.type && e.target.type == 'submit' ) return; // do not track submit btn
            fpdata.last_form_field = e.target;
            FP.doActions( 'form_field_blur' );
        }
    }, true);

    function wait_for_submit_events(){
        setTimeout( ()=>{ document.addEventListener('submit', formSubmitEvents ) }, fp.track.formsubm_trackdelay ? fp.track.formsubm_trackdelay * 1000 : 1000 );
    }

    FP.doWhenDOMLoaded( wait_for_submit_events );
