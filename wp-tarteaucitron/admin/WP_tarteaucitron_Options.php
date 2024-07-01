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
 * @since 1.0.0
 */
class WP_tarteaucitron_Options {

	/**
	 * @since 1.5.0
	 */
	protected string $wp_tarteaucitron_script_version;

	/**
	 * @since 1.0.0
	 */
	public function __construct(string $tarteaucitron_script_version) {
		$this->wp_tarteaucitron_script_version = $tarteaucitron_script_version;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', array($this, 'setup_submenu_with_page' ) );
		add_action( 'admin_init', array(&$this, 'setup_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(WP_TARTEAUCITRON_PLUGIN_FILE_PATH), array( $this, 'plugin_settings_link') );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_submenu_with_page(): void {
		add_submenu_page(
			'options-general.php',
			'WP-tarteaucitron',
			'WP-tarteaucitron',
			'manage_options',
			'wp-tarteaucitron',
			array( $this, 'setup_page' )
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_page(): void {
		echo '<div class="wrap"><div class=""><h1>' . get_admin_page_title() . '</h1>';
		echo '<h4>tarteaucitron.js version : ' . $this->wp_tarteaucitron_script_version . '</h4>';
		if ( current_user_can( 'manage_options' ) ) {
			$this->setup_settings_form();
		} else {
			echo '<h3>' . _e( 'You are not authorised to manage these settings. Please contact your WordPress administrator.', 'wpforms-cpt' ) . '</h3>';
		}
		echo '</div></div>';
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 * @noinspection HtmlUnknownTarget*
	 */
	protected function setup_settings_form(): void {
		echo '<form action="options.php" method="post">';
		settings_fields( 'wp_tarteaucitron_options' );
		do_settings_sections( 'wp-tarteaucitron' );
		submit_button();
		echo '</form>';
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_settings(): void {
		$this->setup_settings_section();
		$this->setup_tracking_code_setting();
		$this->setup_use_wp_privacy_policy_page_setting();
		$this->setup_privacy_policy_url_setting();
		$this->setup_hashtag_setting();
		$this->setup_cookie_name_setting();
		$this->setup_icon_position_setting();
		$this->setup_remove_credit_setting();
		$this->setup_remove_options_setting();
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup_settings_section(): void {
		add_settings_section(
			'wp_tarteaucitron_settings_section',
			__( 'Settings', 'wp-tarteaucitron' ), array( &$this, 'settings_section_callback' ),
			'wp-tarteaucitron'
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings_section_callback(): void {
		echo '<!-- Settings section -->';
	}

	/**
	 * @since 1.9.0
	 *
	 * @return void
	 */
	protected function setup_tracking_code_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_tracking_code_setting_input' ),
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
			'tracking_code_field_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.9.0
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_tracking_code_setting_input( $input ): string {
		return $input;
	}

	/**
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function tracking_code_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_tracking_code" hidden>wp_tarteaucitron_tracking_code</label>';
		$html .= '<textarea id="wp_tarteaucitron_tracking_code" name="wp_tarteaucitron_tracking_code" rows="10" cols="100">';
		$html .= esc_textarea( $this->get_option_tracking_code() );
		$html .= '</textarea></p>';
		echo $html;
	}

	/**
	 * @since 1.9.0
	 *
	 * @return mixed
	 */
	public function get_option_tracking_code(): mixed {
		return get_option( 'wp_tarteaucitron_tracking_code' );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup_use_wp_privacy_policy_page_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_use_wp_privacy_policy_page_setting_input' ),
			'default' => 'false'
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_use_wp_privacy_policy_page',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_use_wp_privacy_policy_page_field',
			__( 'Use privacy policy page defined in WordPress', 'wp-tarteaucitron' ), array( &$this,
			'use_wp_privacy_policy_page_field_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function sanitize_use_wp_privacy_policy_page_setting_input( $input ): bool {
		if( $input == 'on' ) {
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
	public function use_wp_privacy_policy_page_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_use_wp_privacy_policy_page" hidden>wp_tarteaucitron_use_wp_privacy_policy_page</label>';
		$html .= '<input type="checkbox" id="wp_tarteaucitron_use_wp_privacy_policy_page" name="wp_tarteaucitron_use_wp_privacy_policy_page"';
		if( $this->get_option_use_wp_privacy_policy_page() ) {
			$html .= 'value="on" checked';
		}
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	protected function get_option_use_wp_privacy_policy_page(): mixed {
		return get_option( 'wp_tarteaucitron_use_wp_privacy_policy_page' );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup_privacy_policy_url_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_privacy_policy_url_setting_input' ),
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_privacy_policy_url',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_privacy_policy_url_field',
			__( 'Custom privacy policy URL', 'wp-tarteaucitron' ), array( &$this, 'privacy_policy_url_field_callback' ),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_privacy_policy_url_setting_input( $input ): string {
		if( $input === NULL ) {
			return '';
		} else {
			return $input;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function privacy_policy_url_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_privacy_policy_url" hidden>wp_tarteaucitron_privacy_policy_url</label>';
		$html .= '<p><input size="50" type="url" id="wp_tarteaucitron_privacy_policy_url" name="wp_tarteaucitron_privacy_policy_url"';
		$html .= ' value="' . esc_attr( $this->get_option_wp_tarteaucitron_privacy_policy_url() ) . '"';
		$html .= ' placeholder=" ' . site_url() . ' " pattern="https?://.+"';
		if( $this->get_option_use_wp_privacy_policy_page() ) {
			$html .= ' disabled';
		}
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	protected function get_option_wp_tarteaucitron_privacy_policy_url(): mixed {
		return get_option( 'wp_tarteaucitron_privacy_policy_url' );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function plugin_settings_link( $links ): array {
		$plugin_setting_link[] = '<a href="' . admin_url( 'options-general.php?page=wp-tarteaucitron' ) . '">' . __('Settings') . '</a>';
		return array_merge( $links, $plugin_setting_link );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_tatrteaucitron_privacy_policy_url(): string {
		$default_privacy_policy_url = site_url();
		if( $this->get_option_use_wp_privacy_policy_page() ) {
			$wp_privacy_policy_url = get_privacy_policy_url();
			if( empty( $wp_privacy_policy_url ) ) {
				trigger_error( 'WordPress privacy policy page not set' );
				return $default_privacy_policy_url;
			} else {
				return $wp_privacy_policy_url;
			}
		} else {
			$tarteaucitron_privacy_policy_url = $this->get_option_wp_tarteaucitron_privacy_policy_url();
			if( empty( $tarteaucitron_privacy_policy_url ) ) {
				trigger_error( 'tarteaucitron privacy policy URL not set' );
				return $default_privacy_policy_url;
			} else {
				return $tarteaucitron_privacy_policy_url;
			}
		}
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	protected function setup_hashtag_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_hashtag_input' ),
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_hashtag',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_hashtag_field',
			__( 'Customize the hashtag', 'wp-tarteaucitron' ), array( &$this,
			'use_wp_hashtag_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.7.0
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_hashtag_input( $input ): string {
		$sanitized_input = $this->wp_replace($input);
		return ( $sanitized_input == '' ) ? '#tarteaucitron' : '#' . $sanitized_input;
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function use_wp_hashtag_callback(): void {
		get_option('wp_tarteaucitron_hashtag');
		$html = '<p>';
		$html .= '<input type="text" id="wp_tarteaucitron_hashtag" name="wp_tarteaucitron_hashtag" value="';
		$html .= get_option('wp_tarteaucitron_hashtag');
		$html .= '"';
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	protected function setup_cookie_name_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_cookie_name_input' ),
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
			'use_wp_cookie_name_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.7.0
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function sanitize_cookie_name_input( $input ): string {
		$sanitized_input = $this->wp_replace($input);
		return ($sanitized_input == '') ? 'tarteaucitron' : $sanitized_input;
	}


	/**
	 * @since 1.7.0
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function wp_replace( $input ): string {
		return preg_replace('/[^A-Za-z0-9\-]/', '', $input);
	}


	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function use_wp_cookie_name_callback(): void {
		get_option( 'wp_tarteaucitron_cookie_name' );
		$html = '<p>';
		$html .= '<input type="text" id="wp_tarteaucitron_cookie_name" name="wp_tarteaucitron_cookie_name" value="';
		$html .= get_option( 'wp_tarteaucitron_cookie_name' );
		$html .= '"';
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup_icon_position_setting(): void {
		$form_id_setting_args = array(
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_icon_position',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_icon_position_field',
			__( 'Change icon position', 'wp-tarteaucitron' ), array( &$this,
			'use_wp_icon_position_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function use_wp_icon_position_callback(): void {
		$option = get_option('wp_tarteaucitron_icon_position');
		$html = '<p>';
		$html .= '<select id="wp_tarteaucitron_icon_position" name="wp_tarteaucitron_icon_position" />';
				$html .= '<option value="BottomRight"' . (($option == 'BottomRight') ? 'selected ' : '') . '> '. __( 'At the bottom right', 'wp-tarteaucitron' ) . '</option>';
				$html .= '<option value="BottomLeft"' . (($option == 'BottomLeft') ? 'selected ' : '') . '> '. __( 'At the bottom left', 'wp-tarteaucitron' ) . '</option>';
				$html .= '<option value="TopRight"' . (($option == 'TopRight') ? 'selected ' : '') . '> '. __( 'At the top right', 'wp-tarteaucitron' ) . '</option>';
				$html .= '<option value="TopLeft"' . (($option == 'TopLeft') ? 'selected ' : '') . '> '. __( 'At the top left', 'wp-tarteaucitron' ) . '</option>';
		$html .= '</select>';
		$html .= '</p>';
		echo $html;
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	protected function setup_remove_credit_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_checkbox_input' ),
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_remove_credit',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_remove_credit_field',
			__( 'Hide credits', 'wp-tarteaucitron' ), array( &$this,
			'use_wp_remove_credit_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.7.0
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public function sanitize_checkbox_input( $input ): bool {
		return $input == 'on';
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function use_wp_remove_credit_callback(): void {
		$html = '<p>';
		$html .= '<input type="checkbox" id="wp_tarteaucitron_remove_credit" name="wp_tarteaucitron_remove_credit"';
		if(get_option( 'wp_tarteaucitron_remove_credit' )){
			$html .= 'value="on" checked';
		}
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	protected function setup_remove_options_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_checkbox_input' ),
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_remove_options',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_remove_options_field',
			__( 'Remove options on uninstallation', 'wp-tarteaucitron' ), array( &$this,
			'use_wp_remove_options_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function use_wp_remove_options_callback(): void {
		$html = '<p>';
		$html .= '<input type="checkbox" id="wp_tarteaucitron_remove_options" name="wp_tarteaucitron_remove_options"';
		if(get_option( 'wp_tarteaucitron_remove_options' )){
			$html .= 'value="on" checked';
		}
		$html .= '/></p>';
		echo $html;
	}

}

?>