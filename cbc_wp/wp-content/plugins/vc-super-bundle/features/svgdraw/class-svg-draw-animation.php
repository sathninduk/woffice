<?php
/**
 *  @package SVG Draw Animation for WPBakery.
 */

 if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
 }

 if ( ! class_exists( 'SVG_Draw_Animation_Shortcode' ) ) {

	 class SVG_Draw_Animation_Shortcode {

		 /**
		  * Hook into WordPress.
		  *
		  * @return  void
		  * @since   1.0
		  */
		  function __construct() {

			  // Initializes plugin.
			  add_shortcode( 'svg_draw_animation', array( $this, 'render_shortcode' ) );

			  // Create as a WPBakery addon.
			  add_action( 'init', array( $this, 'create_shortcode' ) );
		  }

		  /**
		   * Checks whether SVG Icons plugin is present.
		   *
		   * @return boolean
		   * @since  1.0
		   */
		  public function is_svg_icon_plugin_active() {
			  return function_exists( 'gmb_svg_icons_get_icon' );
		  }

		  /**
		   * Creates the neccessary shortcode for svg draw animation.
		   *
		   * @since 1.0
		   */
		   public function create_shortcode() {
			   if ( ! function_exists( 'vc_map' ) ) {
				   return;
			   }

			   $params = array();
			   if ( $this->is_svg_icon_plugin_active() ) {
				   $params[] = array(
					   'type' => 'svg_icon',
					   'heading' => __( 'Choose your SVG', 'svg-icons' ),
					   'param_name' => 'icon',
					   'value' => 'dev/wordpress',
					   'admin_label' => true,
					   'description' => __( 'Choose an icon. Type in the text box above to search for a specific icon.<br>NOTE: For best results, upload an SVG that\'s made up of outlines. If not, we will try our best to convert it into outlines for you.', 'svg-icons' ),
				   );
				   $params[] = array(
					   'type' => 'textarea_raw_html',
					   'heading' => __( 'Or, upload your own SVG', GAMBIT_VC_SVG_DRAW_ANIMATION ),
					   'param_name' => 'content',
					   'description' => __( 'NOTE: For best results, upload an SVG that\'s made up of outlines. If not, we will try our best to convert it into outlines for you.
											<br><br><a href="https://icons8.com/icon/pack/logos/ios">You can also get free stroke-based icons from icons8.</a><br>', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				   );
			   } else {
				   $params[] = array(
					   'type' => 'textarea_raw_html',
					   'heading' => __( 'Upload your own SVG', GAMBIT_VC_SVG_DRAW_ANIMATION ),
					   'param_name' => 'content',
					   'description' => __( 'NOTE: For best results, upload an SVG that\'s made up of outlines. If not, we will try our best to convert it into outlines for you.
											<br><br><a href="https://icons8.com/icon/pack/logos/ios">You can also get free stroke-based icons from icons8.</a><br>', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				   );
			   }

			   $params[] = array(
				  'type' => 'textfield',
				  'heading' => __( 'SVG Height', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'param_name' => 'size',
				  'value' => '100',
				  'description' => __( 'Enter height in pixels. You can also use other units like em, vw, etc.', GAMBIT_VC_SVG_DRAW_ANIMATION ),
			  );
			  $params[] = array(
				  'type' => 'textfield',
				  'heading' => __( 'Stroke Width', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'param_name' => 'stroke_width',
				  'value' => '1',
			  );
			  $params[] = array(
				  'type' => 'colorpicker',
				  'heading' => __( 'Stroke Color', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'param_name' => 'stroke_color',
				  'description' => __( 'Leave this blank to use the colors supplied in the SVG.', GAMBIT_VC_SVG_DRAW_ANIMATION ),
			  );
			  $params[] = array(
				  'type' => 'dropdown',
				  'heading' => __( 'Animation Type', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'param_name' => 'anim_type',
				  'value' => array(
					  __( 'Delayed', GAMBIT_VC_SVG_DRAW_ANIMATION ) => 'delayed',
					  __( 'Simultaneous', GAMBIT_VC_SVG_DRAW_ANIMATION ) => 'sync',
					  __( 'One line at a time', GAMBIT_VC_SVG_DRAW_ANIMATION ) => 'oneByOne',
				  ),
				  'group' => __( 'Animation', GAMBIT_VC_SVG_DRAW_ANIMATION ),
			  );
			  $params[] = array(
				  'type' => 'textfield',
				  'heading' => __( 'Animation Speed', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'param_name' => 'speed',
				  'value' => '1400',
				  'group' => __( 'Animation', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'description' => __( 'Animation speed in milliseconds', GAMBIT_VC_SVG_DRAW_ANIMATION ),
			  );
			  $params[] = array(
				  'type' => 'dropdown',
				  'heading' => __( 'Alignment', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				  'param_name' => 'alignment',
				  'value' => array(
					  __( 'Center', GAMBIT_VC_SVG_DRAW_ANIMATION ) => 'center',
					  __( 'Left', GAMBIT_VC_SVG_DRAW_ANIMATION ) => 'flex-start',
					  __( 'Right', GAMBIT_VC_SVG_DRAW_ANIMATION ) => 'flex-end',
				  ),
			  );

			   vc_map( array(
				   'base' => 'svg_draw_animation',
				   'name' => __( 'SVG Draw Animation', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				   'description' => __( 'Animate stroke-based SVGs', GAMBIT_VC_SVG_DRAW_ANIMATION ),
				   'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_SVG_DRAW_ANIMATION ) : '',
				   // URL to my icon for WPBakery.
				   'icon' => plugins_url( 'svg-draw-animation/images/SVG_Draw_Animation_Element_Icon.svg', __FILE__ ),
				   // All my attributes, define as many as we need.
				   'params' => $params,
			   ) );
		   }

		   /**
		    * SVG Draw Animation logic.
			*
			* @param array  $atts - The attributes of the shortcode.
			* @param string $content - The content enclosed inside the shortcode if any.
			* @return string - The rendered html.
			* @since 1.0
		    */
			public function render_shortcode( $atts, $content = '' ) {

				$defaults = array(
					'anim_type' => 'delayed',
					'speed' => '1400',
					'stroke_width' => '1',
					'stroke_color' => '',
					'size' => '100',
					'alignment' => 'center',
					'icon' => 'dev/wordpress', // Support for SVG Icon plugin.
				);

				if ( empty( $atts ) ) {
					$atts = array();
				}
				$atts = array_merge( $defaults, $atts );

				wp_enqueue_script( 'svg-draw-animation', plugins_url( 'svg-draw-animation/js/min/svg-draw-animation-min.js', __FILE__ ), array(), VERSION_GAMBIT_VC_SVG_DRAW_ANIMATION );
				wp_enqueue_style( 'svg-draw-animation', plugins_url( 'svg-draw-animation/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_SVG_DRAW_ANIMATION );

				$classes = array( 'gmb-asvg' );

				// Icon size. Check if not empty then check again if it is numeric and non-numeric also.
				if ( is_numeric( $atts['size'] ) ) {
					$size = $atts['size'] . 'px';
				} else {
					$size = $atts['size'];
				}

				$suffix = $this->is_svg_icon_plugin_active() && empty( $content ) ? '%' : 'px';
				if ( is_numeric( $atts['stroke_width'] ) ) {
					$strokeWidth = $atts['stroke_width'] . $suffix;
				} else {
					$strokeWidth = $atts['stroke_width'];
				}

				// Support for SVG Icon's icon picker.
				$icon = '';
				if ( $this->is_svg_icon_plugin_active() && ! empty( $atts['icon'] ) && empty( $content ) ) {
					$icon = gmb_svg_icons_get_icon( $atts['icon'] );
					$classes[] = 'is-svg-icon'; // Only when SVG Icon plugin is used.
				}
				if ( ! empty( $content ) ) {
					$icon = rawurldecode( base64_decode( strip_tags( $content ) ) );
				}

				$output = '<div class="' . implode( ' ', $classes ) . '" ' .
								'style="visibility: hidden; ' .
									( ! empty( $size ) ? 'height: ' . $size . ';' : '' ) .
									( ! empty( $atts['stroke_color'] ) ? '--stroke: ' . $atts['stroke_color'] . ';' : '' ) .
									( ! empty( $strokeWidth ) ? '--stroke-width: ' . $strokeWidth . ';' : '' ) .
									'justify-content: ' . ( ! empty( $atts['alignment'] ) ? $atts['alignment'] : '' ) . ';"' .
		 						'data-type="' . ( ! empty( $atts['anim_type'] ) ? $atts['anim_type'] : '' ) . '"' .
							 	'data-speed="' . ( ! empty( $atts['speed'] ) ? $atts['speed'] : '' ) . '">' .
							$icon .
							'</div>';

				 return $output;
			}
	 }
	 new SVG_Draw_Animation_Shortcode();
 }
