<?php /** @noinspection PhpRedundantClosingTagInspection */

/**
 * @since 1.0.0
 */
class WP_tarteaucitron_Options {

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
		if ( current_user_can( 'manage_options' ) ) {
			?>
			<div class="wrap">
				<div class="">
					<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>
					<?php $this->setup_settings_form() ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="wrap">
				<div class="">
					<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>
					<h3><?php _e( 'You are not authorised to manage these settings. Please contact your WordPress administrator.', 'wpforms-cpt' ) ?></h3>
				</div>
			</div>
			<?php
		}
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
		$this->setup_privacy_url_setting();
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
	 * @return void
	 */
	protected function setup_privacy_url_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_privacy_url_setting_input' ),
			'default' => ''
		);
		register_setting(
			'wp_tarteaucitron_options',
			'wp_tarteaucitron_privacy_url',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_privacy_url_field',
			__( 'WP-tarteaucitron privacy URL', 'wp-tarteaucitron' ), array( &$this, 'privacy_url_field_callback' ),
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
	public function sanitize_privacy_url_setting_input( $input ): string {
		return $input;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function privacy_url_field_callback(): void {
        echo '<input type="url" id="wp_tarteaucitron_privacy_url" name="wp_tarteaucitron_privacy_url" value=" ' . $this->get_privacy_url() . ' " placeholder=" ' . site_url() . ' " pattern="https?://.+" required/>';
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_privacy_url(): string {
		return get_option( 'wp_tarteaucitron_privacy_url' );
	}

	public function plugin_settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=wp-tarteaucitron' ) . '">' . __('Settings') . '</a>';
		return $links;
	}

}

?>