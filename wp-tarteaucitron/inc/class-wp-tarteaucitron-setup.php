<?php /** @noinspection PhpRedundantClosingTagInspection */

/**
 * @since 1.0.0
 */
class WP_tarteaucitron_Setup {

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
			$this->delete_javascript_file();
			trigger_error( 'tarteaucitron js script deleted', E_USER_NOTICE);
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
	protected function delete_javascript_file(): void {
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
			add_action( 'admin_init', array( $this,'just_activated_setup' ), 10, 0 );
			add_action( 'plugins_loaded', array( $this,'options_init' ), 10, 0 );
			add_action( 'wp_enqueue_scripts', array( $this,'scripts' ), 10, 0 );
			add_action( 'wp_enqueue_scripts', array( $this,'check_scripts_enqueued' ), 99, 0 );
			add_action( 'add_option_wp_tarteaucitron_privacy_url', array( $this, 'setup_javascript_file' ) );
			add_action( 'update_option_wp_tarteaucitron_privacy_url', array( $this, 'setup_javascript_file' ) );
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
	public function just_activated_setup(): void {
		if( current_user_can( 'activate_plugins' ) ) {
			if( get_option('wp_tarteaucitron_just_activated' ) ) {
				delete_option( 'wp_tarteaucitron_just_activated' );
				$this->setup_javascript_file();
			}
		} else {
			trigger_error( 'User is not authorized to run activation setup', E_USER_NOTICE);
		}

	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function options_init(): void {
		$wp_tarteaucitron_options = new WP_tateaucitron_Options();
		$wp_tarteaucitron_options->init();
	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function scripts(): void {
		try {
			$tarteaucitron_version = $this->tarteaucitron_script_version();
		} catch ( Exception ) {
			error_log( 'WP-tarteaucitron script version error. Use default version.' );
			$tarteaucitron_version = false;
		}
		if( $this->tarteaucitron_js_file_exists() ) {
			wp_enqueue_script( 'tarteaucitron_js', plugins_url( WP_TARTEAUCITRON_PACKAGE_PATH . WP_TARTEAUCITRON_JS_FILE, WP_TARTEAUCITRON_PLUGIN_FILE_PATH ), array(), $tarteaucitron_version );
		} else {
			$exception = new Exception( 'cannot find ' . WP_TARTEAUCITRON_JS_FILE);
			error_log( $exception->getMessage() );
			throw $exception;
		}
		if( $this->tarteaucitron_script_js_file_exists() ) {
			wp_enqueue_script( 'tarteaucitron_script_js', plugins_url( WP_TARTEAUCITRON_SCRIPT_JS_FILE, WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) );
		} else {
			$exception = new Exception( 'cannot find ' . WP_TARTEAUCITRON_SCRIPT_JS_FILE);
			error_log( $exception->getMessage() );
			throw $exception;
		}

	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return string
	 */
	public function tarteaucitron_script_version(): string {
		if( $this->tarteaucitron_package_json_file_exists() ) {
			$tarteaucitron_package_json = file_get_contents( $this->tarteaucitron_package_json_file_path() );
			$decoded_tarteaucitron_package_json = json_decode( $tarteaucitron_package_json, false );
			if( $decoded_tarteaucitron_package_json == null ) {
				$exception = new Exception( 'cannot decode tarteaucitron package json');
				error_log( $exception->getMessage() );
				throw $exception;
			} else {
				if( property_exists( $decoded_tarteaucitron_package_json, 'version' ) ) {
					$tarteaucitron_version = $decoded_tarteaucitron_package_json->version;
					trigger_error( 'tarteaucitron v' . $tarteaucitron_version );
					return $tarteaucitron_version;
				} else {
					$exception = new Exception( 'tarteaucitron package version not found' );
					error_log( $exception->getMessage() );
					throw $exception;
				}
			}
		} else {
			$exception = new Exception( $this->tarteaucitron_package_json_file_path() . ' not found' );
			error_log( $exception->getMessage() );
			throw $exception;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function tarteaucitron_js_file_exists(): bool {
		if( file_exists( $this->tarteaucitron_js_file_path() ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function tarteaucitron_script_js_file_exists(): bool {
		if( file_exists( $this->tarteaucitron_script_js_file_path() ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function tarteaucitron_package_json_file_exists(): bool {
		if( file_exists( $this->tarteaucitron_package_json_file_path() ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function tarteaucitron_js_file_path(): string {
		return trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_PACKAGE_PATH . WP_TARTEAUCITRON_JS_FILE;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function tarteaucitron_script_js_file_path(): string {
		return trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH ) ) . WP_TARTEAUCITRON_SCRIPT_JS_FILE;
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
		$scripts = array(
			'tarteaucitron_js' => 'tarteaucitron_js',
			'tarteaucitron_script_js' => 'tarteaucitron_script_js'
		);
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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_javascript_file(): void {
		$privacy_url = get_option( 'wp_tarteaucitron_privacy_url' );
		if( ! $privacy_url ) {
			$privacy_url_parameter = site_url();
		} else {
			$privacy_url_parameter = $privacy_url;
		}
		$javascript = 'tarteaucitron.init({"privacyUrl": "' . $privacy_url_parameter . '"});';
		try {
			$javascript_file = fopen( trailingslashit( dirname(WP_TARTEAUCITRON_PLUGIN_FILE_PATH) ) . WP_TARTEAUCITRON_SCRIPT_JS_FILE, 'w+' );
			fwrite( $javascript_file, $javascript);
			fclose($javascript_file);
			trigger_error( 'tarteaucitron js script created', E_USER_NOTICE);
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

}

?>