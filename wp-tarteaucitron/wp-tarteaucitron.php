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
 * Domain Path:          /lang
 */

const WP_TARTEAUCITRON_PLUGIN_FILE_PATH = __FILE__;
const WP_TARTEAUCITRON_PACKAGE_PATH     = 'lib/tarteaucitron.js/';
const WP_TARTEAUCITRON_JS_FILE = 'tarteaucitron.js';
const WP_TARTEAUCITRON_SCRIPT_JS_FILE = 'tarteaucitron-script.js';
const WP_TATEAUCITRON_PACKAGE_JSON_FILE = 'package.json';

wp_tarteaucitron_setup();

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_setup(): void {
	try {
		spl_autoload_register( 'wp_tarteaucitron_autoload' );
		$wp_tarteaucitron_setup = new WP_tarteaucitron_Setup();
		$wp_tarteaucitron_setup->init();
	} catch ( Exception $exception ) {
		exit( $exception->getMessage() );
	}
}

/**
 * @since 1.0.0
 *
 * @param $class
 *
 * @return void
 */
function wp_tarteaucitron_autoload( $class ): void {
	$plugin_dir_path = plugin_dir_path( WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) . 'inc/';
	require_once $plugin_dir_path . $class . '.php';
}

?>