<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache



/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', ( getenv('OPENSHIFT_APP_NAME') ?: '***REMOVED***' ) );

/** MySQL database username */
define('DB_USER', ( getenv('OPENSHIFT_MYSQL_DB_USERNAME') ?: '***REMOVED***' ) );

/** MySQL database password */
define('DB_PASSWORD', ( getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ?: '***REMOVED***' ) );

/** MySQL hostname */
if ( getenv('OPENSHIFT_MYSQL_DB_HOST') )
  define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST') . ":" . getenv('OPENSHIFT_MYSQL_DB_PORT') );
else
  define('DB_HOST', '***REMOVED***');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '***REMOVED***');
define('SECURE_AUTH_KEY',  '***REMOVED***');
define('LOGGED_IN_KEY',    '***REMOVED***');
define('NONCE_KEY',        '***REMOVED***');
define('AUTH_SALT',        '***REMOVED***');
define('SECURE_AUTH_SALT', '***REMOVED***');
define('LOGGED_IN_SALT',   '***REMOVED***');
define('NONCE_SALT',       '***REMOVED***');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

// Force Login page and Admin Dashboard to require SSL
define('FORCE_SSL_LOGIN', true);
define('FORCE_SSL_ADMIN', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
define('WP_MEMORY_LIMIT', '128M');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
