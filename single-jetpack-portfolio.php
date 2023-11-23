<?php
/**
 * Single portfolio template
 *
 * @package vamtam/mozo
 */

if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && have_posts() ) :

	the_post();

	if ( function_exists( 'sharing_add_header' ) ) {
		sharing_add_header();
	}

	FLBuilder::enqueue_layout_styles_scripts();

	extract( vamtam_get_portfolio_options() );
?>

	<h1 class="ajax-portfolio-title textcenter"><?php the_title() ?></h1>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'full ' . $type ); ?>>
		<div class="page-content clearfix">
			<?php include locate_template( 'single-jetpack-portfolio-content.php' ); ?>
		</div>
	</article>

<?php

	if ( function_exists( 'sharing_add_footer' ) ) {
		sharing_add_footer();
	}

	print_late_styles();

?>
	<script> try { twttr.widgets.load(); } catch(e) {} </script>
<?php

	exit;
endif;

get_header();
?>
	<div class="page-wrapper">
		<?php VamtamTemplates::$in_page_wrapper = true; ?>

		<?php
			if ( have_posts() ) :
				while ( have_posts() ) : the_post();
		?>
				<?php
					extract( vamtam_get_portfolio_options() );

					list( $terms_slug, $terms_name, $terms_id ) = vamtam_get_portfolio_terms();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( VamtamTemplates::get_layout() . ' ' . $type ); ?>>
					<div class="page-content clearfix">
						<?php include locate_template( 'single-jetpack-portfolio-content.php' ); ?>

						<?php comments_template(); ?>
					</div>
				</article>
			<?php endwhile ?>
		<?php endif ?>

		<?php get_template_part( 'sidebar' ) ?>
	</div>

	<?php if ( ( vamtam_get_optionb( 'show-related-portfolios' ) || is_customize_preview() ) && class_exists( 'VamtamProjectsModule' ) ) : ?>
		<?php if ( VamtamTemplates::had_limit_wrapper() ) :  ?>
			</div>
		<?php endif ?>
		<?php

			$related_query = new WP_Query( array(
				'post_type'      => Jetpack_Portfolio::CUSTOM_POST_TYPE,
				'posts_per_page' => 1,
				'post__not_in'   => array( get_the_ID() ),
				'tax_query'      => array(
					array(
						'taxonomy' => 'jetpack-portfolio-type',
						'field'    => 'id',
						'terms'    => $terms_id,
					),
				),
			) );

			if ( intval( $related_query->found_posts ) > 0 ) :
		?>
				<div class="related-portfolios vamtam-related-content" <?php VamtamTemplates::display_none( vamtam_get_optionb( 'show-related-portfolios' ) ) ?>>
					<div class="clearfix limit-wrapper vamtam-box-outer-padding">
						<?php echo apply_filters( 'vamtam_related_portfolios_title', '<h5 class="related-content-title">' . esc_html( vamtam_get_option( 'related-portfolios-title' ) ) . '</h5>' ); ?>
						<?php
							FLBuilder::render_module_html( 'vamtam-projects', array(
								'column'                           => 4,
								'tax_jetpack-portfolio-type'       => implode( ',', $terms_id ),
								'ids'                              => '',
								'max'                              => 8,
								'height'                           => 400,
								'show_title'                       => 'below',
								'description'                      => 'true',
								'more'                             => esc_html__( 'View', 'mozo' ),
								'nopaging'                         => 'true',
								'group'                            => 'true',
								'layout'                           => 'scrollable',
								'posts_jetpack-portfolio'          => get_the_ID(),
								'posts_jetpack-portfolio_matching' => 0,
								'gap'                              => 'true',
								'hover_animation'                  => 'hover-animation-4',
							) );
						?>
					</div>
				</div>
		<?php endif ?>
		<?php if ( VamtamTemplates::had_limit_wrapper() ) :  ?>
			<div class="limit-wrapper">
		<?php endif ?>
	<?php endif ?>
<?php get_footer();

