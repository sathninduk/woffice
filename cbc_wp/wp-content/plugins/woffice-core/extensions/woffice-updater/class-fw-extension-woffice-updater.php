<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Extension_Woffice_Updater extends FW_Extension {
	/**
	 * @internal
	 */
	public function _init() {
		add_action('fw_extension_settings_form_saved:woffice-updater', array($this, 'woffice_tf_check'));
	}

	/**
	 * CHECK IF THE PURCHASE CODE IS VALID
	 */
	public function woffice_tf_check() {

		/*
		 * We get the data from the extension and site OPTIONS
		 */
		$tf_username        = fw_get_db_ext_settings_option('woffice-updater', 'tf_username');
		$tf_purchasecode    = fw_get_db_ext_settings_option('woffice-updater', 'tf_purchasecode');
		$site_url           = get_site_url();

		if (empty($tf_username) || empty($tf_purchasecode)) {
			return;
		}


		/**
		 * Filter is the hub updated is enabled
		 *
		 * @param bool
		 */
		$enable_hub = apply_filters('woffice_hub_updater_enabled', true);

		if ($enable_hub) {

            /*
             * We build the request
             */
            $request_string = array(
                'body' => array(
                    'username' => htmlspecialchars($tf_username),
                    'purchase_code' => htmlspecialchars($tf_purchasecode),
                    'product_sku' => '11671924',
                    'email' => get_option('admin_email'),
                    'site_url' => $site_url,
                ),
                'headers' => array(
                    'X-ProductKey' => base64_encode(get_option('woffice_key'))
                )
            );

            /*
             * We call the API
             */
            $raw_response = wp_remote_post('https://hub.woffice.io/api/license/updater/', $request_string);

        } else {

            /*
             * We build the request
             */
            $request_string = array(
                'body' => array(
                    'action' => 'check_purchase',
                    'username' => htmlspecialchars($tf_username),
                    'purchase_code' => htmlspecialchars($tf_purchasecode),
                    'site_url' => $site_url,
                )
            );

            /*
             * We call the API
             */
            $raw_response = wp_remote_post('https://woffice.io/updater/theme-updater.php', $request_string);

        }

		/*
		 * We check the response
		 */
		$response = null;
    	if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) ) {
    		$response = $raw_response['body'];
    	}
    	if(!empty($response) ) {
	    	// If it's checked OR if we're on Localhost
    		if ($response == 'true' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
	    		/*If it works we save that data in the options table*/
				update_option('woffice_license','checked');
				return true;
    		}
    		else {
	    		update_option('woffice_license','not-checked');
				return;
    		}
    	}

	}

}
