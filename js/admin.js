document.getElementById( 'wpbody' ).addEventListener( 'click', function ( event ) {
    // If this wasn't a click on a notice-dismiss close button, then abort
    if ( 'notice-dismiss' !== event.target.className ) {
        return;
    }

    // This should be our parent div for the notice
    var parent = event.path[ 1 ] || null;

    // If the parent div doesn't have our wpengine-geoip class, then abort
    if ( !parent || !parent.classList.includes( 'wpengine-geoip' ) ) {
        return;
    }

    // Get our notice's key
    var key = parent.attributes[ 'data-key' ].value || null;

    // Send our POST request to admin-ajax
    var http = new XMLHttpRequest();
    var params = "action=geoip_dismiss_notice&key=" + key;
    http.open( "POST", ajaxurl, true );
    http.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
    http.send( params );
} );
