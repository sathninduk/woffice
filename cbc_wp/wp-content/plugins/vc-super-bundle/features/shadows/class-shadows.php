<?php
/**
 * The column shadows functionalities are located here.
 *
 * @package Column Shadows for Visual Composer
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'ColumnShadowsForVC' ) ) {

	/**
	 * The class that does the functions.
	 */
	 class ColumnShadowsForVC {

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

			 // Add the shadow parameters to shortcodes.
			 add_action( 'init', array( $this, 'add_column_shadows_param' ), 999 );

			 // Add the font class to the shortcode outputs.
		 	add_filter( 'vc_shortcodes_css_class', array( $this, 'add_column_shadows_class' ), 999, 3 );

		}

		 /**
		  * Adds the shadow option in VC elements.
		  */
		  public function add_column_shadows_param() {
			  if ( ! function_exists( 'vc_add_params' ) ) {
				  return;
			  }

			  $attributes = array(
				  array(
					  'type' => 'dropdown',
					  'param_name' => 'shadow_types',
					  'heading' => __( 'Shadow Thickness', GAMBIT_COLUMN_SHADOWS ),
					  'value' => array(
						  __( 'None', GAMBIT_COLUMN_SHADOWS ) => 'none',
						  __( 'Simple Shadow Small', GAMBIT_COLUMN_SHADOWS ) => 'small',
						  __( 'Simple Shadow Normal', GAMBIT_COLUMN_SHADOWS ) => 'normal',
						  __( 'Simple Shadow Medium', GAMBIT_COLUMN_SHADOWS ) => 'medium',
						  __( 'Simple Shadow Large', GAMBIT_COLUMN_SHADOWS ) => 'large',
						  __( 'Simple Shadow Huge', GAMBIT_COLUMN_SHADOWS ) => 'huge',
						  __( 'Fancy Bottom Tilted (Needs Background Color)', GAMBIT_COLUMN_SHADOWS ) => 'tilted',
						  __( 'Fancy Vertical (Needs Background Color)', GAMBIT_COLUMN_SHADOWS ) => 'vertical',
						  __( 'Fancy Horizontal (Needs Background Color)', GAMBIT_COLUMN_SHADOWS ) => 'horizontal',
						  __( 'Fancy Center Bottom (Needs Background Color)', GAMBIT_COLUMN_SHADOWS ) => 'center',

					  ),
					  'group' => __( 'Shadows', GAMBIT_COLUMN_SHADOWS ),
				  ),
				  array(
					  'type' => 'dropdown',
					  'param_name' => 'shadow_str',
					  'heading' => __( 'Shadow Intensity', GAMBIT_COLUMN_SHADOWS ),
					  'value' => array(
						  __( 'Low', GAMBIT_COLUMN_SHADOWS ) => '1',
						  __( 'Medium', GAMBIT_COLUMN_SHADOWS ) => '2',
						  __( 'High', GAMBIT_COLUMN_SHADOWS ) => '3',
					  ),
					  'std' => '2',
					  'group' => __( 'Shadows', GAMBIT_COLUMN_SHADOWS ),
				  ),
			  );


			  // These are all the shortcodes we will add the gradients to.
				vc_add_params( 'vc_btn', $attributes );
				vc_add_params( 'vc_row_inner', $attributes );
				vc_add_params( 'vc_column', $attributes );
				vc_add_params( 'vc_single_image', $attributes );
				vc_add_params( 'vc_column_inner', $attributes );

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
		public function add_column_shadows_class( $classes, $sc, $atts = array() ) {

			$str = '0';
			$button_class = '';
			if ( ! empty( $atts[ 'shadow_str' ] ) ) {
				$str = $atts['shadow_str'];
			}
			if ( empty( $atts[ 'shadow_types' ] ) || $atts[ 'shadow_types' ] === 'none' ) {
				return $classes;
			}

			$id = '';
			$shadow_types = array( 'none', 'small', 'normal', 'medium', 'large', 'huge', 'tilted', 'vertical', 'horizontal', 'center' );
			if ( in_array( $atts['shadow_types'], $shadow_types ) ) {
				$id .= array_search( $atts['shadow_types'], array_values($shadow_types));
			}
			$this->enqueue_styles();

			// Buttons.
			if ( strpos( $classes, 'vc_btn' ) ) {
				return $classes . ' cs-' . $id . '-' . $str;
			}
			if ( $sc === 'vc_btn' ) {
				return $classes;
			} else if ( $sc === 'vc_column' || $sc === 'vc_column_inner' ) {
				return $classes . ' cs-' . $id . '-' . $str;
			} else if ( $sc === 'vc_single_image' ) {
				return $classes . ' cs-' . $id . '-' . $str;
			}
			return $classes;
		}

		/**
		 * Load our shadows styles.
		 */
		public function enqueue_styles() {
			wp_enqueue_style( __CLASS__, plugins_url( 'shadows/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_COLUMN_SHADOWS );
		}

	 }

	 new ColumnShadowsForVC();
}
