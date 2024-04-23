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

wp_tarteaucitron_uninstall();

/**
 * @since 1.0.0
 *
 * @return void
 */
function wp_tarteaucitron_uninstall(): void {
	try {
		wordpress_plugin_uninstall_called();
		if(get_option('wp_tarteaucitron_remove_options')){
			delete_options();
			$exception = new Exception( 'All options have been removed' );
			throw $exception;
		}
		else{
			$exception = new Exception( 'Options have not been removed' );
			throw $exception;
		}
	} catch( Exception $exception ) {
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
function wordpress_plugin_uninstall_called(): bool {
	if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		return true;
	} else {
		$exception = new Exception( 'Improper plugin uninstall call' );
		error_log( $exception->getMessage() );
		throw $exception;
	}
}

/**
 * @since 1.0.0
 *
 * @throws Exception
 *
 * @return void
 */
function delete_options(): void {
	delete_option( 'wp_tarteaucitron_privacy_policy_url' );
	delete_option( 'wp_tarteaucitron_use_wp_privacy_policy_page' );
	delete_option( 'wp_tarteaucitron_remove_credit' );
	delete_option( 'wp_tarteaucitron_icon_position' );
	delete_option( 'wp_tarteaucitron_cookie_name' );
	delete_option( 'wp_tarteaucitron_hashtag' );
	delete_option( 'wp_tarteaucitron_remove_options' );
}

?>