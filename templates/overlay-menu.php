<?php

if ( vamtam_get_option( 'header-layout' ) !== 'overlay-menu' && ! is_customize_preview() ) {
	return;
}

$location = apply_filters( 'vamtam_overlay_menu_location', 'overlay-menu' );

?>
<div id="vamtam-overlay-menu">
	<div class="vamtam-overlay-menu-contents">
		<?php // disabled for mozo ?>
		<?php if ( false && has_nav_menu( $location ) ) : ?>
			<?php get_template_part( 'templates/overlay-menu', 'logo' ) ?>

			<nav class="vamtam-overlay-menu-items">
				<?php
					wp_nav_menu( array(
						'theme_location' => $location,
						'link_before'    => '<span>',
						'link_after'     => '</span>',
					) );
				?>
			</nav>
		<?php endif ?>

		<?php if ( is_active_sidebar( 'overlay-menu-sidebar' ) ) : ?>
			<aside class="overlay-menu-sidebar">
				<?php dynamic_sidebar( 'overlay-menu-sidebar' ); ?>
			</aside>
		<?php endif ?>
	</div>
</div>
