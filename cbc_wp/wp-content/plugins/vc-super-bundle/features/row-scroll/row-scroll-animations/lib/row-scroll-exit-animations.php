<?php
/**
 * Handles exit animations.
 *
 * @package Row Scroll Animation
 */

if ( ! function_exists( 'gambit_row_scroll_exit_animations' ) ) {

	/**
	 * All exit animations.
	 */
	function gambit_row_scroll_exit_animations() {

		$exits = array(
			'none' => array(),
			'scale-smaller' => array(
				'data-top-bottom' => array(
					'transform-origin' => '50% 100%',
					'opacity' => 0.4,
					'transform' => array(
						'scale' => 0.7,
					),
				),
				'data-%scenter-bottom' => array(
					'transform-origin' => '50% 100%',
					'opacity' => 1,
					'transform' => array(
						'scale' => 1,
					),
				),
			),

			'fade' => array(
				'data-top-bottom' => array(
					'opacity' => '0.0',
				),
				'data-%scenter-bottom' => array(
					'opacity' => 1,
				),
			),

			'content-fade' => array(
				'data-top-bottom' => array(
					'opacity' => '0.0',
				),
				'data-%scenter-bottom' => array(
					'opacity' => 1,
				),
			),

			'rotate-back' => array(
				'data-top-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '70deg',
					),
				),
				'data-%scenter-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '0deg',
					),
				),
			),

			'rotate-forward' => array(
				'data-top-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '-70deg',
					),
				),
				'data-%scenter-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '0deg',
					),
				),
			),

			'carousel' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateY' => '50vh',
						'scale' => '0.4',
					),
					'opacity' => 1,
					'z-index' => '0',
				),
				'data--1-top-bottom' => array(
					'transform' => array(
						'translateY' => '50vh',
						'scale' => '0.4',
					),
					'opacity' => 0,
					'z-index' => '0',
				),
				'data--20p-center-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
						'scale' => '1',
					),
					'opacity' => 1,
					'z-index' => '0',
				),
				'data--19.999p-center-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
						'scale' => '1',
					),
					'opacity' => 1,
					'z-index' => '1',
				),
			),

			'fly-up' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateY' => '-30vh',
					),
				),
				'data-%scenter-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
				),
			),

			'content-fly-up' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateY' => '-20vh',
					),
					'opacity' => 0,
				),
				'data-%scenter-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
					'opacity' => 1,
				),
			),

			'fly-left' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateX' => '-100vw',
					),
				),
				'data-%scenter-bottom' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
				),
			),

			'content-fly-left' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateX' => '-100vw',
					),
					'opacity' => 0,
				),
				'data-%scenter-bottom' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
					'opacity' => 1,
				),
			),

			'fly-right' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateX' => '100vw',
					),
					'opacity' => 0,
				),
				'data-%scenter-bottom' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
					'opacity' => 1,
				),
			),

			'content-fly-right' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateX' => '100vw',
					),
					'opacity' => 0,
				),
				'data-%scenter-bottom' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
					'opacity' => 1,
				),
			),

			// Stick.
			'stick' => array(
				'data--1-top-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '50vh',
					),
					'opacity' => 0,
				),
				'data-top-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '50vh',
					),
					'opacity' => 1,
				),
				'data-center-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
					),
				),
			),

			// Stick-scale.
			'stick-scale' => array(
				'data-top-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'translateZ' => '-300px',
						'translateY' => '100vh',
					),
					'opacity' => 0,
				),
				'data-center-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'translateZ' => '0px',
						'translateY' => '0vh',
					),
					'opacity' => 1,
				),
			),

			// For stick-flip-left.
			'stick-flip-left' => array(
				'data-top-bottom' => array(
					'transform-origin' => '0% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '100vh',
						'rotateY' => '91deg',
					),
					'opacity' => 0,
				),
				'data-bottom-bottom' => array(
					'transform-origin' => '0% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '0vh',
						'rotateY' => '0deg',
					),
					'opacity' => 1,
				),
			),

			// For stick-flip-right.
			'stick-flip-right' => array(
				'data-top-bottom' => array(
					'transform-origin' => '100% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '100vh',
						'rotateY' => '-91deg',
					),
					'opacity' => 0,
				),
				'data-bottom-bottom' => array(
					'transform-origin' => '100% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '0vh',
						'rotateY' => '0deg',
					),
					'opacity' => 1,
				),
			),

			// For stick-flip-top.
			'stick-flip-top' => array(
				'data-top-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '100vh',
						'rotateX' => '-91deg',
					),
					'opacity' => 0,
				),
				'data-bottom-bottom' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '0vh',
						'rotateX' => '0deg',
					),
					'opacity' => 1,
				),
			),

			// For stick-flip-bottom.
			'stick-flip-bottom' => array(
				'data-top-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '100vh',
						'rotateX' => '91deg',
					),
					'opacity' => 0,
				),
				'data-bottom-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'translateY' => '0vh',
						'rotateX' => '0deg',
					),
					'opacity' => 1,
				),
			),

			// For stick-fly-left.
			'stick-fly-left' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateY' => '100vh',
						'translateX' => '-100vw',
					),
					'z-index' => '2',
				),
				'data-bottom-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
						'translateX' => '0vw',
					),
					'z-index' => '2',
				),
				'data-1-bottom-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
						'translateX' => '0vw',
					),
					'z-index' => '1',
				),
			),

			// For stick-fly-right.
			'stick-fly-right' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateY' => '100vh',
						'translateX' => '100vw',
					),
					'z-index' => '2',
				),
				'data-bottom-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
						'translateX' => '0vw',
					),
					'z-index' => '2',
				),
				'data-1-bottom-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
						'translateX' => '0vw',
					),
					'z-index' => '1',
				),
			),

			// For stick-fly-down.
			'stick-fly-down' => array(
				'data-top-bottom' => array(
					'transform' => array(
						'translateY' => '200%',
					),
					'z-index' => '2',
					'opacity' => 1,
				),
				'data-bottom-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
					'z-index' => '2',
					'opacity' => 1,
				),
				'data-1-bottom-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
					'z-index' => '1',
					'opacity' => 1,
				),
				'data--1-top-bottom' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
					'z-index' => '1',
					'opacity' => 0,
				),
			),

			// For stick-rotate-left.
			'stick-rotate-left' => array(
				'data-top-bottom' => array(
					'transform-origin' => '0% 100%',
					'transform' => array(
						'translateY' => '100vh',
						'rotate' => '-91deg',
					),
					'z-index' => '2',
				),
				'data-bottom-bottom' => array(
					'transform-origin' => '0% 100%',
					'transform' => array(
						'translateY' => '0vh',
						'rotate' => '0deg',
					),
					'z-index' => '2',
				),
				'data-1-bottom-bottom' => array(
					'transform-origin' => '0% 100%',
					'transform' => array(
						'translateY' => '0vh',
						'rotate' => '0deg',
					),
					'z-index' => '1',
				),
			),

			// For stick-rotate-right.
			'stick-rotate-right' => array(
				'data-top-bottom' => array(
					'transform-origin' => '100% 100%',
					'transform' => array(
						'translateY' => '100vh',
						'rotate' => '91deg',
					),
					'z-index' => '2',
				),
				'data-bottom-bottom' => array(
					'transform-origin' => '100% 100%',
					'transform' => array(
						'translateY' => '0vh',
						'rotate' => '0deg',
					),
					'z-index' => '2',
				),
				'data-1-bottom-bottom' => array(
					'transform-origin' => '100% 100%',
					'transform' => array(
						'translateY' => '0vh',
						'rotate' => '0deg',
					),
					'z-index' => '1',
				),
			),

			'cube' => array(
				'data-30p-top-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '91deg',
					),
				),
				'data-20p-center-bottom' => array(
					'transform-origin' => '50% 100%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '0deg',
					),
				),
			),
		);

		return apply_filters( 'gambit_row_scroll_exit_animations', $exits );
	}
} // End if().
