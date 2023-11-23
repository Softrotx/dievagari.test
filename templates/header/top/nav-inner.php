<?php
$layout = vamtam_get_option( 'top-bar-layout' );

$layout = ! empty( $layout ) ? explode( '-', $layout ) : null;

$maybe_limit_wrapper = ( vamtam_get_option( 'header-layout' ) !== 'logo-menu' ) ? '' : 'header-maybe-limit-wrapper';

?>
<nav class="top-nav <?php echo esc_attr( implode( '-', $layout ) ) ?>">
	<div class="<?php echo ( vamtam_get_option( 'header-layout' ) !== 'logo-menu' || ! vamtam_get_option( 'full-width-header' ) ) ? 'limit-wrapper' : '' ?> <?php echo esc_attr( $maybe_limit_wrapper ) ?> top-nav-inner header-padding">
		<div class="row">
			<div class="row <?php if ( count( $layout ) === 1 ) echo esc_attr( 'single-cell' ) ?>">
				<?php
					foreach ( $layout as $part ) {
						get_template_part( 'templates/header/top/nav', $part );
					}
				?>
			</div>
		</div>
	</div>
</nav>

