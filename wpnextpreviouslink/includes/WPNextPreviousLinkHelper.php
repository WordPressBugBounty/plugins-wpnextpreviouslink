<?php

/**
 * The file that defines the core plugin helper class
 *
 * This class has some static helper methods
 *
 * @link       codeboxr.com
 * @since      1.0.0
 *
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/includes
 */

/**
 * The core plugin helper class.
 *
 * This is used to define static methods
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/includes
 * @author     CBX Team  <info@codeboxr.com>
 */
class WPNextPreviousLinkHelper {
	/**
	 * Is the frontpage a page ?
	 *
	 * @return bool
	 */
	public static function is_page_front_page() {
		if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) && is_page( get_option( 'page_on_front' ) ) ) {
			return true;
		}

		return false;
	}//end is_page_front_page

	/**
	 * If is home or if frontend is a page
	 *
	 * @return bool
	 */
	public static function is_home_or_frontpage_page() {
		if ( is_home() || WPNextPreviousLinkHelper::is_page_front_page() ) {
			return true;
		} else {
			return false;
		}
	}//end is_home

	/**
	 * Add utm params to any url
	 *
	 * @param  string  $url
	 *
	 * @return string
	 */
	public static function url_utmy( $url = '' ) {
		if ( $url == '' ) {
			return $url;
		}

		$url = add_query_arg( [
			'utm_source'   => 'plgsidebarinfo',
			'utm_medium'   => 'plgsidebar',
			'utm_campaign' => 'wpfreemium',
		], $url );

		return $url;
	}//end url_utmy

	/**
	 * Return the key value pair of posttypes
	 *
	 * @param $all_post_types
	 * @since 2.6.4
	 * @return array
	 */
	public static function get_post_types_formatted( $all_post_types ) {
		$posts_defination = [];

		foreach ( $all_post_types as $key => $post_type_defination ) {
			foreach ( $post_type_defination as $post_type_type => $data ) {
				if ( $post_type_type == 'label' ) {
					$opt_grouplabel = $data;
				}

				if ( $post_type_type == 'types' ) {
					foreach ( $data as $opt_key => $opt_val ) {
						$posts_defination[ $opt_grouplabel ][ $opt_key ] = $opt_val;
					}
				}
			}
		}

		return $posts_defination;
	}//end get_post_types_formatted

	/**
	 * Get All(default and custom) Post types
	 *
	 * @return array
	 * @since    2.6.4
	 *
	 */
	public static function get_post_types() {
		$output    = 'objects'; // names or objects, note names is the default
		$operator  = 'and';     // 'and' or 'or'
		$postTypes = [];

		$post_type_args = [
			'builtin' => [
				'options' => [
					'public'   => true,
					'_builtin' => true,
					'show_ui'  => true,
				],
				'label'   => esc_html__( 'Built in post types', 'wpnextpreviouslink' ),
			],
		];

		$post_type_args = apply_filters( 'wpnp_supported_posttypes', $post_type_args );

		foreach ( $post_type_args as $postArgType => $postArgTypeArr ) {
			$types = get_post_types( $postArgTypeArr['options'], $output, $operator );

			if ( ! empty( $types ) ) {
				foreach ( $types as $type ) {
					$postTypes[ $postArgType ]['label']                = $postArgTypeArr['label'];
					$postTypes[ $postArgType ]['types'][ $type->name ] = $type->labels->name;
				}
			}
		}

		return $postTypes;
	}//end get_post_types

	/**
	 * Set settings fields
	 *
	 * @return type array
	 */
	public static function get_settings_sections() {
		$sections = [
			[
				'id'    => 'wpnextpreviouslink_basics',
				'title' => esc_html__( 'Plugin Options', 'wpnextpreviouslink' ),
			],
			[
				'id'    => 'wpnextpreviouslink_postcats',
				'title' => esc_html__( 'Navigate By Taxonomy', 'wpnextpreviouslink' ),
			],
			[
				'id'    => 'wpnextpreviouslink_postorders',
				'title' => esc_html__( 'Order By Post Type', 'wpnextpreviouslink' ),
			],
			[
				'id'    => 'wpnextpreviouslink_ga',
				'title' => esc_html__( 'Google Analytics', 'wpnextpreviouslink' ),
			],
			[
				'id'    => 'wpnextpreviouslink_tools',
				'title' => esc_html__( 'Tools', 'wpnextpreviouslink' ),
			],
		];

		return apply_filters( 'wpnp_setting_sections', $sections );
	}//end method get_settings_sections

	/**
	 * List all global option name with prefix wpnextpreviouslink
	 *
	 * @since 2.7.0
	 */
	public static function getAllOptionNames() {
		global $wpdb;
		$prefix       = 'wpnextpreviouslink_';

		$wild = '%';
		$like = $wpdb->esc_like( $prefix ) . $wild;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$option_names = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", $like), ARRAY_A );

		return apply_filters( 'wpnextpreviouslink_option_names', $option_names );
	}//end method getAllOptionNames

	/**
	 * Options name only
	 *
	 * @return array
	 * @since 2.7.1
	 */
	public static function getAllOptionNamesValues()
	{
		$option_values = self::getAllOptionNames();
		$names_only    = [];

		foreach ($option_values as $key => $value) {
			$names_only[] = $value['option_name'];
		}

		return $names_only;
	}//end method getAllOptionNamesValues


	/**
	 * Return setting fields
	 *
	 * @return void
	 * @since 2.7.1
	 */
	public static function wpnextpreviouslink_settings_fields() {
		$table_html = '<div id="wpnextpreviouslink_resetinfo_wrap">'.esc_html__('Loading ...', 'wpnextpreviouslink').'</div>';

		//get default values for basic settings
		$settings = new WPNextPreviousLink_Settings_API();

		$wpnp_arrow_type_options = $wpnp_image_type_options = [];
		$wpnp_saved_image_type   = $settings->get_field( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' );
		$post_types              = WPNextPreviousLinkHelper::get_post_types();
		$posts_definition        = WPNextPreviousLinkHelper::get_post_types_formatted( $post_types );//get supported post types


		$wpnp_post_to_show = apply_filters( 'wpnp_post_to_show',
			[
				'1' => esc_html__( 'Previous', 'wpnextpreviouslink' ),
				'2' => esc_html__( 'Next', 'wpnextpreviouslink' ),
			] );//which post to show

		$wpnp_arrow_types = self::wpnextprevios_arrow_type();//get supported arrow types
		$wpnp_image_types = self::wpnextprevios_image_type();//get supported image types


		//arrange options for arrow types
		foreach ( $wpnp_arrow_types as $key => $value ) {
			$wpnp_arrow_type_options[ $key ] = $value;
		}

		//arrange options for image types
		foreach ( $wpnp_image_types as $key => $value ) {
			$wpnp_image_type_options[ $key ] = $value;
		}


		$wpnp_link_img_src_p = plugins_url( 'assets/images/l_' . $settings->get_field( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' ) . '.png', dirname( __FILE__ ) );
		$wpnp_link_img_src_n = plugins_url( 'assets/images/r_' . $settings->get_field( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' ) . '.png', dirname( __FILE__ ) );

		$wpnp_link_img_src_p = apply_filters( 'wpnp_showleftimg', $wpnp_link_img_src_p, $wpnp_saved_image_type );
		$wpnp_link_img_src_n = apply_filters( 'wpnp_showrightimg', $wpnp_link_img_src_n, $wpnp_saved_image_type );

		$settings_fields = [
			'wpnextpreviouslink_basics'   => [
				[
					'name'    => 'basic_fields_heading',
					'label'   => esc_html__( 'Basic Settings', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],
				[
					'name'    => 'wpnp_style_top',
					'label'   => esc_html__( 'Vertical Position', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Vertical position of arrow or thumb', 'wpnextpreviouslink' ),
					'type'    => 'number',
					'default' => 50,
					'size'    => '20'
				],
				[
					'name'              => 'wpnp_z_index',
					'label'             => esc_html__( 'Z Index', 'wpnextpreviouslink' ),
					'desc'              => esc_html__( 'CSS style z index value', 'wpnextpreviouslink' ),
					'type'              => 'number',
					'default'           => 1,
					'sanitize_callback' => 'absint'
				],
				[
					'name'    => 'wpnp_unit_type',
					'label'   => esc_html__( 'Vertical Position Type', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Vertical position type px or %', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => '%',
					'options' => [ '%' => '%', 'px' => 'px' ]
				],
				[
					'name'    => 'wpnp_skip_ids',
					'label'   => esc_html__( 'Skip Posts', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Post/page/custom post type ids as comma to skip display the next prev', 'wpnextpreviouslink' ),
					'type'    => 'text',
					'default' => '',

				],
				[
					'name'    => 'wpnp_new_window', //same for this variable
					'label'   => esc_html__( 'Open Link Target', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Link open in same window or new window', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 0,
					'options' => [
						'0' => esc_html__( 'Same Window', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'New Window/Tab', 'wpnextpreviouslink' )
					],
				],
				[
					'name'    => 'display_mode_archive_heading',
					'label'   => esc_html__( 'Archive Display Settings', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],
				[
					'name'    => 'wpnp_show_home',
					'label'   => esc_html__( 'Show in Home page', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show in home page or not. Default "Yes"', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_show_archive',
					'label'   => esc_html__( 'Show in Archive', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show in Archive View(Category, Tag, Author, Date etc) or not. Default "Yes"', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_show_category',
					'label'   => esc_html__( 'Show in Category View', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show in Category View or not. Default "Yes". If hidden for archive view then hidden for category view.', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_show_tag',
					'label'   => esc_html__( 'Show in Tag View', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show in Tag View or not. Default "Yes".  If hidden for archive view then hidden for tag view.', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_show_author',
					'label'   => esc_html__( 'Show in Author View', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show in Author View or not. Default "Yes".  If hidden for archive view then hidden for author view.', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_show_date',
					'label'   => esc_html__( 'Show in Date View', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show in Date View or not. Default "Yes".  If hidden for archive view then hidden for date view.', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_same_cat',
					'label'   => esc_html__( 'Navigate by Taxonomy', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Navigate by Category/taxonomy or not. Default "No".', 'wpnextpreviouslink' ) . ' <a href="#" id="wpnp_same_cat_more">' . esc_html__( 'Check more option about this feature', 'wpnextpreviouslink' ) . '</a>',
					'type'    => 'radio',
					'default' => 0,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					]
				],
				[
					'name'    => 'wpnp_show_post_archive', //this variable name is very confusing
					'label'   => esc_html__( 'Show Both arrow in archive', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show both left and right for any archive mode for arrow style', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					],
				],
				[
					'name'    => 'display_mode_single_heading',
					'label'   => esc_html__( 'Single Article Display Settings', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],
				[
					'name'     => 'wpnp_show_posttypes',
					'label'    => esc_html__( 'Post Type Selection', 'wpnextpreviouslink' ),
					'desc'     => esc_html__( 'Post Type Selection', 'wpnextpreviouslink' ),
					'type'     => 'select',
					'multi'    => true,
					'optgroup' => 1,
					'default'  => [ 'post', 'page' ],
					'options'  => $posts_definition,
				],
				[
					'name'    => 'wpnp_show_post',
					'label'   => esc_html__( 'Show Post', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show which post to be appeared', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => $wpnp_post_to_show,
				],

				[
					'name'    => 'wpnp_show_post_single', //same for this variable
					'label'   => esc_html__( 'Show Both arrow', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Show both left and right for any details mode for arrow style', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => 1,
					'options' => [
						'0' => esc_html__( 'No', 'wpnextpreviouslink' ),
						'1' => esc_html__( 'Yes', 'wpnextpreviouslink' )
					],
				],
				[
					'name'    => 'wpnp_arrow_type',
					'label'   => esc_html__( 'Next Prev Style Type', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Type of the image or content for next prev style', 'wpnextpreviouslink' ),
					'type'    => 'radio',
					'default' => '0',
					'options' => $wpnp_image_type_options
				],
				[
					'name'    => 'arrow_style_heading',
					'label'   => esc_html__( 'Arrow Style Settings', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],

				[
					'name'    => 'wpnp_image_name',
					'label'   => esc_html__( 'Arrow Style', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Arrow style of the next prev link.', 'wpnextpreviouslink' ),
					'type'    => 'select',
					'size'    => 'wpnp_image_name',
					'default' => 'arrow',
					'options' => $wpnp_arrow_type_options
				],
				// phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
				[
					'name'  => 'wpnp_display_image',
					'label' => esc_html__( 'Arrow Preview', 'wpnextpreviouslink' ),
					//
					'desc'  => '<div style="margin-top:10px;" id="wpnp_next_previous" >
                                         <img style="width: 32px; height: auto;" id="wpnp_previousimg" src="' . esc_url($wpnp_link_img_src_p) . '" alt="' . esc_html__( 'Prev Preview Image(Width: 32px, Height: auto)', 'wpnextpreviouslink' ) . ' " title="' . esc_html__( 'Prev Preview Image(Width: 32px, Height: auto)', 'wpnextpreviouslink' ) . ' " /> 
                                         <img style="width: 32px; height: auto; margin-left: 50px;" id="wpnp_nextimg" src="' . esc_url($wpnp_link_img_src_n) . '" alt="' . esc_html__( 'Next Preview Image(Width: 32px, Height: auto)', 'wpnextpreviouslink' ) . ' " title="' . esc_html__( 'Next Preview Image(Width: 32px, Height: auto)', 'wpnextpreviouslink' ) . ' " />
                                </div>',
					'type'  => 'info',
				],
				// phpcs:enable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage

			],
			'wpnextpreviouslink_postcats' => [
				[
					'name'    => 'navigate_postcats_heading',
					'label'   => esc_html__( 'Post type specific taxonomy navigation', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],
				[
					'name'    => 'navigate_postcats',
					'label'   => esc_html__( 'Post Type Taxonomy Bindings', 'wpnextpreviouslink' ),
					'type'    => 'postcatbinding',
					'default' => [
						'post' => 'category'
					],
				],
			],
			'wpnextpreviouslink_postorders' => [
				[
					'name'    => 'navigate_postorders_heading',
					'label'   => esc_html__( 'Next prev order by each post type', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],
				[
					'name'    => 'navigate_postorders',
					'label'   => esc_html__( 'Navigation order by post type', 'wpnextpreviouslink' ),
					'type'    => 'posttypebinding',
					'default' => [
						'post' => 'date',
						'page' => 'date'
					],
				],
			],
			'wpnextpreviouslink_ga'       => [
				[
					'name'    => 'wpnp_ga_enabled',
					'label'   => esc_html__( 'Google Enalytics Trackings', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Enable/Disable. You must have google analytics added to your website to use this feature.', 'wpnextpreviouslink' ),
					'type'    => 'checkbox',
					'default' => 'off',
				],
				[
					'name'    => 'wpnp_ga_track_views',
					'label'   => esc_html__( 'Track Views', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Enable/Disable . Track next prev display views.', 'wpnextpreviouslink' ),
					'type'    => 'checkbox',
					'default' => 'on',
				],
				[
					'name'    => 'wpnp_ga_track_clicks',
					'label'   => esc_html__( 'Track Clicks', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Enable/Disable. Track next prev link clicks.', 'wpnextpreviouslink' ),
					'type'    => 'checkbox',
					'default' => 'on',
				],
				[
					'name'    => 'wpnp_ga_track_pbr',
					'label'   => esc_html__( 'Prevent Bounce Rate', 'wpnextpreviouslink' ),
					'desc'    => esc_html__( 'Enable/Disable. Turn it on to indicate that the event hit will not be used in bounce-rate calculation.', 'wpnextpreviouslink' ),
					'type'    => 'checkbox',
					'default' => 'on',
				],
			],
			'wpnextpreviouslink_tools'    => [
				[
					'name'    => 'tools_heading',
					'label'   => esc_html__( 'Tools Settings', 'wpnextpreviouslink' ),
					'type'    => 'heading',
					'default' => '',
				],
				[
					'name'    => 'delete_global_config',
					'label'   => esc_html__( 'On Uninstall delete plugin data', 'wpnextpreviouslink' ),
					'desc'    => '<p>' . esc_html__( 'Delete Global Config data and custom table created by this plugin on uninstall.', 'wpnextpreviouslink' ) . '</p>' . '<p><strong>' . esc_html__( 'Please note that this process can not be undone and it is recommended to keep full database backup before doing this.', 'wpnextpreviouslink' ) . '</strong></p>',
					'type'    => 'radio',
					'options' => [
						'yes' => esc_html__( 'Yes', 'wpnextpreviouslink' ),
						'no'  => esc_html__( 'No', 'wpnextpreviouslink' ),
					],
					'default' => 'no'
				],
				[
					'name'    => 'reset_data',
					'label'   => esc_html__( 'Reset all data', 'wpnextpreviouslink' ),
					'desc'    => $table_html.'<p>'.esc_html__('Reset option values and all tables created by this plugin', 'wpnextpreviouslink').'<a data-busy="0" class="button secondary ml-20" id="reset_data_trigger"  href="#">'.esc_html__('Reset Data', 'wpnextpreviouslink').'</a></p>',
					'type'    => 'html',
					'default' => 'off'
				]
			]
		];

		$settings_fields = apply_filters( 'wpnp_setting_fields', $settings_fields );

		return $settings_fields;
	}//end method wpnextpreviouslink_settings_fields

	/**
	 * Get The Available Arrow type for the link
	 *
	 * @return array
	 * @since 2.7.1
	 */
	public static function wpnextprevios_arrow_type() {
		$arrow_types = [
			'arrow'        => esc_html__( 'Classic', 'wpnextpreviouslink' ),
			'arrow_blue'   => esc_html__( 'Blue', 'wpnextpreviouslink' ),
			'arrow_dark'   => esc_html__( 'Dark', 'wpnextpreviouslink' ),
			'arrow_green'  => esc_html__( 'Green', 'wpnextpreviouslink' ),
			'arrow_orange' => esc_html__( 'Orange', 'wpnextpreviouslink' ),
			'arrow_red'    => esc_html__( 'Red', 'wpnextpreviouslink' ),
		];

		return apply_filters( 'wpnp_arrow_options', $arrow_types );
	}//end wpnextprevios_arrow_type

	/**
	 * Get The Available Image type for the link
	 *
	 * @return array
	 * @since 2.7.1
	 */
	public static function wpnextprevios_image_type() {

		$image_types = [ '0' => 'Arrow' ];

		return apply_filters( 'wpnp_image_options', $image_types );
	}//end wpnextprevios_image_type

	/**
	 * Plugin reset html table
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public static function setting_reset_html_table() {
		$option_values = WPNextPreviousLinkHelper::getAllOptionNames();


		$table_html = '<div id="wpnextpreviouslink_resetinfo">';

		$table_html .= '<p style="margin-bottom: 15px;" id="wpnextpreviouslink_plg_gfig_info"><strong>' . esc_html__( 'Following option values created by this plugin(including addon) from WordPress core option table', 'wpnextpreviouslink' ) . '</strong></p>';
		$table_html .= '<p style="margin-bottom: 10px;" class="grouped gapless grouped_buttons" id="wpnextpreviouslink_setting_options_check_actions"><a href="#" class="button primary wpnextpreviouslink_setting_options_check_action_call">' . esc_html__( 'Check All', 'wpnextpreviouslink' ) . '</a><a href="#" class="button outline wpnextpreviouslink_setting_options_check_action_ucall">' . esc_html__( 'Uncheck All', 'wpnextpreviouslink' ) . '</a></p>';

		$table_html .= '<table class="widefat widethin wpnextpreviouslink_table_data">
	<thead>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'wpnextpreviouslink' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'wpnextpreviouslink' ) . '</th>		
	</tr>
	</thead>';

		$table_html .= '<tbody>';

		$i = 0;
		foreach ( $option_values as $key => $value ) {
			$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
			$i ++;

			$table_html .= '<tr class="'.esc_attr($alternate_class).'">
									<td class="row-title"><input checked class="magic-checkbox reset_options" type="checkbox" name="reset_options['.$value['option_name'].']" id="reset_options_'.esc_attr($value['option_name']).'" value="'.$value['option_name'].'" />
  <label for="reset_options_'.esc_attr($value['option_name']).'">'.esc_attr($value['option_name']).'</td>
									<td>'.esc_attr($value['option_id']).'</td>									
								</tr>';
		}

		$table_html .= '</tbody>';
		$table_html .= '<tfoot>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'wpnextpreviouslink' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'wpnextpreviouslink' ) . '</th>				
	</tr>
	</tfoot>
</table>';


		$table_html .= '</div>';

		return $table_html;
	}//end method setting_reset_html_table

	/**
	 * Next prev post type order
	 *
	 * @return void
	 */
	public static function post_type_orders_by($post_type = '') {
		return apply_filters('wpnextpreviouslink_post_type_orders_by', [
			'date' => esc_attr__('Date(default in wordpress core)', 'wpnextpreviouslink'),
		], $post_type);
	}//end method post_type_orders_by

	/**
	 * Kses wysiwyg html
	 *
	 * @param  string  $html
	 *
	 * @return mixed|string
	 *
	 * @since 3.0.9
	 *
	 */
	public static function sanitize_wp_kses( $html = '' ) {
		return wp_kses( $html, WPNextPreviousLinkHelper::allowedHtmlTags() );
	}//end method sanitize_wp_kses

	/**
	 * HTML elements, attributes, and attribute values will occur in your output
	 *
	 * @return array
	 * @since 3.0.9
	 *
	 */
	public static function allowedHtmlTags() {
		$allowed_html_tags = [
			'a'      => [
				'href'  => [],
				'title' => [],
				//'class' => array(),
				//'data'  => array(),
				//'rel'   => array(),
			],
			'br'     => [],
			'em'     => [],
			'ul'     => [//'class' => array(),
			],
			'ol'     => [//'class' => array(),
			],
			'li'     => [//'class' => array(),
			],
			'strong' => [],
			'p'      => [
				//'class' => array(),
				//'data'  => array(),
				//'style' => array(),
			],
			'span'   => [
				//					'class' => array(),
				//'style' => array(),
			],
		];

		return apply_filters( 'wpnextpreviouslink_allowed_html_tags', $allowed_html_tags );
	}//end method allowedHtmlTags

	/**
	 * Get any plugin version number
	 *
	 * @param $plugin_slug
	 *
	 * @return mixed|string
	 */
	public static function get_any_plugin_version( $plugin_slug = '' ) {
		if ( $plugin_slug == '' ) {
			return '';
		}

		// Ensure the required file is loaded
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get all installed plugins
		$all_plugins = get_plugins();

		// Check if the plugin exists
		if ( isset( $all_plugins[ $plugin_slug ] ) ) {
			return $all_plugins[ $plugin_slug ]['Version'];
		}

		// Return false if the plugin is not found
		return '';
	}//end method get_pro_addon_version
}//end class WPNextPreviousLinkHelper