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
	 * @return void
	 */
	public static function just_activated_setup(): void {
		if( current_user_can( 'activate_plugins' ) ) {
			if( get_option('WP_tarteaucitron_just_activated' ) ) {
				delete_option( 'WP_tarteaucitron_just_activated' );
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
			$javascript_file = fopen( trailingslashit( dirname(__FILE__) ) . 'tarteaucitron-script.js', 'w+' );
			fwrite( $javascript_file, $javascript);
			fclose($javascript_file);
			trigger_error( 'tarteaucitron js script created', E_USER_NOTICE);
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

}

?>