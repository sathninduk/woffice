<?php
/**
 * File with all the plugin functionality.
 *
 * @package Before and After Slider for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
// The class name of the plugin.
if ( ! class_exists( 'GambitBeforeAndAfterShortcode' ) ) {

	/**
	 * The main class of the plugin where all the action happens.
	 */
	class GambitBeforeAndAfterShortcode {

		/**
		 * Identifier for all rendered elements. Incremented by the shortcode function.
		 *
		 * @var int $element_id - Increments and serves as unique identifier.
		 */
	    static $element_id = 1;

		/**
		 * Used for loading stuff only once during a page load.
		 *
		 * @var int $first_load - Indicator if plugin is loaded for the first time.
		 */
	    private static $first_load = 0;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialization as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcode' ), 999 );

			// Creates a shortcode (or a VC addon that uses a shortcode).
			add_shortcode( 'before_after', array( $this, 'render_shortcode' ) );
			add_shortcode( 'gambit_before_after', array( $this, 'render_shortcode' ) );
		}


		/**
		 * Converts our word-based rule into padding CSS rules.
		 *
		 * @param string $in_x - The option for X coordinates.
		 * @param string $in_y - The option for Y coordinates.
		 * @param string $val - The padding value, in pixels.
		 * @return string - The padding CSS rule.
		 * @since	1.0
		 */
		function word_to_padding( $in_x, $in_y, $val ) {
			$ret = 'padding: ' . $val . 'px;';
			// switch ( $in_y ) {
			// 	case 'top' :
			// 		$ret = 'padding-top: ' . $val . 'px;';
			// 	break;
			// 	case 'bottom' :
			// 		$ret = 'padding-bottom: ' . $val . 'px;';
			// 	break;
			// 	default :
			// 		$ret = '';
			// 	break;
			// }
			// switch ( $in_x ) {
			// 	case 'right' :
			// 		// case 'half-right' :
			// 		$ret .= 'padding-right: ' . $val . 'px;';
			// 	break;
			// 	default :
			// 		$ret .= 'padding-left: ' . $val . 'px;';
			// 	break;
			// }
			return $ret;
		}

		/**
		 * Converts our word-based rule into CSS rules.
		 *
		 * @param string $in_x - The option for X coordinates.
		 * @param string $in_y - The option for Y coordinates.
		 * @return string - The CSS rule.
		 * @since	1.0
		 */
		function word_to_css( $in_x, $in_y ) {

			// Capture rules that require translation first.
			if ( 'middle' == $in_y && ( 'half-left' == $in_x || 'center' == $in_x || 'half-right' == $in_x) ) {
				$ret = 'top: 50%; ';

				// Process horizontal alignment and use converged transforms. (No 2 transforms, whether be X or Y can coexist, hence this routine.)
				switch ( $in_x ) {
					case 'half-left' :
						$ret .= 'left: 25%; -webkit-transform: translate(-50%, -25%) !important; transform: translate(-50%, -25%) !important;';
					break;
					case 'center' :
						$ret .= 'left: 50%; -webkit-transform: translate(-50%, -50%) !important; transform: translate(-50%, -50%) !important;';
					break;
					case 'half-right' :
						$ret .= 'left: 75%; -webkit-transform: translate(-50%, -75%) !important; transform: translate(-50%, -75%) !important;';
					break;
				}
				return $ret;
			}

			// Process non-centered rules, vertical coordinates first.
			switch ( $in_y ) {
				case 'middle' :
					$ret_x = 'top: 50%; -webkit-transform: translateY(-50%) !important; transform: translateY(-50%) !important;';
				break;
				case 'down' :
				case 'bottom' :
					$ret_x = 'bottom: 0;';
				break;
				case 'up' :
				case 'top' :
				default :
					$ret_x = 'top: 0;';
				break;
			}

			// Process horizontal coordinates.
			switch ( $in_x ) {
				case 'left' :
					$ret_y = 'left: 0;';
				break;
				case 'half-left' :
					$ret_y = 'left: 25%; -webkit-transform: translateX(-25%) !important; transform: translateX(-25%) !important;';
				break;
				case 'center' :
					$ret_y = 'left: 50%; -webkit-transform: translateX(-50%) !important; transform: translateX(-50%) !important;';
				break;
				case 'half-right' :
					$ret_y = 'left: 75%; -webkit-transform: translateX(-75%) !important; transform: translateX(-75%) !important;';
				break;
				case 'right' :
					$ret_y = 'right: 0;';
				break;
				default :
					$ret_y = 'left: 0;';
				break;
			}

			return $ret_x . ' ' . $ret_y;
		}

		function array_swapper( $array, $key1, $key2 ) {
			$tmp = $array[ $key1 ];
			$array[ $key1 ] = $array[ $key2 ];
			$array[ $key2 ] = $tmp;
			return $array;
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

			// Shortcode construct and shortcode settings.
			vc_map( array(
				'name' => __( 'Before And After', GAMBIT_BEFORE_AND_AFTER ),
				'base' => 'gambit_before_after',
				'icon' => plugins_url( 'before-and-after/images/before-after-icon.svg', __FILE__ ),
				'description' => __( 'Interactive image comparison', GAMBIT_BEFORE_AND_AFTER ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle' ) : '',
				'admin_enqueue_css' => plugins_url( 'before-and-after/css/admin.css', __FILE__ ),
				'params' => array(
					array(
						'type' => 'attach_image',
						'heading' => __( 'The "Before" Image', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'before_image_id',
						'value' => '',
						'description' => __( 'Select your image that will serve as the "BEFORE" image. This will typically show underneath. The slider will take on the size of the smallest before or after image.', GAMBIT_BEFORE_AND_AFTER ),
						'group' => __( 'Images', GAMBIT_BEFORE_AND_AFTER ),
					),
					array(
						'type' => 'attach_image',
						'heading' => __( 'The "After" Image', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'after_image_id',
						'value' => '',
						'description' => __( 'Select your image that will serve as the "AFTER" image. This will typically show above the BEFORE image. The slider will take on the size of the smallest before or after image.', GAMBIT_BEFORE_AND_AFTER ),
						'group' => __( 'Images', GAMBIT_BEFORE_AND_AFTER ),
					),
					array(
	                    'type' => 'checkbox',
	                    'heading' => __( 'Swap Before and After Images', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'invert',
	                    'value' => array(
							__( 'Check to swap the before and after images.', GAMBIT_BEFORE_AND_AFTER ) => 'true',
						),
	                    'description' => '',
	                    'group' => __( 'Images', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Mobile Image Size', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'img_mobile_size',
	                    'value' => 'medium',
	                    'description' => __( 'Enter the size you want in mobile to maximize your responsive experience.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Images', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Width', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'width',
						'value' => '',
						'description' => __( 'By default, the slider is responsive. Add a width here in pixels if you want to specify the width.', GAMBIT_BEFORE_AND_AFTER ),
						'group' => __( 'Images', GAMBIT_BEFORE_AND_AFTER ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Slide Direction', GAMBIT_BEFORE_AND_AFTER ),
						'holder' => 'span',
						'param_name' => 'direction',
	                    'value' => array(
	                        __( 'Vertical', GAMBIT_BEFORE_AND_AFTER ) => 'vertical',
	                        __( 'Horizontal', GAMBIT_BEFORE_AND_AFTER ) => 'horizontal',
	                    ),
	                    'description' => __( 'Choose your slide direction here.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textfield',
	                    'heading' => __( 'Starting Position', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'start',
	                    'value' => '.50',
	                    'description' => __( 'Enter the starting point, in decimal percentage, of the comparison slider upon loading. The default is 50% (as .50), where the slider is positioned midway. Value should be between 0.0 to 1.0', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textfield',
	                    'heading' => __( 'Slide Angle', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'angle',
						'holder' => 'span',
	                    'value' => 0,
	                    'description' => __( 'Enter a number in degrees that will determine the sliding angle. Minimum is 0 degrees, maximum value is 45 degrees.<br><br>CAUTION: Rendering engines of browsers may treat differently-angled (not 0 or 45 degrees) images in an unpredictable manner and may introduce unwanted image effects. Use angles at your own risk.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'dropdown',
	                   'heading' => __( 'Slide Movement Behavior', GAMBIT_BEFORE_AND_AFTER ),
	                   'param_name' => 'slide',
					   'holder' => 'span',
	                   'value' => array(
	                       __( 'Follow on Hover', GAMBIT_BEFORE_AND_AFTER ) => 'hover',
						   __( 'Click and Drag', GAMBIT_BEFORE_AND_AFTER ) => 'click',
						   __( 'No Action', GAMBIT_BEFORE_AND_AFTER ) => 'none',
	                   ),
	                   'description' => __( 'Choose your slide behavior effect.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'checkbox',
	                    'heading' => __( 'Return slider to starting point', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'return_on_idle',
	                    'value' => array(
							__( 'Check to return the slider to its starting point after a specified idle period.', GAMBIT_BEFORE_AND_AFTER ) => 'true',
						),
	                    'description' => '',
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textfield',
						'heading' => __( 'Return Delay', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'return_on_idle_interval',
	                    'value' => '5000',
	                    'description' => __( 'Enter the delay, in milliseconds, before the slider reverts to its starting point once the focus is lost.', GAMBIT_BEFORE_AND_AFTER ),
	                    'dependency' => array(
	                        'element' => 'return_on_idle',
	                        'value' => ( 'true' ),
	                    ),
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textfield',
						'heading' => __( 'Return Speed', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'return_on_idle_duration',
	                    'value' => '1000',
	                    'description' => __( 'How long will the animation take for it to go through the returning point? Enter the duration, in milliseconds.', GAMBIT_BEFORE_AND_AFTER ),
	                    'dependency' => array(
	                        'element' => 'return_on_idle',
	                        'value' => ( 'true' ),
	                    ),
	                    'group' => __( 'Slider', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'checkbox',
	                    'heading' => __( 'Arrows', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'arrows',
	                    'value' => array(
							__( 'Check to enable usage of arrows.', GAMBIT_BEFORE_AND_AFTER ) => 'true',
						),
	                    'description' => '',
	                    'group' => __( 'Cosmetics', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                	'type' => 'dropdown',
	                	'heading' => 'Arrow Size',
		                'param_name' => 'arrow_size',
		                'value' => array(
											__( 'Normal', GAMBIT_BEFORE_AND_AFTER ) => 'normal',
											__( 'Small', GAMBIT_BEFORE_AND_AFTER ) => 'small',
										),
		                'description' => __( 'Select the size of the arrows', GAMBIT_BEFORE_AND_AFTER ),
		                'dependency' => array(
			                'element' => 'arrows',
			                'value' => ( 'true' ),
		                ),
		                'group' => __( 'Cosmetics', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Arrow Color', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'arrow_color',
						'value' => '#fff',
						'description' => __( 'Choose the color of the arrow.', GAMBIT_BEFORE_AND_AFTER ),
						'dependency' => array(
							'element' => 'arrows',
							'value' => ( 'true' ),
						),
						'group' => __( 'Cosmetics', GAMBIT_BEFORE_AND_AFTER ),
					),
	                // array(
	                // 'type' => 'textfield',
	                // 'heading' => __( 'Arrow Gap', GAMBIT_BEFORE_AND_AFTER ),
	                // 'param_name' => 'arrow_gap',
	                // 'value' => '0',
	                // 'description' => __( 'The gap of the arrows from the split, in pixels', GAMBIT_BEFORE_AND_AFTER ),
	                // 'dependency' => array(
	                // 'element' => 'arrows',
	                // 'value' => ( 'true' ),
	                // ),
	                // 'group' => __( 'Cosmetics', GAMBIT_BEFORE_AND_AFTER ),
	                // ),
	                array(
	                    'type' => 'textfield',
	                    'heading' => __( 'Arrow Horizontal Offset', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'arrow_offset_x',
	                    'value' => '0',
	                    'description' => __( 'The horizontal offset of the arrows, in pixels', GAMBIT_BEFORE_AND_AFTER ),
	                    'dependency' => array(
	                        'element' => 'arrows',
	                        'value' => ( 'true' ),
	                    ),
	                    'group' => __( 'Cosmetics', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textfield',
	                    'heading' => __( 'Arrow Vertical Offset', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'arrow_offset',
	                    'value' => '0',
	                    'description' => __( 'The vertical offset of the arrows, in pixels', GAMBIT_BEFORE_AND_AFTER ),
	                    'dependency' => array(
	                        'element' => 'arrows',
	                        'value' => ( 'true' ),
	                    ),
	                    'group' => __( 'Cosmetics', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'checkbox',
	                    'heading' => __( 'Border', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'border',
	                    'value' => array(
							__( 'Check to display a border at the edge of the slider', GAMBIT_BEFORE_AND_AFTER ) => 'true',
						),
	                    'description' => '',
	                    'group' => __( 'Border', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textfield',
	                    'heading' => 'Border Width',
	                    'param_name' => 'border_width',
	                    'value' => '2',
	                    'description' => __( 'The width of the slider border, in pixels.', GAMBIT_BEFORE_AND_AFTER ),
	                    'dependency' => array(
	                        'element' => 'border',
	                        'value' => ( 'true' ),
	                    ),
	                    'group' => __( 'Border', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Border Color', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'border_color',
						'value' => '#ffffff',
						'description' => __( 'Choose the color of the border of the slider.', GAMBIT_BEFORE_AND_AFTER ),
						'dependency' => array(
							'element' => 'border',
							'value' => ( 'true' ),
						),
						'group' => __( 'Border', GAMBIT_BEFORE_AND_AFTER ),
					),
	                array(
	                    'type' => 'textarea',
	                    'heading' => __( 'Before image caption', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'before_caption',
	                    'value' => '',
	                    'description' => __( 'Enter your caption for the "BEFORE" image. This field will not be rendered and its styling options disregarded, if empty.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'dropdown',
	                    'heading' => __( 'Before image caption vertical positioning', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'before_caption_pos_y',
	                    'value' => array(
	                        __( 'Top', GAMBIT_BEFORE_AND_AFTER ) => 'up',
	                        __( 'Middle', GAMBIT_BEFORE_AND_AFTER ) => 'middle',
	                        __( 'Bottom', GAMBIT_BEFORE_AND_AFTER ) => 'down',
	                    ),
	                    'description' => __( 'Choose the orientation of the Before image caption.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'dropdown',
	                    'heading' => __( 'Before image caption horizontal positioning', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'before_caption_pos_x',
	                    'value' => array(
	                        __( 'Left', GAMBIT_BEFORE_AND_AFTER ) => 'left',
							__( 'Halfway Left', GAMBIT_BEFORE_AND_AFTER ) => 'half-left',
	                        __( 'Center', GAMBIT_BEFORE_AND_AFTER ) => 'center',
							__( 'Halfway Right', GAMBIT_BEFORE_AND_AFTER ) => 'half-right',
	                        __( 'Right', GAMBIT_BEFORE_AND_AFTER ) => 'right',
	                    ),
	                    'description' => __( 'Choose the orientation of the Before image caption.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Before image caption color', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'before_caption_color',
						'value' => 'rgba(255,255,255,1)',
						'description' => __( 'Choose the color of the Before image caption.', GAMBIT_BEFORE_AND_AFTER ),
						'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
					),
	                array(
	                    'type' => 'textfield',
	                    'heading' => __( 'Before image caption padding', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'before_caption_padding',
	                    'value' => '20',
	                    'description' => __( 'Choose the padding thickness of the Before image caption.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'textarea',
	                    'heading' => __( 'After image caption', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'after_caption',
	                    'value' => '',
	                    'description' => __( 'Enter your caption for the "AFTER" image. This field will not be rendered and its styling options disregarded, if empty.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'dropdown',
	                    'heading' => __( 'After image caption vertical positioning', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'after_caption_pos_y',
	                    'value' => array(
	                        __( 'Top', GAMBIT_BEFORE_AND_AFTER ) => 'up',
	                        __( 'Middle', GAMBIT_BEFORE_AND_AFTER ) => 'middle',
	                        __( 'Bottom', GAMBIT_BEFORE_AND_AFTER ) => 'down',
	                    ),
	                    'description' => __( 'Choose the veritcal orientation of the After image caption.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'dropdown',
	                    'heading' => __( 'After image caption horizontal positioning', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'after_caption_pos_x',
	                    'value' => array(
	                        __( 'Left', GAMBIT_BEFORE_AND_AFTER ) => 'left',
							__( 'Halfway Left', GAMBIT_BEFORE_AND_AFTER ) => 'half-left',
	                        __( 'Center', GAMBIT_BEFORE_AND_AFTER ) => 'center',
							__( 'Halfway Right', GAMBIT_BEFORE_AND_AFTER ) => 'half-right',
	                        __( 'Right', GAMBIT_BEFORE_AND_AFTER ) => 'right',
	                    ),
	                    'description' => __( 'Choose the horizontal orientation of the After image caption.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'After image caption color', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'after_caption_color',
						'value' => 'rgba(255,255,255,1)',
						'description' => __( 'Choose the color of the After image caption.', GAMBIT_BEFORE_AND_AFTER ),
						'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
					),
	                array(
	                    'type' => 'textfield',
	                    'heading' => __( 'After image caption padding', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'after_caption_padding',
	                    'value' => '20',
	                    'description' => __( 'Choose the padding thickness of the After image caption.', GAMBIT_BEFORE_AND_AFTER ),
	                    'group' => __( 'Caption', GAMBIT_BEFORE_AND_AFTER ),
	                ),
	                array(
	                    'type' => 'checkbox',
	                    'heading' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
	                    'param_name' => 'scrollbar',
	                    'value' => array(
							__( 'Check to display a draggable scrollbar.', GAMBIT_BEFORE_AND_AFTER ) => 'true',
						),
	                    'description' => '',
	                    'group' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Scrollbar positioning', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'scrollbar_pos',
						'value' => array(
							__( 'Up', GAMBIT_BEFORE_AND_AFTER ) => 'top',
							__( 'Down', GAMBIT_BEFORE_AND_AFTER ) => 'bottom',
							__( 'Left', GAMBIT_BEFORE_AND_AFTER ) => 'left',
							__( 'Right', GAMBIT_BEFORE_AND_AFTER ) => 'right',
	                    ),
	                    'description' => __( 'Choose the orientation of the scrollbar.', GAMBIT_BEFORE_AND_AFTER ),
	                    'dependency' => array(
	                        'element' => 'scrollbar',
	                        'value' => ( 'true' ),
	                    ),
						'std' => 'right',
	                    'group' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
	                ),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Scrollbar border color', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'scrollbar_color',
						'value' => 'rgba(255,255,255,.3)',
						'description' => __( 'Choose the color of the border of the scrollbar.', GAMBIT_BEFORE_AND_AFTER ),
						'dependency' => array(
							'element' => 'scrollbar',
							'value' => ( 'true' ),
						),
						'group' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Scrollbar border thickness', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'scrollbar_thickness',
						'value' => '8',
						'description' => __( 'Choose the thickness of the border of the scrollbar.', GAMBIT_BEFORE_AND_AFTER ),
						'dependency' => array(
							'element' => 'scrollbar',
							'value' => ( 'true' ),
						),
						'group' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Scrollbar button color', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'scrollbar_button_color',
						'value' => 'rgba(255,255,255,.8)',
						'description' => __( 'Choose the color of the scrollbar button element.', GAMBIT_BEFORE_AND_AFTER ),
						'dependency' => array(
							'element' => 'scrollbar',
							'value' => ( 'true' ),
						),
						'group' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
					),
					// array(
					// 	'type' => 'textfield',
					// 	'heading' => '',
					// 	'param_name' => 'scrollbar_button_thickness',
					// 	'value' => '30',
					// 	'description' => __( 'Choose the thickness of the scrollbar button element.', GAMBIT_BEFORE_AND_AFTER ),
					// 	'dependency' => array(
					// 		'element' => 'manual_before_after_links',
					// 		'value' => ( 'true' ),
					// 	),
					// 	'group' => __( 'Scrollbar', GAMBIT_BEFORE_AND_AFTER ),
					// ),
					array(
						'type' => 'textfield',
						'heading' => __( 'Custom Class', GAMBIT_BEFORE_AND_AFTER ),
						'param_name' => 'class',
						'value' => '',
						'description' => __( 'Add a custom class name for this element.', GAMBIT_BEFORE_AND_AFTER ),
						'group' => __( 'Custom', GAMBIT_BEFORE_AND_AFTER ),
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
				'width' => '',
				'img_mobile_size' => 'medium',
	            'direction' => 'vertical',
	            'angle' => '0',
	            'slide' => 'hover',
				'start' => '.50',
				'arrows' => 'false',
				'arrow_size' => 'normal',
				'arrow_color' => '#ffffff',
				'arrow_gap' => '0',
				'arrow_offset_x' => '0',
				'arrow_offset' => '0',
				'border' => 'false',
				'border_width' => '2',
				'border_color' => '#ffffff',
	            'dim_on_mouseover' => 'false',
				'before_image_id' => '',
				'after_image_id' => '',
				'invert' => 'false',
				'manual_before_after_links' => 'false',
				'before_caption' => '',
				'before_caption_pos_y' => 'up',
				'before_caption_pos_x' => 'left',
				'before_caption_color' => '',
				'before_caption_padding' => '20',
				'after_caption' => '',
				'after_caption_pos_y' => 'up',
				'after_caption_pos_x' => 'left',
				'after_caption_color' => '',
				'after_caption_padding' => '20',
				'disable_for_mobile' => 'false',
				'return_on_idle' => 'false',
				'return_on_idle_interval' => '5000',
				'return_on_idle_duration' => '1000',
				'scrollbar' => 'false',
				'scrollbar_pos' => 'right',
				'scrollbar_color' => 'rgba(0,0,0,.4)',
				'scrollbar_thickness' => '8',
				'scrollbar_button_color' => '#ffffff',
				'scrollbar_button_thickness' => '30',
				'class' => '',
	        );
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			if ( $atts['invert'] === 'true' ) {
				$atts = $this->array_swapper( $atts, 'before_image_id', 'after_image_id' );
				$atts = $this->array_swapper( $atts, 'before_caption', 'after_caption' );
				$atts = $this->array_swapper( $atts, 'before_caption_pos_y', 'after_caption_pos_y' );
				$atts = $this->array_swapper( $atts, 'before_caption_pos_x', 'after_caption_pos_x' );
				$atts = $this->array_swapper( $atts, 'before_caption_color', 'after_caption_color' );
				$atts = $this->array_swapper( $atts, 'before_caption_padding', 'after_caption_padding' );
			}

			$customclass = ( '' != $atts['class'] ? $atts['class'] . ' ' : '' );

			// Fetch image information before Jetpack's Photon plugin could mess with the dimensions size.
			add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
			$image_info_1 = wp_get_attachment_image_src( $atts['before_image_id'], 'full' );
			$image_info_2 = wp_get_attachment_image_src( $atts['after_image_id'], 'full' );
			$image_info_3 = wp_get_attachment_image_src( $atts['before_image_id'], 'medium' );
			$image_info_4 = wp_get_attachment_image_src( $atts['after_image_id'], 'medium' );
			remove_filter( 'jetpack_photon_override_image_downsize', '__return_true' );

			// Get attributes of the image.
	        $image_attributes_1 = wp_get_attachment_image_src( $atts['before_image_id'], 'full' ); // returns an array.
	        $image_attributes_2 = wp_get_attachment_image_src( $atts['after_image_id'], 'full' ); // returns an array.
	        $image_attributes_3 = wp_get_attachment_image_src( $atts['before_image_id'], 'medium' ); // returns an array.
	        $image_attributes_4 = wp_get_attachment_image_src( $atts['after_image_id'], 'medium' ); // returns an array.

	        // Get the smallest width & height for full sizes.
	        if ( $image_info_1[1] < $image_info_2[1] ) {
	            $width_smallest = $image_info_1[1];
	        } else {
	            $width_smallest = $image_info_2[1];
	        }

	        if ( $image_info_1[2] < $image_info_2[2] ) {
	            $height_smallest = $image_info_1[2];
	        } else {
	            $height_smallest = $image_info_2[2];
	        }

			// Get the smallest width and height for the smaller image.
	        if ( $image_info_3[1] < $image_info_4[1] ) {
	            $width_smallest_medium = $image_info_3[1];
	        } else {
	            $width_smallest_medium = $image_info_4[1];
	        }

	        if ( $image_info_3[2] < $image_info_4[2] ) {
	            $height_smallest_medium = $image_info_3[2];
	        } else {
	            $height_smallest_medium = $image_info_4[2];
	        }

			// Resize the image if a width is given.
			if ( ! empty( $atts['width'] ) ) {
				$new_width = (int) $atts['width'];
				$height_smallest = (int) ( $height_smallest / $width_smallest * $new_width );
				$width_smallest = $new_width;
			}

	        // Create the $size variable with 'bfi_thumb' => true.
	        $size = array( $width_smallest, $height_smallest );
			$size_medium = array( $width_smallest_medium, $height_smallest_medium );

	        $ret = '';

	        $ret .= "<div id='gambit_baa_" . self::$element_id . "' class='" . $customclass . "gambit_baa'
			data-width='" . $width_smallest . "'
			data-height='" . $height_smallest . "'
	        data-direction='" . $atts['direction'] . "'
	        data-slide-on='" . $atts['slide'] . "'
	        data-rotation='" . $atts['angle'] . "'
			data-start='" . $atts['start'] . "'
			data-arrows='" . $atts['arrows'] . "'
			data-arrow-size='" . $atts['arrow_size'] . "'
			data-arrow-color='" . $atts['arrow_color'] . "'
			data-arrow-gap='" . $atts['arrow_gap'] . "'
			data-arrow-distance='" . $atts['arrow_offset_x'] . "'
			data-arrow-offset='" . $atts['arrow_offset'] . "'
			data-split-border='" . $atts['border'] . "'
			data-split-border-color='" . $atts['border_color'] . "'
			data-split-border-width='" . $atts['border_width'] . "'
			data-return-value='" . $atts['return_on_idle'] . "'
			data-return-delay='" . $atts['return_on_idle_interval'] . "'
			data-return-duration='" . $atts['return_on_idle_duration'] . "'
			data-slider='" . $atts['scrollbar'] . "'
			data-slider-location='" . $atts['scrollbar_pos'] . "'
			data-slider-bar-color='" . $atts['scrollbar_color'] . "'
			data-slider-bar-thickness='" . $atts['scrollbar_thickness'] . "'
			data-slider-button-color='" . $atts['scrollbar_button_color'] . "'
			data-slider-button-thickness='" . $atts['scrollbar_button_thickness'] . "'
			>";

			// Process Caption styling.
			$before_style = ' style="';
			$before_style .= $this->word_to_css( $atts['before_caption_pos_x'], $atts['before_caption_pos_y'] );
			$before_style .= 'color: ' . $atts['before_caption_color'] . ';';
			$before_style .= $this->word_to_padding( $atts['before_caption_pos_x'], $atts['before_caption_pos_y'], $atts['before_caption_padding'] );
			$before_style .= '"';

			$after_style = ' style="';
			$after_style .= $this->word_to_css( $atts['after_caption_pos_x'], $atts['after_caption_pos_y'] );
			$after_style .= 'color: ' . $atts['after_caption_color'] . ';';
			$after_style .= $this->word_to_padding( $atts['after_caption_pos_x'], $atts['after_caption_pos_y'], $atts['after_caption_padding'] );
			$after_style .= '"';

	        // Get the resized second image.
	        $ret .= "<div class='image_after' ";
	        $image_id = $atts['after_image_id'];
	        if ( ! empty( $image_id ) ) {
	            $imgsrc = wp_get_attachment_image_src( $image_id, $size );
	            $ret .= "style='background-image: url(" . $imgsrc['0'] . ")'>";
	        }
	        if ( ! empty( $atts['after_caption'] ) ) {
	            $ret .= "<div class='baa_content_wrapper'><div class='image_after_content'" . $after_style . '>' . $atts['after_caption'] . '</div></div>';
	        } else {

	        }

	        $ret .= '</div>';

	 		// Get the resized first image.
			$ret .= "<div class='image_before' ";
	        $image_id = $atts['before_image_id'];
	        if ( ! empty( $image_id ) ) {
	            $imgsrc = wp_get_attachment_image_src( $image_id, $size );
	            $ret .= "style='background-image: url(" . $imgsrc['0'] . ")'>";
	        }
	        if ( ! empty( $atts['before_caption'] ) ) {
	            $ret .= "<div class='baa_content_wrapper'><div class='image_before_content'" . $before_style . '>' . $atts['before_caption'] . '</div></div>';
	        } else {

	        }
	        $ret .= '</div>';
			$ret .= '</div>';

			wp_enqueue_style( __CLASS__, plugins_url( 'before-and-after/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_BEFORE_AND_AFTER );
			wp_enqueue_script( __CLASS__, plugins_url( 'before-and-after/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_BEFORE_AND_AFTER, true );

			// Print out the breakpoints.
			$after_medium = wp_get_attachment_image_src( $atts['after_image_id'], ( $atts['img_mobile_size'] ) ? $atts['img_mobile_size'] : $size_medium );
			$before_medium = wp_get_attachment_image_src( $atts['before_image_id'], ( $atts['img_mobile_size'] ) ? $atts['img_mobile_size'] : $size_medium );
			$ret .= '<style>@media only screen and (max-width: 1000px) {
						#gambit_baa_' . self::$element_id . ' .image_after {
							background-image: url("' . $after_medium[0] . '") !important;
						}
						#gambit_baa_' . self::$element_id . ' .image_before {
							background-image: url("' . $before_medium[0] . '") !important;
						}
			}</style>';

			self::$element_id++;

			return $ret;
		}
	}

	new GambitBeforeAndAfterShortcode();
}// End if().
