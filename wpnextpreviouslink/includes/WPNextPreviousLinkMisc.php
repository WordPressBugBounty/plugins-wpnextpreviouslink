<?php

/**
 * Class WPNextPreviousLinkMisc
 */
class WPNextPreviousLinkMisc {
	private $settings;

	public function __construct( $settings ) {
		$this->settings = $settings;
	}//end constructor

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.1.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wpnextpreviouslink', false, WPNEXTPREVIOUSLINK_ROOT_PATH . 'languages/' );
	}//end method load_plugin_textdomain
}//end class WPNextPreviousLinkMisc