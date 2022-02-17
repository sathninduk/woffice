<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Push Notifications', 'woffice' );
$manifest['description'] = __( 'Browser push notifications on BuddyPress new activities.', 'woffice' );
$manifest['version']     = '1.0.0';
$manifest['display']     = true;
$manifest['standalone']  = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/push.jpg';
