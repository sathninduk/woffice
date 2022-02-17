<?php

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// States the plugin version.
defined( 'VERSION_GAMBIT_SMOOTH_SCROLLING_PLUGIN' ) || define( 'VERSION_GAMBIT_SMOOTH_SCROLLING_PLUGIN', '3.3' );

// Connects the languages file to the plugin.
defined( 'GAMBIT_SMOOTH_SCROLLING_PLUGIN' ) || define( 'GAMBIT_SMOOTH_SCROLLING_PLUGIN', 'smooth-scrolling' );

// Main plugin functionality.
require_once( 'class-smooth-scroll.php' );

// Initializes the plugin class.
if ( ! class_exists( 'GambitSmoothScrollPlugin' ) ) {

	/**
	 * Plugin class.
	 */
	class GambitSmoothScrollPlugin {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Add settings link.
			add_filter( 'plugin_action_links', array( $this, 'plugin_settings_link' ), 10, 2 );

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
			load_plugin_textdomain( 'smooth-scrolling', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Adds plugin settings link.
		 *
		 * @access	public
		 * @param	array  $links - The current set of links.
		 * @param string $plugin_file - The plugin filename.
		 * @return array $links - The adjusted set of links.
		 * @since	1.0
		 **/
		public function plugin_settings_link( $links, $plugin_file ) {

			// Get this plugin's base folder.
			static $plugin;
			if ( ! isset( $plugin ) ) {
				$plugin = plugin_basename( __FILE__ );
				$plugin = trailingslashit( dirname( $plugin ) );
			}

			// If we are in the links of our plugin, add the settings link.
			if ( stripos( $plugin_file, $plugin ) !== false ) {

				$settings_url = admin_url( 'options-general.php' );

				array_unshift( $links, '<a href="' . $settings_url . '">' . __( 'Settings', 'smooth-scrolling' ) . '</a>' );

			}

			return $links;
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
					'http://support.gambit.ph?utm_source=' . rawurlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get Customer Support', 'smooth-scrolling' )
				);
				$plugin_meta[] = sprintf( "<a href='%s' target='_blank'>%s</a>",
					'https://gambit.ph/plugins?utm_source=' . rawurlencode( $plugin_data['Name'] ) . '&utm_medium=plugin_link',
					__( 'Get More Plugins', 'smooth-scrolling' )
				);
			}
			return $plugin_meta;
		}
	}

	new GambitSmoothScrollPlugin();
} // End if().
