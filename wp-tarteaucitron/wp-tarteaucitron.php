<?php /** @noinspection PhpRedundantClosingTagInspection */
declare( strict_types=1 );

/*
 * This file is part of WP-tarteaucitron.
 *
 * WP-tarteaucitron is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * WP-tarteaucitron is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * WP-tarteaucitron
 *
 * @package         WP-tarteaucitron
 * @version         1.7.0
 * @author          William Mead & Clément Schneider @ Manche Numérique
 * @copyright       2024 - William Mead & Clément Schneider @ Manche Numérique
 * @license         GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:          WP-tarteaucitron
 * Plugin URI:           https://git.manche.io/wordpress/wptarteaucitron
 * Description:          Plugin to manage cookies with tarteaucitron.js
 * Version:              1.7.0
 * Requires at least:    5.9.5
 * Requires PHP:         8.0.28
 * Author:               William Mead & Clément Schneider @ Manche Numérique
 * Author URI:           https://www.manchenumerique.fr
 * License:              GNU GPLv3
 * License URI:          https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:          wp-tarteaucitron
 * Domain Path:          /lang
 */

const WP_TARTEAUCITRON_PLUGIN_FILE_PATH = __FILE__;
const WP_TARTEAUCITRON_PACKAGE_PATH = 'lib/tarteaucitron.js/';
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
		wp_tarteaucitron_require_once();
		$wp_tarteaucitron_setup = new WP_tarteaucitron_Setup();
		$wp_tarteaucitron_setup->init();
	} catch ( Exception $exception ) {
		exit( $exception->getMessage() );
	}
}

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_require_once(): void {
	$plugin_dir_path = plugin_dir_path( WP_TARTEAUCITRON_PLUGIN_FILE_PATH );
	require_once $plugin_dir_path . 'inc/WP_tarteaucitron_Setup.php';
	require_once $plugin_dir_path . 'admin/WP_tarteaucitron_Options.php';
}

?>