<nav id="main-menu" class="<?php if ( ! class_exists( 'Mega_Menu' ) ) echo 'vamtam-basic-menu'?>">
	<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
	<a href="#main" title="<?php esc_attr_e( 'Skip to content', 'mozo' ); ?>" class="visuallyhidden"><?php esc_html_e( 'Skip to content', 'mozo' ); ?></a>
	<?php
		$location = apply_filters( 'vamtam_header_menu_location', 'menu-header' );

		if ( has_nav_menu( $location ) ) {
			add_filter( 'wp_nav_menu', 'vamtam_add_mobile_top_bar', 10, 2 );
			wp_nav_menu(array(
				'theme_location' => $location,
				'walker'         => new VamtamMenuWalker(),
				'link_before'    => '<span>',
				'link_after'     => '</span>',
			));
			remove_filter( 'wp_nav_menu', 'vamtam_add_mobile_top_bar', 10, 2 );
		}
	?>
</nav>
