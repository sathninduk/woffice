<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Updater', 'woffice' );
$manifest['description'] = __( 'Auto Update your theme with the latest release, just by entering your purchase code.', 'woffice' );
$manifest['version'] = '2.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/updater.jpg';
