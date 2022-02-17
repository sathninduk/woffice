function domReady(fn) {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
	fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

domReady( function() {
	// ...
	var stopCountUp = function( el ) {
		clearTimeout( el.countUpTimeout );
		if ( el._countUpOrigInnerHTML ) {
			el.innerHTML = el._countUpOrigInnerHTML;
			el._countUpOrigInnerHTML = undefined;
		}
		el.style.visibility = '';
	}

	var initCountUp = function( el ) {
		var lang, time, delay, divisions, splitValues, nums, k, i, num, isComma;
		var isFloat, decimalPlaces, val, newNum, output;

		stopCountUp( el );

		// If no number, don't do anything.
		if ( ! /[0-9]/.test( el.innerHTML ) ) {
			return;
		}

		// Remember the element.
		el._countUpOrigInnerHTML = el.innerHTML;

		// Check location language.
		lang = document.querySelector( 'html' ).getAttribute( 'lang' ) || undefined;

		// Get the given time and delay by their attributes.
		time = el.getAttribute( 'data-duration' );
		delay = el.getAttribute( 'data-delay' );

		// Number of times the number will change.
		divisions = time / delay;

		// Split numbers and html tags.
		splitValues = el.innerHTML.split( /(<[^>]+>|[0-9.][,.0-9]*[0-9]*)/ );

		// Contains all numbers to be displayed.
		nums = [];

		// Set blank strings to ready the split values.
		for ( k = 0; k < divisions; k++ ) {
			nums.push( '' );
		}

		// Loop through all numbers and html tags.
		for ( i = 0; i < splitValues.length; i++ ) {

			// If number split it into smaller numbers and insert it to nums.
			if ( /([0-9.][,.0-9]*[0-9]*)/.test( splitValues[ i ] ) && ! /<[^>]+>/.test( splitValues[ i ] ) ) {
				num = splitValues[ i ];

				// Test if numbers have comma.
				isComma = /[0-9]+,[0-9]+/.test( num );

				// Remove comma for computation purposes.
				num = num.replace( /,/g, '' );

				// Test if values have point.
				isFloat = /^[0-9]+\.[0-9]+$/.test( num );

				// Check number of decimals places.
				decimalPlaces = isFloat ? ( num.split( '.' )[1] || [] ).length : 0;

				// Start adding numbers from the end.
				k = nums.length - 1;

				// Create small numbers
				for ( val = divisions; val >= 1; val-- ) {
					newNum = parseInt( num / divisions * val, 10 );

					// If has decimal point, add it again.
					if ( isFloat ) {
						newNum = parseFloat( num / divisions * val ).toFixed( decimalPlaces );
						newNum = parseFloat( newNum ).toLocaleString( lang );
					}

					// If has comma, add it again.
					if ( isComma ) {
						newNum = newNum.toLocaleString( lang );
					}

					// Insert all small numbers.
					nums[ k-- ] += newNum;

				}
			} else {

				// Insert all non-numbers in the same place.
				for ( k = 0; k < divisions; k++ ) {
					nums[ k ] += splitValues[ i ];
				}
			}
		}

		// The last value of the element should be the original one.
		nums[ nums.length - 1 ]  = el.innerHTML;

		el.innerHTML = nums[0];
		el.style.visibility = 'visible';

		// Function for displaying output with the set time and delay.
		output = function() {
			el.innerHTML = nums.shift();
			if ( nums.length ) {
				clearTimeout( el.countUpTimeout );
				el.countUpTimeout = setTimeout( output, delay );
			} else {
				el._countUpOrigInnerHTML = undefined;
			}
		};
		el.countUpTimeout = setTimeout( output, delay );
	};

	// Animate, use waypoints included in VC.
	jQuery( '.number-count-up-vc .number-counter:not(.number-counted)' )
		.waypoint( function() {
			if ( ! jQuery( this ).hasClass( 'number-counted' ) ) {
				initCountUp( jQuery( this ).addClass('number-counted')[0] )
			}
		}, {
			offset: '85%'
		} )
} )
