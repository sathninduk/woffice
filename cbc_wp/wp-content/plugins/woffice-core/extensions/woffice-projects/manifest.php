<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Woffice Projects', 'woffice' );
$manifest['description'] = __( 'Enables the possibility to add a project management to your site.', 'woffice' );
$manifest['version'] = '3.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;
$manifest['thumbnail'] = plugin_dir_url( __FILE__ ) .'/static/img/thumbnails/projects.jpg';
