<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_PARALLAX_BG' ) || define( 'VERSION_GAMBIT_VC_PARALLAX_BG', '4.8' );

// The slug used for translations & other identifiers.
defined( 'GAMBIT_VC_PARALLAX_BG' ) || define( 'GAMBIT_VC_PARALLAX_BG', 'parallax' );

// Disable rating for the Smooth Mousewheel plugin since we're including the plugin with parallax.
defined( 'GAMBIT_DISABLE_SMOOTH_SCROLLING_RATING' ) || define( 'GAMBIT_DISABLE_SMOOTH_SCROLLING_RATING', '1' );

// Loads all the modules related to the plugin.
require_once( 'class-fullwidth-row.php' );
require_once( 'class-fullheight-row.php' );
require_once( 'class-parallax-row.php' );
require_once( 'class-video-row.php' );
require_once( 'class-hover-row.php' );
require_once( 'class-background-row.php' );
require_once( 'class-color-cycle-bg.php' );
require_once( 'class-background-gradient.php' );

// Initializes plugin class.
if ( ! class_exists( 'GambitVCParallaxBackgrounds' ) ) {

	/**
	 * Parallax Background Class.
	 *
	 * @since	1.0
	 */
	class GambitVCParallaxBackgrounds {

		/**
		 * Constructor, checks for Visual Composer and defines hooks.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Our translations.
			add_action( 'init', array( $this, 'load_text_domain' ), 1 );

			// Gambit links.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );

			// Add plugin specific filters and actions here.
			add_action( 'wp_head', array( $this, 'ie9_detector' ) );
		}


		/**
		 * Enables legacy Internet Explorer 9 compatibility.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function ie9_detector() {
			echo '<!--[if IE 9]> <script>var _gambitParallaxIE9 = true;</script> <![endif]-->';
		}


		/**
		 * Loads the translations.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( GAMBIT_VC_PARALLAX_BG, false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Adds plugin links.
		 *
		 * @access public
		 * @param array  $plugin_meta - The current array of links.
		 * @param string $plugin_file - The plugin file.
		 * @return array - The current array of links together with our additions.
		 * @since	2.6
		 **/
		public function plugin_links( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				$plugin_data = get_plugin_data( __FILE__ );

				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://support.gambit.ph?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get Customer Support', GAMBIT_VC_PARALLAX_BG )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://codecanyon.net/user/GambitTech/portfolio?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', GAMBIT_VC_PARALLAX_BG )
				);
			}
			return $plugin_meta;
		}
	}


	new GambitVCParallaxBackgrounds();
}
