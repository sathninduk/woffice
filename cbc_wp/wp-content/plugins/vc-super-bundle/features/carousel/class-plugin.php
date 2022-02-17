<?php

defined( 'VERSION_GAMBIT_CAROUSEL_ANYTHING' ) || define( 'VERSION_GAMBIT_CAROUSEL_ANYTHING', '1.12' );

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
defined( 'GAMBIT_CAROUSEL_ANYTHING' ) || define( 'GAMBIT_CAROUSEL_ANYTHING', 'carousel-anything' );

defined( 'GAMBIT_CAROUSEL_ANYTHING_FILE' ) || define( 'GAMBIT_CAROUSEL_ANYTHING_FILE', __FILE__ );

// Load modules.
require_once( 'class-carousel-anything.php' );
require_once( 'class-carousel-posts.php' );

if ( ! class_exists( 'GambitCarouselShortcode' ) ) {

	/**
	 * Handles all pointers, autoupdates and others.
	 */
	class GambitCarouselShortcode {

		/**
		 * Hook into WordPress
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Our translations.
			add_action( 'init', array( $this, 'load_text_domain' ), 1 );

			// Gambit links.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );

		}


		/**
		 * Loads the translations.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( GAMBIT_CAROUSEL_ANYTHING, false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Adds plugin links.
		 *
		 * @access	public
		 * @param	array  $plugin_meta The current array of links.
		 * @param	string $plugin_file The plugin file.
		 * @return	array The current array of links together with our additions.
		 * @since	1.0
		 **/
		public function plugin_links( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				$plugin_data = get_plugin_data( __FILE__ );

				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://support.gambit.ph?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get Customer Support', GAMBIT_CAROUSEL_ANYTHING )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'https://gambit.ph/plugins?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', GAMBIT_CAROUSEL_ANYTHING )
				);
			}
			return $plugin_meta;
		}
	}

	new GambitCarouselShortcode();
}
