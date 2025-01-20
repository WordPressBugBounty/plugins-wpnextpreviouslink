<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLinkAdmin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wpnextpreviouslink_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix_settings = null;

	/**
	 * The settings ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $wpnextpreviouslink The ID of this plugin.
	 */
	private $wpnextpreviouslink;


	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The basename of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_basename The basename of this plugin.
	 */
	private $plugin_basename;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	//for setting
	private $settings;

	/**
	 * The plugin name of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The plugin name of the plugin.
	 */
	protected $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->wpnextpreviouslink = $plugin_name;
		$this->plugin_name        = $plugin_name;
		$this->plugin_basename    = plugin_basename( plugin_dir_path( __DIR__ ) . $this->wpnextpreviouslink . '.php' );
		$this->version            = $version;
		$this->settings           = new WPNextPreviousLink_Settings_API();

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->version = current_time( 'timestamp' ); //for development time only
		}
	}//end of constructor

	/**
	 * Init setting
	 */
	public function setting_init() {
		//set the settings
		$this->settings->set_sections( $this->get_settings_sections() );
		$this->settings->set_fields( $this->get_settings_fields() );
		//initialize settings
		$this->settings->admin_init();
	}//end setting_init

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$version = $this->version;

		$css_url_part     = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/css/';
		$js_url_part      = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/js/';
		$vendors_url_part = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/vendors/';

		$css_path_part     = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/css/';
		$js_path_part      = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/js/';
		$vendors_path_part = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/vendors/';

		$suffix       = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Recommended

		wp_register_style( 'awesome-notifications', $vendors_url_part . 'awesome-notifications/style.css', [], $version );
		wp_register_style( 'pickr', $vendors_url_part . 'pickr/themes/classic.min.css', [], $version );
		wp_register_style( 'select2', $vendors_url_part . 'select2/css/select2.min.css', [], $this->version );

		wp_register_style( 'wpnextpreviouslink-admin', $css_url_part . 'wpnextpreviouslink-admin.css', [], $version );

		if ( $current_page == 'wpnextpreviouslink' ) {

			wp_register_style( 'wpnextpreviouslink-setting', $css_url_part . 'wpnextpreviouslink-setting.css', [
				'pickr',
				'select2',
				'awesome-notifications',
				'wpnextpreviouslink-admin'
			], $this->version, 'all' );


			/*wp_enqueue_style( 'wp-color-picker' );
			//wp_enqueue_style('chosen');
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'wpnextpreviouslink-setting' );*/

			wp_enqueue_style( 'pickr' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'awesome-notifications' );

			wp_enqueue_style( 'wpnextpreviouslink-admin' );//common admin styles
			wp_enqueue_style( 'wpnextpreviouslink-setting' );
		}

		/*if ( $current_page == 'wpnextpreviouslink'  || $current_page == 'wpnextpreviouslink&doc=1') {
			wp_register_style( 'wpnextpreviouslink-branding', plugin_dir_url( __FILE__ ) . '../assets/css/wpnextpreviouslink-branding.css',
				array(),
				$this->version );
			wp_enqueue_style( 'wpnextpreviouslink-branding' );
		}*/
	}//end enqueue_styles

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$version = $this->version;

		$css_url_part     = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/css/';
		$js_url_part      = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/js/';
		$vendors_url_part = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/vendors/';

		$css_path_part     = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/css/';
		$js_path_part      = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/js/';
		$vendors_path_part = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/vendors/';

		$suffix       = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Recommended


		wp_register_script( 'jquery-validate', $vendors_url_part . 'jquery-validation/jquery.validate.min.js', [ 'jquery' ], $version, true );
		wp_register_script( 'awesome-notifications', $vendors_url_part . 'awesome-notifications/script.js', [], $version, true );
		wp_register_script( 'pickr', $vendors_url_part . 'pickr/pickr.min.js', [], $version, true );
		wp_register_script( 'select2', $vendors_url_part . 'select2/js/select2.min.js', [ 'jquery' ], $this->version, true );


		wp_register_script( 'wpnextpreviouslink-setting', $js_url_part . 'wpnextpreviouslink-setting.js', [
			'jquery',
			'jquery-ui-sortable',
			'select2',
			'pickr',
			'awesome-notifications'
		], $this->version, true );

		$translation_settings_vars = [
			'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
			'ajax_fail'                => esc_html__( 'Request failed, please reload the page.', 'wpnextpreviouslink' ),
			'nonce'                    => wp_create_nonce( "settingsnonce" ),
			'teeny_setting'            => [
				'teeny'         => true,
				'media_buttons' => true,
				'editor_class'  => '',
				'textarea_rows' => 5,
				'quicktags'     => false,
				'menubar'       => false,
			],
			'copycmds'                 => [
				'copy'       => esc_html__( 'Copy', 'wpnextpreviouslink' ),
				'copied'     => esc_html__( 'Copied', 'wpnextpreviouslink' ),
				'copy_tip'   => esc_html__( 'Click to copy', 'wpnextpreviouslink' ),
				'copied_tip' => esc_html__( 'Copied to clipboard', 'wpnextpreviouslink' ),
			],
			'confirm_msg'              => esc_html__( 'Are you sure to remove this step?', 'wpnextpreviouslink' ),
			'confirm_msg_all'          => esc_html__( 'Are you sure to remove all steps?', 'wpnextpreviouslink' ),
			'confirm_yes'              => esc_html__( 'Yes', 'wpnextpreviouslink' ),
			'confirm_no'               => esc_html__( 'No', 'wpnextpreviouslink' ),
			'are_you_sure_global'      => esc_html__( 'Are you sure?', 'wpnextpreviouslink' ),
			'are_you_sure_delete_desc' => esc_html__( 'Once you delete, it\'s gone forever. You can not revert it back.', 'wpnextpreviouslink' ),
			'pickr_i18n'               => [
				// Strings visible in the UI
				'ui:dialog'       => esc_html__( 'color picker dialog', 'wpnextpreviouslink' ),
				'btn:toggle'      => esc_html__( 'toggle color picker dialog', 'wpnextpreviouslink' ),
				'btn:swatch'      => esc_html__( 'color swatch', 'wpnextpreviouslink' ),
				'btn:last-color'  => esc_html__( 'use previous color', 'wpnextpreviouslink' ),
				'btn:save'        => esc_html__( 'Save', 'wpnextpreviouslink' ),
				'btn:cancel'      => esc_html__( 'Cancel', 'wpnextpreviouslink' ),
				'btn:clear'       => esc_html__( 'Clear', 'wpnextpreviouslink' ),

				// Strings used for aria-labels
				'aria:btn:save'   => esc_html__( 'save and close', 'wpnextpreviouslink' ),
				'aria:btn:cancel' => esc_html__( 'cancel and close', 'wpnextpreviouslink' ),
				'aria:btn:clear'  => esc_html__( 'clear and close', 'wpnextpreviouslink' ),
				'aria:input'      => esc_html__( 'color input field', 'wpnextpreviouslink' ),
				'aria:palette'    => esc_html__( 'color selection area', 'wpnextpreviouslink' ),
				'aria:hue'        => esc_html__( 'hue selection slider', 'wpnextpreviouslink' ),
				'aria:opacity'    => esc_html__( 'selection slider', 'wpnextpreviouslink' ),
			],
			'awn_options'              => [
				'tip'           => esc_html__( 'Tip', 'wpnextpreviouslink' ),
				'info'          => esc_html__( 'Info', 'wpnextpreviouslink' ),
				'success'       => esc_html__( 'Success', 'wpnextpreviouslink' ),
				'warning'       => esc_html__( 'Attention', 'wpnextpreviouslink' ),
				'alert'         => esc_html__( 'Error', 'wpnextpreviouslink' ),
				'async'         => esc_html__( 'Loading', 'wpnextpreviouslink' ),
				'confirm'       => esc_html__( 'Confirmation', 'wpnextpreviouslink' ),
				'confirmOk'     => esc_html__( 'OK', 'wpnextpreviouslink' ),
				'confirmCancel' => esc_html__( 'Cancel', 'wpnextpreviouslink' )
			],
			'validation'               => [
				'required'    => esc_html__( 'This field is required.', 'wpnextpreviouslink' ),
				'remote'      => esc_html__( 'Please fix this field.', 'wpnextpreviouslink' ),
				'email'       => esc_html__( 'Please enter a valid email address.', 'wpnextpreviouslink' ),
				'url'         => esc_html__( 'Please enter a valid URL.', 'wpnextpreviouslink' ),
				'date'        => esc_html__( 'Please enter a valid date.', 'wpnextpreviouslink' ),
				'dateISO'     => esc_html__( 'Please enter a valid date ( ISO ).', 'wpnextpreviouslink' ),
				'number'      => esc_html__( 'Please enter a valid number.', 'wpnextpreviouslink' ),
				'digits'      => esc_html__( 'Please enter only digits.', 'wpnextpreviouslink' ),
				'equalTo'     => esc_html__( 'Please enter the same value again.', 'wpnextpreviouslink' ),
				'maxlength'   => esc_html__( 'Please enter no more than {0} characters.', 'wpnextpreviouslink' ),
				'minlength'   => esc_html__( 'Please enter at least {0} characters.', 'wpnextpreviouslink' ),
				'rangelength' => esc_html__( 'Please enter a value between {0} and {1} characters long.', 'wpnextpreviouslink' ),
				'range'       => esc_html__( 'Please enter a value between {0} and {1}.', 'wpnextpreviouslink' ),
				'max'         => esc_html__( 'Please enter a value less than or equal to {0}.', 'wpnextpreviouslink' ),
				'min'         => esc_html__( 'Please enter a value greater than or equal to {0}.', 'wpnextpreviouslink' ),
				'recaptcha'   => esc_html__( 'Please check the captcha.', 'wpnextpreviouslink' ),
			],
			'lang'                     => get_user_locale(),
			'image_url'                => plugins_url( 'assets/images/', dirname( __FILE__ ) ),
			'please_select'            => esc_html__( 'Please Select', 'wpnextpreviouslink' ),
			//'upload_title'  => esc_html__( 'Select Media File', 'wpnextpreviouslink' ),
			'upload_btn'               => esc_html__( 'Upload', 'wpnextpreviouslink' ),
			'upload_title'             => esc_html__( 'Select Media', 'wpnextpreviouslink' ),
		];


		//enqueue admin.js wpnextpreviouslink settings page
		if ( $current_page == 'wpnextpreviouslink' ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();

			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'pickr' );
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'awesome-notifications' );

			wp_localize_script( 'wpnextpreviouslink-setting', 'wpnextpreviouslink_settings_vars', apply_filters( 'wpnextpreviouslink_settings_vars', $translation_settings_vars ) );
			wp_enqueue_script( 'wpnextpreviouslink-setting' );
		}
	}//end enqueue_scripts

	/**
	 * Get plugin basename
	 *
	 * @return    string    The basename of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_basename() {
		return $this->plugin_basename;
	}//end get_plugin_basename


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_pages() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';//phpcs:ignore WordPress.Security.NonceVerification.Recommended

		//setting menu
		$hook = add_options_page(
			esc_html__( 'CBX Next Previous Setting', 'wpnextpreviouslink' ),
			esc_html__( 'CBX Next Previous', 'wpnextpreviouslink' ),
			'manage_options',
			'wpnextpreviouslink',
			[ $this, 'menu_settings' ]
		);
	}//end add_plugin_admin_menu

	/**
	 * Display options page of this plugin
	 * @global type $wpdb
	 *
	 */
	public function menu_settings() {
		$doc = isset( $_REQUEST['doc'] ) ? absint( $_REQUEST['doc'] ) : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $doc ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpnextpreviouslink_get_template_html( 'admin/support.php', [
				'admin_ref' => $this,
				'settings'  => $this->settings
			] );
		} else {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpnextpreviouslink_get_template_html( 'admin/settings.php', [
				'admin_ref' => $this,
				'settings'  => $this->settings
			] );
		}
	}//end menu_settings

	/**
	 * Get The Available Arrow type for the link
	 *
	 * @return array
	 */
	public function get_wpnextprevios_arrow_type() {
		return WPNextPreviousLinkHelper::wpnextprevios_arrow_type();
	}//end get_wpnextprevios_arrow_type

	/**
	 * Get The Available Image type for the link
	 *
	 * @return array
	 */
	public function get_wpnextprevios_image_type() {
		return WPNextPreviousLinkHelper::wpnextprevios_image_type();
	}//end get_wpnextprevios_image_type


	/**
	 * Set settings fields
	 *
	 * @return type array
	 */
	public function get_settings_sections() {
		return WPNextPreviousLinkHelper::get_settings_sections();
	}//end method get_settings_sections

	/**
	 * Return the key value pair of post types
	 *
	 * @param type array $all_post_types
	 *
	 * @return type array
	 */
	public function get_formatted_posttype_multicheckbox( $all_post_types ) {
		return WPNextPreviousLinkHelper::get_post_types_formatted( $all_post_types );
	}//end get_formatted_posttype_multicheckbox

	/**
	 * Get All(default and custom) Post types
	 *
	 * @return array
	 */
	public function wpnp_post_types() {
		return WPNextPreviousLinkHelper::get_post_types();

	}//end wpnp_post_types

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	public function get_settings_fields() {
		return WPNextPreviousLinkHelper::wpnextpreviouslink_settings_fields();
	}//end get_settings_fields

	/**
	 * Filters the array of row meta for each/specific plugin in the Plugins list table.
	 * Appends additional links below each/specific plugin on the plugins page.
	 *
	 * @access  public
	 *
	 * @param array $links_array An array of the plugin's metadata
	 * @param string $plugin_file_name Path to the plugin file
	 * @param array $plugin_data An array of plugin data
	 * @param string $status Status of the plugin
	 *
	 * @return  array       $links_array
	 */
	public function plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {
		if ( strpos( $plugin_file_name, WPNEXTPREVIOUSLINK_BASE_NAME ) !== false ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://wordpress.org/plugins/wpnextpreviouslink/" aria-label="' . esc_attr__( 'Free Support', 'wpnextpreviouslink' ) . '">' . esc_html__( 'Free Support', 'wpnextpreviouslink' ) . '</a>';
			$links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://wordpress.org/plugins/wpnextpreviouslink/#reviews" aria-label="' . esc_attr__( 'Reviews', 'wpnextpreviouslink' ) . '">' . esc_html__( 'Reviews', 'wpnextpreviouslink' ) . '</a>';


			if ( in_array( 'wpnextpreviouslinkaddon/wpnextpreviouslinkaddon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'WPNEXTPREVIOUSLINKADDON_PLUGIN_NAME' ) ) {
				$links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://codeboxr.com/contact-us/" aria-label="' . esc_attr__( 'Pro Support', 'wpnextpreviouslink' ) . '">' . esc_html__( 'Pro Support', 'wpnextpreviouslink' ) . '</a>';
			} else {
				$links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://codeboxr.com/product/show-next-previous-article-for-wordpress/" aria-label="' . esc_attr__( 'Try Pro Addon', 'wpnextpreviouslink' ) . '">' . esc_html__( 'Try Pro Addon', 'wpnextpreviouslink' ) . '</a>';
			}
		}

		return $links_array;
	}//end plugin_row_meta

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			[
				'settings' => '<a style="color:#6648fe !important; font-weight: bold;" href="' . admin_url( 'options-general.php?page=wpnextpreviouslink' ) . '">' . esc_html__( 'Settings', 'wpnextpreviouslink' ) . '</a>',
			],
			$links
		);

	}//end of function add_action_links

	/**
	 * If we need to do something in upgrader process is completed
	 *
	 * @param $upgrader_object
	 * @param $options
	 */
	/*public function plugin_upgrader_process_complete( $upgrader_object, $options ) {
		if ( isset( $options['plugins'] ) && $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach ( $options['plugins'] as $each_plugin ) {
				if ( $each_plugin == WPNEXTPREVIOUSLINK_BASE_NAME ) {
					set_transient( 'wpnextpreviouslink_upgraded_notice', 1 );
					break;
				}
			}
		}
	}//end plugin_upgrader_process_complete*/

	/**
	 * If we need to do something in upgrader process is completed
	 *
	 */
	public function plugin_upgrader_process_complete() {
		$saved_version = get_option( 'wpnextpreviouslink_version' );

		if ( $saved_version === false || version_compare( $saved_version, WPNEXTPREVIOUSLINK_VERSION, '<' ) ) {
			set_transient( 'wpnextpreviouslink_upgraded_notice', 1 );

			// Update the saved version
			update_option( 'wpnextpreviouslink_version', WPNEXTPREVIOUSLINK_VERSION );
		}
	}//end plugin_upgrader_process_complete

	/**
	 * Show a notice to anyone who has just installed the plugin for the first time
	 * This notice shouldn't display to anyone who has just updated this plugin
	 */
	public function plugin_activate_upgrade_notices() {
		$activation_notice_shown = false;

		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'wpnextpreviouslink_activated_notice' ) ) {
			$activation_notice_shown = true;

			echo '<div style="border-left-color:#6648fe;" class="notice notice-success is-dismissible">';
			/* translators: %s. Core plugin's version */
			echo '<p><img style="float: left; display: inline-block; margin-right: 15px;" src="' . esc_url( WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/images/icon_48.png' ) . '" />' . sprintf( wp_kses( __( 'Thanks for installing/deactivating <strong>CBX Next Previous Article</strong> V%s - Codeboxr Team', 'wpnextpreviouslink' ), [ 'strong' => [] ] ), esc_attr( WPNEXTPREVIOUSLINK_VERSION ) ) . '</p>'; //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
			/* translators: 1. Plugin setting url 2. Documentation url */
			echo '<p>' . sprintf( wp_kses( __( 'Check <a href="%1$s">Plugin Setting</a> | <a target="_blank" href="%2$s" target="_blank">Documentation</a>', 'wpnextpreviouslink' ), [
					'a' => [
						'href'   => [],
						'target' => []
					]
				] ), esc_url( admin_url( 'options-general.php?page=wpnextpreviouslink' ) ), 'https://codeboxr.com/product/show-next-previous-article-for-wordpress?utm_source=clientdashboard&utm_medium=clientclick&utm_campaign=cdwordpress' ) . '</p>';
			echo '</div>';


			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'wpnextpreviouslink_activated_notice' );

			$this->pro_addon_compatibility_campaign();

		}

		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'wpnextpreviouslink_upgraded_notice' ) ) {
			if ( ! $activation_notice_shown ) {
				echo '<div style="border-left-color:#6648fe;" class="notice notice-success is-dismissible">';
				/* translators: %s. Core plugin's version */
				echo '<p><img style="float: left; display: inline-block; margin-right: 15px;" src="' . esc_url( WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/images/icon_48.png' ) . '" />' . sprintf( wp_kses( __( 'Thanks for upgrading <strong>CBX Next Previous Article</strong> V%s , enjoy the new features and bug fixes - Codeboxr Team', 'wpnextpreviouslink' ), [ 'strong' => [] ] ), esc_attr( WPNEXTPREVIOUSLINK_VERSION ) ) . '</p>'; //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
				/* translators: 1. Plugin setting url 2. Documentation url */
				echo '<p>' . sprintf( wp_kses( __( 'Check <a href="%1$s">Plugin Setting</a> | <a target="_blank" href="%2$s" target="_blank">Documentation</a>', 'wpnextpreviouslink' ), [
						'a' => [
							'href'   => [],
							'target' => []
						]
					] ), esc_url( admin_url( 'options-general.php?page=wpnextpreviouslink' ) ), 'https://codeboxr.com/product/show-next-previous-article-for-wordpress?utm_source=clientdashboard&utm_medium=clientclick&utm_campaign=cdwordpress' ) . '</p>';
				echo '</div>';

				$this->pro_addon_compatibility_campaign();
			}

			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'wpnextpreviouslink_upgraded_notice' );
		}
	}//end plugin_activate_upgrade_notices

	/**
	 * Check plugin compatibility and pro addon install campaign
	 */
	public function pro_addon_compatibility_campaign() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		//if the pro addon is active or installed
		if ( in_array( 'wpnextpreviouslinkaddon/wpnextpreviouslinkaddon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'WPNEXTPREVIOUSLINKADDON_PLUGIN_NAME' ) ) {
			//plugin is activated

			$pro_addon_version = WPNEXTPREVIOUSLINKADDON_VERSION;

			if ( version_compare( $pro_addon_version, '2.0.4', '<' ) ) {
				echo '<div style="border-left-color:#6648fe;" class="notice notice-success is-dismissible">';
				echo '<p>' . esc_html__( 'CBX Next Previous Article Pro Addon V2.0.4 or later required to work with the current version core plugin  CBX Next Previous Article.', 'wpnextpreviouslink' ) . '</p>';
				/* translators: %s. Pro addon external link */
				echo '<p>' . sprintf( wp_kses( __( 'Please update <a target="_blank" href="%s">CBX Next Previous Article Pro Addon</a> to version 2.0.4 or later  - Codeboxr Team', 'wpnextpreviouslink' ), [
						'a' => [
							'href'   => [],
							'target' => []
						]
					] ), 'https://codeboxr.com/product/show-next-previous-article-for-wordpress?utm_source=clientdashboard&utm_medium=clientclick&utm_campaign=cdwordpress' ) . '</p>';
				echo '</div>';
			}
		} else {
			/* translators: %s. Pro addon external link */
			echo '<div style="border-left-color:#6648fe;" class="notice notice-success is-dismissible"><p>' . sprintf( wp_kses( __( '<a target="_blank" href="%s">CBX Next Previous Article Pro Addon</a> has extended features and settings. try it  - Codeboxr Team', 'wpnextpreviouslink' ), [
					'a' => [
						'href'   => [],
						'target' => []
					]
				] ), 'https://codeboxr.com/product/show-next-previous-article-for-wordpress?utm_source=clientdashboard&utm_medium=clientclick&utm_campaign=cdwordpress' ) . '</p></div>';
		}
	}//end pro_addon_compatibility_campaign


	/**
	 * Show plugin update
	 *
	 * @param $plugin_file
	 * @param $plugin_data
	 *
	 * @return void
	 */
	public function custom_message_after_plugin_row_proaddon( $plugin_file, $plugin_data ) {
		if ( $plugin_file !== 'wpnextpreviouslinkaddon/wpnextpreviouslinkaddon.php' ) {
			return;
		}



		$pro_addon_version = WPNextPreviousLinkHelper::get_any_plugin_version( 'wpnextpreviouslinkaddon/wpnextpreviouslinkaddon.php' );
		$compared_version = '2.0.8';
		$need_update        = false;

		if ( defined( 'WPNEXTPREVIOUSLINKADDON_VERSION' ) ) {
			//of pro addon is too old and doesn't have the update checker implemented
			if(version_compare( $pro_addon_version, '2.0.7', '<' )){
				$compared_version = '2.0.7';
				$need_update = true;
			}
		}
		else{
			if ( $pro_addon_version != '' && version_compare( $pro_addon_version, $compared_version, '<' ) ) {
				$need_update = true;
			}
		}


		if ( $need_update) {
			$plugin_manual_update = 'https://codeboxr.com/manual-update-pro-addon/';

			/* translators:translators: %s: plugin setting url for licence */
			$custom_message = wp_kses( sprintf( __( '<strong>Note:</strong> CBX Next Previous Article Pro Addon is custom plugin. This plugin can not be auto update from dashboard/plugin manager. For manual update please check <a target="_blank" href="%1$s">documentation</a>. <strong style="color: red;">It seems this plugin\'s current version is older than %2$s . To get the latest pro addon features, this plugin needs to upgrade to %2$s or later.</strong>', 'wpnextpreviouslink' ), esc_url( $plugin_manual_update ), $compared_version ), [
				'strong' => [ 'style' => [] ],
				'a'      => [
					'href'   => [],
					'target' => []
				]
			] );

			// Output a row with custom content
			echo '<tr class="plugin-update-tr">
            <td colspan="3" class="plugin-update colspanchange">
                <div class="notice notice-warning inline">
                    ' . wp_kses_post( $custom_message ) . '
                </div>
            </td>
          </tr>';
		}
	}//end method custom_message_after_plugin_row_proaddon

	/**
	 * Load setting html
	 *
	 * @return void
	 * @since 2.7.1
	 */
	public function settings_reset_load() {
		//security check
		check_ajax_referer( 'settingsnonce', 'security' );

		$msg            = [];
		$msg['html']    = '';
		$msg['message'] = esc_html__( 'Next Previous Link reset setting html loaded successfully', 'wpnextpreviouslink' );
		$msg['success'] = 1;

		if ( ! current_user_can( 'manage_options' ) ) {
			$msg['message'] = esc_html__( 'Sorry, you don\'t have enough permission', 'wpnextpreviouslink' );
			$msg['success'] = 0;
			wp_send_json( $msg );
		}

		$msg['html'] = WPNextPreviousLinkHelper::setting_reset_html_table();

		wp_send_json( $msg );
	}//end method settings_reset_load

	/**
	 * Reset plugin data
	 */
	public function plugin_reset() {
		//security check
		check_ajax_referer( 'settingsnonce', 'security' );

		$url = admin_url( 'options-general.php?page=wpnextpreviouslink' );

		$msg            = [];
		$msg['message'] = esc_html__( 'Next Previous Link setting reset scheduled successfully', 'wpnextpreviouslink' );
		$msg['success'] = 1;
		$msg['url']     = $url;

		if ( ! current_user_can( 'manage_options' ) ) {
			$msg['message'] = esc_html__( 'Sorry, you don\'t have enough permission', 'wpnextpreviouslink' );
			$msg['success'] = 0;
			wp_send_json( $msg );
		}


		do_action( 'wpnextpreviouslink_plugin_reset_before' );

		global $wpdb;

		$plugin_resets = $_POST;

		//delete options
		do_action( 'wpnextpreviouslink_plugin_options_deleted_before' );

		$reset_options = isset( $plugin_resets['reset_options'] ) ? $plugin_resets['reset_options'] : [];
		$option_values = ( is_array( $reset_options ) && sizeof( $reset_options ) > 0 ) ? array_values( $reset_options ) : array_values( WPNextPreviousLinkHelper::getAllOptionNamesValues() );

		foreach ( $option_values as $key => $option ) {
			do_action( 'wpnextpreviouslink_plugin_option_delete_before', $option );
			delete_option( $option );
			do_action( 'wpnextpreviouslink_plugin_option_delete_after', $option );
		}

		do_action( 'wpnextpreviouslink_plugin_options_deleted_after' );
		do_action( 'wpnextpreviouslink_plugin_reset_after' );
		do_action( 'wpnextpreviouslink_plugin_reset' );
		wp_send_json( $msg );
	}//end method plugin_reset
}//end class WPNextPreviousLinkAdmin