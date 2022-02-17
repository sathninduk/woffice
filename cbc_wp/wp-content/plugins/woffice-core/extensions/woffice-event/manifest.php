<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Calendar Event', 'woffice' );
$manifest['description'] = __( 'Create calendar events.', 'woffice' );
$manifest['version']     = '1.0.0';
$manifest['display']     = true;
$manifest['standalone']  = true;
$manifest['thumbnail']   = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/extension.jpg';
