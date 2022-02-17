<?php
/**
 * Uninstallation routine.
 *
 * @package Smooth MouseWheel
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'gambit_smoothscroll_speed_new' );
delete_option( 'gambit_smoothscroll_amount' );
