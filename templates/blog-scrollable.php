<?php

/**
 * Scrollable blog
 *
 * @package vamtam/mozo
 */

global $vamtam_loop_vars;

$old_vamtam_loop_vars = $vamtam_loop_vars;

$vamtam_loop_vars = array(
	'show_content' => $settings->show_content,
	'show_title'   => $settings->show_title,
	'show_media'   => $settings->show_media,
	'news'         => true,
	'columns'      => $settings->columns,
	'scrollable'   => true,
	'layout'       => 'scroll-x',
);

$slider_options = array(
	'layoutMode'       => 'slider',
	'drag'             => true,
	'auto'             => false,
	'autoTimeout'      => 5000,
	'autoPauseOnHover' => true,
	'showNavigation'   => true,
	'showPagination'   => true,
	'scrollByPage'     => false,
	'gridAdjustment'   => 'responsive',
	'mediaQueries'     => VamtamTemplates::scrollable_columns( $max_columns ),
	'gapHorizontal'    => $settings->gap ? 30 : 0,
	'gapVertical'      => $settings->gap ? 30 : 0,
	'displayTypeSpeed' => 100,
);

if ( ! VamtamTemplates::has_header_slider() ) {
	wp_enqueue_style( 'cubeportfolio' );
	wp_enqueue_script( 'cubeportfolio' );
}

$GLOBALS['vamtam_inside_cube'] = true;

?>
<div class="loop-wrapper clearfix news scroll-x">
	<div class="vamtam-cubeportfolio cbp cbp-slider-edge" data-options="<?php echo esc_attr( json_encode( $slider_options ) ) ?>">
		<?php
			$useColumns = $settings->columns > 1;
			if ( $blog_query->have_posts() ) while ( $blog_query->have_posts() ) : $blog_query->the_post();
				// ideally, this shouldn't be necessary, but for some reason Beaver Builder
				// uses the post ID from the global $wp_query instead of the global $post
				$GLOBALS['wp_query'] = $blog_query;

				$post_class   = array();
				$post_class[] = 'page-content post-head';
				$post_class[] = 'list-item';
				$post_class[] = 'cbp-item';
			?>
				<div <?php post_class( implode( ' ', $post_class ) ) ?>>
					<?php include locate_template( 'templates/post.php' );	?>
				</div>
			<?php
			endwhile;

			wp_reset_query();
		?>
	</div>
</div>

<?php

$vamtam_loop_vars = $old_vamtam_loop_vars;

$GLOBALS['vamtam_inside_cube'] = false;

