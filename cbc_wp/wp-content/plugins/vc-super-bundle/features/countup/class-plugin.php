<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
 }

 // Identifies the current plugin version.
 defined( 'VERSION_GAMBIT_VC_NUMBER_COUNT_UP' ) || define( 'VERSION_GAMBIT_VC_NUMBER_COUNT_UP', '1.0' );

 // The slug used for translations & other identifiers.
 defined( 'GAMBIT_VC_NUMBER_COUNT_UP' ) || define( 'GAMBIT_VC_NUMBER_COUNT_UP', 'number-count-up' );

 // This is the main plugin functionality.
 require_once( 'class-number-count-up.php' );

 // Initializes plugin class.
 if ( ! class_exists( 'Number_Count_Up' ) ) {

 	/**
 	 * Initializes core plugin that is readable by WordPress.
 	 *
 	 * @return	void
 	 * @since	1.0
 	 */
 	class Number_Count_Up {

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
 			load_plugin_textdomain( 'number-count-up', false, basename( dirname( __FILE__ ) ) . '/languages/' );
 		}

 	}

 	new Number_Count_Up();
 }
