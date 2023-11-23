<?php
/**
 * Portfolio archive page template
 *
 * @package vamtam/mozo
 */

VamtamFramework::set( 'page_title', get_the_archive_title() );

get_header(); ?>

<?php if ( have_posts() ) : the_post(); ?>
	<div class="page-wrapper">

		<?php VamtamTemplates::$in_page_wrapper = true; ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( VamtamTemplates::get_layout() ); ?>>
			<div class="page-content clearfix">
				<?php rewind_posts() ?>
				<?php
					if ( class_exists( 'FLBuilder' ) ) {
						FLBuilder::render_module_html( 'vamtam-projects', array(
							'layout'      => 'mosaic',
							'pagination'  => true,
							'link_opens'  => 'single',
							'show_title'  => '1',
							'description' => '1',
						) );
					}
				?>
				<?php get_template_part( 'templates/share' ); ?>
			</div>
		</article>

		<?php get_template_part( 'sidebar' ) ?>
	</div>
<?php endif ?>

<?php get_footer();

