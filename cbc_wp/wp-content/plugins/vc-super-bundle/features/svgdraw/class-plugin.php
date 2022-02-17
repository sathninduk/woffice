<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
 }

 // Identifies the current plugin version.
 defined( 'VERSION_GAMBIT_VC_SVG_DRAW_ANIMATION' ) || define( 'VERSION_GAMBIT_VC_SVG_DRAW_ANIMATION' , '1.0' );

 // The slug used for translations & other identifiers.
 defined( 'GAMBIT_VC_SVG_DRAW_ANIMATION' ) || define( 'GAMBIT_VC_SVG_DRAW_ANIMATION', 'svg-draw-animation' );

 // This is the main plugin functionality.
 require_once( 'class-svg-draw-animation.php' );

 // Initializes plugin class.
 if ( ! class_exists( 'SVG_Draw_Animation' ) ) {

	 /**
	  * Initializes core plugin that is readable by WordPress.
	  *
	  * @return  void
	  * @since   1.0
	  */
	  class SVG_Draw_Animation {

		  /**
		   * Hook into WordPress.
		   *
		   * @return  void
		   * @since   1.0
		   */
		   function __construct() {

			   // Our translations.
			   add_action( 'init', array( $this, 'load_text_domain' ), 1 );
		   }


		   /**
		    * Loads the translations.
			*
			* @return  void
			* @since   1.0
		    */
			public function load_text_domain() {
				load_plugin_textdomain( 'svg-draw-animation', false, basename( dirname( __FILE__ ) ) . '/languages/' );
			}
	  }

	  new SVG_Draw_Animation();
 }
