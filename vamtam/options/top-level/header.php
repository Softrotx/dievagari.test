<?php

/**
 * Theme options /Header
 *
 * @package vamtam/mozo
 */

return array(

	array(
		'label'     => esc_html__( 'Header Layout', 'mozo' ),
		'type'      => 'image-select',
		'id'        => 'header-layout',
		'transport' => 'postMessage',
		'choices'   => array(
			'logo-menu' => array(
				'alt'  => esc_html__( 'One row, left logo, menu on the right', 'mozo' ),
				'img'  => VAMTAM_ADMIN_ASSETS_URI . 'images/header-layout-1.png',
			),
			'logo-text-menu' => array(
				'alt' => esc_html__( 'Two rows; left-aligned logo on top, right-aligned text and search', 'mozo' ),
				'img' => VAMTAM_ADMIN_ASSETS_URI . 'images/header-layout-2.png',
			),
			'standard' => array(
				'alt' => esc_html__( 'Two rows; centered logo on top', 'mozo' ),
				'img' => VAMTAM_ADMIN_ASSETS_URI . 'images/header-layout-3.png',
			),
			'overlay-menu' => array(
				'alt' => esc_html__( 'One row with overlay menu', 'mozo' ),
				'img' => VAMTAM_ADMIN_ASSETS_URI . 'images/header-layout-overlay.png',
			),
		),
	),

	array(
		'label'       => esc_html__( 'Minimum Header Height', 'mozo' ),
		'description' => esc_html__( 'This is the area above the slider. Includes the height of the menu for two line header layouts. A larger logo or a long menu displayed on more than one row will increase the header height.', 'mozo' ),
		'id'          => 'header-height',
		'type'        => 'number',
		'compiler'    => true,
		'transport'   => 'postMessage',
		'input_attrs' => array(
			'min' => 30,
			'max' => 300,
		),
	),
	array(
		'label'       => esc_html__( 'Sticky Header', 'mozo' ),
		'description' => esc_html__( 'This option is switched off automatically for mobile devices because the animation is not well supported by the majority of the mobile devices.', 'mozo' ),
		'id'          => 'sticky-header',
		'type'        => 'switch',
		'transport'   => 'postMessage',
	),

	array(
		'label'     => esc_html__( 'Enable Header Search', 'mozo' ),
		'id'        => 'enable-header-search',
		'type'      => 'switch',
		'transport' => 'postMessage',
	),

	array(
		'label'     => esc_html__( 'Show Empty WooCommerce Cart in Header', 'mozo' ),
		'id'        => 'show-empty-header-cart',
		'type'      => 'switch',
		'transport' => 'postMessage',
	),

	array(
		'label'       => esc_html__( 'Full Width Header', 'mozo' ),
		'description' => esc_html__( 'One row header only', 'mozo' ),
		'id'          => 'full-width-header',
		'type'        => 'switch',
		'transport'   => 'postMessage',
	),

	array(
		'label'       => esc_html__( 'Header Text Area', 'mozo' ),
		'description' => esc_html__( 'You can place text/HTML or any shortcode in this field. The text will appear in the header on the left hand side.', 'mozo' ),
		'id'          => 'header-text-main',
		'type'        => 'select',
		'transport'   => 'postMessage',
		'choices'     => vamtam_get_beaver_layouts( array(
			'' => esc_html__( '-- Select Template --', 'mozo' ),
		) ),
	),

	array(
		'label'       => esc_html__( 'Header Background', 'mozo' ),
		'description' => esc_html__( 'If you want to use an image as a background, enabling the cover button will resize and crop the image so that it will always fit the browser window on any resolution.', 'mozo' ),
		'id'          => 'header-background',
		'type'        => 'background',
		'compiler'    => true,
		'transport'   => 'postMessage',
		'show'        => array(
			'background-position' => false,
		),
	),

	array(
		'label'       => esc_html__( 'Sub-Header Background', 'mozo' ),
		'id'          => 'sub-header-background',
		'type'        => 'background',
		'compiler'    => true,
		'transport'   => 'postMessage',
		'show'        => array(
			'background-attachment' => false,
			'background-position'   => false,
		),
	),

	array(
		'label'       => esc_html__( 'Page Title Layout', 'mozo' ),
		'id'          => 'page-title-layout',
		'description' => esc_html__( 'The first row is the Title, the second row is the Description. The description can be added in the local option panel just below the editor.', 'mozo' ),
		'type'        => 'select',
		'transport'   => 'postMessage',
		'choices'     => array(
			'centered'      => esc_html__( 'Two rows, Centered', 'mozo' ),
			'one-row-left'  => esc_html__( 'One row, title on the left', 'mozo' ),
			'one-row-right' => esc_html__( 'One row, title on the right', 'mozo' ),
			'left-align'    => esc_html__( 'Two rows, left-aligned', 'mozo' ),
			'right-align'   => esc_html__( 'Two rows, right-aligned', 'mozo' ),
		),
	),

	array(
		'label'     => esc_html__( 'Page Title Background', 'mozo' ),
		'id'        => 'page-title-background',
		'type'      => 'background',
		'compiler'  => true,
		'transport' => 'postMessage',
		'show'      => array(
			'background-attachment' => false,
			'background-position'   => false,
		),
	),

	array(
		'label'     => esc_html__( 'Hide Page Title Background Image on Lower Resolutions', 'mozo' ),
		'id'        => 'page-title-background-hide-lowres',
		'type'      => 'switch',
		'transport' => 'postMessage',
	),

	array(
		'label'       => esc_html__( 'Site Title', 'mozo' ),
		'id'          => 'logo',
		'type'        => 'typography',
		'compiler'    => true,
		'transport'   => 'postMessage',
	),

	array(
		'label'     => esc_html__( 'Text Color for Transparent Header', 'mozo' ),
		'type'      => 'color',
		'id'        => 'main-menu-text-sticky-color',
		'compiler'  => true,
		'transport' => 'postMessage',
	),

	array(
		'id'          => 'info-menu-styles',
		'type'        => 'info',
		'label'       => esc_html__( 'Menu Styles', 'mozo' ),
		'description' => wp_kses( sprintf( __( 'Menu styling options are available <a href="%s" title="Max Mega Menu" target="_blank">here</a> if you have the Max Mega Menu plugin installed.', 'mozo' ), admin_url( 'admin.php?page=maxmegamenu_theme_editor' ) ), 'vamtam-a-span' ),
	),

	array(
		'id'   => 'info-mobile-header-layout',
		'type' => 'info',
		'description' => wp_kses( sprintf( __( 'Mobile header layout options are available <a href="%s" title="Max Mega Menu" target="_blank">here</a> if you have Max Mega Menu installed.', 'mozo' ), admin_url( 'admin.php?page=maxmegamenu' ) ), 'vamtam-a-span' ),
	),

);

