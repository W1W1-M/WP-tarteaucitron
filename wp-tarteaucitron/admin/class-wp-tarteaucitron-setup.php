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
		if ( current_user_can( 'activate_plugins' && get_option('WP_tarteaucitron_just_activated') ) ) {
			delete_option( 'WP_tarteaucitron_just_activated' );
			setup_javascript_file();
		} else {
			trigger_error( 'User is not authorized to run activation setup', E_USER_NOTICE);
		}

	}

}

?>