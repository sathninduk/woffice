<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_TEXT_GRADIENT' ) || define( 'VERSION_GAMBIT_TEXT_GRADIENT', '1.0' );

// The slug used for translation & other identifiers.
defined( 'GAMBIT_TEXT_GRADIENT' ) || define( 'GAMBIT_TEXT_GRADIENT', 'text-gradient-vc' );

// This is the main plugin functionality.
require_once( 'class-text-gradient.php' );

// Initializes plugin class.
if ( ! class_exists( 'TextGradientForVCPlugin' ) ) {
	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void.
	 * @since 1.0
	 */
	 class TextGradientForVCPlugin {

		 /**
		  * Hook into WordPress.
		  *
		  * @return void
		  * @since 1.0
		  */
		  function __construct() {

		  }
	 }
	 new TextGradientForVCPlugin();
}
