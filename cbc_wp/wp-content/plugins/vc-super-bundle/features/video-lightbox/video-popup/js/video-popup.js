import domready from 'domready'
import { videoPopup } from 'effectbox'

domready( () => {
	const elems = document.querySelectorAll( '.eb-video-popup' )
	elems.forEach( el => {
		const options = {}
		if ( el.getAttribute( 'data-webm' ) ) {
			options.video = [
				el.getAttribute( 'data-webm' ),
				el.getAttribute( 'data-video' ),
			]
		}
		videoPopup.start( el, options )
	} )
} )
