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
		( new WP_tarteaucitron_Option_Tracking_Code() )->setup_setting();
		( new WP_tarteaucitron_Option_Use_WP_Privacy_Policy_Page() )->setup_setting();
		( new WP_tarteaucitron_Option_Privacy_Policy_URL() )->setup_setting();
		( new WP_tarteaucitron_Option_Hashtag() )->setup_setting();
		( new WP_tarteaucitron_Option_Cookie_Name() )->setup_setting();
		( new WP_tarteaucitron_Option_Icon_Position() )->setup_setting();
		( new WP_tarteaucitron_Option_Remove_Credit() )->setup_setting();
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