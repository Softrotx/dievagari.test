<?php

function vamtam_show_portfolio_options() {
	return class_exists( 'Jetpack_Portfolio' ) && is_singular( array( Jetpack_Portfolio::CUSTOM_POST_TYPE ) );
}

function vamtam_show_single_post_options() {
	return is_single();
}

// general

function vamtam_partial_header_logo() {
	ob_start();

	get_template_part( 'templates/header/top/logo', 'wrapper' );

	return ob_get_clean();
}

$wp_customize->selective_refresh->add_partial( 'header-logo-selective', array(
	'selector' => '.logo-wrapper',
	'settings' => array(
		'vamtam_theme[header-logo-type]',
		'vamtam_theme[custom-header-logo]',
		'vamtam_theme[custom-header-logo-transparent]',
	),
	'container_inclusive' => true,
	'render_callback'     => 'vamtam_partial_header_logo',
) );

function vamtam_partial_overlay_menu_logo() {
	ob_start();

	get_template_part( 'templates/overlay-menu', 'logo' );

	return ob_get_clean();
}

$wp_customize->selective_refresh->add_partial( 'overlay-menu-logo', array(
	'selector' => '.vamtam-overlay-menu-logo',
	'settings' => array(
		'vamtam_theme[custom-header-logo-transparent]'
	),
	'container_inclusive' => true,
	'render_callback'     => 'vamtam_partial_overlay_menu_logo',
) );

// selectively hide options

function vamtam_show_archive_layout_option() {
	return vamtam_extra_features();
}
$wp_customize->get_control( 'vamtam_theme[archive-layout]' )->active_callback = 'vamtam_show_archive_layout_option';
