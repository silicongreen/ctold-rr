<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'classtune');

/** MySQL database username */
define('DB_USER', 'champs21');

/** MySQL database password */
define('DB_PASSWORD', '079366');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4.-SOJJQPmXL`B93!0i:&_<It=Go%ZZY,nW*SWAmr}2F2wDD5Qv&)^Ma7ss;p%@i');
define('SECURE_AUTH_KEY',  'iQ:eG(~wW45ol2sc+-:Vxa<FtwyE(p)8j.+-}^2VE~AWCOX@QJ?6<Z0T#~MuLm^A');
define('LOGGED_IN_KEY',    ' euxTWxL.d+7D*Io7Z[y*@.?ITzl?r-bmx?W|YMcI2MFEQZjRDVfZj^Q:qNAw5wP');
define('NONCE_KEY',        '@w6D4T~(f<qpdq ;;zVlVt2 ?,M:|Er6G1DJzns>Dj733z7(WNEVmzV~6]hkl%x=');
define('AUTH_SALT',        ']WYsL<MZ|LTzC*?/4 lu?vORIODUv=}s_Q^t)z=K|?.`Wlr}(&!$fr UUIG5H_rT');
define('SECURE_AUTH_SALT', 'ne]oLh7r[(t9Pq*FLuN=ls{Dc^yJ!T2>7[5RQ!Nc(Y[GxW%c|{=2N@y{kW}1d+Dp');
define('LOGGED_IN_SALT',   'BMvsgAU&!)=+K)ag7XJ%lDfD,F-]*9di.tNo3zp=Lfj0-+N?~jNeO-K*-|*Q}(v6');
define('NONCE_SALT',       'tbcbJ>D*@P-z7mHAf)J74A!<#c6^N)XzaGO`65>%(>LfZ@H9,Ro*!rg+p8$bb>Lw');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'cl_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define( 'COOKIE_DOMAIN', 'www.classtune.dev' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

