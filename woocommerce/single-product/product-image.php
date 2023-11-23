<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $product;

$attachment_ids   = $product->get_gallery_image_ids();

if ( $product->get_type() === 'variable' ) {
	$variation_images = wp_list_pluck( wp_list_pluck( $product->get_available_variations(), 'image', true ), 'url', true );
	$attachment_ids = array_unique( array_merge( $attachment_ids, array_map( 'attachment_url_to_postid', $variation_images ) ) );
}

$attachment_count = count( $attachment_ids );

$post_thumbnail_id = $product->get_image_id();

$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

$wrapper_classes = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">

	<?php do_action( 'vamtam_woocommerce_before_single_product_images' ) ?>

	<?php
		if ( $post_thumbnail_id || $attachment_count > 0 ) :
			$large_thumbnail_size = apply_filters( 'single_product_large_thumbnail_size', 'shop_single' );
			$small_thumbnail_size = apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' );

			if ( VamtamTemplates::early_cube_load() ) {
				wp_enqueue_style( 'cubeportfolio' );
				wp_enqueue_script( 'cubeportfolio' );
			}

			$slider_options = array(
				'layoutMode'       => 'slider',
				'drag'             => true,
				'auto'             => false,
				'autoTimeout'      => 5000,
				'autoPauseOnHover' => true,
				'showNavigation'   => true,
				'showPagination'   => false,
				'rewindNav'        => true,
				'gridAdjustment'   => 'responsive',
				'mediaQueries'     => array(
					array(
						'width' => 1,
						'cols'  => 1,
					),
				),
				'gapHorizontal' => 0,
				'gapVertical'   => 0,
				'caption'       => '',
				'displayType'   => 'default',
				'plugins'       => array(
					'slider' => array(
						'pagination'      => '#product-gallery-pager-' . intval( $post->ID ),
						'paginationClass' => 'cbp-pagination-active',
					),
				),
			);

			if ( $post_thumbnail_id ) {
				$main_image_id = $post_thumbnail_id;
				array_unshift( $attachment_ids, $main_image_id );

				$attachment_ids = array_unique( $attachment_ids );
			}

			?>
				<div id="product-gallery-<?php echo intval( $post->ID ) ?>" class="vamtam-cubeportfolio cbp cbp-slider-edge" data-options="<?php echo esc_attr( json_encode( $slider_options ) ) ?>">
					<?php foreach ( $attachment_ids as $aid ) : ?>
						<div class="cbp-item">
							<div class="cbp-caption">
								<div class="cbp-caption-defaultWrap">
									<?php
										$full_size_image = wp_get_attachment_image_src( $aid, $large_thumbnail_size );
										$image_title     = esc_attr( get_the_title( $aid ) );

										$attributes = array(
											'class'                   => 'wp-post-image',
											'title'                   => $image_title,
											'data-caption'            => get_post_field( 'post_excerpt', $aid ),
											'data-src'                => $full_size_image[0],
											'data-large_image'        => $full_size_image[0],
											'data-large_image_width'  => $full_size_image[1],
											'data-large_image_height' => $full_size_image[2],
										);

										if ( $post_thumbnail_id ) {
											$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'full' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" class="cbp-lightbox">';
											$html .= wp_get_attachment_image( $aid, $large_thumbnail_size, false, $attributes );
											$html .= '</a></div>';
										} else {
											$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
											$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'mozo' ) );
											$html .= '</div>';
										}

										echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // xss ok
									?>
								</div>
							</div>
						</div>
					<?php endforeach ?>
				</div>
	<?php
		else :

			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'mozo' ) );
			$html .= '</div>';

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // xss ok

		endif;
	?>

	<?php if ( is_countable( $attachment_ids ) && count( $attachment_ids ) > 1 ) : ?>
		<ul class="thumbnails flex-control-nav" id="product-gallery-pager-<?php echo intval( $post->ID ) ?>"><?php

			$loop = 0;

			foreach ( $attachment_ids as $attachment_id ) {

				$classes = array( 'cbp-pagination-item' );

				if ( 0 === $loop || 0 === $loop % $columns )
					$classes[] = 'first';

				if ( 0 === ( $loop + 1 ) % $columns )
					$classes[] = 'last';

				$image_link = wp_get_attachment_url( $attachment_id );

				if ( $image_link ) {
					$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
					$image_class = esc_attr( implode( ' ', $classes ) );
					$image_title = esc_attr( get_the_title( $attachment_id ) );

					echo wp_kses_post( apply_filters( 'vamtam_woocommerce_single_product_image_thumbnail_html', sprintf( '<li><a href="" class="%s" title="%s">%s</a></li>', $image_class, $image_title, $image ), $attachment_id, $post->ID, $image_class ) );

					$loop++;
				}
			}

		?></ul>
	<?php endif ?>

	<?php do_action( 'vamtam_woocommerce_after_single_product_images' ) ?>
</div>
