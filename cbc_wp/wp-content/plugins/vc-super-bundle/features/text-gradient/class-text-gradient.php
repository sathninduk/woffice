<?php
/**
 * The text gradient functionalities are located here.
 *
 * @package Text Gradient for Visual Composer
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'TextGradientForVC' ) ) {

	/**
	 * The class that does the functions.
	 */
	 class TextGradientForVC {

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

		public $gradient_id = 1;

 		/**
 		 * Hook into WordPress.
 		 *
 		 * @return void.
 		 * @since 1.0
 		 */

		 function __construct() {

			 // Add the text gradient parameters to shortcodes.
			 add_action( 'init', array( $this, 'add_text_gradient_param' ), 999 );

			 // Add the font class to the shortcode outputs.
			add_filter( 'vc_shortcodes_css_class', array( $this, 'add_text_gradient_class' ), 999, 3 );

			// Print out the styles in the footer.
			add_action( 'wp_footer', array( $this, 'add_text_gradient_styles' ) );
		 }

		 /**
		  * Adds the text gradient to VC elements.
		  */
		  public function add_text_gradient_param() {
			  if ( ! function_exists( 'vc_add_params' ) ) {
				  return;
			  }

			  $attributes = array(
				  array(
					  'type' => 'colorpicker',
					  'param_name' => 'grad_text_color1',
					  'heading' => __( 'First Color', GAMBIT_TEXT_GRADIENT ),
					  'group' => __( 'Gradient', GAMBIT_TEXT_GRADIENT ),
				  ),
				  array(
					  'type' => 'colorpicker',
					  'param_name' => 'grad_text_color2',
					  'heading' => __( 'Second Color', GAMBIT_TEXT_GRADIENT ),
					  'group' => __( 'Gradient', GAMBIT_TEXT_GRADIENT ),
				  ),
				 array(
					 'type' => 'colorpicker',
					 'param_name' => 'grad_text_color3',
					 'heading' => __( 'Third Color', GAMBIT_TEXT_GRADIENT ),
					 'group' => __( 'Gradient', GAMBIT_TEXT_GRADIENT ),
				 ),
				 array(
					 'type' => 'textfield',
					 'param_name' => 'deg',
					 'heading' => __( 'Gradient Direction', GAMBIT_TEXT_GRADIENT ),
					 'description' => __( 'Enter a number from 0 to 360.', GAMBIT_TEXT_GRADIENT ),
					 'value' => '270',
					 'group' => __( 'Gradient', GAMBIT_TEXT_GRADIENT ),
				 ),
			  );


			  // These are all the shortcodes we will add the gradients to.
			  vc_add_params( 'vc_column_text', $attributes );

		  }

	  /**
		* Adds the special class to the affected VC elements.
		*
		* @param string $classes The current classes of the element.
		* @param object $sc The shortcode object.
		* @param object $atts The attributes of the shortcode.
		*
		* @return string The modified classes
		*/
		public function add_text_gradient_class( $classes, $sc, $atts = array() ) {

			$gradient_colors = array();
			if ( ! empty( $atts[ 'grad_text_color1' ] ) ) {
				$gradient_colors[] = $atts[ 'grad_text_color1' ];
			}
			if ( ! empty( $atts[ 'grad_text_color2' ] ) ) {
				$gradient_colors[] = $atts[ 'grad_text_color2' ];
			}
			if ( ! empty( $atts[ 'grad_text_color3' ] ) ) {
				$gradient_colors[] = $atts[ 'grad_text_color3' ];
			}

			if ( empty( $gradient_colors ) ) {
				return $classes;
			}

			$deg = ! empty ( $atts[ 'deg' ] ) ? preg_replace( '/[^0-9]/','', $atts[ 'deg' ] ) . 'deg' : '0deg';
			$id = implode( ', ', $gradient_colors ) . ', ' . $deg;
			$colorClass = 'cg_' . substr( md5( $id ), 0, 8 );

			if ( count( $gradient_colors ) === 1 ) {
				$css = 'color: ' . $gradient_colors[0] . ';';
			} else {
				$css = 'color: ' . $gradient_colors[0] . ';';
				$css .= 'background: -webkit-linear-gradient(' . $deg . ', ' . implode( ', ', $gradient_colors ) . ');';
				$css .= '-webkit-background-clip: text;';
				$css .= '-webkit-text-fill-color: transparent;';
				$css .= 'display: inline-block;';
			}

		 	$this->css[ $colorClass ] = '.' . $colorClass . ' > * > *, .' . $colorClass . ' .cg_wrapper > * { ' . $css . '}';
		 	return $classes . ' ' . $colorClass . ' cg_color_gradient';
		}


		public function add_text_gradient_styles() {

			if ( count( $this->css ) ) {
				echo '<style>';
				echo implode( ' ', array_values( $this->css ) );
				echo '</style>';
			}
			wp_enqueue_script( 'text-gradient', plugins_url( 'script.js', __FILE__ ), array(), GAMBIT_TEXT_GRADIENT, true );
		}

	 }

	 new TextGradientForVC();
}
