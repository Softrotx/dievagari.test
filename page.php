<?php
/**
 * Single page template
 *
 * @package vamtam/mozo
 */

get_header();
?>

<?php if ( have_posts() ) : the_post(); ?>
	<div class="page-wrapper">
		<?php VamtamTemplates::$in_page_wrapper = true; ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( VamtamTemplates::get_layout() ); ?>>
			<div class="page-content clearfix the-content-parent">
				<?php
					the_content();

					wp_link_pages( array(
						'before' => '<nav class="navigation post-pagination" role="navigation"><span class="screen-reader-text">' . esc_html__( 'Pages:', 'mozo' ) . '</span>',
						'after'  => '</nav>',
					) );
				?>
				<?php get_template_part( 'templates/share' ); ?>
			</div>

			<?php comments_template( '', true ); ?>
		</article>

		<?php get_template_part( 'sidebar' ) ?>

	</div>
<?php endif;

get_footer();

