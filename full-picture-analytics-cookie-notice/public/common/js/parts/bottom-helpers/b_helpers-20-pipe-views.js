    //
    // VIEWS TRIGGERS
    //

    function setIntersectionObserver ( els, trigger_id, trigger_data ) {

        let observed_count = els.length;

        function unobserveTarget( observer_name, observer, target ) {
            observer.unobserve( target ); // stop observing after the first view
            observed_count--;

            if ( observed_count == 0 ) {
                observer.disconnect();
                delete fp.observers[observer_name];
            }
        }

        // make an observer
        const observer = new IntersectionObserver( 
            ( entries, observer ) => {
                entries.forEach( ( { isIntersecting, target } ) => {
                    if ( isIntersecting ) {
                        let hooked_actions = trigger_data.actions;
                        unobserveTarget( observer_name, observer, target );
                        if ( hooked_actions ) hooked_actions.forEach( action => action[0]( target ) );
                    }
                });
            }, { 
                rootMargin: fp.track.intersections 
            } 
        );

        // save observer in array
        const observer_name = trigger_id + '_' + Date.now();
        fp.observers[observer_name] = observer;

        // run observer on every found element
        els.forEach( el => fp.observers[observer_name].observe(el) );
    };

    function findViewTriggerEls( added_els, selector ) {

        let els = [];

        added_els.forEach( el => {
            if ( el.nodeType !== Node.ELEMENT_NODE ) return;
            if ( el.matches( selector ) ) els.push( el );
            els = els.concat( FP.findAll( selector, el ) );
        } );

        return els;
    }

    // Check if there are elements on page that match triggers
    // Set observers for these triggers

    function registerIntersObservers ( newly_added_els = false ) {

        // STOP if there were some dom modification, but no new elements were added
        if ( newly_added_els && newly_added_els.length == 0 ) return;
        if ( ! fp.atrig || ! fp.atrig.view_triggers ) return;

        let triggers_o = fp.atrig.view_triggers;

        for ( const [trigger_id, trigger_data] of Object.entries(triggers_o) ) {
            
            // We do NOT STOP if the trigger is not used. CBs may be added by tools that haven't loaded yet

            let els = [];

            if ( newly_added_els && newly_added_els.length > 0 ) {
                els = findViewTriggerEls( newly_added_els, trigger_data.sel );
            } else {
                els = FP.findAll( trigger_data.sel );
            }

            if ( els.length == 0 ) continue;

            // Set observer for this trigger
            setIntersectionObserver( els, trigger_id, trigger_data );
        };
    };

    //
    // Enable Mutation Observers for Intersection Observers
    //
    
    FP.detectAddedElement = callback => { // also used by consb.js
        
        if ( ! Array.isArray( fp.observers.mutation ) ) {
            fp.observers.mutation = [];
        }

        // Create a unique ID for this observer registration
        const observerId = 'mutation_' + Date.now() + '_' + FP.getRandomStr();

        const observer = new MutationObserver( mutations => {
            mutations.forEach( mutation => {
                if ( mutation.type === 'childList' && mutation.addedNodes.length > 0 ) {
                    callback( mutation.addedNodes );
                }
            } );
        } );

        observer.observe( document.body, {
            childList: true,
            subtree: true
        } );

        // Store this observer registration with its ID
        fp.observers.mutation.push({
            id: observerId,
            observer: observer,
            callback: callback
        });
    }

    // Start tracking views only after the DOM is loaded and later, when it is modified

    function initObservers(){
        
        registerIntersObservers();

        if ( fp.track.use_mutation_observer ) {
            FP.detectAddedElement( addedNodes => {
                let els = FP.nl2Arr( addedNodes );
                if ( els.length > 0 ) FP.doActions( ['dom_modified'], els );
            } );
        }
    }

    FP.doWhenDOMLoaded( initObservers );

    FP.addAction( ['dom_modified'], registerIntersObservers );
    
