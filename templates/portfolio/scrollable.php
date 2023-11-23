<?php

/**
 * Portfolio scrollable template
 *
 * @package vamtam/mozo
 */

$slider_options = array(
	'layoutMode'       => 'slider',
	'drag'             => true,
	'auto'             => false,
	'autoTimeout'      => 5000,
	'autoPauseOnHover' => true,
	'showNavigation'   => true,
	'showPagination'   => true,
	'scrollByPage'     => false,
	'gridAdjustment'   => 'responsive',
	'mediaQueries'     => VamtamTemplates::scrollable_columns( $max_columns ),
	'gapHorizontal'    => $settings->gap ? 30 : 0,
	'gapVertical'      => $settings->gap ? 30 : 0,
	'displayTypeSpeed' => 100,
);

if ( VamtamTemplates::early_cube_load() ) {
	wp_enqueue_style( 'cubeportfolio' );
	wp_enqueue_script( 'cubeportfolio' );
}

$wrapper_class = array(
	'portfolios',
	'title-' . $settings->show_title,
	$settings->description ? 'has-description' : 'no-description',
	$settings->gap ? 'has-gap' : 'no-gap',
	$settings->class,
);

$GLOBALS['vamtam_inside_cube'] = true;

?>

<section class="<?php echo esc_attr( implode( ' ', $wrapper_class ) ) ?>">
	<div class="portfolio-items vamtam-cubeportfolio cbp cbp-slider-edge" data-options="<?php echo esc_attr( json_encode( $slider_options ) ) ?>">
		<?php
			while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post();
				include locate_template( 'templates/portfolio/loop/item.php' );
			endwhile;
		?>
	</div>
</section>
<?php

$GLOBALS['vamtam_inside_cube'] = false;

