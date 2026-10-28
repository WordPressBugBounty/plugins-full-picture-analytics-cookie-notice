	FP.updateScrollValues = function(){

        fpdata.scrolled = fpdata.scrolled || {};

        fpdata.scrolled.current         = Math.round( document.documentElement.scrollTop / ( document.documentElement.scrollHeight - document.documentElement.clientHeight ) * 100 ) || 0;
        fpdata.scrolled.current_px      = document.documentElement.scrollTop || 0;
        fpdata.scrolled.max             = fpdata.scrolled.max || fpdata.scrolled.current;
        fpdata.scrolled.max_px          = fpdata.scrolled.max_px || fpdata.scrolled.current_px;

        if ( fpdata.scrolled.current > fpdata.scrolled.max ) {
			fpdata.scrolled.max         = fpdata.scrolled.current;
			fpdata.scrolled.max_px      = fpdata.scrolled.current_px;
		}
	}

	FP.actionChecks.scroll_after_wait = ()=>{

		if ( ! fp.events.scroll_after_wait || fp.events.scroll_after_wait.length == 0 ) return false;
		if ( fpdata.activity.total < fp.track.track_scroll_time || fpdata.scrolled.current_px < fp.track.track_scroll_min ) return false;
		
		let reached_point = false,
			max = fp.track.scroll_depths.length;

		// get lowest reached scroll
		for ( var i = 0; i < max; i++ ) {
			if ( fpdata.scrolled.max > fp.track.scroll_depths[i] ) {
				reached_point = fp.track.scroll_depths[i];
			}
		}

		if ( ! reached_point ) return false;

		// remove reached scroll points
		fp.track.scroll_depths = fp.track.scroll_depths.filter( function( point ){ return point > reached_point } );

		// remember in fpdata
		fpdata.scrolled.reached_point = reached_point;

		return true;
	}

	// set base values
	FP.updateScrollValues();

	// listen to scroll
    function on_scroll() {
        FP.startActivityTimer();
        FP.updateScrollValues();
		FP.doActions( ['scroll_after_wait', 'scroll'] );
    }

    document.addEventListener( 'scroll', FP.throttle( on_scroll, 50, { trailing: true } ) );
