<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Time Tracking', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a time tracking widget for your staff.', 'woffice' );
$manifest['version'] = '1.1.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/time-tracking.jpg';
