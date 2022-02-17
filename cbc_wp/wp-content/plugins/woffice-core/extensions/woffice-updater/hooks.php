<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
* CHeck for new version - Update
*/
function woffice_pre_set_transient_update_theme ( $transient )
{

    /*
     * We first check if it's the purchase code has been entered
     */
    $status = get_option('woffice_license');
    if ($status == "checked") {

        if (empty($transient->checked))
            return $transient;

        $theme_version = fw()->theme->manifest->get('version');
        $theme_slug    = fw()->theme->manifest->get('id');
        $beta          = fw_get_db_ext_settings_option('woffice-updater', 'beta');

        $request_string = array(
            'body' => array(
                'action' => 'check_updated',
                'version' => $theme_version,
                'beta' => $beta
            )
        );

	    /**
	     * Filter if the hub version checker is enabled
	     *
	     * @param bool
	     */
        $enable_hub = apply_filters('woffice_hub_version_check_enabled', true);

        if ($enable_hub) {

            /*
             * We call our API
             */
            $raw_response = wp_remote_get('https://hub.woffice.io/api/woffice/version/', $request_string);

            /*
             * We also send out the product keys and update Hub DB
             * This will be called on each website using the updater
             * It will allow us to make our db more consistent
             *
             * We are using the only parameters available to us
             * TF related parameters are not stored into the DB
             */
            $request_string_product = array(
                'body' => array(
                    'product_sku' => '11671924',
                    'email' => get_option('admin_email'),
                    'site_url' => get_site_url(),
                ),
                'headers' => array(
                    'X-ProductKey' => base64_encode(get_option('woffice_key'))
                )
            );
            wp_remote_post('https://hub.woffice.io/api/license/key/', $request_string_product);

            /*
             * We check the response
             */
            $response = null;
            if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
                $response = json_decode($raw_response['body'], true);
            }


        } else {

            /*
             * We call our API
             */
            $raw_response = wp_remote_post('https://woffice.io/updater/theme-updater.php', $request_string);

            /*
             * We check the response
             */
            $response = null;
            if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
                $response = unserialize($raw_response['body']);
            }

        }

        if (!empty($response)) // Feed the update data into WP updater
            $transient->response[$theme_slug] = $response;

        return $transient;

    }

}
add_filter ( 'pre_set_site_transient_update_themes', 'woffice_pre_set_transient_update_theme' );
