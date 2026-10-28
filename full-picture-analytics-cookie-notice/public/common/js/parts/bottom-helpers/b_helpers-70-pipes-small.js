    // MOUSELEAVE
    document.documentElement.addEventListener('mouseleave', (e) => {
        // Don't fire if the page is losing focus (not a real mouse leave)
        if (document.hidden) return;
        
        // Check if mouse actually left viewport boundaries
        if (e.clientY <= 0 || e.clientX <= 0 || 
            e.clientX >= window.innerWidth || e.clientY >= window.innerHeight) {
            FP.doActions('mouse_leave_window');
        }
    }, false );
    
    // DYNAMIC URL CHANGE && TIME UPDATE IN FPDATA
    // Use History API hooks for immediate URL change detection, with fallback polling
    
    // Track current URL
    let currentUrl = location.href;
    
    // Update time every second regardless of focus
    setInterval( () => { 
        fpdata.date = FP.getFormattedDateTime();
    }, 1000);

    function isVirtualPageview( url ) {

        const rules = fp.track.virtual_pageviews || [];

        if ( rules.length == 0 ) return true; // Treat all URL changes as virtual pageviews

        return rules.some( rule => {
            if ( ! rule.url_part ) return false;
            return rule.compare == 'contains' ? url.includes( rule.url_part ) : ! url.includes( rule.url_part );
        } );
    }
    
    // Function to check and handle URL changes
    function checkUrlChange() {
        if ( location.href !== currentUrl ) {
            currentUrl = location.href;
            FP.doActions( 'url_change' );

            if ( isVirtualPageview( currentUrl ) ) {
                FP.doActions( 'virtual_pageview' );
            }
        }
    }
    
    // Try to use History API for immediate detection
    try {
        // Override pushState
        const originalPushState = history.pushState;
        history.pushState = function() {
            const result = originalPushState.apply(this, arguments);
            // Delay slightly to let SPAs update the page title and content.
            setTimeout(checkUrlChange, 200);
            return result;
        };
        
        // Override replaceState
        const originalReplaceState = history.replaceState;
        history.replaceState = function() {
            const result = originalReplaceState.apply(this, arguments);
            // Delay slightly to let SPAs update the page title and content.
            setTimeout(checkUrlChange, 200);
            return result;
        };
        
        // Listen for popstate events (back/forward buttons)
        window.addEventListener('popstate', () => setTimeout(checkUrlChange, 200));
    } catch (e) {
        // If History API wrapping fails, fall back to polling
        if (fp.main.debug) console.log('[FP] History API wrapping failed, using polling for URL changes');
        setInterval(checkUrlChange, 1000);
    }