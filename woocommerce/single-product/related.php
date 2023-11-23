<?php
/**
 * Related Products
 *
 * @author		WooThemes
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

$related = wc_get_related_products( get_the_ID(), $posts_per_page );

if ( count( $related ) === 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->get_id() ),
) );

$products = new WP_Query( $args );

$columns = intval( $columns );

$max_columns = $columns;

if ( 0 === $columns ) {
	$columns = 4; // this is used for thumbnails only
}

$woocommerce_loop['columns'] = $columns;

echo '<div class="vamtam-related-content">';
echo '<div class="limit-wrapper vamtam-box-outer-padding">';

$heading = apply_filters( 'woocommerce_product_related_products_heading', esc_html__( 'Related products', 'mozo' ) );

if ( $heading ) {
	echo '<div class="limit-wrapper vamtam-box-outer-padding">';
		echo '<h5 class="vamtam-wc-related-title">' . esc_html( $heading ) . '</h5>';
	echo '</div>';
}

include locate_template( array( 'templates/woocommerce-scrollable/loop.php' ) );

echo '</div>';
echo '</div>';
