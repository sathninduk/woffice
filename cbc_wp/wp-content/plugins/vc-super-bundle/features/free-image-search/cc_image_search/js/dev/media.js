/* globals CCImageParams */

jQuery( document ).ready( function( $ ) {
	'use strict';

	// Create the media manager instance.
	var _mediaImageSearchFrame = wp.media( {
		title: CCImageParams.media_title,
		library: {
			type: 'image'
		},
		button: null
	} );

    _mediaImageSearchFrame.on( 'open', function() {

        // Hide the insert button
        $( '.media-toolbar-primary a' ).css( 'opacity', '0' );

        // Hide the select button
        $( '.media-button-select' ).css( 'opacity', '0' );

        // Select our tab.
        $( '.media-router a' ).each( function() {
			if ( $( this ).text().indexOf( CCImageParams.tab_title ) !== -1 ) {
				$( this ).click().addClass( 'active' );
			} else {
				$( this ).removeClass( 'active' );
				$( this ).remove();
			}
	    } );
    } );

	// Open the media manager frame with the free image tab open.
	$( 'body' ).on( 'click', '.cc-image-search-button', function() {
        _mediaImageSearchFrame.open();
    } );

	// Add the "Search for Free Images" button in the media-new.php page.
	$( '.drag-drop-inside' ).css( 'width', '100%' );
	$( '.drag-drop-buttons' ).append( '&nbsp; <input type="button" value="' + CCImageParams.media_title + '" class="button cc-image-search-button" />' );

	// Add the "Search for Free Images" button in the library page.
	setTimeout( function() {
		$( '.uploader-inline .upload-ui' ).append( '&nbsp; <input type="button" value="' + CCImageParams.media_title + '" class="button button-hero cc-image-search-button" />' );
	}, 1 );
});
