<?php
/**
Plugin Name: Super Bundle for WPBakery Page Builder
Description: A super bundle of different addons to get the most out of WPBakery Page Builder (Formerly Visual Composer)
Author: Gambit Technologies, Inc
Version: 1.4.2
Author URI: http://gambit.ph
Plugin URI: https://codecanyon.net/user/gambittech/portfolio
Text Domain: super-bundle
Domain Path: /languages
 *
 * The main plugin file.
 *
 * @package VC Super Bundle
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly.
}

// Identifies the current plugin version.
defined( 'VERSION_GAMBIT_VC_SUPER_BUNDLE' ) || define( 'VERSION_GAMBIT_VC_SUPER_BUNDLE', '1.4.2' );

// The slug used fot translation & other identifiers.
defined( 'GAMBIT_VC_SUPER_BUNDLE' ) || define( 'GAMBIT_VC_SUPER_BUNDLE', 'super-bundle' );

// Initializes plugin class.
if ( ! class_exists( 'VC_Super_Bundle' ) ) {

	/**
	 * Initializes core plugin that is readable by WordPress.
	 *
	 * @return void
	 * @since 1.0
	 */
	class VC_Super_Bundle {

		/**
		 * Hook into WordPress.
		 *
		 * @return	void
		 * @since	1.0
		 */
		function __construct() {

			// Create the admin submenu.
			add_action( 'admin_menu', array( $this, 'add_submenu' ) );

			// Add a link to the settings page in the plugin list page.
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_settings_link' ) );

			// Add our styles and scripts for our settings page.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Ajax handler for enabling/disabling features.
			add_action( 'wp_ajax_sb_toggle_feature', array( $this, 'wp_ajax_sb_toggle_feature' ) );

			// Loads all our features.
			add_action( 'plugins_loaded', array( $this, 'load_features' ) );
		}

		/**
		 * Adds a link to the settings page in the plugin list page.
		 *
		 * @param array $links The current links.
		 *
		 * @return array The links.
		 */
		public function add_plugin_settings_link( $links ) {
			$settings_link = '<a href="admin.php?page=super-bundle">' . __( 'Settings', 'super-bundle' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 * Load all the features enabled.
		 */
		public function load_features() {

			// Get the features enabled.
			$features_enabled = get_option( 'sb_features_enabled' );
			if ( empty( $features_enabled ) ) {
				$features_enabled = array();
			}

			// Require the plugin files.
			$features = self::get_features();
			foreach ( $features_enabled as $feature_name ) {
				if ( ! empty( $features[ $feature_name ]['require'] ) ) {
					require_once( $features[ $feature_name ]['require'] );
				}
			}
		}

		/**
		 * Add our own menu within VC's menu.
		 */
		public function add_submenu() {
			if ( ! defined( 'VC_PAGE_MAIN_SLUG' ) ) {
				return;
			}

			add_submenu_page(
				VC_PAGE_MAIN_SLUG,
				__( 'Super Bundle', 'super-bundle' ),
				__( 'Super Bundle', 'super-bundle' ),
				'manage_options',
				'super-bundle',
				array( $this, 'render_settings_page' )
			);
		}

		/**
		 * Returns a list of all the features available and the functions
		 * to call when they're enabled/disabled.
		 */
		public static function get_features() {

			// This list contains the array of all our plugins/features.
			include 'plugin-list.php';

			// Allow other plugins to add/modify this feature list.
			return apply_filters( 'sb_get_features', $features );
		}

		/**
		 * Render our settings page.
		 */
		public function render_settings_page() {

			?>
			<div class='wrap super-bundle-wrap'>
			<h1><?php esc_html_e( 'Super Bundle', 'super-bundle' ) ?></h1>
			<p>
				<?php esc_html_e( 'Super bundle contains a lot of awesome elements and addons for WPBakery Page Builder. You can enable/disable the different elements and features here.', 'super-bundle' ) ?>
			</p>
			<div class="card">
				<h2 class="title"><?php esc_html_e( 'Activate / Deactivate Features', 'super-bundle' ) ?></h2>
				<?php

				$features = self::get_features();
				$features_enabled = get_option( 'sb_features_enabled' );
				if ( empty( $features_enabled ) ) {
					$features_enabled = array();
				}

				$current_group = '';

				foreach ( $features as $feature_name => $feature_info ) {

					if ( empty( $feature_info['group'] ) ) {
						$feature_info['group'] = __( 'General', 'super-bundle' );
					}

					if ( $current_group !== $feature_info['group'] ) {
						?>
						<p class="gvc-group-title"><?php echo esc_html( $feature_info['group'] ) ?></p>
						<?php
						$current_group = $feature_info['group'];
					}

					$is_checked = in_array( $feature_name, $features_enabled, true );

					?>
					<p>
						<?php
						echo esc_html( $feature_info['name'] );
						if ( ! empty( $feature_info['new'] ) ) {
							?>
							<sup class="gvc-new"><?php echo $feature_info['new'] ?></sup>
							<?php
						}
						?>
						<a href="#" data-title="<?php echo esc_attr( $feature_info['description'] ) ?>"><span class="dashicons dashicons-editor-help"></span></a>
						<span class="spinner"></span>
						<label class="gvc-switch <?php echo $is_checked ? 'is-enabled' : '' ?>">
						  <input type="checkbox" class="gvc-switch-toggle" data-feature="<?php echo esc_attr( $feature_name ) ?>" <?php checked( $is_checked ) ?>>
						  <span class="gvc-switch-on"><?php esc_html_e( 'Enabled', 'super-bundle' ) ?></span>
						  <span class="gvc-switch-ui"></span>
						  <span class="gvc-switch-off"><?php esc_html_e( 'Disabled', 'super-bundle' ) ?></span>
						</label>
						<?php
						if ( ! empty( $feature_info['demo'] ) ) {
							?>
							<a href="http://gambitph.github.io/cc/superbundle/demo.html#<?php echo esc_attr( $feature_name ) ?>" class="gvc-demo" target="gvc-demo"><?php esc_html_e( 'See it in action', 'super-bundle' ) ?></a>
							<?php
						}
						if ( ! empty( $feature_info['admin_settings'] ) ) {
							?>
							<a href="<?php echo esc_url( admin_url( $feature_info['admin_settings'] ) ) ?>" class="gvc-settings"><?php esc_html_e( 'Configure', 'super-bundle' ) ?></a>
							<?php
						}
						?>
					</p>
					<?php
				} // End foreach().
				?>

			</div>
			</div>
			<?php
		}

		/**
		 * Enqueue our all scripts and styles for our admin settings.
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( __CLASS__, plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery', 'wp-util' ), VERSION_GAMBIT_VC_SUPER_BUNDLE );
			wp_enqueue_style( __CLASS__, plugins_url( 'css/admin.css', __FILE__ ), array(), VERSION_GAMBIT_VC_SUPER_BUNDLE );
			wp_localize_script( __CLASS__, 'sbParams', array(
				'nonce' => wp_create_nonce( 'super-bundle' ),
			) );
		}

		/**
		 * Ajax handler for toggling features from the settings page.
		 */
		public function wp_ajax_sb_toggle_feature() {

			// Check post variables.
			if ( empty( $_POST['nonce'] ) || empty( $_POST['features'] ) ) { // Input var ok.
				wp_send_json_error( __( 'Invalid parameters', 'super-bundle' ) );
			}

			// Security.
			$nonce = sanitize_key( $_POST['nonce'] ); // Input var ok.
			if ( ! wp_verify_nonce( $nonce, 'super-bundle' ) ) {
				wp_send_json_error( __( 'Invalid nonce, please refresh the page', 'super-bundle' ) );
			}

			// Remove the disabled features so we only have a list of enabled ones.
			$features = array_map( 'sanitize_text_field', wp_unslash( $_POST['features'] ) ); // Input var ok.
			$enabled_features = array();
			foreach ( $features as $feature_name => $status ) {
				if ( 'enable' === $status ) {
					$enabled_features[] = $feature_name;
				}
			}

			// Save the new list.
			$enabled_features = apply_filters( 'sb_features_enabled', $enabled_features );
			update_option( 'sb_features_enabled', $enabled_features );

			wp_send_json_success( __( 'Feature toggled successfully', 'super-bundle' ) );
		}
	}

	new VC_Super_Bundle();

} // End if().

/**
 * Actions to perform when the plugin is activated for the first time.
 */
register_activation_hook( __FILE__, 'sb_activation_hook' );
if ( ! function_exists( 'sb_activation_hook' ) ) {

	/**
	 * Enable all plugin features upon activation if not yet available.
	 */
	function sb_activation_hook() {
		if ( get_option( 'sb_features_enabled' ) === false ) {
			$all_features = VC_Super_Bundle::get_features();
			$all_features = array_keys( $all_features );
			
			// Set undo/redo by default.
			unset( $all_features[ array_search( 'undo-redo', $all_features ) ] );

			update_option( 'sb_features_enabled', $all_features );
		}
	}
}
