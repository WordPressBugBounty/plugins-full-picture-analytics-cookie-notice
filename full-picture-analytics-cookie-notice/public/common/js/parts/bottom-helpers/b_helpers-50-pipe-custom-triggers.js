    

    FP.doWhenDOMLoaded( () => {
        if ( fp.vars.dom_loaded ) return;

        fp.vars.dom_loaded = true;
        FP.doActions( 'dom_loaded' );
    } );
