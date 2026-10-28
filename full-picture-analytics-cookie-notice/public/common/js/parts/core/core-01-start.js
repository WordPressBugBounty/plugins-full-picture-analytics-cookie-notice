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
