(()=>{

	// INCREASE / DECREASE VALUE IN THE "NUMBER" FIELD AFTER CLICKS IN "PLUS" AND "MINUS" BUTTONS

	function changeNumber( action, btn ){

		let num_field = FP.findFirst( 'input[type="number"]', btn.parentElement.parentElement ),
			old_val = parseInt( num_field.value || 0 ),
			min = num_field.min ? parseInt( num_field.min ) : -999;

		if ( action == 'plus' ){
			num_field.value = old_val + ( num_field.step || 1 );
		} else {
			let new_val = old_val - ( num_field.step || 1 );
			num_field.value = new_val < min ? min : new_val;
		}

		num_field.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	}

	document.addEventListener('click', e=>{
		
		let btn = e.target;

		if ( btn.closest('.fupi_number_increase') ) {
			changeNumber( 'plus', btn );
		} else if ( btn.closest('.fupi_number_decrease') ) {
			changeNumber( 'minus', btn );
		};
	})
})();
