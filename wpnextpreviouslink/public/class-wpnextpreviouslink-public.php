<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 * @package    WPNextPreviousLink
 * @subpackage WPNextPreviousLink/public
 * @author     Codeboxr <info@codeboxr.com>
 */
class WPNextPreviousLink_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;
	//for settings

	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param  string  $plugin_name  The name of the plugin.
	 * @param  string  $version  The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->version = current_time( 'timestamp' ); //for development time only
		}

		$this->settings_api = new WPNextPreviousLink_Settings_API();
	}//end constructor

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$css_url_part     = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/css/';
		$js_url_part      = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/js/';
		$vendors_url_part = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/vendors/';

		$css_path_part     = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/css/';
		$js_path_part      = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/js/';
		$vendors_path_part = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/vendors/';

		wp_register_style( 'wpnextpreviouslink-public', $css_url_part . 'wpnextpreviouslink-public.css', [], $this->version, 'all' );
		wp_enqueue_style( 'wpnextpreviouslink-public' );
	}//end enqueue_styles

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$css_url_part     = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/css/';
		$js_url_part      = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/js/';
		$vendors_url_part = WPNEXTPREVIOUSLINK_ROOT_URL . 'assets/vendors/';

		$css_path_part     = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/css/';
		$js_path_part      = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/js/';
		$vendors_path_part = WPNEXTPREVIOUSLINK_ROOT_PATH . 'assets/vendors/';

		$settings = $this->settings_api;

		$wpnp_ga_enabled      = $settings->get_option( 'wpnp_ga_enabled', 'wpnextpreviouslink_ga', 'off' );
		$wpnp_ga_track_views  = $settings->get_option( 'wpnp_ga_track_views', 'wpnextpreviouslink_ga', 'on' );
		$wpnp_ga_track_clicks = $settings->get_option( 'wpnp_ga_track_clicks', 'wpnextpreviouslink_ga', 'on' );
		$wpnp_ga_track_pbr    = $settings->get_option( 'wpnp_ga_track_pbr', 'wpnextpreviouslink_ga', 'on' );

		$translation_public_vars = [
			'ga_enable'   => ( $wpnp_ga_enabled === 'on' ) ? 1 : 0,
			'track_view'  => ( $wpnp_ga_track_views === 'on' ) ? 1 : 0,
			'track_click' => ( $wpnp_ga_track_clicks === 'on' ) ? 1 : 0,
			'track_pbr'   => ( $wpnp_ga_track_pbr === 'on' ) ? 1 : 0,
		];


		wp_register_script( 'wpnextpreviouslink-public', $js_url_part . 'wpnextpreviouslink-public.js', [ 'jquery' ], $this->version, true );
		wp_localize_script( 'wpnextpreviouslink-public', 'wpnextpreviouslink_public', apply_filters( 'wpnextpreviouslink_public_vars', $translation_public_vars ) );
		//wp_enqueue_script( 'wpnextpreviouslink-public' );
	}//end method enqueue_scripts


	/**
	 * Apply next prev link on front end
	 */
	public function wordPress_next_previous_link() {
		global $style, $post;


		$settings    = $this->settings_api;
		$show_action = true;
		$show_action = apply_filters( 'wpnp_go_or_not', $show_action, $this ); //this may help in many ways

		if ( ! $show_action ) {
			return;
		}


		$show_home     = intval( $settings->get_option( 'wpnp_show_home', 'wpnextpreviouslink_basics', 1 ) );
		$show_archive  = intval( $settings->get_option( 'wpnp_show_archive', 'wpnextpreviouslink_basics', 1 ) );
		$show_category = intval( $settings->get_option( 'wpnp_show_category', 'wpnextpreviouslink_basics', 1 ) );
		$show_tag      = intval( $settings->get_option( 'wpnp_show_tag', 'wpnextpreviouslink_basics', 1 ) );
		$show_author   = intval( $settings->get_option( 'wpnp_show_author', 'wpnextpreviouslink_basics', 1 ) );
		$show_date     = intval( $settings->get_option( 'wpnp_show_date', 'wpnextpreviouslink_basics', 1 ) );

		//if is home and show on home is off
		if ( $this->is_home() && $show_home == 0 ) {
			return;
		}

		//condition to show/hide for archive views
		if ( ( ! $show_archive ) || ( ! $show_category && is_category() ) || ( ! $show_tag && is_tag() ) || ( ! $show_author && is_author() ) || ( ! $show_date && is_date() ) ) {
			return;
		}


		$left_image = $left_image_hover = $right_image = $right_image_hover = '';

		//set the default values to show fire in front end
		$image_name    = $settings->get_option( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' );
		$style_top     = intval( $settings->get_option( 'wpnp_style_top', 'wpnextpreviouslink_basics', 50 ) );
		$z_index       = intval( $settings->get_option( 'wpnp_z_index', 'wpnextpreviouslink_basics', 1 ) );
		$unit_type     = $settings->get_option( 'wpnp_unit_type', 'wpnextpreviouslink_basics', '%' );
		$wpnp_skip_ids = esc_attr( $settings->get_option( 'wpnp_skip_ids', 'wpnextpreviouslink_basics', '' ) );
		$wpnp_skip_ids = array_map( 'trim', explode( ',', $wpnp_skip_ids ) );
		$wpnp_skip_ids = array_map( 'absint', $wpnp_skip_ids );


		//for showing different type of arrow in front end
		$left_image = plugins_url( 'assets/images/l_' . $settings->get_option( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' ) . '.png', dirname( __FILE__ ) );
		$left_image = apply_filters( 'wpnp_showleftimg', $left_image, $image_name );

		$left_image_hover = plugins_url( 'assets/images/l_' . $settings->get_option( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' ) . '_hover.png', dirname( __FILE__ ) );
		$left_image_hover = apply_filters( 'wpnp_showleftimg_hover', $left_image_hover, $image_name );

		$right_image = plugins_url( 'assets/images/r_' . $settings->get_option( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' ) . '.png', dirname( __FILE__ ) );
		$right_image = apply_filters( 'wpnp_showrightimg', $right_image, $image_name );

		$right_image_hover = plugins_url( 'assets/images/r_' . $settings->get_option( 'wpnp_image_name', 'wpnextpreviouslink_basics', 'arrow' ) . '_hover.png', dirname( __FILE__ ) );
		$right_image_hover = apply_filters( 'wpnp_showrightimg_hover', $right_image_hover, $image_name );


		//
		$style = '<style>
        #wpnp_previous{
                    background-image: url(' . esc_url( $left_image ) . ') ;
                    top:' . esc_attr( $style_top ) . esc_attr( $unit_type ) . ';                   
                    z-index:' . intval( $z_index ) . ' !important;                   
                    }

        #wpnp_previous:hover{
                    background-image: url(' . $left_image_hover . ');
                    }

        #wpnp_next{
                    background-image: url(' . esc_url( $right_image ) . ') ;
                    top: ' . esc_attr( $style_top ) . esc_attr( $unit_type ) . ';
                    z-index:' . intval( $z_index ) . ' !important;                   
                    }
        #wpnp_next:hover{
                    background-image: url(' . $right_image_hover . ');
                    }
        </style>';

		echo $style;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$post_to_show         = intval( $settings->get_option( 'wpnp_show_post', 'wpnextpreviouslink_basics', 1 ) );         //show post next(2) or prev(1)
		$show_both_in_archive = intval( $settings->get_option( 'wpnp_show_post_archive', 'wpnextpreviouslink_basics', 1 ) ); //show both next and prev in archive
		$show_both_in_single  = intval( $settings->get_option( 'wpnp_show_post_single', 'wpnextpreviouslink_basics', 1 ) );  //show both next and prev in single
		$new_window           = intval( $settings->get_option( 'wpnp_new_window', 'wpnextpreviouslink_basics', 0 ) );        //link target


		$next_posts_html = '';
		$prev_posts_html = '';

		$next_post_html = '';
		$prev_post_html = '';


		if ( $this->is_page_front_page() || is_singular() ) {
			$same_cat       = intval( $settings->get_option( 'wpnp_same_cat', 'wpnextpreviouslink_basics', 0 ) );
			$same_post_cats = $settings->get_option( 'navigate_postcats', 'wpnextpreviouslink_postcats', [] );

			if ( ! is_array( $same_post_cats ) ) {
				$same_post_cats = [
					'post' => 'category'
				];
			}


			$post_type = get_post_type();
			$post_id   = get_the_ID();
			if ( in_array( $post_id, $wpnp_skip_ids ) ) {
				return;
			}


			$same_cat_do       = false;
			$same_cat_category = 'category';
			if ( $same_cat && isset( $same_post_cats[ $post_type ] ) && $same_post_cats[ $post_type ] != '' ) {
				$same_cat_do       = true;
				$same_cat_category = $same_post_cats[ $post_type ];
			}

			//$same_cat_do = ($same_cat)? true : false;

			$exclude_terms = '';

			$format    = '%link';
			$link_prev = '<span id="wpnp_previous" class="wpnp_previous_' . $image_name . '"> &larr; %title</span>';
			$link_next = '<span id="wpnp_next" class="wpnp_next_' . $image_name . '"> &larr; %title</span>';

			//any kind of single post or details post
			$prev_post_html = get_previous_post_link( $format, $link_prev, $same_cat_do, $exclude_terms, $same_cat_category ); // will return html link
			$next_post_html = get_next_post_link( $format, $link_next, $same_cat_do, $exclude_terms, $same_cat_category );     //will return html link

			$prev_post_html = apply_filters( 'wpnextpreviouslink_prev_post_html', $prev_post_html, $format, $link_prev, $same_cat_do, $exclude_terms, $same_cat_category );
			$next_post_html = apply_filters( 'wpnextpreviouslink_next_post_html', $next_post_html, $format, $link_next, $same_cat_do, $exclude_terms, $same_cat_category );


			$next_post_html = $this->adjust_link_html( $next_post_html, 'next', $new_window );
			$prev_post_html = $this->adjust_link_html( $prev_post_html, 'previous', $new_window );

		} else {
			//archive view
			$next_posts_html = get_next_posts_link( '<span id="wpnp_next" class="wpnp_next_' . $image_name . '">&larr;</span>' );
			$prev_posts_html = get_previous_posts_link( '<span id="wpnp_previous" class="wpnp_previous_' . $image_name . '">&rarr;</span>' );

			$next_posts_html = $this->adjust_link_html( $next_posts_html, 'next', $new_window );
			$prev_posts_html = $this->adjust_link_html( $prev_posts_html, 'previous', $new_window );

		}


		//for details
		if ( $this->is_page_front_page() || is_singular() ) {
			$post_types_to_show = $settings->get_option( 'wpnp_show_posttypes', 'wpnextpreviouslink_basics', [ 'post', 'page', 'attachment' ] );
			if(!is_array($post_types_to_show)) $post_types_to_show = [];

			//if show both next prev for single
			if ( $show_both_in_single ) {
				if ( ! in_array( $post->post_type, $post_types_to_show ) ) {
					return;
				}

				//show prev if exists
				if ( $prev_post_html != null ) {
					echo $prev_post_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}


				//show next if exits
				if ( $next_post_html != null ) {
					echo $next_post_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

			} else {
				//show either prev and next for singular post

				if ( $post_to_show == 1 ) { //show prev
					if ( ! in_array( $post->post_type, $post_types_to_show ) ) {
						return;
					}

					if ( $prev_post_html != null ) {
						echo $prev_post_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}


				} elseif ( $post_to_show == 2 ) {//show next
					if ( ! in_array( $post->post_type, $post_types_to_show ) ) {
						return;
					}


					if ( $next_post_html != null ) {
						echo $next_post_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

				}//for future implementation of more options
			}

		} //end singular post
		else {
			//archive
			$show = true;
			if ( ( ! $show_archive ) || ( ! $show_category && is_category() ) || ( ! $show_tag && is_tag() ) || ( ! $show_author && is_author() ) || ( ! $show_date && is_date() ) ) {
				$show = false;
			}
			if ( $show ) {

				//if in archive any one wants to show both next and prev
				if ( $show_both_in_archive ) {

					//prev
					if ( $prev_posts_html != null ) {
						echo $prev_posts_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					//next
					if ( $next_posts_html != null ) {
						echo $next_posts_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				} else {
					if ( $post_to_show == 1 ) { //prev

						if ( $prev_posts_html != null ) {
							echo $prev_posts_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}

					} elseif ( $post_to_show == 2 ) { //next
						if ( $next_posts_html != null ) {
							echo $next_posts_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					}
				}

			}
		}//end archive


		if ( $this->is_page_front_page() || is_singular() ) {
			$page_title = esc_attr( get_the_title() );

			wp_add_inline_script( 'wpnextpreviouslink-public', ' wpnextpreviouslink_public.title =  "' . $page_title . '" ;', 'before' );
			wp_enqueue_script( 'wpnextpreviouslink-public' );
		}

	}//end wordPress_next_previous_link

	/**
	 * Is home page
	 *
	 * @return bool
	 */
	public function is_home() {
		if ( WPNextPreviousLinkHelper::is_home_or_frontpage_page() ) {
			return true;
		} else {
			return false;
		}
	}//end is_home

	/**
	 * Is the frontpage a page ?
	 *
	 * @return bool
	 */
	public function is_page_front_page() {
		return WPNextPreviousLinkHelper::is_page_front_page();
	}//end is_page_front_page

	/**
	 * Adjust the next previous link with new window option and id, class etc html functionality
	 *
	 * @param  string  $link_html
	 * @param  string  $next_prev
	 * @param  bool  $new_window
	 *
	 * @return string
	 */
	public function adjust_link_html( $link_html = '', $next_prev = 'next', $new_window = false ) {
		$extra_html = ' id="wpnp_' . esc_attr($next_prev) . '_anchor" class="wpnp_anchor_js" ';

		if ( $new_window ) {
			$extra_html .= ' target="blank" ';
		}

		if ( $link_html != '' ) {
			$link_html = str_replace( '<a href="', '<a ' . $extra_html . ' href="', $link_html );
		}

		return $link_html;
	}//end adjust_link_html
}//end class WPNextPreviousLink_Public