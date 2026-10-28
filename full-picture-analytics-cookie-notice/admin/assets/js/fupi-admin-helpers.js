let FP = {};

(function(FP){

    'use strict';

    let d = document,
	    w = window;

    FP.v = {}; // for variables
    FP.f = {}; // for single functions
    FP.v.viewport = {};
    FP.m = {}; // for modules and extensions

    FP.updateViewportDimensions = () => {
        var e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth,
        y = w.innerHeight || e.clientHeight || g.clientHeight;
        FP.v.viewport = {
            width: x,
            height: y
        };
    };

    FP.getUrlParamByName = name => {
        // var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
        // return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
        const queryString = window.location.search,
			urlParams = new URLSearchParams(queryString);
		return urlParams.get(name);
    };

    FP.getSiblings = e => {

        // for collecting sibling elements, including the given element
        let siblings = [];

        // if no parent, return no sibling
        if (!e.parentNode) return [];

        // first child of the parent node
        let sibling  = e.parentNode.firstChild;

        // collecting siblings
        while (sibling) {
            if (sibling.nodeType === 1) {
                siblings.push(sibling);
            }
            sibling = sibling.nextSibling;
        }
        return siblings;
    }

    // ASYNC GET SCRIPT
    FP.getScript = (src, func, id) => {
        var script = d.createElement('script');
        script.async = "async";
        script.src = src;
        script.id = id;
        if (func) { script.onload = func; }
        d.getElementsByTagName("head")[0].appendChild( script );
    };

    // CONVERT NODE LIST TO ARRAY
    FP.nl2Arr = nl => nl ? [].slice.call(nl) : false;

    FP.remove = el => {
        if(el){ el.parentNode.removeChild(el); }
    };

    FP.findAll = (e, c) => {
        if ( c === null ) return [];
        c = c || document;
        return FP.nl2Arr(c.querySelectorAll(e));
    };

    FP.findFirst = (e, c) => {
        if ( c === null ) return null;
        c = c || document;
        return c.querySelector(e);
    };

    FP.findID = (e, c) => {
        if ( c === null ) return null;
        c = c || document;
        return c.getElementById(e);
    };

    FP.findTags = (e, c) => {
        if ( c === null ) return null;
        c = c || document;
        return FP.nl2Arr(c.getElementsByTagName(e));
    };

    FP.setCookie = (name, value, days = 0, mins = 0, path = "/") => {
        var expires = "";
        if ( days > 0 || mins > 0 ) {
            var date = new Date();
            if (days > 0) {
                date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            } else {
                date.setTime(date.getTime() + mins * 60 * 1000);
            }
            expires = "; expires=" + date.toGMTString();
        };
        d.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=" + path + "; sameSite=strict" + (window.location.protocol === 'https:' ? '; Secure' : '');
    };

    FP.readCookie = name => {
        var nameEQ = name + "=";
        var ca = document.cookie.split(";");
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == " ") {c = c.substring(1, c.length);}
            if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        };
        return null;
    };

    FP.deleteCookie = name => {
        FP.setCookie(name,"",-1);
    };

    FP.sendEvt = (evt_name, details_a) => {
        var details = details_a ? { 'detail' : details_a } : {},
            fp_event = new CustomEvent( evt_name, details );
        document.dispatchEvent(fp_event);
    };

})(FP);
