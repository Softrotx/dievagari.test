<?php

/**
 * Theme options / Layout / General
 *
 * @package vamtam/mozo
 */

return array(

	array(
		'label'       => esc_html__( 'Layout Type', 'mozo' ),
		'description' => esc_html__( 'Please note that in full width layout mode, the body background option found in Styles - Body, acts as page background.', 'mozo' ),
		'id'          => 'site-layout-type',
		'type'        => 'radio',
		'choices'     => array(
			'boxed' => esc_html__( 'Boxed', 'mozo' ),
			'full'  => esc_html__( 'Full width', 'mozo' ),
		),
	),

	array(
		'label'       => esc_html__( 'Boxed Layout Padding', 'mozo' ),
		'description' => esc_html__( 'Add padding between the edge of the box and the page content. Only used on VamTam Builder pages.', 'mozo' ),
		'id'          => 'boxed-layout-padding',
		'type'        => 'switch',
	),

	array(
		'label'       => esc_html__( 'Maximum Page Width', 'mozo' ),
		'description' => wp_kses( sprintf( __( 'If you have changed this option, please use the <a href="%s" title="Regenerate thumbnails" target="_blank">Regenerate thumbnails</a> plugin in order to update your images.', 'mozo' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ), 'vamtam-a-span' ),
		'id'          => 'site-max-width',
		'type'        => 'radio',
		'choices'     => array(
			1140 => '1140px',
			1260 => '1260px',
			1400 => '1400px',
		),
		'compiler'  => true,
		'transport' => 'postMessage',
	),

);

