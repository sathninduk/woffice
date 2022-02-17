<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Members Map', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a map of Buddypress members in Woffice. (See documentation for detailed informations).', 'woffice' );
$manifest['version'] = '2.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/map.jpg';
