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

	var isMac = navigator.appVersion.indexOf( 'Mac' ) !== -1;

	var postID = $( '#post_ID' ).val();
	var undoStack = [];
	var redoStack = [];
	var currentContent = null;
	var isUndoRedoing = false;
	var isUndoRedoingTimeout;
	var stackLimit = 50;

	var updateButtons = function() {
		$( '.ur_undo' ).css( 'opacity', undoStack.length ? '1' : '0.4' );
		$( '.ur_redo' ).css( 'opacity', redoStack.length ? '1' : '0.4' );

		localStorage.setItem( 'ur_stack_' + postID, JSON.stringify( undoStack ) );
	};

	var undo = function() {
		var content;
		if ( ! undoStack.length ) {
			return;
		}
		content = undoStack.pop();
		redoStack.push( currentContent );
		if ( redoStack.length > stackLimit ) {
			redoStack.shift();
		}
		setContent( content );
		isUndoRedoing = true;
		updateVC();
		clearTimeout( isUndoRedoingTimeout );
		isUndoRedoingTimeout = setTimeout( function() {
			isUndoRedoing = false;
		}, 10 );
		updateButtons();
	};

	var redo = function() {
		var content;
		if ( ! redoStack.length ) {
			return;
		}
		content = redoStack.pop();
		undoStack.push( currentContent );
		if ( undoStack.length > stackLimit ) {
			undoStack.shift();
		}
		setContent( content );
		isUndoRedoing = true;
		updateVC();
		clearTimeout( isUndoRedoingTimeout );
		isUndoRedoingTimeout = setTimeout( function() {
			isUndoRedoing = false;
		}, 10 );
		updateButtons();
	};

	var vcChanged = debounce( function() {
		var content;

		if ( isUndoRedoing ) {
			return;
		}

		// Clear redo.
		redoStack = [];

		content = getContent();

		if ( null === currentContent ) {
			currentContent = content;
			return;
		}

		// Add in undo stack.
		undoStack.push( currentContent );

		currentContent = content;
		updateButtons();
	}, 10 );

	var getContent = function() {
		try {
			return tinyMCE.activeEditor.getContent();
		} catch ( err ) {

			// If content isn't ready yet (just loaded), get the form value.
			return jQuery( '.wp-editor-area' ).val();
		}
	};

	var setContent = function( content ) {
		if ( content ) {
			currentContent = content;
			tinyMCE.activeEditor.setContent( content );
		}
	};

	var updateVC = function() {
		vc.shortcodes.fetch( { reset: true });
	};

	var keydownHandler = debounce( function( ev ) {

		// Redo Mac.
		if ( ( ev.metaKey || ev.ctrlKey ) && ev.shiftKey && 90 === ev.keyCode ) {
			redo();

		// Undo Mac & Windows.
		} else if ( ( ev.metaKey || ev.ctrlKey ) && 90 === ev.keyCode ) {
			undo();

		// Redo Windows.
		} else if ( ! isMac && ( ev.metaKey || ev.ctrlKey ) && 89 === ev.keyCode ) {
			redo();
		}
	}, 10 );

	if ( 'undefined' === typeof vc ) {
		return;
	}
	if ( 'undefined' === typeof vc.shortcodes ) {
		return;
	}

	if ( localStorage.getItem( 'ur_stack_' + postID ) ) {
		try {
			undoStack = JSON.parse( localStorage.getItem( 'ur_stack_' + postID ) );
		} catch ( err ) {

			// Do nothing.
		}
	}

	// Listen to VC changes.
	vc.shortcodes.bind( 'change', vcChanged );
	vc.shortcodes.bind( 'remove', vcChanged );

	// Listen to shortcut buttons.
	$( document ).keydown( keydownHandler );

	// Add our undo & redo buttons.
	$( '.vc_templates-button' ).css( 'border-right', 'none' );
	$( '<li class="ur_undo_wrapper"><a href="javascript:;" class="vc_icon-btn vc_templates-button ur_undo" title="Undo"><i class="vc-composer-icon dashicons-undo dashicons" style="font-family: \'dashicons\' !important; text-decoration: none;"></i></a></li>' ).appendTo( '.vc_navbar-nav' );
	$( '<li class="ur_redo_wrapper"><a href="javascript:;" class="vc_icon-btn vc_templates-button vc_navbar-border-right ur_redo" title="Redo"><i class="vc-composer-icon dashicons-redo dashicons" style="font-family: \'dashicons\' !important; text-decoration: none;"></i></a></li>' ).appendTo( '.vc_navbar-nav' );
	$( 'body' ).append( '<style>.vc_navgar-frontend .ur_undo_wrapper, .vc_navgar-frontend .ur_redo_wrapper { display: none; }</style>' );

	// Listen for clicks.
	$( 'body' ).on( 'click', '.ur_undo', function() {
		undo();
	} );
	$( 'body' ).on( 'click', '.ur_redo', function() {
		redo();
	} );
	updateButtons();
});
