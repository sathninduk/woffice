<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_FONTS' ) || define( 'VERSION_GAMBIT_VC_FONTS', '1.3.1' );

// The slug used fot translation & other identifiers.
defined( 'GAMBIT_VC_FONTS' ) || define( 'GAMBIT_VC_FONTS', 'fonts-vc' );

// This is the main plugin functionality.
require_once( 'class-fonts-vc.php' );

// Initializes plugin class.
if ( ! class_exists( 'FontsForVCPlugin' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void
	 * @since 1.0
	 */
	class FontsForVCPlugin {

		 /**
		  * Hook into WordPress.
		  *
		  * @return	void
		  * @since	1.0
		  */
		function __construct() {
		}
	}

	new FontsForVCPlugin();
}
