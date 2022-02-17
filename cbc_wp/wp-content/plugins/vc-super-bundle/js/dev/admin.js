/* globals sbParams */

jQuery( document ).ready( function( $ ) {
	'use strict';

	var getAllFeatures = function() {
		var features = {};
		$( 'input.gvc-switch-toggle' ).each( function() {
			features[ $( this ).attr( 'data-feature' ) ] = $( this )[0].checked ? 'enable' : 'disable';
		} );
		return features;
	};

	// Only do this in our settings page.
	if ( ! $( '.super-bundle-wrap' ).length ) {
		return;
	}

	// Feautre enable/disable switch.
	$( 'body' ).on( 'change', 'input.gvc-switch-toggle', function() {
		var p = $( this ).parents( 'p' );
		var spinner = p.find( '.spinner' );

		// Remove all spinners.
		$( this ).parents( '.gvc-switch + .spinner.is-active' ).removeClass( 'is-active' );
		spinner.addClass( 'is-active' );

		// Add an enabled class to the switch.
		$( this ).parents( '.gvc-switch' ).toggleClass( 'is-enabled' );

		// Disable this input.
		$( this ).attr( 'disabled', 'disabled' );

		// Abort any unfinished ajax calls.
		if ( window.sbToggleAjax ) {
			window.sbToggleAjax.abort();
		}

		// Update the settings.
		window.sbToggleAjax = wp.ajax.send( 'sb_toggle_feature', {
		    success: function() {
				window.sbToggleAjax = null;

				// Hide spinner.
				spinner.removeClass( 'is-active' );

				// Enable all inputs.
				p.parents( '.card' ).find( 'input[disabled]' ).removeAttr( 'disabled' );
			},
		    error: function() {
				spinner.removeClass( 'is-active' );

				// Hide spinner.
				window.sbToggleAjax = null;

				// Enable all inputs.
				p.parents( '.card' ).find( 'input[disabled]' ).removeAttr( 'disabled' );
			},
		    data: {
				features: getAllFeatures(),
				nonce: sbParams.nonce
		    }
		} );
	} );

} );
