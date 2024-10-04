<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLink {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.7.0
	 */
	private static $instance = null;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The plugin name of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The plugin name of the plugin.
	 */
	protected $plugin_name;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = WPNEXTPREVIOUSLINK_PLUGIN_NAME;
		$this->version     = WPNEXTPREVIOUSLINK_VERSION;

		$this->load_dependencies();

		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}//end of constructor

	/**
	 * Singleton Instance.
	 *
	 * Ensures only one instance of wpnextpreviouslink is loaded or can be loaded.
	 *
	 * @since  2.7.0
	 * @static
	 * @see run_wpnextpreviouslink()
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}//end method instance

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wpnextpreviouslink-tpl-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpnextpreviouslink-setting.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpnextpreviouslink-helper.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpnextpreviouslink-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpnextpreviouslink-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wpnextpreviouslink-functions.php';
	}//end method load_dependencies

	/**
	 * Register common hooks
	 *
	 * @since    2.7.0
	 * @access   private
	 */
	private function define_common_hooks() {
		add_action( 'plugins_loaded', [$this, 'load_plugin_textdomain'] );
	}//end method define_common_hooks

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.1.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(	'wpnextpreviouslink',false,WPNEXTPREVIOUSLINK_ROOT_PATH . 'languages/'	);
	}//end method load_plugin_textdomain

	/**
	 * Register admin facing hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Wpnextpreviouslink_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', [$plugin_admin, 'enqueue_styles']);
		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_scripts']);

		//adding the setting action
		add_action( 'admin_init', [$plugin_admin, 'setting_init'] );
		//add_action( 'admin_init', [$plugin_admin, 'plugin_reset'], 1 );
		add_action( 'admin_menu', [$plugin_admin, 'admin_pages'] );
		add_action( 'plugin_row_meta', [$plugin_admin, 'plugin_row_meta'], 10, 4 );

		// add settings link
		add_filter( 'plugin_action_links_wpnextpreviouslink/wpnextpreviouslink.php', [$plugin_admin, 'add_action_links'] );
		add_action( 'upgrader_process_complete', [$plugin_admin, 'plugin_upgrader_process_complete'], 10, 2 );
		add_action( 'admin_notices', [$plugin_admin, 'plugin_activate_upgrade_notices'] );

		//plugin updates
		add_filter( 'pre_set_site_transient_update_plugins', [$plugin_admin, 'pre_set_site_transient_update_plugins_pro_addon'] );
		add_action( 'in_plugin_update_message-' . 'wpnextpreviouslinkaddon/wpnextpreviouslinkaddon.php', [$plugin_admin, 'plugin_update_message_pro_addon'] );

		//plugin reset
		add_action('wp_ajax_wpnextpreviouslink_settings_reset_load', [$plugin_admin, 'settings_reset_load']);
		add_action('wp_ajax_wpnextpreviouslink_settings_reset', [$plugin_admin, 'plugin_reset']);
	}//end method define_admin_hooks


	/**
	 * Register public facing hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new WPNextPreviousLink_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', [$plugin_public, 'enqueue_styles'] );
		add_action( 'wp_enqueue_scripts', [$plugin_public, 'enqueue_scripts'] );

		add_action( 'wp_footer', [$plugin_public, 'wordPress_next_previous_link'] );
	}//end method define_public_hooks




	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}//end method get_plugin_name

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}//end method get_version

}//end class WPNextPreviousLink