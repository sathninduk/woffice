(function() {
	var ready = function() {
		var gradientTexts = document.querySelectorAll( '.cg_color_gradient > * > [style*="text-align:"]' );
		Array.prototype.forEach.call( gradientTexts, function(el) {
			var wrapper = document.createElement('div');
			wrapper.style.textAlign = el.style.textAlign;
			wrapper.classList.add( 'cg_wrapper' );
			wrapper.style.display = 'block';
			el.parentNode.replaceChild(wrapper, el);
			wrapper.appendChild(el);
		} )
	}
  if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading'){
	ready();
  } else {
	document.addEventListener('DOMContentLoaded', ready);
  }
})()
