<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
 }

 // Identifies the current plugin version.
 defined( 'VERSION_GAMBIT_VC_VIDEO_POPUP' ) || define( 'VERSION_GAMBIT_VC_VIDEO_POPUP', '1.0' );

 // The slug used for translations & other identifiers.
 defined( 'GAMBIT_VC_VIDEO_POPUP' ) || define( 'GAMBIT_VC_VIDEO_POPUP', 'video-popup' );

 // This is the main plugin functionality.
 require_once( 'class-video-popup.php' );

 // Initializes plugin class.
 if ( ! class_exists( 'GMB_Video_Popup' ) ) {

 	/**
 	 * Initializes core plugin that is readable by WordPress.
 	 *
 	 * @return	void
 	 * @since	1.0
 	 */
 	class GMB_Video_Popup {

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
 			load_plugin_textdomain( 'video-popup', false, basename( dirname( __FILE__ ) ) . '/languages/' );
 		}

 	}

 	new GMB_Video_Popup();
 }
