<?php
/**
 * @link              https://codeboxr.com
 * @since             1.0
 * @package           Wpnextpreviouslink
 *
 * @wordpress-plugin
 * Plugin Name:       CBX Next Previous Article
 * Plugin URI:        https://codeboxr.com/product/show-next-previous-article-for-wordpress
 * Description:       WordPress Next Previous Article/Link
 * Version:           2.7.4
 * Author:            Codeboxr Team
 * Author URI:        https://codeboxr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpnextpreviouslink
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'WPNEXTPREVIOUSLINK_VERSION' ) or define( 'WPNEXTPREVIOUSLINK_VERSION', '2.7.4' );
defined( 'WPNEXTPREVIOUSLINK_PLUGIN_NAME' ) or define( 'WPNEXTPREVIOUSLINK_PLUGIN_NAME', 'wpnextpreviouslink' );
defined( 'WPNEXTPREVIOUSLINK_ROOT_PATH' ) or define( 'WPNEXTPREVIOUSLINK_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'WPNEXTPREVIOUSLINK_ROOT_URL' ) or define( 'WPNEXTPREVIOUSLINK_ROOT_URL', plugin_dir_url( __FILE__ ) );
defined( 'WPNEXTPREVIOUSLINK_BASE_NAME' ) or define( 'WPNEXTPREVIOUSLINK_BASE_NAME', plugin_basename( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/WPNextPreviousLinkActivator.php
 */
function activate_wpnextpreviouslink() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/WPNextPreviousLinkActivator.php';
	WPNextPreviousLinkActivator::activate();
}//end method activate_wpnextpreviouslink

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/WPNextPreviousLinkDeactivator.php
 */
function deactivate_wpnextpreviouslink() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/WPNextPreviousLinkDeactivator.php';
	WPNextPreviousLinkDeactivator::deactivate();
}//end method

register_activation_hook( __FILE__, 'activate_wpnextpreviouslink' );
register_deactivation_hook( __FILE__, 'deactivate_wpnextpreviouslink' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/WPNextPreviousLink.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.7.0
 */
function run_wpnextpreviouslink() {
	return WPNextPreviouslink::instance();
}//end method run_wpnextpreviouslink

$GLOBALS['wpnextpreviouslink_core'] = run_wpnextpreviouslink();