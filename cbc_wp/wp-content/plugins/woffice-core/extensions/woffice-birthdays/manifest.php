<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Birthdays', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a birthdays widget in Woffice. (See documentation for detailed information).', 'woffice' );
$manifest['version'] = '1.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/birthdays.jpg';
