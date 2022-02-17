<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cbc_cbc_wp' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Multisite */
define( 'WP_ALLOW_MULTISITE', true );

/* WP MULTISITE SETUP */
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'localhost' );
define( 'PATH_CURRENT_SITE', '/cbc_wp/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'rFIi; QeBm+llLLf2SQDZER8crxHr-5B)3 +P~59hg/4xJm>Je3I?<,D@NLcPD?5' );
define( 'SECURE_AUTH_KEY',  'ZwgrY`0wU$ N@g=ZCA-N:@8?B&vunc!bv(Z~G_-vyWTMK3tQc{+%6ltIBVucM9;H' );
define( 'LOGGED_IN_KEY',    'cr,c`LW&xy<Vq~+1?5]{f(qJ_&)x)_t%]_-57iev`@G^nb/7M5VjYmAWN:zWo.Fv' );
define( 'NONCE_KEY',        '{bL~xxLlVU3&.ajP:jz8B<kpYAmup%b%[bDhRqbUbg3C?LSu@tW&s1{*W|Fcm-l#' );
define( 'AUTH_SALT',        '.OJ&bSjSs7!joKNp=QER`=@6,9$>V;)Rm_;#7RN t1i-SF3jsp_kZ4JjLa$Sk[,:' );
define( 'SECURE_AUTH_SALT', '>f[2XhOvLL(NQo:0DZ:lU Cgxj@J)GgX5vTK@CF941>g<|BuW,)@T.+!8hOV56$W' );
define( 'LOGGED_IN_SALT',   'r?o~Bci|Vex_JdV2)BVT*uiHh-Wy^Z;>$GmGbZ5}grxVw}ad;!kNnG>To3}@5fAM' );
define( 'NONCE_SALT',       'u6z)Cw0{HCty(&S&_v_6`M6E:Bc4@bt4NFP~/3Gn`dd$Y]*2ZGEN]qI_4I5B#V:+' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
