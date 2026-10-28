    // Helpers

    fp.load_queue = {};
    
    // Print script element to HTML
    FP.getScript = ( url, cb, attrs, tag = 'script', fallback = false, error_cb = false ) => {

        attrs = attrs || false;
        var new_tag = "";

        if ( tag == "img" ) {
            new_tag = d.createElement('img');
            new_tag.src = url;
        } else if ( tag == "link" ) {
            new_tag = d.createElement('link');
            new_tag.href = url;
        } else {
            new_tag = d.createElement('script')
            new_tag.src = url;
            new_tag.type = 'application/javascript';
        }

        if ( cb ) new_tag.onload = cb;

        if ( fallback ) {
            new_tag.onerror = ()=>{ FP.getScript( fallback, cb, attrs, tag, false, error_cb ); };
        } else if ( error_cb ) {
            new_tag.onerror = error_cb;
        }

        if ( attrs ) {
            // check if attrs holds a reference to and HTML tag
            if ( attrs instanceof Element ) {
                let old_tag = attrs;
                // copy from the original tag (with exceptions)
                Array.from( old_tag.attributes ).forEach( attr => {
                    if ( attr.name !== 'type' && attr.name !== 'data-src' && ! attr.name.includes('fupi') ) {
                        new_tag.setAttribute( attr.name, attr.value );
                    }
                });
            // or add provided values
            } else {
                for (var key in attrs) {
                    if ( key !== "/" ) new_tag.setAttribute(key, attrs[key]);
                }
            }
        }

        const append_to = tag == "img" ? 'body' : 'head';
        d.getElementsByTagName( append_to )[0].appendChild(new_tag);

        return new_tag;
    };
    
    

    FP.isScriptAllowedToLoad = ( id, force_load, permissions_a, geo_a ) => {

        // STOP if we don't have all the basic data yet
        if ( ! fp.ready ) return false;

        // STOP if the script has already loaded
        if ( fp.loaded.includes(id) ) return false;

        // START if the load is forced
        if ( force_load ) return true;

        // STOP if the tab is not in focus AND we are NOT on WooCommerce checkout page
        if ( fpdata.page_type !== 'Woo Order Received' ) {
            if ( document.hidden ) return false;
        }

        // STOP tracking an excluded visitor (MUST be after checking FORCE LOAD)
        if ( ! fp.main.track_current_user ) return false;
        
        // STOP if any permissions need to be given in this location...
        if ( fp.notice.enabled && fp.notice.mode == 'optin' && ! fpdata.cookies && permissions_a.length > 0 ) return false;

        // STOP if required permissions are not given
        if ( fpdata.cookies && permissions_a.length > 0 ) {
            if ( ! permissions_a.every( permission => fpdata.cookies[permission] ) ) return false;
        }

        return true;
    };

    // LOADING BLOCKED SCRIPTS
    // goes over all blocked scripts and check one by one if they can be loaded

    // used only in addScriptsToDom() below
    function loadInlineScript(temp_script) {

        let scr_id = temp_script.dataset?.fupi_id,
            orig_id = scr_id.replace('fp_',''),
            is_cscr = temp_script.dataset?.module == 'cscr';
        
        if ( ! scr_id ) return;

        let new_script = document.createElement('script');

        // Copy attributes from the original script except blocking-specific ones
        Array.from(temp_script.attributes).forEach(attr => {
            // Skip attributes that are specific to the blocking mechanism
            if (!['data-fupi_class', 'data-fupi_type', 'data-fupi_id', 'data-fupi_url', 
                  'data-fupi_force', 'data-fupi_permiss', 'data-fupi_geo'].includes(attr.name)) {
                new_script.setAttribute(attr.name, attr.value);
            }
        });

        new_script.setAttribute('type', "text/javascript");
        new_script.innerHTML = temp_script.innerHTML;
        temp_script.parentNode.replaceChild(new_script, temp_script);

        if ( fp.main.debug ) {
            if ( is_cscr && fp.cscr[orig_id] ) {
                console.log( "[FP] Custom script loaded: " + fp.cscr[orig_id] );
            } else {
                console.log( "[FP] Inline script loaded");
            }
        };

        fp.loaded.push( orig_id );
    };

    function loadBlockedIframe( placeholder, url, scr_id ) {

        let iframe = document.createElement('iframe');

        iframe.setAttribute('data-fupi_restore_id', scr_id);

        Array.from( placeholder.attributes ).forEach( attr => {
            if (
                attr.name == 'type' ||
                attr.name == 'src' ||
                attr.name == 'data-no-optimize' ||
                attr.name == 'data-noptimize' ||
                attr.name == 'nowprocket' ||
                attr.name.includes('fupi')
            ) return;

            iframe.setAttribute( attr.name, attr.value );
        });

        iframe.addEventListener( 'load', ()=>{ FP.loaded( scr_id, scr_id ) }, { once: true } );
        iframe.src = url;
        placeholder.parentNode.replaceChild( iframe, placeholder );
        iframe.removeAttribute('data-fupi_restore_id');

        return iframe;
    }

    // ENABLING MODULES FILES THAT HAVE ALREADY LOADED
    
    function create_queue_element( slug ) {

        return {
            "id" : slug,
            "state" : "registered",
            "eligibility" : {
                "state" : "pending",
                "reasons" : [],
            },
            "file" : {
                "state" : "not_requested",
                "attempts" : 0,
                "error" : false,
            },
            "fn" : {
                "state" : "not_registered",
                "cb" : false,
                "req_a" : [],
                "error" : false,
            },
        };
    }

    function get_queue_element( slug ) {

        if ( ! fp.load_queue[slug] ) fp.load_queue[slug] = create_queue_element( slug );

        return fp.load_queue[slug];
    }

    function update_queue_state( queue_element ) {

        if ( queue_element.fn.state == 'script_enabled' ) {
            queue_element.state = 'script_enabled';
        } else if ( queue_element.fn.state == 'function_failed' ) {
            queue_element.state = 'function_failed';
        } else if ( queue_element.fn.state == 'function_enabling' ) {
            queue_element.state = 'function_enabling';
        } else if ( queue_element.fn.state == 'function_waiting_for_dependencies' ) {
            queue_element.state = 'function_waiting_for_dependencies';
        } else if ( queue_element.file.state == 'file_load_failed' ) {
            queue_element.state = 'file_load_failed';
        } else if ( queue_element.file.state == 'file_loaded' ) {
            queue_element.state = 'file_loaded';
        } else if ( queue_element.file.state == 'loading_file' ) {
            queue_element.state = 'loading_file';
        } else if ( queue_element.eligibility.state == 'blocked' ) {
            queue_element.state = 'file_cannot_load';
        } else {
            queue_element.state = 'registered';
        }
    }

    function is_script_enabled( slug ) {

        return fp.load_queue[slug]?.fn?.state == 'script_enabled';
    }

    function enable_if_dependencies_loaded( queue_element ) {

        if ( queue_element.fn.state != 'function_waiting_for_dependencies' ) return;

        let deps_loaded = queue_element.fn.req_a.length == 0 || queue_element.fn.req_a.every( is_script_enabled );

        if ( deps_loaded ) {
            let fn = FP.getInner( queue_element.fn.cb, ".");

            if ( typeof fn === "function" ) {
                queue_element.fn.state = 'function_enabling';
                update_queue_state( queue_element );

                try {
                    fn();
                } catch ( error ) {
                    queue_element.fn.state = 'function_failed';
                    queue_element.fn.error = error instanceof Error ? error.message : String( error );
                    update_queue_state( queue_element );
                    console.error( '[FP] Could not enable ' + queue_element.id + '.', error );
                }
            } else {
                queue_element.fn.state = 'function_failed';
                queue_element.fn.error = 'Initializer not found: ' + queue_element.fn.cb;
                update_queue_state( queue_element );
                if ( fp.main.debug ) console.error( '[FP] ' + queue_element.fn.error );
            }
        }
    }

    FP.load = ( slug, cb_s = false, req_a = [] ) => {

        let queue_element = get_queue_element( slug );

        if ( cb_s && queue_element.fn.state == 'not_registered' ) {
            queue_element.fn.cb = cb_s;
            queue_element.fn.req_a = Array.isArray( req_a ) ? req_a : [];
            queue_element.fn.error = false;
            queue_element.fn.state = 'function_waiting_for_dependencies';
            update_queue_state( queue_element );
            enable_if_dependencies_loaded( queue_element );
        }
    };

    FP.loaded = ( slug, marker = false, text = false ) => {

        let queue_element = get_queue_element( slug );

        queue_element.file.state = 'file_loaded';
        queue_element.file.error = false;
        queue_element.fn.state = 'script_enabled';
        queue_element.fn.error = false;
        update_queue_state( queue_element );
        // if (fp.main.debug) console.log( text || "[FP] " + slug + " loaded " );

        if ( marker && ! fp.loaded.includes( marker ) ) fp.loaded.push( marker ); // for consent banner reloading

        // FP.loaded is the only dependency wake-up mechanism.
        for ( const data_o of Object.values( fp.load_queue ) ) {
            if ( data_o.fn?.state == 'function_waiting_for_dependencies' ) {
                enable_if_dependencies_loaded( data_o );
            }
        }
    };

    // File loader

    function requirement_error( code, message, retryable = false ) {

        return {
            "code" : code,
            "message" : message,
            "retryable" : retryable,
        };
    }

    function check_reqs_for_errors( fp_id, module_o ){

        let reqs = module_o['reqs'];

        if ( ! reqs ) return false;

        // START if the load is forced
        if ( fp?.[fp_id]?.force_load ) return false;

        // STOP tracking an excluded visitor (MUST be after checking FORCE LOAD)
        if ( ! fp.main.track_current_user ) return requirement_error( 'visitor_excluded', 'User cannot be tracked' );
        
        // START if the tool doesn't use cookies

        let no_cookie_field_id = reqs.no_cookie_field_id,
            doesnt_use_cookies = no_cookie_field_id && fp?.[fp_id]?.[no_cookie_field_id];
            
        if ( doesnt_use_cookies ) return false;

        if ( reqs.consents && fp.notice.enabled ) {
            
            let permissions_a = reqs.consents;
            
            if ( ! fpdata.cookies ) {

                // STOP if we wait for consent but some permissions need to be given
                if ( fp.notice.mode == 'optin' ) return requirement_error( 'waiting_for_consent', 'Waiting for consents', true );
            
            } else {

                // STOP if user chose their cookie preferences but some required permissions are missing
                if ( ! permissions_a.every( permission => fpdata.consents[ 'can_track_' + permission] ) ) return requirement_error( 'consent_missing', 'Required consents not provided', true );
            }
        }

        return false;

    }

    function check_file_reqs( module_error_msg, file_data_o ){
        
        let errors = module_error_msg ? [ module_error_msg ] : [],
            must_be_truthy = file_data_o['must_be_truthy'],
            cant_be_truthy = file_data_o['cant_be_truthy'];

        if ( file_data_o['must_exist'] ) {
            file_data_o['must_exist'].forEach( path => {
                if ( typeof FP.getInner( path ) === 'undefined' ) errors.push( requirement_error( 'required_value_missing', 'must contain ' + path ) );
            } );
        }

        if ( must_be_truthy ) {
            must_be_truthy.forEach( path => {
                if ( ! FP.getInner( path ) ) errors.push( requirement_error( 'required_value_missing', 'must have ' + path ) );
            } );
        }

        if ( cant_be_truthy ) {
            cant_be_truthy.forEach( path => {
                if ( FP.getInner( path ) ) errors.push( requirement_error( 'forbidden_value_present', 'cannot have ' + path ) );
            } )
        }

        return errors;
    }

    function is_valid_runtime_url( value ) {

        if ( typeof value !== 'string' || value.trim() === '' ) return false;

        try {
            let url = new URL( value );
            return url.protocol == 'http:' || url.protocol == 'https:';
        } catch ( error ) {
            return false;
        }
    }

    function fail_file_load( queue_element, error, attempt_nr ) {

        if ( queue_element.file.state != 'loading_file' || queue_element.file.attempts != attempt_nr ) return;

        queue_element.file.state = 'file_load_failed';
        queue_element.file.error = error;
        update_queue_state( queue_element );

        if ( fp.main.debug ) console.error( '[FP] Could not load ' + queue_element.id + ': ' + error );

        if ( queue_element.file.attempts < 2 ) {
            setTimeout( load_modules_files, 1000 );
        }
    }

    function load_runtime_file( file_data_o, queue_element ) {

        let load_timeout,
            attempt_nr = queue_element.file.attempts + 1;

        queue_element.eligibility.state = 'eligible';
        queue_element.eligibility.reasons = [];
        queue_element.file.state = 'loading_file';
        queue_element.file.error = false;
        queue_element.file.attempts = attempt_nr;
        update_queue_state( queue_element );

        let script_el = FP.getScript(
            file_data_o.url,
            () => {
                clearTimeout( load_timeout );

                if ( queue_element.file.state != 'loading_file' || queue_element.file.attempts != attempt_nr ) return;

                queue_element.file.state = 'file_loaded';
                queue_element.file.error = false;

                if ( ! file_data_o.path && queue_element.fn.state == 'not_registered' ) {
                    queue_element.fn.state = 'function_failed';
                    queue_element.fn.error = 'The file did not register or enable an initializer';
                }

                update_queue_state( queue_element );
            },
            false,
            'script',
            false,
            () => {
                clearTimeout( load_timeout );
                if ( script_el?.parentNode ) FP.remove( script_el );
                fail_file_load( queue_element, 'Network or browser error', attempt_nr );
            }
        );

        load_timeout = setTimeout( () => {
            if ( script_el?.parentNode ) FP.remove( script_el );
            fail_file_load( queue_element, 'Timed out after 15 seconds', attempt_nr );
        }, 15000 );
    }

    function load_modules_files(){
        
        if ( typeof fp.runtime == 'object' ) for ( const [fp_id, module_o] of Object.entries( fp.runtime )) {

            if ( ! module_o || ! Array.isArray( module_o.files ) ) {
                if ( fp.main.debug ) console.error( '[FP] Invalid runtime definition in fp.runtime.' + fp_id + '.' );
                continue;
            }
            
            let module_error_msg = check_reqs_for_errors( fp_id, module_o ),
                files_a = module_o['files'];

            // start loading files
            files_a.forEach( file_data_o => {

                let queue_element = get_queue_element( file_data_o.id ),
                    is_terminal = queue_element.fn.state == 'script_enabled' || queue_element.fn.state == 'function_failed',
                    is_loading = queue_element.file.state == 'loading_file' || queue_element.file.state == 'file_loaded',
                    retry_exhausted = queue_element.file.state == 'file_load_failed' && queue_element.file.attempts >= 2;

                if ( is_terminal || is_loading || retry_exhausted ) return;

                if ( file_data_o.path ) {
                    let maybe_url = FP.getInner( file_data_o.path );
                    
                    if ( ! maybe_url.startsWith('http') ){
                        maybe_url = 'https:' + maybe_url;
                    }

                    if ( is_valid_runtime_url( maybe_url ) ) {
                        file_data_o.url = maybe_url.trim();
                    } else {
                        queue_element.eligibility.state = 'blocked';
                        queue_element.eligibility.reasons = [ requirement_error(
                            'invalid_url',
                            file_data_o.path + ' must resolve to an absolute HTTP(S) URL'
                        ) ];
                        update_queue_state( queue_element );
                        return;
                    }
                }

                let file_errors = check_file_reqs( module_error_msg, file_data_o );

                if ( file_errors.length == 0 ) {
                    load_runtime_file( file_data_o, queue_element );
                } else {
                    queue_element.eligibility.state = 'blocked';
                    queue_element.eligibility.reasons = file_errors;
                    update_queue_state( queue_element );
                }
            } )
        }
    }

    function load_blocked_scripts(){
        
        let plain_txt_scripts = FP.findAll('script[data-fupi_class*="fupi_blocked_script"]');

        plain_txt_scripts.forEach( plain_el => {
            
            let dta = plain_el.dataset,
                tag_type = dta.fupi_type,
                scr_id = dta.fupi_id,
                url = dta.fupi_url ?? false,
                force_load = dta.fupi_force == '1',
                permissions_a = dta.fupi_permiss ? dta.fupi_permiss.split(' ') : [],
                geo_a = dta.fupi_geo ? dta.fupi_geo.split(',') : [];
            
            if ( FP.isScriptAllowedToLoad( scr_id, force_load, permissions_a, geo_a ) ) {
                if ( tag_type == 'inline' ){
                    loadInlineScript( plain_el ); // replaces old tag
                } else if ( tag_type == 'iframe' ) {
                    if ( url ) {
                        loadBlockedIframe( plain_el, url, scr_id );
                    } else if ( fp.main.debug ) {
                        console.error('[FP] Couldn\'t load iframe with ID ' + scr_id +  '. Missing src value.')
                    }
                } else {
                    if ( url ){
                        FP.getScript(
                            url, 
                            ()=>{ FP.loaded( scr_id, scr_id ) }, 
                            plain_el,
                            tag_type
                        );
                        FP.remove(plain_el);
                    } else {
                        if (fp.main.debug) console.error('[FP] Couldn\'t load script with ID ' + scr_id +  '. Missing src or href values.')
                    }
                }
            }
        });
    }

    function addScriptsToDOM() {

        if ( ! fp.ready ) return false;

        load_modules_files();
        load_blocked_scripts();
    }
