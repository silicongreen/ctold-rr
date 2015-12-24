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
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'fkapQBTMF+LMfd1^SiU1m8jF4ss]c}_Uw@Y[%dl4ag|[BP(MF&|V{e4!%YNsf+^M');
define('SECURE_AUTH_KEY',  '9/xy~cs>iS2LKgM2&cU~XNAuj;^SYI0/<Y%ik.|t4/e@?28*zE)j!9A.|%E|9Yq:');
define('LOGGED_IN_KEY',    'NWX`,-[4bSldkniG :-7yeBrVN+p&Se*t4{~K;KCwS.NkxN~+.~Oae#(!j+|e7Wa');
define('NONCE_KEY',        'uU~KWQSQAy!(uD?7_C+zHq&F1PeES&F>Je<z- u+HyPc&,=c:qqh,4[*$v:14zbi');
define('AUTH_SALT',        'f}:k7d$Dju%[[,Ls|?$f3m$+mor5K3prIYwVB-L5oK}_U^r&45/LOM#5r9I.4.ap');
define('SECURE_AUTH_SALT', 'E2]AUrBsr]m?v-)<7**O^ByF^F5)1]VBLOw%96r,Rmpa/k<y(6KP=a#{zU+XPP+]');
define('LOGGED_IN_SALT',   '1B3+(ZKe{+V/lG<Q|b/!,IfK=+Q<Lea() aq7Q:H{Wy:?-V+/uaTeJ--GSQcwiz:');
define('NONCE_SALT',       'c61t|hgvHtP>t!8Q3cwP;ujX-q%XhSD:qR(*ddZV!6!zX$v+S!/y!?G}Vi_#)]92');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
