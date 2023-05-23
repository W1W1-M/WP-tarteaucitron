<?php /** @noinspection PhpRedundantClosingTagInspection */

/**
 * WP-tarteaucitron
 *
 * @package         WP-tarteaucitron
 * @version         1.0.0
 * @author          William Mead - Manche Numérique
 * @copyright       2023 Manche Numérique
 * @license         GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:          WP-tarteaucitron
 * Plugin URI:           https://git.manche.io/si/web/wptarteaucitron
 * Description:          Plugin to manage cookies with tarteaucitron.js
 * Version:              1.0.0
 * Requires at least:    5.9.5
 * Requires PHP:         7.4.33
 * Author:               William Mead - Manche Numérique
 * Author URI:           https://www.manchenumerique.fr
 * License:              GNU GPLv3
 * License URI:          https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:          wp-tarteaucitron
 * Domain Path:          /languages
 */

const WP_TARTEAUCITRON_PACKAGE_FOLDER = 'tarteaucitron.js/';

wp_tarteaucitron_setup();

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_setup(): void {
	try {
		wp_tarteaucitron_wordpress_absolute_path_available();
		wp_tarteaucitron_actions();
	} catch ( Exception $exception ) {
		exit( $exception->getMessage() );
	}
}

/**
 * @since 1.0.0
 *
 * @throws Exception
 *
 * @return bool
 */
function wp_tarteaucitron_wordpress_absolute_path_available(): bool {
    if( defined( 'ABSPATH' )) {
        return true;
    } else {
        throw new Exception( 'WordPress unavailable. Plugin not loaded.' );
    }
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_actions(): void {
    add_action( 'wp_enqueue_scripts', 'wp_tarteaucitron_scripts', 10, 0 );
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_scripts(): void {
	try {
		$tarteaucitron_version = wp_tarteaucitron_script_version();
	} catch ( Exception $exception ) {
		echo $exception->getMessage();
		$tarteaucitron_version = false;
	}
	wp_enqueue_script( 'tarteaucitron_js', plugins_url( WP_TARTEAUCITRON_PACKAGE_FOLDER . 'tarteaucitron.js' ), array(), $tarteaucitron_version );
}

/**
 * @since 1.0.0
 *
 * @throws Exception
 *
 * @return string
 */
function wp_tarteaucitron_script_version(): string {
    $tarteaucitron_package_json_path = plugin_dir_path( '__FILE__' ) . WP_TARTEAUCITRON_PACKAGE_FOLDER . 'package.json';
    if( file_exists( $tarteaucitron_package_json_path ) ) {
        $tarteaucitron_package_json = file_get_contents( $tarteaucitron_package_json_path );
        $decoded_tarteaucitron_package_json = json_decode( $tarteaucitron_package_json, false );
        if ( property_exists( $decoded_tarteaucitron_package_json, 'version' ) ) {
            return $decoded_tarteaucitron_package_json->version;
        } else {
	        throw new Exception( 'tarteaucitron package version not found' );
        }
    } else {
	    throw new Exception( $tarteaucitron_package_json_path . ' not found' );
    }
}

?>