<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Poll', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a poll widget in Woffice. (See documentation for detailed informations).', 'woffice' );
$manifest['version'] = '1.0.1';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/poll.jpg';
