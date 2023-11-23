<?php

global $vamtam_theme_customizer;

$thispath = VAMTAM_OPTIONS . 'general/';

$vamtam_theme_customizer->add_section( array(
	'title'       => esc_html__( 'Blog & Portfolio', 'mozo' ),
	'description' => '',
	'id'          => 'general',
) );

$vamtam_theme_customizer->add_section( array(
	'title'       => esc_html__( 'Posts', 'mozo' ),
	'description' => '',
	'id'          => 'general-posts',
	'subsection'  => true,
	'fields'      => include $thispath . 'posts.php',
) );

$vamtam_theme_customizer->add_section( array(
	'title'       => esc_html__( 'Projects', 'mozo' ),
	'description' => '',
	'id'          => 'general-projects',
	'subsection'  => true,
	'fields'      => include $thispath . 'projects.php',
) );
