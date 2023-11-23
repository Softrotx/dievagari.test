<?php
	$wrapper_class = '';

	if ( $settings->style_base !== 'tag' ) {
		$wrapper_class .= 'vamtam-font-' . $settings->style_base;
	}
?>
<<?php echo esc_html( $settings->tag ) ?> class="vamtam-heading <?php echo esc_attr( $wrapper_class ) ?>">
	<?php if ( 'on' === $settings->with_divider ) : ?>
		<span class="vamtam-heading-line vamtam-heading-line-left"></span>
	<?php endif ?>

	<?php if ( ! empty( $settings->link ) ) : ?>
		<a href="<?php echo esc_url( $settings->link ) ?>" title="<?php echo esc_attr( $settings->heading ) ?>" target="<?php echo esc_attr( $settings->link_target ) ?>">
	<?php endif; ?>

	<span class="vamtam-heading-text"><?php echo wp_kses_post( $settings->heading ) ?></span>

	<?php if ( ! empty( $settings->link ) ) : ?>
		</a>
	<?php endif; ?>

	<?php if ( 'on' === $settings->with_divider ) : ?>
		<span class="vamtam-heading-line vamtam-heading-line-right"></span>
	<?php endif ?>
</<?php echo esc_html( $settings->tag ) ?>>
