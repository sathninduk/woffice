<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_SVG_ICONS' ) || define( 'VERSION_GAMBIT_VC_SVG_ICONS', '1.3' );

// The slug used fot translation & other identifiers.
defined( 'GAMBIT_VC_SVG_ICONS' ) || define( 'GAMBIT_VC_SVG_ICONS', 'svg-icons' );

// This is the main plugin functionality.
require_once( 'class-svg-icons.php' );

// Initializes plugin class.
if ( ! class_exists( 'SVGIconsPlugin' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void
	 * @since 1.0
	 */
	class SVGIconsPlugin {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Our translations.
			add_action( 'init', array( $this, 'load_text_domain' ) );
		}

		/**
		 * Loads the translations.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'svg-icons', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}
	}

	new SVGIconsPlugin();
}
