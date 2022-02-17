<?php
if (!defined('FW')) {
	die('Forbidden');
}

if (!class_exists('WofficePluginsLicence')) :

	class WofficePluginsLicence
	{

		protected static $_instance = null;

		public static function instance()
		{
			if (is_null(self::$_instance)) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct()
		{
			add_action('admin_enqueue_scripts', array($this, 'woffice_related_script'));
			add_action('wp_ajax_woffice_plugins_licence_activate', array($this, 'woffice_plugins_licence_activate'));
			add_action('wp_ajax_nopriv_woffice_plugins_licence_activate', array($this, 'woffice_plugins_licence_activate'));
			add_action('wp_ajax_woffice_plugins_licence_deactivate', array($this, 'woffice_plugins_licence_deactivate'));
			add_action('wp_ajax_nopriv_woffice_plugins_licence_deactivate', array($this, 'woffice_plugins_licence_deactivate'));
		}
		public function woffice_related_script($hook)
		{
			wp_enqueue_script('woffice-licence-plugins-js',  get_template_directory_uri() . '/js/licence-plugins.min.js', array('jquery'));
			wp_localize_script('woffice-licence-plugins-js', 'licencedata', array('ajax_url' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('license-nonce')));
		}

		/**
		 * Activate licence Ajax
		 *
		 */
		public function woffice_plugins_licence_activate()
		{

			if ( !wp_verify_nonce( $_POST['nonce'], 'license-nonce' ) ) {
				die( __('Sorry! Direct Access is not allowed.', "woffice"));
			}
			
			$plugins_key =	$_POST['plugins_key'];
			$plugins_slug =	$_POST['plugins_slug'];
			if ($plugins_slug) {
				switch ($plugins_slug) {

					case 'woffice_cpt_key':

						$get_db_woffice_cpt_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_cpt_key) {
							$license = trim($get_db_woffice_cpt_key);
							$Woffice_Wo_CPT_License = new Woffice_Wo_CPT_License();
							// data to send in our API request
							$wocpt_licence_deactivate = $Woffice_Wo_CPT_License->wocpt_licence_activate($license);
							woffice_echo_output($wocpt_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the activate.', 'woffice');
							$response = array("type" => 'success', 'message' => $message, 'license_data' => $license_data,"plugins_slug" => 'woffice_cpt_key');
							echo json_encode($response);
							exit();
						}
						break;
						
					case 'woffice_woae_key':

						$get_db_woffice_woae_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_woae_key) {
							$license = trim($get_db_woffice_woae_key);
							$Woffice_woae_License = new Woffice_woae_License();
							// data to send in our API request
							$woae_licence_deactivate = $Woffice_woae_License->woae_licence_activate($license);
							$plugin_test_slug = json_encode($woae_licence_deactivate);
							woffice_echo_output($woae_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '',"plugins_slug" => 'woffice_woae_key');
							echo json_encode($response);
							exit();
						}
						break;
					case 'woffice_wosubscribe_key':

						$get_db_woffice_wosubscribe_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_wosubscribe_key) {
							$license = trim($get_db_woffice_wosubscribe_key);
							$Woffice_wosubscribe_License = new Woffice_Wosubscribe_License();
							// data to send in our API request
							$wosubscribe_licence_deactivate = $Woffice_wosubscribe_License->wowcps_licence_activate($license);
							woffice_echo_output($wosubscribe_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'afj_company_listings':
						$get_db_woffice_wpjob_companylisting_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_wpjob_companylisting_key) {
							$license = trim($get_db_woffice_wpjob_companylisting_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wosubscribe_License = new Plugin_Item_Activator();
							$wosubscribe_licence_activate = $Woffice_wosubscribe_License->licence_activate();
							woffice_echo_output($wosubscribe_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_review':
						$get_db_wpjm_reviews_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_reviews_key) {
							$license = trim($get_db_wpjm_reviews_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjob_review_License = new Plugin_Item_Activator();
							$wpjob_review_licence_activate = $Woffice_wpjob_review_License->licence_activate();
							woffice_echo_output($wpjob_review_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_autosuggest':
						$get_db_wpjm_autosuggest_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_autosuggest_key) {
							$license = trim($get_db_wpjm_autosuggest_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_autosuggest_License = new Plugin_Item_Activator();
							$wpjm_autosuggest_licence_activate = $Woffice_wpjm_autosuggest_License->licence_activate();
							woffice_echo_output($wpjm_autosuggest_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'afj_job_style':
						$get_db_afj_job_style_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_afj_job_style_key) {
							$license = trim($get_db_afj_job_style_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_afj_job_style_License = new Plugin_Item_Activator();
							$afj_job_style_licence_activate = $Woffice_afj_job_style_License->licence_activate();
							woffice_echo_output($afj_job_style_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_listing_labels':
						$get_db_wpjm_listing_labels_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_listing_labels_key) {
							$license = trim($get_db_wpjm_listing_labels_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_listing_labels_License = new Plugin_Item_Activator();
							$wpjm_listing_labels_activate = $Woffice_wpjm_listing_labels_License->licence_activate();
							woffice_echo_output($wpjm_listing_labels_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_listing_payment':
						$get_db_wpjm_listing_payment_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_listing_payment_key) {
							$license = trim($get_db_wpjm_listing_payment_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_listing_payment_License = new Plugin_Item_Activator();
							$wpjm_listing_payment_activate = $Woffice_wpjm_listing_payment_License->licence_activate();
							woffice_echo_output($wpjm_listing_payment_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_product':
						$get_db_wpjm_product_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_product_key) {
							$license = trim($get_db_wpjm_product_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_product_License = new Plugin_Item_Activator();
							$wpjm_product_activate = $Woffice_wpjm_product_License->licence_activate();
							woffice_echo_output($wpjm_product_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_stat':
						$get_db_wpjm_stat_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_stat_key) {
							$license = trim($get_db_wpjm_stat_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_stat_License = new Plugin_Item_Activator();
							$wpjm_stat_activate = $Woffice_wpjm_stat_License->licence_activate();
							woffice_echo_output($wpjm_stat_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wokss_kanban':
						$get_db_wokss_kanban_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wokss_kanban_key) {
							$license = trim($get_db_wokss_kanban_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wokss_kanban_License = new Plugin_Item_Activator();
							$wokss_kanban_licence_activate = $Woffice_wokss_kanban_License->licence_activate();
							woffice_echo_output($wokss_kanban_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;

					case 'advanced_tasks_for_woffice':
						$get_db_advanced_tasks_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_advanced_tasks_key) {
							$license = trim($get_db_advanced_tasks_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_advanced_tasks_License = new Plugin_Item_Activator();
							$wokss_advanced_tasks_activate = $Woffice_advanced_tasks_License->licence_activate();
							woffice_echo_output($wokss_advanced_tasks_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'docs_to_wiki':
						$get_db_docs_to_wiki_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_docs_to_wiki_key) {
							$license = trim($get_db_docs_to_wiki_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_docs_to_wiki_License = new Plugin_Item_Activator();
							$docs_to_wiki_licence_activate = $Woffice_docs_to_wiki_License->licence_activate();
							woffice_echo_output($docs_to_wiki_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'woffice_timeline':
						$get_db_woffice_timeline_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_timeline_key) {
							$license = trim($get_db_woffice_timeline_key);
							update_option($plugins_slug,$plugins_key);
							$woffice_timeline_License = new Plugin_Item_Activator();
							$woffice_timeline_licence_activate = $woffice_timeline_License->licence_activate();
							woffice_echo_output($woffice_timeline_licence_activate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					
					default:

						$message = __('An error occurred, please try again.', 'woffice');
						break;
				}
				
			} else {
				$response = array("type" => 'unsuccessful', 'message' => 'Slug Missing', 'license_data' => '');
				echo json_encode($response);
				exit();
			}
		}
		/**
		 * Deactivate licence Ajax
		 *
		 */
		public function woffice_plugins_licence_deactivate()
		{
			if ( !wp_verify_nonce( $_POST['nonce'], 'license-nonce' ) ) {
				die( __('Sorry! Direct Access is not allowed.', "woffice"));
			}

			$plugins_key =	$_POST['plugins_key'];
			$plugins_slug =	$_POST['plugins_slug'];
			
			if ($plugins_slug) {
				switch ($plugins_slug) {

					case 'woffice_cpt_key':

						$get_db_woffice_cpt_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_cpt_key) {
							$license = trim($get_db_woffice_cpt_key);
							$Woffice_Wo_CPT_License = new Woffice_Wo_CPT_License();
							// data to send in our API request
							$wocpt_licence_deactivate = $Woffice_Wo_CPT_License->wocpt_licence_deactivate($license);
							woffice_echo_output($wocpt_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '',"plugins_slug" => 'woffice_cpt_key');
							echo json_encode($response);
							exit();
						}
						break;
					case 'woffice_woae_key':

						$get_db_woffice_woae_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_woae_key) {
							$license = trim($get_db_woffice_woae_key);
							$Woffice_woae_License = new Woffice_woae_License();
							// data to send in our API request
							$woae_licence_deactivate = $Woffice_woae_License->woae_licence_deactivate($license);
							echo woffice_echo_output($woae_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '',"plugins_slug" => 'woffice_woae_key');
							echo json_encode($response);
							exit();
						}
						break;
					case 'woffice_wosubscribe_key':

						$get_db_woffice_wosubscribe_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_wosubscribe_key) {
							$license = trim($get_db_woffice_wosubscribe_key);
							$Woffice_wosubscribe_License = new Woffice_Wosubscribe_License();
							// data to send in our API request
							$wosubscribe_licence_deactivate = $Woffice_wosubscribe_License->wowcps_licence_deactivate($license);
							woffice_echo_output($wosubscribe_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'afj_company_listings':

						$get_db_wafj_company_listings = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wafj_company_listings) {
							$license = trim($get_db_wafj_company_listings);
							$Woffice_wosubscribe_License = new Plugin_Item_Activator();
							$wosubscribe_licence_deactivate = $Woffice_wosubscribe_License->licence_deactivate();
							woffice_echo_output($wosubscribe_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_review':
						$get_db_wpjm_reviews_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_reviews_key) {
							$license = trim($get_db_wpjm_reviews_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjob_review_License = new Plugin_Item_Activator();
							$wpjob_review_licence_deactivate = $Woffice_wpjob_review_License->licence_deactivate();
							woffice_echo_output($wpjob_review_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_autosuggest':
						$get_db_wpjm_autosuggest_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_autosuggest_key) {
							$license = trim($get_db_wpjm_autosuggest_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_autosuggest_License = new Plugin_Item_Activator();
							$wpjm_autosuggest_licence_deactivate = $Woffice_wpjm_autosuggest_License->licence_deactivate();
							woffice_echo_output($wpjm_autosuggest_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'afj_job_style':
						$get_db_afj_job_style_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_afj_job_style_key) {
							$license = trim($get_db_afj_job_style_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_afj_job_style_License = new Plugin_Item_Activator();
							$afj_job_style_licence_deactivate = $Woffice_afj_job_style_License->licence_deactivate();
							woffice_echo_output($afj_job_style_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_listing_labels':
						$get_db_wpjm_listing_labels_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_listing_labels_key) {
							$license = trim($get_db_wpjm_listing_labels_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_listing_labels_License = new Plugin_Item_Activator();
							$wpjm_listing_labels_licence_deactivate = $Woffice_wpjm_listing_labels_License->licence_deactivate();
							woffice_echo_output($wpjm_listing_labels_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_listing_payment':
						$get_db_wpjm_listing_payment_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_listing_payment_key) {
							$license = trim($get_db_wpjm_listing_payment_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_listing_payment_License = new Plugin_Item_Activator();
							$wpjm_listing_payment_licence_deactivate = $Woffice_wpjm_listing_payment_License->licence_deactivate();
							woffice_echo_output($wpjm_listing_payment_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_product':
						$get_db_wpjm_product_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_product_key) {
							$license = trim($get_db_wpjm_product_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_product_License = new Plugin_Item_Activator();
							$wpjm_product_licence_deactivate = $Woffice_wpjm_product_License->licence_deactivate();
							woffice_echo_output($wpjm_product_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wpjm_stat':
						$get_db_wpjm_stat_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wpjm_stat_key) {
							$license = trim($get_db_wpjm_stat_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wpjm_stat_License = new Plugin_Item_Activator();
							$wpjm_stat_licence_deactivate = $Woffice_wpjm_stat_License->licence_deactivate();
							woffice_echo_output($wpjm_stat_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'wokss_kanban':
						$get_db_wokss_kanban_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_wokss_kanban_key) {
							$license = trim($get_db_wokss_kanban_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_wokss_kanban_License = new Plugin_Item_Activator();
							$wokss_kanban_licence_deactivate = $Woffice_wokss_kanban_License->licence_deactivate();
							woffice_echo_output($wokss_kanban_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'advanced_tasks_for_woffice':
						$get_db_advanced_tasks_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_advanced_tasks_key) {
							$license = trim($get_db_advanced_tasks_key);
							update_option($plugins_slug,$plugins_key);
							$Woffice_advanced_tasks_License = new Plugin_Item_Activator();
							$advanced_tasks_licence_deactivate = $Woffice_advanced_tasks_License->licence_deactivate();
							woffice_echo_output($advanced_tasks_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					case 'woffice_timeline':
						$get_db_woffice_timeline_key = fw_get_db_settings_option($plugins_slug);
						if ($plugins_key == $get_db_woffice_timeline_key) {
							$license = trim($get_db_woffice_timeline_key);
							update_option($plugins_slug,$plugins_key);
							$woffice_timeline_License = new Plugin_Item_Activator();
							$woffice_timeline_licence_deactivate = $woffice_timeline_License->licence_deactivate();
							woffice_echo_output($woffice_timeline_licence_deactivate);
							exit();
						} else {
							$message = __('New licence key must be save before the deactivate.', 'woffice');
							$response = array("type" => 'unsuccessful', 'message' => $message, 'license_data' => '');
							echo json_encode($response);
							exit();
						}
						break;
					default:
						$message = __('An error occurred, please try again.', 'woffice');
						break;
				}
			} else {
				$response = array("type" => 'unsuccessful', 'message' => 'Slug Missing', 'license_data' => '');
				echo json_encode($response);
				exit();
			}
		}
	}
endif;

function WOFFICERELATED()
{
	return WofficePluginsLicence::instance();
}

WOFFICERELATED();
