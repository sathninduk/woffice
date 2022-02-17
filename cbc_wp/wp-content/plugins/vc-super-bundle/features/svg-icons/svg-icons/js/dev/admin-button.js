jQuery( document ).ready( function( $ ) {
	'use strict';

	var iconKeyUpTimeout = null;
	var prevSearchTerm = '';

	$( '.wpb_edit_form_elements .svg_icon_button_field' ).each( function() {
		var preview, previewSuccess, previewError;
		preview = $( this ).prev();

		// Update the icon preview.
		previewSuccess = function( data ) {
			preview.html( data );
		};
		previewError = function( data ) {
		};
		wp.ajax.send( 'svg_get', {
			success: previewSuccess,
			error: previewError,
			data: {
				nonce: $( this ).attr( 'data-nonce' ),
				icon: $( this ).val()
			}
		} );

	} ).on( 'keyup', function() {
		var $this;
		if ( prevSearchTerm === $( this ).val() ) {
			return;
		}

		prevSearchTerm = $( this ).val();

		// Make sure searching is throttled.
		if ( null !== iconKeyUpTimeout ) {
			clearTimeout( iconKeyUpTimeout );
			iconKeyUpTimeout = null;
		}

		$this = $( this );
		iconKeyUpTimeout = setTimeout( function() {
			var searchTerms, searchSuccess, searchError;
			var $displayArea = $this.parent().find( '.svg_select_window' );
			$displayArea.html( '' ).show();

			searchTerms = $this.val();

			searchSuccess = function( data ) {
				var iconPath, div;
				$displayArea.html( '' ).show();

				// Put all search results in the select window.
				// <div class="genericons/phone"><svg>...</svg></div>
				for ( iconPath in data ) {
					if ( ! data.hasOwnProperty( iconPath ) ) {
						continue;
					}
					div = document.createElement( 'DIV' );
					div.classList.add( iconPath );
					div.innerHTML = data[ iconPath ];
					$displayArea.append( $( div ) );
				}
			};
			searchError = function( data ) {
			};

			// Search for matching icon using ajax.
			wp.ajax.send( 'svg_search', {
				success: searchSuccess,
				error: searchError,
				data: {
					nonce: $this.attr( 'data-nonce' ),
					search_terms: searchTerms
				}

			} );

		}, 500 );
	} );

	$( '.wpb_edit_form_elements .svg_icon_button_field ~ .svg_select_window' ).on( 'click', 'div', function() {

		// Update input value.
		var $field = $( this ).parents( '.svg_select_window' ).parent().find( 'input' );
		$field.val( $( this ).attr( 'class' ) )

		// Update preview.
		.prev().html( $( this ).html() );
	});

});
