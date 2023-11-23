<?php

/**
 * Theme options / Footer
 *
 * @package vamtam/mozo
 */

return array(
	array(
		'label'   => esc_html__( 'Show Footer on One Page Template', 'mozo' ),
		'id'      => 'one-page-footer',
		'type'    => 'switch',
	),

	array(
		'label'     => esc_html__( 'Footer Template', 'mozo' ),
		'id'        => 'footer-beaver-template',
		'type'      => 'select',
		'choices'   => vamtam_get_beaver_layouts( array(
			'' => esc_html__( '-- Select Template --', 'mozo' ),
		) ),
	),

	array(
		'id'    => 'footer-typography-title',
		'label' => esc_html__( 'Typography', 'mozo' ),
		'type'  => 'heading',
	),

	array(
		'label'       => esc_html__( 'Widget Areas Titles', 'mozo' ),
		'description' => esc_html__( 'Please note that this option will override the general headings style set in the General Typography" tab.', 'mozo' ),
		'id'          => 'footer-sidebars-titles',
		'type'        => 'typography',
		'compiler'    => true,
		'transport'   => 'postMessage',
	),
);

