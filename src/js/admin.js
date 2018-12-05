/**
 * This JS file sends an ajax request on admin notification close.
 *
 * @package wpengine-geoip
 */

document.getElementById( 'wpbody' ).addEventListener(
	'click',
	function ( event ) {
		// If this wasn't a click on a notice-dismiss close button, then abort.
		if ( 'notice-dismiss' !== event.target.className ) {
			return;
		}

		// This should be our parent div for the notice.
		var parent = event.path[ 1 ] || null;

		// If the parent div doesn't have our wpengine-geoip class, then abort.
		if ( ! parent || -1 === jQuery.inArray( 'wpengine-geoip', parent.classList ) ) {
			return;
		}

		// Get our notice's key.
		var key = parent.attributes[ 'data-key' ].value || null;

		// Send our POST request to admin-ajax.
		var http   = new XMLHttpRequest();
		var params = "action=geoip_dismiss_notice&key=" + key + "&nonce=" + window.nonce;
		http.open( "POST", ajaxurl, true );
		http.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
		http.send( params );
	}
);
