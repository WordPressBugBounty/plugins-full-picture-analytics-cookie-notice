(() => {

	// DISABLE SETTINGS NOT AVAILABLE FOR FREE USERS

	let fupi_content_el = FP.findID('fupi_content');

	if ( ! fupi_content_el ) return;
	
	FP.disableFieldsForPlans = () => {
		
		FP.findAll( '.fupi_must_have_info, .fupi_must_have_pro_ico, .fupi_must_have_pro_ico_round' ).forEach( el => {

			let wrap = el.closest('.fupi_r3_field, tr'),
				fields = FP.findAll( 'input, select, textarea', wrap ),
				buttons = FP.findAll( '.fupi_r3_btn', wrap );
			
			fields.forEach( field => field.disabled = true );
			buttons.forEach( btn => btn.classList.add('fupi_disabled') );
			
			wrap.classList.add('fupi_missing_must_have', 'fupi_disabled');
		});
	}

	FP.disableFieldsForPlans();
	
})();