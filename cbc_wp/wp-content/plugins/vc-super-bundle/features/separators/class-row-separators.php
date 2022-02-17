<?php
/**
 * Where all the row separators are rendered.
 *
 * @package Row Separators
 */

/**
 * This is the main script that adds in the plugin's main shortcode/behavior/etc.
 * Each important component has it's own class-shortcode.php file.
 *
 * For example, in Parallax, we have a class-parallax-row.php for the parallax row element,
 * and class-fullwidth-row.php for the full-width row element.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
// Initializes plugin class.
if ( ! class_exists( 'GambitRowSeparator' ) ) {

	/**
	 * This is where all the plugin's functionality happens.
	 */
	class GambitRowSeparator {

		/**
		 * Sets a unique identifier of each separator.
		 *
		 * @var int id - Separator count, that uniquely identifies each separator rendered.
		 */
		private static $id = 0;

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initializes as a Visual Composer addon.
			add_action( 'init', array( $this, 'create_shortcode' ), 999 );

			// Makes the plugin function accessible as a shortcode.
			add_shortcode( 'row_separator', array( $this, 'render_shortcode' ) );
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

			require_once( 'row-separators/lib/svgs.php' );

			vc_map( array(
			    'name' => __( 'Row Separator', GAMBIT_ROW_SEPARATORS ),
			    'base' => 'row_separator',
				'icon' => plugins_url( 'row-separators/images/row-separator-icon.svg', __FILE__ ),
				'description' => __( 'A cool top/bottom separator for your row', GAMBIT_ROW_SEPARATORS ),
				'admin_enqueue_css' => plugins_url( 'row-separators/css/admin.css', __FILE__ ),
				'category' => defined( 'GAMBIT_VC_SUPER_BUNDLE' ) ? __( 'Super Bundle', GAMBIT_ROW_SEPARATORS ) : '',
			    'params' => array(
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Row Separator Type / Location', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'location',
						'value' => array(
							__( 'Top Separator', GAMBIT_ROW_SEPARATORS ) => 'top',
							__( 'Bottom Separator', GAMBIT_ROW_SEPARATORS ) => 'bottom',
						),
	                    'description' => __( 'For best results, ensure that your Visual Composer row is set to Full Width', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'span',
						'heading' => __( 'Row Separator', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'separator',
						'value' => gambit_row_separators_get_svg_names(),
	                    'description' => __( 'Choose the design of the row separator', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Flip Separator Horizontally', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'flip',
						'value' => array(
							__( 'Flip the separator horizontally', GAMBIT_ROW_SEPARATORS ) => '1',
						),
	                    'description' => __( 'You can flip the separator horizontally for more variation', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Height Scale', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'scale',
						'value' => '1',
	                    'description' => __( 'You can scale the separator to be larger or smaller. Use value between 0 and 1 to make the separator smaller, and more than 1 to make it larger.<br>You may use decimal values if necessary', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Decoration 1 Color', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'color1',
						'value' => '#95A5A6',
	                    'description' => __( 'Separator designs have 1-2 decoration colors, pick the color for the first decoration here', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Decoration 2 Color', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'color2',
						'value' => '#BDC3C7',
	                    'description' => __( 'Separator designs have 1-2 decoration colors, pick the color for the second decoration here', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Main Separator Fill Color', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'color3',
						'value' => '',
	                    'description' => __( 'NOTE: Applicable only in certain themes - use this if adding a background color via Row Settings doesn\'t work with the separator color.', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Decoration 1 Object Opacity', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'opacity1',
						'value' => '1',
	                    'description' => __( 'A decimal value of 0.0 to 1.0. 0 means fully transparent, 1 means fully opaque. Put 0 here to remove the decor from view', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Decoration 2 Object Opacity', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'opacity2',
						'value' => '1',
	                    'description' => __( 'A decimal value of 0.0 to 1.0. 0 means fully transparent, 1 means fully opaque. Put 0 here to remove the decor from view', GAMBIT_ROW_SEPARATORS ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Separator Offset', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'offset',
						'value' => '.5',
						'description' => __( 'If your separator shows gaps or thin lines, you can adjust this to prevent gaps from occuring. Positive offset will move the separator down, negative values will move it up', GAMBIT_ROW_SEPARATORS ),
					),
					// array(
					// 	'group' => __( 'Stroke & Outlines', GAMBIT_ROW_SEPARATORS ),
					// 	'type' => 'textfield',
					// 	'heading' => __( 'Main Outline / Stroke Thickness', GAMBIT_ROW_SEPARATORS ),
					// 	'param_name' => 'main_stroke_width',
					// 	'value' => '0',
	        //             'description' => __( 'Place a number greater than zero here to enable outlines', GAMBIT_ROW_SEPARATORS ),
					// ),
					// array(
					// 	'group' => __( 'Stroke & Outlines', GAMBIT_ROW_SEPARATORS ),
					// 	'type' => 'colorpicker',
					// 	'heading' => __( 'Main Outline / Stroke Color', GAMBIT_ROW_SEPARATORS ),
					// 	'param_name' => 'main_stroke_color',
					// 	'value' => '#222222',
					// ),
					// array(
					// 	'group' => __( 'Stroke & Outlines', GAMBIT_ROW_SEPARATORS ),
					// 	'type' => 'textfield',
					// 	'heading' => __( 'Decoration 1 Outline / Stroke Thickness', GAMBIT_ROW_SEPARATORS ),
					// 	'param_name' => 'stroke1_width',
					// 	'value' => '0',
	        //             'description' => __( 'Place a number greater than zero here to enable outlines', GAMBIT_ROW_SEPARATORS ),
					// ),
					// array(
					// 	'group' => __( 'Stroke & Outlines', GAMBIT_ROW_SEPARATORS ),
					// 	'type' => 'colorpicker',
					// 	'heading' => __( 'Decoration 1 Outline / Stroke Color', GAMBIT_ROW_SEPARATORS ),
					// 	'param_name' => 'stroke1_color',
					// 	'value' => '#222222',
					// ),
					// array(
					// 	'group' => __( 'Stroke & Outlines', GAMBIT_ROW_SEPARATORS ),
					// 	'type' => 'textfield',
					// 	'heading' => __( 'Decoration 2 Outline / Stroke Thickness', GAMBIT_ROW_SEPARATORS ),
					// 	'param_name' => 'stroke2_width',
					// 	'value' => '0',
	        //             'description' => __( 'Place a number greater than zero here to enable outlines', GAMBIT_ROW_SEPARATORS ),
					// ),
					// array(
					// 	'group' => __( 'Stroke & Outlines', GAMBIT_ROW_SEPARATORS ),
					// 	'type' => 'colorpicker',
					// 	'heading' => __( 'Decoration 2 Outline / Stroke Color', GAMBIT_ROW_SEPARATORS ),
					// 	'param_name' => 'stroke2_color',
					// 	'value' => '#222222',
					// ),
					array(
						'group' => __( 'Custom Separator', GAMBIT_ROW_SEPARATORS ),
						'type' => 'textarea_raw_html',
						'heading' => __( 'Custom SVG Separator', GAMBIT_ROW_SEPARATORS ),
						'param_name' => 'custom_svg',
						'value' => '',
	                    'description' => __( 'Selected your <strong>Row Separator</strong> as <em>Custom SVG</em> in the <strong>General tab</strong> to use your custom separator', GAMBIT_ROW_SEPARATORS ) .
							'<br/><br/>' .
							__( 'If you want to add your own SVG separator, add the contents of your <code>&lt;svg></code> here (omit the <code>&lt;svg></code> tags) Here are a few rules you will need to follow to fully import your separators:', GAMBIT_ROW_SEPARATORS ) .
							'<ol>' .
							'<li>' . __( 'Make sure you create your SVG with a document dimension <strong>1600x200</strong>,', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'<li>' . __( 'Make sure your SVG is oriented as a <strong>top separator</strong> (the bottom of your SVG should take up the whole width of the document),', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'<li>' . __( 'For shapes that line the edges of the document, make sure they go past or bleed through the edges. This is to prevent tiny gaps and outlines showing up on the sides of the separator,', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'<li>' . __( 'You can add your own colors by adding your own style attributes in your paths,', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'<li>' . __( 'Add a <code>class="gambit_sep_main"</code> to paths that you want to have the same background color as the row,', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'<li>' . __( 'Paths that have a <code>class="gambit_sep_decor1"</code> will get <strong>Decoration #1</strong> settings applied to them,', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'<li>' . __( 'Paths that have a <code>class="gambit_sep_decor2"</code> will get <strong>Decoration #2</strong> settings applied to them,', GAMBIT_ROW_SEPARATORS ) . '</li>' .
							'</ol>' .
							__( 'Here are a few examples:', GAMBIT_ROW_SEPARATORS ) .
							"<pre style='font-size: .8em; background: #eee; overflow: auto; padding: 5px;'>&lt;polygon class='gambit_sep_main' points='800,172 224,138 -4,20 -4,204 1604,204 1604,167 1236,10'/>
&lt;polygon class='gambit_sep_decor1' points='-4,0 -4,20 224,138 800,172 228,128'/>
&lt;polygon class='gambit_sep_decor2' points='1236,0 800,172 1236,10 1604,167 1604,141'/></pre>" .
							"<pre style='font-size: .8em; background: #eee; overflow: auto; padding: 5px;'>&lt;path class='gambit_sep_decor1' d='M1198,196C1002,196,607,2,398,2S-8,100-8,100v110h1206V196z'/>
&lt;path class='gambit_sep_decor2' d='M1198,196c-196,0-591-182-800-182S-8,100-8,100v110h1206V196z'/>
&lt;path class='gambit_sep_main' d='M-8,100c0,0,197-74,406-74s604,170,800,170s412-96,412-96v110H-8V100z'/></pre>",
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
				'location' => 'top',
				'separator' => 'slant-decor1',
				'flip' => '',
				'scale' => '1',
				'offset' => '.5',
				'color1' => '#95A5A6',
				'color2' => '#BDC3C7',
				'color3' => '',
				'opacity1' => '1',
				'opacity2' => '1',
				'main_stroke_width' => '0',
				'main_stroke_color' => '#222222',
				'stroke1_width' => '0',
				'stroke1_color' => '#222222',
				'stroke2_width' => '0',
				'stroke2_color' => '#222222',
				'custom_svg' => '',
	        );
			if ( empty( $atts ) ) {
				$atts = array();
			}
			$atts = array_merge( $defaults, $atts );

			// Increment identifier. This will become the separator ID.
			self::$id++;

			// Get the SVG.
			require_once( 'row-separators/lib/svgs.php' );
			$svgs = gambit_row_separators_get_svgs();

			if ( empty( $svgs[ $atts['separator'] ] ) ) {
				return '';
			}

			wp_enqueue_style( __CLASS__, plugins_url( 'row-separators/css/style.css', __FILE__ ), array(), VERSION_GAMBIT_ROW_SEPARATORS );
			wp_enqueue_script( __CLASS__, plugins_url( 'row-separators/js/min/script-min.js', __FILE__ ), array(), VERSION_GAMBIT_ROW_SEPARATORS, true );

			$ret = '';
			$style_ret = '';

			$svg_classes = 'gambit_separator';
			$svg_classes .= ' gambit_sep_' . $atts['location'];
			$svg_classes .= ' gambit-sep-type-' . $atts['separator'];
			$svg_classes .= empty( $atts['flip'] ) ? '' : ' gambit_sep_flip';

			$svg = $svgs[ $atts['separator'] ]['svg'];

			if ( ! empty( $atts['custom_svg'] ) ) {
				$svg = rawurldecode( base64_decode( strip_tags( $atts['custom_svg'] ) ) );
				$svg = preg_replace( "/(class=('|\")gambit_sep_main('|\"))/", '$1 style="{main}"', $svg );
				$svg = preg_replace( "/(class=('|\")gambit_sep_decor1('|\"))/", '$1 style="{decor1}"', $svg );
				$svg = preg_replace( "/(class=('|\")gambit_sep_decor2('|\"))/", '$1 style="{decor2}"', $svg );
			}

			$height = (int) $svgs[ $atts['separator'] ]['height'] * (float) $atts['scale'];

			// Add main stroke.
			$style = '';
			// if ( ! empty( $atts['main_stroke_width'] ) && '0' != $atts['main_stroke_width'] ) {
			// 	$style .= 'stroke-width: ' . $atts['main_stroke_width'] . ';';
			// 	$style .= 'stroke:' . $atts['main_stroke_color'] . ';';
			// }
			$svg = preg_replace( '/\{main\}/', $style, $svg );

			// Add decor color 1.
			$style = '';
			if ( ! empty( $atts['opacity1'] ) && ! empty( $atts['color1'] ) && '0' != $atts['opacity1'] && 'transparent' != $atts['color1'] ) {
				$style = 'opacity: ' . $atts['opacity1'] . ';';
				$style .= 'fill: ' . $atts['color1'] . ';';

				// if ( ! empty( $atts['stroke1_width'] ) && '0' != $atts['stroke1_width'] ) {
				// 	$style .= 'stroke-width: ' . $atts['stroke1_width'] . ';';
				// 	$style .= 'stroke:' . $atts['stroke1_color'] . ';';
				// }

				$svg = preg_replace( '/\{decor1\}/', $style, $svg );
			} else {
				$svg = preg_replace( '/[\s\n]?<[^>]+\{decor1\}[^>]+>[\s\n]?/', '', $svg );

			}

			// Add decor color 2.
			$style = '';
			if ( ! empty( $atts['opacity2'] ) && ! empty( $atts['color2'] ) && '0' != $atts['opacity2'] && 'transparent' != $atts['color2'] ) {
				$style = 'opacity: ' . $atts['opacity2'] . ';';
				$style .= 'fill: ' . $atts['color2'] . ';';

				// if ( ! empty( $atts['stroke2_width'] ) && '0' != $atts['stroke2_width'] ) {
				// 	$style .= 'stroke-width: ' . $atts['stroke2_width'] . ';';
				// 	$style .= 'stroke:' . $atts['stroke2_color'] . ';';
				// }

				$svg = preg_replace( '/\{decor2\}/', $style, $svg );
			} else {
				$svg = preg_replace( '/[\s\n]?<[^>]+\{decor2\}[^>]+>[\s\n]?/', '', $svg );
			}

			// We adjust the viewBox inward by a tiny bit to make sure we don't have tiny gaps on the edges.
			$view_box_height = (int) $svgs[ $atts['separator'] ]['height'];
			// $view_box_height = $height;
			$view_box_width = (int) $svgs[ $atts['separator'] ]['width'];
			$dataAttr = '';
			if ( ! empty( $atts['color3'] ) ) {
				$dataAttr .= 'data-bg-color="' . $atts['color3'] . '"';
			}

			$ret .= '<svg id="' . 'gambit-row-separator-' . esc_attr( self::$id ) . '" preserveAspectRatio="xMidYMax meet" class="' . $svg_classes . '" viewBox="0 0 ' . $view_box_width . ' ' . $view_box_height . '" ' . $dataAttr . ' style="display: none; width: 100%; height: calc(' . $height . ' / ' . $svgs[ $atts['separator'] ]['width'] . ' * 100vw)" data-height="' . $height . '">' .
				$svg .
				'</svg>';

			$offset = '' == $atts['offset'] ? '.5' : $atts['offset'];

			// If a different offset is specified, print out a custom CSS rule.
			$position = 'bottom' == $atts['location'] ? -1 : 1;
			$translate_scale = 'bottom' == $atts['location'] ? 0 : (-100 * $atts['scale']) * $position;
			$tiny_offset = 'top' === $atts['location'] ? 0.5 : -0.5;
			$css_rule = 'transform: translateY(' . $translate_scale . '%) ';
			$css_rule .= 'translateY(' . ( $offset * $position + $tiny_offset ) . 'px)';
			$flip_x = '1' == $atts['flip'] ? -1 : 1;
			$flip_y = $atts['scale'] * $position;
			$css_rule .= ' scale(' . $flip_x . ', ' .$flip_y . ');';
			$css_rule .= ' transform-origin: ' . $atts['location'];
			$style_ret .= '#gambit-row-separator-' . esc_attr( self::$id ) . '{ ' . $css_rule . ' }';

			wp_add_inline_style( __CLASS__, $style_ret );

			return $ret;
		}
	}
	new GambitRowSeparator();
}
