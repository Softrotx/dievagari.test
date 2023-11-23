<?php

global $post;

$slider_options = array(
	'layoutMode'       => 'slider',
	'drag'             => true,
	'auto'             => false,
	'autoTimeout'      => 5000,
	'autoPauseOnHover' => true,
	'showNavigation'   => true,
	'showPagination'   => false,
	'scrollByPage'     => false,
	'gridAdjustment'   => 'responsive',
	'mediaQueries'     => VamtamTemplates::scrollable_columns( $settings->columns ),
	'gapHorizontal'    => 30,
	'gapVertical'      => 30,
	'displayTypeSpeed' => 100,
);

if ( VamtamTemplates::early_cube_load() ) {
	wp_enqueue_style( 'cubeportfolio' );
	wp_enqueue_script( 'cubeportfolio' );
}

$GLOBALS['vamtam_inside_cube'] = true;

?>
<div class="vamtam-cubeportfolio cbp cbp-slider-edge vamtam-tribe-multiple-events" data-options="<?php echo esc_attr( json_encode( $slider_options ) ) ?>">
	<?php foreach ( $events as $i => $event ) : ?>
		<?php
			setup_postdata( $event );
			$post = $event;
		?>
		<div class="cbp-item">
			<?php get_template_part( 'templates/beaver/tribe-events/single-event' ) ?>
		</div>
	<?php endforeach; ?>
	<?php wp_reset_postdata(); ?>
</div>
