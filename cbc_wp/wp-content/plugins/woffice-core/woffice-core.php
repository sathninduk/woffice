<?php
/**
 * Plugin Name: Woffice Core
 * Plugin URI:  https://woffice.io
 * Description: Woffice extensions and settings
 * Version:     4.0.6
 * Author:      Xtendify
 * Author URI:  https://woffice.io
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woffice-core
 * Domain Path: /languages
 */

require_once 'woffice-functions.php';

/**
 * Class Woffice_Core
 *
 * @since 2.8.2
 */
class Woffice_Core
{
	/**
	 * Woffice_Core constructor.
	 */
	public function __construct()
	{
		define('WOFFICE_CORE_ENABLED', true);

		add_action('fw_extensions_locations', array($this, 'loadExtensions'));
		add_action('admin_enqueue_scripts', array($this, 'removeDpProEventGoogleMap'), 99);
		add_action('wp_enqueue_scripts', array($this, 'avoidEventonMapsConflict'), 99);
		add_action('after_setup_theme', array($this, 'removeAdminBar'));
		add_action('fw_init', array($this, 'fwInit'));
		add_action( 'widgets_init', array($this, 'removePluginWidgets') );
		add_action('admin_bar_menu', array($this, 'toolbarAdminMenu'), 999);
	}

	/**
	 * Creates the Woffice admin menu in the top bar
	 *
	 * @param $wp_admin_bar
	 */
	public function toolbarAdminMenu($wp_admin_bar)
	{

		/*DOC LINK*/
		$topbar_woffice = woffice_get_settings_option('topbar_woffice');

		if (current_user_can('administrator') && $topbar_woffice == "yep") {
			// Main Link
			$wp_admin_bar->add_node(array(
				'id' => 'woffice',
				'title' => 'Woffice',
				'href' => admin_url('themes.php?page=fw-settings'),
				'meta' => array('class' => 'woffice_page')
			));

			$wp_admin_bar->add_node(array(
				'id' => 'woffice_settings',
				'title' => 'Theme Settings',
				'parent' => 'woffice',
				'href' => admin_url('themes.php?page=fw-settings'),
				'meta' => array('class' => 'woffice-theme-settings')
			));

			$settings = array(
				'general' => __('General', 'woffice'),
				'chat' => __('Live chat', 'woffice'),
				'permissions' => __('Permissions', 'woffice'),
				'login' => __('Login/Register', 'woffice'),
				'dashboard' => __('Dashboard', 'woffice'),
				'buddypress' => __('BuddyPress', 'woffice'),
				'posts' => __('Posts/Wiki/Projects', 'woffice'),
				'news' => __('Blog', 'woffice'),
				'menu' => __('Menu', 'woffice'),
				'header' => __('Header bar', 'woffice'),
				'page-title' => __('Page title', 'woffice'),
				'sidebar' => __('Sidebar', 'woffice'),
				'footer' => __('Footer & Extrafooter', 'woffice'),
				'styling' => __('Styling', 'woffice'),
				'custom' => __('Custom code', 'woffice'),
				'settings' => __('System status', 'woffice'),
			);

			foreach ($settings as $tab_key=>$tab_label) {
				$wp_admin_bar->add_node(array(
					'id' => 'woffice_settings_'. $tab_key,
					'title' => $tab_label,
					'parent' => 'woffice_settings',
					'href' => admin_url('themes.php?page=fw-settings#fw-options-tab-'. $tab_key),
					'meta' => array('class' => 'woffice-theme-settings-'. $tab_key)
				));
			}

			$wp_admin_bar->add_node(array(
				'id' => 'woffice_doc',
				'title' => 'Online Documentation',
				'parent' => 'woffice',
				'href' => 'https://alkaweb.atlassian.net/wiki/spaces/WOF/overview',
				'meta' => array('class' => 'woffice-documentation-page')
			));

			$wp_admin_bar->add_node(array(
				'id' => 'woffice_feedback',
				'title' => 'Give your Feedback ($5 Amazon eGift)',
				'parent' => 'woffice',
				'href' => 'https://fdier.co/woffice',
				'meta' => array('class' => 'woffice-feedback-page')
			));

			$wp_admin_bar->add_node(array(
				'id' => 'woffice_extensions',
				'title' => 'Extensions',
				'parent' => 'woffice',
				'href' => admin_url('index.php?page=fw-extensions'),
				'meta' => array('class' => 'woffice-extension-page')
			));
			$wp_admin_bar->add_node(array(
				'id' => 'woffice_welcome',
				'title' => 'Getting Started',
				'parent' => 'woffice',
				'href' => admin_url('index.php?page=woffice-welcome'),
				'meta' => array('class' => 'woffice-welcome-page')
			));
			$wp_admin_bar->add_node(array(
				'id' => 'woffice_support',
				'title' => 'Support',
				'parent' => 'woffice',
				'href' => 'https://alkaweb.ticksy.com/',
				'meta' => array('class' => 'woffice-support-page')
			));
			$wp_admin_bar->add_node(array(
				'id' => 'woffice_plugins',
				'title' => 'Download bundled plugins',
				'parent' => 'woffice',
				'href' => '#',
				'meta' => array('class' => 'woffice-plugin-page')
			));

			$bundled = array(
				'revslider',
				'dpProEventCalendar',
				'js_composer',
				'vc-super-bundle',
				'eventON',
				'eventon-full-cal',
				'multiverso',
			);

			foreach ($bundled as $slug) {
				$plugin_info = woffice_core_bundled_plugin($slug);

				$wp_admin_bar->add_node(array(
					'id' => 'woffice_plugins_'. $slug,
					'title' => $plugin_info['name'],
					'parent' => 'woffice_plugins',
					'href' => $plugin_info['source'],
					'meta' => array('class' => 'woffice-theme-plugins-'. $slug)
				));
			}

			$wp_admin_bar->add_node(array(
				'id' => 'woffice_changelog',
				'title' => 'Changelog',
				'parent' => 'woffice',
				'href' => 'https://hub.woffice.io/woffice/changelog/',
				'meta' => array('class' => 'woffice-changelog-page')
			));

			$wp_admin_bar->add_node(array(
				'id' => 'woffice_feedier',
				'title' => 'Collect Feedback with Feedier',
				'parent' => 'woffice',
				'href' => 'https://feedier.com?ref=woffice_menu',
				'meta' => array('class' => 'feedier')
			));
		}
	}

	/**
	 * Remove the extra plugin widgets
	 */
	public function removePluginWidgets()
	{
		if (class_exists('multiverso_mv_category_files')) {
			unregister_widget('multiverso_mv_category_files');
			unregister_widget('multiverso_login_register');
			unregister_widget('multiverso_mv_personal_recent_files');
			unregister_widget('multiverso_mv_recent_files');
			unregister_widget('multiverso_search');
			unregister_widget('multiverso_mv_registered_recent_files');
		}

		if (class_exists('EventON')) {
			unregister_widget('EvcalWidget');
			unregister_widget('EvcalWidget_SC');
			unregister_widget('EvcalWidget_three');
			unregister_widget('EvcalWidget_four');
		}

		if (class_exists('bbPress')) {
			unregister_widget('BBP_Login_Widget');
		}
	}

	/**
	 * Setting our own / customer key for the Google Map API
	 */
	public function replaceGmapsScript()
	{
		$handle = 'google-maps-api-v3';

		if (!wp_script_is($handle) || !defined( 'FW')) {
			return;
		}

		wp_dequeue_script($handle);
		wp_deregister_script($handle);

		/* GET THE API KEY */
		$key_option = woffice_get_settings_option('gmap_api_key');
		if (!empty($key_option)){
			$key = $key_option;
		}
		else {
			$key = "AIzaSyAyXqXI9qYLIWaD9gLErobDccodaCgHiGs";
		}

		wp_enqueue_script(
			$handle,
			'https://maps.googleapis.com/maps/api/js?'. http_build_query( array(
				'v'         => '3.15',
				'libraries' => 'places',
				'language'  => substr(get_locale(),0,2),
				'key'       => $key,
			) ),
			array(),
			fw()->manifest->get_version(),
			true
		);
	}

	/**
	 * Fixing a page builder conflict issue with Unyson
	 *
	 * @link https://github.com/ThemeFuse/Unyson-PageBuilder-Extension/commit/a780e1789e6ff454e3382ac71dd98c78b7844037
	 */
	public function fwInit()
	{
		if (function_exists('fw') && fw()->extensions->get( 'page-builder' ) ) {
			if ( version_compare( fw_ext( 'page-builder' )->manifest->get_version(), '1.5.6', '>=' ) ) {
				add_action( 'admin_enqueue_scripts', array($this, 'replaceGmapsScript'), 20 );
			} else {
				add_action( 'admin_print_scripts', array($this, 'replaceGmapsScript'), 20 );
			}
		}
	}

	/**
	 * Remove the admin bar for any user if he isn't an administrator
	 */
	public function removeAdminBar()
	{
		/**
		 * Custom filter to allow the admin bar in the frontend for a certain role
		 *
		 * @param string - the role name or any valid Capability
		 */
		$role = apply_filters('woffice_admin_bar_capability', 'administrator');

		if (!current_user_can($role) && !is_admin()) {
			show_admin_bar(false);
		}
	}

	/**
	 * Deactivate EventON Google Map API calls
	 */
	public function avoidEventonMapsConflict()
	{
		if (wp_script_is('google-maps-api-v3')) {
			wp_dequeue_script('evcal_gmaps');
			wp_deregister_script('evcal_gmaps');
		}
	}

	/**
	 * Temporary patch regarding
	 * WordPress Pro Event Calendar and Unyson Map conflict
	 * We can't stop the plugin to load the API and the MAPS API loaded
	 * does not have all the parameters requested by the Unyson map option type
	 * "Cannot read property 'Autocomplete' of undefined"
	 * @since 2.1.5
	 */
	public function removeDpProEventGoogleMap()
	{
		wp_deregister_script('gmaps');
	}

	/**
	 * Load the Woffice extensions
	 *
	 * @param array $locations
	 *
	 * @return array
	 */
	public function loadExtensions($locations)
	{
		$locations[dirname(__FILE__) .'/extensions'] = dirname(__FILE__) .'extensions';

		return $locations;
	}
}

new Woffice_Core();
