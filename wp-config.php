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

// Get Real IP's instead of loadbalancer ip
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $xffaddrs = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
    $_SERVER['REMOTE_ADDR'] = $xffaddrs[0];
}
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv('MYSQL_DB') );

/** MySQL database username */
define('DB_USER', getenv('MYSQL_USER') );

/** MySQL database password */
define('DB_PASSWORD', getenv('MYSQL_PASS') );

/** MySQL hostname */
define('DB_HOST', getenv('MYSQL_HOST') );

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

//Disable internal Wp-Cron function
define('DISABLE_WP_CRON', true);
//Setup a cronjob on server to refresh cron on a schedule instead of by page load

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         getenv('WP_AUTH_KEY') );
define('SECURE_AUTH_KEY',  getenv('WP_SECURE_AUTH_KEY') );
define('LOGGED_IN_KEY',    getenv('WP_LOGGED_IN_KEY') );
define('NONCE_KEY',        getenv('WP_NONCE_KEY') );
define('AUTH_SALT',        getenv('WP_AUTH_SALT') );
define('SECURE_AUTH_SALT', getenv('WP_SECURE_AUTH_SALT') );
define('LOGGED_IN_SALT',   getenv('WP_LOGGED_IN_SALT') );
define('NONCE_SALT',       getenv('WP_NONCE_SALT') );

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
