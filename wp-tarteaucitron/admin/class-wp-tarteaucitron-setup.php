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

	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public static function wordpress_absolute_path_available(): bool {
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
	public static function plugin_activate(): void {
		add_option('wp_tarteaucitron_just_activated',true );
	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public static function plugin_deactivate(): void {
		try {
			WP_tarteaucitron_Setup::delete_javascript_file();
		} catch( Exception $exception ) {
			error_log( $exception->getMessage() );
			throw $exception;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function plugin_uninstall(): void {
		delete_option( 'wp_tarteaucitron_privacy_url' );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function just_activated_setup(): void {
		if( current_user_can( 'activate_plugins' ) ) {
			if( get_option('wp_tarteaucitron_just_activated' ) ) {
				delete_option( 'wp_tarteaucitron_just_activated' );
				WP_tarteaucitron_Setup::setup_javascript_file();
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
	public static function setup_javascript_file(): void {
		$privacy_url = get_option( 'wp_tarteaucitron_privacy_url' );
		if( ! $privacy_url ) {
			$privacy_url_parameter = site_url();
		} else {
			$privacy_url_parameter = $privacy_url;
		}
		$javascript = 'tarteaucitron.init({"privacyUrl": "' . $privacy_url_parameter . '"});';
		try {
			$javascript_file = fopen( trailingslashit( dirname(PLUGIN_FILE_PATH) ) . 'tarteaucitron-script.js', 'w+' );
			fwrite( $javascript_file, $javascript);
			fclose($javascript_file);
			trigger_error( 'tarteaucitron js script created', E_USER_NOTICE);
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public static function delete_javascript_file(): void {
		unlink( PLUGIN_FILE_PATH . 'tarteaucitron-script.js');
	}
}

?>