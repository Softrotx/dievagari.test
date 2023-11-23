<?php
/**
 * Various static template helpers
 *
 * @package vamtam/mozo
 */
/**
 * class VamtamTemplates
 */
class VamtamTemplates {

	private static $layout_cache = false;

	public static $in_page_wrapper = false;

	/**
	 * Returns the current layout type and defines VAMTAM_LAYOUT accordingly
	 *
	 * @return string current page layout
	 */
	public static function get_layout() {
		global $post;

		if ( ! self::$layout_cache ) {
			$has_left  = VamtamSidebars::get_instance()->has_sidebar( 'left' );
			$has_right = VamtamSidebars::get_instance()->has_sidebar( 'right' );

			$layout_type = 'full';

			if ( $has_left && $has_right ) {
				$layout_type = 'left-right';
			} elseif ( $has_left ) {
				$layout_type = 'left-only';
			} elseif ( $has_right ) {
				$layout_type = 'right-only';
			}

			self::$layout_cache = $layout_type;
		}

		return self::$layout_cache;
	}

	/**
	 * Echoes a pagination in the form of 1 2 [3] 4 5
	 */
	public static function pagination_list( $query = null, $format = '', $base = '' ) {
		if ( is_null( $query ) ) {
			$query = $GLOBALS['wp_query'];
		}

		$total_pages = (int) $query->max_num_pages;

		$output = '';

		if ( $total_pages > 1 ) {
			$big = PHP_INT_MAX;

			if ( isset( $query->query_vars['paged'] ) && $query->query_vars['paged'] ) {
				$current_page = $query->query_vars['paged'];
			} else {
				$current_page = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );
			}

			$current_page = max( 1, $current_page );

			$output .= '<div class="navigation vamtam-pagination-wrapper">';

			$output .= '<span class="pages screen-reader-text">' . sprintf( esc_html__( 'Page %1$d of %2$d', 'mozo' ), (int) $current_page, (int) $total_pages ) . '</span>';

			$output .= paginate_links( array( // xss ok
				'base'      => empty( $base ) ? str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ) : $base,
				'format'    => empty( $format ) ? '?paged=%#%' : $format,
				'current'   => $current_page,
				'total'     => $total_pages,
				'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Prev', 'mozo' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'mozo' ) . '</span>',
			) );
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Checks whether the main content area is currently being printed
	 *
	 * @return bool True immediately before displaying the left sidebar, false after the right sidebar has been displayed
	 */
	public static function in_page_wrapper() {
		return self::$in_page_wrapper;
	}

	/**
	 * Displays the pagination code based on the theme options or $pagination_type
	 *
	 * @param  string|null $pagination_type		overrides the pagination settings
	 * @param  bool        $echo                print or return the pagination code
	 * @param  array       $other_vars          vars passed to the remote handler - can be anything but elements must be whitelisted in VamtamLoadMore
	 * @param  object|null $query               WP_Query object
	 */
	public static function pagination( $pagination_type = null, $echo = true, $other_vars = array(), $query = null ) {
		$output = apply_filters( 'vamtam_pagination', null, $pagination_type );

		if ( is_archive() || is_search() ) {
			$pagination_type = 'paged';
		}

		if ( is_null( $output ) ) {
			if ( is_null( $pagination_type ) ) {
				$pagination_type = vamtam_get_option( 'pagination-type' );
			}

			if ( 'load-more' === $pagination_type || 'infinite-scrolling' === $pagination_type ) {
				$max   = $query->max_num_pages;
				$paged = 1;

				if ( isset( $query->query_vars['paged'] ) ) {
					$paged = $query->query_vars['paged'] ? $query->query_vars['paged'] : 1;
				} else {
					$paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );
				}

				$new_query = $query->query;

				$new_query['paged'] = $paged + 1;

				$class = 'lm-btn vamtam-button';
				if ( 'cube-load-more' === $pagination_type ) {
					$class .= ' vamtam-cube-load-more';
				}

				$output = '';
				if ( (int) $max > (int) $paged ) {
					$url  = remove_query_arg( array( 'page', 'paged' ) );
					$url .= ( strpos( $url, '?' ) === false ) ? '?' : '&';
					$url .= 'paged=' . $new_query['paged'];

					if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
						$url = '#';
					}

					$btext = esc_html__( 'Load more', 'mozo' );

					$output = '<div class="load-more clearboth vamtam-pagination-wrapper"><a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '" data-query="' . esc_attr( json_encode( $new_query ) ) . '" data-other-vars="' . esc_attr( json_encode( $other_vars ) ) . '"><span class="btext" data-text="' . esc_attr( strip_tags( $btext ) ) . '">' . $btext . '</span></a></div>';
					wp_enqueue_script( 'wp-mediaelement' );
					wp_enqueue_style( 'wp-mediaelement' );
				}
			} else {
				$output = self::pagination_list( $query );
			}
		}

		if ( $echo ) {
			echo apply_filters( 'vamtam_pagination_output', $output ); // xss ok
		} else {
			return $output;
		}
	}

	/**
	 * Checks whether the current page has a title
	 *
	 * @return boolean whether the current page has a title
	 */
	public static function has_page_header() {
		$post_id = vamtam_get_the_ID();

		// the event listing has its own title below the filter
		if ( ( function_exists( 'tribe_is_events_home' ) && tribe_is_events_home() ) || is_post_type_archive( 'tribe_events' ) ) {
			return false;
		}

		if ( is_null( $post_id ) || is_search() ) {
			return true;
		}

		if ( is_single() && has_post_format( 'aside' ) ) {
			return false;
		}

		if ( vamtam_has_woocommerce() && is_product() ) {
			return false;
		}

		return get_post_meta( $post_id, 'show-page-header', true ) !== 'false' && ! is_page_template( 'page-blank.php' );
	}

	/**
	 * Returns a CSS string with background-related properties
	 *
	 * Since WP Core insists on supporting PHP 5.2, we can't even use __callStatic() to overload static methods
	 * also no null coalescence
	 */
	public static function build_background( $bg ) {
		if ( ! is_array( $bg ) ) {
			return '';
		}

		return self::build_background_full(
			isset( $bg['background-color'] )      ? $bg['background-color'] : '',
			isset( $bg['background-image'] )      ? $bg['background-image'] : '',
			isset( $bg['background-repeat'] )     ? $bg['background-repeat'] : '',
			isset( $bg['background-size'] )       ? $bg['background-size'] : '',
			isset( $bg['background-attachment'] ) ? $bg['background-attachment'] : '',
			isset( $bg['background-position'] )   ? $bg['background-position'] : ''
		);
	}

	public static function build_background_full( $bgcolor, $bgimage, $bgrepeat, $bgsize, $bgattachment, $bgposition = 'center top' ) {
		$style = '';
		if ( ! empty( $bgcolor ) ) {
			$style .= "background-color:$bgcolor;";

			if ( empty( $bgimage ) ) {
				$style .= 'background-image:none;';
			}
		}

		if ( ! empty( $bgimage ) ) {
			$style .= "background-image:url('$bgimage' );";

			if ( ! empty( $bgrepeat ) ) {
				$style .= "background-repeat:$bgrepeat;";
			}

			if ( ! empty( $bgsize ) ) {
				$style .= "background-size:$bgsize;";
			}

			if ( ! empty( $bgattachment ) ) {
				$style .= "background-attachment:$bgattachment;";
			}

			if ( ! empty( $bgposition ) ) {
				$style .= "background-position:$bgposition;";
			}
		}

		return $style;
	}

	/**
	 * Page title background styles
	 *
	 * @return string background styles
	 */
	public static function page_header_background() {
		$post_id = vamtam_get_the_ID();

		if ( is_null( $post_id ) || ! self::has_page_header() || is_archive() || is_search() )
			return '';

		$bgcolor      = vamtam_sanitize_accent( vamtam_post_meta( $post_id, 'local-page-title-background-color', true ), 'css' );
		$bgimage      = vamtam_post_meta( $post_id, 'local-page-title-background-image', true );
		$bgrepeat     = vamtam_post_meta( $post_id, 'local-page-title-background-repeat', true );
		$bgsize       = vamtam_post_meta( $post_id, 'local-page-title-background-size', true );
		$bgattachment = vamtam_post_meta( $post_id, 'local-page-title-background-attachment', true );
		$bgposition   = vamtam_post_meta( $post_id, 'local-page-title-background-position', true );

		return self::build_background_full( $bgcolor, $bgimage, $bgrepeat, $bgsize, $bgattachment, $bgposition );
	}

	/**
	 * Checks whether the current page has post siblings links
	 *
	 * @return boolean whether the current page has post siblings links
	 */
	public static function has_post_siblings_buttons() {
		return is_singular( array( 'post', 'jetpack-portfolio', 'product' ) ) && current_theme_supports( 'vamtam-ajax-siblings' ) && ! is_page_template( 'page-blank.php' );
	}

	/**
	 * Displays the page header
	 *
	 * @param  bool $placed whether the title has already been output
	 * @param  string|null $title if set, overrides the current post title
	 */
	public static function page_header( $placed = false, $title = null ) {
		if ( $placed ) return;

		global $post;

		if ( is_null( $title ) ) {
			$title = get_the_title();
		}

		$title_color = $layout = '';

		if ( ! is_archive() && ! is_search() && isset( $post ) && isset( $post->ID ) ) {
			$title_color = vamtam_post_meta( $post->ID, 'local-page-title-color', true );
			$layout      = vamtam_post_meta( $post->ID, 'local-page-title-layout', true );
		}

		$uses_local_title_layout = '';

		if ( empty( $layout ) ) {
			$layout = vamtam_get_option( 'page-title-layout' );
		} elseif ( is_customize_preview() ) {
			$uses_local_title_layout = 'uses-local-title-layout';
		}

		$description = '';

		if ( ! empty( $title_color ) ) {
			$title_color = "color:$title_color";
		}

		if ( is_archive() ) {
			if ( vamtam_has_woocommerce() && is_shop() ) {
				$description = get_post_meta( wc_get_page_id( 'shop' ), 'description', true );
			} else {
				$description = get_the_archive_description();
			}
		} elseif ( ! is_search() && is_object( $post ) ) {
			$description = get_post_meta( $post->ID, 'description', true );
		}

		if ( has_post_format( 'link' ) && ! empty( $title ) ) {
			$title = "<a href='" . vamtam_post_meta( vamtam_get_the_ID(), 'vamtam-post-format-link', true ) . "' target='_blank'>$title</a>";
		}

		if ( VamtamTemplates::has_page_header() && ! empty( $title ) ) {
			include locate_template( 'templates/header/page-title.php' );
		}
	}

	/**
	 * Comments template
	 *
	 * @param  object $comment comment data
	 * @param  array $args    comment arguments
	 * @param  int $depth   comment depth
	 */
	public static function comments( $comment, $args, $depth ) {
		include locate_template( 'templates/comment' . ( isset( $args['vamtam-layout'] ) ? '-' . $args['vamtam-layout'] : '' ) . '.php' );
	}

	/**
	 * Displays the icon for a post format $format
	 * @param  string $format post format slug
	 * @return string         icon html
	 */
	public static function post_format_icon( $format ) {
		?>
		<a class="single-post-format" href="<?php echo esc_url( add_query_arg( 'post_format', $format, home_url( '/' ) ) ) ?>" title="<?php echo esc_attr( get_post_format_string( $format ) ) ?>">
			<?php echo do_shortcode( '[icon name="' . VamtamPostFormats::get_post_format_icon( $format ) . '"]' ) ?>
		</a>
		<?php
	}

	/**
	 * Outputs the page title styles
	 */
	public static function get_title_style() {
		$post_id = vamtam_get_the_ID();

		if ( ! current_theme_supports( 'vamtam-page-title-style' ) || is_null( $post_id ) )
			return;

		$bgcolor      = vamtam_sanitize_accent( vamtam_post_meta( $post_id, 'local-title-background-color', true ), 'css' );
		$bgimage      = vamtam_post_meta( $post_id, 'local-title-background-image', true );
		$bgrepeat     = vamtam_post_meta( $post_id, 'local-title-background-repeat', true );
		$bgsize       = vamtam_post_meta( $post_id, 'local-title-background-size', true );
		$bgattachment = vamtam_post_meta( $post_id, 'local-title-background-attachment', true );
		$bgposition   = vamtam_post_meta( $post_id, 'local-title-background-position', true );

		$style = '';
		if ( ! empty( $bgcolor ) ) {
			$style .= "background-color:$bgcolor;";
		}

		if ( ! empty( $bgimage ) ) {
			$style .= "background-image:url('$bgimage' );";

			if ( ! empty( $bgrepeat ) ) {
				$style .= "background-repeat:$bgrepeat;";
			}

			if ( ! empty( $bgsize ) ) {
				$style .= "background-size:$bgsize;";
			}
		}

		return $style;
	}

	/**
	 * Checks whether the current page has a header slider
	 * @return boolean true if there is a header slider
	 */
	public static function has_header_slider() {
		$post_id = vamtam_get_the_ID();

		return ! is_null( $post_id ) &&
				apply_filters(
					'vamtam_has_header_slider',
					( ! is_404() && vamtam_post_meta( $post_id, 'slider-category', true ) !== '' && ! is_page_template( 'page-blank.php' ) )
				);
	}

	/**
	 * This is used to determine whether Cube Portfolio should be enqueued as normal,
	 * or if it should be loader later (on DOMContentLoaded)
	 *
	 * @return book   true if cubeportfolio.js should be enqueued early
	 */
	public static function early_cube_load() {
		return ! self::has_header_slider() && ! self::has_page_header() && ! is_home();
	}

	/**
	 * Returns the list of all embeddable sliders to be used in the config generator
	 *
	 * @return array list of sliders
	 */
	public static function get_all_sliders() {
		return array_merge( self::get_layer_sliders(), self::get_rev_sliders() );
	}

	/**
	 * Returns the list of Revolution Slider sliders in 'revslider-ID' => 'Name' array
	 * @return array list of Revolution Slider WP sliders
	 */
	public static function get_rev_sliders( $prefix = 'revslider-' ) {
		$result = array();

		if ( class_exists( 'RevSlider' ) ) {
			$revslider = new RevSlider();
			$sliders   = $revslider->getArrSliders();

			foreach ( $sliders as $item ) {
				$result[ $prefix . $item->getAlias() ] = $item->getTitle();
			}
		}

		return $result;
	}

	/**
	 * Returns the list of LayerSlider sliders in 'layerslider-ID' => 'Name' array
	 * @return array list of LayerSlider WP sliders
	 */
	public static function get_layer_sliders( $prefix = 'layerslider-' ) {
		$result = array();

		if ( class_exists( 'LS_Sliders' ) ) {
			$sliders = LS_Sliders::find(
				array(
				'orderby' => 'date_m',
				'limit' => 10000,
				'data' => false,
				)
			);

			foreach ( $sliders as $item ) {
				$result[ $prefix . $item['id'] ] = $item['name'];
			}
		}

		return $result;
	}

	public static function project_tax( $tax ) {
		$project_types = get_the_terms( get_the_id(), $tax );

		$links = array();

		foreach ( $project_types as $project_type ) {
			$project_type_link = get_term_link( $project_type, $tax );

			if ( is_wp_error( $project_type_link ) ) {
				return $project_type_link;
			}

			$links[] = '<a href="' . esc_url( $project_type_link ) . '" rel="tag">' . esc_html( $project_type->name ) . '</a>';
		}

		return $links;
	}

	public static function scrollable_columns( $max ) {
		global $content_width;

		if ( 0 === $max ) {
			$max = 11;
		}

		$min = apply_filters( 'vamtam-scrollable-columns-minimum', 1 ); // should be replaced with min( 2, $max ); if a minimum of two columns is required

		$queries = array();

		$step = ( $content_width - 120 ) / 4;

		// start from site_width/4, increment column count by 1 for every $step px
		for ( $cols = $min; $cols <= $max; ++$cols ) {
			$queries[] = array(
				'width' => ( $cols === $min ? 1 : $cols ) * $step,
				'cols'  => $cols,
			);
		}

		$queries = array_reverse( $queries );

		return apply_filters( 'vamtam-scrollable-columns', $queries, $max );
	}

	/**
	 * Prints display: none if $visible is false
	 *
	 * @param  bool $visible
	 */
	public static function display_none( $visible, $with_attr = true ) {
		if ( ! $visible ) {
			if ( $with_attr ) {
				echo 'style="display:none"';
			} else {
				echo 'display:none;';
			}
		}
	}

	public static function shortcode( $name, $atts, $content = null ) {
		$function_name = 'vamtam_shortcode_' . $name;

		if ( ! function_exists( $function_name ) ) {
			return '<!-- ' . sprintf( esc_html__( '%s not found.', 'mozo' ), $function_name ) . '-->';
		}

		if ( is_null( $content ) ) {
			return call_user_func( $function_name, $atts );
		}

		return call_user_func( $function_name, $atts, $content );
	}

	public static function the_author_posts_link_with_icon() {
		global $authordata;
		if ( ! is_object( $authordata ) ) {
			return;
		}

		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			esc_attr( sprintf( __( 'Posts by %s', 'mozo' ), get_the_author() ) ),
			vamtam_get_icon_html( array(
				'name' => 'vamtam-theme-pencil2',
			) ) . '' . esc_html( get_the_author() )
		);

		/**
		 * Filters the link to the author page of the author of the current post.
		 *
		 * @since 2.9.0
		 *
		 * @param string $link HTML link.
		 */
		echo apply_filters( 'the_author_posts_link', $link );
	}

	public static function lazyload_image( $thumbnail_id, $size, $attr = '' ) {
		return VamtamOverrides::post_thumbnail_html( wp_get_attachment_image( $thumbnail_id, 'full' ), null, $thumbnail_id, $size, $attr );
	}

	/**
	 * True if the top-most .limit-wrapper has to be used for this page
	 *
	 * @return bool
	 */
	public static function had_limit_wrapper() {
		return apply_filters( 'vamtam_had_limit_wrapper', isset( $GLOBALS['vamtam_had_limit_wrapper'] ) ? $GLOBALS['vamtam_had_limit_wrapper'] : true );
	}
}

