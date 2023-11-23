<?php

/**
 * Controls attached to core sections
 *
 * @package vamtam/mozo
 */


return array(
	array(
		'label'     => esc_html__( 'Header Logo Type', 'mozo' ),
		'id'        => 'header-logo-type',
		'type'      => 'switch',
		'transport' => 'postMessage',
		'section'   => 'title_tagline',
		'choices'   => array(
			'image'      => esc_html__( 'Image', 'mozo' ),
			'site-title' => esc_html__( 'Site Title', 'mozo' ),
		),
		'priority' => 8,
	),

	array(
		'label'       => esc_html__( 'Alternative Logo', 'mozo' ),
		'description' => esc_html__( 'This logo is used when you are using the transparent sticky header. It must be the same size as the main logo.', 'mozo' ),
		'id'          => 'custom-header-logo-transparent',
		'type'        => 'image',
		'transport'   => 'postMessage',
		'section'     => 'title_tagline',
		'priority' => 9,
	),

	array(
		'label'       => esc_html__( 'Show Splash Screen', 'mozo' ),
		'description' => esc_html__( 'This option is useful if you have video backgrounds, featured slider, galleries or other elements that may load slowly. You may override this setting for a specific page using the local options.', 'mozo' ),
		'id'          => 'show-splash-screen',
		'type'        => 'switch',
		'transport'   => 'postMessage',
		'section'     => 'title_tagline',
		'priority' => 9,
	),

	array(
		'label'     => esc_html__( 'Splash Screen Logo', 'mozo' ),
		'id'        => 'splash-screen-logo',
		'type'      => 'image',
		'transport' => 'postMessage',
		'section'   => 'title_tagline',
		'priority' => 9,
	),

	array(
		'label'    => esc_html__( 'Sitemap page', 'mozo' ),
		'id'       => 'sitemap-page',
		'type'     => 'dropdown-pages',
		'section'  => 'static_front_page',
		'priority' => 11,
	),

	array(
		'label'    => esc_html__( 'Maintenance mode page', 'mozo' ),
		'id'       => 'maintenance-page',
		'type'     => 'dropdown-pages',
		'section'  => 'static_front_page',
		'priority' => 12,
	),
);

