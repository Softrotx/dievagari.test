<div class="<?php echo vamtam_get_option( 'full-width-header' ) ? '' : 'limit-wrapper' ?> header-maybe-limit-wrapper header-padding">
	<div class="header-contents">
		<div class="first-row">
			<?php get_template_part( 'templates/header/top/logo' ) ?>
		</div>

		<div class="second-row <?php if ( vamtam_get_option( 'enable-header-search' ) ) echo 'has-search' ?>">
		</div>

		<?php get_template_part( 'templates/header/top/search-button' ) ?>

		<?php get_template_part( 'templates/header/top/text-main' ) ?>

		<?php do_action( 'vamtam_header_cart' ) ?>

		<div class="vamtam-overlay-menu-toggle-wrapper">
			<button class="vamtam-overlay-menu-toggle">
				<span class="lines-wrapper">
					<span class="top-line"></span>
					<span class="middle-line"><?php esc_html_e( 'Open/Close Menu', 'mozo' ) ?></span>
					<span class="bottom-line"></span>
				</span>
			</button>
		</div>
	</div>
</div>
