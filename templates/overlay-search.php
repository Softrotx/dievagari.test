<div id="vamtam-overlay-search">
	<button id="vamtam-overlay-search-close"><?php echo vamtam_get_icon_html( array( // xss ok
		'name' => 'vamtam-theme-close-sample',
	) ) ?></button>
	<form action="<?php echo esc_url( home_url( '/' ) ) ?>" class="searchform" method="get" role="search" novalidate="">
		<input type="search" required="required" placeholder="<?php esc_attr_e( 'Search...', 'mozo' ) ?>" name="s" value="" />
		<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
			<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ) ?>"/>
		<?php endif ?>
	</form>
</div>