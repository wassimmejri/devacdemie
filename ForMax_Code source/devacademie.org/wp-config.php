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

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sc3ighb1166_wpdevac' );
define( 'DB_USER', 'root' );  // correspond à docker-compose (root)
define( 'DB_PASSWORD', 'rootpass' );  // mot de passe MySQL dans docker-compose
define( 'DB_HOST', 'db' );  // nom du service MySQL dans docker-compose

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
define( 'AUTH_KEY',         'mbshzvamyjj2w9mtjyquah2qdhjogqkyprypdroxfhewi19scfgmkeerwzmrq1tp' );
define( 'SECURE_AUTH_KEY',  'wcetvoa55qwspzct3dfnfgjmthofviihm8rjo6kw0cdu3ix6h4dfkrvcbch3vwf0' );
define( 'LOGGED_IN_KEY',    'fzuocdxnxmhbef4kldrz1shxtm7nxilpjnwkohhj9wkvniyeznlt7dkjngmvo9s0' );
define( 'NONCE_KEY',        'jp3ucdk1jkg3xyidmqmxgfb0l8jeeggpahhi8oqrwva8eo4m08m7yqv4dr4dqprr' );
define( 'AUTH_SALT',        'gxqa6ud2f3qsj6jyep4ocx8ylbgyxsy5cbh7ha4bzpqxmaojjprlvolzkvwbgwta' );
define( 'SECURE_AUTH_SALT', 'icfn7dunm9xywhs9jexxbewyljrcbwtmxqyyaymaq2nrzaydfznr5d657hr4zujj' );
define( 'LOGGED_IN_SALT',   'qqwusr3cn8z0asbyhvvhonlxghsqldkm2dyyompdpaz842fyaza6ol6jo1qydlop' );
define( 'NONCE_SALT',       'ldxs44nf9twa6owlb1lavh1ma2i1q7ktytdrubgenzc0bokt0mabhveki8km10v6' );

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
$table_prefix = 'wpb4_';

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

/* Add any custom values between this line and the "stop editing" line. */

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

define('WP_HOME', 'http://192.168.0.56:8085');
define('WP_SITEURL', 'http://192.168.0.56:8085');


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
