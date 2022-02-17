<?php
/**
 * Gradient Color BG routines.
 *
 * @version 1.1
 * @package Parallax Backgrounds for VC
 */

// Initializes the Gradient BG functionality.
if ( ! class_exists( 'GambitGradientBGColor' ) ) {

	/**
	 * This is where all the actions of the colors happen.
	 */
	class GambitGradientBGColor {

		/**
		 * Uniquely identifies rendered bg.
		 *
		 * @var string $element_id - The ID used.
		 */
		public static $element_id = 1;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {
			// Initialize as a Visual Composer addon.
			add_filter( 'init', array( $this, 'create_row_shortcodes' ), 999 );

			// Makes the plugin function accessible as a shortcode.
			add_shortcode( 'gp_gradient_bg', array( $this, 'create_shortcode' ) );

			// Our admin-side scripts & styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}


		/**
		 * Includes admin scripts and styles needed.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'gambit_parallax_admin', plugins_url( 'css/admin.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );
		}


		/**
		 * Creates our shortcode settings in Visual Composer.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_row_shortcodes() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Gradient BG Color', GAMBIT_VC_PARALLAX_BG ),
				'base' => 'gp_gradient_bg',
				'icon' => plugins_url( 'parallax/images/vc-gradient.png', __FILE__ ),
				'description' => __( 'Add a gradient background color instead of the usual row color.', GAMBIT_VC_PARALLAX_BG ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_PARALLAX_BG ) : null,
				'params' => array(
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Start Color', GAMBIT_VC_PARALLAX_BG ),
						'param_name' => 'color1',
						'value' => '',
						'std' => '#7330b0',
						'admin_label' => true,
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'End Color', GAMBIT_VC_PARALLAX_BG ),
						'param_name' => 'color2',
						'value' => '',
						'std' => '#1490ec',
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Gradient Angle', GAMBIT_VC_PARALLAX_BG ),
						'param_name' => 'angle',
						'value' => '45',
						'description' => __( 'Angle of the gradient in degrees.', GAMBIT_VC_PARALLAX_BG ),
						'admin_label' => true,
					),
				),
			) );
		}


		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function create_shortcode( $atts, $content = null ) {

			$defaults = array(
				'angle' => '45',
				'color1' => '#7330b0',
				'color2' => '#1490ec',
			);

			if ( empty( $atts ) ) {
				$atts = array();
			}

			$atts = array_merge( $defaults, $atts );

			wp_enqueue_script( 'gambit_parallax', plugins_url( 'parallax/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_PARALLAX_BG, true );
			wp_enqueue_style( 'gambit_parallax', plugins_url( 'parallax/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_PARALLAX_BG );

			$output = "<div class='gambit_gradient_bg' data-color1='{$atts['color1']}' data-color2='{$atts['color2']}' data-angle='{$atts['angle']}'></div>";

			self::$element_id++;

			return $output;
		}
	}

	new GambitGradientBGColor();

}
