<?php

function vamtam_customizer_is_one_page() {
	return is_page_template( 'onepage.php' );
}

$wp_customize->get_control( 'vamtam_theme[one-page-footer]' )->active_callback = 'vamtam_customizer_is_one_page';

function vamtam_partial_header_text_main() {
	ob_start();

	get_template_part( 'templates/header/top/text-main' );

	return ob_get_clean();
}

$wp_customize->selective_refresh->add_partial( 'header-text-main-selective', array(
	'selector' => '#header-text',
	'settings' => array(
		'vamtam_theme[header-text-main]',
	),
	'container_inclusive' => true,
	'render_callback'     => 'vamtam_partial_header_text_main',
) );

function vamtam_partial_header_layout() {
	ob_start();

	get_template_part( 'templates/header/top' );

	return ob_get_clean();
}

$wp_customize->selective_refresh->add_partial( 'header-layout-selective', array(
	'selector' => '.fixed-header-box:not( .hbox-filler )',
	'settings' => array(
		'vamtam_theme[header-layout]',
		'vamtam_theme[top-bar-layout]',
		'vamtam_theme[header-height]',
	),
	'container_inclusive' => true,
	'render_callback'     => 'vamtam_partial_header_layout',
) );
