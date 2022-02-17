<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * LOAD THE JAVASCRIPT FOR THE MAP
 */
if (!is_admin()) {
	
	if (function_exists('bp_is_active')) {

        global $bp;
        global $post;

        // Page slug
        $members_root = BP_MEMBERS_SLUG;
        $post_name = (is_page()) ? get_post($post)->post_title : "nothing";
        $current_slug = sanitize_title($post_name);

        // Get the API key
        $key_option = woffice_get_settings_option('gmap_api_key');
        $key = (!empty($key_option)) ? $key_option :  "AIzaSyAyXqXI9qYLIWaD9gLErobDccodaCgHiGs";

        // Language
        $language = substr(get_locale(), 0, 2);

        // We add the file
        wp_enqueue_script(
            'google-maps-api-v3',
            'https://maps.googleapis.com/maps/api/js?' . http_build_query(array(
                'v' => '3',
                'libraries' => 'places',
                'language' => $language,
                'key' => $key,
            )),
            true
        );

    }
	
}