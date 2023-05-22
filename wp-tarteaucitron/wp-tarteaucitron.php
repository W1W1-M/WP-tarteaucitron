<?php

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
 * Description:          Plugin to manage cookies with tarteaucitron js script
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


wp_tarteaucitron_setup();
/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_setup(): void {
    if( wp_tarteaucitron_wordpress_absolute_path_available()) {
        wp_tarteaucitron_actions();
    } else {
        exit( 'WordPress unavailable. Plugin not loaded.' );
    }
}

/**
 * @since 1.0.0
 *
 * @return bool
 */
function wp_tarteaucitron_wordpress_absolute_path_available(): bool {
    if( defined( 'ABSPATH' )) {
        return true;
    } else {
        return false;
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
    $tarteaucitron_version = wp_tarteaucitron_script_version();
    wp_enqueue_script( 'tarteaucitron_js', plugins_url( 'tarteaucitron/tarteaucitron.js' ), array(), $tarteaucitron_version, false );
}

/**
 * @since 1.0.0
 *
 * @return string|bool
 */
function wp_tarteaucitron_script_version(): string | bool {
    $tarteaucitron_package_json_path = 'tarteaucitron/package.json';
    if( file_exists( $tarteaucitron_package_json_path ) ) {
        $tarteaucitron_package_json = file_get_contents( $tarteaucitron_package_json_path );
        $decoded_tarteaucitron_package_json = json_decode( $tarteaucitron_package_json, false );
        if ( array_key_exists('version', $decoded_tarteaucitron_package_json ) ) {
            return $decoded_tarteaucitron_package_json->version;
        } else {
            echo 'tarteaucitron package version not found';
            return false;
        }

    } else {
        echo 'tarteaucitron/package.json not found';
        return false;
    }
}

?>