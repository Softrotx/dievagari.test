<?php
return array(
	'name' => esc_html__( 'Help', 'mozo' ),
	'auto' => true,
	'config' => array(

		array(
			'name' => esc_html__( 'Help', 'mozo' ),
			'type' => 'title',
			'desc' => '',
		),

		array(
			'name' => esc_html__( 'Help', 'mozo' ),
			'type' => 'start',
			'nosave' => true,
		),
//----
		array(
			'type' => 'docs',
		),

			array(
				'type' => 'end',
			),
	),
);
