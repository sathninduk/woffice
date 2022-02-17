<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_LOUPE' ) || define( 'VERSION_GAMBIT_VC_LOUPE', '1.4' );

// The slug used for translations & other identifiers.
defined( 'GAMBIT_VC_LOUPE' ) || define( 'GAMBIT_VC_LOUPE', 'loupe' );

// This is the main plugin functionality.
require_once( 'class-loupe.php' );

if ( ! class_exists( 'GambitLoupe' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return	void
	 * @since	1.0
	 */
	class GambitLoupe {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Initialize translations.
			add_action( 'init', array( $this, 'load_text_domain' ), 1 );

			// Gambit links.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );

		}


		/**
		 * Loads the translations
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'loupe', false, basename( dirname( __FILE__ ) ) . '/languages/' );
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
					__( 'Get Customer Support', 'loupe' )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'https://gambit.ph/plugins?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', 'loupe' )
				);
			}
			return $plugin_meta;
		}
	}

	new GambitLoupe();
} // End if().
