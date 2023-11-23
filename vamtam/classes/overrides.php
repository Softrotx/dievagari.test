<?php

/**
 * Various filters and actions configuring some of the shortcodes
 *
 * @package vamtam/mozo
 */

/**
 * class VamtamOverrides
 */
class VamtamOverrides {

	/**
	 * add filters
	 */
	public static function filters() {
		add_filter( 'excerpt_length', array( __CLASS__, 'excerpt_length' ) );
		add_filter( 'excerpt_more', array( __CLASS__, 'excerpt_more' ) );

		add_filter( 'wp_title', array( __CLASS__, 'wp_title' ) );

		add_filter( 'pre_option_page_for_posts', '__return_zero' );

		add_filter( 'option_sgf_options', [ __CLASS__, 'sgf_options' ] );

		add_filter( 'oembed_dataparse', array( __CLASS__, 'oembed_dataparse' ), 90, 3 );

		add_filter( 'nav_menu_css_class', array( __CLASS__, 'nav_menu_css_class' ), 10, 2 );
		add_filter( 'nav_menu_css_class', array( __CLASS__, 'nav_menu_css_class' ), 11, 2 );
		add_filter( 'megamenu_nav_menu_css_class', array( __CLASS__, 'nav_menu_css_class' ), 10, 2 );
		add_filter( 'megamenu_nav_menu_css_class', array( __CLASS__, 'nav_menu_css_class' ), 11, 2 );

		add_action( 'vamtam_body', array( __CLASS__, 'vamtam_splash_screen' ) );

		add_filter( 'wpcf7_form_elements', array( __CLASS__, 'shortcodes_in_cf7' ) );

		add_filter( 'pre_option_vamtam_header-layout', array( __CLASS__, 'header_layout' ) );

		add_action( 'loop_start', array( __CLASS__, 'jetpack_remove_share' ) );

		add_action( 'wp_footer', array( __CLASS__, 'post_siblings' ) );

		add_filter( 'vamtam_customizer_get_options_vamtam_theme', array( __CLASS__, 'customizer_get_options' ) );

		add_filter( 'widget_title', array( __CLASS__, 'widget_title' ), 10, 3 );

		add_filter( 'post_thumbnail_html', array( __CLASS__, 'post_thumbnail_html' ), 20, 5 );
		add_filter( 'vamtam_maybe_lazyload', array( __CLASS__, 'maybe_lazyload' ), 10, 4 );

		add_filter( 'jetpack_tiled_gallery_partial', array( __CLASS__, 'jetpack_tiled_gallery_partial' ), 10, 3 );

		add_filter( 'option_jetpack_active_modules', array( __CLASS__, 'jetpack_active_modules' ) );

		add_filter( 'wp_link_pages_link', array( __CLASS__, 'wp_link_pages_link' ), 10, 2 );

		add_filter( 'wp_head', array( __CLASS__, 'limit_wrapper' ), 10, 2 );

		add_filter( 'render_block', array( __CLASS__, 'render_block' ), 10, 2 );

		add_action( 'wp_footer', [ __CLASS__, 'footer_additions'], 5 );

		add_filter( 'show_recent_comments_widget_style', '__return_false' );

		add_filter( 'wp_kses_allowed_html', [ __CLASS__, 'wp_kses_allowed_html' ], 10, 2 );
	}

	/**
	 * Custom wp_kses contexts
	 *
	 * @param  array  $allowedtags
	 * @param  string $context
	 * @return array
	 */
	public static function wp_kses_allowed_html( $allowedtags, $context ) {
		if ( $context === 'vamtam-a-span' ) {
			return [
				'a' => [
					'aria-describedby' => true,
					'aria-details'     => true,
					'aria-label'       => true,
					'aria-labelledby'  => true,
					'aria-hidden'      => true,
					'class'            => true,
					'id'               => true,
					'style'            => true,
					'title'            => true,
					'role'             => true,
					'data-*'           => true,
					'href'             => true,
					'target'           => true,
				],
				'span' => [
					'class'  => true,
					'id'     => true,
					'style'  => true,
					'data-*' => true,
				],
			];
		}

		if ( $context === 'vamtam-admin' ) {
			return [
				'a' => [
					'aria-describedby' => true,
					'aria-details'     => true,
					'aria-label'       => true,
					'aria-labelledby'  => true,
					'aria-hidden'      => true,
					'class'            => true,
					'id'               => true,
					'style'            => true,
					'title'            => true,
					'role'             => true,
					'data-*'           => true,
					'href'             => true,
					'target'           => true,
				],
				'span' => [
					'class'  => true,
					'id'     => true,
					'style'  => true,
					'data-*' => true,
				],
				'code' => [],
				'br'   => [],
				'p'    => [
					'class' => true,
					'id'    => true,
				],
			];
		}

		return $allowedtags;
	}

	public static function sgf_options( $opt ) {
		$opt['font_display'] = 'swap';

		return $opt;
	}

	/**
	 * Extra templates
	 */
	public static function footer_additions() {
		get_template_part( 'templates/overlay-search' );

		get_template_part( 'templates/side-buttons' );

		get_template_part( 'templates/overlay-menu' );
	}

	/**
	 * @param  string
	 * @param  array
	 * @return string
	 */
	public static function render_block( $block_content, $block ) {
		if ( $block['blockName'] === 'core/cover' ) {
			if ( isset( $block['attrs']['align'] ) && in_array( $block['attrs']['align'], array( 'left', 'right' ), true ) ) {
				$block_content = str_replace( "align{$block['attrs']['align']}", '', $block_content );
				$block_content = "<div class='vamtam-wp-block-cover-wrapper align{$block['attrs']['align']}'>" . $block_content . '</div>';
			}
		}

		return $block_content;
	}

	/**
	 * @return  bool	true if the pages needs the outer .limit-wrapper
	 */
	public static function limit_wrapper() {
		global $vamtam_theme, $post;

		$GLOBALS['vamtam_had_limit_wrapper'] =
			(
				isset( $vamtam_theme['boxed-layout-padding'] ) && vamtam_sanitize_bool( $vamtam_theme['boxed-layout-padding'] ) &&
				isset( $vamtam_theme['site-layout-type'] ) && 'full' !== $vamtam_theme['site-layout-type']
			) ||
			! is_singular() ||
			( class_exists( 'VamtamTemplates' ) && VamtamTemplates::get_layout() !== 'full' ) ||
			! (
				( class_exists( class_exists('\Elementor\Plugin') ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( $post->ID ) ) ||
				( class_exists( 'Vamtam_Elements_B' ) && Vamtam_Elements_B::is_beaver_used() ) ||
				( is_callable( 'has_blocks' ) && has_blocks() )
			);
	}


	/**
	 * Wrap the current page number in a span
	 * @param  string $link
	 * @param  int    $i
	 * @return string
	 */
	public static function wp_link_pages_link( $link, $i ) {
		if ( strpos( $link, '<a' ) !== 0 ) {
			$link = '<span class="current" aria-current="page">' . $link . '</span>';
		}

		return $link;
	}

	/**
	 * We have alternative implementations of some Jetpack modules which are better integrated with the theme
	 */
	public static function jetpack_active_modules( $modules ) {
		return array_diff( $modules, [
			'infinite-scroll',
			'lazy-images',
		] );
	}

	/**
	 * Override Jetpack's tiled gallery template so that it supports our lazyload implementation
	 */
	public static function jetpack_tiled_gallery_partial( $path, $name, $context ) {
		if ( $name === 'item' ) {
			// this must return the path to the file
			return locate_template( 'templates/jetpack-tiled-gallery-item.php' );
		}

		return $path;
	}

	public static function post_thumbnail_html( $html, $post_ID, $post_thumbnail_id, $size, $attr ) {
		return self::maybe_lazyload( $html, $post_thumbnail_id, $size );
	}

	/**
	 * [maybe_lazyload description]
	 * @param  string       $html
	 * @param  int          $image_id  attachment id
	 * @param  string|array $size      thumbnail name or [width, height] array
	 * @return string
	 */
	public static function maybe_lazyload( $html, $image_id, $size, $with_wrapper = true ) {
		if ( $html === '' ) {
			return $html;
		}

		if ( is_array( $size ) ) {
			list( $width, $height ) = $size;
		} else {
			list( , $width, $height ) = wp_get_attachment_image_src( $image_id, $size );
		}

		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . esc_attr( $width ) .' ' . esc_attr( $height ) .'" height="' . esc_attr( $height ) . 'px" width="' . esc_attr( $width ) . 'px"/>';

		$wrapped = '';

		if ( $with_wrapper ) {
			$wrapped .= '<div class="vamtam-responsive-wrapper">';
		}

		$wrapped .= $html;

		if ( $with_wrapper ) {
			$wrapped .= '</div>';
		}

		return $wrapped;
	}

	public static function widget_title( $title = '', $instance = null, $id_base = null ) {
		if ( ! is_null( $instance ) && $id_base === 'recent-posts' && empty( $instance['title'] ) ) {
			return '';
		}

		return $title;
	}

	public static function unlimited_image_sizes() {
		add_filter( 'wp_calculate_image_sizes', array( __CLASS__, 'wp_calculate_image_sizes' ), 10, 5 );
	}

	public static function limit_image_sizes() {
		remove_filter( 'wp_calculate_image_sizes', array( __CLASS__, 'wp_calculate_image_sizes' ), 10, 5 );
	}

	public static function wp_calculate_image_sizes( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
		return '(min-width: 900px) 50vw, 100vw';
	}

	public static function post_siblings() {
		if ( VamtamTemplates::has_post_siblings_buttons() ) {
			get_template_part( 'templates/post-siblings-links' );
		}
	}

	public static function jetpack_remove_share() {
		remove_filter( 'the_content', 'sharing_display',19 );
		remove_filter( 'the_excerpt', 'sharing_display',19 );

		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
		}
	}

	public static function header_layout( $layout ) {
		$logo_type = vamtam_get_option( 'header-logo-type' );

		if ( $logo_type === 'names' ) {
			return 'standard';
		}

		return $layout;
	}

	public static function wp_title( $title ) {
		if ( empty( $title ) && ( is_home() || is_front_page() ) ) {
			$description = get_bloginfo( 'description' );
			return get_bloginfo( 'name' ) . ( ! empty( $description ) ? ' | ' . $description : '' );
		}

		return $title;
	}

	/**
	 * enable any shortcodes in CF7
	 * @param  string $form original html
	 * @return string       parsed with do_shortcode
	 */
	public static function shortcodes_in_cf7( $form ) {
		return do_shortcode( vamtam_fix_shortcodes( $form ) );
	}

	/**
	 * Show a splash screen on some pages
	 */
	public static function vamtam_splash_screen() {
		$local = vamtam_post_meta( null, 'show-splash-screen-local', true );

		$enabled = $local === 'default' ? vamtam_get_option( 'show-splash-screen' ) : vamtam_sanitize_bool( $local );

		$style = '';

		if ( ! $enabled ) {
			if ( ! is_customize_preview() ) {
				return;
			}

			$style = 'style="display: none"'; // we need the html for the customizer preview, but there is no need to show it on the first load
		}

		$logo = vamtam_get_option( 'splash-screen-logo' );

		echo '<div class="vamtam-splash-screen" ' . $style . '>'; // xss ok
		echo '<div class="vamtam-splash-screen-progress-wrapper">';

		if ( ! empty( $logo ) ) {
			vamtam_url_to_image( $logo, 'full' );
		}

		echo '<div class="vamtam-splash-screen-progress"></div>
			</div>
		</div>';

		echo '<noscript><style>.vamtam-splash-screen { display: none !important }</style></noscript>';

		wp_enqueue_script( 'vamtam-splash-screen' );
	}

	/**
	 * Remove unnecessary menu item classes
	 *
	 * @param  array  $classes current menu item classes
	 * @param  object $item    menu item
	 * @param  object $args    menu item args
	 * @return array           filtered classes
	 */
	public static function nav_menu_css_class( $classes, $item ) {
		if ( isset( $item->url ) && strpos( $item->url, '#' ) !== false && ( $key = array_search( 'mega-current-menu-item', $classes ) ) !== false ) {
			unset( $classes[ $key ] );
			$classes[] = 'maybe-current-menu-item';

			$GLOBALS['vamtam_menu_had_hash'] = true;
		}

		if ( isset( $GLOBALS['vamtam_menu_had_hash'] ) && $GLOBALS['vamtam_menu_had_hash'] ) {
			$classes = array_diff( $classes, array( 'mega-current-menu-item', 'mega-current-menu-ancestor', 'mega-current-menu-parent' ) );
		}

		return $classes;
	}

	/**
	 * Wrap oEmbeds in .vamtam-video-frame
	 *
	 * @param  string $output original oembed output
	 * @param  object $data   data from the oEmbed provider
	 * @param  string $url    original embed url
	 * @return string         $output wrapped in additional html
	 */
	public static function oembed_dataparse( $output, $data, $url ) {
		if ( $data->type == 'video' && ! ( has_blocks() && doing_filter( 'the_content' ) ) )
			return '<div class="vamtam-video-frame">' . $output . '</div>';

		return $output;
	}

	/**
	 * Sets the excerpt length
	 *
	 * @param  int $length original length
	 * @return int         excerpt length
	 */
	public static function excerpt_length( $length ) {
		global $vamtam_loop_vars;

		if ( isset( $vamtam_loop_vars ) && $vamtam_loop_vars['news'] )
			return 15;

		return $length;
	}

	/**
	 * Sets the excerpt ending
	 *
	 * @param  string $more original ending
	 * @return string         excerpt ending
	 */
	public static function excerpt_more( $more ) {
		return '...';
	}
}

