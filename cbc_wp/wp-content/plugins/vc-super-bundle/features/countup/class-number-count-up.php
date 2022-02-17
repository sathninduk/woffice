<?php
/**
 *  @package Number Count Up for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Number_Count_Up_Shortcode' ) ) {

	class Number_Count_Up_Shortcode {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialize plugin.
			add_shortcode( 'count_up', array( $this, 'render_shortcode' ) );

			// Create as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcode' ), 999 );

			// Add styles to add the element icon, see more in function desc.
			add_action( 'admin_head', array( $this, 'fix_vc_css' ) );

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
				.vc_el-container [id='number_count_up'] .vc_element-icon,
				.wpb_number_count_up .wpb_element_title .vc_element-icon {
					background-image: url(" . plugins_url( 'number_count_up/images/Number-Count-Up-Icon.svg', __FILE__ ) . ');
				}
			</style>';
		}

		/**
		 * Creates the necessary shortcode for text effects.
		 *
		 * @since 1.0
		 */
		public function create_shortcode() {
			if ( ! function_exists( 'vc_map' ) ) {
				return;
			}

			vc_map( array(
				'base' => 'count_up',
				'name' => __( 'Number Count Up', GAMBIT_VC_NUMBER_COUNT_UP ),
				'description' => __( 'Animated number count up', GAMBIT_VC_NUMBER_COUNT_UP ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle' ) : '',
				// URL to my icon for Visual Composer.
				'icon' => plugins_url( 'number-count-up/images/Number-Count-Up-Icon.svg', __FILE__ ),
				// All my attributes, define as many as we need.
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Number Count Up Text', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'content',
						'value' => __( '9,123,456.78', GAMBIT_VC_NUMBER_COUNT_UP ),
						'description' => __( 'Enter the number to animate. You can add non-numeric text like "$1,234/year". If you add multiple numbers, they will animate at the same time.', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Heading', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'heading',
						'value' => __( 'Heading', GAMBIT_VC_NUMBER_COUNT_UP ),
						'description' => __( 'Text that appears before the number count up text', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Description', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'desc',
						'value' => __( 'Description', GAMBIT_VC_NUMBER_COUNT_UP ),
						'description' => __( 'Text that appears after the number count up text', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Alignment', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'text_align',
						'value' => array(
							__( 'Center', GAMBIT_VC_NUMBER_COUNT_UP ) => 'center',
							__( 'Left', GAMBIT_VC_NUMBER_COUNT_UP ) => 'left',
							__( 'Right', GAMBIT_VC_NUMBER_COUNT_UP ) => 'right',
						),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Number Count Up Font Size', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'countup_font_size',
						'value' => '64',
						'group' => __( 'Styles', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Number Count Up Text Font Color', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'countup_color',
						'group' => __( 'Styles', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Heading Font Size', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'heading_font_size',
						'value' => '18',
						'group' => __( 'Styles', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Heading Font Color', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'heading_color',
						'group' => __( 'Styles', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Description Font Size', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'desc_font_size',
						'value' => '16',
						'group' => __( 'Styles', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Description Text Color', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'desc_color',
						'group' => __( 'Styles', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animation Speed', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'animation_speed',
						'value' => __( '1000', GAMBIT_VC_NUMBER_COUNT_UP ),
						'description' => __( 'Speed of the text animation', GAMBIT_VC_NUMBER_COUNT_UP ),
						'group' => __( 'Speed', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animation Delay', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'animation_delay',
						'value' => __( '16', GAMBIT_VC_NUMBER_COUNT_UP ),
						'description' => __( 'The pause duration between animations (in milliseconds)', GAMBIT_VC_NUMBER_COUNT_UP ),
						'group' => __( 'Speed', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Number Count Up Font Size', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'countup_font_size_mobile',
						'description' => __( 'Number count up\'s font size in mobile', GAMBIT_VC_NUMBER_COUNT_UP ),
						'group' => __( 'Responsiveness', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Heading Font Size', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'heading_font_size_mobile',
						'description' => __( 'Heading\'s font size in mobile', GAMBIT_VC_NUMBER_COUNT_UP ),
						'group' => __( 'Responsiveness', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Description Font Size', GAMBIT_VC_NUMBER_COUNT_UP ),
						'param_name' => 'desc_font_size_mobile',
						'description' => __( 'Description\'s font size in mobile', GAMBIT_VC_NUMBER_COUNT_UP ),
						'group' => __( 'Responsiveness', GAMBIT_VC_NUMBER_COUNT_UP ),
					),
				),
			) );
		}


		/**
		 * Text Effects Shortcode logic.
		 *
		 * @param array  $atts - The attributes of the shortcode.
		 * @param string $content - The content enclosed inside the shortcode if any.
		 * @return string - The rendered html.
		 * @since 1.0
		 */
		public function render_shortcode( $atts, $content = '' ) {

			$defaults = array(
				'heading' => 'Heading',
				'desc' => 'Description',
				'text_align' => 'center',
				'animation_delay' => '16',
				'animation_speed' => '1000',
				'countup_font_size' => '64',
				'heading_font_size' => '18',
				'desc_font_size' => '16',
				'heading_color' => '',
				'countup_color' => '',
				'desc_color' => '',
				'heading_font_size_mobile' => '',
				'countup_font_size_mobile' => '',
				'desc_font_size_mobile' => '',
			);

			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			wp_enqueue_style( 'number-count-up', plugins_url( 'number-count-up/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_NUMBER_COUNT_UP );
			wp_enqueue_script( 'number-count-up', plugins_url( 'number-count-up/js/min/number-count-up-min.js', __FILE__ ), array( 'waypoints' ), VERSION_GAMBIT_VC_NUMBER_COUNT_UP );
			wp_enqueue_script( 'jquery' );
			if ( function_exists( 'vc_asset_url' ) ) {
				wp_enqueue_script( 'waypoints', vc_asset_url( 'lib/waypoints/waypoints.min.js' ), array( 'jquery' ), VERSION_GAMBIT_VC_NUMBER_COUNT_UP, true );
			}

			$countUpStyle = array();
			$headingStyle = array();
			$descStyle = array();
			$style = array();
			$displayContent = '';

			// Mobile Font Sizes.
			if ( ! empty( $atts['heading_font_size_mobile'] ) ) {
				if ( is_numeric( $atts['heading_font_size_mobile'] ) ) {
					$headingStyle[] = '--mobile-font-size: ' . $atts['heading_font_size_mobile'] . 'px';
				} else {
					$headingStyle[] = '--mobile-font-size: ' . $atts['heading_font_size_mobile'];
				}
			}
			if ( ! empty( $atts['countup_font_size_mobile'] ) ) {
				if ( is_numeric( $atts['countup_font_size_mobile'] ) ) {
					$countUpStyle[] = '--mobile-font-size: ' . $atts['countup_font_size_mobile'] . 'px';
				} else {
					$countUpStyle[] = '--mobile-font-size: ' . $atts['countup_font_size_mobile'];
				}
			}
			if ( ! empty( $atts['desc_font_size_mobile'] ) ) {
				if ( is_numeric( $atts['desc_font_size_mobile'] ) ) {
					$descStyle[] = '--mobile-font-size: ' . $atts['desc_font_size_mobile'] . 'px';
				} else {
					$descStyle[] = '--mobile-font-size: ' . $atts['desc_font_size_mobile'];
				}
			}

			// Font Sizes.
			if ( ! empty( $atts['countup_font_size'] ) ) {
				if ( is_numeric( $atts['countup_font_size'] ) ) {
				    $countUpStyle[] = '--font-size: ' . $atts['countup_font_size'] . 'px';
				} else {
				    $countUpStyle[] = '--font-size: ' . $atts['countup_font_size'];
				}
			}
			if ( ! empty( $atts['heading_font_size'] ) ) {
				if ( is_numeric( $atts['heading_font_size'] ) ) {
				    $headingStyle[] = '--font-size: ' . $atts['heading_font_size'] . 'px';
				} else {
				    $headingStyle[] = '--font-size: ' . $atts['heading_font_size'];
				}
			}
			if ( ! empty( $atts['desc_font_size'] ) ) {
				if ( is_numeric( $atts['desc_font_size'] ) ) {
				    $descStyle[] = '--font-size: ' . $atts['desc_font_size'] . 'px';
				} else {
				    $descStyle[] = '--font-size: ' . $atts['desc_font_size'];
				}
			}

			// Font Colors.
			if ( ! empty( $atts['heading_color'] ) ) {
				$headingStyle[] = 'color: ' . $atts['heading_color'];
			}
			if ( ! empty( $atts['countup_color'] ) ) {
				$countUpStyle[] = 'color: ' . $atts['countup_color'];
			}
			if ( ! empty( $atts['desc_color'] ) ) {
				$descStyle[] = 'color: ' . $atts['desc_color'];
			}

			// Text Alignment.
			$style[] = 'text-align: ' . $atts['text_align'];

			$output = '<div class="number-count-up-vc" style="' . esc_attr( implode( '; ', $style ) ) . '">
							' . ( ! empty( $atts[ 'heading' ] ) ? '<h4 style="' . esc_attr( implode( '; ', $headingStyle ) ) . '">' . $atts[ 'heading' ] . '</h4>' : '' ) .
							' <div class="number-counter wpb_animate_when_almost_visible" data-delay="' . ( ! empty( $atts['animation_delay'] ) ? esc_attr( (int) $atts['animation_delay'] ) : 1 ) . '" data-duration="' . esc_attr( $atts['animation_speed'] ) . '"  style="' . esc_attr( implode( '; ', $countUpStyle ) ) . '">
							' . $content . '</div>
							' . ( ! empty( $atts[ 'desc' ] ) ? '<p style="' . esc_attr( implode( '; ', $descStyle ) ) . '">' . $atts[ 'desc' ] . '</p>' : '' ) .
						'</div>';

			return $output;
		}
	}
	new Number_Count_Up_Shortcode();
} // End if().
