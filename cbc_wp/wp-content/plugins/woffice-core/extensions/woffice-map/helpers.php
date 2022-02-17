<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Returns the MAP HTML
 *
 * @return string
 */
function woffice_get_members_map() {
	return fw()->extensions->get( 'woffice-map' )->render( 'view' );
}

/**
 * Return the location field id
 *
 * @return array
 */
function woffice_get_the_location_field() {

    if (!function_exists('bp_is_active'))
        return array();

    global $wpdb;

    $field_name = fw()->extensions->get( 'woffice-map' )->mapFieldName();
    $table_name = woffice_get_xprofile_table('fields');
    $sqlStr = "SELECT `id`, `type` FROM $table_name WHERE `name` = '$field_name'";
    $field = $wpdb->get_results($sqlStr);

    return $field;

}