<?php

/**
 * class WPNextPreviousLink *
 *
 */
final class WPNextPreviousLink {
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

	private $settings;

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

		$this->settings    = new WPNextPreviousLink_Settings_API();

		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}//end of constructor

	/**
	 * Singleton Instance.
	 *
	 * Ensures only one instance of wpnextpreviouslink is loaded or can be loaded.
	 *
	 * @return self Main instance.
	 * @see run_wpnextpreviouslink()
	 * @since  2.7.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}//end method instance

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		wpnextpreviouslink_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'wpnextpreviouslink' ), '1.0.0' );
	}//end method clone

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		wpnextpreviouslink_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'wpnextpreviouslink' ), '1.0.0' );
	}//end method wakeup

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Functions/wpnextpreviouslink-tpl-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/WPNextPreviousLink_Settings_API.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Helpers/WPNextPreviousLinkHelper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Functions/wpnextpreviouslink-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/WPNextPreviousLinkAdmin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/WPNextPreviousLinkPublic.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/WPNextPreviousLinkMisc.php';
	}//end method load_dependencies

	/**
	 * Register common hooks
	 *
	 * @since    2.7.0
	 * @access   private
	 */
	private function define_common_hooks() {
		$settings = $this->settings;

		$misc = new WPNextPreviousLinkMisc( $settings );
		add_action( 'init', [ $misc, 'load_plugin_textdomain' ] );
	}//end method define_common_hooks


	/**
	 * Register admin facing hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new WpnextpreviouslinkAdmin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_scripts' ] );

		//adding the setting action
		add_action( 'admin_init', [ $plugin_admin, 'setting_init' ] );
		//add_action( 'admin_init', [$plugin_admin, 'plugin_reset'], 1 );
		add_action( 'admin_menu', [ $plugin_admin, 'admin_pages' ] );
		add_action( 'plugin_row_meta', [ $plugin_admin, 'plugin_row_meta' ], 10, 4 );

		// add settings link
		add_filter( 'plugin_action_links_wpnextpreviouslink/wpnextpreviouslink.php', [
			$plugin_admin,
			'add_action_links'
		] );


		//plugin updates
		add_action( 'plugins_loaded', [ $plugin_admin, 'plugin_upgrader_process_complete' ] );
		add_action( 'admin_notices', [ $plugin_admin, 'plugin_activate_upgrade_notices' ] );
		add_action( 'after_plugin_row_wpnextpreviouslinkaddon/wpnextpreviouslinkaddon.php', [
			$plugin_admin,
			'custom_message_after_plugin_row_proaddon'
		], 10, 2 );

		//plugin reset
		add_action( 'wp_ajax_wpnextpreviouslink_settings_reset_load', [ $plugin_admin, 'settings_reset_load' ] );
		add_action( 'wp_ajax_wpnextpreviouslink_settings_reset', [ $plugin_admin, 'plugin_reset' ] );
	}//end method define_admin_hooks


	/**
	 * Register public facing hooks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new WPNextPreviousLinkPublic( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', [ $plugin_public, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $plugin_public, 'enqueue_scripts' ] );

		add_action( 'wp_footer', [ $plugin_public, 'wordPress_next_previous_link' ] );
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