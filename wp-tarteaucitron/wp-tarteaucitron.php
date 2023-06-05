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
const PLUGIN_FILE_PATH = __FILE__;

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
	register_activation_hook( PLUGIN_FILE_PATH, 'wp_tarteaucitron_plugin_activate' );
	register_deactivation_hook( PLUGIN_FILE_PATH, 'wp_tarteaucitron_plugin_deactivate' );
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
	$plugin_dir_path = plugin_dir_path( __FILE__ );
	require_once $plugin_dir_path . 'admin/class-wp-tarteaucitron-setup.php';
	require_once $plugin_dir_path . 'admin/class-wp-tarteaucitron-options.php';
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_plugin_activate(): void {
	add_option('WP_tarteaucitron_just_activated',true );
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_plugin_deactivate(): void {

}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_actions(): void {
	try {
		add_action( 'admin_init', 'wp_tarteaucitron_just_activated_setup', 10, 0 );
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
	if( file_exists( trailingslashit( dirname(PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_PACKAGE_PATH . 'tarteaucitron.js' ) ) {
		wp_enqueue_script( 'tarteaucitron_js', plugins_url( WP_TARTEAUCITRON_PACKAGE_PATH . 'tarteaucitron.js', PLUGIN_FILE_PATH ), array(), $tarteaucitron_version );
	} else {
		$exception = new Exception( 'cannot find tarteaucitron.js');
		error_log( $exception->getMessage() );
		throw $exception;
	}
	if( file_exists( trailingslashit( dirname(PLUGIN_FILE_PATH ) ) . 'tarteaucitron-script.js' ) ) {
		wp_enqueue_script( 'tarteaucitron_script_js', plugins_url( 'tarteaucitron-script.js', PLUGIN_FILE_PATH ) );
	} else {
		$exception = new Exception( 'cannot find tarteaucitron-script.js');
		error_log( $exception->getMessage() );
		throw $exception;
	}

}

/**
 * @since 1.0.0
 *
 * @throws Exception
 *
 * @return string
 */
function wp_tarteaucitron_script_version(): string {
    $tarteaucitron_package_json_path = trailingslashit( dirname(PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_PACKAGE_PATH . 'package.json';
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

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_just_activated_setup(): void {
	if ( current_user_can( 'activate_plugins' && get_option('WP_tarteaucitron_just_activated') ) ) {
		delete_option( 'WP_tarteaucitron_just_activated' );
		setup_javascript_file();
	} else {
		trigger_error( 'User is not authorized to run activation setup', E_USER_NOTICE);
	}

}

/**
 * @since 1.0.0
 *
 * @return void
 */
function setup_javascript_file(): void {
	$privacy_url = get_option( 'wp_tarteaucitron_privacy_url' );
	if( ! $privacy_url ) {
		$privacy_url_parameter = site_url();
	} else {
		$privacy_url_parameter = $privacy_url;
	}
	$javascript = 'tarteaucitron.init({"privacyUrl": "' . $privacy_url_parameter . '"});';
	try {
		$javascript_file = fopen( trailingslashit( dirname(__FILE__) ) . 'tarteaucitron-script.js', 'w+' );
		fwrite( $javascript_file, $javascript);
		fclose($javascript_file);
		trigger_error( 'tarteaucitron js script created', E_USER_NOTICE);
	} catch ( Exception $exception ) {
		error_log( $exception->getMessage() );
	}
}

?>