<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_UNDO_REDO' ) || define( 'VERSION_GAMBIT_VC_UNDO_REDO', '1.1' );

// The slug used fot translation & other identifiers.
defined( 'GAMBIT_VC_UNDO_REDO' ) || define( 'GAMBIT_VC_UNDO_REDO', 'undo-redo' );

// This is the main plugin functionality.
require_once( 'class-undo-redo-vc.php' );

// Initializes plugin class.
if ( ! class_exists( 'UndoRedoVCPlugin' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void
	 * @since 1.0
	 */
	class UndoRedoVCPlugin {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {
		}
	}

	new UndoRedoVCPlugin();
}
