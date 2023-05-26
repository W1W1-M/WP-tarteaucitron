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
		$this->get_plugin_file_path();
		register_activation_hook( '', 'plugin_activate' );
		register_deactivation_hook( '', 'plugin_deactivate');
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function plugin_activate(): void {
		add_action( 'init', 'setup_javascript_file' );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function plugin_deactivate(): void {

	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function setup_javascript_file(): void {
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
		} catch ( Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

	function get_plugin_file_path() {
		$path = plugin_dir_path( __FILE__ );
		trigger_error( $path, E_USER_NOTICE);
	}

}

?>