<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}
// Identifies the plugin itself. If already existing, it will not redefine itself.
defined( 'VERSION_GAMBIT_HERO_BOX' ) || define( 'VERSION_GAMBIT_HERO_BOX', '1.3' );

// Initializes the plugin translations.
defined( 'GAMBIT_HERO_BOX' ) || define( 'GAMBIT_HERO_BOX', 'hero-boxes' );

// Main class file for the plugin.
require_once( 'class-hero-boxes.php' );

if ( ! class_exists( 'GambitHeroBox' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return	void
	 * @since	1.0
	 */
	class GambitHeroBox {

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
			load_plugin_textdomain( GAMBIT_HERO_BOX, false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Adds plugin links.
		 *
		 * @access	public
		 * @param	array  $plugin_meta  - The current array of links.
		 * @param	string $plugin_file  - The plugin file.
		 * @return	array - The current array of links together with our additions.
		 * @since	1.0
		 **/
		public function plugin_links( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				$plugin_data = get_plugin_data( __FILE__ );

				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://support.gambit.ph?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get Customer Support', GAMBIT_HERO_BOX )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'https://gambit.ph/plugins?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', GAMBIT_HERO_BOX )
				);
			}
			return $plugin_meta;
		}
	}
	new GambitHeroBox();
} // End if().
