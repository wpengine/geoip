jQuery( document ).ready( function ( $ ) {

    // When one of our notices are clicked, process it
    $( '#wpbody' ).on( 'click', '.notice.wpengine-geoip', function () {
        $.post( {
            url: ajaxurl,
            data: {
                action: 'geoip_dismiss_notice',
                key: $( this ).data( 'key' )
            }
        } );
    } );
} );
