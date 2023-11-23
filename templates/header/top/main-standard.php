<div class="first-row header-content-wrapper header-padding">
	<?php get_template_part( 'templates/header/top/logo' ) ?>
</div>

<div class="second-row header-content-wrapper">
	<div class="limit-wrapper header-padding">
		<div class="second-row-columns">
			<div class="header-left">
				<?php get_template_part( 'templates/header/top/text-main' ) ?>
			</div>

			<div class="header-center">
				<div id="menus">
					<?php get_template_part( 'templates/header/top/main-menu' ) ?>
				</div>
			</div>

			<div class="header-right">
				<?php get_template_part( 'templates/header/top/search-button' ) ?>
				<?php do_action( 'vamtam_header_cart' ) ?>
			</div>
		</div>
	</div>
</div>
