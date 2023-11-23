<div class="header-content-wrapper header-background">
	<div class="limit-wrapper header-padding">
		<div class="first-row">
			<div class="first-row-left">
				<?php get_template_part( 'templates/header/top/logo' ) ?>
			</div>
			<div class="first-row-right">
				<div class="first-row-right-inner">
					<?php get_template_part( 'templates/header/top/text-main' ) ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="second-row header-content-wrapper">
	<div class="limit-wrapper header-padding">
		<div class="second-row-columns">
			<div class="header-center">
				<div id="menus">
					<?php get_template_part( 'templates/header/top/main-menu' ) ?>
				</div>
			</div>

			<?php get_template_part( 'templates/header/top/search-button' ) ?>

			<?php do_action( 'vamtam_header_cart' ) ?>
		</div>
	</div>
</div>
