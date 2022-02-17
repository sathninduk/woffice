<?php
/**
 * Loupe main functionality class.
 *
 * @package Loupe Magnifying Glass for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Initializes plugin class.
if ( ! class_exists( 'GambitLoupeShortcode' ) ) {

	/**
	 * This is where all the plugin's functionality happens.
	 */
	class GambitLoupeShortcode {

		/**
		 * Sets individual identifiers for the loupe.
		 *
		 * @var int $loupe_id
		 */
		private static $loupe_id = 1;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialize as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcode' ), 999 );

			// Makes the plugin function accessible as a shortcode.
			add_shortcode( 'image_loupe', array( $this, 'render_shortcode' ) );

			// TODO Add more necessary codes here. Delete when done, or if there is none to add.
		}


		/**
		 * Creates the loupe element inside VC
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_shortcode() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'name' => __( 'Loupe Magnifying Glass', 'loupe' ),
				'base' => 'image_loupe',
				'icon' => plugins_url( '/loupe/images/Loupe_Element_Icon.svg', __FILE__ ),
				'description' => __( 'An image with a loupe magnifying glass', 'loupe' ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', 'loupe' ) : '',
				'admin_enqueue_css' => plugins_url( 'loupe/css/admin.css', __FILE__ ),
				'params' => array(
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Loupe Movement Type', 'loupe' ),
						'param_name' => 'movement',
						'value' => array(
							__( 'Click and Drag', 'loupe' ) => 'draggable',
							__( 'Follow Mouse (hover)', 'loupe' ) => 'hover',
						),
						'description' => __( 'Configure how the loupe behaves with pointing devices like the mouse.<br />Make the loupe element draggable or let it follow your mouse pointer.', 'loupe' ),
					),
					array(
						'type' => 'attach_image',
						'holder' => 'span',
						'heading' => __( 'Full Sized Image', 'loupe' ),
						'param_name' => 'image_id',
						'value' => '',
						'description' => __( 'Select your full sized image. Your image will be displayed as <strong>full sized</strong> inside the loupe magnifying glass. The same image will be scaled down to match the width of the container behind the loupe.', 'loupe' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Loupe Magnifying Glass X Position (Horizontal Position)', 'loupe' ),
						'param_name' => 'x',
						'value' => '25',
						'description' => __( 'The <strong>percentage</strong> x / horizontal position of the loupe on your image (from the left side of your image). The value should be between 0 to 100.', 'loupe' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Loupe Magnifying Glass Y Position (Vertical Position)', 'loupe' ),
						'param_name' => 'y',
						'value' => '25',
						'description' => __( 'The <strong>percentage</strong> y / vertical position of the loupe on your image (from the top of your image). The value should be between 0 to 100.', 'loupe' ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Loupe Shape', 'loupe' ),
						'param_name' => 'shape',
						'value' => array(
							__( 'Circle', 'loupe' ) => 'circle',
							__( 'Square', 'loupe' ) => 'square',
						),
						'description' => __( 'The shape of your loupe magnifying glass.', 'loupe' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Loupe Size', 'loupe' ),
						'param_name' => 'size',
						'value' => '200',
						'description' => __( 'The size of your loupe in pixels.', 'loupe' ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Hide Shadow', 'loupe' ),
						'param_name' => 'hide_shadow',
						'value' => array(
							__( 'If checked, the loupe magnifying glass will <strong>NOT</strong> have shadows.', 'loupe' ) => '1',
						),
						'description' => '',
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Display Shine', 'loupe' ),
						'param_name' => 'shine',
						'value' => array(
							__( 'If checked, the loupe magnifying glass have a slight shine to it.', 'loupe' ) => 'shine',
						),
						'description' => '',
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Loupe Border Size', 'loupe' ),
						'param_name' => 'border_size',
						'value' => '0',
						'description' => __( 'The thickness of the border of the loupe in pixels.', 'loupe' ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Loupe Border Color', 'loupe' ),
						'param_name' => 'border_color',
						'value' => 'rgba(255, 255, 255, .65)',
						'description' => __( 'The border color of the loupe, note that you can also change the opacity.', 'loupe' ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Loupe Out-of-Bounds Background Color', 'loupe' ),
						'param_name' => 'background_color',
						'value' => '#ffffff',
						'description' => __( 'The out-of-bounds background color. This is visible when the loupe goes past the image.', 'loupe' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Magnification Scaling', 'loupe' ),
						'param_name' => 'scaling',
						'value' => '100',
						'description' => __( 'Enter the scaling value (in percentage) of the magnified image. Always defaults to 100.', 'loupe' ),
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
		 * Converts a hex color to rgb values.
		 *
		 * @param string $hex - The hexadecimal color valuess.
		 * @return	array - the rgb colors
		 * @since	1.0
		 */
		private function hex2rgb( $hex ) {
			$hex = str_replace( '#', '', $hex );

			if ( strlen( $hex ) === 3 ) {
				$r = hexdec( substr( $hex,0,1 ) . substr( $hex,0,1 ) );
				$g = hexdec( substr( $hex,1,1 ) . substr( $hex,1,1 ) );
				$b = hexdec( substr( $hex,2,1 ) . substr( $hex,2,1 ) );
			} else {
				$r = hexdec( substr( $hex,0,2 ) );
				$g = hexdec( substr( $hex,2,2 ) );
				$b = hexdec( substr( $hex,4,2 ) );
			}
			$rgb = array( $r, $g, $b );

			// Returns an array with the rgb values.
			return $rgb;
		}

		/**
		 * Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function render_shortcode( $atts, $content = null ) {
			$defaults = array(
				'image_id' => '',
				'movement' => 'draggable',
				'shape' => 'circle',
				'size' => '200',
				'hide_shadow' => '',
				'shine' => '',
				'border_size' => '0',
				'border_color' => '#ffffff',
				'background_color' => '#ffffff',
				'x' => '25',
				'y' => '25',
				'scaling' => '100',
				'el_class' => '',
			);

			if ( empty( $atts ) ) {
				$atts = array();
			}

			$atts = array_merge( $defaults, $atts );

			$id = self::$loupe_id++;

			$has_shadow = '';

			wp_enqueue_style( 'vc-loupe', plugins_url( 'loupe/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_LOUPE );
			wp_enqueue_script( 'vc-loupe', plugins_url( 'loupe/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_LOUPE, true );

			if ( empty( $atts['image_id'] ) ) {
				return '';
			}

			// Jetpack issue, Photon is not giving us the image dimensions.
			// This snippet gets the dimensions for us.
			add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
			$image_info = wp_get_attachment_image_src( $atts['image_id'], 'full' );
			remove_filter( 'jetpack_photon_override_image_downsize', '__return_true' );

			$image = wp_get_attachment_image_src( $atts['image_id'], 'full' );
			$image_url = esc_url( $image[0] );
			$width = esc_attr( $image_info[1] );
			$height = esc_attr( $image_info[2] );

			$ret = '';

			if ( (int) $atts['border_size'] > 0 ) {
				$border_size = 'solid ' . esc_attr( $atts['border_size'] ) . 'px ' . esc_attr( $atts['border_color'] );
			} else {
				$border_size = '0';
			}

			if ( '1' !== $atts['hide_shadow'] ) {
				$has_shadow = 'has-shadow';
			}

			$ret .= "<style>
				#gambit-loupe{$id} .gambit-loupe-glass {
					border: " . esc_attr( $border_size ) . ';
					height: ' . esc_attr( $atts['size'] ) . 'px;
					width: ' . esc_attr( $atts['size'] ) . 'px;
					box-sizing: border-box;
					background-color: ' . esc_attr( $atts['border_color'] ) . ";
				}
				#gambit-loupe{$id} .gambit-loupe-glass > div {
					background-color: " . esc_attr( $atts['background_color'] ) . ';
				}
			</style>';

			$ret .= "<div id='gambit-loupe{$id}' class='gambit-loupe-container wpb_content_element " . esc_attr( $atts['el_class'] ) . "'>";

			$ret .= "<img class='gambit-loupe-bg' src='{$image_url}' data-orig-width='{$width}' data-orig-height='{$height}'/>";

			$ret .= "<div class='gambit-loupe-glass " . $has_shadow . ' ' . esc_attr( $atts['shape'] ) . ' ' . esc_attr( $atts['shine'] ) . "' data-movement='" . esc_attr( $atts['movement'] ) . "' data-loupe-id='" . esc_attr( $id ) . "' data-x='" . esc_attr( $atts['x'] ) . "%' data-y='" . esc_attr( $atts['y'] ) . "%'><div class='gambit-loupe-magnified-image' data-scaling='" . esc_attr( $atts['scaling'] ) . "' style='background-image: url({$image_url})'></div></div>";

			$ret .= '</div>';

			return $ret;
		}
	}
	new GambitLoupeShortcode();
} // End if().
