function fupi_simpl(){

	function simpleServer(){
		

		return Promise.resolve({
			success: true,
			skipped: true,
		});
	}

	function simplePixel(){

		let script_src = fp.simpl.src ? fp.simpl.src : 'https://scripts.simpleanalyticscdn.com/',
			latest_file_name = 'latest.js',
			params = { 'defer' : 'defer' };

		// placeholder event function
		// https://docs.simpleanalytics.com/events#placeholder-event-function
		window.sa_event=window.sa_event||function(){var a=[].slice.call(arguments);window.sa_event.q?window.sa_event.q.push(a):window.sa_event.q=[a]};

		if ( fp.simpl.join_traffic ) params['data-hostname'] = fp.simpl.join_traffic;
		if ( fp.simpl.hashes ) params['data-mode'] = 'hash';
		if ( fp.simpl.localhost ) latest_file_name = 'latest.dev.js';

		FP.getScript( script_src + latest_file_name, false, params );
	}

	let use_server = false;
	
	use_server ? simpleServer() : simplePixel();

	FP.loaded('simpl','simpl','[FP] Simple Analytics loaded');
};

fupi_simpl();
