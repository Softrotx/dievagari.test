<?php

/**
 * Theme options / General / Projects
 *
 * @package vamtam/mozo
 */

return array(
	array(
		'label'       => esc_html__( 'Show "Related Projects" in Single Project View', 'mozo' ),
		'description' => esc_html__( 'Enabling this option will show more projects from the same type in the single project.', 'mozo' ),
		'id'          => 'show-related-portfolios',
		'type'        => 'switch',
		'transport'   => 'postMessage',
	),

	array(
		'label'     => esc_html__( '"Related Projects" title', 'mozo' ),
		'id'        => 'related-portfolios-title',
		'type'      => 'text',
		'transport' => 'postMessage',
	),
);

