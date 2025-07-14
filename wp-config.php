<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */
// define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);

 define('FS_METHOD', 'direct');
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tripcraft' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '123456' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'y52}7^eVsxv 5%60}uY3)hPw-Ty.8E|v^!</Sd1,;4:i(8A#J$EO/|n]RT,vg]]T' );
define( 'SECURE_AUTH_KEY',  'P>[MKVoyt*cu#1h:?!N@(Qk*+{0@p+/K!2gX3I~`#%<_,Le#?|pS=L,r/y!*h.R4' );
define( 'LOGGED_IN_KEY',    ')[VH2UjK|R^ F?@[q6s/oW!jRJ=WzgY]5Idc5^N4v4K6>3$?FRxU>`Vy[9gL^nFe' );
define( 'NONCE_KEY',        ')E. 7uhg,` Qrc`gkY+4SAup?XJ2fgh>!vK=TqNXW!BawEP-R1F?Ds!p0YKh^]]J' );
define( 'AUTH_SALT',        'N6YVb+(/NH?C_F4k1gMGJf}j*~FFv#35B7*F#(t4eUoR%up^[/>$)h&Vl6+?37E@' );
define( 'SECURE_AUTH_SALT', 'aiE|{OL]5S%Kr7pm4+Kxjl1`Sq![TQBnd&usJ?-atieQ/V 0_MlkY4n5>QN^I17}' );
define( 'LOGGED_IN_SALT',   'WY::6kIN6SXVsF5@frbN&xMt,vrwOUXhvh!?6X.9{@7>(`S.f}HkL361BY3nFZ!W' );
define( 'NONCE_SALT',       'B]lC~<2%nyxu<&EdAAjfz(3G^Z#pBv:=BHgNSc!Yopb[T92T}X}`R!]UhK5xO>mn' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
