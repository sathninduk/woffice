<?php
/**
 * The CSS Animator file.
 *
 * @version 1.7
 * @package CSS Animator for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'GambitVCCSSAnimations' ) ) {

	/**
	 * CSS Animation Class.
	 *
	 * @since	1.0
	 */
	class GambitVCCSSAnimations {

		/**
		 * Used for loading stuff only once during a page load.
		 *
		 * @var int $firstload - Indicates if loaded for the first time.
		 */
		private static $first_load = 0;

		/**
		 * Placeholder for the animation roster.
		 *
		 * @var string $animations - Holds the value.
		 */
		private $animations;

		const COMPATIBILITY_MODE = '_gambit_css_animator_compat_mode';

		/**
		 * A blacklist of what CSS Animator will not process.
		 *
		 * @var array $ignore_elements - Any value that matches here will not have the animations apply.
		 */
		private $ignore_elements = array(
			'vc_column',
			'vc_row',
		);

		/**
		 * Constructor, checks for Visual Composer and defines hooks.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initializaes the admin requires for the plugin.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Initialize as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_animation_element' ), 999 );

			// Initializes shortcode.
			add_shortcode( 'css_animation', array( $this, 'css_animation_shortcode' ) );

			// Add a compatibility mode toggler.
			add_filter( 'plugin_row_meta', array( $this, 'add_compatibility_mode_toggle' ), 11, 2 );
			add_action( 'admin_init', array( $this, 'toggle_compatibility_mode' ) );

			// Creates animation array needed for selection.
			$this->form_animation_array();

		}


		/**
		 * Includes admin scripts and styles needed.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'css_animation', plugins_url( '/css-animator/css/admin.css', __FILE__ ), false, VERSION_GAMBIT_VC_CSS_ANIMATIONS );
		}


		/**
		 * Creates the animation selection roster.
		 *
		 * @return	void
		 * @since	1.0
		 */
		private function form_animation_array() {
			$this->animations = array(
				__( '- Entrance Animations -', GAMBIT_VC_CSS_ANIMATIONS ) => '',
				__( 'Fade in', GAMBIT_VC_CSS_ANIMATIONS ) => 'fade-in',
				__( 'Flip top to bottom 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'flip-3d-to-bottom',
				__( 'Flip bottom to top 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'flip-3d-to-top',
				__( 'Flip right to left 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'flip-3d-to-left',
				__( 'Flip left to right 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'flip-3d-to-right',
				__( 'Flip in horizontally 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'flip-3d-horizontal',
				__( 'Flip in vertically 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'flip-3d-vertical',
				__( 'Fall bottom to top 3D', GAMBIT_VC_CSS_ANIMATIONS ) => 'fall-3d-to-top',
				__( 'Fall top to bottom 3D', GAMBIT_VC_CSS_ANIMATIONS )           => 'fall-3d-to-bottom',
				__( 'Roll bottom to top 3D', GAMBIT_VC_CSS_ANIMATIONS )           => 'roll-3d-to-top',
				__( 'Roll right to left 3D', GAMBIT_VC_CSS_ANIMATIONS )           => 'roll-3d-to-left',
				__( 'Roll left to right 3D', GAMBIT_VC_CSS_ANIMATIONS )           => 'roll-3d-to-right',
				__( 'Rotate in top left 2D', GAMBIT_VC_CSS_ANIMATIONS )           => 'rotate-in-top-left',
				__( 'Rotate in top right 2D', GAMBIT_VC_CSS_ANIMATIONS )          => 'rotate-in-top-right',
				__( 'Rotate in bottom left 2D', GAMBIT_VC_CSS_ANIMATIONS )        => 'rotate-in-bottom-left',
				__( 'Rotate in bottom right 2D', GAMBIT_VC_CSS_ANIMATIONS )       => 'rotate-in-bottom-right',
				__( 'Slide top to bottom 3D', GAMBIT_VC_CSS_ANIMATIONS )          => 'slide-to-bottom',
				__( 'Slide bottom to top 3D', GAMBIT_VC_CSS_ANIMATIONS )          => 'slide-to-top',
				__( 'Slide right to left 3D', GAMBIT_VC_CSS_ANIMATIONS )          => 'slide-to-left',
				__( 'Slide left to right 3D', GAMBIT_VC_CSS_ANIMATIONS )          => 'slide-to-right',
				__( 'Slide elastic bottom to top 2D', GAMBIT_VC_CSS_ANIMATIONS )     => 'slide-elastic-to-top',
				__( 'Slide elastic top to bottom 2D', GAMBIT_VC_CSS_ANIMATIONS )     => 'slide-elastic-to-bottom',
				__( 'Slide elastic right to left 2D', GAMBIT_VC_CSS_ANIMATIONS )     => 'slide-elastic-to-left',
				__( 'Slide elastic left to right 2D', GAMBIT_VC_CSS_ANIMATIONS )     => 'slide-elastic-to-right',
				__( 'Grow 2D', GAMBIT_VC_CSS_ANIMATIONS )                         => 'size-grow-2d',
				__( 'Shrink 2D', GAMBIT_VC_CSS_ANIMATIONS )                       => 'size-shrink-2d',
				__( 'Spin 2D', GAMBIT_VC_CSS_ANIMATIONS )                         => 'spin-2d',
				__( 'Spin 2D reverse', GAMBIT_VC_CSS_ANIMATIONS )                 => 'spin-2d-reverse',
				__( 'Spin 3D', GAMBIT_VC_CSS_ANIMATIONS )                         => 'spin-3d',
				__( 'Spin 3D reverse', GAMBIT_VC_CSS_ANIMATIONS )                 => 'spin-3d-reverse',
				__( 'Twirl top left 3D', GAMBIT_VC_CSS_ANIMATIONS )               => 'twirl-3d-top-left',
				__( 'Twirl top right 3D', GAMBIT_VC_CSS_ANIMATIONS )              => 'twirl-3d-top-right',
				__( 'Twirl bottom left 3D', GAMBIT_VC_CSS_ANIMATIONS )            => 'twirl-3d-bottom-left',
				__( 'Twirl bottom right 3D', GAMBIT_VC_CSS_ANIMATIONS )           => 'twirl-3d-bottom-right',
				__( 'Twirl 3D', GAMBIT_VC_CSS_ANIMATIONS )                        => 'twirl-3d',
				__( 'Unfold top to bottom 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'unfold-3d-to-bottom',
				__( 'Unfold bottom to top 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'unfold-3d-to-top',
				__( 'Unfold right to left 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'unfold-3d-to-left',
				__( 'Unfold left to right 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'unfold-3d-to-right',
				__( 'Unfold horzitonal 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'unfold-3d-horizontal',
				__( 'Unfold vertical 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'unfold-3d-vertical',
				// __( 'Three unfold top to bottom 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'three_unfold-3d-to-bottom',
				// __( 'Three unfold bottom to top 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'three_unfold-3d-to-top',
				// __( 'Three unfold right to left 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'three_unfold-3d-to-left',
				// __( 'Three unfold left to right 3D', GAMBIT_VC_CSS_ANIMATIONS )                 => 'three_unfold-3d-to-right',
				__( '- Looped Animations -', GAMBIT_VC_CSS_ANIMATIONS )   => '',
				__( 'Pulsate', GAMBIT_VC_CSS_ANIMATIONS )                         => 'loop-pulsate',
				__( 'Pulsate fade', GAMBIT_VC_CSS_ANIMATIONS )                    => 'loop-pulsate-fade',
				__( 'Hover', GAMBIT_VC_CSS_ANIMATIONS )                           => 'loop-hover',
				__( 'Hover floating', GAMBIT_VC_CSS_ANIMATIONS )                  => 'loop-hover-float',
				__( 'Wobble', GAMBIT_VC_CSS_ANIMATIONS )                          => 'loop-wobble',
				__( 'Wobble 3D', GAMBIT_VC_CSS_ANIMATIONS )                       => 'loop-wobble-3d',
				__( 'Dangle', GAMBIT_VC_CSS_ANIMATIONS )                          => 'loop-dangle',
			);
		}


		/**
		 * Creates our shortcode settings in Visual Composer.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_animation_element() {

			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			// We need to define this so that VC will show our nesting container correctly.
			cssa_row_fixes();

			vc_map( array(
			    'name' => __( 'CSS Animator', GAMBIT_VC_CSS_ANIMATIONS ),
			    'base' => 'css_animation',
			    'as_parent' => array( 'except' => 'css_animation' ),
			    'content_element' => true,
				'icon' => plugins_url( '/css-animator/images/CSS-Animator_Element_Icon.svg', __FILE__ ),
			    'js_view' => 'VcColumnView',
				'description' => __( 'Add animations to your elements', GAMBIT_VC_CSS_ANIMATIONS ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_CSS_ANIMATIONS ) : '',
			    'params' => array(
					array(
						'type' => 'dropdown',
						'heading' => __( 'Functionality', GAMBIT_VC_CSS_ANIMATIONS ),
						'param_name' => 'enable_animator',
						'value' => array(
							__( 'All devices', GAMBIT_VC_CSS_ANIMATIONS ) => 'all',
							__( 'Disabled in mobile', GAMBIT_VC_CSS_ANIMATIONS ) => 'nomobile',
						),
						'description' => __( 'Select whether to have CSS Animator work in all devices, or disable on mobile devices.', GAMBIT_VC_CSS_ANIMATIONS ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'CSS Animation', 'js_composer' ),
						'param_name' => 'animation',
						'value' => array_merge( array( __( 'None', 'js_composer' ) => 'none' ), $this->animations ),
						'description' => __( 'Select type of animation if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.', 'js_composer' ),
						'std' => 'fade-in',
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animation Duration', GAMBIT_VC_CSS_ANIMATIONS ),
						'param_name' => 'duration',
						'value' => '',
						'description' => __( 'Duration in seconds. You can use decimal points in the value. Use this field to specify the amount of time the animation plays. <em>The default value depends on the animation, leave blank to use the default.</em>', GAMBIT_VC_CSS_ANIMATIONS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animation Delay', GAMBIT_VC_CSS_ANIMATIONS ),
						'param_name' => 'delay',
						'value' => '',
						'description' => __( 'Delay in seconds. You can use decimal points in the value. Use this field to delay the animation for a few seconds, this is helpful if you want to chain different effects one after another above the fold.', GAMBIT_VC_CSS_ANIMATIONS ),
					),
			        array(
			            'type' => 'textfield',
			            'heading' => __( 'Extra class name', 'js_composer' ),
			            'param_name' => 'el_class',
			            'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' ),
			        ),
			    ),
			) );
		}


		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any. Normally null.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function css_animation_shortcode( $atts, $content = null ) {
			$defaults = array(
				'el_class' => '',
				'animation' => 'fade-in',
				'duration' => '',
				'delay' => '',
				'enable_animator' => 'all',
			);

			$ret = '';

			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			if ( 'none' === $atts['animation'] ) {
				$atts['animation'] = '';
			}

			if ( empty( $atts['animation'] ) ) {
				return do_shortcode( $content );
			}

			// Enqueue the animation script.
			$anim_group = substr( $atts['animation'], 0, stripos( $atts['animation'], '-' ) );
			wp_enqueue_style( 'vc-css-animation-' . $anim_group, plugins_url( '/css-animator/css/' . $anim_group . '.css', __FILE__ ), false, VERSION_GAMBIT_VC_CSS_ANIMATIONS );

			if ( false === get_option( self::COMPATIBILITY_MODE ) ) {
				wp_enqueue_script( 'vc-css-animation-script', plugins_url( '/css-animator/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_CSS_ANIMATIONS, true );
			} else {
				wp_enqueue_script( 'vc-css-animation-script-compat', plugins_url( '/css-animator/js/min/script-compat-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_CSS_ANIMATIONS, true );
			}

			// Waypoints is needed to trigger the animations.
			wp_enqueue_script( 'waypoints' );
			wp_enqueue_script( 'vc_waypoints' ); // VC 6.x changed the name.

			// Set default values.
			$styles = array();
			if ( '0' != $atts['duration'] && ! empty( $atts['duration'] ) ) {
				$atts['duration'] = (float) trim( $atts['duration'], "\n\ts" );
				$styles[] = "-webkit-animation-duration: {$atts['duration']}s";
				$styles[] = "-moz-animation-duration: {$atts['duration']}s";
				$styles[] = "-ms-animation-duration: {$atts['duration']}s";
				$styles[] = "-o-animation-duration: {$atts['duration']}s";
				$styles[] = "animation-duration: {$atts['duration']}s";
				$styles[] = "-webkit-transition-duration: {$atts['duration']}s";
				$styles[] = "-moz-transition-duration: {$atts['duration']}s";
				$styles[] = "-ms-transition-duration: {$atts['duration']}s";
				$styles[] = "-o-transition-duration: {$atts['duration']}s";
				$styles[] = "transition-duration: {$atts['duration']}s";
			}

			// Delay all animations by 0.1. In some cases, animations may not play when the delay is 0.
			if ( '0' === $atts['delay'] || empty( $atts['delay'] ) ) {
				$atts['delay'] = 0.1;
			} else {
				$atts['delay'] = (float) $atts['delay'] + 0.1;
			}

			if ( '0' !== $atts['delay'] && ! empty( $atts['delay'] ) ) {
				$atts['delay'] = (float) trim( $atts['delay'], "\n\ts" );
				$styles[] = 'opacity: 0';
				$styles[] = "-webkit-animation-delay: {$atts['delay']}s";
				$styles[] = "-moz-animation-delay: {$atts['delay']}s";
				$styles[] = "-ms-animation-delay: {$atts['delay']}s";
				$styles[] = "-o-animation-delay: {$atts['delay']}s";
				$styles[] = "animation-delay: {$atts['delay']}s";
				$styles[] = "-webkit-transition-delay: {$atts['delay']}s";
				$styles[] = "-moz-transition-delay: {$atts['delay']}s";
				$styles[] = "-ms-transition-delay: {$atts['delay']}s";
				$styles[] = "-o-transition-delay: {$atts['delay']}s";
				$styles[] = "transition-delay: {$atts['delay']}s";
			}
			$styles = implode( ';', $styles );

			if ( preg_match( '/^unfold-/', $atts['animation'] ) ) {
				$ret .= "<div data-enable_animator='" . $atts['enable_animator'] . "' class='wpb_animate_when_almost_visible gambit-css-animation " . $atts['animation'] . ' ' . $atts['el_class'] . "' style='$styles'><div class='unfolder-container right' style='$styles'><div class='unfolder-content'>" . do_shortcode( $content ) . "</div></div><div class='unfolder-container left' style='$styles'><div class='unfolder-content'>" . do_shortcode( $content ) . "</div></div><div class='real-content' style='$styles'>" . do_shortcode( $content ) . '</div></div>';
			} elseif ( preg_match( '/^three-unfold-/', $atts['animation'] ) ) {
				$ret .= "<div data-enable_animator='" . $atts['enable_animator'] . "' class='wpb_animate_when_almost_visible gambit-css-animation " . $atts['animation'] . ' ' . $atts['el_class'] . "' style='$styles'><div class='unfolder-container top left' style='$styles'><div class='unfolder-content'>" . do_shortcode( $content ) . "</div></div><div class='unfolder-container mid' style='$styles'><div class='unfolder-content'>" . do_shortcode( $content ) . "</div></div><div class='unfolder-container bottom right' style='$styles'><div class='unfolder-content'>" . do_shortcode( $content ) . "</div></div><div class='real-content' style='$styles'>" . do_shortcode( $content ) . '</div></div>';
			} else {
				$ret .= "<div data-enable_animator='" . $atts['enable_animator'] . "' class='wpb_animate_when_almost_visible gambit-css-animation " . $atts['animation'] . ' ' . $atts['el_class'] . "' style='$styles'>" . do_shortcode( $content ) . '</div>';
			}
			return $ret;
		}


		/**
		 * Adds an enabled/disable link for toggling compatiblity mode. Compatibility mode changes the hook so that the plugin will work in impractical situations where VC is embedded into a theme.
		 *
		 * @access	public
		 * @param	array  $plugin_meta The current array of links.
		 * @param	string $plugin_file The plugin file.
		 * @return	array The current array of links together with our additions.
		 * @since	1.6
		 **/
		public function add_compatibility_mode_toggle( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) == $plugin_file ) {
				$plugin_data = get_plugin_data( __FILE__ );

				$compatibility_mode = get_option( self::COMPATIBILITY_MODE );
				$nonce = wp_create_nonce( self::COMPATIBILITY_MODE );
				if ( empty( $compatibility_mode ) ) {
					$plugin_meta[] = sprintf( "<a href='%s' target='_self'>%s</a>",
						admin_url( 'plugins.php?' . self::COMPATIBILITY_MODE . '=1&nonce=' . $nonce ),
						__( 'Enable Compatibility Mode', GAMBIT_VC_CSS_ANIMATIONS )
					);
				} else {
					$plugin_meta[] = sprintf( "<a href='%s' target='_self'>%s</a>",
						admin_url( 'plugins.php?' . self::COMPATIBILITY_MODE . '=0&nonce=' . $nonce ),
						__( 'Disable Compatibility Mode', GAMBIT_VC_CSS_ANIMATIONS )
					);
				}
			}
			return $plugin_meta;
		}


		/**
		 * Compatibility mode toggling handler.
		 *
		 * @access	public
		 * @return	void
		 * @since	1.6
		 **/
		public function toggle_compatibility_mode() {
			if ( empty( $_REQUEST['nonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::COMPATIBILITY_MODE ) ) {
				return;
			}

			if ( isset( $_REQUEST[ self::COMPATIBILITY_MODE ] ) ) {
				if ( empty( $_REQUEST[ self::COMPATIBILITY_MODE ] ) ) {
					delete_option( self::COMPATIBILITY_MODE );
				} else {
					update_option( self::COMPATIBILITY_MODE, '1' );
				}
				wp_redirect( admin_url( 'plugins.php' ) );
				die();
			}
		}
	}

	new GambitVCCSSAnimations();
}

if ( ! function_exists( 'cssa_row_fixes' ) ) {

	/**
	 * Loads the fixes that makes CSS Animator work.
	 *
	 * @return void
	 * @since 1.5
	 */
	function cssa_row_fixes() {

		$create_class = false;

		/**
		 * We need to define this so that VC will show our nesting container correctly.
		 */
		if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Css_Animation' ) ) {
			$create_class = true;
		} else {
			// If we can't detech the classes it means that VC is embeded in a theme.
			global $composer_settings;

			// The class WPBakeryShortCodesContainer is defined in VC's shortcodes.php, include it so we can define our container.
			if ( ! empty( $composer_settings ) ) {
				if ( array_key_exists( 'COMPOSER_LIB', $composer_settings ) ) {
					$lib_dir = $composer_settings['COMPOSER_LIB'];
					if ( file_exists( $lib_dir . 'shortcodes.php' ) ) {
						require_once( $lib_dir . 'shortcodes.php' );
					}
				}
			}

			// We need to define this so that VC will show our nesting container correctly.
			if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Css_Animation' ) ) {
				$create_class = true;
			}
		}

		if ( $create_class ) {

			/**
			 * Defines a subclass of the shortcodes container, for modifed Visual Composer modules.
			 *
			 * @package	CSS Animator for VC
			 * @class WPBakeryShortCode_Css_Animation
			 */
			class WPBakeryShortCode_Css_Animation extends WPBakeryShortCodesContainer {
			}
		}
	}
}
