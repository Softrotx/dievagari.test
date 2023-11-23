<?php
/**
 * Single page template
 *
 * Template Name: Guestbook
 *
 * @package the-wedding-day
 */

get_header();

?>

<?php if ( have_posts() ) : the_post(); ?>

<div class="page-wrapper">
	<?php VamtamTemplates::$in_page_wrapper = true; ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( VamtamTemplates::get_layout() ); ?>>

		<?php comments_template( '/comments-guestbook.php', true ); ?>

		<div class="page-content clearfix the-content-parent">
			<?php the_content(); ?>
			<?php wp_link_pages( array(
				'before' => '<div class="page-link">' . esc_html__( 'Pages:', 'mozo' ),
				'after' => '</div>',
			) ); ?>
			<?php get_template_part( 'templates/share' ); ?>
		</div>
	</article>

	<?php get_template_part( 'sidebar' ) ?>
</div>

<?php endif;

get_footer();
