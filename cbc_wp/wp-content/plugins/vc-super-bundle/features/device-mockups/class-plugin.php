<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_MOCKUP' ) || define( 'VERSION_GAMBIT_VC_MOCKUP', '1.3.2' );

// The slug used for translations & other identifiers.
defined( 'GAMBIT_VC_MOCKUP' ) || define( 'GAMBIT_VC_MOCKUP', 'mockups' );

// This is the main plugin functionality.
require_once( 'class-mockup.php' );

// Initializes plugin class.
if ( ! class_exists( 'GambitMockup' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return	void
	 * @since	1.0
	 */
	class GambitMockup {

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
		 * Loads the translations.
		 *
		 * @return	void
		 * @since	1.0
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'mockups', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Adds plugin links.
		 *
		 * @access	public
		 * @param	array  $plugin_meta - The current array of links.
		 * @param	string $plugin_file - The plugin file.
		 * @return	array - The current array of links together with our additions.
		 * @since	1.0
		 **/
		public function plugin_links( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				$plugin_data = get_plugin_data( __FILE__ );

				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'http://support.gambit.ph?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get Customer Support', 'mockups' )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'https://gambit.ph/plugins?utm_source=' . urlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', 'mockups' )
				);
			}
			return $plugin_meta;
		}
	}
	new GambitMockup();
} // End if().
