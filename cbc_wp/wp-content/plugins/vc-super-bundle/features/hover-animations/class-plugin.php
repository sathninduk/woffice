<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_HOVER_ANIM' ) || define( 'VERSION_GAMBIT_VC_HOVER_ANIM', '1.1.1' );

// The slug used fot translation & other identifiers.
defined( 'GAMBIT_VC_HOVER_ANIM' ) || define( 'GAMBIT_VC_HOVER_ANIM', 'hover-anim' );

// This is the main plugin functionality.
require_once( 'class-hover-animations.php' );

// Initializes plugin class.
if ( ! class_exists( 'HoverAnimForVCPlugin' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void
	 * @since 1.0
	 */
	class HoverAnimForVCPlugin {

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
			load_plugin_textdomain( 'hover-anim', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}
	}

	new HoverAnimForVCPlugin();
}
