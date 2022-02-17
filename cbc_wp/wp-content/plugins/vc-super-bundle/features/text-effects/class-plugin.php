<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_TEXT_EFFECTS' ) || define( 'VERSION_GAMBIT_VC_TEXT_EFFECTS', '1.2' );

// The slug used for translations & other identifiers.
defined( 'GAMBIT_VC_TEXT_EFFECTS' ) || define( 'GAMBIT_VC_TEXT_EFFECTS', 'text-effects' );

// This is the main plugin functionality.
require_once( 'class-text-effects.php' );

// Initializes plugin class.
if ( ! class_exists( 'GambitTextEffects' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return	void
	 * @since	1.0
	 */
	class GambitTextEffects {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Our translations.
			add_action( 'init', array( $this, 'load_text_domain' ), 1 );
		}


		/**
		 * Loads the translations.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'text-effects', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

	}

	new GambitTextEffects();
}
