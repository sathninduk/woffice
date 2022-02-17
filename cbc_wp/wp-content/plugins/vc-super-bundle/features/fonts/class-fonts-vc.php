<?php
/**
 * The icon's functionalities are located here.
 * @package Fonts for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'FontsForVC' ) ) {

	/**
	 * The class that does the functions.
	 */
	class FontsForVC {

		/**
		 * Holds all the styles which we're going to print out in the footer.
		 *
		 * @var array
		 */
		public $css = array();

		/**
		 * Holds all the font names to load in the frontend.
		 *
		 * @var array
		 */
		public $fonts = array();

		/**
		 * Holds all the Google Fonts loaded from function-google-fonts.php
		 *
		 * @var array
		 */
		public $all_fonts = array();

		/**
		 * Hook into WordPress.
		 *
		 * @return void.
		 * @since 1.0
		 */
		function __construct() {

			// Add the font parameters to shortcodes.
			add_action( 'init', array( $this, 'add_font_param' ), 999 );

			// Add the font class to the shortcode outputs.
			add_filter( 'vc_shortcodes_css_class', array( $this, 'add_font_class' ), 999, 3 );

			// Print out the font styles in the footer.
			add_action( 'wp_footer', array( $this, 'add_font_scripts' ) );

			// Add our own custom VC param for picking fonts.
			add_action( 'after_setup_theme', array( $this, 'create_font_param' ) );

			// Add the necessary admin scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Adds the font parameter to VC elements.
		 */
		public function add_font_param() {
			if ( ! function_exists( 'vc_add_param' ) ) {
				return;
			}

			$attributes = array(
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family',
					'heading' => __( 'Normal Font', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size',
					'heading' => __( 'Normal Font Size', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family_h1',
					'heading' => __( 'Heading 1 Font (H1)', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size_h1',
					'heading' => __( 'Heading 1 Size (H1)', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family_h2',
					'heading' => __( 'Heading 2 Font (H2)', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size_h2',
					'heading' => __( 'Heading 2 Size (H2)', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family_h3',
					'heading' => __( 'Heading 3 Font (H3)', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size_h3',
					'heading' => __( 'Heading 3 Size (H3)', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family_h4',
					'heading' => __( 'Heading 4 Font (H4)', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size_h4',
					'heading' => __( 'Heading 4 Size (H4)', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family_h5',
					'heading' => __( 'Heading 5 Font (H5)', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size_h5',
					'heading' => __( 'Heading 5 Size (H5)', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'fnt_picker',
					'param_name' => 'fnt_family_h6',
					'heading' => __( 'Heading 6 Font (H6)', GAMBIT_VC_FONTS ),
					'value' => '',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
				array(
					'type' => 'textfield',
					'param_name' => 'fnt_size_h6',
					'heading' => __( 'Heading 6 Size (H6)', GAMBIT_VC_FONTS ),
					'value' => '',
					'description' => 'If unit is not supplied, px is used. Use blank to use theme default.',
					'group' => __( 'Fonts', GAMBIT_VC_FONTS ),
				),
			);

			// These are all the shortcodes we will add the fonts to.
			vc_add_params( 'vc_row', $attributes );
			vc_add_params( 'vc_row_inner', $attributes );
			vc_add_params( 'vc_column', $attributes );
			vc_add_params( 'vc_column_text', $attributes );
			vc_add_params( 'vc_column_inner', $attributes );
		}

		/**
		 * Adds the special font class to the affected VC elements.
		 *
		 * @param string $classes The current classes of the element.
		 * @param object $sc The shortcode object.
		 * @param object $atts The attributes of the shortcode.
		 *
		 * @return string The modified classes
		 */
		public function add_font_class( $classes, $sc, $atts = array() ) {

			$font_data = vcfnts_get_all_font_attrs( $atts );

			// Create the font data.
			foreach ( $font_data as $tag => $font_single_data ) {
				$font_class_name = vcfnts_get_font_classname( $tag, $font_single_data );

				// Append the new class names to our container.
				$classes .= " $font_class_name ";

				$selector = vcfnts_get_style_selector( $tag, $font_single_data );
				$styles = vcfnts_get_font_style( $font_single_data );

				// Save the new styles.
				$this->css[ $font_class_name ] = "$selector { $styles }";
			}

			// Save our font names for enqueuing later.
			$fonts = vcfnts_get_all_font_names( $font_data );
			foreach ( $fonts as $font ) {
				if ( ! array_search( $font, $this->fonts ) ) {
					$this->fonts[] = $font;
				}
			}

			return $classes;
		}

		/**
		 * Prints out the necessary styles & scripts to load our fonts.
		 */
		public function add_font_scripts() {
			if ( count( $this->css ) ) {

				/**
				 * Styles.
				 */

				echo '<style>';

				// While webfont isn't finished loading, hide the affected elements first.
				// This is to prevent FOUT.
				echo 'html:not(.wf-active) [class*="fnt_"] { visibility: hidden; }';

				// Print out our font styles.
				echo "\n";
				echo implode( "\n", array_values( $this->css ) );

				echo '</style>';

				/**
				 * Scripts.
				 */

				$fonts = array();
				foreach ( $this->fonts as $font ) {

					// Add subsets & variations.
					$fonts[] = $font;
				}

				// Print out the loader script.
				echo '<script>
				( function( d ) {
					WebFontConfig = {
						google: {
							families: [\'' . implode( '\', \'', $fonts ) . '\']
						}
					};

					if ( "undefined" !== typeof WebFont ) {
						WebFont.load( WebFontConfig );
					}

					var wf = d.createElement( "script" ), s = d.scripts[0];
					wf.src = "https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js?lol";
					wf.async = true;
					s.parentNode.insertBefore( wf, s );
				} )( document );
				</script>';
			}
		}

		/**
		 * Register our font parameter in VC.
		 */
		public function create_font_param() {
			if ( ! function_exists( 'vc_add_shortcode_param' ) ) {
				return;
			}

			vc_add_shortcode_param( 'fnt_picker', array( $this, 'create_font_picker_field' ), plugins_url( 'fonts-vc/js/admin.js', __FILE__ )  );
		}

		/**
		 * Enqueue all the needed scripts in the backend.
		 */
		public function admin_enqueue_scripts() {
			if ( ! function_exists( 'vc_editor_post_types' ) ) {
				return;
			}

			global $current_screen;
			if ( false !== array_search( $current_screen->post_type, vc_editor_post_types() ) ) {
				wp_enqueue_style( 'fnt_select2', plugins_url( 'fonts-vc/css/select2.min.css', __FILE__ ), array(), VERSION_GAMBIT_VC_FONTS );
				wp_enqueue_script( 'fnt_select2', plugins_url( 'fonts-vc/js/select2.js', __FILE__ ), array(), VERSION_GAMBIT_VC_FONTS );
				wp_enqueue_script( 'webfontloader', '//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array(), VERSION_GAMBIT_VC_FONTS );
			}
		}


		/**
		 * Creates our own font picker parameter for VC.
		 *
		 * @since 1.0
		 *
		 * @param array $settings The settings array from existing VC fields.
		 * @param string $value As specified.
		 */
		public function create_font_picker_field( $settings, $value ) {

			if ( empty( $this->all_fonts ) ) {
				require_once( 'function-google-fonts.php' );
				$this->all_fonts = fvc_get_all_google_fonts();
			}

			$select = '<option value="" ' . selected( $value, '', false ) . '>— ' . __( 'Default theme font', GAMBIT_VC_FONTS ) . ' —</option>';
			foreach ( array_keys( $this->all_fonts ) as $font ) {
				$select .= '<option value="' . esc_attr( $font ) . '" ' . selected( $value, $font, false ) . ' style="font-family: \"' . $font . '\"">' . esc_html( $font ) . '</option>';
			}

			return '<div>'
				  . '<style>.fnt_picker_field + .select2-container { width: 100% !important; } .select2-container {
					z-index: 999999;
				} .select2-results__option {     line-height: 1em; font-size: 22px; }</style>'
				  . '<select name="' . $settings['param_name'] . '" '
				  . 'data-param-name="' . $settings['param_name'] . '" '
				  . 'data-placeholder="— ' . __( 'Default theme font', GAMBIT_VC_FONTS ) . ' —"'
				  . 'class="wpb_vc_param_value wpb-textinput '
				  . $settings['param_name'] . ' ' . $settings['type'] . '_field" '
				  . '>' . $select . '</select>'
			  .'</div>';
		}
	}

	new FontsForVC();
} // End if().

if ( ! function_exists( 'vcfnts_get_all_font_attrs' ) ) {

	/**
	 * Converts all font related attributes into a "Font Array". This array will
	 * be used for the succeeding functions.
	 *
	 * @since 1.3
	 */
	function vcfnts_get_all_font_attrs( $atts = array() ) {
		$tags = array( '', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
		$headings = array();

		foreach ( $tags as $tag ) {

			$attr_font = 'fnt_family' . ( ! empty( $tag ) ? '_' : '' ) . $tag;
			$attr_size = 'fnt_size' . ( ! empty( $tag ) ? '_' : '' ) . $tag;

			if ( empty( $atts[ $attr_font ] ) && empty( $atts[ $attr_size ] ) ) {
				continue;
			}

			$headings[ $tag ] = array();

			if ( ! empty( $atts[ $attr_font ] ) ) {
				$headings[ $tag ]['font-family'] = $atts[ $attr_font ];
			}

			if ( ! empty( $atts[ $attr_size ] ) ) {
				$headings[ $tag ]['font-size'] = $atts[ $attr_size ];
			}
		}

		return $headings;
	}
}

if ( ! function_exists( 'vcfnts_get_font_classname' ) ) {

	/**
	 * Forms a classname for this specific font styles.
	 *
	 * @since 1.3
	 */
	function vcfnts_get_font_classname( $tag = '', $font_single_data = array() ) {
		$find = array( '/[:,]/', '/[^_a-zA-Z0-9]/' );
		$replace = array( '_', '' );
		$class_name = preg_replace( $find, $replace, json_encode( $font_single_data ) );
		return 'vcfnt_' . strtolower( ( $tag ? $tag . '_' : '' ) . $class_name );
	}
}

if ( ! function_exists( 'vcfnts_get_font_style' ) ) {

	/**
	 * Forms style rules from the given array.
	 *
	 * @since 1.3
	 */
	function vcfnts_get_font_style( $font_single_data = array() ) {
		$styles = '';
		foreach ( $font_single_data as $style => $value ) {
			if ( 'font-size' === $style ) {
				if ( is_numeric( $value ) ) {
					$value .= 'px';
				}
			}

			$styles .= $style . ':' . $value . ';';
		}
		return $styles;
	}
}

if ( ! function_exists( 'vcfnts_get_all_font_names' ) ) {

	/**
	 * Gets all the font names of a set of fonts.
	 *
	 * @since 1.3
	 */
	function vcfnts_get_all_font_names( $font_data = array() ) {
		$fonts = array();
		foreach ( $font_data as $tag => $font_styles ) {
			foreach ( $font_styles as $style => $value ) {
				if ( 'font-family' === $style ) {
					$fonts[] = $value;
				}
			}
		}
		return $fonts;
	}
}

if ( ! function_exists( 'vcfnts_get_style_selector' ) ) {

	/**
	 * Creates a selector for the given tag for the fonts associated with it.
	 *
	 * @since 1.3
	 */
	function vcfnts_get_style_selector( $tag = '', $font_single_data = array() ) {
		$class_name = vcfnts_get_font_classname( $tag, $font_single_data );

		// Generate the CSS that we need to implement the fonts.
		if ( ! empty( $tag ) ) {
			return sprintf( '.%s %s, %s %s *', $class_name, $tag, $class_name, $tag );
		} else {
			return sprintf( '.%s, .%s *', $class_name, $class_name );
		}
	}
}
