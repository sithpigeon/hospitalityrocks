<?php
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
define('DB_NAME', 'db369965973');

/** MySQL database username */
define('DB_USER', 'dbo369965973');

/** MySQL database password */
define('DB_PASSWORD', 'twisterzoe2');

/** MySQL hostname */
define('DB_HOST', 'db369965973.db.1and1.com');

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
define('AUTH_KEY','ElfQq[XmfQgr^b%p@#me{n|ZHwT8G<q=&nE/+Y%R5)TJM3FS+tdT&fLe>_|Jx+Cz');
define('SECURE_AUTH_KEY','$)J8pnch!I)_|JSxNAjo0e~&/*pOu(%6:3T|xn=+blY-e}E_j(`E0<[xRgI^$^e-');
define('LOGGED_IN_KEY',':b:]pOEt(.>c`0D^z8NKV/;T6NjzJ-kD<k)=j_v;g`zlwSA%~wlH+imjz{ |$Nf8');
define('NONCE_KEY','a+y#qv0Zu_V+Ou79C@5zIKZyJ4v[H-:]+YdQ%T@A{Qn,h-Q &gbDjdlEI(UwxT9T');
define('AUTH_SALT','+9JQ0I*oU&%8|X03I=k%KRnK={+4$:;n2iUk_#@6?+}mA}`^2{4)h--nToI-U0gz');
define('SECURE_AUTH_SALT','#b+E|vi<= BLM g:=I+S`O8U*AE@ys4T56JR-,3 yBv!&A>L1oz`-_a#NyK*|1|)');
define('LOGGED_IN_SALT','sQU|G&+@H+| xuH#;`q}o=kIOP^Nu5ORTk-6$_]-^(3-duMbbl-c8F@vRGVVkTTy');
define('NONCE_SALT','wa>A-6F5X3!Tv(pMnN{D_m*_w>m) j2~B;Vsdm4pEX(cR:>Sc=ozK?QL:C|*Wn)i');

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

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
