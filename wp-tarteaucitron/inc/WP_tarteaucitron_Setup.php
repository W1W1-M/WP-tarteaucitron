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
class WP_tarteaucitron_Setup {

	/**
	 * @since 1.0.0
	 */
	public WP_tarteaucitron_Options $wp_tarteaucitron_options;

	/**
	 * @since 1.5.0
	 */
	protected string $wp_tarteaucitron_script_version;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->wp_tarteaucitron_script_version = $this->tarteaucitron_script_version();
		$this->wp_tarteaucitron_options = new WP_tarteaucitron_Options($this->wp_tarteaucitron_script_version);
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void {
		try {
			$this->wordpress_absolute_path_available();
			register_activation_hook( WP_TARTEAUCITRON_PLUGIN_FILE_PATH, array( $this, 'plugin_activate' ) );
			register_deactivation_hook( WP_TARTEAUCITRON_PLUGIN_FILE_PATH, array( $this, 'plugin_deactivate' ) );
			$this->actions();
		} catch ( Exception $exception ) {
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
	protected function wordpress_absolute_path_available(): bool {
		if( defined( 'ABSPATH' )) {
			return true;
		} else {
			$exception = new Exception( 'WordPress unavailable. Plugin not loaded.' );
			error_log( $exception->getMessage() );
			throw $exception;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function plugin_activate(): void {
		add_option('wp_tarteaucitron_just_activated',true );
	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function plugin_deactivate(): void {
		try {
			$this->delete_tarteaucitron_script_js_file();
			trigger_error( 'tarteaucitron js script deleted' );
		} catch( Exception $exception ) {
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
	protected function delete_tarteaucitron_script_js_file(): void {
		unlink( trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH) ) . WP_TARTEAUCITRON_SCRIPT_JS_FILE );
	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	protected function actions(): void {
		try {
			add_action( 'init', array( $this, 'load_textdomain' ), 10, 0 );
			add_action( 'admin_init', array( $this,'just_activated_setup' ), 10, 0 );
			add_action( 'plugins_loaded', array( $this->wp_tarteaucitron_options,'init' ), 10, 0 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 10, 0 );
			add_action( 'wp_enqueue_scripts', array( $this,'check_scripts_enqueued' ), 99, 0 );
			add_action( 'add_option_wp_tarteaucitron_use_wp_privacy_policy_page', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'update_option_wp_tarteaucitron_use_wp_privacy_policy_page', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'add_option_wp_tarteaucitron_privacy_policy_url', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'update_option_wp_tarteaucitron_privacy_policy_url', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'add_option_wp_tarteaucitron_hashtag', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'update_option_wp_tarteaucitron_hashtag', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'add_option_wp_tarteaucitron_name', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'update_option_wp_tarteaucitron_name', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'add_option_wp_tarteaucitron_icon_position', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'update_option_wp_tarteaucitron_icon_position', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'add_option_wp_tarteaucitron_remove_credit', array( $this, 'setup_tarteaucitron_script_js_file' ) );
			add_action( 'update_option_wp_tarteaucitron_remove_credit', array( $this, 'setup_tarteaucitron_script_js_file' ) );
		} catch ( Exception $exception ) {
			error_log( 'WP-tarteaucitron actions error' );
			throw $exception;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain( 'wp-tarteaucitron', false, dirname( plugin_basename( WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) ) . '/lang/' );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function just_activated_setup(): void {
		if( current_user_can( 'activate_plugins' ) ) {
			if( get_option('wp_tarteaucitron_just_activated' ) ) {
				delete_option( 'wp_tarteaucitron_just_activated' );
				try {
					$this->setup_tarteaucitron_script_js_file();
				} catch ( Exception $exception ) {
					trigger_error( $exception->getMessage() );
					error_log( $exception->getMessage() );
				}
			}
		} else {
			trigger_error( 'User is not authorized to run activation setup' );
		}

	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_scripts(): void {
		try {
			$tarteaucitron_version = $this->tarteaucitron_script_version();
			$this->enqueue_tarteaucitron_js( $tarteaucitron_version );
			$this->enqueue_tarteaucitron_script_js( $tarteaucitron_version );
			$this->enqueue_tracking_code_script();
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function tarteaucitron_script_version(): mixed {
		$tarteaucitron_version = false;
		if( $this->tarteaucitron_package_json_file_exists() ) {
			$tarteaucitron_package_json = file_get_contents( $this->tarteaucitron_package_json_file_path() );
			$decoded_tarteaucitron_package_json = json_decode( $tarteaucitron_package_json, false );
			if( $decoded_tarteaucitron_package_json == null ) {
				$exception = new Exception( 'Cannot decode tarteaucitron package json. ');
				trigger_error( $exception->getMessage() . 'Script version error. Use default version.' );
			} elseif( property_exists( $decoded_tarteaucitron_package_json, 'version' ) ) {
				$tarteaucitron_version = $decoded_tarteaucitron_package_json->version;
				trigger_error( 'tarteaucitron v' . $tarteaucitron_version );
			} else {
				$exception = new Exception( 'tarteaucitron package version not found. ' );
				trigger_error( $exception->getMessage() . 'Script version error. Use default version.' );
			}
		} else {
			$exception = new Exception( $this->tarteaucitron_package_json_file_path() . ' not found. ' );
			trigger_error( $exception->getMessage() . 'Script version error. Use default version.' );
		}
		return $tarteaucitron_version;
	}

	/**
	 * @since 1.2.0
	 *
	 * @param $tarteaucitron_version
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 */
	protected function enqueue_tarteaucitron_js( $tarteaucitron_version ): void {
		if ( $this->tarteaucitron_js_file_missing() ) {
			$exception = new Exception( 'cannot find ' . WP_TARTEAUCITRON_JS_FILE);
			error_log( $exception->getMessage() );
			throw $exception;
		} else {
			wp_enqueue_script( WP_TARTEAUCITRON_JS, plugins_url( WP_TARTEAUCITRON_PACKAGE_PATH . WP_TARTEAUCITRON_JS_FILE, WP_TARTEAUCITRON_PLUGIN_FILE_PATH ), array(), $tarteaucitron_version );
		}
	}

	/**
	 * @since 1.2.0
	 *
	 * @param $tarteaucitron_version
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	protected function enqueue_tarteaucitron_script_js( $tarteaucitron_version ): void {
		if( $this->tarteaucitron_script_js_file_missing() ) {
			$exception = new Exception( 'cannot find ' . WP_TARTEAUCITRON_SCRIPT_JS_FILE);
			trigger_error( $exception->getMessage() );
			$this->setup_tarteaucitron_script_js_file();
		}
		wp_enqueue_script( WP_TARTEAUCITRON_SCRIPT_JS, plugins_url( WP_TARTEAUCITRON_SCRIPT_JS_FILE, WP_TARTEAUCITRON_PLUGIN_FILE_PATH), array( WP_TARTEAUCITRON_JS ), $tarteaucitron_version );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function tarteaucitron_js_file_missing(): bool {
		$tarteaucitron_js_file_path = trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_PACKAGE_PATH . WP_TARTEAUCITRON_JS_FILE;
		return !file_exists( $tarteaucitron_js_file_path );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function tarteaucitron_script_js_file_missing(): bool {
		$tarteaucitron_script_js_file_path = trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_SCRIPT_JS_FILE;
		return !file_exists( $tarteaucitron_script_js_file_path );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function tarteaucitron_package_json_file_exists(): bool {
		return file_exists( $this->tarteaucitron_package_json_file_path() );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function tarteaucitron_package_json_file_path(): string {
		return trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_PACKAGE_PATH . WP_TATEAUCITRON_PACKAGE_JSON_FILE;
	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function check_scripts_enqueued(): void {
		$scripts = array( WP_TARTEAUCITRON_JS, WP_TARTEAUCITRON_SCRIPT_JS );
		foreach( $scripts as $script ) {
			if( wp_script_is( $script ) ) {
				trigger_error( $script . ' is enqueued' );
			} else {
				$exception = new Exception( $script . ' is not enqueued. ' );
				error_log( $exception->getMessage() );
				throw $exception;
			}
		}
	}

	/**
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function enqueue_tracking_code_script(): void {
		$tracking_code_script = $this->wp_tarteaucitron_options->get_option_tracking_code();
		wp_add_inline_script( WP_TARTEAUCITRON_SCRIPT_JS, $tracking_code_script );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_tarteaucitron_script_js_file(): void {
		$privacy_policy_url = $this->wp_tarteaucitron_options->get_tatrteaucitron_privacy_policy_url();
		$hashtag = get_option( 'wp_tarteaucitron_hashtag' ) ?: '#tarteaucitron';
		$icon_position = get_option( 'wp_tarteaucitron_icon_position' ) ?: 'BottomRight';
		$cookie_name = get_option( 'wp_tarteaucitron_cookie_name' ) ?: 'tarteaucitron';
		$remove_credit = get_option( 'wp_tarteaucitron_remove_credit' ) ? 'true' : 'false';
		$javascript = 'tarteaucitron.init({"privacyUrl": "' . $privacy_policy_url . '", "hashtag": "' . $hashtag . '", "cookieName": "' . $cookie_name . '", "iconPosition": "' . $icon_position . '", "removeCredit": ' . $remove_credit . '});';
		try {
			$javascript_file = fopen( trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH) ) . WP_TARTEAUCITRON_SCRIPT_JS_FILE, 'w+' );
			fwrite( $javascript_file, $javascript );
			fclose( $javascript_file );
			trigger_error( 'tarteaucitron js script created' );
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}
}

?>