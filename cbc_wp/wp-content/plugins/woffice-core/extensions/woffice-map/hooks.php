<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
	
/**
 * Generate the js for the map
 *
 * @return void
 */		
function woffice_members_map_js_users(){
	if (function_exists('bp_is_active') && bp_is_members_directory()) {
        echo fw()->extensions->get('woffice-map')->usersMapJs("members");
    }
}
add_action('wp_footer', 'woffice_members_map_js_users');

/**
 * Renders the map in the directory
 */
function woffice_members_output_map() {
	echo fw()->extensions->get( 'woffice-map' )->render( 'view' );
}
add_action('bp_before_directory_members_page', 'woffice_members_output_map');

/**
 * Create the field to prompt the user location
 *
 * @return void
 */
function woffice_location_add_field() {

    if (!woffice_bp_is_active('xprofile'))
        return;

    $field = woffice_get_the_location_field();
    if(count($field) > 0) {
        //in order to remove the old textarea on some Woffice websites
        if ($field[0]->type === "textarea") {
            global $wpdb;
            $table_name = woffice_get_xprofile_table('fields');
            $wpdb->update(
                $table_name,
                array(
                    'type' => 'textbox'
                ),
                array( 'id' => $field[0]->id ),
                array(
                    '%s',	// string
                ),
                array( '%d' )
            );
        }
        return;
    }
    xprofile_insert_field(
        array (
            'field_group_id'  => 1,
            'can_delete' => true,
            'type' => 'textbox',
            'description' => __('This address will be used on the members directory map, please make sure this address is valid for Google Map.','woffice'),
            'name' => fw()->extensions->get('woffice-map')->mapFieldName(),
            'field_order'     => 1,
            'is_required'     => false,
        )
    );

}
add_action('xprofile_updated_profile', 'woffice_location_add_field');

/**
 * We send the data to our scripts.js file
 *
 * @param array $data - the current data sent to the file
 *
 * @return array
 */
if(!function_exists('woffice_location_data_exchanger')) {
    function woffice_location_data_exchanger($data) {

        $field = woffice_get_the_location_field();

        if(is_null($field) || empty($field) )
            return $data;

        $data['input_location_bb'] = 'field_' . $field[0]->id;

        return $data;

    }
}
add_filter('woffice_js_exchanged_data', 'woffice_location_data_exchanger');

/**
 * Refresh all map coordinates for all members
 *
 * @return void
 */
function woffice_map_refresh_all_coordinates() {

    if (!isset($_GET["refresh_all_coordinates"]))
        return;

    if ($_GET["refresh_all_coordinates"] === "true") {
        $ext_instance = fw()->extensions->get('woffice-map');
        $ext_instance->saveAllMembers();
        wp_redirect(admin_url('admin.php?page=fw-extensions&sub-page=extension&extension=woffice-map&refresh_all_coordinates=done'));
        exit();
    }
    else if ($_GET["refresh_all_coordinates"] === "done") {
        /**
         * Triggers a notice in the backend in order to let the user know that the operation succeeded
         */
        function woffice_map_refresh_admin_notice_success() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Done!', 'woffice' ); ?></p>
            </div>
            <?php
        }
        add_action( 'admin_notices', 'woffice_map_refresh_admin_notice_success' );
    }

}
add_action('admin_init', 'woffice_map_refresh_all_coordinates');
