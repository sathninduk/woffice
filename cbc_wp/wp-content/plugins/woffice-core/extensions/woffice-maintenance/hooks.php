<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Check if the there are defined views for the portfolio templates, otherwise are used theme templates
 *
 * @param string $template
 *
 * @return string
 */
function woffice_filter_fw_ext_maintenance_template_include( $template ) {

	$maintenance = fw()->extensions->get( 'woffice-maintenance' );

	if ( is_page( 'maintenance' )  ) {
		$new_template = $maintenance->locate_view_path( 'page-maintenance' );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}

	return $template;

}
add_filter( 'template_include', 'woffice_filter_fw_ext_maintenance_template_include' );

/**
 * Redirect user to the maintenance page
 */
function woffice_maintenance_redirect_user()
{

	/* We get the data */
	$maintenance_status = fw_get_db_ext_settings_option( 'woffice-maintenance', 'maintenance_status' );

	// If not enabled - we return nothing
	if ($maintenance_status === "off")
		return;

	// We check that there
	woffice_maintenance_create_check_page();

	if (!current_user_can('edit_themes')) {

		$maintenance_url = esc_url( home_url( '/maintenance/' ) );
		if (!is_page(array('maintenance','login'))){
			wp_redirect( $maintenance_url );
			exit;
		}

	}

}

add_action( 'template_redirect', 'woffice_maintenance_redirect_user' );

/**
 * Check that the maintenance page exists - or else create it
 *
 * @return void
 */
function woffice_maintenance_create_check_page()
{
	/* We check if the page already exist and if not then we create it */
	global $wpdb;
	$table_name = $wpdb->prefix . 'posts';
	$check_page = $wpdb->get_row("SELECT post_name FROM " . $table_name . " WHERE post_name = 'maintenance'", 'ARRAY_A');
	if (empty($check_page)) {
		$prop_page = array(
			'ID' => '',
			'post_title' => 'Maintenance',
			'post_content' => 'No need for content, all is in the Extension settings, the title does not matter too.',
			'post_excerpt' => '',
			'post_name' => 'maintenance',
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_author' => 1
		);
		wp_insert_post($prop_page);
	}
}