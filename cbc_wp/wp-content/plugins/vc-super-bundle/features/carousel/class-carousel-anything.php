<?php
/**
 * Turn any Visual Composer element into a carousel element.
 *
 * @package	Carousel Anything for VC
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'GambitCarouselAnything' ) ) {

	/**
	 * Sorry, but that doesn't include your kitchen sink.
	 */
	class GambitCarouselAnything {

		/**
		 * Sets a unique identifier of each carousel.
		 *
		 * @var int id - Counts and uniquely identifies each carousel rendered.
		 */
		private static $id = 0;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initializes VC shortcode.
			add_filter( 'init', array( $this, 'create_ca_shortcodes' ), 999 );

			// Render shortcode for the plugin.
			add_shortcode( 'carousel_anything', array( $this, 'render_ca_shortcodes' ) );

			// Enqueues scripts and styles specific for all parts of the plugin.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_css' ), 5 );
		}


		/**
		 * Includes normal scripts and css purposed globally by the plugin.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function enqueue_frontend_scripts_and_css() {

			// Loads the general styles used by the carousel.
			wp_enqueue_style( 'carousel-anything-css', plugins_url( 'carousel-anything/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads styling specific to Owl Carousel.
			wp_enqueue_style( 'carousel-anything-owl', plugins_url( 'carousel-anything/css/owl.theme.default.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads scripts specific to Owl Carousel.
			wp_enqueue_script( 'carousel-anything-owl', plugins_url( 'carousel-anything/js/min/owl.carousel2-min.js', __FILE__ ), array( 'jquery' ), '1.3.3' );

			// Loads transitions specific to Owl Carousel.
			wp_enqueue_style( 'carousel-anything-transitions', plugins_url( 'carousel-anything/css/owl.carousel.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			wp_enqueue_style( 'carousel-anything-animate', plugins_url( 'carousel-anything/css/animate.css', __FILE__ ), array(), VERSION_GAMBIT_CAROUSEL_ANYTHING );

			// Loads scripts.
			wp_enqueue_script( 'carousel-anything', plugins_url( 'carousel-anything/js/min/script-min.js', __FILE__ ), array( 'jquery' ), VERSION_GAMBIT_CAROUSEL_ANYTHING );
		}


		/**
		 * Creates the carousel element inside VC.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function create_ca_shortcodes() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			// $default_content = '[vc_row_inner][vc_column_inner width="1/1"][/vc_column_inner][/vc_row_inner]';
			$default_content = '[vc_row_inner][vc_column_inner][/vc_column_inner][/vc_row_inner][vc_row_inner][vc_column_inner][/vc_column_inner][/vc_row_inner][vc_row_inner][vc_column_inner][/vc_column_inner][/vc_row_inner]';
			// $default_content = '';
			if ( vc_is_frontend_editor() ) {
				$default_content = '';
			}

			// Loads fixes that makes Carousel Anything possible.
			ca_row_fixes();
			vc_map( array(
				'name' => __( 'Carousel Anything', GAMBIT_CAROUSEL_ANYTHING ),
				'base' => 'carousel_anything',
				'icon' => plugins_url( 'carousel-anything/images/carousel-icon.svg', GAMBIT_CAROUSEL_ANYTHING_FILE ),
				'description' => __( 'A modern and responsive content carousel system', GAMBIT_CAROUSEL_ANYTHING ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle' ) : '',
				'as_parent' => array( 'only' => 'vc_row,vc_row_inner' ),
				'js_view' => 'VcColumnView',
				'content_element' => true,
				//'is_container' => true,
				'admin_enqueue_css' => plugins_url( 'carousel-anything/css/admin.css', __FILE__ ),
				'container_not_allowed' => false,
				'default_content' => $default_content,
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Items to display on screen', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'items',
						'value' => '1',
						'group' => __( 'General Options', GAMBIT_CAROUSEL_ANYTHING ),
						'description' => __( 'Maximum items to display at a time', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Slide Animations', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'slide_anim',
						'value' => array(
							__( 'None', GAMBIT_CAROUSEL_ANYTHING ) => '',
							__( 'Bounce', GAMBIT_CAROUSEL_ANYTHING ) => 'bounce',
							__( 'Flash', GAMBIT_CAROUSEL_ANYTHING ) => 'flash',
							__( 'Pulse', GAMBIT_CAROUSEL_ANYTHING ) => 'pulse',
							__( 'RubberBand', GAMBIT_CAROUSEL_ANYTHING ) => 'rubberband',
							__( 'Shake', GAMBIT_CAROUSEL_ANYTHING ) => 'shake',
							__( 'Swing', GAMBIT_CAROUSEL_ANYTHING ) => 'swing',
							__( 'Tada', GAMBIT_CAROUSEL_ANYTHING ) => 'tada',
							__( 'Wobble', GAMBIT_CAROUSEL_ANYTHING ) => 'wobble',
							__( 'Jello', GAMBIT_CAROUSEL_ANYTHING ) => 'jello',
							__( 'Bounce In', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceIn',
							__( 'Bounce In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInDown',
							__( 'Bounce In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInLeft',
							__( 'Bounce In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInRight',
							__( 'Bounce In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceInUp',
							// __( 'Bounce Out', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOut',
							// __( 'Bounce Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutDown',
							// __( 'Bounce Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutLeft',
							// __( 'Bounce Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutRight',
							// __( 'Bounce Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'bounceOutUp',
							__( 'Fade In', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeIn',
							__( 'Fade In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInDown',
							__( 'Fade In Down Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInDownBig',
							__( 'Fade In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInLeft',
							__( 'Fade In Left Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInLeftBig',
							__( 'Fade In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInRight',
							__( 'Fade In Right Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInRightBig',
							__( 'Fade In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInUp',
							__( 'Fade In Up Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeInUpBig',
							// __( 'Fade Out', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOut',
							// __( 'Fade Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutDown',
							// __( 'Fade Out Down Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutDownBig',
							// __( 'Fade Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutLeft',
							// __( 'Fade Out Left Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutLeftBig',
							// __( 'Fade Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutRight',
							// __( 'Fade Out Right Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutRightBig',
							// __( 'Fade Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutUp',
							// __( 'Fade Out Up Big', GAMBIT_CAROUSEL_ANYTHING ) => 'fadeOutUpBig',
							__( 'Flip', GAMBIT_CAROUSEL_ANYTHING ) => 'flip',
							__( 'Flip In X', GAMBIT_CAROUSEL_ANYTHING ) => 'flipInX',
							__( 'Flip In Y', GAMBIT_CAROUSEL_ANYTHING ) => 'flipInY',
							// __( 'Flip Out X', GAMBIT_CAROUSEL_ANYTHING ) => 'flipOutX',
							// __( 'Flip Out Y', GAMBIT_CAROUSEL_ANYTHING ) => 'flipOutY',
							__( 'Light Speed In', GAMBIT_CAROUSEL_ANYTHING ) => 'lightSpeedIn',
							// __( 'Light Speed Out', GAMBIT_CAROUSEL_ANYTHING ) => 'lightSpeedOut',
							__( 'Rotate In', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateIn',
							__( 'Rotate In Down Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInDownLeft',
							__( 'Rotate In Down Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInDownRight',
							__( 'Rotate In Up Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInUpLeft',
							__( 'Rotate In Up Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateInUpRight',
							// __( 'Rotate Out', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOut',
							// __( 'Rotate Out Down Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutDownLeft',
							// __( 'Rotate Out Down Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutDownRight',
							// __( 'Rotate Out Up Left', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutUpLeft',
							// __( 'Rotate Out Up Right', GAMBIT_CAROUSEL_ANYTHING ) => 'rotateOutUpRight',
							__( 'Slide In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInUp',
							__( 'Slide In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInDown',
							__( 'Slide In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInLeft',
							__( 'Slide In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'slideInRight',
							// __( 'Slide Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutUp',
							// __( 'Slide Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutDown',
							// __( 'Slide Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutLeft',
							// __( 'Slide Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'slideOutRight',
							__( 'Zoom In', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomIn',
							__( 'Zoom In Down', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInDown',
							__( 'Zoom In Left', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInLeft',
							__( 'Zoom In Right', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInRight',
							__( 'Zoom In Up', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomInUp',
							// __( 'Zoom Out', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOut',
							// __( 'Zoom Out Down', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutDown',
							// __( 'Zoom Out Left', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutLeft',
							// __( 'Zoom Out Right', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutRight',
							// __( 'Zoom Out Up', GAMBIT_CAROUSEL_ANYTHING ) => 'zoomOutUp',
							__( 'Hinge', GAMBIT_CAROUSEL_ANYTHING ) => 'hinge',
							__( 'Jack In The Box', GAMBIT_CAROUSEL_ANYTHING ) => 'jackInTheBox',
							__( 'Roll In', GAMBIT_CAROUSEL_ANYTHING ) => 'rollIn',
							// __( 'Roll Out', GAMBIT_CAROUSEL_ANYTHING ) => 'rollOut',
						),
						'group' => __( 'General Options', GAMBIT_CAROUSEL_ANYTHING ),
						'description' => __( 'Note: Slide Animations only work with one item per slide and only in modern browsers. Slide Animations will not work on touch dragging and has to be navigated using thumbnails or arrows.', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Navigation', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'thumbnails',
						'value' => array(
							__( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ) => 'thumbnail',
							__( 'Arrows (will display navigation arrows at each side)', GAMBIT_CAROUSEL_ANYTHING ) => 'arrow',
							__( 'Thumbnails and Arrows', GAMBIT_CAROUSEL_ANYTHING ) => 'both',
							__( 'None', GAMBIT_CAROUSEL_ANYTHING ) => 'none',
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
						'description' => '',
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Navigation Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'thumbnail_shape',
						'value' => array(
							__( 'Circle', GAMBIT_CAROUSEL_ANYTHING ) => 'circles',
							__( 'Square', GAMBIT_CAROUSEL_ANYTHING ) => 'squares',
						),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'thumbnail', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
						'description' => __( 'Select the thumbnail type for your carousel for navigation', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Thumbnail Offset', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'thumbnail_offset',
						'value' => '15',
						'description' => __( 'The distance of the thumbnails from the carousel, in pixels', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'thumbnail', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Thumbnail Default Color', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'thumbnail_color',
						'value' => '#c3cbc8',
						'description' => __( 'The color of the non-active thumbnail. Not applicable to Arrows type of navigation', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'thumbnail', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Thumbnail Active Color', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'thumbnail_active_color',
						'value' => '#869791',
						'description' => __( 'The color of the active / current thumbnail. Not applicable to Arrows type of navigation', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'thumbnail', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Arrow Offset', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'arrows_offset',
						'value' => '40',
						'description' => __( 'The horizontal distance of the arrows, in pixels. Use this to either put the arrows within the box or outside of it', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'arrow', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Arrow Alignment', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'arrows_alignment',
						'value' => '50',
						'description' => __( 'Use this to align the arrows vertically to an ideal place, such as portrait areas in the carousel', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'arrow', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Arrows Size', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'arrows_size',
						'value' => '20',
						'description' => __( 'Customize the size of arrows in pixels', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'arrow', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Arrows Default Color', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'arrows_color',
						'value' => '#c3cbc8',
						'description' => __( 'The default color of the navigation arrow', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'arrow', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Arrows Active Color', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'arrows_active_color',
						'value' => '#869791',
						'description' => __( 'The color of the active / current arrows when highlighted', GAMBIT_CAROUSEL_ANYTHING ),
						'dependency' => array(
							'element' => 'thumbnails',
							'value' => array( 'arrow', 'both' ),
						),
						'group' => __( 'Thumbnails', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Items to display on tablets', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'items_tablet',
						'value' => '1',
						'group' => __( 'Responsive', GAMBIT_CAROUSEL_ANYTHING ),
						'description' => __( 'Maximum items to display at a time for tablet devices', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Items to display on mobile phones', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'items_mobile',
						'value' => '1',
						'group' => __( 'Responsive', GAMBIT_CAROUSEL_ANYTHING ),
						'description' => __( 'Maximum items to display at a time for mobile devices', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Autoplay', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'autoplay',
						'value' => '5000',
						'description' => __( 'Enter an amount in milliseconds for the carousel to move. Leave blank to disable autoplay', GAMBIT_CAROUSEL_ANYTHING ),
						'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'checkbox',
						'heading' => '',
						'param_name' => 'stop_on_hover',
						'value' => array( __( 'Pause the carousel when the mouse is hovered onto it.', GAMBIT_CAROUSEL_ANYTHING ) => 'true' ),
						'description' => '',
						'dependency' => array(
							'element' => 'autoplay',
							'not_empty' => true,
						),
						'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Scroll Speed', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'speed_scroll',
						'value' => '800',
						'description' => __( 'The speed the carousel scrolls in milliseconds. Use a reasonable duration, as slower speeds higher than 1500ms may bring unpredictable results in browsers', GAMBIT_CAROUSEL_ANYTHING ),
						'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'checkbox',
						'heading' => '',
						'param_name' => 'touchdrag',
						'value' => array( __( 'Check this box to disable touch dragging of the carousel. (Normally enabled by default)', GAMBIT_CAROUSEL_ANYTHING ) => 'true' ),
						'description' => '',
						'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Keyboard Navigation', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'keyboard',
						'value' => array(
							__( 'Enable keyboard navigation', GAMBIT_CAROUSEL_ANYTHING ) => 'cursor',
						),
						'description' => '',
						'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Custom Class', GAMBIT_CAROUSEL_ANYTHING ),
						'param_name' => 'class',
						'value' => '',
						'description' => __( 'Add a custom class name for the carousel here', GAMBIT_CAROUSEL_ANYTHING ),
						'group' => __( 'Advanced', GAMBIT_CAROUSEL_ANYTHING ),
					),
					array(
						'type' => 'css_editor',
						'heading' => __( 'CSS box', 'js_composer' ),
						'param_name' => 'css',
						'group' => __( 'Design Options', 'js_composer' ),
					),
				),
				//'js_view' => 'VcRowView',
			) );
		}


		/**
		 * Shortcode logic
		 *
		 * @param array  $atts - WordPress shortcode attributes, defined by Visual Composer.
		 * @param string $content - Not needed in this plugin.
		 * @return	string The rendered html
		 * @since	1.0
		 */
		public function render_ca_shortcodes( $atts, $content = null ) {
			$defaults = array(
				'start' => '1',
				'items' => '1',
				'items_tablet' => '1',
				'items_mobile' => '1',
				'autoplay' => '5000',
				'stop_on_hover' => false,
				'scroll_per_page' => false,
				'speed_scroll' => '800',
				'speed_rewind' => '1000',
				'thumbnails' => 'thumbnail',
				'thumbnail_shape' => 'circles',
				'thumbnail_color' => '#c3cbc8',
				'thumbnail_active_color' => '#869791',
				'thumbnail_numbers' => false,
				'thumbnail_number_color' => '#ffffff',
				'thumbnail_number_active_color' => '#ffffff',
				'thumbnail_offset' => '15',
				'arrows_color' => '#c3cbc8',
				'arrows_active_color' => '#869791',
				'arrows_inactive_color' => '#ffffff',
				'arrows_size' => '20',
				'arrows_offset' => '40',
				'arrows_alignment' => '50',
				'touchdrag' => 'false',
				'keyboard' => 'false',
				'class' => '',
				'css' => '',
				'slide_anim' => '',
				// 'exit_anim' => '',

				// Backward compatibility. These attributes are no longer used.
				'equal_slide_height' => 'true',
			);
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			self::$id++;
			$id = 'carousel-anything-' . esc_attr( self::$id );

			// Initialize styles.
			wp_register_style( 'gambit-carousel-anything-styles', false );

			$styles = '';

			// If styles is populated, do this.
			if ( '' != $atts['css'] ) {
				preg_match_all('/{(.*?)}/', $atts['css'], $matches, PREG_SET_ORDER, 0);

				$styles .= !empty( $matches ) ? '#' . $id . $matches[0][0] : '';
			}

			// Parse what to show.
			$arrows_offset = ! empty( $atts['arrows_offset'] ) && is_numeric( $atts['arrows_offset'] ) ? $atts['arrows_offset'] * -1 : '-40';

			$items = '' != $atts['items'] ? $atts['items'] : '3';
			$items_tablet = '' != $atts['items_tablet'] ?  $atts['items_tablet'] : '2';
			$items_mobile = '' != $atts['items_mobile'] ? $atts['items_mobile'] : '1';

			$ret = '';

			// Thumbnail styles.
			$carousel_class = '';
			$navigation_buttons = 'true';
			if ( 'none' != $atts['thumbnails'] ) {
				if ( 'thumbnail' == $atts['thumbnails'] || 'both' == $atts['thumbnails'] ) {
					if ( '' != $atts['thumbnail_color'] ) {
						$styles .= "#{$id} .owl-dots .owl-dot span { opacity: 1; background-color: " . esc_attr( $atts['thumbnail_color'] ) . ' }';
					}
					if ( '' != $atts['thumbnail_active_color'] ) {
						$styles .= "#{$id} .owl-dots .owl-dot.active span { background-color: " . esc_attr( $atts['thumbnail_active_color'] ) . ' }';
					}
					if ( 'squares' == $atts['thumbnail_shape'] || 'diamonds' == $atts['thumbnail_shape'] ) {
						$styles .= "#{$id} .owl-dots .owl-dot span { border-radius: 0 }";
						if ( 'diamonds' == $atts['thumbnail_shape'] ) {
							$styles .= "#{$id} .owl-dots .owl-dot { transform: rotate(45deg); -webkit-transform: rotate(45deg); }";
						}
					}
				} else {
					$styles .= "#{$id} .owl-dots { display: none; }";
				}
				if ( 'arrow' == $atts['thumbnails'] || 'both' == $atts['thumbnails'] ) {
					$carousel_class = ' has-arrows';
					$navigation_buttons = 'true';
					$styles .= "#{$id} .owl-prev, #{$id} .owl-next { width: " . $atts['arrows_size'] . 'px !important; }';
					$styles .= "#{$id} .owl-prev::before, #{$id} .owl-next::before { color: " . $atts['arrows_color'] . ' !important; font-size: ' . $atts['arrows_size'] . 'px !important; }';
					$styles .= "#{$id} .owl-prev:hover::before, #{$id} .owl-next:hover::before { color: " . $atts['arrows_active_color'] . '!important; }';
				} else {
					$styles .= "#{$id} .owl-nav { display: none; }";
				}

				// Create the margin only if we're using thumbnails.
				if ( ! empty( $atts['thumbnail_offset'] ) && ( 'thumbnail' == $atts['thumbnails'] || 'both' == $atts['thumbnails'] ) ) {
					$styles .= "#{$id} .owl-dots { margin-top: " . esc_attr( $atts['thumbnail_offset'] ) . 'px; }';
				}

				if ( ! empty( $atts['arrows_offset'] ) ) {
					$styles .= '#' . $id . '.has-arrows .owl-dots .owl-nav .owl-prev { left: ' . esc_attr( $arrows_offset ) . 'px } ';
					$styles .= '#' . $id . '.has-arrows .owl-dots .owl-nav .owl-next { left: auto; right: ' . esc_attr( $arrows_offset ) . 'px } ';
				}
			}
			else {
				$navigation_buttons = 'false';
			}
			if ( ! empty( $atts['class'] ) ) {
				$carousel_class .= ' ' . esc_attr( $atts['class'] );
			}
			if ( $styles ) {
				wp_enqueue_style( 'gambit-carousel-anything-styles' );
				wp_add_inline_style( 'gambit-carousel-anything-styles', $styles );
			}

			if ( 'true' == $navigation_buttons ) {
				wp_enqueue_style( 'dashicons' );
			}

			// Backward compatibility. This attribute is no longer used.
			if ( 'true' == $atts['equal_slide_height'] ) {
				$carousel_class .= ' ca-equal-height';
			}

			// Enable filters for the shortcode content, if it exists.
			$content = apply_filters( 'gambit_ca_output', $content );

			$columns = substr_count( $content, '[vc_row_inner]' );

			$slide_number = ( $atts['start'] > 0 ? $atts['start'] - 1 : $atts['start'] );

			$rtl = is_rtl() ? 'true' : 'false';

			// Carousel html.
			$ret .= '<div style="visibility: hidden;" id="' . esc_attr( $id ) . '" class="gambit-carousel-anything carousel-anything-container owl-ca-carousel ' . $carousel_class . '" data-slide-anim="' . esc_attr( $atts['slide_anim'] ) . '" data-rtl="' . esc_attr( $rtl ) . '" data-items="' . esc_attr( $atts['items'] ) . '"';
			$ret .= 'data-totalitems="' . esc_attr( $columns ) . '" data-scroll_per_page="' . esc_attr( $atts['scroll_per_page'] ) . '" data-autoplay="' . esc_attr( empty( $atts['autoplay'] ) || '0' == $atts['autoplay'] ? 'false' : $atts['autoplay'] ) . '"';
			$ret .= 'data-items-tablet="' . esc_attr( $items_tablet ) . '" data-items-mobile="' . esc_attr( $items_mobile ) . '" data-stop-on-hover="' . esc_attr( $atts['stop_on_hover'] ) . '" data-speed-scroll="' . esc_attr( $atts['speed_scroll'] ) . '" data-speed-rewind="' . esc_attr( $atts['speed_rewind'] ) . '"';
			$ret .= 'data-thumbnails="' . esc_attr( $atts['thumbnails'] ) . '" data-navigation="' . esc_attr( $navigation_buttons ) . '" data-touchdrag="' . esc_attr( $atts['touchdrag'] ) . '" data-keyboard="' . esc_attr( $atts['keyboard'] ) . '" data-alignment="' . esc_attr( $atts['arrows_alignment'] ) . '"';
			$ret .= 'data-start="' . esc_attr( $slide_number ) . '">';
			$ret .= do_shortcode( $content ) . '</div>';

			return $ret;
		}
	}
	new GambitCarouselAnything();
}

if ( ! function_exists( 'ca_row_fixes' ) ) {

	/**
	 * Loads the fixes that makes Carousel Anything work.
	 *
	 * @return	void
	 * @since	1.5
	 */
	function ca_row_fixes() {

		$create_class = false;

		/**
		 * We need to define this so that VC will show our nesting container correctly.
		 */
		if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Carousel_Anything' ) ) {
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
			if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Carousel_Anything' ) ) {
				$create_class = true;
			}
		}

		if ( $create_class ) {

			/**
			 * Defines a subclass of the shortcodes container, for modifed Visual Composer modules.
			 *
			 * @package	carousel-anything
			 * @class WPBakeryShortCode_Carousel_Anything
			 */
			class WPBakeryShortCode_Carousel_Anything extends WPBakeryShortCodesContainer {
			}
		}
	}
}
