import domReady from '@wordpress/dom-ready'
import Vivus from 'vivus'

const anims = []

// Expose this so the demo can call it.
domReady( () => {
	var svgs = document.querySelectorAll( '.gmb-asvg' )
	Array.prototype.forEach.call( svgs, ( svg, i ) => {
		var svgTag = svg.querySelector( 'svg' )
		if ( svgTag ) {
			svgTag.removeAttribute( 'width' );
			svgTag.removeAttribute( 'height' );
			svg.style.visibility = 'visible';
			const anim = new Vivus( svgTag, {
				type: svg.getAttribute( 'data-type' ) || 'delayed',
				// Convert milliseconds to # of frames, assume 16 frames per second.
				duration: ( svg.getAttribute( 'data-speed' ) || 1400 ) / 0.016 / 1000,
			} )
			anims.push( anim )
		}
	} )
} )

window._gmbRefreshSVGAnims = () => {
	anims.forEach( v => v.reset().play() )
}
