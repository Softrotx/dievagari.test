<?php
/**
 * Template Name: One Page Menu
 */

function vamtam_onepage_body_class( $class ) {
	$class[] = 'no-sticky-header-animation';
	return $class;
}
add_filter( 'body_class', 'vamtam_onepage_body_class' );

get_template_part( 'page' );
