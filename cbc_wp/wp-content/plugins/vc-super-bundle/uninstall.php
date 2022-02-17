<?php
/**
 * Uninstallation, also call feature uninstalls.
 *
 * @version 1.0
 * @package VC Super Bundle
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Call the uninstall routines of the other plugins.
require_once( 'class-vc-super-bundle.php' );
$features = VC_Super_Bundle::get_features();

foreach ( $features as $feature_name => $feature_info ) {
	if ( ! empty( $feature_info['uninstall'] ) ) {
		if ( file_exists( $feature_info['uninstall'] ) ) {
			include( $feature_info['uninstall'] );
		}
	}
}
