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
define('DB_NAME', ( getenv('AMAZON_RDS_DB_NAME') ?: '***REMOVED***' ) );

/** MySQL database username */
define('DB_USER', ( getenv('AMAZON_RDS_USER') ?: '***REMOVED***' ) );

/** MySQL database password */
define('DB_PASSWORD', ( getenv('AMAZON_RDS_PASS') ?: '***REMOVED***' ) );

/** MySQL hostname */
define('DB_HOST', ( getenv('AMAZON_RDS_HOST') ?: 'localhost'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/* AWS KEYS */
define( 'AWS_ACCESS_KEY_ID', getenv('AMAZON_AWS_ACCESS_KEY') );
define( 'AWS_SECRET_ACCESS_KEY', getenv('AMAZON_AWS_SECRET_KEY'));

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '}(mm,}oIWC;Y~kCQ>ut{)-+h&w1]fJW )|)nrE]4bUxKckV7<FK|cV/DIjyRQD5r');
define('SECURE_AUTH_KEY',  ']im5h^3]Lj7y.lRnbsVct0^3HkQu,y+SbrzXAnQ(:x0. Qr=Muu6W#Dqy8;(uC9U');
define('LOGGED_IN_KEY',    'fbXsQVTi>9c[,!rO?qw|%*]~HNu(9S7){~ouE$4[w/c0G2]q^^I GrHnl)UvuE6#');
define('NONCE_KEY',        'Mx=G-gT?2j&$!mA^3PlR`PoD-5{3p V%hOK0^*gZ$#.^|c_&eBoVJa#zEbqZhYst');
define('AUTH_SALT',        '%L@(yW 214]}lPsg]/ie|;tIs2AkJ;$_JFA4q<{r@2-TKoVum`>1r8<@uOuofM.+');
define('SECURE_AUTH_SALT', 'n..@Z0r+@_O+l>zL^pe-]C++P1&P<hdg.b+F~*]+|7d!XB7G,a(=,-5_`Fs^adux');
define('LOGGED_IN_SALT',   '&ov&a6]Ddi;W_MHfFOl7Ca.>c]CWW;;&Q)iS|8y;EM>01VQ<<eGw3nbPej.k@!0j');
define('NONCE_SALT',       '_@x_gIX%`?:L;@4oa|?WTDV}neBx+-{NO8kdb%KOsv05=W2n+.3CVX;=+A1@#X@(');

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
