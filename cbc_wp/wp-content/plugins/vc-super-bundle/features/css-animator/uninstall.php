<?php
/**
 * Plugin uninstallation routine.
 *
 * @version 1.7
 * @package CSS Animator for VC
 */

// If uninstall is not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Deletes compatibility mode options stored.
delete_option( '_gambit_css_animator_compat_mode' );
