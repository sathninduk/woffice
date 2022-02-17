<?php
/**
 * Handles entrance animations.
 *
 * @package Row Scroll Animation
 */

if ( ! function_exists( 'gambit_row_scroll_entrance_animations' ) ) {

	/**
	 * All entrance animations.
	 */
	function gambit_row_scroll_entrance_animations() {

		$entrances = array(
			'none' => array(),
			'scale-smaller' => array(
				'data-%scenter-top' => array(
					'transform-origin' => '50% 0%',
					'opacity' => 1,
					'transform' => array(
						'scale' => 1,
					),
				),
				'data-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'opacity' => 0.4,
					'transform' => array(
						'scale' => 1.2,
					),
				),
			),

			'fade' => array(
				'data-%scenter-top' => array(
					'opacity' => 1,
				),
				'data-bottom-top' => array(
					'opacity' => '0.0',
				),
			),

			'content-fade' => array(
				'data-%scenter-top' => array(
					'opacity' => 1,
				),
				'data-bottom-top' => array(
					'opacity' => '0.0',
				),
			),

			'rotate-forward' => array(
				'data-%scenter-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '0deg',
					),
				),
				'data-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '-70deg',
					),
				),
			),

			'rotate-back' => array(
				'data-%scenter-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '0deg',
					),
				),
				'data-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '70deg',
					),
				),
			),

			'carousel' => array(
				'data-20p-center-top' => array(
					'opacity' => 1.0,
					'transform' => array(
						'scale' => '1',
						'translateY' => '0vh',
					),
					'z-index' => '0',
					'opacity' => 1,
				),
				'data-19.9999p-center-top' => array(
					'opacity' => 1.0,
					'transform' => array(
						'scale' => '1',
						'translateY' => '0vh',
					),
					'z-index' => '1',
					'opacity' => 1,
				),
				'data-bottom-top' => array(
					'transform' => array(
						'scale' => '0.4',
						'translateY' => '-50vh',
					),
					'z-index' => '1',
					'opacity' => 1,
				),
				'data-1-bottom-top' => array(
					'transform' => array(
						'scale' => '0.4',
						'translateY' => '-50vh',
					),
					'z-index' => '0',
					'opacity' => 0,
				),
			),

			'fly-up' => array(
				'data-%scenter-top' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
				),
				'data-bottom-top' => array(
					'transform' => array(
						'translateY' => '30vh',
					),
				),
			),

			'content-fly-up' => array(
				'data-%scenter-top' => array(
					'transform' => array(
						'translateY' => '0vh',
					),
					'opacity' => 1,
				),
				'data-bottom-top' => array(
					'transform' => array(
						'translateY' => '30vh',
					),
					'opacity' => 0,
				),
			),

			'fly-left' => array(
				'data-%scenter-top' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
				),
				'data-bottom-top' => array(
					'transform' => array(
						'translateX' => '100vw',
					),
				),
			),

			'content-fly-left' => array(
				'data-%scenter-top' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
					'opacity' => 1,
				),
				'data-bottom-top' => array(
					'transform' => array(
						'translateX' => '100vw',
					),
					'opacity' => 0,
				),
			),

			'fly-right' => array(
				'data-%scenter-top' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
				),
				'data-bottom-top' => array(
					'transform' => array(
						'translateX' => '-100vw',
					),
				),
			),

			'content-fly-right' => array(
				'data-%scenter-top' => array(
					'transform' => array(
						'translateX' => '0vw',
					),
					'opacity' => 1,
				),
				'data-bottom-top' => array(
					'transform' => array(
						'translateX' => '-100vw',
					),
					'opacity' => 0,
				),
			),

			// Stick.
			'stick' => array(
				'data-1-top-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
					),
					'opacity' => 1,
					'z-index' => 1,
				),
				'data-2-top-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
					),
					'opacity' => 1,
					'z-index' => 0,
				),
				'data-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '-100vh',
					),
					'opacity' => 1,
					'z-index' => 0,
				),
				'data-1-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
					),
					'opacity' => 0,
					'z-index' => 0,
				),
			),

			// Stick-scale.
			'stick-scale' => array(
				'data-1-center-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
						'scale' => 1,
					),
					'opacity' => 1,
					'z-index' => 1,
				),
				'data-2-center-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
						'scale' => 1,
					),
					'opacity' => 1,
					'z-index' => 0,
				),
				'data-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '-50vh',
						'scale' => 1.2,
					),
					'opacity' => 0,
					'z-index' => 0,
				),
				'data-1-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'translateY' => '0vh',
						'scale' => 1,
					),
					'opacity' => 1,
					'z-index' => 0,
				),
			),

			'cube' => array(
				'data--20p-center-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '0deg',
					),
				),
				'data--30p-bottom-top' => array(
					'transform-origin' => '50% 0%',
					'transform' => array(
						'perspective' => '1000px',
						'rotateX' => '-91deg',
					),
				),
			),
		);

		return apply_filters( 'gambit_row_scroll_entrance_animations', $entrances );
	}
} // End if().
