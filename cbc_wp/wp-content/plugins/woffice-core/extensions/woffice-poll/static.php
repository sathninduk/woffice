<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * LOAD THE JAVASCRIPT FOR THE FORM
 */
if ( !is_admin() && defined('WOFFICE_THEME_VERSION')) {

		$ext_instance = fw()->extensions->get( 'woffice-poll' );

		wp_enqueue_script(
			'fw-extension-'. $ext_instance->get_name() .'-woffice-poll-scripts',
			woffice_get_extension_uri('poll', 'static/js/woffice-poll-scripts.js'),
			array( 'jquery', 'woffice-theme-script'),
            WOFFICE_THEME_VERSION,
			true
		);


}