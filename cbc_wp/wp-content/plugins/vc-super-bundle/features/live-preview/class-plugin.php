<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_PREVIEW' ) || define( 'VERSION_GAMBIT_VC_PREVIEW', 1.3 );

// The slug used fot translation & other identifiers.
defined( 'GAMBIT_VC_PREVIEW' ) || define( 'GAMBIT_VC_PREVIEW', 'vc-preview' );

// This is the main plugin functionality.
require_once( 'class-vc-preview.php' );

// Initializes plugin class.
if ( ! class_exists( 'GVcPreviewPlugin' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void
	 * @since 1.0
	 */
	class GVcPreviewPlugin {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {
		}
	}

	new GVcPreviewPlugin();
}
