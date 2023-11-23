<?php

/**
 * Header slider template for LayerSlider WP
 *
 * @package vamtam/mozo
 */

$post_id = vamtam_get_the_ID();

if ( is_null( $post_id ) ) {
	return;
}

$slider = str_replace( 'revslider-', '', vamtam_post_meta( $post_id, 'slider-category', true ) );

if ( ! empty( $slider ) && function_exists( 'putRevSlider' ) ) {
	putRevSlider( $slider );
}

