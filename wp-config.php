<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cl_ecommerce_1' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         'vV1rM[[zvoF}zMvl_ 6?]2ltXEcD0? HdrRum2;;@]+Aj+iV0^y8v)-=Z,!TJG>*' );
define( 'SECURE_AUTH_KEY',  'a ,(=gKl(>}!<Cqn,%_/!x>GI:%l.qpV>)y>`zsr6h+)P;^>Wn`3>SYA.%#3`6)%' );
define( 'LOGGED_IN_KEY',    '~Oh(-OmV0CsEIa^UEI}U@o0$mWkg]S7PdXY3<V?Z}}mJ:Jv#&a5p=!`,l7:S19h8' );
define( 'NONCE_KEY',        'S_> w&{=*mjD,vea_MxlA4<`bKvC KS0lb#Cd*Zq~M.hTPHE*$s}GA1D;(? >w~y' );
define( 'AUTH_SALT',        'K;I9Y|$#)BCY9zTRDv~.Gc#{ ti#HeHOWt,K|Krw9R)Ydw/Q[W{Mgp5#ZJ=Npk%`' );
define( 'SECURE_AUTH_SALT', 'x15$gz`4M.tQ{#Xa4Ed<%b*UmXm(w(}rg~qpqx{8`98qv3xYumkL^<^*m6gHv6A2' );
define( 'LOGGED_IN_SALT',   'gN~bEF<9?Bw#{7$7av[2F &b0OVo$ugSN/%bxrm!v;,q)aS@heTwB99EyN 0u.+@' );
define( 'NONCE_SALT',       '!8GQAk{5.4pnQu$A2!^lqD+mzL <kBv[!-j6W_lTz`;A<kSnSA|VhgV/ }/LRB{!' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpex_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
