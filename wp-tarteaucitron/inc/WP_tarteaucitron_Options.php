<?php /** @noinspection PhpRedundantClosingTagInspection */

/**
 * @since 1.0.0
 */
class WP_tarteaucitron_Options {

	/**
	 * @since 1.0.0
	 *
	 * @see $this->init()
	 */
	public function __construct() {
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'setup_submenu_with_page' ) );
		add_action( 'admin_init', array( &$this, 'setup_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( WP_TARTEAUCITRON_PLUGIN_FILE_PATH ), array(
			$this,
			'plugin_settings_link'
		) );
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
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
	 * @return void
	 * @since 1.0.0
	 *
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
	 * @return void
	 *
	 * @noinspection HtmlUnknownTarget*
	 * @since 1.0.0
	 *
	 */
	protected function setup_settings_form(): void {
		echo '<form action="options.php" method="post">';
		settings_fields( 'inc\WP_tarteaucitron_Options' );
		do_settings_sections( 'wp-tarteaucitron' );
		submit_button();
		echo '</form>';
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function setup_settings(): void {
		$this->setup_settings_section();
		$this->setup_use_wp_privacy_policy_page_setting();
		$this->setup_privacy_policy_url_setting();
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	protected function setup_settings_section(): void {
		add_settings_section(
			'wp_tarteaucitron_settings_section',
			__( 'Settings', 'wp-tarteaucitron' ), array( &$this, 'settings_section_callback' ),
			'wp-tarteaucitron'
		);
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function settings_section_callback(): void {
		echo '<!-- Settings section -->';
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	protected function setup_use_wp_privacy_policy_page_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_use_wp_privacy_policy_page_setting_input' ),
			'default'           => 'false'
		);
		register_setting(
			'inc\WP_tarteaucitron_Options',
			'wp_tarteaucitron_use_wp_privacy_policy_page',
			$form_id_setting_args
		);
		add_settings_field(
			'wp_tarteaucitron_use_wp_privacy_policy_page_field',
			__( 'Use privacy policy page defined in WordPress', 'wp-tarteaucitron' ), array(
			&$this,
			'use_wp_privacy_policy_page_field_callback'
		),
			'wp-tarteaucitron',
			'wp_tarteaucitron_settings_section'
		);
	}

	/**
	 * @param $input
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function sanitize_use_wp_privacy_policy_page_setting_input( $input ): bool {
		if ( $input == 'on' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function use_wp_privacy_policy_page_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_use_wp_privacy_policy_page" hidden>wp_tarteaucitron_use_wp_privacy_policy_page</label>';
		$html .= '<input type="checkbox" id="wp_tarteaucitron_use_wp_privacy_policy_page" name="wp_tarteaucitron_use_wp_privacy_policy_page"';
		if ( $this->get_option_use_wp_privacy_policy_page() ) {
			$html .= 'value="on" checked';
		}
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	protected function get_option_use_wp_privacy_policy_page(): bool {
		return get_option( 'wp_tarteaucitron_use_wp_privacy_policy_page' );
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	protected function setup_privacy_policy_url_setting(): void {
		$form_id_setting_args = array(
			'sanitize_callback' => array( &$this, 'sanitize_privacy_policy_url_setting_input' ),
			'default'           => ''
		);
		register_setting(
			'inc\WP_tarteaucitron_Options',
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
	 * @param $input
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function sanitize_privacy_policy_url_setting_input( $input ): string {
		if ( $input === null ) {
			return '';
		} else {
			return $input;
		}
	}

	/**
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function privacy_policy_url_field_callback(): void {
		$html = '<p>';
		$html .= '<label for="wp_tarteaucitron_privacy_policy_url" hidden>wp_tarteaucitron_privacy_policy_url</label>';
		$html .= '<p><input type="url" id="wp_tarteaucitron_privacy_policy_url" name="wp_tarteaucitron_privacy_policy_url"';
		$html .= ' value="' . esc_attr( $this->get_option_wp_tarteaucitron_privacy_policy_url() ) . '"';
		$html .= ' placeholder=" ' . site_url() . ' " pattern="https?://.+"';
		if ( $this->get_option_use_wp_privacy_policy_page() ) {
			$html .= ' disabled';
		}
		$html .= '/></p>';
		echo $html;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 *
	 */
	protected function get_option_wp_tarteaucitron_privacy_policy_url(): string {
		return get_option( 'wp_tarteaucitron_privacy_policy_url' );
	}

	/**
	 * @param $links
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function plugin_settings_link( $links ): mixed {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=wp-tarteaucitron' ) . '">' . __( 'Settings' ) . '</a>';

		return $links;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function get_privacy_policy_url(): string {
		$default_privacy_policy_url = site_url();
		if ( $this->get_option_use_wp_privacy_policy_page() ) {
			$wp_privacy_policy_url = get_privacy_policy_url();
			if ( empty( $wp_privacy_policy_url ) ) {
				trigger_error( 'WordPress privacy policy page not set' );

				return $default_privacy_policy_url;
			} else {
				return $wp_privacy_policy_url;
			}
		} else {
			$tarteaucitron_privacy_policy_url = $this->get_option_wp_tarteaucitron_privacy_policy_url();
			if ( empty( $tarteaucitron_privacy_policy_url ) ) {
				trigger_error( 'tarteaucitron privacy policy URL not set' );

				return $default_privacy_policy_url;
			} else {
				return $tarteaucitron_privacy_policy_url;
			}
		}
	}

}

?>