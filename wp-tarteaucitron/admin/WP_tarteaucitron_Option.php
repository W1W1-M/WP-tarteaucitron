<?php
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
 * @since 1.10.0
 */
interface WP_tarteaucitron_Option {

	/**
	 * @since 1.10.0
	 *
	 * @return void
	 */
	public function setup_setting(): void;

	/**
	 * @since 1.10.0
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function sanitize_setting_input( $input ): mixed;

	/**
	 * @since 1.10.0
	 *
	 * @return void
	 */
	public function setting_field_callback(): void;

	/**
	 * @since 1.10.0
	 *
	 * @return mixed
	 */
	public static function get_option_value(): mixed;
}