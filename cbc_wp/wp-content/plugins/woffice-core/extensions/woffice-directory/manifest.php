<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Directory', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a complete directory page to your site for Jobs, Partners...', 'woffice' );
$manifest['version']     = '2.0.0';
$manifest['display']     = true;
$manifest['standalone']  = true;
$manifest['thumbnail']   = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/directory.jpg';
