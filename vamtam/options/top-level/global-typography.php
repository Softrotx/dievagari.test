<?php
/**
 * Theme options / Styles / General Typography
 *
 * @package vamtam/mozo
 */

return array(

array(
	'label'  => esc_html__( 'Headlines', 'mozo' ),
	'type'   => 'heading',
	'id'     => 'styles-typography-headlines',
),

array(
	'label'      => esc_html__( 'H1', 'mozo' ),
	'id'         => 'h1',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'H2', 'mozo' ),
	'id'         => 'h2',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'H3', 'mozo' ),
	'id'         => 'h3',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'H4', 'mozo' ),
	'id'         => 'h4',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'H5', 'mozo' ),
	'id'         => 'h5',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'H6', 'mozo' ),
	'id'         => 'h6',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'  => esc_html__( 'Additional Fonts', 'mozo' ),
	'type'   => 'heading',
	'id'     => 'styles-typography-additional',
),

array(
	'label'      => esc_html__( 'Emphasis Font', 'mozo' ),
	'id'         => 'em',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'Style 1', 'mozo' ),
	'id'         => 'additional-font-1',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'      => esc_html__( 'Style 2', 'mozo' ),
	'id'         => 'additional-font-2',
	'type'       => 'typography',
	'compiler'   => true,
	'transport'  => 'postMessage',
),

array(
	'label'  => esc_html__( 'Google Fonts Options', 'mozo' ),
	'type'   => 'heading',
	'id'     => 'styles-typography-gfonts',
),

array(
	'label'      => esc_html__( 'Subsets', 'mozo' ),
	'id'         => 'gfont-subsets',
	'type'       => 'multicheck',
	'transport'  => 'postMessage',
	'choices'    => vamtam_get_google_fonts_subsets(),
),

);

