<?php

/**
 * Various template helpers
 */

function vamtam_theme_background_styles() {
	global $post;

	$post_id = vamtam_get_the_ID();

	if (is_null( $post_id )) return;

	$bgcolor      = esc_html( vamtam_sanitize_accent( vamtam_post_meta( $post_id, 'background-color', true ), 'css' ) );
	$bgimage      = esc_url( vamtam_post_meta( $post_id, 'background-image', true ) );
	$bgrepeat     = esc_html( vamtam_post_meta( $post_id, 'background-repeat', true ) );
	$bgsize       = esc_html( vamtam_post_meta( $post_id, 'background-size', true ) );
	$bgattachment = esc_html( vamtam_post_meta( $post_id, 'background-attachment', true ) );
	$bgposition   = esc_html( vamtam_post_meta( $post_id, 'background-position', true ) );

	$page_style = '';
	if ( ! empty( $bgcolor ) ) {
		$page_style .= "background-color:$bgcolor;";
	}
	if ( ! empty( $bgimage ) ) {
		$page_style .= "background-image:url('$bgimage');";

		if ( ! empty( $bgrepeat ) ) {
			$page_style .= "background-repeat:$bgrepeat;";
		}

		if ( ! empty( $bgattachment ) ) {
			$page_style .= "background-attachment:$bgattachment;";
		}

		if ( ! empty( $bgsize ) ) {
			$page_style .= "background-size:$bgsize;";
		}
	}

	$bgcolor      = esc_html( vamtam_sanitize_accent( vamtam_post_meta( $post_id, 'local-main-background-color', true ), 'css' ) );
	$bgimage      = esc_url( vamtam_post_meta( $post_id, 'local-main-background-image', true ) );
	$bgrepeat     = esc_html( vamtam_post_meta( $post_id, 'local-main-background-repeat', true ) );
	$bgsize       = esc_html( vamtam_post_meta( $post_id, 'local-main-background-size', true ) );
	$bgattachment = esc_html( vamtam_post_meta( $post_id, 'local-main-background-attachment', true ) );
	$bgposition   = esc_html( vamtam_post_meta( $post_id, 'local-main-background-position', true ) );

	$main_style = '';
	if ( ! empty( $bgcolor ) ) {
		$main_style .= "background-color:$bgcolor;";
	}
	if ( ! empty( $bgimage ) ) {
		$main_style .= "background-image:url('$bgimage');";

		if ( ! empty( $bgrepeat ) ) {
			$main_style .= "background-repeat:$bgrepeat;";
		}

		if ( ! empty( $bgattachment ) ) {
			$main_style .= "background-attachment:$bgattachment;";
		}

		if ( ! empty( $bgsize ) ) {
			$main_style .= "background-size:$bgsize;";
		}
	}

	if ( ! empty( $page_style ) || ! empty( $main_style ) ) {
		echo "<style>html,#main-content{{$page_style}}.vamtam-main{{$main_style}}</style>"; // xss ok, escaped above
	}
}
add_action( 'wp_head', 'vamtam_theme_background_styles' );

function vamtam_body_classes( $body_class ) {
	global $post;

	$is_blank_page     = is_page_template( 'page-blank.php' );
	$has_header_slider = VamtamTemplates::has_header_slider();
	$has_page_header   = VamtamTemplates::has_page_header() && ! is_404();
	$has_middle_header = vamtam_post_meta( null, 'page-middle-header-type', true ) === '';
	$is_responsive     = VamtamFramework::get( 'is_responsive' );

	$body_class[] = $is_blank_page ? 'full' : vamtam_get_option( 'site-layout-type' );
	$body_class[] = 'header-layout-' . vamtam_get_option( 'header-layout' );
	$body_class[] = 'pagination-' . vamtam_get_option( 'pagination-type' );

	$sticky_header_type = vamtam_post_meta( null, 'sticky-header-type', true );

	$body_class[] = ! is_singular( VamtamFramework::$complex_layout ) || empty( $sticky_header_type ) ? 'sticky-header-type-normal' : 'sticky-header-type-' . $sticky_header_type;

	$body_class_conditions = array(
		'no-page-header'         => ! $has_page_header,
		'has-page-header'        => $has_page_header,
		'no-middle-header'       => ! $has_middle_header,
		'has-middle-header'      => $has_middle_header,
		'has-header-slider'      => $has_header_slider,
		'no-header-slider'       => ! $has_header_slider,
		'responsive-layout'      => $is_responsive,
		'fixed-layout'           => ! $is_responsive,
		'has-post-thumbnail'     => is_singular() && has_post_thumbnail(),
		'sticky-header'          => vamtam_get_optionb( 'sticky-header' ),
		'vamtam-limit-wrapper'   => VamtamTemplates::had_limit_wrapper(),
		'fl-builder-active'      => class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active(),
		'single-post-one-column' => is_single(),
		'has-blocks'             => ! is_archive() && is_callable( 'has_blocks' ) && has_blocks() && ( ! class_exists( 'FLBuilderModel' ) || ! FLBuilderModel::is_builder_enabled() ),
	);

	foreach ( $body_class_conditions as $class => $cond ) {
		if ( $cond ) {
			$body_class[] = $class;
		}
	}

	$body_class[] = 'layout-' . VamtamTemplates::get_layout();

	if ( is_search() || get_query_var( 'post_format' ) ) {
		define( 'VAMTAM_ARCHIVE_TEMPLATE', true );
	}

	return $body_class;
}
add_filter( 'body_class', 'vamtam_body_classes' );
