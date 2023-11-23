<?php

class VamtamSitemap {
	/**
	 * Actions and filters
	 */
	public static function setup() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		add_action( 'vamtam-display-sitemap', array( __CLASS__, 'display_sitemap' ) );
	}

	/**
	 * Modify current template if on sitemap page
	 *
	 * @param  string $template original template
	 * @return string           template to be used
	 */
	public static function template_loader( $template ) {
		$sitemap_page_slug = vamtam_get_option( 'sitemap-page' );

		if ( ! empty( $sitemap_page_slug ) && is_page( $sitemap_page_slug ) ) {
			$template = locate_template( 'sitemap.php' );
		}

		return $template;
	}

	/**
	 * Output the sitemap
	 */
	public static function display_sitemap() {
		echo '<div class="sitemap">';

		$types = array(
			'pages'      => esc_html__( 'Pages', 'mozo' ),
			'categories' => esc_html__( 'Categories', 'mozo' ),
			'posts'      => esc_html__( 'Posts', 'mozo' ),
			'projects'   => esc_html__( 'Projects', 'mozo' ),
		);

		foreach ( $types as $type => $name ) {
			echo '<h3>' . esc_html( $name ) . '</h3>';

			echo call_user_func( array( __CLASS__, $type ) ); // xss ok
		}

		echo '</div>';
	}

	private static function page_level( $number, $parent ) {
		$query = array(
			'posts_per_page' => (int) $number,
			'post_type'      => 'page',
			'order'          => 'ASC',
			'orderby'        => 'title',
			'post_parent'    => $parent,
		);

		return self::display( $query );
	}

	/**
	 * Display pages
	 *
	 * @return string output html
	 */
	private static function pages() {
		return self::page_level( 1000, 0 );
	}

	/**
	 * Display categories
	 *
	 * @return string output html
	 */
	private static function categories() {
		$feed = esc_html__( 'RSS', 'mozo' );

		return '<ul>' . wp_list_categories(array(
			'feed'               => $feed,
			'show_count'         => true,
			'use_desc_for_title' => false,
			'title_li'           => false,
			'echo'               => 0,
		)) . '</ul>';
	}

	/**
	 * Display posts
	 *
	 * @return string output html
	 */
	private static function posts() {
		$query = array(
			'posts_per_page' => 1000,
			'post_type'      => 'post',
			'order'          => 'ASC',
			'orderby'        => 'title',
		);

		return self::display( $query, true );
	}

	/**
	 * Display projects
	 *
	 * @return string output html
	 */
	private static function projects() {
		$query = array(
			'posts_per_page' => 1000,
			'post_type'      => 'jetpack-portfolio',
			'order'          => 'ASC',
			'orderby'        => 'title',
		);

		return self::display( $query, false );
	}

	/**
	 * Generic printer
	 *
	 * @param  object  $query        WP_Query object with results
	 * @param  boolean $show_comment display number of comments
	 * @return string                HTML for this section
	 */
	private static function display( $query, $show_comment = false ) {
		$show_comment = vamtam_sanitize_bool( $show_comment );

		$archive_query = new WP_Query( $query );
		$output        = '';

		global $post;

		while ( $archive_query->have_posts() ) {
			$archive_query->the_post();

			$title = get_the_title();

			if ( ! empty( $title ) ) {
				$output .= '<li>';
				$output .= "<a href='" . get_permalink() . "' rel='bookmark' title='" . esc_attr( $title ) . "'>" . strip_tags( $title ) . '</a>';

				if ( $show_comment ) {
					$output .= ' (' . get_comments_number() . ')';
				}

				if ( 'page' === $post->post_type ) {
					$output .= self::page_level( -1, get_the_ID() );
				}

				$output .= '</li>';
			}
		}

		wp_reset_postdata();

		return '<ul>' . $output . '</ul>';
	}
}
