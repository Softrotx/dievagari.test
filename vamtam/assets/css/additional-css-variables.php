<?php

$overlay_color = get_option( 'carousel_background_color', vamtam_get_option( 'accent-color', 5 ) );

if ( empty( $overlay_color ) ) {
	$overlay_color = '#000000';
}

return array(
	// The current CSS Variables polyfill for IE 11 does not support nested variables
	// However, these are necessary for the live preview,
	// so we use a static color for the live site and a CSS var for the customizer
	'default-bg-color' => is_customize_preview() ? 'var( --vamtam-main-background-background-color )' : vamtam_get_option( 'main-background', 'background-color' ),
	'default-line-color' => is_customize_preview() ? 'var( --vamtam-accent-color-7 )' : vamtam_get_option( 'accent-color', 7 ),

	'small-padding' => '20px',

	'horizontal-padding' => '50px',
	'vertical-padding' => '30px',

	'horizontal-padding-large' => '60px',
	'vertical-padding-large' => '60px',

	'no-border-link' => 'none',

	'border-radius' => '6px',
	'border-radius-oval' => '30em',

	'overlay-color' => $overlay_color,
	'overlay-color-hc' => ( new VamtamColor( $overlay_color ) )->luminance > 0.4 ? '#000000' : '#ffffff',

	/** DO NOT CHANGE BELOW */
	 'box-outer-padding' => '60px',
	/** DO NOT CHANGE ABOVE  */
);

