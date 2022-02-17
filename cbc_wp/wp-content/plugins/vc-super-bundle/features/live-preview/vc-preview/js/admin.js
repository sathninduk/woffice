/* globals vc, tinyMCE */

jQuery( document ).ready( function( $ ) {
	'use strict';

	var debounce = function( func, wait, immediate ) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if ( ! immediate ) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if ( callNow ) {
				func.apply( context, args );
			}
		};
	};

	// This is our URL.
	var previewUrl = $( '#preview-action' ).find( '.preview' ).attr( 'href' ).replace( /&?preview=\w+/, '' ) + '&gvc_preview=1&post_ID=' + $( 'form#post input[name="post_ID"]' ).val();

	var vcChanged = debounce( function() {
		var title;

		// Only do this in full screen.
		if ( ! $( 'body' ).hasClass( 'vc_fullscreen' ) ) {
			return;
		}

		// If there's no title yet, then that means the page hasn't saved yet. Trigger a save so we can preview.
		title = $( '#title' );
		if ( 'undefined' !== typeof title && '' === title.val() ) {
			title.val( 'Post #' + $( '#post_ID' ).val() ).blur();
			if ( wp.autosave ) {
				wp.autosave.server.triggerSave();
				setTimeout( vcChanged, 200 );
				return;
			}
		}

		// Save the preview content.
		wp.ajax.send( 'save_preview', {
			success: function() {
				$( '#gvcpreview' ).attr( 'src', previewUrl + '&t=' + ( new Date() ).getTime() );
			},
			data: {
				'_wpnonce': $( 'form#post input[name="_wpnonce"]' ).val(),
				'post_ID': $( 'form#post input[name="post_ID"]' ).val(),
				'content': getContent(),
				'custom_css': $( '#vc_post-custom-css' ).val()
			}
		} );
	}, 10 );

	var getContent = function() {
		try {
			return tinyMCE.activeEditor.getContent() || jQuery( '.wp-editor-area' ).val();
		} catch ( err ) {

			// If content isn't ready yet (just loaded), get the form value.
			return jQuery( '.wp-editor-area' ).val();
		}
	};

	// VC Listeners
	vc.shortcodes.bind( 'change', vcChanged );
	vc.shortcodes.bind( 'remove', vcChanged );
	$( 'body' ).on( 'click', '#vc_fullscreen-button', vcChanged );
	$( 'body' ).on( 'click', '[data-vc-ui-element="button-save"]', vcChanged );

	$( 'body' ).on( 'click', '.gvc-resp-buttons > *', function( ev ) {
		var c = '';
		ev.preventDefault();

		// Refresh button.
		if ( $( this ).hasClass( 'gvc-refresh' ) ) {
			vcChanged();
			return;
		}

		// Responsive buttons.
		if ( $( this ).hasClass( 'gvc-tablet' ) ) {
			c = 'gvc-tablet';
		} else if ( $( this ).hasClass( 'gvc-phone' ) ) {
			c = 'gvc-phone';
		}
		$( '#gvcpreview' ).removeClass( 'gvc-tablet gvc-phone' );
		if ( c ) {
			$( '#gvcpreview' ).addClass( c );
		}
		$( this ).parent().find( '> *' ).removeClass( 'gvc-active' );
		$( this ).addClass( 'gvc-active' );
	} );

} );
