<?php
/**
 *  @package Text Effects for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

if ( ! class_exists( 'TextEffectsShortcode' ) ) {

	class TextEffectsShortcode {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialize plugin.
			add_shortcode( 'text_effects', array( $this, 'render_shortcode' ) );

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
				.vc_el-container [id='text_effects'] .vc_element-icon,
				.wpb_text_effects .wpb_element_title .vc_element-icon {
					background-image: url(" . plugins_url( 'text_effects/images/Text-Effects-Icon.png', __FILE__ ) . ');
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
				'base' => 'text_effects',
				'name' => __( 'Text Effects', GAMBIT_VC_TEXT_EFFECTS ),
				'description' => __( 'Animated typing text effect', GAMBIT_VC_TEXT_EFFECTS ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_VC_TEXT_EFFECTS ) : '',
				// URL to my icon for Visual Composer.
				'icon' => plugins_url( 'text-effects/images/Text-Effects-Icon.png', __FILE__ ),
				// All my attributes, define as many as we need.
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Animated Text', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'text_mid',
						'value' => __( 'Typing, Text, Effects', GAMBIT_VC_TEXT_EFFECTS ),
						'description' => __( 'The text that has the animation effect', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Text Effect', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'text_effect',
						'value' => array(
							__( 'Text Scrambled', GAMBIT_VC_TEXT_EFFECTS ) => 'text-scrambled',
							__( 'Typing', GAMBIT_VC_TEXT_EFFECTS ) => 'typing',
							__( 'Fade', GAMBIT_VC_TEXT_EFFECTS ) => 'fade',
							__( 'Top To Bottom', GAMBIT_VC_TEXT_EFFECTS ) => 'top-to-bottom',
							__( 'Bottom To Top', GAMBIT_VC_TEXT_EFFECTS ) => 'bottom-to-top',
							__( 'Vertical Flip', GAMBIT_VC_TEXT_EFFECTS ) => 'vertical-flip',
							__( 'Random Letters', GAMBIT_VC_TEXT_EFFECTS ) => 'random-letters',
						),
						'description' => __( 'Select the animation for your text effect.', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Prepend Text', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'text_before',
						'value' => '',
						'description' => __( 'Text that appears before the animated text', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Append Text', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'text_after',
						'value' => '',
						'description' => __( 'Text that appears after the animated text', GAMBIT_VC_TEXT_EFFECTS ),
					),

					array(
						'type' => 'dropdown',
						'heading' => __( 'Text Style', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'text_style',
						'value' => array(
							__( 'H1', GAMBIT_VC_TEXT_EFFECTS ) => 'h1',
							__( 'H2', GAMBIT_VC_TEXT_EFFECTS ) => 'h2',
							__( 'H3', GAMBIT_VC_TEXT_EFFECTS ) => 'h3',
							__( 'H4', GAMBIT_VC_TEXT_EFFECTS ) => 'h4',
							__( 'H5', GAMBIT_VC_TEXT_EFFECTS ) => 'h5',
							__( 'H6', GAMBIT_VC_TEXT_EFFECTS ) => 'h6',
							__( 'Body Text', GAMBIT_VC_TEXT_EFFECTS ) => 'div',
						),
						'std' => 'h2',
						'description' => __( 'Size/text style', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Alignment', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'text_align',
						'value' => array(
							__( 'Center', GAMBIT_VC_TEXT_EFFECTS ) => 'center',
							__( 'Left', GAMBIT_VC_TEXT_EFFECTS ) => 'left',
							__( 'Right', GAMBIT_VC_TEXT_EFFECTS ) => 'right',
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'Animation Speed', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'animation_speed',
						'value' => array(
							__( 'Fast', GAMBIT_VC_TEXT_EFFECTS ) => 'fast',
							__( 'Normal', GAMBIT_VC_TEXT_EFFECTS ) => 'normal',
							__( 'Slow', GAMBIT_VC_TEXT_EFFECTS ) => 'slow',
						),
						'std' => 'normal',
						'description' => __( 'The speed of the text animation.', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Animation Delay', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'animation_delay',
						'value' => __( '2000', GAMBIT_VC_TEXT_EFFECTS ),
						'description' => __( 'The pause duration between animations (in milliseconds)', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Font Size', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'font_size',
						'value' => '40',
						'description' => __( 'Font size in pixels', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Font Color', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'font_color',
						'description' => __( 'The color of the text including appended and prepended text', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Middle Text Background Color', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'middle_bg_color',
						'description' => __( 'Pick a color to add a solid background to the animated text. Leave blank if you don\'t want any background.', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Middle Text Color', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'middle_text_color',
						'description' => __( 'Select color for the animated text.', GAMBIT_VC_TEXT_EFFECTS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Scrambled Character Color', GAMBIT_VC_TEXT_EFFECTS ),
						'param_name' => 'char_color',
						'description' => __( 'The color of the scrambled characters for the text-scrambled animation.', GAMBIT_VC_TEXT_EFFECTS ),
						'value' => '#b7b7b7',
						'dependency' => array(
							'element' => 'text_effect',
							'value' => 'text-scrambled',
						),
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
				'text_before' => '',
				'text_after' => '',
				'text_mid' => 'Typing, Text, Effects',
				'text_effect' => 'text-scrambled',
				'char_color' => '#b7b7b7',
				'animation_delay' => '2000',
				'animation_speed' => 'normal',
				'font_size' => '40',
				'font_color' => '',
				'middle_bg_color' => '',
				'middle_text_color' => '',
				'text_align' => 'center',
				'text_style' => 'h2',
			);

			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			wp_enqueue_style( 'text-effects', plugins_url( 'text-effects/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_VC_TEXT_EFFECTS );
			wp_enqueue_script( 'text-effects', plugins_url( 'text-effects/js/min/text-effects-min.js', __FILE__ ), array(), VERSION_GAMBIT_VC_TEXT_EFFECTS );

			$style = array();
			$mid_styles = array( 'opacity: 0; ' );
			$has_bg_color = ! empty( $atts['middle_bg_color'] );

			if ( ! empty( $atts['font_color'] ) ) {
				$style[] = 'color: ' . $atts['font_color'];
			}

			if ( ! empty( $atts['font_size'] ) ) {
				$style[] = 'font-size: ' . $atts['font_size'] . 'px';
			}

			if ( ! empty( $atts['middle_text_color'] ) ) {
				$mid_styles[] = 'color: ' . $atts['middle_text_color'] . '; ';
			}

			if ( ! empty( $atts['middle_bg_color'] ) ) {
				$mid_styles[] = 'background-color: ' . $atts['middle_bg_color'] . '; ';
			}

			$style[] = 'text-align: ' . $atts['text_align'];
			$text_mid = preg_replace( '/\s*,\s*/', ',', $atts['text_mid'] );

			// Form the SEO text - temp text which will be removed on startup.
			$display_text = $text_mid;
			if ( false !== stripos( $display_text, ',' ) ) {
				$display_text = explode( ',', $text_mid );
				$display_text = $display_text[0];
			}

			$output = '<' . $atts['text_style'] . ' class="tte_wrapper ' . ( $has_bg_color ? '' : 'no-bg-color' ) . '" data-effect="' . esc_attr( $atts['text_effect'] ) . '" data-delay="' . esc_attr( (int) $atts['animation_delay'] ) . '" data-speed="' . esc_attr( $atts['animation_speed'] ) . '" char-color="' . esc_attr( $atts['char_color'] ) . '" style="' . esc_attr( implode( '; ', $style ) ) . '">' .
				'<span class="tte_before">' . $atts['text_before'] . '</span>' .
				'<span class="tte_mid" data-text="' . esc_attr( $text_mid ) . '" style="' . esc_attr( implode( ';', $mid_styles ) ) . '">' . $display_text . '</span>' .
				'<span class="tte_after">' . $atts['text_after'] . '</span>' .
				'</' . $atts['text_style'] . '>';

			return $output;
		}
	}
	new TextEffectsShortcode();
} // End if().
