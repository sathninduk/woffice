window._tteInitEffectType = function( wrapper ) {
	var counter = 0;
	var mid = wrapper.querySelector( '.tte_mid' );
	var midAttr = mid.getAttribute( 'data-text' ).replace( /\s*,\s*/g, ',' );
	if ( ! midAttr ) {
		return;
	}
	midAttr = midAttr.split( ',' );
	var cursor = '<span class="tte-cursor">|</span>';
	var wordIndex = 0;
	var direction = 'right';
	var waitCounter = 0;
	var animSpeed = wrapper.getAttribute( 'data-speed' ); // ms

	if ( animSpeed === 'slow' ) {
	   animSpeed = 100;
   } else if ( animSpeed === 'normal' ) {
		animSpeed = 60;
	} else if ( animSpeed === 'fast' ) {
		animSpeed = 30;
	}

	var animDelay = wrapper.getAttribute( 'data-delay' ); // ms

	var typedLen = midAttr[ wordIndex ].length;
	mid.innerHTML = cursor;
	var i = setInterval( function() {

		if ( counter === typedLen + 1 ) {
			waitCounter++;
			if ( waitCounter * animSpeed <= animDelay ) {
				return;
			} else {
				waitCounter = 0;
			}
		}

		mid.innerHTML = midAttr[ wordIndex ].substr( 0, counter ) + cursor;
		if ( direction === 'right' ) {
			counter++;
		} else {
			counter--;
		}
		if ( counter === typedLen + 1 ) {
			direction = 'left';
		}
		if ( counter === -1 ) {
			wordIndex++;
			direction = 'right';
			counter = 0;
			if ( wordIndex === midAttr.length ) {
				wordIndex = 0;
			}
			typedLen = midAttr[ wordIndex ].length;
		}

	}, animSpeed );
};

window._tteInitEffectFade = function( wrapper ) {
	var mid = wrapper.querySelector( '.tte_mid' );
	var words = mid.getAttribute( 'data-text' ).split( ',' );
	var delay = parseInt( wrapper.getAttribute( 'data-delay' ), 10 );
	var numWords = words.length;
	var currWord = 1;
	if ( currWord > numWords - 1 ) {
		currWord = 0;
	}
	mid.innerHTML = words[0];
	mid.classList.add( 'tte_show' );
	setInterval( function() {
		mid.classList.remove( 'tte_show' );
		mid.classList.add( 'tte_hide' );
		mid.style.width = '';
		setTimeout( function() {
			var oldWidth = mid.getBoundingClientRect().width;
			mid.innerHTML = words[ currWord ];
			var newWidth = mid.getBoundingClientRect().width;
			mid.style.width = oldWidth + 'px';
			mid.classList.remove( 'tte_hide' );
			mid.style.transition = 'none';
			setTimeout( function() {
				mid.style.width = newWidth + 'px';
				currWord++;
				if ( currWord > numWords - 1 ) {
					currWord = 0;
				}
				mid.style.transition = '';
				mid.classList.add( 'tte_show' );
			}, 30 );
		}, 410 );
	}, delay );
};

window._tteInitEffectRandom = function( wrapper ) {

	var resolver = {
		resolve: function resolve( options, callback ) {
		// The string to resolve
		var resolveString = options.resolveString || options.element.getAttribute( 'data-text' );

		var combinedOptions = Object.assign({}, options, { resolveString: resolveString });
		function getRandomInteger( min, max ) {
		  return Math.floor( Math.random() * ( max - min + 1 ) ) + min;
		};

		function randomCharacter( characters ) {
		  return characters[getRandomInteger( 0, characters.length - 1 )];
		};

		function doRandomiserEffect( options, callback ) {
		  var characters = options.characters;
		  var timeout = options.timeout;
		  var element = options.element;
		  var partialString = options.partialString;

		  var iterations = options.iterations;

		  setTimeout( function() {
			if ( iterations >= 0 ) {
			  var nextOptions = Object.assign({}, options, { iterations: iterations - 1 });

			  // Ensures partialString without the random character as the final state.
			  if ( iterations === 0 ) {
				element.innerHTML = partialString;
			  } else {
				// Replaces the last character of partialString with a random character
				element.innerHTML = partialString.substring( 0, partialString.length - 1 ) + randomCharacter( characters );
			  }

			  doRandomiserEffect( nextOptions, callback );
			} else if ( typeof callback === "function" ) {
			  callback();
			}
		  }, options.timeout );
		};

		function doResolverEffect( options, callback ) {
		  var resolveString = options.resolveString;
		  var characters = options.characters;
		  var offset = options.offset;
		  var partialString = resolveString.substring( 0, offset );
		  var combinedOptions = Object.assign({}, options, { partialString: partialString });

		  doRandomiserEffect( combinedOptions, function() {
			var nextOptions = Object.assign({}, options, { offset: offset + 1 });

			if ( offset <= resolveString.length ) {
			  doResolverEffect( nextOptions, callback );
			} else if ( typeof callback === "function" ) {
			  callback();
			}
		  });
		};

		doResolverEffect( combinedOptions, callback );
	  }
	};

	var midData = wrapper.querySelector('.tte_mid')

	var strings = midData.getAttribute('data-text').split( ',' );

	var counter = 0;

	var animSpeed = wrapper.getAttribute( 'data-speed' ); // ms

	if ( animSpeed === 'slow' ) {
	   animSpeed = 50;
   } else if ( animSpeed === 'normal' ) {
		animSpeed = 20;
	} else if ( animSpeed === 'fast' ) {
		animSpeed = 9;
	}

	var options = {
		// Initial position
		offset: 0,
		// Timeout between each random character
		timeout: animSpeed,
		// Number of random characters to show
		iterations: 5,

		// Random characters to pick from
		characters: [
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
			'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z'
		],

		// String to resolve
		resolveString: strings[counter],

		// The element
		element: wrapper.querySelector( '.tte_mid' )
	};

	// Callback function when resolve completes
	function callback() {
	var animDelay = wrapper.getAttribute( 'data-delay' );
	  setTimeout( function() {
		counter++;

		if ( counter >= strings.length ) {
		  counter = 0;
		}

		var nextOptions = Object.assign({}, options, { resolveString: strings[counter] });
		resolver.resolve( nextOptions, callback );
	  }, animDelay);
	}
	resolver.resolve( options, callback );
};

window._tteInitEffectScrambled = function( wrapper ) {

	'use strict';

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

	var TextScramble = function () {
	  function TextScramble(el) {
		_classCallCheck(this, TextScramble);

		this.el = el;
		this.chars = '!<>-_\\/[]{}—=+*^?#________';
		this._lastTime = 0;
		this.speed = el.parentNode.getAttribute( 'data-speed' );
		if ( this.speed === 'fast' ) {
			this.speed = 5;
		} else if ( this.speed === 'normal' ) {
			this.speed = 20;
		} else {
			this.speed = 40;
		}
		this.update = this.update.bind(this);
	  }

	  TextScramble.prototype.setText = function setText(newText) {
		var _this = this;

		var oldText = this.el.innerText;
		var length = Math.max(oldText.length, newText.length);
		var promise = new Promise(function (resolve) {
		  return _this.resolve = resolve;
		});
		this.queue = [];
		for (var i = 0; i < length; i++) {
		  var from = oldText[i] || '';
		  var to = newText[i] || '';
		  var start = Math.floor(Math.random() * 40);
		  var end = start + Math.floor(Math.random() * 40);
		  this.queue.push({ from: from, to: to, start: start, end: end });
		}
		cancelAnimationFrame(this.frameRequest);
		this.frame = 0;
		this.update();
		return promise;
	  };

	  TextScramble.prototype.update = function update( timer ) {
		  if ( timer - this._lastTime < this.speed ) {
	  		  this.frameRequest = requestAnimationFrame(this.update);
			  return;
		  }
			 this._lastTime = timer;
		var output = '';
		var complete = 0;
		for (var i = 0, n = this.queue.length; i < n; i++) {
		  var _queue$i = this.queue[i];
		  var from = _queue$i.from;
		  var to = _queue$i.to;
		  var start = _queue$i.start;
		  var end = _queue$i.end;
		  var char = _queue$i.char;
		  var charColor = wrapper.getAttribute('char-color');
		  if (this.frame >= end) {
			complete++;
			output += to;
		  } else if (this.frame >= start) {
			if (!char || Math.random() < 0.28) {
			  char = this.randomChar();
			  this.queue[i].char = char;
			}
			output += '<span class="tte-scramble-symbol" style="color: ' + charColor + ';">' + char + '</span>';
		  } else {
			output += from;
		  }
		}
		this.el.innerHTML = output;
		if (complete === this.queue.length) {
		  this.resolve();
		} else {
		  this.frameRequest = requestAnimationFrame(this.update);
		  this.frame++;
		}
	  };

	  TextScramble.prototype.randomChar = function randomChar() {
		return this.chars[Math.floor(Math.random() * this.chars.length)];
	  };

	  return TextScramble;
	}();

	var mid = wrapper.querySelector('.tte_mid');
	var words = mid.getAttribute('data-text').split(',');
	var el = wrapper.querySelector('.tte_mid');
	var animDelay = wrapper.getAttribute('data-delay');

	var fx = new TextScramble(el);

	var counter = 0;
	var next = function next() {
	  fx.setText(words[counter]).then(function () {
		setTimeout( next, animDelay );
	  });
	  counter = (counter + 1) % words.length;
	};
	next();
};


(function() {

	var ttes = document.querySelectorAll( '.tte_wrapper' );
	Array.prototype.forEach.call( ttes, function( el ) {
		var typeAttr = el.getAttribute( 'data-effect' );

		// Make the element visible and remove original text.
		// Original text is for SEO.
		var mid = el.querySelector( '.tte_mid' );
		mid.innerHTML = '';
		mid.style.opacity = '';

		if ( typeAttr === 'typing' ) {
			window._tteInitEffectType( el );
		} else if ( typeAttr === 'fade' || typeAttr === 'top-to-bottom' || typeAttr === 'bottom-to-top' || typeAttr === 'vertical-flip' ) {
			window._tteInitEffectFade( el );
		} else if ( typeAttr === 'random-letters' ) {
			window._tteInitEffectRandom( el );
		} else if ( typeAttr === 'text-scrambled' ) {
			window._tteInitEffectScrambled( el );
		}

	} );
})();
