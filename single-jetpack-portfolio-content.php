<?php

/**
 * Single portfolio content template
 * @package vamtam/mozo
 */

global $content_width;

$client = get_post_meta( get_the_id(), 'portfolio-client', true );

$client = preg_replace( '@</\s*([^>]+)\s*>@', '</$1>', $client );

$portfolio_options = vamtam_get_portfolio_options();

$content = get_the_content();

if ( 'gallery' === $portfolio_options['type'] ) {
	list( $gallery, $content ) = VamtamPostFormats::get_first_gallery( $content, null, 'single-portfolio' );
}

$project_types       = get_the_terms( get_the_id(), Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE );
$project_tags        = get_the_terms( get_the_id(), Jetpack_Portfolio::CUSTOM_TAXONOMY_TAG );
$logo                = get_post_meta( get_the_id(), 'portfolio-logo', true );
$portfolio_thumbnail = get_the_post_thumbnail( get_the_ID(), VAMTAM_THUMBNAIL_PREFIX . 'single' );
$portfolio_top_html  = get_post_meta( get_the_ID(), 'portfolio-top-html', true );

$display_image = true;

if ( 'document' == $type ) {
	$display_image = false;
} elseif ( 'image' == $type && empty( $portfolio_thumbnail ) ) {
	$display_image = false;
} elseif ( 'gallery' === $type && empty( $gallery ) ) {
	$display_image = false;
} elseif ( 'video' === $type && empty( $href ) ) {
	$display_image = false;
} elseif ( 'html' === $type && empty( $portfolio_top_html ) ) {
	$display_image = false;
} elseif ( 'link' === $type && empty( $portfolio_thumbnail ) ) {
	$display_image = false;
}

?>

<?php if ( $display_image ) : ?>
	<div class="clearfix limit-wrapper vamtam-box-outer-padding">
		<div class="portfolio-image-wrapper fullwidth-folio">
			<?php
				if ( 'gallery' === $type ) :
					echo VamtamGallery::gallery( $gallery['attrs'] );
				elseif ( 'video' === $type ) :
					global $wp_embed;
					echo do_shortcode( $wp_embed->run_shortcode( '[embed width="' . esc_attr( $content_width ) . '"]' . $href . '[/embed]' ) );
				elseif ( 'html' === $type ) :
					echo do_shortcode( $portfolio_top_html );
				else :
					the_post_thumbnail( VAMTAM_THUMBNAIL_PREFIX . 'single' );
				endif;
			?>

		</div>
	</div>
<?php endif ?>



<div class="portfolio-text-content">
	<div class="portfolio-content">
		<div class="project-meta">

			<?php if ( ! empty( $logo ) ) : ?>
				<div class="client-logo">
					<span style="background-image: url(<?php echo esc_url( $logo ) ?>" alt="<?php the_title_attribute() ?>)" ></span>
				</div>
			<?php endif ?>

			<?php if ( ! empty( $project_types ) && ! is_wp_error( $project_types ) ) : ?>
					<p class="meta posted_in">
						<?php echo wp_kses_post( implode( ' ', VamtamTemplates::project_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) ) ) ?>
					</p>
			<?php endif ?>

			<div class="meta-top clearfix">
				<span class="post-date vamtam-meta-date"><?php the_date() ?></span>
				<?php if ( ! empty( $client ) ) : ?>
					&mdash;
					<span class="client-name">
						<?php echo wp_kses_post( $client ) ?>
					</span>
				<?php endif ?>
			</div>
		</div>

		<div class="project-main-content the-content-parent">
			<?php
				if ( 'gallery' === $portfolio_options['type'] ) {
					VamtamPostFormats::block_gallery_beaver();
				}

				echo apply_filters( 'the_content', $content );
				VamtamPostFormats::enable_gallery_beaver();
			?>

			<?php if ( ! empty( $project_tags ) && ! is_wp_error( $project_tags ) ) : ?>
				<div class="meta tagged_as"><span class="icon theme"><?php vamtam_icon( 'vamtam-theme-tag3' ); ?></span><?php echo wp_kses_post( implode( ' ', VamtamTemplates::project_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TAG ) ) ) ?></div>
			<?php endif ?>

			<?php get_template_part( 'templates/share' ); ?>
		</div>
	</div>
</div>

