<?php
/**
 * 404 page template
 *
 * @package vamtam/mozo
 */

get_header();

VamtamEnqueues::enqueue_style_and_print( 'vamtam-not-found' );

?>

<div class="clearfix">
	<div id="header-404">
		<div class="line-1"><?php echo esc_html_x( '404', 'page not found error', 'mozo' ) ?></div>
		<div class="line-2"><?php esc_html_e( 'Holy guacamole!', 'mozo' ) ?></div>
		<div class="line-3"><?php esc_html_e( 'Looks like this page is on vacation. Or just playing hard to get. At any rate... it is not here.', 'mozo' ) ?></div>
		<div class="line-4"><a href="<?php echo esc_url( home_url( '/' ) ) ?>"><?php echo esc_html__( '&larr; Go to the home page or just search...', 'mozo' ) ?></a></div>
	</div>
	<div class="page-404">
		<?php get_search_form(); ?>
	</div>
</div>

<?php get_footer(); ?>

