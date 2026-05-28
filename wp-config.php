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
define( 'DB_NAME', 'hotelcosmopolitan' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '7+|zB}AO*<hk])h!tR)l^z%D|Z^6%PDxdH+%|u=-DhRf}X]v^V.SZNmSkkyx ~6/' );
define( 'SECURE_AUTH_KEY',  'CTL *WB(|t-tIYe(^tz+Xp`p`>PV$d+1YDjM]Zy0=NRLE^|8kSr+u/f%TN~>t(PO' );
define( 'LOGGED_IN_KEY',    'Ue-2xwNAL#oq!H0k%eV%$&&=&.N,;Ga35d>4pyu4?5O#>l@L?CBSm2r*`FG.$vv6' );
define( 'NONCE_KEY',        ')>VQT5>0;7UOiJy=LR6y=U]O4c7#hcP]([dGh&Z[?GWYNH7=FB@ay*s:TA(&FP$R' );
define( 'AUTH_SALT',        './6jSUdvSi|Ur~$S-A$ZUEm#vjYIfv[aODJiqS6F*vD5&)f#HuIDspOJ,@.-PY)^' );
define( 'SECURE_AUTH_SALT', '=8m >wT]46[x tyVXNYLqWR,jC+7:Xz0LtGIdQqK.+N=!;zajEFv|s%yBAFRnUlZ' );
define( 'LOGGED_IN_SALT',   '}[n%u,j%?<>QKIXPWO_Uy@KXGIh2cTj{f8TCY8ofj00s#&KD6r6JzO@9f`3hl)<W' );
define( 'NONCE_SALT',       'Rfp<FvFsnc)|EuTi@]&TX|lANS|SAMLIwW@@:MHvhWf``2omi-DAr+#2^TQ[aa O' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
