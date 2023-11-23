<?php
/**
 * Catch-all template
 *
 * @package vamtam/mozo
 */

$format = get_query_var( 'post_format' );

VamtamFramework::set( 'page_title', $format ? sprintf( esc_html__( 'Post format: %s', 'mozo' ), $format ) : esc_html__( 'Blog', 'mozo' ) );

get_header();
?>
<div class="page-wrapper">

	<article <?php post_class( VamtamTemplates::get_layout() ) ?>>
		<div class="page-content clearfix">
			<?php get_template_part( 'loop', 'index' ); ?>
		</div>
	</article>

	<?php get_template_part( 'sidebar' ) ?>
</div>
<?php get_footer(); ?>

