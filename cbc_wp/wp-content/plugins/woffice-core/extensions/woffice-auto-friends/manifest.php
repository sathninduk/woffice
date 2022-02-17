<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Auto Friends', 'woffice' );
$manifest['description'] = __( 'Create friendship relationships automatically on BuddyPress.', 'woffice' );
$manifest['version']     = '1.0.0';
$manifest['display']     = true;
$manifest['standalone']  = true;
$manifest['thumbnail']   = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/extension.jpg';
