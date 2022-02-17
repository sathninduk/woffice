<?php
/**
 * Handles all plugin functions.
 *
 * @package Row Scroll Animation
 */

/**
 * This is the main script that adds in the plugin's main shortcode/behavior/etc.
 * Each important component has it's own class-shortcode.php file.
 *
 * For example, in Parallax, we have a class-parallax-row.php for the parallax row element,
 * and class-fullwidth-row.php for the full-width row element.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

require_once( 'row-scroll-animations/lib/row-scroll-exit-animations.php' );
require_once( 'row-scroll-animations/lib/row-scroll-entrance-animations.php' );

// Initializes plugin class.
if ( ! class_exists( 'GambitRowScrollAnimation' ) ) {

	/**
	 * This is where all the plugin's functionality happens.
	 */
	class GambitRowScrollAnimation {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Our admin-side scripts & styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Initializes as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcode' ), 999 );

			// Makes the plugin function accessible as a shortcode.
			add_shortcode( 'row_scroll', array( $this, 'render_shortcode' ) );
		}


		/**
		 * Includes admin scripts and styles needed.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function enqueue_admin_scripts() {
			wp_enqueue_style( __CLASS__ . '-admin', plugins_url( 'row-scroll-animations/css/admin.css', __FILE__ ), array(), GAMBIT_ROW_SCROLL );
		}


		/**
		 * Creates our shortcode settings in Visual Composer.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_shortcode() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Row Scroll Animation', GAMBIT_ROW_SCROLL ),
				'base' => 'row_scroll',
				'icon' => plugins_url( 'row-scroll-animations/images/row-scroll-icon.svg', __FILE__ ),
				'description' => __( 'Entrance & exit row animations.', GAMBIT_ROW_SCROLL ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_ROW_SCROLL ) : '',
				'admin_enqueue_css' => plugins_url( 'row-scroll-animations/css/admin.css', __FILE__ ),
				'params' => array(
					array(
						'type' => 'dropdown',
						'heading' => __( 'Entrance animation', GAMBIT_ROW_SCROLL ),
						'param_name' => 'entrance',
						'value' => array(
							__( 'None', GAMBIT_ROW_SCROLL ) => 'none',
							__( 'Content fade (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fade',
							__( 'Content fly up (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fly-up',
							__( 'Content fly left (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fly-left',
							__( 'Content fly right (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fly-right',
							__( 'Scale smaller', GAMBIT_ROW_SCROLL ) => 'scale-smaller',
							__( 'Fade in', GAMBIT_ROW_SCROLL ) => 'fade',
							__( '3D Rotate backward', GAMBIT_ROW_SCROLL ) => 'rotate-back',
							__( '3D Rotate forward', GAMBIT_ROW_SCROLL ) => 'rotate-forward',
							__( 'Carousel forward', GAMBIT_ROW_SCROLL ) => 'carousel',
							__( 'Fly up', GAMBIT_ROW_SCROLL ) => 'fly-up',
							__( 'Fly left', GAMBIT_ROW_SCROLL ) => 'fly-left',
							__( 'Fly right', GAMBIT_ROW_SCROLL ) => 'fly-right',
							// __( 'Stick to bottom (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick',
							// __( 'Stick & scale smaller (will make your row have at least half the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-scale',
							__( '3D cube (use 3D cube entrance animation on the next row to make this look good)', GAMBIT_ROW_SCROLL ) => 'cube',
						),
						'description' => '',
						'std' => 'fade',
						'holder' => 'span',
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Exit animation', GAMBIT_ROW_SCROLL ),
						'param_name' => 'exit',
						'value' => array(
							__( 'None', GAMBIT_ROW_SCROLL ) => 'none',
							__( 'Content fly up (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fly-up',
							__( 'Content fade (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fade',
							__( 'Content fly left (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fly-left',
							__( 'Content fly right (animates only the content)', GAMBIT_ROW_SCROLL ) => 'content-fly-right',
							__( 'Scale smaller', GAMBIT_ROW_SCROLL ) => 'scale-smaller',
							__( 'Fade out', GAMBIT_ROW_SCROLL ) => 'fade',
							__( '3D Rotate backward', GAMBIT_ROW_SCROLL ) => 'rotate-back',
							__( '3D Rotate forward', GAMBIT_ROW_SCROLL ) => 'rotate-forward',
							__( 'Carousel forward', GAMBIT_ROW_SCROLL ) => 'carousel',
							__( 'Fly up', GAMBIT_ROW_SCROLL ) => 'fly-up',
							__( 'Fly left', GAMBIT_ROW_SCROLL ) => 'fly-left',
							__( 'Fly right', GAMBIT_ROW_SCROLL ) => 'fly-right',
							// __( 'Stick to top (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick',
							// __( 'Stick & scale smaller (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-scale',
							// __( 'Stick & flip left (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-flip-left',
							// __( 'Stick & flip right (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-flip-right',
							// __( 'Stick & flip top (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-flip-top',
							// __( 'Stick & flip bottom (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-flip-bottom',
							// __( 'Stick & fly left (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-fly-left',
							// __( 'Stick & fly right (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-fly-right',
							// __( 'Stick & fly down (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-fly-down',
							// __( 'Stick & rotate left (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-rotate-left',
							// __( 'Stick & rotate right (will make your row have at least the height of the screen)', GAMBIT_ROW_SCROLL ) => 'stick-rotate-right',
							__( '3D cube (use 3D cube entrance animation on the next row to make this look good)', GAMBIT_ROW_SCROLL ) => 'cube',
						),
						'description' => '',
						'std' => 'fade',
						'holder' => 'span',
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Entrance delay', GAMBIT_ROW_SCROLL ),
						'param_name' => 'entrance_offset',
						'value' => '',
						'description' => __( 'Add any value from -20 to 20.<br>(Tip: <strong>Positive</strong> numbers lengthen the entrance animation duration, and <strong>negative</strong> numbers shorten it.)', GAMBIT_ROW_SCROLL ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Exit delay', GAMBIT_ROW_SCROLL ),
						'param_name' => 'exit_offset',
						'value' => '',
						'description' => __( 'The exit animation triggers when your container reaches more than 1/3th of the screen from the top (some effects are different, some are unchangeable). Add any value from -20 to 20.<br>(Tip: <strong>Positive</strong> numbers lengthen the exit animation duration, and <strong>negative</strong> numbers shorten it.)', GAMBIT_ROW_SCROLL ),
					),
					array(
						'type' => 'checkbox',
						'heading' => '',
						'param_name' => 'body_overflow',
						'value' => array(
							__( 'Fix Body Overflow', GAMBIT_ROW_SCROLL ) => 'true',
						),
						'description' => __( 'Check this if you see an unwanted scrollbar that shows up for a very short time while scrolling with some effects & some themes.', GAMBIT_ROW_SCROLL ),
						'group' => __( 'Advanced', GAMBIT_ROW_SCROLL ),
					),
				),
			) );
		}


		/**
		 * Shortcode logic.
		 *
		 * @param	array  $atts - The attributes of the shortcode.
		 * @param	string $content - The content enclosed inside the shortcode if any.
		 * @return	string - The rendered html.
		 * @since	1.0
		 */
		public function render_shortcode( $atts, $content = null ) {
			$defaults = array(
				'exit' => 'fade',
				'exit_offset' => '-5',
				'entrance' => 'fade',
				'entrance_offset' => '5',
				'body_overflow' => '',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			// Default values for the offset.
			$atts['exit_offset'] = '' == $atts['exit_offset'] ? '-5' : (int) $atts['exit_offset'];
			$atts['entrance_offset'] = '' == $atts['entrance_offset'] ? '5' : (int) $atts['entrance_offset'] * -1;

			$data_attributes = $this->generate_scroll_data( $atts['entrance'], $atts['exit'], $atts['entrance_offset'], $atts['exit_offset'] );
			if ( is_wp_error( $data_attributes ) ) {
				return '<strong>' . $data_attributes->get_error_message() . '</strong>';
			}

			// Skrollr script.
			wp_enqueue_script( __CLASS__, plugins_url( 'row-scroll-animations/js/min/script-min.js', __FILE__ ), array(), VERSION_GAMBIT_ROW_SCROLL, true );
			wp_enqueue_style( __CLASS__, plugins_url( 'row-scroll-animations/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_ROW_SCROLL );

			$content_manipulators = array(
				'content-fade',
				'content-fly-up',
				'content-fly-left',
				'content-fly-right',
			);

			wp_localize_script( __CLASS__, 'rowScrollParams', array(
				'content_manipulators' => $content_manipulators,
			) );

			return "<div class='gambit_row_scroll' " .
				"data-row-entrance='" . esc_attr( $atts['entrance'] ) . "' " .
				"data-row-exit='" . esc_attr( $atts['exit'] ) . "' " .
				( in_array( $atts['exit'], $content_manipulators ) ? "data-exit-do-children='true' " : '' ) .
				( in_array( $atts['entrance'], $content_manipulators ) ? "data-entrance-do-children='true' " : '' ) .
				( ! empty( $atts['body_overflow'] ) ? "data-body-overflow='" . esc_attr( $atts['body_overflow'] ) . "' " : '' ) .
				$data_attributes .
				'>' . do_shortcode( $content ) . '</div>';
		}


		/**
		 * Handles the animations and whatnot.
		 *
		 * @param	string $entrance - The slug name of the entrance animation.
		 * @param	string $exit - The slug name of the exit animation.
		 * @param	string $entrance_offset - The mount to offset the entrance animation.
		 * @param	string $exit_offset - The mount to offset the exit animation.
		 */
		public function generate_scroll_data( $entrance, $exit, $entrance_offset = '', $exit_offset = '' ) {

			if ( empty( $entrance ) ) {
				$entrance = 'none';
			}
			if ( empty( $exit ) ) {
				$exit = 'none';
			}

			// All the possible exits.
			$exits = gambit_row_scroll_exit_animations();

			// All the possible entrances.
			$entrances = gambit_row_scroll_entrance_animations();

			// These animations have Skrollr smooth scrolling turned off.
			$smooth_scroll_off = array(
				// 'cube',
				'stick',
				'stick-flip-left',
				'stick-flip-right',
				'stick-flip-top',
				'stick-flip-bottom',
				'stick-fly-left',
				'stick-fly-right',
				'stick-rotate-left',
				'stick-rotate-right',
			);

			// These are the only allowed styles & transforms along with their default values.
			$defaults = array(
				'transform-origin' => '50% 50%',
				'opacity' => 1,
				'scale' => 1,
				'perspective' => '1000px',
				'translateX' => '0vw',
				'translateY' => '0vh',
				'translateZ' => '0px',
				'rotate' => '0deg',
				'rotateX' => '0deg',
				'rotateY' => '0deg',
				'rotateZ' => '0deg',
				'box-shadow' => '0 0 0 rgba(0,0,0,0)',
				'z-index' => 1,
			);

			/**
			 * Generate a set of the default styles based on all stuff that's used-
			 * by both the entrance and exit styles.
			 *
			 * We need to do this since for Skrollr to work in both entrance & exit animations,
			 * ALL styles being manipulated should be present in all animation steps.
			 */
			$all_keys = array();
			$default_styles = array(
				'transform' => array(),
			);

			/**
			 * Apply the offsets.
			 */

			// By default, offsets are % of the screen height which is designated by 'p' in Skrollr.
			// If units are.
			if ( preg_match( '/px$/', $exit_offset ) ) {
				$exit_offset = str_replace( 'px', '', $exit_offset );
			} elseif ( '' != $exit_offset ) {
				preg_match( '/([\-0-9.]*)/', $exit_offset, $matches );
				if ( count( $matches ) ) {
					$exit_offset = $matches[0] . 'p';
				}
			}
			if ( preg_match( '/px$/', $entrance_offset ) ) {
				$entrance_offset = str_replace( 'px', '', $entrance_offset );
			} elseif ( '' != $entrance_offset ) {
				preg_match( '/([\-0-9.]*)/', $entrance_offset, $matches );
				if ( count( $matches ) ) {
					$entrance_offset = $matches[0] . 'p';
				}
			}

			$exit_offset .= '' != $exit_offset ? '-' : '';
			$entrance_offset .= '-' != $entrance_offset ? '-' : '';

			if ( ! isset( $entrances[ $entrance ] ) ) {
				return new WP_Error( 'attribute_error', __( 'Row Scroll Error: Entrance animation ' . $entrance . ' is not valid', GAMBIT_ROW_SCROLL ) );
			}
			if ( ! isset( $exits[ $exit ] ) ) {
				return new WP_Error( 'attribute_error', __( 'Row Scroll Error: Exit animation ' . $exit . ' is not valid', GAMBIT_ROW_SCROLL ) );
			}

			foreach ( $exits[ $exit ] as $location => $styles ) {
				$new_location = sprintf( $location, $exit_offset ) . '-exit';
				if ( $location != $new_location ) {
					$exits[ $exit ][ $new_location ] = $styles;
					unset( $exits[ $exit ][ $location ] );
				}
			}
			foreach ( $entrances[ $entrance ] as $location => $styles ) {
				$new_location = sprintf( $location, $entrance_offset ) . '-entrance';
				if ( $location != $new_location ) {
					$entrances[ $entrance ][ $new_location ] = $styles;
					unset( $entrances[ $entrance ][ $location ] );
				}
			}

			$animations = array_merge( $exits[ $exit ], $entrances[ $entrance ] );

			foreach ( $animations as $location => $styles ) {
				foreach ( array_keys( $styles ) as $style_key ) {
					if ( is_array( $styles[ $style_key ] ) ) {

						foreach ( array_keys( $styles[ $style_key ] ) as $sub_style_key ) {

							if ( empty( $all_keys[ $style_key ] ) ) {
								$all_keys[ $style_key ] = array();
							}

							if ( ! in_array( $sub_style_key, $all_keys[ $style_key ] ) ) {
								$all_keys[ $style_key ][] = $sub_style_key;
								$default_styles[ $style_key ][ $sub_style_key ] = $defaults[ $sub_style_key ];
							}
						}
						continue;
					}

					if ( ! in_array( $style_key, $all_keys ) ) {
						$all_keys[] = $style_key;
						$default_styles[ $style_key ] = $defaults[ $style_key ];
					}
				}
			}

			/**
			 * $default_styles should now contain all styles with default values.
			 * $all_keys should now contain all the possible keys.
			 */

			// Generate into data attributes.
			$data_attrib = '';
			foreach ( $animations as $location => $styles ) {

				$data_attrib .= $location . '="';

				foreach ( $default_styles as $style_key => $style_rule ) {
					if ( is_array( $style_rule ) ) {

						if ( empty( $style_rule ) ) {
							continue;
						}

						$data_attrib .= esc_attr( $style_key ) . ':';
						foreach ( $style_rule as $sub_style_key => $sub_style_rule ) {

							$sub_style_rule = isset( $styles[ $style_key ][ $sub_style_key ] ) ? $styles[ $style_key ][ $sub_style_key ] : $sub_style_rule;

							$data_attrib .= ' ' . esc_attr( $sub_style_key ) . '(' . esc_attr( $sub_style_rule ) . ')';
						}
						$data_attrib .= ';';

					} else {

						$style_rule = isset( $styles[ $style_key ] ) ? $styles[ $style_key ] : $style_rule;

						if ( 'transform-origin' === $style_key ) {
							$data_attrib .= esc_attr( $style_key ) . ': !' . esc_attr( $style_rule ) . ';';
						} else {
							$data_attrib .= esc_attr( $style_key ) . ': ' . esc_attr( $style_rule ) . ';';
						}
					}
				}

				$data_attrib .= '" ';
			}

			// Apply OFF Skrollr smooth scrolling.
			if ( in_array( $exit, $smooth_scroll_off ) ) {
				$data_attrib .= 'data-smooth-scrolling-exit="off"';
			}
			if ( in_array( $entrance, $smooth_scroll_off ) ) {
				$data_attrib .= 'data-smooth-scrolling-entrance="off"';
			}

			return $data_attrib;
		}
	}

	new GambitRowScrollAnimation();

} // End if().
