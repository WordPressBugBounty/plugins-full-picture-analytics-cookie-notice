function fupi_core(){

    'use strict';

    var d = document;

    FP.v = {};
    FP.actionChecks = {};
    fp.observers = {};
    fpdata.activity = { total : 0, last : 0, current : 0 };
    fpdata.refreshed = false;

    if ( document.referrer == document.location.origin + document.location.pathname ) {
        fpdata.refreshed = true;
    } else {
        if ( performance.navigation ? performance.navigation.type === performance.navigation.TYPE_RELOAD : performance.getEntriesByType('navigation')[0].type === 'reload' ) fpdata.refreshed = true; // deprecated method first
    }

    FP.getFormattedDateTime = () =>{
        const d = new Date();
        
        const year = d.getFullYear();
        const month = d.getMonth() + 1;
        const day = d.getDate();
        const hours = d.getHours() * 100;
        const minutes = d.getMinutes();
        
        return {
            year: year,
            month: month,
            day: day,
            time: hours + minutes
        };
    }

    fpdata.date = FP.getFormattedDateTime();

    FP.getInner = function (vals, splitter = ".") {

        let args = Array.isArray(vals) ? vals : vals.split(splitter).map( arg => arg.trim() ),
            obj = window[args.shift()];

        for (var i = 0; i < args.length; i++) {
            var prop = args[i];
            if (! obj || ! Object.prototype.hasOwnProperty.call(obj, prop)) return undefined;
            obj = obj[prop];
        };

        return obj;
    };

    FP.isObject = val => {
        return typeof val == 'object' && val !== null && ! Array.isArray( val );
    }

    FP.setCookie = (name, value, days = 0, mins = 0, path = "/") => {
        const encodingPrefix = "__fp1__";
        let expires = "";

        if (days < 0) {
            expires = "; Max-Age=0; Expires=Thu, 01 Jan 1970 00:00:00 GMT";
        } else if (days > 0 || mins > 0) {
            const milliseconds = days > 0
                ? days * 24 * 60 * 60 * 1000
                : mins * 60 * 1000;

            expires = `; Expires=${new Date(Date.now() + milliseconds).toUTCString()}`;
        }

        document.cookie =
            `${name}=${encodingPrefix}${encodeURIComponent(String(value))}` +
            `${expires}; Path=${path}; SameSite=Lax` +
            (location.protocol === "https:" ? "; Secure" : "");
    };

    FP.readCookie = name => {
        try {
            const prefix = `${name}=`;
            const encodingPrefix = "__fp1__";

            const cookie = document.cookie
                .split(";")
                .map(item => item.trim())
                .find(item => item.startsWith(prefix));

            if (!cookie) {
                return null;
            }

            const value = cookie.slice(prefix.length);

            // Decode only cookies written by the new implementation.
            if ( value.startsWith(encodingPrefix) ) {
                return decodeURIComponent(value.slice(encodingPrefix.length));
            }

            // Preserve legacy cookies exactly as they were stored.
            return value;
        } catch {
            return null;
        }
    };

    FP.deleteCookie = (name, path = "/") => {
        FP.setCookie(name, "", -1, 0, path);
    };

    FP.resetTrackingState = () => {
        [
            'fp_tracking_disabled',
            'fp_cookie',
            'fp_country',
            'fp_current_session',
            'fp_recent_sessions',
        ].forEach( FP.deleteCookie );
    };

    FP.parseJSON = ( value, fallback = null ) => {
        if ( typeof value !== 'string' ) return fallback;

        try {
            return JSON.parse( value );
        } catch ( error ) {
            return fallback;
        }
    };

    FP.getUrlParamByName = ( name, url = false ) => {
        if ( ! url ) {
            url = window.location.search;
        } else {
            url = url.split('?')[1];
            if ( ! url ) return null;
        };
        
        const queryString = url,
            urlParams = new URLSearchParams(queryString);
        return urlParams.get(name);
    };

    FP.nl2Arr = nl => nl ? [].slice.call(nl) : false;

    FP.findID = (e, c) => {
        if ( c === null ) return null;
        if ( !e ) return false;
        c = c || document;
        return c.getElementById(e);
    };

    FP.findFirst = (e, c) => {
        if ( c === null ) return null;
        if ( !e ) return false;
        c = c || document;
        return c.querySelector(e);
    };

    FP.findAll = (e, c) => {
        if ( c === null ) return [];
        if ( ! e ) return false;
        c = c || document;
        return FP.nl2Arr(c.querySelectorAll(e));
    };

    FP.sendEvt = (evt_name, details_a) => {
        var details = details_a ? { 'detail' : details_a } : {},
            fp_event = new CustomEvent( evt_name, details );
        document.dispatchEvent(fp_event);
    };

    FP.getRandomStr = ()=>{
        return ( Math.random() + 1 ).toString(36).substring(2);
    };

    fp.random = FP.getRandomStr();

    FP.postToServer = ( event_data_a, options ) => {

        let callback = typeof options.callback == 'function' ? options.callback : false,
            keepalive = !! options.keepalive;

        if ( fpdata.is_robot ) {
            return Promise.resolve({
                success: false,
                skipped: true,
                retryable: false,
                error: 'robot',
            });
        }

        if ( fp.main.debug ) console.log( "[FP] Posting to server", event_data_a );

        let use_rest = fp.main.server_method == "rest",
            fetch_url = use_rest ? fp.main.rest_url : fp.main.ajax_url;

        // Keep fallbacks for cached pages generated by versions before the URLs
        // were added to the public configuration.
        if ( ! fetch_url ) {
            fetch_url = use_rest ? "/index.php?rest_route=/fupi/v1/sender" : "/wp-admin/admin-ajax.php";
        }

        if ( ! use_rest ) {
            fetch_url += ( fetch_url.includes('?') ? '&' : '?' ) + 'action=fupi_ajax';
        }

        let fetch_options = {
            method: "POST",
            credentials: 'same-origin',
            keepalive: keepalive,
            body: JSON.stringify( event_data_a ),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        };

        if ( use_rest && fp.main.rest_nonce ) {
            fetch_options.headers['X-WP-Nonce'] = fp.main.rest_nonce;
        }

        let request_promise = fetch( fetch_url, fetch_options )
            .then( async response => {
                let json;

                let refreshed_nonce = use_rest && response.headers ? response.headers.get('X-WP-Nonce') : false;
                if ( refreshed_nonce ) fp.main.rest_nonce = refreshed_nonce;

                try {
                    json = await response.json();
                } catch ( error ) {
                    return {
                        success: false,
                        retryable: response.status >= 500,
                        status: response.status,
                        error: 'invalid_json_response',
                    };
                }

                if ( ! response.ok ) {
                    if ( typeof json != 'object' || json === null ) json = {};

                    return {
                        ...json,
                        success: false,
                        retryable: typeof json.retryable == 'boolean' ? json.retryable : response.status >= 500,
                        status: response.status,
                    };
                }

                return json;
            })
            .catch( error => {
                return {
                    success: false,
                    retryable: true,
                    status: 0,
                    error: error.message,
                };
            });

        return request_promise.then( result => {
            if ( callback ) {
                try {
                    callback( result );
                } catch ( error ) {
                    if ( fp.main.debug ) console.error('[FP] Server callback failed:', error);
                }
            } else if ( fp.main.debug ) {
                result.success === false
                    ? console.error( '[FP] Server request failed:', result )
                    : console.log( "[FP] Server response", result );
            }

            return result;
        });
    };

    // fires function every X ms with an option to fire them ADDITIONALLY on start ("leading") and after events ("trailing")
    FP.throttle = (func, wait, options) => {
        var context, args, result;
        var timeout = null;
        var previous = 0;
        if (!options) options = {};

        var later = function later() {
            previous = options.leading === false ? 0 : Date.now();
            timeout = null;
            result = func.apply(context, args);
            if (!timeout) context = args = null;
        };

        return function () {
            var now = Date.now();
            if (!previous && options.leading === false) previous = now;
            var remaining = wait - (now - previous);
            context = this;
            args = arguments;

            if (remaining <= 0 || remaining > wait) {
                if (timeout) {
                    clearTimeout(timeout);
                    timeout = null;
                }

                previous = now;
                result = func.apply(context, args);
                if (!timeout) context = args = null;
            } else if (!timeout && options.trailing !== false) {
                timeout = setTimeout(later, remaining);
            }

            return result;
        };
    };

    FP.remove = el => { if (el) el.parentNode.removeChild(el); };

    FP.addEvtParams = ( obj, params ) => {
        if ( params && params.length > 0 ) {
            params.forEach( par => {
                if ( par.name && par.type ) {
                    switch ( par.type ) {
                        case 'string':
                            obj[par.name] = par.val;
                        break;
                        case 'number':
                            obj[par.name] = parseFloat( par.val );
                        break;
                        case 'bool':
                            if ( ! isNaN(par.val) ) {
                                obj[par.name] = parseFloat(par.val);
                            } else {
                                obj[par.name] = par.val.toLowerCase() == 'true';
                            }
                        break;
                        case 'path':{
                            let temp_val = FP.getInner( par.val + '', '>' );
                            if ( typeof temp_val !== 'undefined' ) obj[par.name] = temp_val;
                            }
                        break;
                    }
                }
            } )
        }

        return obj;
    }

    FP.runAction = ( action_name, fn, args_o = null, pass_args = false ) => {

        try {
            pass_args ? fn( args_o ) : fn();
        } catch ( error ) {
            console.error( '[FP] Action "' + action_name + '" failed.', error );
        }
    }

    FP.addAction = ( action_name, fn ) => {

        let arr = Array.isArray(action_name) ? action_name : [action_name]; // make sure that trigger is in an array

        arr.forEach( name => {
            if ( ! fp.events[name] ) fp.events[name] = [];
            fp.events[name].push( fn );
        });
    }

    FP.doWhenDOMLoaded = fn => {
        if ( document.readyState === "interactive" || document.readyState === "complete" ) {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn );
        }
    }

    FP.hasActions = action_name => {
        return fp.events[action_name] && fp.events[action_name].length > 0;
    };

    FP.doActions = ( action_name, args_o = null, cb ) => {
        
        let actions = Array.isArray(action_name) ? action_name : [action_name];

        actions.forEach( name => {

            let test = FP.actionChecks[name] ? FP.actionChecks[name]() : true;

            if ( test && fp.events[name] ) {
                fp.events[name].forEach( fn => {
                    FP.runAction( name, fn, args_o, !! args_o );
                } );
            }
        });
        
        if ( cb ) cb( args_o ); // always triggered, even if there are no actions
    }

    FP.addTriggerActions = ( trigger_type, events_settings, cb ) => {

        if ( ! fp.atrig ) return;
                    
        let triggers_o = fp.atrig[trigger_type];

        if ( ! triggers_o ) return;

        events_settings.forEach( evt_data => {

            let trigger = triggers_o[evt_data.atrig_id];

            if ( trigger ) {
                
                let action = [
                    cb.bind( this, evt_data ),
                    evt_data.repeat  || 'no'
                ];

                trigger.actions = trigger.actions || [];
                trigger.actions.push( action );

                if ( trigger.events ) { // only custom triggers have "events"
    
                    if ( 
                        trigger.events.includes( 'dom_loaded' ) && 
                        fp.vars.dom_loaded && 
                        typeof FP.runCustomTrigger == 'function'
                    ) {
                        FP.runCustomTrigger( trigger, [action] );
                    }
                }
            };
        });
    }

    FP.doTriggerActions = ( trigger_types )=>{

        let trigger_types_a = Array.isArray(trigger_types) ? trigger_types : [trigger_types];

        trigger_types_a.forEach( trigger_type => {

            let triggers_o = fp.atrig[trigger_type];

            if ( triggers_o ) for ( const [trigger_id, trigger_data] of Object.entries(triggers_o) ) {

                // STOP if the trigger is not used
                let hooked_actions = trigger_data?.actions;

                if ( ! hooked_actions || hooked_actions.length == 0 ) continue;

                // TEST
                let ret_val = FP.actionChecks[trigger_type] && FP.actionChecks[trigger_type](trigger_data);
                
                if ( ! ret_val ) continue;

                // if we are debugging the trigger
                if ( trigger_data.debug ) console.log( '[FP] Trigger fired: ', trigger_data );

                // trigger all actions
                hooked_actions.forEach( action => {
                    if ( action[1] != 'stop' ){
                        let cb = action[0];
                        FP.runAction( trigger_type, cb, ret_val, true ); // !! passes "evt_data" (from addTriggerActions) as the first argument !!
                        if ( action[1] == 'no' ) action[1] = 'stop';
                    }
                } )
            }
        } );
    }

    FP.startActivityTimer = () => {

        fpdata.activity.last = 0;

        if (!FP.v.activityTimerRunning) {
            FP.v.activityTimerRunning = true;
            FP.v.activityTimer = setInterval(function () {

                fpdata.activity.total++;
                fpdata.activity.current++;
                fpdata.activity.last++;

                FP.doActions( ['active_time_tick', 'scroll_after_wait'] );

                if ( fpdata.activity.last >= 15 ) FP.stopActivityTimer(); // stop timer after 15 secs from last activity
            }, 1000);
        }
    };

    FP.stopActivityTimer = () => {
        if (FP.v.activityTimerRunning) {
            FP.v.activityTimerRunning = false;
            clearInterval(FP.v.activityTimer);
        }
    };

    FP.encode = (string) => {
        return window.btoa(string).split('=').join('')
    }

    // ! The "get[x]name" fns may return empty strings or false (possible in old FP)
    
    FP.getFormName = evt => {
        return evt.val == '_fallback' ? location.host + location.pathname : evt.val;
    }

    FP.getAffName = evt  => {
        if ( evt.val == '_fallback' ) {
            return fpdata.clicked.link.href;
        } else {
            return FP.makeName( evt.val, fpdata.clicked.link.element );
        }
    }

    FP.getClickName = evt => {
        return FP.makeName( evt.val, fpdata.clicked.element );
    }

    FP.makeName = ( str, el ) => {
        if ( str ) {
            if ( str.indexOf('[name]') != -1  ) {
				var textContent = el.textContent;
				if ( textContent.length > 20 ) {
					textContent = textContent.substring(0, 20);
				};
				return str.replace('[name]', textContent);
			} else {
				return str;
			}
		} else {
			return false;
		}
	}

    

    // -- modified https://stackoverflow.com/a/65563155/7702522
    fpdata.is_robot = (() => {
        
        // SMALL list
        if ( fp.main.bot_list == "basic" ) {
            
            const robots = new RegExp([/bot/,/spider/,/crawl/,/APIs-Google/,/AdsBot/,/Googlebot/,/mediapartners/,/Google Favicon/,/FeedFetcher/,/Google-Read-Aloud/,/googleweblight/,/bingbot/,/yandex/,/baidu/,/duckduck/,/Yahoo Link Preview/,/ia_archiver/,/facebookexternalhit/,/pinterest\.combot/,/redditbot/,/slackbot/,/Twitterbot/,/WhatsApp/,/S[eE][mM]rushBot/].map((r) => r.source).join("|"),"i");

            return robots.test(navigator.userAgent);

        // BIG list
        } else if ( fp.main.bot_list == "big" ) {

            const robots = new RegExp([
                /Googlebot/, /AdsBot/, /Feedfetcher-Google/, /Mediapartners-Google/, /Mediapartners/, /APIs-Google/, 
                /Google-InspectionTool/, /Storebot-Google/, /GoogleOther/, /bingbot/, /Slurp/, /wget/, /LinkedInBot/, 
                /Python-urllib/, /python-requests/, /aiohttp/, /httpx/, /libwww-perl/, /httpunit/, /Nutch/, 
                /Go-http-client/, /phpcrawl/, /msnbot/, /jyxobot/, /FAST-WebCrawler/, /FAST Enterprise Crawler/, 
                /BIGLOTRON/, /Teoma/, /convera/, /seekbot/, /Gigabot/, /Gigablast/, /exabot/, /ia_archiver/, 
                /GingerCrawler/, /webmon/, /HTTrack/, /grub\.org/, /UsineNouvelleCrawler/, /antibot/, 
                /netresearchserver/, /speedy/, /fluffy/, /findlink/, /msrbot/, /panscient/, /yacybot/, /AISearchBot/, 
                /ips-agent/, /tagoobot/, /MJ12bot/, /woriobot/, /yanga/, /buzzbot/, /mlbot/, /yandex\.combots/, 
                /purebot/, /Linguee Bot/, /CyberPatrol/, /voilabot/, /Baiduspider/, /citeseerxbot/, /spbot/, 
                /twengabot/, /postrank/, /Turnitin/, /scribdbot/, /page2rss/, /sitebot/, /linkdex/, /Adidxbot/, 
                /ezooms/, /dotbot/, /Mail\.RU_Bot/, /discobot/, /heritrix/, /findthatfile/, /europarchive\.org/, 
                /NerdByNature\.Bot/, /sistrix crawler/, /Ahrefs/, /fuelbot/, /CrunchBot/, /IndeedBot/, 
                /mappydata/, /woobot/, /ZoominfoBot/, /PrivacyAwareBot/, /Multiviewbot/, /SWIMGBot/, /Grobbot/, 
                /eright/, /Apercite/, /semanticbot/, /Aboundex/, /domaincrawler/, /wbsearchbot/, /summify/, /CCBot/, 
                /edisterbot/, /SeznamBot/, /ec2linkfinder/, /gslfbot/, /aiHitBot/, /intelium_bot/, 
                /facebookexternalhit/, /Yeti/, /RetrevoPageAnalyzer/, /lb-spider/, /Sogou/, /lssbot/, /careerbot/, 
                /wotbox/, /wocbot/, /ichiro/, /DuckDuckBot/, /lssrocketcrawler/, /drupact/, /webcompanycrawler/, 
                /acoonbot/, /openindexspider/, /gnam gnam spider/, /web-archive-net\.com\.bot/, /backlinkcrawler/, 
                /coccoc/, /integromedb/, /content crawler spider/, /toplistbot/, /it2media-domain-crawler/, 
                /ip-web-crawler\.com/, /siteexplorer\.info/, /elisabot/, /proximic/, /changedetection/, /arabot/, 
                /WeSEE:Search/, /niki-bot/, /CrystalSemanticsBot/, /rogerbot/, /360Spider/, /psbot/, 
                /InterfaxScanBot/, /CC Metadata Scaper/, /g00g1e\.net/, /GrapeshotCrawler/, /urlappendbot/, 
                /brainobot/, /fr-crawler/, /binlar/, /SimpleCrawler/, /Twitterbot/, /cXensebot/, /smtbot/, 
                /bnf\.fr_bot/, /A6-Indexer/, /ADmantX/, /Facebot/, /OrangeBot/, /memorybot/, /AdvBot/, 
                /MegaIndex/, /SemanticScholarBot/, /ltx71/, /nerdybot/, /xovibot/, /BUbiNG/, /Qwantify/, 
                /archive\.org_bot/, /Applebot/, /TweetmemeBot/, /crawler4j/, /findxbot/, /SemrushBot/, 
                /yoozBot/, /lipperhey/, /Y!J/, /Domain Re-Animator Bot/, /AddThis/, /Screaming Frog SEO Spider/, 
                /MetaURI/, /Scrapy/, /Livelapbot/, /OpenHoseBot/, /CapsuleChecker/, /collection@infegy\.com/, 
                /IstellaBot/, /DeuSu/, /betaBot/, /Cliqzbot/, /MojeekBot/, /netEstate NE Crawler/, 
                /SafeSearch microdata crawler/, /Gluten Free Crawler/, /Sonic/, /Sysomos/, /Trove/, /deadlinkchecker/, 
                /Slack-ImgProxy/, /Embedly/, /RankActiveLinkBot/, /iskanie/, /SafeDNSBot/, /SkypeUriPreview/, 
                /Veoozbot/, /Slackbot/, /redditbot/, /datagnionbot/, /Google-Adwords-Instant/, /adbeat_bot/, 
                /WhatsApp/, /contxbot/, /pinterest\.combot/, /electricmonk/, /GarlikCrawler/, /BingPreview/, 
                /vebidoobot/, /FemtosearchBot/, /Yahoo Link Preview/, /MetaJobBot/, /DomainStatsBot/, /mindUpBot/, 
                /Daum/, /Jugendschutzprogramm-Crawler/, /Xenu Link Sleuth/, /Pcore-HTTP/, /moatbot/, /KosmioBot/, 
                /pingdom/, /AppInsights/, /PhantomJS/, /Gowikibot/, /PiplBot/, /Discordbot/, /TelegramBot/, 
                /Jetslide/, /newsharecounts/, /James BOT/, /Barkrowler/, /TinEye/, /SocialRankIOBot/, 
                /trendictionbot/, /Ocarinabot/, /epicbot/, /Primalbot/, /DuckDuckGo-Favicons-Bot/, /GnowitNewsbot/, 
                /Leikibot/, /LinkArchiver/, /YaK/, /PaperLiBot/, /Digg Deeper/, /dcrawl/, /Snacktory/, /AndersPinkBot/, 
                /Fyrebot/, /EveryoneSocialBot/, /Mediatoolkitbot/, /Luminator-robots/, /ExtLinksBot/, /SurveyBot/, 
                /NING/, /okhttp/, /Nuzzel/, /omgili/, /PocketParser/, /YisouSpider/, /um-LN/, /ToutiaoSpider/, 
                /MuckRack/, /Jamie's Spider/, /AHC/, /NetcraftSurveyAgent/, /Laserlikebot/, /^Apache-HttpClient/, 
                /AppEngine-Google/, /Jetty/, /Upflow/, /Thinklab/, /Traackr\.com/, /Twurly/, /Mastodon/, /http_get/, 
                /DnyzBot/, /botify/, /007ac9 Crawler/, /BehloolBot/, /BrandVerity/, /check_http/, /BDCbot/, 
                /ZumBot/, /EZID/, /ICC-Crawler/, /ArchiveBot/, /^LCC /, /filterdb\.iss\.netcrawler/, /BLP_bbot/, 
                /BomboraBot/, /Buck/, /Companybook-Crawler/, /Genieo/, /magpie-crawler/, /MeltwaterNews/,
                /Moreover/,/newspaper/,/ScoutJet/,/sentry/,/StorygizeBot/,/UptimeRobot/,/OutclicksBot/,
                /seoscanners/,/Hatena/,/Google Web Preview/,/MauiBot/,/AlphaBot/,/SBL-BOT/,/IAS crawler/,
                /adscanner/,/Netvibes/,/acapbot/,/Baidu-YunGuanCe/,/bitlybot/,/blogmuraBot/,/Bot\.AraTurka\.com/,
                /bot-pge\.chlooe\.com/,/BoxcarBot/,/BTWebClient/,/ContextAd Bot/,/Digincore bot/,/Disqus/,/Feedly/,
                /Fetch/,/Fever/,/Flamingo_SearchEngine/,/FlipboardProxy/,/g2reader-bot/,/G2 Web Services/,/imrbot/,
                /K7MLWCBot/,/Kemvibot/,/Landau-Media-Spider/,/linkapediabot/,/vkShare/,/Siteimprove\.com/,/BLEXBot/,
                /DareBoost/,/ZuperlistBot/,/Miniflux/,/Feedspot/,/Diffbot/,/SEOkicks/,/tracemyfile/,/Nimbostratus-Bot/,
                /zgrab/,/PR-CY\.RU/,/AdsTxtCrawler/,/Datafeedwatch/,/Zabbix/,/TangibleeBot/,/google-xrawler/,/axios/,
                /Amazon CloudFront/,/Pulsepoint/,/CloudFlare-AlwaysOnline/,/Google-Structured-Data-Testing-Tool/,
                /WordupInfoSearch/,/WebDataStats/,/HttpUrlConnection/,/Seekport Crawler/,/ZoomBot/,/VelenPublicWebCrawler/,
                /MoodleBot/,/jpg-newsbot/,/outbrain/,/W3C_Validator/,/Validator\.nu/,/W3C-checklink/,/W3C-mobileOK/,
                /W3C_I18n-Checker/,/FeedValidator/,/W3C_CSS_Validator/,/W3C_Unicorn/,/Google-PhysicalWeb/,/Blackboard/,
                /ICBot/,/BazQux/,/Twingly/,/Rivva/,/Experibot/,/awesomecrawler/,/Dataprovider\.com/,/GroupHigh/,
                /theoldreader\.com/,/AnyEvent/,/Uptimebot\.org/,/Nmap Scripting Engine/,/2ip\.ru/,/Clickagy/,
                /Caliperbot/,/MBCrawler/,/online-webceo-bot/,/B2B Bot/,/AddSearchBot/,/Google Favicon/,/HubSpot/,
                /Chrome-Lighthouse/,/HeadlessChrome/,/CheckMarkNetwork/,/www\.uptime\.com/,/Streamline3Bot/,/serpstatbot/,
                /MixnodeCache/,/^curl/,/SimpleScraper/,/RSSingBot/,/Jooblebot/,/fedoraplanet/,/Friendica/,/NextCloud/,
                /Tiny Tiny RSS/,/RegionStuttgartBot/,/Bytespider/,/Datanyze/,/Google-Site-Verification/,/TrendsmapResolver/,
                /tweetedtimes/,/NTENTbot/,/Gwene/,/SimplePie/,/SearchAtlas/,/Superfeedr/,/feedbot/,/UT-Dorkbot/,/Amazonbot/,
                /SerendeputyBot/,/Eyeotabot/,/officestorebot/,/Neticle Crawler/,/SurdotlyBot/,/LinkisBot/,/AwarioSmartBot/,
                /AwarioRssBot/,/RyteBot/,/FreeWebMonitoring SiteChecker/,/AspiegelBot/,/NAVER Blog Rssbot/,/zenback bot/,
                /SentiBot/,/Domains Project/,/Pandalytics/,/VKRobot/,/bidswitchbot/,/tigerbot/,/NIXStatsbot/,/Atom Feed Robot/,
                /curebot/,/PagePeeker/,/Vigil/,/rssbot/,/startmebot/,/JobboerseBot/,/seewithkids/,/NINJA bot/,/Cutbot/,
                /BublupBot/,/BrandONbot/,/RidderBot/,/Taboolabot/,/Dubbotbot/,/FindITAnswersbot/,/infoobot/,/Refindbot/,
                /BlogTraffic\d\.\d+ Feed-Fetcher/,/SeobilityBot/,/Cincraw/,/Dragonbot/,/VoluumDSP-content-bot/,/FreshRSS/,
                /BitBot/,/^PHP-Curl-Class/,/Google-Certificates-Bridge/,/centurybot/,/Viber/,/e\.ventures Investment Crawler/,
                /evc-batch/,/PetalBot/,/virustotal/,/(^| )PTST/,/minicrawler/,/Cookiebot/,/trovitBot/,/seostar\.co/,/IonCrawl/,
                /Uptime-Kuma/,/SeekportBot/,/FreshpingBot/,/Feedbin/,/CriteoBot/,/Snap URL Preview Service/,/Better Uptime Bot/,
                /RuxitSynthetic/,/Google-Read-Aloud/,/ValveSteam/,/OdklBot/,/GPTBot/,/ChatGPT-User/,/YandexRenderResourcesBot/,
                /LightspeedSystemsCrawler/,/ev-crawler/,/BitSightBot/,/woorankreview/,/Google-Safety/,/AwarioBot/,/DataForSeoBot/,
                /Linespider/,/WellKnownBot/,/A Patent Crawler/,/StractBot/,/search\.marginalia\.nu/,/YouBot/,/Nicecrawler/,/Neevabot/,
                /BrightEdge Crawler/,/SiteCheckerBotCrawler/,/TombaPublicWebCrawler/,/CrawlyProjectCrawler/,/KomodiaBot/,/KStandBot/,
                /CISPA Webcrawler/,/MTRobot/,/hyscore\.io/,/AlexandriaOrgBot/,/2ip bot/,/Yellowbrandprotectionbot/,/SEOlizer/,
                /vuhuvBot/,/INETDEX-BOT/,/Synapse/,/t3versionsBot/,/deepnoc/,/Cocolyzebot/,/hypestat/,/ReverseEngineeringBot/,
                /sempi\.tech/,/Iframely/,/MetaInspector/,/node-fetch/,/lkxscan/,/python-opengraph/,/OpenGraphCheck/,
                /developers\.google\.com\+websnippet/,/SenutoBot/,/MaCoCu/,/NewsBlur/,/inoreader/,/NetSystemsResearch/,/PageThing/,
                /WordPress/,/PhxBot/,/ImagesiftBot/,/Expanse/,/InternetMeasurement/,/^BW/,/GeedoBot/,/Audisto Crawler/,
                /PerplexityBot/,/claudebot/,/Monsidobot/,/GroupMeBot/].map((r) => r.source).join("|"),"i");

                return robots.test(navigator.userAgent);

        } else {
            return false;
        };
    })();

    FP.getConsentValues = ( cookies = {} ) => {

        let stats = !! cookies?.stats,
            pers = !! cookies?.personalisation,
            market = !! cookies?.marketing;

        let cons_vals = {
            "cookies" : {
                "stats" : stats,
                "personalisation" : pers,
                "marketing" : market,
            },
            "consents" : {
                "can_track_stats" : stats,
                "can_track_pers" : pers,
                "can_track_market" : market,
            },
            "gtag" : {
                "ad_storage": market ? "granted" : "denied",
                "ad_user_data" : market ? "granted" : "denied",
                "ad_personalization" : market ? "granted" : "denied",
                "analytics_storage": stats ? "granted" : "denied",
                "personalization_storage": pers ? "granted" : "denied",
                "functionality_storage": pers || stats || market ? "granted" : "denied",
                "security_storage": "granted",
            },
        };

        return cons_vals;
    };

    FP.updateConsents = ( cookies = {}, run_actions = false ) => {

        let cons_vals = FP.getConsentValues( cookies ),
            type = "update";
        
        fpdata.consents = cons_vals.consents;

        // GTAG
        ["gtag", "fupi_gtm_gtag"].forEach( tag_name => {

            if ( typeof window[tag_name] == "function" ) {

                window[tag_name]( "consent", type, cons_vals.gtag );

                if ( type == "update" ) {
                    if ( tag_name == "gtag" ) {
                        window.dataLayer.push( {
                            "event" : "fp_privacyPreferencesChanged",
                            "fp_visitorPrivacyPreferences" : cons_vals.cookies,
                        } );
                    } else {
                        window.fupi_dataLayer.push( {
                            "event" : "fp_privacyPreferencesChanged",
                            "fp_visitorPrivacyPreferences" : cons_vals.cookies,
                        } );
                    }
                }
            }
        });

        // MS ADS
        if ( window.uetq ) {
            window.uetq.push( "consent", type, {
                "ad_storage": cons_vals.gtag.ad_storage
            });
        }

        // Pixel Manager
        if ( fp.notice.scrblk_auto_rules && fp.notice.scrblk_auto_rules.includes("pixelman") ){
            window._pmwq = window._pmwq || [];
            window._pmwq.push(function() {
                if ( fpdata.cookies && !! window?.pmw?.consent?.api?.updateSelectively ) {
                    pmw.consent.api.updateSelectively({
                        statistics: cons_vals.cookies.stats,
                        marketing: cons_vals.cookies.marketing,
                        preferences: cons_vals.cookies.personalisation,
                        necessary: true,
                    });
                    console.log("[FP] Updated tracking consents for Pixel Manager");
                }
            });
        }

        if ( run_actions && typeof FP.doActions == "function" ) {
            FP.doActions(["privacy_consents_changed"], cons_vals);
        }

        if ( fp.main.debug ) console.log("[FP] Consents updated", cons_vals.consents);

        return cons_vals;
    };
    
    FP.updatePrivacyChoices = ( update_pageviews_count = true, run_actions = true )=>{

        let cookies = {},
            update_tools = false;
        
        if ( ! fp.main.track_current_user ) {

            if ( fp.main.debug ) console.log("[FP] Current user cannot be tracked");

        // if the user made a choice in the past
        } else if ( fpdata.cookies ){

            cookies = fpdata.cookies;
            update_tools = true;

        // if no choice was made in the past
        } else {
            
            // agree to all if consent banner is disabled or we are in optout or notification mode
            if ( ! ( fp.notice.enabled && fp.notice.mode == "optin" ) ) {
                cookies = {
                    'stats' : true,
                    'personalisation' : true,
                    'marketing' : true,
                };
                update_tools = true;
            }
        }

        if ( ! fp.main.is_customizer && update_tools ) {
            FP.updateConsents( cookies, run_actions );
        } else {
            let cons_vals = FP.getConsentValues( cookies );
            fpdata.consents = cons_vals.consents;
        }

        FP.setSessionData( update_pageviews_count );
    }

    FP.setSessionData = ()=>{};// for the free version
    

    // ACTIVITY TIMER START
    document.addEventListener( 'mousemove', FP.throttle( FP.startActivityTimer, 500 ) ); // This starts the user's activity timer

    // PAGE SHOW/HIDE

    function sync_cookies_and_data(){

        let fp_cookie = FP.readCookie( 'fp_cookie' ),
            fp_cookie_data = FP.parseJSON( fp_cookie, false ),
            had_consets_before = !! fpdata.cookies;

        // if we have fp_cookie data
        if ( fp_cookie ) {

            // Delete the data if it is empty or is incorrect...
            if ( ! fp_cookie_data || ! FP.isObject( fp_cookie_data ) ) {
            
                FP.deleteCookie( 'fp_cookie' );
            
                // and reload if some consents were given before
                if ( had_consets_before ) {
                    fp.vars.will_reload = true;
                    window.location.reload();
                    return;
                }

            // if cookie data is correct
            } else {

                // and some consents were given before
                if ( had_consets_before ) {

                    // reload if they changed
                    if ( JSON.stringify( fp_cookie_data ) !== JSON.stringify( fpdata.cookies ) ) {
                        fp.vars.will_reload = true;
                        window.location.reload();
                        return;
                    
                    // or just update session data if they are the same
                    } else {
                        FP.setSessionData( false );
                    }
                
                // and no consent were given before
                } else {
                    
                    // instruct the CMS to hide the banner
                    fp.notice.hide_consent_banner = true;
                    FP.sendEvt('fupi_hide_cookie_banner');
                    
                    // update all data and add scr to DOM
                    fpdata.cookies = fp_cookie_data;
                    fp.ready.cmp = true;
                    fupi_run('tab_switch');
                }
            }

        // if we don't have fp_cookie
        } else {
            
            // reload the page if some consents were given before
            if ( had_consets_before ) {
                fp.vars.will_reload = true;
                window.location.reload();
                return;
            
            // or just update session data
            } else {
                FP.setSessionData( false );
            }
        }
    }

    fpdata.doc_in_focus = false;

    if ( ! document.hidden) {
        fpdata.doc_in_focus = true; // skips "show" actions on initial pageload
        FP.startActivityTimer();
    };

    function showPageActions() {
        if ( fpdata.doc_in_focus ) return;

        fpdata.doc_in_focus = true;
        fpdata.activity.current = 0;
        FP.startActivityTimer();
        sync_cookies_and_data();

        if ( ! fp.vars.will_reload ) {
            FP.doActions( ['page_show', 'page_in_focus'] );
        }
    }

    function hidePageActions() {
        if ( ! fpdata.doc_in_focus ) return;

        fpdata.doc_in_focus = false;
        FP.stopActivityTimer();
        FP.doActions( ['page_hide', 'page_in_blur'] );
    }

    window.addEventListener( 'pagehide', hidePageActions, false );

    window.addEventListener( 'pageshow', ()=>{
        // Gives visibilitychange priority because it sees updated cookies/storage.
        setTimeout( showPageActions, 0 );
    }, false );

    document.addEventListener( 'visibilitychange', ()=>{
        document.hidden ? hidePageActions() : showPageActions();
    }, false );
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

    // INIT

    let cookies             = FP.readCookie('fp_cookie'),
        normally_trackable  = fp.main.track_current_user,
        magic_keyw          = FP.getUrlParamByName( fp.main.magic_keyword ),
        cookies_modified_by_url = false;

    // Get data

    fpdata.cookies = FP.parseJSON( cookies, false );
    if ( cookies && ! FP.isObject( fpdata.cookies ) ) {
        FP.deleteCookie( 'fp_cookie' );
        fpdata.cookies = false;
    }

    // Migrate tracking and diagnostic properties out of the consent record.
    if ( fpdata.cookies && ( Object.prototype.hasOwnProperty.call( fpdata.cookies, 'disabled' ) || Object.prototype.hasOwnProperty.call( fpdata.cookies, 'ga4_debug' ) ) ) {
        if ( fpdata.cookies.disabled === true ) FP.setCookie( 'fp_tracking_disabled', '1', 7300 );
        delete fpdata.cookies.disabled;
        delete fpdata.cookies.ga4_debug;
        FP.setCookie( 'fp_cookie', JSON.stringify(fpdata.cookies), 182 );
    }

    fpdata.new_tab = ! window.sessionStorage['fp_old_session'];
    fpdata.new_session = fpdata.new_tab; // updated with better method if we get permission to set cookies later

    window.sessionStorage['fp_old_session'] = true; // removed on a new tab

    // Reset first so diagnostic values in the same URL are not reset

    switch ( magic_keyw ) {
        case 'reset':
            FP.resetTrackingState();
            fpdata.cookies = false;
            cookies_modified_by_url = true;
            break;

        case 'off':
            FP.setCookie( 'fp_tracking_disabled', '1', 7300 );
            cookies_modified_by_url = true;
            break;

        case 'on':
            FP.deleteCookie( 'fp_tracking_disabled' );
            cookies_modified_by_url = true;
            break;
    }

    fp.main.testing = FP.readCookie('fp_track_me') === '1';
    fp.main.tracking_disabled = FP.readCookie('fp_tracking_disabled') === '1';
    fp.main.track_current_user = fp.main.testing || ( ! fp.main.tracking_disabled && normally_trackable );
    
    // Ask for consent again
    // if pp pub was modified or the website wants to load different tools than the user agreed to
    if ( 
        ! cookies_modified_by_url && 
        fpdata.cookies && 
        fp.notice.enabled && ! fp.notice.dont_ask_again
    ) {

        let pp_pub_changed = fp.notice.priv_policy_update && ( ! fpdata.cookies.pp_pub || fpdata.cookies.pp_pub != fp.notice.priv_policy_update );

        let tools_changed = fp.tools && ( ! fpdata.cookies.tools || ! fp.tools.every( id => fpdata.cookies.tools.includes(id) ) );

        if ( pp_pub_changed || tools_changed ) {
            FP.deleteCookie("fp_cookie");
            fpdata.cookies = false;
        }
    }

    // CONSENT BANNER - apply page blur and lock scroll ASAP

    if ( 
        fp.main.track_current_user && 
        ! fp.main.is_customizer && 
        fp.notice.enabled && 
        ! fpdata.cookies && 
        fp.notice.display_notice 
    ) {
        
        // BLUR BACKGROUND
        if ( fp.notice.blur_page ) document.getElementsByTagName( "html" )[0].classList.add("fupi_blur");
        
        // LOCK PAGESCROLL
        if ( fp.notice.scroll_lock ) document.getElementsByTagName( "html" )[0].classList.add("fupi_scroll_lock");
    }

    let uses_geo = false;

    

    if ( ! uses_geo ) {
        fpdata.country = 'unknown';
        FP.updatePrivacyChoices();
        fp.ready = true;
    }

    // BROKEN LINK TRACKING

    if ( fp.track.track404 && fpdata.page_type == "404" && ! FP.getUrlParamByName("broken_link_location") ){
        const location = fp.track.redirect404_url ? new URL( fp.track.redirect404_url ) : window.location;
        window.location = location + ( location.search ? "&" : "?" ) + "broken_link_location=" + ( document.referrer || "direct_traffic_or_unknown" ) + "&broken_link=" + window.location;
    }

    // LOAD SCRIPTS
    FP.doWhenDOMLoaded( addScriptsToDOM );
    document.addEventListener( 'fp_load_scripts', addScriptsToDOM );

};

fupi_core();