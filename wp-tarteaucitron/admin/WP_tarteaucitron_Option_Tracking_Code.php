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
class WP_tarteaucitron_Option_Tracking_Code implements WP_tarteaucitron_Option {

	/**
	 * @inheritDoc
	 */
	public function setup_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_setting_input' ),
			'default' => 'false'
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_tracking_code',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_tracking_code_field',
			__( 'Tracking code', 'wp-tarteaucitron' ), array( &$this,
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
		return sanitize_textarea_field( $input );
	}

	/**
	 * @inheritDoc
	 */
	public function setting_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_tracking_code" hidden>wp_tarteaucitron_tracking_code</label>';
		$html .= '<textarea id="wp_tarteaucitron_tracking_code" name="wp_tarteaucitron_tracking_code" rows="10" cols="100">';
		$html .= esc_textarea( WP_tarteaucitron_Option_Tracking_Code::get_option_value() );
		$html .= '</textarea></p>';
		echo $html;
	}

	/**
	 * @inheritDoc
	 */
	public static function get_option_value(): mixed {
		return get_option( 'wp_tarteaucitron_tracking_code' );
	}
}