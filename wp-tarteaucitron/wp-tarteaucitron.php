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

const WP_TARTEAUCITRON_PACKAGE_PATH = 'tarteaucitron.js/';

wp_tarteaucitron_setup();

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_setup(): void {
	try {
		wp_tarteaucitron_wordpress_absolute_path_available();
		wp_tarteaucitron_require_once();
	} catch ( Exception $exception ) {
		exit( $exception->getMessage() );
	}
	wp_tarteaucitron_actions();
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
        $exception = new Exception( 'WordPress unavailable. Plugin not loaded.' );
		error_log( $exception->getMessage() );
		throw $exception;
    }
}

function wp_tarteaucitron_require_once(): void {
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-wp-tarteaucitron-options.php';
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_actions(): void {
	try {
		add_action( 'plugins_loaded', 'wp_tarteaucitron_options_init', 10, 0 );
		add_action( 'wp_enqueue_scripts', 'wp_tarteaucitron_scripts', 10, 0 );
		add_action( 'wp_enqueue_scripts', 'wp_tarteaucitron_check_scripts_enqueued', 99, 0 );
	} catch ( Exception $exception ) {
		error_log( 'WP-tarteaucitron actions error' );
	}
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_scripts(): void {
	try {
		$tarteaucitron_version = wp_tarteaucitron_script_version();
	} catch ( Exception ) {
		error_log( 'WP-tarteaucitron script version error. Use default version.' );
		$tarteaucitron_version = false;
	}
	wp_enqueue_script( 'tarteaucitron_js', plugins_url( WP_TARTEAUCITRON_PACKAGE_PATH . 'tarteaucitron.js', __FILE__ ), array(), $tarteaucitron_version );
	wp_enqueue_script( 'tarteaucitron_script_js', plugins_url( 'tarteaucitron-script.js', __FILE__ ) );
}

/**
 * @since 1.0.0
 *
 * @throws Exception
 *
 * @return string
 */
function wp_tarteaucitron_script_version(): string {
    $tarteaucitron_package_json_path = trailingslashit( dirname(__FILE__) ) . WP_TARTEAUCITRON_PACKAGE_PATH . 'package.json';
    if( file_exists( $tarteaucitron_package_json_path ) ) {
        $tarteaucitron_package_json = file_get_contents( $tarteaucitron_package_json_path );
        $decoded_tarteaucitron_package_json = json_decode( $tarteaucitron_package_json, false );
		if( $decoded_tarteaucitron_package_json == null ) {
			$exception = new Exception( 'cannot decode tarteaucitron package json');
			error_log( $exception->getMessage() );
			throw $exception;
		} else {
			if( property_exists( $decoded_tarteaucitron_package_json, 'version' ) ) {
				$tarteaucitron_version = $decoded_tarteaucitron_package_json->version;
				trigger_error( 'tarteaucitron v' . $tarteaucitron_version, E_USER_NOTICE );
				return $tarteaucitron_version;
			} else {
				$exception = new Exception( 'tarteaucitron package version not found' );
				error_log( $exception->getMessage() );
				throw $exception;
			}
		}
    } else {
	    $exception = new Exception( $tarteaucitron_package_json_path . ' not found' );
		error_log( $exception->getMessage() );
		throw $exception;
    }
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_options_init(): void {
	$wp_tarteaucitron_options = new WP_tateaucitron_Options();
	$wp_tarteaucitron_options->setup();
}

/**
 * @since 1.0.0
 *
 * @throws Exception
 *
 * @return void
 */
function wp_tarteaucitron_check_scripts_enqueued(): void {
	$scripts = array(
		'tarteaucitron_js' => 'tarteaucitron_js',
		'tarteaucitron_script_js' => 'tarteaucitron_script_js'
	);
	foreach( $scripts as $script ) {
		if( wp_script_is( $script ) ) {
			trigger_error( $script . ' is enqueued', E_USER_NOTICE );
		} else {
			$exception = new Exception( $script . ' is not enqueued. ' );
			error_log( $exception->getMessage() );
			throw $exception;
		}
	}
}

?>