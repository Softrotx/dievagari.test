<?php

/**
 * Theme options / Layout / Body
 *
 * @package vamtam/mozo
 */

return array(

	array(
		'label'  => esc_html__( 'Side Widget Areas', 'mozo' ),
		'type'   => 'heading',
		'id'     => 'layout-body-regular-sidebars',
	),

	array(
		'label'   => esc_html__( 'Left', 'mozo' ),
		'id'      => 'left-sidebar-width',
		'type'    => 'select',
		'choices' => array(
			'33.333333' => '1/3',
			'20' => '1/5',
			'25' => '1/4',
		),
		'compiler'  => true,
		'transport' => 'postMessage',
	),

	array(
		'label'       => esc_html__( 'Right', 'mozo' ),
		'description' => wp_kses( sprintf( __( 'The width of the sidebars is a percentage of the website width. If you have changed this option, please use the <a href="%s" title="Regenerate thumbnails" target="_blank">Regenerate thumbnails</a> plugin in order to update your images.', 'mozo' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ), 'vamtam-a-span' ),
		'id'          => 'right-sidebar-width',
		'type'        => 'select',
		'choices'     => array(
			'33.333333' => '1/3',
			'20'        => '1/5',
			'25'        => '1/4',
		),
		'compiler'  => true,
		'transport' => 'postMessage',
	),

	array(
		'label'  => esc_html__( 'Styles', 'mozo' ),
		'type'   => 'heading',
		'id'     => 'body-styles',
	),

	array(
		'label'       => esc_html__( 'Page Background', 'mozo' ),
		'description' => esc_html__( 'If you want to use an image as a background, enabling the cover button will resize and crop the image so that it will always fit the browser window on any resolution. If the color opacity  is less than 1 the page background underneath will be visible.', 'mozo' ),
		'id'          => 'main-background',
		'type'        => 'background',
		'compiler'    => true,
		'transport'   => 'postMessage',
	),

	array(
		'label'     => esc_html__( 'Hide the Background Image on Lower Resolutions', 'mozo' ),
		'id'        => 'main-background-hide-lowres',
		'type'      => 'switch',
		'transport' => 'postMessage',
	),

	array(
		'label'       => esc_html__( 'Body Font', 'mozo' ),
		'description' => esc_html__( 'This is the general font used in the body and the sidebars. Please note that the styles of the heading fonts are located in the general typography tab.', 'mozo' ),
		'id'          => 'primary-font',
		'type'        => 'typography',
		'compiler'    => true,
		'transport'   => 'postMessage',
	),

	array(
		'label'   => esc_html__( 'Links', 'mozo' ),
		'type'    => 'color-row',
		'id'      => 'body-link',
		'choices' => array(
			'regular' => esc_html__( 'Regular:', 'mozo' ),
			'hover'   => esc_html__( 'Hover:', 'mozo' ),
			'visited' => esc_html__( 'Visited:', 'mozo' ),
			'active'  => esc_html__( 'Active:', 'mozo' ),
		),
		'compiler'  => true,
		'transport' => 'postMessage',
	),

);

