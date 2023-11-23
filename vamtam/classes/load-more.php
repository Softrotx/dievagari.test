<?php

/**
 * VamTam load more for Cube Portfolio
 *
 * @author Nikolay Yordanov <me@nyordanov.com>
 * @package vamtam/mozo
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class VamtamLoadMore {
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_vamtam-load-more', array( $this, 'get_items' ) );
		add_action( 'wp_ajax_nopriv_vamtam-load-more', array( $this, 'get_items' ) );
	}

	public function get_items() {
		$query_args = array_intersect_key(
			$_POST['query'],
			array_flip( array(
				'author',
				'category__in',
				'fields',
				'fl_builder_loop',
				'ignore_sticky_posts',
				'order',
				'orderby',
				'paged',
				'post__in',
				'post__not_in',
				'post_type',
				'posts_per_page',
				's',
				'tax_query',
			) ) // allowed query args
		);

		if ( ! isset( $query_args['post_type'] ) ) {
			$query_args['post_type'] = 'post';
		}

		$query_args['post_status'] = 'publish';

		$other_vars = array();

		$GLOBALS['vamtam_inside_cube'] = true;

		ob_start();

		$query = new WP_Query( $query_args );

		while ( $query->have_posts() ) {
			$query->the_post();

			if ( 'jetpack-portfolio' === $query_args['post_type'] ) {
				// sanitize
				$_POST['other_vars']['pagination']  = vamtam_sanitize_bool( $_POST['other_vars']['pagination'] );
				$_POST['other_vars']['show_title']  = vamtam_sanitize_bool( $_POST['other_vars']['show_title'] );
				$_POST['other_vars']['description'] = vamtam_sanitize_bool( $_POST['other_vars']['description'] );

				// filter
				$settings = $other_vars = (object) array_intersect_key(
					$_POST['other_vars'],
					array_flip( array( 'pagination', 'link_opens', 'show_title', 'description', 'columns', 'layout', 'hover_animation' ) ) // allowed keys
				);

				include locate_template( 'templates/portfolio/loop/item.php' );
			} elseif ( 'post' === $query_args['post_type'] ) {
				global $vamtam_loop_vars;

				$vamtam_loop_vars = $other_vars = array_intersect_key(
					$_POST['other_vars'],
					array_flip( array( 'show_content', 'show_title', 'show_media', 'news', 'columns', 'layout' ) ) // allowed keys
				);

				extract( $other_vars );

				$post_class = array(
					'page-content post-header',
					'list-item',
					'cbp-item',
				);

				$starting_width = 100 / $columns;

				?>
				<div <?php post_class( implode( ' ', $post_class ) ) ?> style="width: <?php echo esc_attr( $starting_width ) ?>%">
					<div>
						<?php get_template_part( 'templates/post' );	?>
					</div>
				</div>
				<?php
			}
		}// End while().

		header( 'Content-Type: application/json' );

		echo json_encode( array(
			'content' => ob_get_clean(),
			'button'  => VamtamTemplates::pagination( 'load-more', false, $other_vars, $query ),
		) );

		exit;
	}
}

