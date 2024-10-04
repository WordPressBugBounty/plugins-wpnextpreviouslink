<?php

/**
 * Fired during plugin activation
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    Wpnextpreviouslink
 * @subpackage Wpnextpreviouslink/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wpnextpreviouslink
 * @subpackage Wpnextpreviouslink/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLink_Activator {

	/**
	 * On plugin activate do some jobs
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		set_transient( 'wpnextpreviouslink_activated_notice', 1 );
	}//end activate

}//end class WPNextPreviousLink_Activator
