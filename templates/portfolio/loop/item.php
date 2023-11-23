<?php
/**
 * Single portfolio item used in a loop
 *
 * @package vamtam/mozo
 */

list( $terms_slug, $terms_name ) = vamtam_get_portfolio_terms();

$item_class = array();

$item_class[] = $settings->show_title ? 'has-title' : 'no-title';
$item_class[] = $settings->description ? 'has-description' : 'no-description';
$item_class[] = 'state-closed';
$item_class[] = 'vamtam-project';
$item_class[] = $settings->hover_animation;

$item_class[] = 'cbp-item';

$featured       = vamtam_sanitize_bool( vamtam_post_meta( get_the_id(), 'featured-project', true ) );
$starting_width = 100 / $settings->columns;

if ( $featured ) {
	$starting_width *= 2;
}

$gallery = $href = '';
extract( vamtam_get_portfolio_options() );

$video_url = ( $type === 'video' && ! empty( $href ) ) ? $href : '';

$single_url = $type === 'link' ? get_post_meta( get_the_ID(), 'vamtam-portfolio-format-link', true ) : get_permalink();

$suffix = ( 'mosaic' === $settings->layout ) ? 'normal' : 'loop';

if ( $featured ) {
	$suffix .= '-featured';
}

$cbp_singlepage = '';
if ( 'ajax' === $settings->link_opens && 'link' !== $type ) {
	$cbp_singlepage = 'cbp-singlePage';
}

if ( $settings->hover_animation === 'hover-animation-2' ) {
	$gallery = '';
}

$has_details = ( $settings->show_title || $settings->description ) && ( 'video' !== $type || empty( $video_url ) || has_post_thumbnail() );

?>
<div data-id="<?php the_id()?>" data-type="<?php echo esc_attr( implode( ' ', $terms_slug ) )?>" <?php post_class( implode( ' ', $item_class ) ); ?> style="width: <?php echo intval( $starting_width ) ?>%">
	<div class="portfolio-item-wrapper">
		<?php if ( $has_details ) : ?>
			<div class="portfolio_details">
				<a href="<?php echo esc_url( $single_url ) ?>" class="project-title <?php echo esc_attr( $cbp_singlepage ) ?>" target="<?php echo esc_attr( $link_target ) ?>">
					<span class="text-wrapper">
						<?php if ( $settings->show_title ) : ?>
							<span class="title">
								<?php the_title()?>
							</span>
						<?php endif ?>
						<?php if ( $settings->description ) : ?>
							<span class="excerpt"><?php echo strip_tags( get_the_excerpt(), '<span><bold><b><i><em>' ) ?></span>
						<?php endif ?>
					</span>
				</a>
				<a href="<?php echo esc_url( $single_url ) ?>" class="link-read-more"><?php esc_html_e( 'Read more &rarr;', 'mozo' ); ?></a>
				<?php if ( $settings->lightbox_button === 'true' && 'mosaic' === $settings->layout && ( has_post_thumbnail() || ! empty( $video_url ) || ! empty( $gallery ) ) ) : ?>
					<div class="lightbox-wrapper">
						<?php
							if ( 'gallery' === $type && ! empty( $gallery ) ) :
								echo VamtamGallery::gallery_lightbox( $gallery['attrs'] );
							else :
								if ( 'video' === $type ) {
									$link = $video_url;
								} else {
									$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

									$link = $image[0];
								}
						?>
							<a href="<?php echo esc_url( $link ) ?>" class="cbp-lightbox icon theme" title="<?php esc_attr_e( 'View Media', 'mozo' ) ?>" data-title="<?php the_title_attribute() ?>"><?php vamtam_icon( 'vamtam-theme-magnifier' ) ?></a>
						<?php endif ?>
					</div>
				<?php endif ?>
			</div>
		<?php endif ?>

		<div class="portfolio-image">
			<?php
				if ( ! empty( $gallery ) ) :
					echo VamtamGallery::gallery( $gallery['attrs'] );
				elseif ( ! empty( $video_url ) && ! has_post_thumbnail() ) :
					global $wp_embed;
					echo do_shortcode( $wp_embed->run_shortcode( '[embed]' . $video_url . '[/embed]' ) );
					echo '<a href="' . esc_url( $video_url ) . '" class="cbp-lightbox" title="' . esc_attr( get_the_title() ) . '" data-title="' . esc_attr( get_the_title() ) . '" style="display:none"></a>';
				elseif ( has_post_thumbnail() ) :
			?>
						<a href="<?php echo esc_url( $single_url ) ?>" class="meta <?php echo esc_attr( $cbp_singlepage ) ?>" target="<?php echo esc_attr( $link_target ) ?>">
							<?php
								VamtamOverrides::unlimited_image_sizes();
								the_post_thumbnail( apply_filters( 'vamtam_portfolio_loop_image_size', VAMTAM_THUMBNAIL_PREFIX . "{$suffix}-4", $suffix, $settings->columns ) );
								VamtamOverrides::limit_image_sizes();
							?>
						</a>
			<?php endif ?>
		</div>
	</div>
</div>

