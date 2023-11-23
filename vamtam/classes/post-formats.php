<?php

/**
 * Post-format functions
 *
 * @package vamtam/mozo
 */

/**
 * class VamtamPostFormats
 */
class VamtamPostFormats {
	/**
	 * Returns the icon denoting the current post format
	 *
	 * @param  string $format post format name
	 * @return string         icon name
	 */
	public static function get_post_format_icon( $format ) {
		if ( is_sticky() ) {
			return 'pushpin';
		}

		$formats = apply_filters( 'vamtam_post_format_icons', array(
			'aside'    => 'notebook',
			'audio'    => 'music2',
			'gallery'  => 'vamtam-theme-gallery',
			'image'    => 'vamtam-theme-camera',
			'link'     => 'link',
			'quote'    => 'quotes-right2',
			'standard' => 'pencil1',
			'status'   => 'notebook',
			'video'    => 'vamtam-theme-video',
		) );

		if ( isset( $formats[ $format ] ) ) {
			return $formats[ $format ];
		}

		return 'vamtam-theme-pencil';
	}

	/**
	 * Process the data for the current post according to its format
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data, possibly modified for the respective post format
	 */
	public static function process( $post_data ) {
		$post_data_unchanged = $post_data;

		$process_method = 'format_' . $post_data['format'];
		if ( method_exists( __CLASS__, $process_method ) ) {
			$post_data = call_user_func( array( __CLASS__, $process_method ), $post_data );
		}

		if ( isset( $post_data['media'] ) && empty( $post_data['media'] ) ) {
			unset( $post_data['media'] );
			unset( $post_data['act_as_image'] );
			$post_data['act_as_standard'] = true;
		}

		return apply_filters( 'vamtam_post_format_process', $post_data, $post_data_unchanged );
	}

	/**
	 * Get the first gallery from the post content
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data modified for the gallery post format
	 */
	private static function format_gallery( $post_data ) {
		list( $gallery, $post_data['content'] ) = self::get_first_gallery( $post_data['content'], $post_data['p']->post_content, self::get_thumb_name( $post_data ) );

		$post_data['media'] = VamtamGallery::gallery( $gallery['attrs'] );

		return $post_data;
	}

	/**
	 * Get the post format image
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data modified for the image post format
	 */
	private static function format_image( $post_data ) {
		$blog_query = isset( $GLOBALS['vamtam_blog_query'] ) ? $GLOBALS['vamtam_blog_query'] : $GLOBALS['wp_query'];

		if ( ! $blog_query->is_single() ) {
			VamtamOverrides::unlimited_image_sizes();
		}

		$post_data['media'] = get_the_post_thumbnail( $post_data['p']->ID, self::get_thumb_name( $post_data ) );

		if ( ! $blog_query->is_single() ) {
			VamtamOverrides::limit_image_sizes();
		}

		return $post_data;
	}

	/**
	 * Standard post format
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data modified for the image post format
	 */
	private static function format_standard( $post_data ) {
		$post_data                 = self::format_image( $post_data );
		$post_data['act_as_image'] = true;

		return $post_data;
	}

	/**
	 * Get the post format video
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data modified for the video post format
	 */
	private static function format_video( $post_data ) {
		$blog_query = isset( $GLOBALS['vamtam_blog_query'] ) ? $GLOBALS['vamtam_blog_query'] : $GLOBALS['wp_query'];

		if ( ! $blog_query->is_single() ) {
			VamtamOverrides::unlimited_image_sizes();
			$post_data['media'] = get_the_post_thumbnail( $post_data['p']->ID, self::get_thumb_name( $post_data ) );
			VamtamOverrides::limit_image_sizes();
		}

		if ( ! isset( $post_data['media'] ) || empty( $post_data['media'] ) ) {
			global $wp_embed;
			$post_data['media'] = do_shortcode( $wp_embed->run_shortcode( '[embed]' . get_post_meta( $post_data['p']->ID, 'vamtam-post-format-video-link', true ) . '[/embed]' ) );
		} else {
			$post_data['act_as_image'] = true;
		}

		return $post_data;
	}

	/**
	 * Get the post format audio
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data modified for the audio post format
	 */
	private static function format_audio( $post_data ) {
		global $wp_embed;
		$post_data['media'] = do_shortcode( $wp_embed->run_shortcode( '[embed]' . get_post_meta( $post_data['p']->ID, 'vamtam-post-format-audio-link', true ) . '[/embed]' ) );

		return $post_data;
	}

	/**
	 * Get the post format quote
	 *
	 * @param  array $post_data current post data
	 * @return array            current post data modified for the quote post format
	 */
	private static function format_quote( $post_data ) {
		$quote = self::get_the_post_format_quote( $post_data['p'] );

		// Replace the existing quote in-place.
		if ( ! empty( $quote ) ) {
			$post_data['content'] = $quote;
		}

		return $post_data;
	}

	/**
	 * Returns the correct thumbnail name for the current post
	 *
	 * @param  array  $post_data current post data as used in VamtamPostFormats::process( $post_data )
	 * @return string            thumbnail name
	 */
	public static function get_thumb_name( $post_data ) {
		if ( $post_data['p']->post_type == 'jetpack-portfolio' ) {
			$columns = isset( $GLOBALS['vamtam_portfolio_column'] ) ? $GLOBALS['vamtam_portfolio_column'] : 1;

			$thumb_prefix = "loop-$columns";
			$thumb_suffix = '';
		} else {
			$blog_query = isset( $GLOBALS['vamtam_blog_query'] ) ? $GLOBALS['vamtam_blog_query'] : $GLOBALS['wp_query'];

			extract( self::post_layout_info() );

			$thumb_prefix = $blog_query->is_single() ?
								'single' :
								(
									$news ?
										(
											( $layout === 'grid' || $layout === 'mosaic' ) && ! has_post_format( 'gallery' ) ?
												'normal' : 'loop'
										) :
										'loop'
								);

			if ( ! vamtam_extra_features() ) {
				$thumb_prefix = 'normal';
			}

			$thumb_suffix = $news ? '-' . $columns : '-3';

		}

		return VAMTAM_THUMBNAIL_PREFIX . $thumb_prefix . $thumb_suffix;
	}

	/**
	 * Post layout settings
	 *
	 * @return array filtered post layout settings
	 */
	public static function post_layout_info() {
		global $vamtam_loop_vars;

		$result = array();

		if ( is_array( $vamtam_loop_vars ) ) {
			$result['show_content'] = vamtam_sanitize_bool( $vamtam_loop_vars['show_content'] );
			$result['show_title']   = vamtam_sanitize_bool( $vamtam_loop_vars['show_title'] );
			$result['show_media']   = vamtam_sanitize_bool( $vamtam_loop_vars['show_media'] );
			$result['news']         = vamtam_sanitize_bool( $vamtam_loop_vars['news'] );
			$result['columns']      = intval( $vamtam_loop_vars['columns'] );
			$result['layout']       = isset( $vamtam_loop_vars['layout'] ) ? $vamtam_loop_vars['layout'] : 'normal';
		} else {
			$result['show_content'] = true;
			$result['show_title']   = true;
			$result['show_media']   = true;
			$result['news']         = false;
			$result['columns']      = 1;
			$result['layout']       = 'normal';
		}

		return apply_filters( 'vamtam_post_layout_info', $result );
	}

	/**
	 * Get the first [gallery] shortcode from a string
	 *
	 * @param  string|null $content           search string
	 * @param  string|null $original_content  content to extract the gallery from
	 * @param  string      $thumbnail_name    thumbnail name for the current gallery
	 * @return array                          gallery shortcode and filtered content string
	 */
	public static function get_first_gallery( $content = null, $original_content = null, $thumbnail_name = 'thumbnail' ) {
		if ( is_null( $original_content ) ) {
			$original_content = $content;
		}

		$gallery = [
			'attrs' => [],
		];

		preg_match( '!\[(?:vamtam_)?gallery(.*?)\]!', $original_content, $matches );

		if ( ! empty( $matches ) ) {
			$gallery = $matches[0];
			$content = trim( preg_replace( '/' . preg_quote( $matches[0], '/' ) . '/', '', $content, 1 ) );

			$gallery = [
				'attrs' => empty( $matches[1] ) ? [] : shortcode_parse_atts( $matches[1] ),
			];
		} elseif ( false !== strpos( (string) $original_content, '<!-- wp:' ) ) {
			// check taken from has_blocks()
			preg_match( '#<!-- wp:gallery(?!<!-- /wp:gallery).*<!-- /wp:gallery -->#s', $original_content, $matches );

			if ( ! empty( $matches ) ) {
				$gallery = $matches[0];
				$content = trim( preg_replace( '/' . preg_quote( $matches[0], '/' ) . '/', '', $content, 1 ) );

				$gallery = parse_blocks( $matches[0] )[0];
			}
		}

		$gallery['attrs']['size'] = $thumbnail_name;

		return array( $gallery, $content );
	}

	/**
	 * Enable removal of first gallery in beaver-edited post
	 */
	public static function block_gallery_beaver() {
		add_filter( 'fl_builder_before_render_shortcodes', array( __CLASS__, 'remove_first_gallery' ) );
	}

	/**
	 * Disable removal of first gallery in beaver-edited post
	 */
	public static function enable_gallery_beaver() {
		remove_filter( 'fl_builder_before_render_shortcodes', array( __CLASS__, 'remove_first_gallery' ) );
	}

	/**
	 * Remove first gallery in $content
	 *
	 * @param  string $content post content
	 * @return string          post content without first gallery
	 */
	public static function remove_first_gallery( $content ) {
		return preg_replace( '!\[(?:vamtam_)?gallery.*?\]!', '', $content );
	}

	/**
	 * Get a quote from the post content
	 *
	 *
	 * @uses get_content_quote()
	 *
	 * @param object $post ( optional ) A reference to the post object, falls back to get_post().
	 * @return string The quote html.
	 */
	public static function get_the_post_format_quote( &$post = null ) {
		if ( empty( $post ) )
			$post = get_post();

		if ( empty( $post ) )
			return '';

		$quote  = $post->post_content;
		$source = '';
		$author = get_post_meta( $post->ID, 'vamtam-post-format-quote-author', true );
		$link   = get_post_meta( $post->ID, 'vamtam-post-format-quote-link', true );

		if ( ! empty( $author ) ) {
			VamtamOverrides::unlimited_image_sizes();
			$thumb = get_the_post_thumbnail( $post->ID, 'thumbnail' );
			VamtamOverrides::limit_image_sizes();

			$author = empty( $thumb ) ? $author : "$thumb <span class='quote-author'>$author</span>";

			$source = empty( $link ) ?
				$author :
				sprintf( '<a href="%s">%s</a>', esc_url( $link ), $author );
		}

		$quote = preg_replace( '!</?\s*blockquote.*?>!i', '', $quote );

		$content = vamtam_fix_shortcodes( wpautop( $quote ) );

		$cite = "<div class='cite'>$source</div>";

		$blockquote = "<blockquote class='clearfix'><div class='quote-text'>" . do_shortcode( $content ) . "</div>$cite</blockquote>";

		if ( is_single() ) {
			return "<div class='limit-wrapper'>$blockquote</div>";
		}

		return $blockquote;
	}
}

