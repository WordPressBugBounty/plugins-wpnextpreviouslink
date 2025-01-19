<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'wpnextpreviouslink_icon_path' ) ) {
	/**
	 * Resume icon path
	 *
	 * @return mixed|null
	 * @since 1.0.0
	 */
	function wpnextpreviouslink_icon_path() {
		$directory = trailingslashit( WPNEXTPREVIOUSLINK_ROOT_PATH ) . 'assets/icons/';

		return apply_filters( 'wpnextpreviouslink_icon_path', $directory );
	}//end method wpnextpreviouslink_icon_path
}


if ( ! function_exists( 'wpnextpreviouslink_load_svg' ) ) {
	/**
	 * Load an SVG file from a directory.
	 *
	 * @param string $svg_name The name of the SVG file (without the .svg extension).
	 * @param string $directory The directory where the SVG files are stored.
	 *
	 * @return string|false The SVG content if found, or false on failure.
	 * @since 1.0.0
	 */
	function wpnextpreviouslink_load_svg( $svg_name = '', $folder = '' ) {
		//note: code partially generated using chatgpt
		if ( $svg_name == '' ) {
			return '';
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$credentials = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null );
		if ( ! WP_Filesystem( $credentials ) ) {
			return ''; // Error handling here
		}

		global $wp_filesystem;

		$directory = wpnextpreviouslink_icon_path();

		// Sanitize the file name to prevent directory traversal attacks.
		$svg_name = sanitize_file_name( $svg_name );

		if($folder != ''){
			$folder = trailingslashit($folder);
		}

		// Construct the full file path.
		$file_path = $directory. $folder . $svg_name . '.svg';
		$file_path = apply_filters('wpnextpreviouslink_svg_file_path', $file_path, $svg_name);

		// Check if the file exists.
		//if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
		if ( $wp_filesystem->exists( $file_path ) && is_readable( $file_path ) ) {
			// Get the SVG file content.
			return $wp_filesystem->get_contents( $file_path );
		} else {
			// Return false if the file does not exist or is not readable.
			return '';
		}
	}//end method wpnextpreviouslink_load_svg
}

if(!function_exists('wpnextpreviouslink_doing_it_wrong')){
	/**
	 * Wrapper for _doing_it_wrong().
	 *
	 * @since  1.0.0
	 * @param string $function Function used.
	 * @param string $message Message to log.
	 * @param string $version Version the message was added in.
	 */
	function wpnextpreviouslink_doing_it_wrong( $function, $message, $version ) {
		// @codingStandardsIgnoreStart
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

		if ( wp_doing_ajax() || wpnextpreviouslink_is_rest_api_request() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
		// @codingStandardsIgnoreEnd
	}//end function wpnextpreviouslink_doing_it_wrong
}

if(!function_exists('wpnextpreviouslink_is_rest_api_request')){
	/**
	 * Check if doing rest request
	 *
	 * @return bool
	 */
	function wpnextpreviouslink_is_rest_api_request() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		$REQUEST_URI = isset($_SERVER['REQUEST_URI'])? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';

		if ( empty( $REQUEST_URI ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );


		return ( false !== strpos( $REQUEST_URI, $rest_prefix ) );
	}//end function wpnextpreviouslink_is_rest_api_request
}


if(!function_exists('wpnextpreviouslink_sanitize_wp_kses')){
	function  wpnextpreviouslink_sanitize_wp_kses($html = '') {
		return WPNextPreviousLinkHelper::sanitize_wp_kses($html);
	}//end function wpnextpreviouslink_sanitize_wp_kses
}