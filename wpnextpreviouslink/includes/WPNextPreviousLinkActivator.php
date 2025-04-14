<?php

/**
 * Class WPNextPreviousLinkActivator
 */
class WPNextPreviousLinkActivator {

	/**
	 * On plugin activate do some jobs
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		set_transient( 'wpnextpreviouslink_activated_notice', 1 );

		// Update the saved version
		update_option('wpnextpreviouslink_version', WPNEXTPREVIOUSLINK_VERSION);
	}//end activate

}//end class WPNextPreviousLinkActivator
