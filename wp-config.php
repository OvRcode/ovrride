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
define('DB_NAME', 'ovrride' );

/** MySQL database username */
define('DB_USER', 'root' );

/** MySQL database password */
define('DB_PASSWORD', 'root' );

/** MySQL hostname */
define('DB_HOST', 'localhost:8889' );

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
define('AUTH_KEY',         '.H*Sg8+8_F)pNryoah_%_grbC:NOyS%an_-WCfat=Y2E*v)6XA-[<m.a_Xi5xBv]');
define('SECURE_AUTH_KEY',  '8TAkM6i_M+?+*@&*{|x]jN~Xbg!%C,vbDKU%Vk3gdKe_a>%UmUTkBe9n-hhm)-do');
define('LOGGED_IN_KEY',    '-ES3.)ifQl8=6);C%a=(24-2G5E}cD<4Y8G:.HLyo5R6q+=L[[]bs~~6v5+|YTG0');
define('NONCE_KEY',        '-r<q.<Xg)#{|)lSxuUgf4rH|)Z5w#%$zW!-}G_<7/l&$= Z5)L3vA+Mez9lGRO_+');
define('AUTH_SALT',        '>p{{:]c?;ye8MWiyfoU^~1[e#~&2L{.]&r|^c;y7H}dCb+c$10-|+@^.-qdHka]_');
define('SECURE_AUTH_SALT', 'w)&1+>pJ(nn[{_^I%LC)uzbk+Exyk=g.A<PS 4hZ3`}!N=7of ayz0;O71,J|h~;');
define('LOGGED_IN_SALT',   'i3t(-S@-xyHy-#sDW_-dyy5,$t2tJoXvv+n^TS5[8@q.F5ql)@QV2aM-M<.WY)5:');
define('NONCE_SALT',       'D`PRmRjODw=J#?7TK;cg6jMyP:=G8-Q-pQ!TQdHjztg^nZ*t]iJSq-,{g+4|m|?v');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
	
define('WP_MEMORY_LIMIT', '128M');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
