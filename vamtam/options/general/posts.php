<?php

/**
 * Theme options / General / Posts
 *
 * @package vamtam/mozo
 */

return array(

	array(
		'label'       => esc_html__( 'Pagination Type', 'mozo' ),
		'description' => esc_html__( 'Also used for portfolio', 'mozo' ),
		'id'          => 'pagination-type',
		'type'        => 'select',
		'choices'     => array(
			'paged'              => esc_html__( 'Paged', 'mozo' ),
			'load-more'          => esc_html__( 'Load more button', 'mozo' ),
			'infinite-scrolling' => esc_html__( 'Infinite scrolling', 'mozo' ),
		),
	),

	array(
		'label'       => esc_html__( 'Show "Related Posts" in Single Post View', 'mozo' ),
		'description' => esc_html__( 'Enabling this option will show more posts from the same category when viewing a single post.', 'mozo' ),
		'id'          => 'show-related-posts',
		'type'        => 'switch',
		'transport'   => 'postMessage',
	),

	array(
		'label'     => esc_html__( '"Related Posts" title', 'mozo' ),
		'id'        => 'related-posts-title',
		'type'      => 'text',
		'transport' => 'postMessage',
	),

	array(
		'label'     => esc_html__( 'Meta Information', 'mozo' ),
		'id'        => 'post-meta',
		'type'      => 'multicheck',
		'transport' => 'postMessage',
		'choices'   => array(
			'author'   => esc_html__( 'Post Author', 'mozo' ),
			'tax'      => esc_html__( 'Categories and Tags', 'mozo' ),
			'date'     => esc_html__( 'Timestamp', 'mozo' ),
			'comments' => esc_html__( 'Comment Count', 'mozo' ),
		),
	),

	array(
		'label'       => esc_html__( 'Show Featured Image on Single Posts', 'mozo' ),
		'id'          => 'show-single-post-image',
		'description' => esc_html__( 'Please note, that this option works only for Blog Post Format Image.', 'mozo' ),
		'type'        => 'switch',
		'transport'   => 'postMessage',
	),

	array(
		'label'       => esc_html__( 'Post Archive Layout', 'mozo' ),
		'description' => '',
		'id'          => 'archive-layout',
		'type'        => 'radio',
		'choices'     => array(
			'normal' => esc_html__( 'Large', 'mozo' ),
			'mosaic' => esc_html__( 'Small', 'mozo' ),
		),
	),

);

