<?php

return <<<'JS'
; (function(){

			if ( ! window.fp || ! Array.isArray( fp.scrblk ) || fp.scrblk.length === 0 ) return;

			fp.vars = fp.vars || {};
			if ( fp.vars.dynamic_blocking_ready ) return;
			fp.vars.dynamic_blocking_ready = true;

			var url_attrs = {
					"script" : { attr : "src", rule : "src", type : "script" },
					"link"   : { attr : "href", rule : "link_href", type : "link" },
					"img"    : { attr : "src", rule : "img_src", type : "img" },
					"iframe" : { attr : "src", rule : "iframe_src", type : "iframe" },
				},
				block_counts = {},
				script_load_check_pending = false,
				orig_set_attribute = Element.prototype.setAttribute,
				orig_get_attribute = Element.prototype.getAttribute,
				orig_append_child = Node.prototype.appendChild,
				orig_insert_before = Node.prototype.insertBefore,
				orig_replace_child = Node.prototype.replaceChild;

			function isSupportedElement( el ){
				return !! ( el && el.nodeType === 1 && el.tagName && url_attrs[el.tagName.toLowerCase()] );
			}

			function isBlockedPlaceholder( el ){
				var fupi_class = isSupportedElement( el ) ? orig_get_attribute.call( el, "data-fupi_class" ) || "" : "";
				return fupi_class.indexOf( "fupi_blocked_script" ) !== -1;
			}

			function getRuleMatch( el, url ){

				if ( ! url || ! isSupportedElement( el ) || isBlockedPlaceholder( el ) ) return false;

				var tag_data = url_attrs[el.tagName.toLowerCase()],
					url_lc = ( url + "" ).toLowerCase();

				for ( var i = 0; i < fp.scrblk.length; i++ ) {

					var tool = fp.scrblk[i];
					if ( ! tool || ! tool.id || tool.force_load || ! Array.isArray( tool.rules ) ) continue;

					for ( var j = 0; j < tool.rules.length; j++ ) {

						var rule = tool.rules[j];
						if ( ! rule || rule.block_by !== tag_data.rule || ! rule.unique ) continue;
						if ( url_lc.indexOf( ( rule.unique + "" ).toLowerCase() ) !== -1 ) return { tool : tool, tag_data : tag_data, url : url, el : el };
					}
				}

				return false;
			}

			function getElementUrl( el ){
				if ( ! isSupportedElement( el ) ) return "";
				var attr = url_attrs[el.tagName.toLowerCase()].attr;
				return orig_get_attribute.call( el, "data-fupi_pending_url" ) || orig_get_attribute.call( el, attr ) || el[attr] || "";
			}

			function getPermissions( tool ){
				var permissions = [];
				if ( tool.stats ) permissions.push( "stats" );
				if ( tool.pers ) permissions.push( "personalisation" );
				if ( tool.market ) permissions.push( "marketing" );
				return permissions;
			}

			function getGeo( tool ){
				return tool.method && tool.countries ? tool.method + "," + ( tool.countries + "" ).replace( /,/g, " " ) : "";
			}

			function getScriptId( tool_id ){
				var base_id = "fp_" + tool_id;
				block_counts[base_id] = ( block_counts[base_id] || 0 ) + 1;
				return block_counts[base_id] === 1 ? base_id : base_id + "_" + block_counts[base_id];
			}

			function canLoadNow( script_id, tool, permissions, geo ){

				if ( ! fp.ready ) return false;

				return FP.isScriptAllowedToLoad(
					script_id,
					!! tool.force_load,
					permissions,
					geo ? geo.split( "," ) : []
				);
			}

			function canMatchLoadNow( match ){
				if ( match.tool.always ) return false;
				var script_id = orig_get_attribute.call( match.el, "data-fupi_restore_id" ) || "fp_" + match.tool.id;
				return canLoadNow( script_id, match.tool, getPermissions( match.tool ), getGeo( match.tool ) );
			}

			function triggerScriptLoadCheck(){
				if ( script_load_check_pending ) return;

				script_load_check_pending = true;

				Promise.resolve().then( function(){
					script_load_check_pending = false;
					document.dispatchEvent( new CustomEvent( "fp_load_scripts" ) );
				});
			}

			function makePlaceholder( el, match ){

				var permissions = getPermissions( match.tool ),
					geo = getGeo( match.tool ),
					placeholder,
					script_id,
					url_attr = match.tag_data.attr;

				if ( match.tool.always ) return document.createComment( "fupi_blocked_script" );

				if ( canMatchLoadNow( match ) ) return el;
				script_id = getScriptId( match.tool.id );

				placeholder = document.createElement( "script" );

				Array.from( el.attributes ).forEach( function( attr ){
					if ( attr.name === url_attr || attr.name === "type" || attr.name === "data-fupi_pending_url" || attr.name.indexOf( "fupi" ) !== -1 ) return;
					orig_set_attribute.call( placeholder, attr.name, attr.value );
				});

				orig_set_attribute.call( placeholder, "type", "text/plain" );
				orig_set_attribute.call( placeholder, "data-fupi_type", match.tag_data.type );
				orig_set_attribute.call( placeholder, "data-fupi_id", script_id );
				orig_set_attribute.call( placeholder, "data-fupi_class", "fupi_blocked_script fupi_no_defer" );
				orig_set_attribute.call( placeholder, "data-fupi_force", match.tool.force_load ? "1" : "0" );
				orig_set_attribute.call( placeholder, "data-fupi_permiss", permissions.join( " " ) );
				orig_set_attribute.call( placeholder, "data-fupi_geo", geo );
				orig_set_attribute.call( placeholder, "data-fupi_url", match.url );
				orig_set_attribute.call( placeholder, "data-no-optimize", "1" );
				orig_set_attribute.call( placeholder, "data-noptimize", "" );
				orig_set_attribute.call( placeholder, "nowprocket", "" );

				return placeholder;
			}

			function getBlockedReplacement( el ){
				var match = getRuleMatch( el, getElementUrl( el ) );
				return match ? makePlaceholder( el, match ) : el;
			}

			function replaceConnectedElement( el ){

				if ( ! el || ! el.isConnected || ! el.parentNode ) return;

				var replacement = getBlockedReplacement( el );

				if ( replacement === el ) return;

				orig_replace_child.call( el.parentNode, replacement, el );
				triggerScriptLoadCheck();
			}

			function blockNode( node ){

				if ( ! node || ( node.nodeType !== 1 && node.nodeType !== 11 ) ) return node;

				node.__fupi_blocked_dynamic_node = false;

				if ( node.nodeType === 1 ) {
					var replacement = getBlockedReplacement( node );
					if ( replacement !== node ) {
						replacement.__fupi_blocked_dynamic_node = true;
						return replacement;
					}
				}

				Array.from( node.querySelectorAll ? node.querySelectorAll( "script[src],link[href],img[src],iframe[src]" ) : [] ).forEach( function( child ){
					var child_replacement = getBlockedReplacement( child );
					if ( child_replacement !== child && child.parentNode ) {
						orig_replace_child.call( child.parentNode, child_replacement, child );
						node.__fupi_blocked_dynamic_node = true;
					}
				});

				return node;
			}

			function blockNodes( nodes ){
				var blocked = false,
					blocked_nodes = nodes.map( function( node ){
						var replacement;
						if ( node && typeof node === "string" ) return node;
						replacement = blockNode( node );
						if ( replacement && replacement.__fupi_blocked_dynamic_node ) blocked = true;
						return replacement;
				});

				blocked_nodes.__fupi_blocked_dynamic_node = blocked;

				return blocked_nodes;
			}

			Element.prototype.setAttribute = function( attr_name, value ){

				var tag_data = isSupportedElement( this ) ? url_attrs[this.tagName.toLowerCase()] : false,
					match;

				if ( tag_data && ( attr_name + "" ).toLowerCase() === tag_data.attr ) {
					match = getRuleMatch( this, value );
				}

				if ( match ) {
					if ( canMatchLoadNow( match ) ) return orig_set_attribute.call( this, attr_name, value );
					orig_set_attribute.call( this, "data-fupi_pending_url", value );
					replaceConnectedElement( this );
					return;
				}

				return orig_set_attribute.call( this, attr_name, value );
			};

			function patchUrlProperty( proto, prop ){

				if ( ! proto ) return;

				var descriptor = Object.getOwnPropertyDescriptor( proto, prop );
				if ( ! descriptor || ! descriptor.set || ! descriptor.get ) return;

				Object.defineProperty( proto, prop, {
					configurable : true,
					enumerable : descriptor.enumerable,
					get : function(){
						return descriptor.get.call( this );
					},
					set : function( value ){
						var match = getRuleMatch( this, value );
						if ( match ) {
							if ( canMatchLoadNow( match ) ) return descriptor.set.call( this, value );
							orig_set_attribute.call( this, "data-fupi_pending_url", value );
							replaceConnectedElement( this );
							return;
						}
						descriptor.set.call( this, value );
					}
				} );
			}

			patchUrlProperty( window.HTMLScriptElement && window.HTMLScriptElement.prototype, "src" );
			patchUrlProperty( window.HTMLImageElement && window.HTMLImageElement.prototype, "src" );
			patchUrlProperty( window.HTMLLinkElement && window.HTMLLinkElement.prototype, "href" );
			patchUrlProperty( window.HTMLIFrameElement && window.HTMLIFrameElement.prototype, "src" );

			Node.prototype.appendChild = function( child ){
				var replacement = blockNode( child ),
					result = orig_append_child.call( this, replacement );
				if ( replacement !== child || replacement.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
				return result;
			};

			Node.prototype.insertBefore = function( child, reference_node ){
				var replacement = blockNode( child ),
					result = orig_insert_before.call( this, replacement, reference_node );
				if ( replacement !== child || replacement.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
				return result;
			};

			Node.prototype.replaceChild = function( new_child, old_child ){
				var replacement = blockNode( new_child ),
					result = orig_replace_child.call( this, replacement, old_child );
				if ( replacement !== new_child || replacement.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
				return result;
			};

			if ( Element.prototype.append ) {
				var orig_append = Element.prototype.append;
				Element.prototype.append = function(){
					var nodes = blockNodes( Array.from( arguments ) );
					var result = orig_append.apply( this, nodes );
					if ( nodes.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
					return result;
				};
			}

			if ( Element.prototype.prepend ) {
				var orig_prepend = Element.prototype.prepend;
				Element.prototype.prepend = function(){
					var nodes = blockNodes( Array.from( arguments ) );
					var result = orig_prepend.apply( this, nodes );
					if ( nodes.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
					return result;
				};
			}

			if ( Element.prototype.before ) {
				var orig_before = Element.prototype.before;
				Element.prototype.before = function(){
					var nodes = blockNodes( Array.from( arguments ) );
					var result = orig_before.apply( this, nodes );
					if ( nodes.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
					return result;
				};
			}

			if ( Element.prototype.after ) {
				var orig_after = Element.prototype.after;
				Element.prototype.after = function(){
					var nodes = blockNodes( Array.from( arguments ) );
					var result = orig_after.apply( this, nodes );
					if ( nodes.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
					return result;
				};
			}

			if ( Element.prototype.replaceWith ) {
				var orig_replace_with = Element.prototype.replaceWith;
				Element.prototype.replaceWith = function(){
					var nodes = blockNodes( Array.from( arguments ) );
					var result = orig_replace_with.apply( this, nodes );
					if ( nodes.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
					return result;
				};
			}

			if ( Element.prototype.insertAdjacentElement ) {
				var orig_insert_adjacent_element = Element.prototype.insertAdjacentElement;
				Element.prototype.insertAdjacentElement = function( position, element ){
					var replacement = blockNode( element ),
						result = orig_insert_adjacent_element.call( this, position, replacement );
					if ( replacement !== element || replacement.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
					return result;
				};
			}

			if ( Element.prototype.insertAdjacentHTML ) {
				Element.prototype.insertAdjacentHTML = function( position, html ){

					var template = document.createElement( "template" ),
						fragment;

					template.innerHTML = html;
					fragment = blockNode( template.content );

					if ( position === "beforebegin" && this.parentNode ) {
						orig_insert_before.call( this.parentNode, fragment, this );
					} else if ( position === "afterbegin" ) {
						orig_insert_before.call( this, fragment, this.firstChild );
					} else if ( position === "beforeend" ) {
						orig_append_child.call( this, fragment );
					} else if ( position === "afterend" && this.parentNode ) {
						orig_insert_before.call( this.parentNode, fragment, this.nextSibling );
					}

					if ( fragment.__fupi_blocked_dynamic_node ) triggerScriptLoadCheck();
				};
			}
		})();
JS;
