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
define('DB_NAME', 'champs21_classtune_wp');

/** MySQL database username */
define('DB_USER', 'champs21_champ');

/** MySQL database password */
define('DB_PASSWORD', '1_84T~vADp2$');

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

define('AUTH_KEY',         'Y_`V`IlOlDLP$UaQ6a)DN;6itgrNV5@8U+4U&6ThL!{vLDo3uLMBPvIGHfSXn9VS');
define('SECURE_AUTH_KEY',  'RJ@f)a%C%Vsg!7.q^}EBg?R?Ms9qg}]]|Yvmi8<(7x@:?Ks-GO>8S$|+7g&BDR#J');
define('LOGGED_IN_KEY',    'mNUpwVK+V78)L=MC=a<Rw)ACM+([Ul+l( }F`&| :r||V@)y&1GEX8{AT;CerkrS');
define('NONCE_KEY',        'k{iHa&%Plb2*+, ns8?E---#gAEr)(y1 3_JdwC}Gjnf(-}{z-,T+p^?WU,7k5CJ');
define('AUTH_SALT',        'm}pZ# u4DMJ+o68T5k@t#w=#o}<@q#/&C<hK;%_i+y2wKxz<WLaq]b$:1<<BWIu$');
define('SECURE_AUTH_SALT', 'U(!%},MkxxjZnr?===vRbspeIGwg3iI>xmM~v&^8fr+M^/=l=3%fh-MB|NgaeC}7');
define('LOGGED_IN_SALT',   'e1~;m$7|05KF4yl%>9v~++3zcQk)8{hTn3K^p#,D.-u<N0|u-$OooG4!zD$6>LQ[');
define('NONCE_SALT',       'GFvf@|OA`Y;Xz%uAmp[0Z;qS%GQYJ]-|z3]WCx4]Ax4G.O@L5}v;-],7r3R.kpr)');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'tds_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
