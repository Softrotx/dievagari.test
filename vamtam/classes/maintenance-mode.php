<?php

class VamtamMaintenanceMode {
	/**
	 * Actions and filters
	 */
	public static function setup() {
		add_filter( 'template_redirect', array( __CLASS__, 'template_redirect' ) );
		add_filter( 'get_post_metadata', array( __CLASS__, 'get_post_metadata' ), 10, 4 );
	}

	/**
	 * Redirect to selected Maintenance page if user is not logged in
	 */
	public static function template_redirect() {
		global $wp_query;

		$maintenance_page = vamtam_get_option( 'maintenance-page' );

		if ( ! empty( $maintenance_page ) && false !== get_post_status( $maintenance_page ) && ! is_user_logged_in() && ! is_page( $maintenance_page ) ) {
			wp_safe_redirect( get_permalink( $maintenance_page ) );
			exit;
		}
	}

	/**
	 * The Maintenance page always uses a blank template
	 *
	 * @param  mixed  $value
	 * @param  int    $object_id
	 * @param  string $meta_key
	 * @param  bool   $single
	 * @return mixed
	 */
	public static function get_post_metadata( $value, $object_id, $meta_key, $single ) {
		if (
			isset( $GLOBALS['vamtam_theme']['maintenance-page'] ) &&
			(int) $object_id === (int) $GLOBALS['vamtam_theme']['maintenance-page'] &&
			'_wp_page_template' === $meta_key
		) {
			return 'page-blank.php';
		}

		return $value;
	}
}
