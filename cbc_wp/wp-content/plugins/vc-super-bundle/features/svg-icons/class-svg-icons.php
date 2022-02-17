<?php
/**
 * The icon's functionalities are located here.
 * @package svg Icons for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'SVGIcons' ) ) {

	/**
	 * The class that does the functions.
	 */
	 class SVGIcons {


		 /**
		  * Hook into WordPress.
		  *
		  * @return void.
		  * @since 1.0
		  */
		  function __construct() {

			  // Initialize plugin.
			  add_shortcode( 'svg_icon', array( $this, 'render_shortcode' ) );
			  add_shortcode( 'svg_icon_button', array( $this, 'render_shortcode_button' ) );

			  // Create as a Visual Composer addon.
			  add_action( 'init', array( $this, 'create_shortcode' ), 999 );
			  add_action( 'init', array( $this, 'create_shortcode_button' ), 999 );

			  // Make necessary changes for VC integration.
			  add_action( 'after_setup_theme', array( $this, 'integrate_with_vc' ) );

			  // Add styles to add the element icon, see more in function desc.
			  add_action( 'admin_head', array( $this, 'fix_vc_css' ) );

			  // Add our callback to both ajax actions.
			  add_action( 'wp_ajax_svg_search', array( $this, 'search_for_icon' ) );

			  // Add our callback to both ajax actions.
			  add_action( 'wp_ajax_svg_get', array( $this, 'ajax_get_icon' ) );

		  }


		  /**
			* Fixes the element icon in VC since our base starts with a number, CSS rules aren't being applied.
			* This writes new styles to show the icon.
			*
			* @return	void
			* @since	2.8
			*/
			public function fix_vc_css() {
				echo "<style>
					.vc_el-container [id='svg_icon'] .vc_element-icon,
					.wpb_svg_icon .wpb_element_title .vc_element-icon {
						background-image: url(" . plugins_url( 'svg-icons/images/12k-element-icon-1.png', __FILE__ ) . ');
					}
				</style>';
			}


			/**
			 * Creates the necessary shortcode for icon buttons.
			 *
			 * @since 1.0
			 */
			public function create_shortcode_button() {
				if ( ! function_exists( 'vc_map' ) ) {
					return;
				}

				vc_map( array(
					'base' => 'svg_icon_button',
					'name' => __( 'SVG Icon button', 'svg-icons' ),
					'description' => __( 'Awesome styleable icon', 'svg-icons' ),
					'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_SVG_ICONS ) : '',
					// URL to my icon for Visual Composer.
					'icon' => plugins_url( 'svg-icons/images/SVG_Element_Icon.svg', __FILE__ ),
					// All my attributes, define as many as we need.
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => __( 'Text', 'js_composer' ),
							'param_name' => 'title',
							// Fully compatible to btn1 and btn2.
							'value' => __( 'Text on the button', 'js_composer' ),
						),
						array(
							'type' => 'vc_link',
							'heading' => __( 'URL (Link)', 'js_composer' ),
							'param_name' => 'link',
							'description' => __( 'Add link to button.', 'js_composer' ),
							// Compatible with btn2 and converted from href{btn1}.
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Style', 'js_composer' ),
							'description' => __( 'Select button display style.', 'js_composer' ),
							'param_name' => 'style',
							// Partly compatible with btn2, need to be converted shape+style from btn2 and btn1.
							'value' => array(
								__( 'Modern', 'js_composer' ) => 'modern',
								__( 'Classic', 'js_composer' ) => 'classic',
								__( 'Flat', 'js_composer' ) => 'flat',
								__( 'Outline', 'js_composer' ) => 'outline',
								__( '3d', 'js_composer' ) => '3d',
								__( 'Custom', 'js_composer' ) => 'custom',
								__( 'Outline custom', 'js_composer' ) => 'outline-custom',
								__( 'Gradient', 'js_composer' ) => 'gradient',
								__( 'Gradient Custom', 'js_composer' ) => 'gradient-custom',
							),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Gradient Color 1', 'js_composer' ),
							'param_name' => 'gradient_color_1',
							'description' => __( 'Select first color for gradient.', 'js_composer' ),
							'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
							'value' => getVcShared( 'colors-dashed' ),
							'std' => 'turquoise',
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'gradient' ),
							),
							'edit_field_class' => 'vc_col-sm-6',
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Gradient Color 2', 'js_composer' ),
							'param_name' => 'gradient_color_2',
							'description' => __( 'Select second color for gradient.', 'js_composer' ),
							'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
							'value' => getVcShared( 'colors-dashed' ),
							'std' => 'blue',
							// Must have default color grey.
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'gradient' ),
							),
							'edit_field_class' => 'vc_col-sm-6',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Gradient Color 1', 'js_composer' ),
							'param_name' => 'gradient_custom_color_1',
							'description' => __( 'Select first color for gradient.', 'js_composer' ),
							'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
							'value' => '#dd3333',
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'gradient-custom' ),
							),
							'edit_field_class' => 'vc_col-sm-4',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Gradient Color 2', 'js_composer' ),
							'param_name' => 'gradient_custom_color_2',
							'description' => __( 'Select second color for gradient.', 'js_composer' ),
							'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
							'value' => '#eeee22',
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'gradient-custom' ),
							),
							'edit_field_class' => 'vc_col-sm-4',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Button Text Color', 'js_composer' ),
							'param_name' => 'gradient_text_color',
							'description' => __( 'Select button text color.', 'js_composer' ),
							'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
							'value' => '#ffffff',
							// Must have default color grey.
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'gradient-custom' ),
							),
							'edit_field_class' => 'vc_col-sm-4',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Background', 'js_composer' ),
							'param_name' => 'custom_background',
							'description' => __( 'Select custom background color for your element.', 'js_composer' ),
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'custom' ),
							),
							'edit_field_class' => 'vc_col-sm-6',
							'std' => '#ededed',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Text', 'js_composer' ),
							'param_name' => 'custom_text',
							'description' => __( 'Select custom text color for your element.', 'js_composer' ),
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'custom' ),
							),
							'edit_field_class' => 'vc_col-sm-6',
							'std' => '#666',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Outline and Text', 'js_composer' ),
							'param_name' => 'outline_custom_color',
							'description' => __( 'Select outline and text color for your element.', 'js_composer' ),
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'outline-custom' ),
							),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '#666',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Hover background', 'js_composer' ),
							'param_name' => 'outline_custom_hover_background',
							'description' => __( 'Select hover background color for your element.', 'js_composer' ),
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'outline-custom' ),
							),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '#666',
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Hover text', 'js_composer' ),
							'param_name' => 'outline_custom_hover_text',
							'description' => __( 'Select hover text color for your element.', 'js_composer' ),
							'dependency' => array(
								'element' => 'style',
								'value' => array( 'outline-custom' ),
							),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '#fff',
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Shape', 'js_composer' ),
							'description' => __( 'Select button shape.', 'js_composer' ),
							'param_name' => 'shape',
							// Need to be converted.
							'value' => array(
								__( 'Rounded', 'js_composer' ) => 'rounded',
								__( 'Square', 'js_composer' ) => 'square',
								__( 'Round', 'js_composer' ) => 'round',
							),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Color', 'js_composer' ),
							'param_name' => 'color',
							'description' => __( 'Select button color.', 'js_composer' ),
							// Compatible with btn2, need to be converted from btn1.
							'param_holder_class' => 'vc_colored-dropdown vc_btn3-colored-dropdown',
							'value' => array(
									// Btn1 Colors.
									__( 'Classic Grey', 'js_composer' ) => 'default',
									__( 'Classic Blue', 'js_composer' ) => 'primary',
									__( 'Classic Turquoise', 'js_composer' ) => 'info',
									__( 'Classic Green', 'js_composer' ) => 'success',
									__( 'Classic Orange', 'js_composer' ) => 'warning',
									__( 'Classic Red', 'js_composer' ) => 'danger',
									__( 'Classic Black', 'js_composer' ) => 'inverse',
									// + Btn2 Colors (default color set).
								) + getVcShared( 'colors-dashed' ),
							'std' => 'grey',
							// Must have default color grey.
							'dependency' => array(
								'element' => 'style',
								'value_not_equal_to' => array(
									'custom',
									'outline-custom',
									'gradient',
									'gradient-custom',
								),
							),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Size', 'js_composer' ),
							'param_name' => 'size',
							'description' => __( 'Select button display size.', 'js_composer' ),
							// Compatible with btn2, default md, but need to be converted from btn1 to btn2.
							'std' => 'md',
							'value' => getVcShared( 'sizes' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Alignment', 'js_composer' ),
							'param_name' => 'align',
							'description' => __( 'Select button alignment.', 'js_composer' ),
							// Compatible with btn2, default left to be compatible with btn1.
							'value' => array(
								__( 'Inline', 'js_composer' ) => 'inline',
								// Default as well.
								__( 'Left', 'js_composer' ) => 'left',
								// Default as well.
								__( 'Right', 'js_composer' ) => 'right',
								__( 'Center', 'js_composer' ) => 'center',
							),
						),
						array(
							'type' => 'checkbox',
							'heading' => __( 'Set full width button?', 'js_composer' ),
							'param_name' => 'button_block',
							'dependency' => array(
								'element' => 'align',
								'value_not_equal_to' => 'inline',
							),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Icon Alignment', 'js_composer' ),
							'description' => __( 'Select icon alignment.', 'js_composer' ),
							'param_name' => 'i_align',
							'value' => array(
								__( 'Left', 'js_composer' ) => 'left',
								// Default as well.
								__( 'Right', 'js_composer' ) => 'right',
							),
							'dependency' => array(
								'element' => 'add_icon',
								'value' => 'true',
							),
						),
						array(
						   'type' => 'svg_icon_button',
						   'heading' => __( 'Choose your icon', 'svg-icons' ),
						   'param_name' => 'icon_button',
						   'value' => 'awesome/wordpress',
						   'admin_label' => true,
						   'description' => __( 'Choose an icon. Type in the text box above to search for a specific icon.', 'svg-icons' ),
					   ),
					   vc_map_add_css_animation( true ),
						array(
							'type' => 'textfield',
							'heading' => __( 'Extra class name', 'js_composer' ),
							'param_name' => 'el_class',
							'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
						),
						array(
							'type' => 'checkbox',
							'heading' => __( 'Advanced on click action', 'js_composer' ),
							'param_name' => 'custom_onclick',
							'description' => __( 'Insert inline onclick javascript action.', 'js_composer' ),
						),
						array(
							'type' => 'textfield',
							'heading' => __( 'On click code', 'js_composer' ),
							'param_name' => 'custom_onclick_code',
							'description' => __( 'Enter onclick action code.', 'js_composer' ),
							'dependency' => array(
								'element' => 'custom_onclick',
								'not_empty' => true,
							),
						),
						array(
							'type' => 'css_editor',
							'heading' => __( 'CSS box', 'js_composer' ),
							'param_name' => 'css',
							'group' => __( 'Design Options', 'js_composer' ),
						),
						array(
							'type' => 'textarea_raw_html',
							'heading' => __( 'Upload your own SVG', 'svg-icons' ),
							'param_name' => 'custom_svg',
							'placeholder' => '<svg> code here',
							'value' => '',
							'description' => __( 'If you want to use your own SVGs, paste the whole SVG code here.', 'svg-icons' ),
						),
					),
				) );
			}


		  /**
		   * Creates the necessary shortcode.
		   *
		   * @since 1.0
		   */
		   public function create_shortcode() {
			   if ( ! function_exists( 'vc_map' ) ) {
				   return;
			   }

			   vc_map( array(
				   'base' => 'svg_icon',
				   'name' => __( 'SVG Icon', 'svg-icons' ),
				   'description' => __( 'Awesome styleable icon', 'svg-icons' ),
				   'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_SVG_ICONS ) : '',
				   // URL to my icon for Visual Composer.
				   'icon' => plugins_url( 'svg-icons/images/SVG_Element_Icon.svg', __FILE__ ),
				   // All my attributes, define as many as we need.
				   'params' => array(
					   array(
						   'type' => 'svg_icon',
						   'heading' => __( 'Choose your icon', 'svg-icons' ),
						   'param_name' => 'icon',
						   'value' => 'awesome/wordpress',
						   'admin_label' => true,
						   'description' => __( 'Choose an icon. Type in the text box above to search for a specific icon.', 'svg-icons' ),
					   ),
					   array(
						   'type' => 'colorpicker',
						   'heading' => __( 'Icon Color', 'svg-icons' ),
						   'param_name' => 'icon_color',
						   'value' => '#9b59b6',
					   ),
					   array(
						   'type' => 'checkbox',
						   'heading' => __( 'Force Color', 'svg-icons' ),
						   'param_name' => 'force_color',
							'value' => array(
								__( 'Some icons have their own colors and cannot be changed, check this to force our own color (this may affect multi-colored icons, and may not work with some icons).', 'svg-icons' ) => 'force',
							),
					   ),
					   array(
							'type' => 'textfield',
							'heading' => __( 'Icon Size', 'svg-icons' ),
							'param_name' => 'icon_size',
							'value' => '40',
							'description' => __( 'The size of your icon in pixels.', 'svg-icons' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Background Shape', 'svg-icons' ),
							'param_name' => 'bg_shape',
							'value' => array(
								__( 'No background shape', 'svg-icons' ) => '',
								__( 'Circle', 'svg-icons' ) => 'circle',
								__( 'Square', 'svg-icons' ) => 'square',
								__( 'Rounded', 'svg-icons' ) => 'rounded',
								__( 'Circle Outline', 'svg-icons' ) => 'circle-outline',
								__( 'Square Outline', 'svg-icons' ) => 'square-outline',
								__( 'Rounded Outline', 'svg-icons' ) => 'rounded-outline',
							),
							'description' => __( 'Select a background shape for your icon.', 'svg-icons' ),
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Background Color', 'svg-icons' ),
							'param_name' => 'bg_color',
							'value' => '#bdc3c7',
							'description' => __( "Pick a color for your icon's background shape.", 'svg-icons' ),
						),
						array(
							'type' => 'colorpicker',
							'heading' => __( 'Outline Color', 'svg-icons' ),
							'param_name' => 'outline_color',
							'value' => '#bdc3c7',
							'description' => __( "Pick a color for your icon's outline color shape.", 'svg-icons' ),
							'dependency' => array(
								'element' => 'bg_shape',
								'value' => array(
									'circle-outline',
									'square-outline',
									'rounded-outline',
								),
							),
						),
						// array(
					// 		'type' => 'textfield',
					// 		'heading' => __( 'Outline Color Thickness', 'svg-icons' ),
					// 		'param_name' => 'outline_thickness',
					// 		'value' => '3',
					// 		'description' => __( 'The size of outline color thickness of your icon in pixels.', 'svg-icons' ),
						// 	'dependency' => array(
						// 		'element' => 'bg_shape',
						// 		'value' => array(
						// 			'circle-outline',
						// 			'square-outline',
						// 			'rounded-outline',
						// 		),
						// 	),
					// 	),
						vc_map_add_css_animation( true ),
						array(
							'type' => 'vc_link',
							'heading' => __( 'URL (Link)', 'svg-icons' ),
							'param_name' => 'link',
							'description' => __( 'Enter a URL here to make your icon a link.', 'svg-icons' ),
						),
						array(
							'type' => 'dropdown',
							'heading' => __( 'Alignment', 'svg-icons' ),
							'param_name' => 'float',
							'value' => array(
								__( 'Center', 'svg-icons' ) => '',
								__( 'Left', 'svg-icons' ) => 'left',
								__( 'Right', 'svg-icons' ) => 'right',
							),
						),
						array(
							'type' => 'textarea_raw_html',
							'heading' => __( 'Upload your own SVG', 'svg-icons' ),
							'param_name' => 'custom_svg',
							'placeholder' => '<svg> code here',
							'value' => '',
							'description' => __( 'If you want to use your own SVGs, paste the whole SVG code here.', 'svg-icons' ),
						),
						array(
							'type' => 'textfield',
							'heading' => __( 'Extra class name', 'svg-icons' ),
							'param_name' => 'el_class',
							'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'svg-icons' ),
						),
				   ),
			   ) );
		   }


		   /**
			* Applies fixes for the icon roster.
			*
			* @since 1.0
			*/
			public function integrate_with_vc() {

				if ( ! function_exists( 'vc_add_shortcode_param' ) ) {
					return;
				}

				vc_add_shortcode_param( 'svg_icon', array( $this, 'create_icon_settings_field' ), plugins_url( 'svg-icons/js/min/admin-min.js', __FILE__ )  );
				vc_add_shortcode_param( 'svg_icon_button', array( $this, 'create_icon_settings_field' ), plugins_url( 'svg-icons/js/min/admin-button-min.js', __FILE__ )  );
			}


			/**
			* SVG Icon Button Shortcode logic.
			*
			* @param array  $atts - The attributes of the shortcode.
			* @param string $content - The content enclosed inside the shortcode if any.
			* @return string - The rendered html.
			* @since 1.0
			*/
			public function render_shortcode_button( $atts, $content = '' ) {

				$defaults = array(
					'title' => __( 'Text on the button', 'svg-icons' ),
					'link' => '',
					'style' => 'modern',
					'gradient_color_1' => 'turquoise',
					'gradient_color_2' => 'blue',
					'gradient_custom_color_1' => '#dd3333',
					'gradient_custom_color_2' => '#eeee22',
					'gradient_text_color' => '#ffffff',
					'custom_background' => '#ededed',
					'custom_text' => '#666',
					'outline_custom_color' => '#666',
					'outline_custom_hover_background' => '#666',
					'outline_custom_hover_text' => '#fff',
					'shape' => 'rounded',
					'color' => 'grey',
					'size' => 'md',
					'align' => 'inline',
					'button_block' => '',
					'i_align' => 'left',
					'icon_button' => 'awesome/wordpress',
					'el_class' => '',
					'custom_onclick' => '',
					'custom_onclick_code' => '',
					'css' => '',
					'css_animation' => '',
					'custom_svg' => '',
				);

				if ( empty( $atts ) ) {
					$atts = array();
				}
				$atts = array_merge( $defaults, $atts );

				$output = '';
				// Enqueue the CSS.
				if ( ! empty( $atts['icon_button'] ) ) {
					wp_enqueue_style( 'svg-icons', plugins_url( 'svg-icons/css/style.css', __FILE__ ), null, VERSION_GAMBIT_VC_SVG_ICONS );
				}

				$icon = gmb_svg_icons_get_icon( $atts['icon_button'], $atts['custom_svg'] );

				// If no icon, don't display anything.
				if ( empty( $icon ) ) {
					return '';
				}

				VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Btn' );
				$vc_btn = new WPBakeryShortCode_VC_Btn( array( 'base' => 'vc_btn' ) );

				$inline_css = '';
				$attributes = array();
				$colors = array(
					'blue' => '#5472d2',
					'turquoise' => '#00c1cf',
					'pink' => '#fe6c61',
					'violet' => '#8d6dc4',
					'peacoc' => '#4cadc9',
					'chino' => '#cec2ab',
					'mulled-wine' => '#50485b',
					'vista-blue' => '#75d69c',
					'orange' => '#f7be68',
					'sky' => '#5aa1e3',
					'green' => '#6dab3c',
					'juicy-pink' => '#f4524d',
					'sandy-brown' => '#f79468',
					'purple' => '#b97ebb',
					'black' => '#2a2a2a',
					'grey' => '#ebebeb',
					'white' => '#ffffff',
				);
				/** @var $vc_btn WPBakeryShortCode_VC_Btn. */
				$atts = vc_map_get_attributes( $vc_btn->getShortcode(), $atts );
				extract( $atts );

				$button_classes = array(
					'vc_general',
					'vc_btn3',
					'vc_btn3-size-' . $size,
					'vc_btn3-shape-' . $shape,
					'vc_btn3-style-' . $style,
				);

				//parse link
				$link = ( '||' === $link ) ? '' : $link;
				$link = vc_build_link( $link );
				$use_link = false;
				$a_href = '';
				$a_title = '';
				$a_target = '';
				$a_rel = '';
				if ( strlen( $link['url'] ) > 0 ) {
					$use_link = true;
					$a_href = $link['url'];
					$a_title = $link['title'];
					$a_target = $link['target'];
					$a_rel = $link['rel'];
				}

				$class_to_filter = 'vc_btn3-container ' . $vc_btn->getCSSAnimation( $css_animation ) . ' ';
				$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $vc_btn->getExtraClass( $el_class ) . ' ';
				$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $vc_btn->settings( 'base' ), $atts );

				$button_html = $title;

				if ( '' === trim( $title ) ) {
					$button_classes[] = ' vc_btn3-o-empty';
					$button_html = '<span class="vc_btn3-placeholder">&nbsp;</span>';
				}

				if ( 'true' === $button_block && 'inline' !== $align ) {
					$button_classes[] = ' vc_btn3-block';
				}

				if ( $atts['align'] === 'center' ) {
					$css_class .= 'vc_btn3-' . $atts['align'];
				} elseif ( $atts['align'] === 'left' ) {
					$css_class .= 'vc_btn3-' . $atts['align'];
				} elseif ( $atts['align'] === 'right' ) {
					$css_class .= 'vc_btn3-' . $atts['align'];
				} elseif ( $atts['align'] === 'inline' ) {
					$css_class .= 'vc_btn3-' . $atts['align'];
				}

				$button_classes[] = ' vc_btn3-icon-' . $atts[ 'i_align' ];
				if ( 'left' === $atts[ 'i_align' ] ) {
					$button_html = $icon . ' ' . $button_html;
				} else {
					$button_html .= ' ' . $icon;
				}

				if ( 'custom' === $style ) {
					$inline_css = vc_get_css_color( 'background-color', $custom_background ) . vc_get_css_color( 'color', $custom_text )  . vc_get_css_color( 'fill', $custom_text );
				} elseif ( 'outline-custom' === $style ) {
					$inline_css = vc_get_css_color( 'border-color', $outline_custom_color ) . vc_get_css_color( 'color', $outline_custom_color ) . vc_get_css_color( 'fill', $outline_custom_color );
					$attributes[] = 'onmouseenter="this.style.borderColor=\'' . $outline_custom_hover_background . '\'; this.style.backgroundColor=\'' . $outline_custom_hover_background . '\'; this.style.color=\'' . $outline_custom_hover_text . '\'; this.style.fill=\'' . $outline_custom_hover_text . '\'"';
					$attributes[] = 'onmouseleave="this.style.borderColor=\'' . $outline_custom_color . '\'; this.style.backgroundColor=\'transparent\'; this.style.color=\'' . $outline_custom_color . '\'; this.style.fill=\'' . $outline_custom_color . '\'"';
				} elseif ( 'gradient' === $style || 'gradient-custom' === $style ) {
					$gradient_color_1 = $colors[ $gradient_color_1 ];
					$gradient_color_2 = $colors[ $gradient_color_2 ];
					$button_text_color = '#fff';
					if ( 'gradient-custom' === $style ) {
						$gradient_color_1 = $gradient_custom_color_1;
						$gradient_color_2 = $gradient_custom_color_2;
						$button_text_color = $gradient_text_color;
					}

					$gradient_css = array();
					$gradient_css[] = 'color: ' . $button_text_color;
					$gradient_css[] = 'border: none';
					$gradient_css[] = 'background-color: ' . $gradient_color_1;
					$gradient_css[] = 'background-image: -webkit-linear-gradient(left, ' . $gradient_color_1 . ' 0%, ' . $gradient_color_2 . ' 50%,' . $gradient_color_1 . ' 100%)';
					$gradient_css[] = 'background-image: linear-gradient(to right, ' . $gradient_color_1 . ' 0%, ' . $gradient_color_2 . ' 50%,' . $gradient_color_1 . ' 100%)';
					$gradient_css[] = '-webkit-transition: all .2s ease-in-out';
					$gradient_css[] = 'transition: all .2s ease-in-out';
					$gradient_css[] = 'background-size: 200% 100%';

					// Hover css.
					$gradient_css_hover = array();
					$gradient_css_hover[] = 'color: ' . $button_text_color;
					$gradient_css_hover[] = 'background-color: ' . $gradient_color_2;
					$gradient_css_hover[] = 'border: none';
					$gradient_css_hover[] = 'background-position: 100% 0';

					$uid = uniqid();
					$output .= '<style type="text/css">.vc_btn3-style-' . $style . '.vc_btn-gradient-btn-' . $uid . ':hover{' . implode( ';', $gradient_css_hover ) . ';' . '}</style>';
					$output .= '<style type="text/css">.vc_btn3-style-' . $style . '.vc_btn-gradient-btn-' . $uid . '{' . implode( ';', $gradient_css ) . ';' . '}</style>';
					$output .= '<style type="text/css">.vc_btn3-style-' . $style . '.vc_btn-gradient-btn-' . $uid . '{ fill: ' . $button_text_color . ';' . '}</style>';
					$output .= '<style type="text/css">.vc_general.vc_btn3.vc_btn3-style-gradient-custom svg { fill: ' . $button_text_color . ';}</style>';
					$button_classes[] = ' vc_btn-gradient-btn-' . $uid;
					$attributes[] = 'data-vc-gradient-1="' . $gradient_color_1 . '"';
					$attributes[] = 'data-vc-gradient-2="' . $gradient_color_2 . '"';
				} else {
					$button_classes[] = ' vc_btn3-color-' . $color . ' ';
				}

				if ( '' !== $inline_css ) {
					$inline_css = ' style="' . $inline_css . '"';
				}


				$attributes[] = 'href="' . trim( $a_href ) . '"';
				$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
				if ( ! empty( $a_target ) ) {
					$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
				}
				if ( ! empty( $a_rel ) ) {
					$attributes[] = 'rel="' . esc_attr( trim( $a_rel ) ) . '"';
				}

				if ( ! empty( $custom_onclick ) && $custom_onclick_code ) {
					$onlick_code_js = str_replace( '``', "'", $custom_onclick_code );
					$attributes[] = 'onclick="' . esc_attr(  $onlick_code_js ) . '"';
				}

				$attributes = implode( ' ', $attributes );
				$output .= '<div class="' . trim( esc_attr( $css_class ) ) . '">';
				$output .= '<a ' . $attributes . ' class="' . join( ' ', $button_classes ) . '" ' . $inline_css . '>' . $button_html . '</a>';
				$output .= '</div>';

				return $output;
			}


			/**
			 * SVG Icons Shortcode logic.
			 *
			 * @param array  $atts - The attributes of the shortcode.
			 * @param string $content - The content enclosed inside the shortcode if any.
			 * @return string - The rendered html.
			 * @since 1.0
			 */
			public function render_shortcode( $atts, $content = '' ) {

				$defaults = array(
					'icon' => 'awesome/wordpress',
					'icon_size' => '40',
					'custom_svg' => '',
					'float' => '',
					'bg_shape' => '',
					'icon_color' => '#9b59b6',
					'bg_color' => '#bdc3c7',
					'outline_color' => '#bdc3c7',
					// 'outline_thickness' => '3',
					'link' => '',
					'css_animation' => '',
					'force_color' => '',
					'el_class' => '',
					'css' => '',
				);
				if ( empty( $atts ) ) {
					$atts = array();
				}
				$atts = array_merge( $defaults, $atts );

				// Enqueue the CSS.
				if ( ! empty( $atts['icon'] ) ) {
					// $css_file = substr( $atts['icon'], 0, stripos( $atts['icon'], '-' ) );
					// Don't load the CSS files to trim loading time, include the specific styles via PHP.
					// wp_enqueue_script( '4k-icons', plugins_url( '4k-icons/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_4K_ICONS, true );
					// wp_enqueue_script( 'svg-icons', plugins_url( 'svg-icons/js/min/admin-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_VC_SVG_ICONS, true );
					wp_enqueue_style( 'svg-icons', plugins_url( 'svg-icons/css/style.css', __FILE__ ), null, VERSION_GAMBIT_VC_SVG_ICONS );
				}

				VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Btn' );
				$vc_btn = new WPBakeryShortCode_VC_Btn( array( 'base' => 'vc_btn' ) );

				$ret = '';

				$icon = gmb_svg_icons_get_icon( $atts['icon'], $atts['custom_svg'] );

				// If no icon, don't display anything.
				if ( empty( $icon ) ) {
					return '';
				}

				$styles = '';
				$svg_classes = array( 'svg_icon' );
				$linkedIconDiv = '';
				$padding = '';

				$size = is_numeric( $atts['icon_size'] ) ? (int) $atts['icon_size'] : 40;

				$class_to_filter = $vc_btn->getCSSAnimation( $atts[ 'css_animation' ] );
				$class_to_filter .= vc_shortcode_custom_css_class( $atts['css'], ' ' ) . ' ' . $atts['el_class'];
				$svg_classes[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, '', $atts );


				// Icon size.
				$styles .= 'height: ' . $size . 'px; width: ' . $size . 'px;';

				// Alignment.
				if ( $atts['float'] === 'left' ) {
					$styles .= 'float: ' . $atts['float'] . ';';
				} else if ( $atts['float'] === 'right' ) {
					$styles .= 'float: ' . $atts['float'] . ';';
				}

				// Background Color.
				if ( ! empty( $atts['bg_color'] ) ) {
					if ( ! empty( $atts['bg_shape'] ) ) {
						$styles .= 'background-color: ' . $atts['bg_color'] . ';';
					} else {
						$styles .= 'background-color: transparent;';
					}
				}

				// Background shape.
				if ( ! empty( $atts['bg_shape'] ) ) {
					// Padding, only when there's a background shape.
					$padding = $size / 4;
					$styles .= 'padding: ' . $padding . 'px;';

					if ( $atts['bg_shape'] === 'circle' || $atts['bg_shape'] === 'circle-outline' ) {
						$styles .= 'border-radius: 100%;';
					} else if ( $atts['bg_shape'] === 'rounded' || $atts['bg_shape'] == 'rounded-outline' ) {
						$styles .= 'border-radius: ' . ( $size / 4 ) . 'px;';
					}

					// Background color or outline color.
					if ( false === stripos( $atts['bg_shape'], 'outline' ) ) {
						$styles .= 'background-color: ' . $atts['bg_color'] . ';';
					} else {
						// $styles .= 'border: ' . (  (int) $atts['outline_thickness'] ) . 'px solid ' . $atts['outline_color'] . ';';
						$styles .= 'border: 4px solid ' . $atts['outline_color'] . ';';
					}
				} else {
					$atts['bg_shape'] = 'none';
				}

				// Color.
				if ( preg_match( '/(<svg[^>]+)(fill=[\'"][^\'"]+[\'"])/i', $icon ) ) {
					// If there's a fill attribute, replace it.
					$icon = preg_replace( '/(<svg[^>]+)(fill=[\'"][^\'"]+[\'"])/i', '$1fill="' . $atts['icon_color'] . '"', $icon );
				} else if ( preg_match( '/(<svg[^>]+)(fill:\s*[^;"\']+)/i', $icon ) ) {
					// Or if there's a fill style, replace it.
					$icon = preg_replace( '/(<svg[^>]+)(fill:\s*[^;"\']+)/i', '$1fill: ' . $atts['icon_color'] . '', $icon );
				} else if ( preg_match( '/(<svg[^>]+style=[\'"])/i', $icon ) ) {
					// Or if there's a style attribute, add a fill.
					$icon = preg_replace( '/(<svg[^>]+style=[\'"])/i', '$1fill: ' . $atts['icon_color'] . ';', $icon );
				} else {
					// Else, add one.
					$icon = preg_replace( '/(<svg\s*)/i', '$1style="fill: ' . $atts['icon_color'] . ';" ', $icon );
				}

				// Force Color.
				if ( ! empty( $atts['force_color'] ) ) {

					// If there's a fill attribute, replace it.
					if ( preg_match( '/(<\w+[^>]+)(fill=[\'"][^\'"]+[\'"])/i', $icon ) ) {
						$icon = preg_replace( '/(<\w+[^>]+)(fill=[\'"][^\'"]+[\'"])/i', '$1fill="' . $atts['icon_color'] . '"', $icon );
					}
				}


				// To avoid the svg to become very big before the css loaded.
				$styles .= 'display: block';

				// Get the link.
				$url = '';
				if ( ! empty( $atts['link'] ) ) {
					$link = vc_build_link( trim( $atts['link'] ) );

					if ( ! empty( $link['url'] ) ) {
						$url = $link['url'];
						$target = $link['target'];
					}
				}

				if ( ! empty( $url ) ) {
					$float = 'none';
					if ( ! empty( $atts['float'] ) ) {
						$float = $atts['float'];
					}

					$ret .= '<a href=' . esc_url( $url ) . ' class="svg-icon-link" style="width: ' . $size . 'px; float: ' . $float . ';" target="' . esc_attr( $target ) . '"><span class="' . esc_attr( join( ' ', $svg_classes ) ) . '" style="' . $styles . '">'
						. $icon
						. '</span></a>';
				} else {
					$ret .= '<span class="' . esc_attr( join( ' ', $svg_classes ) ) . '" style="' . $styles . '">'
						. $icon
						. '</span>';
				}

				return $ret;
			}


			public function ajax_get_icon() {

				$nonce = isset( $_POST[ 'nonce' ] ) ? $_POST[ 'nonce' ] : '';
				$icon = isset( $_POST[ 'icon' ] ) ? $_POST[ 'icon' ] : '';

				if ( wp_verify_nonce( $nonce, 'svg' ) && ! empty( $icon ) ) {
					if ( file_exists( plugin_dir_path( __FILE__ ) . 'svg-icons/icons/' . $icon . '.svg' ) ) {
						ob_start();
						include( 'svg-icons/icons/' . $icon . '.svg' );
						$icon = ob_get_clean();
					} else {
						$icon = '';
					}
				} else {

					// If the nonce was invalid or the comment was empty, send an error.
					wp_send_json_error( __( 'Security Error', 'svg-icons' ) );
				}

				// Send svgs back to Javascript.
				wp_send_json_success( $icon );

			}


			public function search_for_icon() {

				$nonce = isset( $_POST[ 'nonce' ] ) ? $_POST[ 'nonce' ] : '';
				$search_terms = isset( $_POST[ 'search_terms' ] ) ? $_POST[ 'search_terms' ] : '';

				// Make sure the nonce is valid and we have comment data.
				if ( wp_verify_nonce( $nonce, 'svg' ) && ! empty( $search_terms ) ) {

					// Search each element in $search_term for a match
					$icon_names = include( 'function-icon-names.php' );

					$search_terms = preg_replace( '/[\s,-_]/', '(.*?)', trim( $search_terms ) );
					$matched_icons = preg_grep( '/' . $search_terms . '/', $icon_names );

					$svgs = array();
					foreach ( $matched_icons as $icon_path ) {
						ob_start();
						include( 'svg-icons/icons/' . $icon_path . '.svg' );
						$icon = ob_get_clean();
						$svgs[ $icon_path ] = $icon;
					}

				} else {
					// If the nonce was invalid or the comment was empty, send an error.
					wp_send_json_error( __( 'Security Error', 'svg-icons' ) );
				}

				// Send svgs back to Javascript.
				wp_send_json_success( $svgs );

			}


			/**
			 * Applies a unique Visual Composer option for the SVG Icons.
			 * @param array  $settings - The settings array from existing VC fields.
			 * @param string $value - As specified.
			 * @since 1.0
			 */
			public function create_icon_settings_field( $settings, $value ) {

				$extra_entries = '';
				return '<div>'
					  . '<style>.svg_select_window div {'
					  . 'display:inline-block;height:40px;min-width:40px;text-align:center;padding:5px;vertical-align:middle;border:1px solid #ddd;margin:2px;cursor:pointer;box-sizing:content-box'
					  . '}'
					  . '.svg_select_window svg {'
					  . 'width: 40px; height: 40px;'
					  . '}'
					  . '.svg_icon_preview svg {'
					  . 'width: 100%; height: 100%;'
					  . '}'
					  . '</style>'
					  . '<div class="svg_icon_preview" style="display: inline-block;
							margin-right: 10px;
							height: 60px;
							width: 90px;
							text-align: center;
							background: #FAFAFA;
							font-size: 60px;
							padding: 15px 0;
							margin-bottom: 10px;
							border: 1px solid #DDD;
							float: left;
							box-sizing: content-box;"></div>'
					. '<input placeholder="' . __( 'Search icon then pick one below...', 'svg-icons' ) . '" name="' . $settings['param_name'] . '"'
					. ' data-param-name="' . $settings['param_name'] . '"'
					. ' data-nonce="' . esc_attr( wp_create_nonce( 'svg' ) ) . '"'
					. 'class="wpb_vc_param_value wpb-textinput'
					. $settings['param_name'] . ' ' . $settings['type'] . '_field" type="text" value="'
					. $value . '" style="width: 230px; margin-right: 10px; vertical-align: top; float: left; margin-bottom: 10px"/>'
					. '<div class="svg_select_window" style="display: none; font-size: 40px; width: 100%; padding: 8px;
						box-sizing: border-box;
						-moz-box-sizing: border-box;
						background: #FAFAFA;
						height: 250px;
						overflow-y: scroll;
						border: 1px solid #DDD;
						clear: both"></div>'
					. '</div>';
			  }
		 }

	 new SVGIcons();
}

if ( ! function_exists( 'gmb_svg_icons_get_icon' ) ) {
	function gmb_svg_icons_get_icon( $icon, $custom_svg = '' ) {

		// Make sure the icon exists.
		if ( ! empty( $icon ) ) {
			if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'svg-icons/icons/' . $icon . '.svg' ) ) {
				return $content;
			}

			ob_start();
			include( 'svg-icons/icons/' . $icon . '.svg' );
			$icon = ob_get_clean();
		}

		// If there is a custom SVG given, use that for the icon.
		if ( ! empty( $custom_svg ) ) {
			$decoded = base64_decode( $custom_svg );
			if ( $decoded ) {
				$icon = urldecode( $decoded );
			}
		}

		return $icon;
	}
}
