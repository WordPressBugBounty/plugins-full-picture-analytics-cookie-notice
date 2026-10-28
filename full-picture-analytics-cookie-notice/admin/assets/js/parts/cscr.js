(()=>{

    const module_id = fupi_content.dataset.page;

	if ( module_id != 'cscr' ) return;

	function get_attributes_from_str( str ){

		// return an object with key-value pairs
		// string example: 'data-param='abc def ghi' async src="https://www.googletagmanager.com/gtag/js?id=UA-11111111-1" data-param2="abc" id="anID" class="class1 class2"'

		let parts = str.trim().split(' '), // we break the string into parts by 'space' (spaces can occur in unexpected places! see example above)
			ok_parts = parts.filter( v => v ), // and remove empty values from array
			ret_val = {};

		for ( var i = 0; i < ok_parts.length; i++ ) {

			let part = ok_parts[i],
				eq_index = part.indexOf('='),
				last_char = part[part.length-1];

			// if we have an equal sign
			// (this means that the value before the equal sign is the key and the one after is the value)
			if ( eq_index != -1 ){

				// we check if the last element of the current part is an apostrophe. If it isn\'t then it means we split the value in two or more pieces and need to join it with the next string parts
				while ( ! ( last_char == '"' || last_char == "'" ) && i+1 <= ok_parts.length ) {
					part += ' ' + ok_parts[i+1];
					last_char = part[part.length-1];
					i++;
				}

				// if we have the key and the whole value
				if ( last_char == '"' || last_char == "'" ) {
					// we save the key and value pair
					let key_val = part.split(/=(.*)/s); // split on the first occurance of "=" sign. They may sometimes show up in values too
					ret_val[key_val[0]]= key_val[1];
				}

			// if we don't have an equal sign this is a single value
			} else {
				ret_val[part] = true;
			}
		}

		return ret_val;
	}

	function reformat_custom_scripts( textarea ){

        let script = textarea.value;

        if ( script.length == 0 ) return;

		// remove all ending "</script>" tags
		script = script.replaceAll('</script>', '');

		// remove all HTML comments including everything between <!-- and -->
		script = script.replaceAll(/<!--/g, '//');
		script = script.replaceAll(/-->/g, '');

		// remove noscript tags with everything in between
		script = script.replaceAll(/<noscript>[\s\S]*?<\/noscript>/g, '');

		// find all <script[something or nothing]> tags
		let start_regex = /<script[\s\S]*?>/gi,
			matches = script.match(start_regex),
			replacements = [];

		// get their attributes
		if ( Array.isArray( matches ) && matches.length > 0 ) matches.forEach( txt => {

			// remove "<script" and final ">"
			txt = txt.replace('<script','');
			txt = txt.slice(0, -1);
			txt = txt.trim();

			if ( txt.length == 0 ) {

				replacements.push('');

			} else {

				// try to transform the "<script [attributes]>" into "getScript()"
				let attributes_obj = get_attributes_from_str(txt),
					src = attributes_obj.src ? attributes_obj.src : false,
					attrs_str = '';

				if ( ! src ) {
					// Remove the whole <script> tag
					replacements.push('');
				} else {
					// Build string with attributes
					for (const [key, val] of Object.entries(attributes_obj)) {
						if ( key != 'src' ) {
							if ( attrs_str.length > 1 ) attrs_str += ", ";
							attrs_str += '"' + key + '": ' + val;
						}
					}
	
					replacements.push([src, attrs_str]);
				}
			}
		} );

		// prepare elements to replace <string [something or nothing]> in the original script
		replacements.forEach( ( repl, i ) =>{
			if ( repl ) replacements[i] = 'FP.getScript(' + repl[0] + ', false, {' + repl[1] + '});'; // repl[0] & repl[1] are already properly formatted
		} )

		// replace elements in string one by one

		let i = 0;

		textarea.value = script.replace(start_regex, ()=>{ return replacements[i++]; });
	}

	// INIT
	
	document.addEventListener( 'focusout', e => {

        let target = e.target,
            is_cscr_textarea = target.tagName == 'TEXTAREA' && target.id && target.id.includes('fupi_cscr') && target.id.includes('[scr]');

        if ( ! is_cscr_textarea ) return;
        
        reformat_custom_scripts( target ); // e.target.value = e.target.value.replaceAll(/<.*?script[\s\S]*?>/g, '');
        
    } )
})();