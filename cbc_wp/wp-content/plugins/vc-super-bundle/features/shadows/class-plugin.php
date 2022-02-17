<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_COLUMN_SHADOWS' ) || define( 'VERSION_GAMBIT_COLUMN_SHADOWS', '1.0' );

// The slug used for translation & other identifiers.
defined( 'GAMBIT_COLUMN_SHADOWS' ) || define( 'GAMBIT_COLUMN_SHADOWS', 'shadows-vc' );

// This is the main plugin functionality.
require_once( 'class-shadows.php' );

// Initializes plugin class.
if ( ! class_exists( 'ColumnShadowsForVCPlugin' ) ) {
	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void.
	 * @since 1.0
	 */
	 class ColumnShadowsForVCPlugin {

		 /**
		  * Hook into WordPress.
		  *
		  * @return void
		  * @since 1.0
		  */
		  function __construct() {

		  }
	 }
	 new ColumnShadowsForVCPlugin();
}
