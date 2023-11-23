<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $upsells ) {
	return;
}

global $product, $woocommerce_loop;

$upsells_ids = array();

foreach ( $upsells as $upsell ) {
	$upsells_ids[] = $upsell->get_id();
}

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $upsells_ids,
	'post__not_in'         => array( $product->get_id() ),
) );

$products = new WP_Query( $args );

$columns = intval( $columns );

$max_columns = $columns;

if ( 0 === $columns ) {
	$columns = 4; // this is used for thumbnails only
}

$woocommerce_loop['columns'] = $columns;

?>

<section class="up-sells upsells products">
	<div class="vamtam-related-content">
		<div class="limit-wrapper vamtam-box-outer-padding">
			<?php echo '<h5 class="related-content-title">' . esc_html__( 'You may also like&hellip;', 'mozo' ) . '</h5>' ?>
			<?php include locate_template( array( 'templates/woocommerce-scrollable/loop.php' ) ); ?>
		</div>
	</div>
</section>
