<?php

return array(
	array(
		'label'     => esc_html__( 'Layout', 'mozo' ),
		'id'        => 'top-bar-layout',
		'type'      => 'select',
		'transport' => 'postMessage',
		'choices'   => vamtam_get_beaver_layouts( array(
			''            => esc_html__( 'Disabled', 'mozo' ),
		), 'beaver-' ),
	),
);
