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

	var loadThese = [];
	var loadTimeout;
	var loadFont = function( font ) {
		if ( -1 !== font.indexOf( '—' ) ) {
			return;
		}
		if ( -1 !== window.fnt_loaded.indexOf( font ) ) {
			return;
		}
		window.fnt_loaded.push( font );
		loadThese.push( font + ':400:latin' );
		clearTimeout( loadTimeout );
		loadTimeout = setTimeout( function() {
			var fontsToLoad = loadThese;
			WebFont.load( {
				google: {
					families: fontsToLoad
				}
			} );
			loadThese = [];
		}, 150 );
	};

	var loadVisibles = debounce( function() {

		var options = $( '.select2-results__options' );
		var buffer = 40;
		var scrollTop = options.scrollTop() - buffer;
		var height = options.outerHeight() + buffer * 2;

		options.find( 'li' ).filter( function() {
			return scrollTop < $( this ).data( 'top' ) && $( this ).data( 'top' ) < scrollTop + height;
		} ).each( function() {
			loadFont( $( this ).text() );
		} );
	}, 150 );

	var loadAll = debounce( function() {
		var h = $( '.select2-results__options li:eq(0)' ).outerHeight();
		var options = $( '.select2-results__options' );
		var height = options.outerHeight();
		var numToLoad = Math.ceil( height / h );

		options.find( 'li' ).each( function( i ) {
			if ( i <= numToLoad ) {
				loadFont( $( this ).text() );
			}
		} );
	}, 150 );

	var didSearch = function() {
		adjustOptions();
		loadVisibles();
	};

	var adjustOptions = function() {
		var h = 0;
		$( '.select2-results__options li' ).each( function( i ) {
			var font = $( this ).text();
			$( this ).css( 'font-family', font );
			$( this ).addClass( 'fnt_option' );

			if ( 0 === h ) {
				h = $( this ).outerHeight();
			}
			$( this ).data( 'top', h * i );
		} );
	};

	if ( 'undefined' === typeof window.fnt_loaded ) {
		window.fnt_loaded = [];
	}

	$( '.wpb_edit_form_elements .fnt_picker_field' ).each( function() {
		$( this ).fnt_select2( {
			placeholder: $( this ).attr( 'data-placeholder' ),
			allowClear: true
		} );

	} ).on( 'select2:open', function() {

		setTimeout( function() {
			adjustOptions();

			$( '.select2-results__options' ).on( 'scroll', loadVisibles );
			loadVisibles();

			$( '.select2-search__field' ).on( 'input', didSearch );
		}, 10 );

	} ).on( 'select2:close', function() {

		$( '.select2-results__options' ).off( 'scroll', loadVisibles );

		$( '.select2-search__field' ).off( 'input', didSearch );

	} );

});
