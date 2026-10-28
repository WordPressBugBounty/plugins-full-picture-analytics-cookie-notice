( function( window ) {
	'use strict';

	const EXCERPT_LENGTH = 400;
	const IFRAME_TITLE_LENGTH = 200;
	const PLACEHOLDER_CLASS = 'fupi_blocked_script';
	const IFRAME_PLACEHOLDER_CLASS = 'fupi_blocked_iframe';
	const IFRAME_DATA_CLASS = 'fupi_iframe_data';
	const IFRAME_PLACEHOLDER_ID = 'iframe_placeholder';
	const SELF_URL_PARTS = [
		'/full-picture-analytics-cookie-notice/',
		'/full-picture-premium/',
		'fupi-html-scanner.js',
		'fupi-setup-console.js',
	];
	const SELF_INLINE_IDS = [
		'fp_data_js',
		'fupi_default_texts',
		'fupi_iframe_texts',
		'fupi_mini_cart_data',
		'fupi_pixelman_compat',
		'fupi_woo_cart_block',
	];
	const CONSENT_MODE_SCRIPT_IDS = [
		'fupi_gtag_script',
		'fupi_gtag_gateway_script',
	];
	const RULE_TYPES = {
		IFRAME: 'iframe_src',
		IMAGE: 'img_src',
		INLINE_SCRIPT: 'content',
		LINK: 'link_href',
		SCRIPT: 'src',
	};
	const PREFIX_REASONS = {
		child_theme: 'Likely-safe active child-theme resource',
		parent_theme: 'Likely-safe active parent-theme resource',
		uploads: 'Likely-safe WordPress uploads resource',
		wordpress_core: 'Likely-safe WordPress core resource',
	};
	const NON_FETCHING_LINK_RELS = [
		'alternate',
		'author',
		'bookmark',
		'canonical',
		'help',
		'license',
		'next',
		'prev',
		'search',
		'tag',
	];
	const NON_EXECUTABLE_SCRIPT_TYPES = [
		'application/json',
		'application/ld+json',
		'importmap',
	];
	const IMAGE_FILE_PATTERN = /\.(?:avif|gif|ico|jpe?g|png|svg|webp)$/i;
	const FONT_FILE_PATTERN = /\.(?:eot|otf|ttf|woff2?)$/i;
	const STYLESHEET_FILE_PATTERN = /\.css$/i;
	const VERSION_QUERY_PARAMETERS = [ 'v', 'ver', 'version' ];
	const SENSITIVE_ID_TERMS = [
		'user',
		'customer',
		'client',
		'visitor',
		'session',
		'member',
		'subscriber',
		'account',
		'order',
		'email',
		'person',
		'profile',
		'cart',
		'transaction',
		'payment',
	];

	function getAttribute( element, name ) {
		return element.getAttribute( name );
	}

	function hasClass( element, className ) {
		return String( getAttribute( element, 'class' ) || '' ).split( /\s+/ ).includes( className );
	}

	function getBaseUrl( document ) {
		return document.baseURI || document.location && document.location.href || '';
	}

	function normalizeUrl( value, baseUrl ) {
		if ( ! value ) return null;

		try {
			const url = new URL( value, baseUrl );
			url.username = '';
			url.password = '';
			return url.href;
		} catch ( error ) {
			return null;
		}
	}

	function normalizeKind( kind ) {
		const normalized = String( kind || '' ).toLowerCase().replace( /[ -]/g, '_' );

		if ( [ 'core', 'wp_core', 'wordpress', 'wordpress_core' ].includes( normalized ) ) return 'wordpress_core';
		if ( [ 'upload', 'uploads', 'uploads_url' ].includes( normalized ) ) return 'uploads';
		if ( [ 'theme', 'parent', 'parent_theme' ].includes( normalized ) ) return 'parent_theme';
		if ( [ 'child', 'child_theme' ].includes( normalized ) ) return 'child_theme';
		return normalized;
	}

	function normalizePlugins( metadata ) {
		const plugins = [];
		const groupedPlugins = metadata && metadata.plugins && typeof metadata.plugins === 'object' && ! Array.isArray( metadata.plugins ) ? Object.values( metadata.plugins ) : [];
		const sources = [
			...groupedPlugins,
			metadata && Array.isArray( metadata.plugins ) ? metadata.plugins : null,
			metadata && metadata.active_plugins,
			metadata && metadata.network_plugins,
			metadata && metadata.network_active_plugins,
			metadata && metadata.mu_plugins,
			metadata && metadata.must_use_plugins,
		];

		function addPlugin( plugin, fallbackFolder ) {
			if ( typeof plugin === 'string' ) {
				plugins.push( { folder: fallbackFolder || plugin, name: plugin } );
				return;
			}

			if ( ! plugin || typeof plugin !== 'object' ) return;
			const folder = plugin.folder || plugin.slug || plugin.directory || fallbackFolder;
			const name = plugin.name || plugin.display_name || plugin.displayName;
			if ( folder && name ) plugins.push( {
				folder: String( folder ).replace( /^\/+|\/+$/g, '' ),
				name: String( name ),
				url_prefix: plugin.url_prefix || null,
			} );
		}

		for ( const source of sources ) {
			if ( Array.isArray( source ) ) {
				for ( const plugin of source ) addPlugin( plugin );
			} else if ( source && typeof source === 'object' ) {
				for ( const [ folder, plugin ] of Object.entries( source ) ) addPlugin( plugin, folder );
			}
		}

		return plugins;
	}

	function normalizePrefixes( metadata, baseUrl ) {
		const prefixes = [];
		const source = metadata && metadata.url_prefixes;

		function addPrefix( kind, value ) {
			const url = normalizeUrl( value, baseUrl );
			kind = normalizeKind( kind );
			if ( url && PREFIX_REASONS[kind] ) prefixes.push( { kind, url } );
		}

		if ( Array.isArray( source ) ) {
			for ( const prefix of source ) {
				if ( prefix && typeof prefix === 'object' ) {
					addPrefix( prefix.kind || prefix.type || prefix.category, prefix.url || prefix.prefix );
				}
			}
		} else if ( source && typeof source === 'object' ) {
			for ( const [ kind, values ] of Object.entries( source ) ) {
				for ( const value of Array.isArray( values ) ? values : [ values ] ) addPrefix( kind, value );
			}
		}

		return prefixes;
	}

	function getProbablePlugin( urlValue, plugins ) {
		let pathname;
		try {
			pathname = decodeURIComponent( new URL( urlValue ).pathname );
		} catch ( error ) {
			return null;
		}

		for ( const plugin of plugins ) {
			if ( plugin.url_prefix ) {
				const prefix = normalizeUrl( plugin.url_prefix, urlValue );
				if ( prefix && urlValue.startsWith( prefix ) ) return plugin;
			}
			const folder = escapeRegExp( plugin.folder );
			if ( new RegExp( `/wp-content/(?:mu-)?plugins/${ folder }(?:/|$)`, 'i' ).test( pathname ) ) return plugin;
		}

		return null;
	}

	function isSameOrigin( urlValue, pageOrigin ) {
		try {
			return new URL( urlValue ).origin === pageOrigin;
		} catch ( error ) {
			return false;
		}
	}

	function isSelfUrl( urlValue, selfPrefixes ) {
		const lowerUrl = urlValue.toLowerCase();
		return SELF_URL_PARTS.some( part => lowerUrl.includes( part ) ) || selfPrefixes.some( prefix => urlValue.startsWith( prefix ) );
	}

	function isConsentModeTagLoader( element, urlValue ) {
		if ( CONSENT_MODE_SCRIPT_IDS.includes( getAttribute( element, 'id' ) ) ) return true;

		const url = getUrlParts( urlValue );
		return Boolean(
			url &&
			url.hostname.toLowerCase() === 'www.googletagmanager.com' &&
			[ '/gtag/js', '/gtm.js' ].includes( url.pathname )
		);
	}

	function isSelfInlineScript( excerpt ) {
		return /\bFupiHtmlScanner\b|\bfupi_setup_console\b/.test( excerpt ) ||
			/^\s*(?:let|var|const)\s+fp_(?:usermeta|postmeta|termmeta)\s*=/.test( excerpt );
	}

	function containsAuthorizationValue( value, authorizationValues ) {
		return authorizationValues.some( authorizationValue => authorizationValue && value.includes( authorizationValue ) );
	}

	function escapeRegExp( value ) {
		return String( value ).replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );
	}

	function getRedactionOrigins( document, pageOrigin ) {
		const origins = [];

		for ( const value of [ pageOrigin, document.location && document.location.href ] ) {
			try {
				const origin = new URL( value ).origin;
				if ( origin && origin !== 'null' && ! origins.includes( origin ) ) origins.push( origin );
			} catch ( error ) {
				continue;
			}
		}

		return origins;
	}

	function sanitizeExcerpt( excerpt, pageOrigins ) {
		let sanitized = String( excerpt );

		for ( const pageOrigin of Array.isArray( pageOrigins ) ? pageOrigins : [ pageOrigins ] ) {
			let origin;
			try {
				origin = new URL( pageOrigin );
			} catch ( error ) {
				continue;
			}

			if ( ! origin.hostname ) continue;

			const hostname = escapeRegExp( origin.hostname );

			sanitized = sanitized.replace(
				new RegExp( 'https?:\\/\\/' + hostname + '(?::\\d+)?(?![a-z0-9.-])', 'gi' ),
				'https://site.invalid'
			);
			sanitized = sanitized.replace(
				new RegExp( '(^|[^a-z0-9.:-])\\/\\/' + hostname + '(?![a-z0-9.-])', 'gi' ),
				'$1//site.invalid'
			);
			sanitized = sanitized.replace(
				new RegExp( '(^|[^a-z0-9.-])' + hostname + '(?![a-z0-9.-])', 'gi' ),
				'$1site.invalid'
			);
		}

		return sanitized;
	}

	function getConsentString( element ) {
		const categories = [
			[ 'data-stats', 'stats' ],
			[ 'data-market', 'marketing' ],
			[ 'data-pers', 'personalisation' ],
		];
		const enabled = categories
			.filter( ( [ attribute ] ) => getAttribute( element, attribute ) === '1' )
			.map( ( [ , name ] ) => name );

		return enabled.length ? enabled.join( ' ' ) : null;
	}

	function getRelTokens( element ) {
		return String( getAttribute( element, 'rel' ) || '' ).toLowerCase().split( /\s+/ ).filter( Boolean );
	}

	function isNonFetchingLink( element ) {
		const rels = getRelTokens( element );
		return rels.length > 0 && rels.every( rel => NON_FETCHING_LINK_RELS.includes( rel ) );
	}

	function isNonExecutableScript( element ) {
		const type = String( getAttribute( element, 'type' ) || '' ).toLowerCase().split( ';', 1 )[0].trim();
		return NON_EXECUTABLE_SCRIPT_TYPES.includes( type );
	}

	function getUrlParts( urlValue ) {
		try {
			return new URL( urlValue );
		} catch ( error ) {
			return null;
		}
	}

	function hasOnlyVersionQuery( url ) {
		return Array.from( url.searchParams.keys() ).every( isVersionParameter );
	}

	function isVersionParameter( name ) {
		return VERSION_QUERY_PARAMETERS.includes( String( name ).toLowerCase() );
	}

	function isIdParameter( name ) {
		const normalized = String( name ).toLowerCase();
		return normalized === 'id' || /[_-]id$/.test( normalized );
	}

	function isSensitiveIdParameter( name ) {
		const normalized = String( name ).toLowerCase().replace( /[_-]/g, '' );
		return SENSITIVE_ID_TERMS.some( term => normalized.includes( term ) );
	}

	function shouldPreserveQueryValue( name, sanitizeVersionIds ) {
		if ( sanitizeVersionIds ) return false;
		return isVersionParameter( name ) || ( isIdParameter( name ) && ! isSensitiveIdParameter( name ) );
	}

	function shouldSanitizeVersionIds( result ) {
		return ! ( result && result.options && result.options.sanitize_version_ids === false );
	}

	function isLikelySafeStaticAsset( element, tagName, urlValue ) {
		const url = getUrlParts( urlValue );
		if ( ! url || ! hasOnlyVersionQuery( url ) ) return false;

		if ( tagName === 'img' ) return IMAGE_FILE_PATTERN.test( url.pathname );
		if ( tagName !== 'link' ) return false;

		const rels = getRelTokens( element );
		if ( rels.includes( 'stylesheet' ) ) return STYLESHEET_FILE_PATTERN.test( url.pathname );
		if ( rels.some( rel => [ 'icon', 'apple-touch-icon', 'mask-icon' ].includes( rel ) ) ) return IMAGE_FILE_PATTERN.test( url.pathname );
		if ( ! rels.includes( 'preload' ) ) return false;

		const resourceType = String( getAttribute( element, 'as' ) || '' ).toLowerCase();
		if ( resourceType === 'font' ) return FONT_FILE_PATTERN.test( url.pathname );
		if ( resourceType === 'image' ) return IMAGE_FILE_PATTERN.test( url.pathname );
		if ( resourceType === 'style' ) return STYLESHEET_FILE_PATTERN.test( url.pathname );
		return false;
	}

	function getSafeReason( element, tagName, urlValue, prefixes, pageOrigin ) {
		if ( ! isSameOrigin( urlValue, pageOrigin ) || ! isLikelySafeStaticAsset( element, tagName, urlValue ) ) return null;

		for ( const prefix of prefixes ) {
			if ( urlValue.startsWith( prefix.url ) ) {
				const nextCharacter = urlValue[prefix.url.length];
				if ( ! nextCharacter || prefix.url.endsWith( '/' ) || [ '/', '?', '#' ].includes( nextCharacter ) ) {
					return PREFIX_REASONS[prefix.kind];
				}
			}
		}

		if ( tagName === 'link' ) {
			const rels = getRelTokens( element );
			if ( rels.some( rel => [ 'icon', 'apple-touch-icon', 'mask-icon' ].includes( rel ) ) ) return 'Likely-safe same-origin site icon';
			if ( rels.includes( 'preload' ) && String( getAttribute( element, 'as' ) || '' ).toLowerCase() === 'font' ) return 'Likely-safe same-origin font';
		}
		return null;
	}

	function createUrlRecord( elementType, ruleType, url, pageOrigin, plugins ) {
		const plugin = getProbablePlugin( url, plugins );
		return {
			element_type: elementType,
			rule_type: ruleType,
			url,
			excerpt: null,
			same_origin: isSameOrigin( url, pageOrigin ),
			probable_plugin: plugin ? plugin.name : null,
			probable_plugin_folder: plugin ? plugin.folder : null,
			attribution: plugin ? 'probable' : null,
		};
	}

	function createInlineRecord( excerpt ) {
		return {
			element_type: 'script',
			rule_type: RULE_TYPES.INLINE_SCRIPT,
			url: null,
			excerpt,
			same_origin: null,
			probable_plugin: null,
			probable_plugin_folder: null,
			attribution: null,
		};
	}

	function getIframePlaceholderUrl( iframeData, baseUrl, authorizationValues ) {
		for ( const attribute of [ 'src', 'data-src' ] ) {
			const rawUrl = getAttribute( iframeData, attribute );
			if ( ! rawUrl || containsAuthorizationValue( rawUrl, authorizationValues ) ) continue;

			const url = normalizeUrl( rawUrl, baseUrl );
			if ( url && /^https?:\/\//i.test( url ) ) return url;
		}

		return null;
	}

	function createIframePlaceholderRecord( element, baseUrl, pageOrigin, authorizationValues ) {
		if ( typeof element.querySelector !== 'function' ) return null;

		const iframeData = element.querySelector( '.' + IFRAME_DATA_CLASS );
		if ( ! iframeData ) return null;

		const url = getIframePlaceholderUrl( iframeData, baseUrl, authorizationValues );
		if ( ! url ) return null;

		return {
			status: 'already_blocked',
			element_type: 'iframe',
			blocker_id: IFRAME_PLACEHOLDER_ID,
			url,
			excerpt: null,
			consent: getConsentString( element ),
			geography: null,
			force_load: null,
			same_origin: isSameOrigin( url, pageOrigin ),
		};
	}

	function getIframeEvidence( element, document ) {
		let rect = null,
			styles = null;

		if ( typeof element.getBoundingClientRect === 'function' ) {
			try {
				rect = element.getBoundingClientRect();
			} catch ( error ) {
				rect = null;
			}
		}

		if ( document.defaultView && typeof document.defaultView.getComputedStyle === 'function' ) {
			try {
				styles = document.defaultView.getComputedStyle( element );
			} catch ( error ) {
				styles = null;
			}
		}

		function dimension( value ) {
			value = Number( value );
			return Number.isFinite( value ) && value >= 0 ? Math.round( value * 100 ) / 100 : null;
		}

		const title = String( getAttribute( element, 'title' ) || '' ).trim();
		return {
			rendered_width: rect ? dimension( rect.width ) : null,
			rendered_height: rect ? dimension( rect.height ) : null,
			width_attribute: getAttribute( element, 'width' ),
			height_attribute: getAttribute( element, 'height' ),
			title: title ? title.slice( 0, IFRAME_TITLE_LENGTH ) : null,
			hidden: Boolean( getAttribute( element, 'hidden' ) !== null || styles && ( styles.display === 'none' || [ 'hidden', 'collapse' ].includes( styles.visibility ) ) ),
			css_display: styles && styles.display || null,
			css_visibility: styles && styles.visibility || null,
			loading: getAttribute( element, 'loading' ),
		};
	}

	function createBlockedRecord( element, baseUrl, pageOrigin, authorizationValues, redactionOrigins ) {
		const rawUrl = getAttribute( element, 'data-fupi_url' ) || '';
		const rawExcerpt = String( element.textContent || '' );
		const representedUrl = containsAuthorizationValue( rawUrl, authorizationValues ) ? null : normalizeUrl( rawUrl, baseUrl );
		const excerpt = containsAuthorizationValue( rawExcerpt, authorizationValues ) ? '' : sanitizeExcerpt( rawExcerpt, redactionOrigins ).slice( 0, EXCERPT_LENGTH );
		const forceLoad = getAttribute( element, 'data-fupi_force' );

		return {
			status: 'already_blocked',
			element_type: getAttribute( element, 'data-fupi_type' ) || null,
			blocker_id: getAttribute( element, 'data-fupi_id' ) || null,
			url: representedUrl,
			excerpt: excerpt || null,
			consent: getAttribute( element, 'data-fupi_permiss' ) || null,
			geography: getAttribute( element, 'data-fupi_geo' ) || null,
			force_load: forceLoad === null ? null : forceLoad === '1',
			same_origin: representedUrl ? isSameOrigin( representedUrl, pageOrigin ) : null,
		};
	}

	function getPage( document, metadata, options, baseUrl ) {
		let url;
		try {
			url = new URL( baseUrl );
		} catch ( error ) {
			url = { origin: '', pathname: '' };
		}

		return {
			origin: url.origin,
			path: url.pathname,
			scanned_at: options.scannedAt || new Date().toISOString(),
			wp_full_picture_version: metadata.full_picture_version || metadata.wp_full_picture_version || metadata.version || null,
		};
	}

	function scan( document, metadata = {}, options = {} ) {
		if ( ! document || typeof document.querySelectorAll !== 'function' ) {
			throw new TypeError( 'FupiHtmlScanner.scan requires a document.' );
		}

		const baseUrl = getBaseUrl( document );
		const page = getPage( document, metadata, options, baseUrl );
		const redactionOrigins = getRedactionOrigins( document, page.origin );
		const plugins = normalizePlugins( metadata );
		const prefixes = normalizePrefixes( metadata, baseUrl );
		const selfPrefixes = ( metadata.url_prefixes && metadata.url_prefixes.full_picture || [] )
			.map( value => normalizeUrl( value, baseUrl ) )
			.filter( Boolean );
		const ignoreLikelySafe = options.ignoreLikelySafe !== false && options.ignore_likely_safe !== false;
		const sanitizeVersionIds = options.sanitizeVersionIds !== false && options.sanitize_version_ids !== false;
		const authorizationValues = Array.isArray( options.authorizationValues ) ? options.authorizationValues.filter( value => typeof value === 'string' && value ) : [];
		const candidates = [];
		const alreadyBlocked = [];
		const ignored = [];
		const dedupe = new Map();

		function addCandidate( record ) {
			const key = `${ record.rule_type }\n${ record.url || record.excerpt }`;
			if ( dedupe.has( key ) ) return;

			dedupe.set( key, record );
			candidates.push( record );
		}

		for ( const element of document.querySelectorAll( 'script, link[href], img[src], iframe[src], .fupi_blocked_iframe' ) ) {
			const tagName = String( element.tagName || '' ).toLowerCase();
			const placeholderClass = getAttribute( element, 'data-fupi_class' ) || '';
			const elementId = getAttribute( element, 'id' );

			if (
				getAttribute( element, 'data-fp-runtime' ) !== null ||
				SELF_INLINE_IDS.includes( elementId ) ||
				typeof element.closest === 'function' && element.closest( '#fupi_console_wrap' )
			) continue;

			if ( hasClass( element, IFRAME_PLACEHOLDER_CLASS ) ) {
				const iframePlaceholder = createIframePlaceholderRecord( element, baseUrl, page.origin, authorizationValues );
				if ( iframePlaceholder ) alreadyBlocked.push( iframePlaceholder );
				continue;
			}

			if ( tagName === 'script' && placeholderClass.includes( PLACEHOLDER_CLASS ) ) {
				alreadyBlocked.push( createBlockedRecord( element, baseUrl, page.origin, authorizationValues, redactionOrigins ) );
				continue;
			}
			if ( tagName === 'link' && isNonFetchingLink( element ) ) continue;
			if ( tagName === 'script' && isNonExecutableScript( element ) ) continue;

			let record;
			if ( tagName === 'script' && getAttribute( element, 'src' ) === null ) {
				const inlineContent = String( element.textContent || '' );
				const excerpt = sanitizeExcerpt( inlineContent, redactionOrigins ).slice( 0, EXCERPT_LENGTH );
				if ( ! excerpt || isSelfInlineScript( excerpt ) || containsAuthorizationValue( inlineContent, authorizationValues ) ) continue;
				record = createInlineRecord( excerpt );
			} else {
				const definitions = {
					iframe: [ 'src', RULE_TYPES.IFRAME ],
					img: [ 'src', RULE_TYPES.IMAGE ],
					link: [ 'href', RULE_TYPES.LINK ],
					script: [ 'src', RULE_TYPES.SCRIPT ],
				};
				const definition = definitions[tagName];
				if ( ! definition ) continue;
				if ( tagName === 'iframe' && typeof element.closest === 'function' && element.closest( '.' + IFRAME_PLACEHOLDER_CLASS ) ) continue;
				const url = normalizeUrl( getAttribute( element, definition[0] ), baseUrl );
				if ( ! url || ! /^https?:\/\//i.test( url ) || isSelfUrl( url, selfPrefixes ) || ( tagName === 'script' && isConsentModeTagLoader( element, url ) ) || containsAuthorizationValue( url, authorizationValues ) ) continue;
				record = createUrlRecord( tagName, definition[1], url, page.origin, plugins );
				if ( tagName === 'iframe' ) record.iframe = getIframeEvidence( element, document );

				const reason = ignoreLikelySafe && getSafeReason( element, tagName, url, prefixes, page.origin );
				if ( reason ) {
					record.status = 'ignored';
					record.ignored_reason = reason;
					ignored.push( record );
					continue;
				}
			}

			addCandidate( record );
		}

		return {
			page,
			options: { ignore_likely_safe: ignoreLikelySafe, sanitize_version_ids: sanitizeVersionIds },
			candidates,
			already_blocked: alreadyBlocked,
			ignored,
		};
	}

	function formatPage( page ) {
		return `${ page.origin || '(unknown origin)' }${ page.path || '/' } at ${ page.scanned_at }`;
	}

	function sanitizePage( page ) {
		return { ...page, origin: page.origin ? 'https://site.invalid' : page.origin };
	}

	function sanitizeReportUrl( urlValue, pageOrigin, sanitizeVersionIds = true ) {
		if ( ! urlValue ) return null;

		try {
			const url = new URL( urlValue );
			const parameters = Array.from( url.searchParams.entries() );

			url.hash = '';
			url.search = '';
			parameters.forEach( ( [ name, value ] ) => {
				url.searchParams.append( name, shouldPreserveQueryValue( name, sanitizeVersionIds ) ? value : 'xxx' );
			} );

			if ( url.origin && url.origin !== 'null' && url.origin === pageOrigin ) {
				if ( [ 'http:', 'https:' ].includes( url.protocol ) ) {
					url.protocol = 'https:';
					url.hostname = 'site.invalid';
					url.port = '';
				} else {
					return url.href.split( url.origin ).join( 'https://site.invalid' );
				}
			}

			return url.href;
		} catch ( error ) {
			return null;
		}
	}

	function buildCandidatesReport( result ) {
		const sourcePage = result && result.page || {};
		const page = sanitizePage( sourcePage );
		const sanitizeVersionIds = shouldSanitizeVersionIds( result );
		const candidates = result && Array.isArray( result.candidates ) ? result.candidates.map( candidate => {
			const reportCandidate = { ...candidate };
			delete reportCandidate.attribution;
			delete reportCandidate.probable_plugin_folder;
			reportCandidate.url = sanitizeReportUrl( reportCandidate.url, sourcePage.origin, sanitizeVersionIds );
			return reportCandidate;
		} ) : [];

		return JSON.stringify( {
			page,
			options: result && result.options || {},
			candidates,
		}, null, 2 );
	}

	function buildBlockedReport( result ) {
		const blocked = result && Array.isArray( result.already_blocked ) ? result.already_blocked : [];
		const sourcePage = result && result.page || {};
		const page = sanitizePage( sourcePage );
		const sanitizeVersionIds = shouldSanitizeVersionIds( result );
		const lines = [
			'WP Full Picture: blocked elements observed on the current page',
			'',
			`Page and scan time: ${ formatPage( page ) }`,
			`Sanitization: ${ sanitizeVersionIds ? 'all query values redacted' : 'versions and non-sensitive IDs preserved' }`,
			'This is page-level evidence from the scan time, not the site\'s blocking configuration or a complete rule audit.',
			'Elements removed by always_block rules cannot appear in the live DOM, are not included, and are not reconstructed from server settings.',
			'Only WP Full Picture placeholders successfully observed on this current page are listed.',
			'',
			`Already blocked (${ blocked.length }):`,
		];

		if ( ! blocked.length ) lines.push( 'None observed.' );
		blocked.forEach( ( item, index ) => {
			const forceLoad = item.force_load === null ? 'unknown' : item.force_load ? 'yes' : 'no';
			const url = sanitizeReportUrl( item.url, sourcePage.origin, sanitizeVersionIds );
			lines.push( `${ index + 1 }. Element type: ${ item.element_type || 'unknown' }; blocker identifier: ${ item.blocker_id || 'unknown' }; URL: ${ url || 'none' }; consent: ${ item.consent || 'none' }; geography: ${ item.geography || 'none' }; force-load: ${ forceLoad }; inline excerpt: ${ item.excerpt || 'none' }` );
		} );

		return lines.join( '\n' );
	}

	window.FupiHtmlScanner = Object.freeze( {
		scan,
		buildCandidatesReport,
		buildBlockedReport,
	} );
}( window ) );
