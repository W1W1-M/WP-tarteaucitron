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
class WP_tarteaucitron_Option_Privacy_Policy_URL implements WP_tarteaucitron_Option {

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
			'wp_tarteaucitron_privacy_policy_url',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_privacy_policy_url_field',
			__( 'Custom privacy policy URL', 'wp-tarteaucitron' ), array( &$this, 'setting_field_callback' ),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize_setting_input( $input ): mixed {
		if( $input === NULL ) {
			return '';
		} else {
			return sanitize_text_field( $input );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function setting_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_privacy_policy_url" hidden>wp_tarteaucitron_privacy_policy_url</label>';
		$html .= '<p><input size="50" type="url" id="wp_tarteaucitron_privacy_policy_url" name="wp_tarteaucitron_privacy_policy_url"';
		$html .= ' value="' . esc_attr( WP_tarteaucitron_Option_Privacy_Policy_URL::get_option_value() ) . '"';
		$html .= ' placeholder=" ' . site_url() . ' " pattern="https?://.+"';
		if( WP_tarteaucitron_Option_Use_WP_Privacy_Policy_Page::get_option_value() ) {
			$html .= ' disabled';
		}
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @inheritDoc
	 */
	public static function get_option_value(): mixed {
		$default_privacy_policy_url = site_url();
		if( WP_tarteaucitron_Option_Use_WP_Privacy_Policy_Page::get_option_value() ) {
			$wp_privacy_policy_url = get_privacy_policy_url();
			if( empty( $wp_privacy_policy_url ) ) {
				trigger_error( 'WordPress privacy policy page not set' );
				return $default_privacy_policy_url;
			} else {
				return $wp_privacy_policy_url;
			}
		} else {
			$tarteaucitron_privacy_policy_url = get_option( 'wp_tarteaucitron_privacy_policy_url' );
			if( empty( $tarteaucitron_privacy_policy_url ) ) {
				trigger_error( 'tarteaucitron privacy policy URL not set' );
				return $default_privacy_policy_url;
			} else {
				return $tarteaucitron_privacy_policy_url;
			}
		}
	}
}