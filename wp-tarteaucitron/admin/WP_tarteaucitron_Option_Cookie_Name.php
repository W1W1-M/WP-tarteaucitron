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
class WP_tarteaucitron_Option_Cookie_Name implements WP_tarteaucitron_Option {

	/**
	 * @inheritDoc
	 */
	public function setup_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_setting_input' ),
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_cookie_name',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_cookie_name_field',
			__( 'Customize cookie name', 'wp-tarteaucitron' ), array( &$this,
			'setting_field_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_setting_input( $input ): string {
		$sanitized_input = preg_replace('/[^A-Za-z0-9\-]/', '', sanitize_text_field( $input ) );
		return ( $sanitized_input == '' ) ? 'tarteaucitron' : $sanitized_input;
	}

	/**
	 * @inheritDoc
	 */
	public function setting_field_callback(): void {
		get_option( 'wp_tarteaucitron_cookie_name' );
		$html = '<p>';
		$html .= '<input type="text" id="wp_tarteaucitron_cookie_name" name="wp_tarteaucitron_cookie_name" value="';
		$html .= WP_tarteaucitron_Option_Cookie_Name::get_option_value();
		$html .= '"';
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @inheritDoc
	 */
	public static function get_option_value(): string {
		return get_option( 'wp_tarteaucitron_cookie_name' ) ?: 'tarteaucitron';
	}
}