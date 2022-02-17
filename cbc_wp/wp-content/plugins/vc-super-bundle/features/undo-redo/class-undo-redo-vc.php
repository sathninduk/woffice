<?php
/**
 * The undo and redo functionalities are located here.
 * @package Undo and Redo for Visual Composer.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
if ( ! class_exists( 'UndoRedoVC' ) ) {

	/**
	 * The class that does the functions.
	 */
	class UndoRedoVC {

		/**
		 * Hook into WordPress.
		 *
		 * @return void.
		 * @since 1.0
		 */
		function __construct() {

			// Add the necessary admin scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Enqueue all the needed scripts in the backend.
		 */
		public function admin_enqueue_scripts() {
			if ( ! function_exists( 'vc_add_param' ) ) {
				return;
			}

			wp_enqueue_script( 'undo-redo', plugins_url( 'undo-redo-vc/js/min/admin-min.js', __FILE__ ), array(), VERSION_GAMBIT_VC_UNDO_REDO );
		}
	}

	new UndoRedoVC();
}
