<?php
/**
 * The hover animation functionalities are located here.
 *
 * @package Hover Animations for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'HoverAnimForVC' ) ) {

	/**
	 * The class that does the functions.
	 */
	class HoverAnimForVC {

		/**
		 * Holds all the styles which we're going to print out in the footer.
		 *
		 * @var array
		 */
		public $css = array();

		/**
		 * Keeps the supported VC shortcodes so that we won't have to look it up every time.
		 *
		 * @var array
		 */
		public $supported_vc_shortcodes = array();

		/**
		 * Hook into WordPress.
		 *
		 * @return void.
		 * @since 1.0
		 */
		function __construct() {

			// Add the hover options to shortcodes.
			add_action( 'init', array( $this, 'add_row_hover_tab' ), 999 );
			add_action( 'init', array( $this, 'add_element_hover_tab' ), 999 );

			// Add the hover link to the row output.
			add_filter( 'vc_shortcode_output', array( $this, 'add_hover_link' ), 999, 3 );

			// Add the class to the shortcode outputs.
			add_filter( 'vc_shortcodes_css_class', array( $this, 'add_hover_class' ), 999, 3 );

			// Print out the font styles in the footer.
			add_action( 'wp_footer', array( $this, 'add_styles' ) );
		}

		/**
		 * Adds the font parameter to VC elements.
		 */
		public function add_row_hover_tab() {
			if ( ! function_exists( 'vc_add_param' ) ) {
				return;
			}

			$attributes = array(
				array(
					'type' => 'dropdown',
					'param_name' => 'hover_anim',
					'heading' => __( 'Animation', GAMBIT_VC_HOVER_ANIM ),
					'value' => array(
						'– ' . __( 'No hover animation', GAMBIT_VC_HOVER_ANIM ) . ' –' => '',
						__( 'Fade in new background image', GAMBIT_VC_HOVER_ANIM ) => 'image',
						__( 'Colorize', GAMBIT_VC_HOVER_ANIM ) => 'color',
						__( 'Zoom', GAMBIT_VC_HOVER_ANIM ) => 'zoom',
						__( 'Zoom and tilt', GAMBIT_VC_HOVER_ANIM ) => 'zoom-tilt',
						__( 'Color from bottom to top', GAMBIT_VC_HOVER_ANIM ) => 'bottom-to-top',
						__( 'Color from top to bottom', GAMBIT_VC_HOVER_ANIM ) => 'top-to-bottom',
						__( 'Color from left to right', GAMBIT_VC_HOVER_ANIM ) => 'left-to-right',
						__( 'Color from right to left', GAMBIT_VC_HOVER_ANIM ) => 'right-to-left',
						__( 'Lift', GAMBIT_VC_HOVER_ANIM ) => 'lift',
						__( 'Grow or Shrink', GAMBIT_VC_HOVER_ANIM ) => 'grow',
					),
					'description' => __( 'This animation will play when your mouse hovers on this row. You can also place hover animations on elements inside this row to also animate them together with this row. (Doesn\'t work with FULL-HEIGHT rows)', GAMBIT_VC_HOVER_ANIM ),
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
				),
				array(
					'type' => 'attach_image',
					'heading' => __( 'Background image to fade in or zoom', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_image',
					'value' => '',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'image',
							'zoom',
							'zoom-tilt',
						),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Tilt Degrees', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_zoom_tilt_degrees',
					'description' => __( 'The number of degrees to tilt the background image.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '3.0',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'zoom-tilt',
						),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Zoom opacity', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_zoom_opacity',
					'description' => __( 'The opacity of the zoomed image when hovered on. This can be used to show the background image a bit. Value should be from 0.0 to 1.0.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '1.0',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'zoom',
							'zoom-tilt',
						),
					),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Background Colorize', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_color',
					'description' => __( 'The color of the background when hovering.', GAMBIT_VC_HOVER_ANIM ),
					'value' => 'rgba(0,0,0,0.75)',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'color',
							'bottom-to-top',
							'top-to-bottom',
							'left-to-right',
							'right-to-left',
						),
					),
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'hover_anim_color_pre_hover',
					'value' => array(
						__( 'Show color before hovering, instead of when hovering', GAMBIT_VC_HOVER_ANIM ) => 'color_before',
					),
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'color',
							'bottom-to-top',
							'top-to-bottom',
							'left-to-right',
							'right-to-left',
						),
					),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Shadow Intensity', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_shadow',
					'description' => __( 'The intensity of the shadow that appears on hover.', GAMBIT_VC_HOVER_ANIM ),
					'value' => array(
						__( 'Light', GAMBIT_VC_HOVER_ANIM ) => 'light',
						__( 'Moderate', GAMBIT_VC_HOVER_ANIM ) => 'moderate',
						__( 'Heavy', GAMBIT_VC_HOVER_ANIM ) => 'heavy',
					),
					'std' => 'moderate',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'lift',
						),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Scale Amount', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_scale_amount',
					'description' => __( 'The scale amount. 0.0 - 1.0 means the element will scale down, 1.0+ will scale up the element.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '1.07',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'grow',
						),
					),
				),
				array(
					'type' => 'dropdown',
					'param_name' => 'hover_anim_speed',
					'heading' => __( 'Animation Speed', GAMBIT_VC_HOVER_ANIM ),
					'value' => array(
						__( 'Slow', GAMBIT_VC_HOVER_ANIM ) => 'slow',
						__( 'Normal', GAMBIT_VC_HOVER_ANIM ) => 'normal',
						__( 'Fast', GAMBIT_VC_HOVER_ANIM ) => 'fast',
					),
					'std' => 'normal',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'not_empty' => true,
					),
				),
				array(
					'type' => 'vc_link',
					'heading' => __( 'Link URL', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_link',
					'description' => __( 'You can turn this entire row into a link. If you add a link, the contents of the row will no longer be clickable.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
				),
			);

			// These are all the shortcodes we will add the fonts to.
			vc_add_params( 'vc_row', $attributes );
			vc_add_params( 'vc_row_inner', $attributes );
		}

		/**
		 * Adds the hover options in elements.
		 */
		public function add_element_hover_tab() {
			if ( ! function_exists( 'vc_add_param' ) ) {
				return;
			}

			$attributes = array(
				array(
					'type' => 'dropdown',
					'param_name' => 'hover_anim',
					'heading' => __( 'Animation', GAMBIT_VC_HOVER_ANIM ),
					'value' => array(
						'– ' . __( 'No hover animation', GAMBIT_VC_HOVER_ANIM ) . ' –' => '',
						__( 'Change text color', GAMBIT_VC_HOVER_ANIM ) => 'color',
						__( 'Fade into view', GAMBIT_VC_HOVER_ANIM ) => 'fade-in',
						__( 'Fade out of view', GAMBIT_VC_HOVER_ANIM ) => 'fade-out',
						__( 'Move up', GAMBIT_VC_HOVER_ANIM ) => 'move-up',
						__( 'Move down', GAMBIT_VC_HOVER_ANIM ) => 'move-down',
						__( 'Move left', GAMBIT_VC_HOVER_ANIM ) => 'move-left',
						__( 'Move right', GAMBIT_VC_HOVER_ANIM ) => 'move-right',
						__( 'Grow or Shrink', GAMBIT_VC_HOVER_ANIM ) => 'grow',
					),
					'description' => __( 'This animation will play when your mouse hovers on this element, or on this element\'s row.', GAMBIT_VC_HOVER_ANIM ),
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
				),
				array(
					'type' => 'colorpicker',
					'heading' => __( 'Change color to', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_color',
					'description' => __( 'The text color to change to on hover.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '#ffffff',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'color',
						),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Move amount', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_move',
					'description' => __( 'The amount in pixels to move the element on hover.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '10',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'move-up',
							'move-down',
							'move-left',
							'move-right',
						),
					),
				),
				array(
					'type' => 'checkbox',
					'heading' => __( 'Movement options', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_move_hide_start',
					'value' => array(
						__( 'Make element hidden before hovering', GAMBIT_VC_HOVER_ANIM ) => 'hide_start',
					),
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'move-up',
							'move-down',
							'move-left',
							'move-right',
						),
					),
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'hover_anim_move_hide_end',
					'value' => array(
						__( 'Make element hidden after hovering', GAMBIT_VC_HOVER_ANIM ) => 'hide_end',
					),
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'move-up',
							'move-down',
							'move-left',
							'move-right',
						),
					),
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'hover_anim_move_before',
					'value' => array(
						__( 'Move before hovering', GAMBIT_VC_HOVER_ANIM ) => 'move_before',
					),
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'move-up',
							'move-down',
							'move-left',
							'move-right',
						),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Scale Amount', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_scale',
					'description' => __( 'The scale amount. 0.0 - 1.0 means the element will scale down, 1.0+ will scale up the element.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '1.07',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'value' => array(
							'grow',
						),
					),
				),
				array(
					'type' => 'dropdown',
					'param_name' => 'hover_anim_speed',
					'heading' => __( 'Animation Speed', GAMBIT_VC_HOVER_ANIM ),
					'value' => array(
						__( 'Slow', GAMBIT_VC_HOVER_ANIM ) => 'slow',
						__( 'Normal', GAMBIT_VC_HOVER_ANIM ) => 'normal',
						__( 'Fast', GAMBIT_VC_HOVER_ANIM ) => 'fast',
					),
					'std' => 'normal',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'not_empty' => true,
					),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Animation delay', GAMBIT_VC_HOVER_ANIM ),
					'param_name' => 'hover_anim_delay',
					'description' => __( 'The time in milliseconds to delay the start of the animation.', GAMBIT_VC_HOVER_ANIM ),
					'value' => '0',
					'group' => __( 'Hover Behavior', GAMBIT_VC_HOVER_ANIM ),
					'dependency' => array(
						'element' => 'hover_anim',
						'not_empty' => true,
					),
				),
			);

			$shortcodes = $this->supported_vc_shortcodes();
			foreach ( $shortcodes as $shortcode ) {
				vc_add_params( $shortcode, $attributes );
			}
		}

		/**
		 * Gets all the VC shortcodes that support (element) hover anims.
		 *
		 * @return array All the names of the VC shortcodes (minus vc_row).
		 */
		public function supported_vc_shortcodes() {
			if ( ! empty( $this->supported_vc_shortcodes ) ) {
				return $this->supported_vc_shortcodes;
			}

			// Use getShortCodes instead of getAllShortCodes.
			// getAllShortCodes can perform some stuff on the shortcode,
			// that can screw with VC 5.1.1 + some other addons.
			$shortcodes = array_keys( WPBMap::getShortCodes() );

			$shortcodes_ignored = array(
				'vc_row',
				'vc_column',
				'vc_column_inner',
				'vc_row_inner',
				'vc_empty_space',
				'vc_media_grid',
				'vc_basic_grid',
				'vc_masonry_grid',
				'vc_masonry_media_grid',
				'vc_pie',
				'vc_round_chart',
				'vc_line_chart',
				'vc_raw_js',
			);
			foreach ( $shortcodes as $shortcode ) {
				if ( 0 !== stripos( $shortcode, 'vc_' ) ) {
					continue;
				}
				if ( in_array( $shortcode, $shortcodes_ignored ) ) {
					continue;
				}
				$this->supported_vc_shortcodes[] = $shortcode;
			}
			return $this->supported_vc_shortcodes;
		}

		/**
		 * Adds the hover link as the last child of a row.
		 *
		 * @param string $output The shortcode output.
		 * @param string $sc The shortcode name.
		 * @param array $atts The attributes of the shortcode.
		 *
		 * @return string The modified shortcode output.
		 */
		public function add_hover_link( $output, $sc, $atts ) {
			if ( ! empty( $atts['hover_anim_link'] ) ) {
				$href = vc_build_link( $atts['hover_anim_link'] );
				if ( ! empty( $href['url'] ) ) {
					$a = '<a href="' . $href['url'] . '" title="' . $href['title'] . '" target="' . $href['target'] . '" rel="' . $href['rel'] . '" class="hover-anim-link">&nbsp;</a>';
					$output = preg_replace( '/(<\/\w*[^>]+>\s*$)/', $a . '$1', $output, 1 );
				}
			}
			return $output;
		}

		/**
		 * Adds the special hover class to the affected VC elements.
		 *
		 * @param string $classes The current classes of the element.
		 * @param object $sc The shortcode object.
		 * @param object $atts The attributes of the shortcode.
		 *
		 * @return string The modified classes
		 */
		public function add_hover_class( $classes, $sc, $atts = array() ) {

			if ( in_array( $sc, array( 'vc_row', 'vc_row_inner' ) ) ) {

				if ( ! empty( $atts['hover_anim_link'] ) ) {
					$href = vc_build_link( $atts['hover_anim_link'] );
					if ( ! empty( $href['url'] ) ) {
						$this->enqueue_styles();
						$classes .= ' has-hover-anim-link ';
					}
				}

				if ( ! empty( $atts['hover_anim'] ) ) {
					$this->enqueue_styles();
					$classes .= ' has-hover-anim ';
					$classes .= ' hover-anim-' . $atts['hover_anim'] . ' ';
					$classes .= ' hover-anim-shadow-' . $atts['hover_anim_shadow'] . ' ';
					$classes .= ' hover-anim-speed-' . $atts['hover_anim_speed'] . ' ';

					$guid = 'hover-anim-' . substr( md5( rand() ), 0, 6 );
					$classes .= ' ' . $guid . ' ';

					$animations_with_backgrounds = array(
						'color',
						'bottom-to-top',
						'top-to-bottom',
						'left-to-right',
						'right-to-left',
					);

					$animations_with_background_image = array(
						'image',
						'zoom',
						'zoom-tilt',
					);

					if ( in_array( $atts['hover_anim'], $animations_with_backgrounds ) ) {
						$this->css[] = '.' . $guid . ':before { background: ' . $atts['hover_anim_color'] . '}';

						if ( ! empty( $atts['hover_anim_color_pre_hover'] ) ) {
							$classes .= ' hover-anim-color-pre-hover ';
						}

					} else if ( in_array( $atts['hover_anim'], $animations_with_background_image ) ) {
						$image = wp_get_attachment_url( $atts['hover_anim_image'] );
						$this->css[] = '.' . $guid . ':before { background-image: url(' . $image . ') }';

					} else if ( 'grow' === $atts['hover_anim'] ) {
						$this->css[] = '.' . $guid . ':hover { transform: scale(' . $atts['hover_anim_scale_amount'] . ') }';

					}
					if ( 'zoom-tilt' === $atts['hover_anim'] || 'zoom' === $atts['hover_anim'] ) {
						$this->css[] = '.' . $guid . ':hover:before { opacity: ' . $atts['hover_anim_zoom_opacity'] . ' }';
					}
					if ( 'zoom-tilt' === $atts['hover_anim'] ) {
						$this->css[] = '.' . $guid . ':hover:before { transform: scale(1.1) rotate(' . (float)$atts['hover_anim_zoom_tilt_degrees'] . 'deg) !important; }';
					}
				}

			} else if ( in_array( $sc, $this->supported_vc_shortcodes() ) ) {

				// Element animations.
				if ( ! empty( $atts['hover_anim'] ) ) {
					$this->enqueue_styles();

					$classes .= ' has-hover-elem-anim ';
					$classes .= ' hover-elem-anim-' . $atts['hover_anim'] . ' ';
					$classes .= ' hover-elem-anim-speed-' . $atts['hover_anim_speed'] . ' ';

					$guid = 'hover-anim-' . substr( md5( rand() ), 0, 6 );
					$classes .= ' ' . $guid . ' ';

					// Animation delay.
					if ( ! empty( $atts['hover_anim_delay'] ) ) {
						if ( '0' !== $atts['hover_anim_delay'] ) {
							$this->css[] = '.vc_row:hover .' . $guid . ', .wpb_row:hover .' . $guid . ' { transition-delay: ' . $atts['hover_anim_delay'] . 'ms !important }';
						}
					}

					// All movement animations.
					$movement_animations = array(
						'move-up',
						'move-down',
						'move-left',
						'move-right',
					);

					// Animation styles.
					$animation = $atts['hover_anim'];
					if ( 'color' === $animation ) {
						$this->css[] = '.vc_row:hover > * > * > * > .' . $guid . ', .wpb_row:hover > * > * > * > .' . $guid . ' { color: ' . $atts['hover_anim_color'] . ' !important }';
						$this->css[] = '.vc_row:hover > * > * > * > .' . $guid . ' *, .wpb_row:hover > * > * > * > .' . $guid . ' * { color: ' . $atts['hover_anim_color'] . ' !important; }';

					} else if ( 'grow' === $animation ) {
						$this->css[] = '.vc_row:hover > * > * > * > .' . $guid . ', .wpb_row:hover > * > * > * > .' . $guid . ' { transform: scale(' . $atts['hover_anim_scale'] . ') }';

					} else if ( in_array( $animation, $movement_animations ) ) {
						$value = $atts['hover_anim_move'];
						if ( empty( $value ) ) {
							$value = '10';
						}
						$translate = 'X';
						if ( 'move-up' === $animation ||
							'move-down' === $animation ) {
							$translate = 'Y';
						}
						if ( 'move-up' === $animation ||
							'move-left' === $animation ) {
							$value = -(float) $value;
						}

						if ( ! empty( $atts['hover_anim_move_hide_start'] ) ) {
							$this->css[] = '.vc_row > * > * > * > .' . $guid . ', .wpb_row > * > * > * > .' . $guid . ' { opacity: 0 }';
							$this->css[] = '.vc_row:hover > * > * > * > .' . $guid . ', .wpb_row:hover > * > * > * > .' . $guid . ' { opacity: 1 }';
						}
						if ( ! empty( $atts['hover_anim_move_hide_end'] ) ) {
							$this->css[] = '.vc_row > * > * > * > .' . $guid . ', .wpb_row > * > * > * > .' . $guid . ' { opacity: 1 }';
							$this->css[] = '.vc_row:hover > * > * > * > .' . $guid . ', .wpb_row:hover > * > * > * > .' . $guid . ' { opacity: 0 }';
						}

						if ( ! empty( $atts['hover_anim_move_before'] ) ) {
							$this->css[] = sprintf( '.vc_row:hover > * > * > * > .%s, .wpb_row:hover > * > * > * > %s { transform: translate%s(0px) }',
						 		$guid, $guid, $translate );
							$this->css[] = sprintf( '.vc_row > * > * > * > .%s, .wpb_row > * > * > * > %s { transform: translate%s(%spx) }',
						 		$guid, $guid, $translate, $value );

						} else {

							$this->css[] = sprintf( '.vc_row > * > * > * > .%s, .wpb_row > * > * > * > %s { transform: translate%s(0px) }',
						 		$guid, $guid, $translate );
							$this->css[] = sprintf( '.vc_row:hover > * > * > * > .%s, .wpb_row:hover > * > * > * > %s { transform: translate%s(%spx) }',
						 		$guid, $guid, $translate, $value );
						}
					}
				}
			}

			return $classes;
		}

		/**
		 * Prints out the necessary styles & scripts to load our fonts.
		 */
		public function add_styles() {
			if ( count( $this->css ) ) {
				echo '<style>';
				echo 'body:not(.hover-animation-loaded) .has-hover-anim:before, body:not(.hover-animation-loaded) .has-hover-elem-anim { display: none; }';
				echo implode( ' ', array_values( $this->css ) );
				echo '</style>';
				?>
				<script>
				( function() {
					var ready = function() {

						// Assign overflow hidden on rows with hover elements.
						var elements = document.querySelectorAll( '.vc_row .has-hover-elem-anim, .wpb_row .has-hover-elem-anim' );
						Array.prototype.forEach.call( elements, function( el ) {
							var row = el.parentNode;
							while ( row && ! row.classList.contains( 'vc_row' ) && ! row.classList.contains( 'wpb_row' ) ) {
								row = row.parentNode;
							}
							if ( row ) {
								row.classList.add( 'has-hover-elem-child' );
							}
						} );

						// On iOS, hover elements do not work unless they have an onclick attribute.
						if ( window.innerWidth <= 900 ) {
							elements = document.querySelectorAll( '.has-hover-elem-child, .has-hover-elem-anim' );
							Array.prototype.forEach.call( elements, function( el ) {
								if ( ! el.getAttribute( 'onclick' ) ) {
									el.setAttribute( 'onclick', '' );
								}
							} );
						}

						// Unhide animated elements.
						document.body.classList.add( 'hover-animation-loaded' );

					};
					if ( 'loading' !== document.readyState ) {
						ready();
					} else {
						document.addEventListener( 'DOMContentLoaded', ready );
					}
				} )();
				</script>
				<?php
			} // End if().
		}

		/**
		 * Load our hover animation styles.
		 */
		public function enqueue_styles() {
			wp_enqueue_style( __CLASS__, plugins_url( 'hover-animations/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_HOVER_ANIM );
		}
	}

	new HoverAnimForVC();
} // End if().
