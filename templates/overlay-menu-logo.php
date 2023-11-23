<?php
	$logo_url = vamtam_get_option( 'custom-header-logo-transparent' );

	$attachment = attachment_url_to_postid( $logo_url );

	if ( ! empty( $logo_url ) ) :
		$logo_meta = get_post_meta( $attachment, '_wp_attachment_metadata', true );

		$logo_size = array(
			'width'  => isset( $logo_meta['width'] ) ? intval( $logo_meta['width'] ) / 2 : 0,
			'height' => isset( $logo_meta['height'] ) ? intval( $logo_meta['height'] ) / 2 : 0,
		);

		if ( ! empty( $logo_size['height'] ) ) {
			$logo_style = "max-height: {$logo_size['height']}px;";
		}
?>
	<div class="vamtam-overlay-menu-logo">
		<img src="<?php echo esc_url( $logo_url ) ?>" alt="<?php bloginfo( 'name' )?>" class="menu-logo" <?php echo image_hwstring( $logo_size['width'], $logo_size['height'] ) ?> />
	</div>
<?php endif ?>
