<?php
/**
 * Theme options / Styles / Global Colors and Backgrounds
 *
 * @package vamtam/mozo
 */

return array(

array(
	'label' => esc_html__( 'Box Background', 'mozo' ),
	'hint'  => array(
	'content' => esc_html__( "Please note that this option is used only in boxed layout mode.<br> In full width layout mode the page background is covered by the header, slider, body and footer backgrounds respectively. If the color opacity of these areas is 1 or an opaque image is used, the page background won't be visible.<br> If you want to use an image as a background, enabling the cover button will resize and crop the image so that it will always fit the browser window on any resolution.<br> You can override this option on a page by page basis.", 'mozo' ),
	),
	'id'        => 'body-background',
	'type'      => 'background',
	'compiler'  => true,
	'transport' => 'postMessage',
),

array(
	'label'       => esc_html__( 'Accent Colors', 'mozo' ),
	'description' => esc_html__( 'Most of the design elements are attached to the accent colors below. You can easily create your own skin by changing these colors.', 'mozo' ) . ( vamtam_use_accent_preview() ? '' : '<p style="color: red; font-weight: bold">' . esc_html__( 'We have detected that your browser does not support CSS variables. This has a serious impact on performance and changing the accent color will require a full preview refresh. Please consider using Firefox, Chrome or Safari when using the Theme Customizer.', 'mozo' ) . '</p>' ),
	'id'          => 'accent-color',
	'type'        => 'color-row',
	'choices'     => array(
		1 => esc_html__( 'Accent 1', 'mozo' ),
		2 => esc_html__( 'Accent 2', 'mozo' ),
		3 => esc_html__( 'Accent 3', 'mozo' ),
		4 => esc_html__( 'Accent 4', 'mozo' ),
		5 => esc_html__( 'Accent 5', 'mozo' ),
		6 => esc_html__( 'Accent 6', 'mozo' ),
		7 => esc_html__( 'Accent 7', 'mozo' ),
		8 => esc_html__( 'Accent 8', 'mozo' ),
	),
	'compiler'  => true,
	'transport' => vamtam_use_accent_preview() ? 'postMessage' : 'refresh',
	'with_hc' => true,
),

array(
	'id'          => 'info-menu-styles',
	'type'        => 'info',
	'label'       => esc_html__( 'Menu Styles', 'mozo' ),
	'description' => wp_kses( sprintf( __( 'Menu styling options are available <a href="%s" title="Max Mega Menu" target="_blank">here</a> if you have the Max Mega Menu plugin installed.', 'mozo' ), admin_url( 'admin.php?page=maxmegamenu_theme_editor' ) ), 'vamtam-a-span' ),
),

);

