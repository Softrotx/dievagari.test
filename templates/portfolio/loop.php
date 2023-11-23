<?php

/**
 * Portfolio loop template
 *
 * @package vamtam/mozo
 */

$li_style = '';

$main_id = uniqid();

$cube_options = array(
	'layoutMode'        => $settings->layout,
	'defaultFilter'     => '*',
	'animationType'     => 'slideDelay',
	'gapHorizontal'     => $settings->gap ? 30 : 0,
	'gapVertical'       => $settings->gap ? 30 : 0,
	'gridAdjustment'    => 'responsive',
	'mediaQueries'      => VamtamTemplates::scrollable_columns( $max_columns ),
	'displayType'       => 'bottomToTop',
	'displayTypeSpeed'  => 100,
);

if ( 'ajax' === $settings->link_opens ) {
	$cube_options = array_merge( $cube_options, array(
		'singlePageDelegate'         => '.cbp-singlePage',
		'singlePageDeeplinking'      => true,
		'singlePageStickyNavigation' => true,
		'singlePageCounter'          => '<div class="cbp-popup-singlePage-counter">' . esc_html__( '{{current}} of {{total}}', 'mozo' ) . '</div>',
		'singlePageCallback'         => 'portfolio',
		'singlePageAnimation'        => 'fade',
	) );

	if ( function_exists( 'sharing_display' ) ) {
		wp_enqueue_style( 'sharedaddy' );

		sharing_display( '', true );
	}
}

if ( VamtamTemplates::early_cube_load() ) {
	wp_enqueue_style( 'cubeportfolio' );
	wp_enqueue_script( 'cubeportfolio' );
}

$wrapper_class = array(
	'portfolios',
	'normal',
	'clearfix',
	'title-' . $settings->show_title,
	$settings->description ? 'has-description' : 'no-description',
	$settings->gap ? 'has-gap' : 'no-gap',
	$settings->class,
);

$GLOBALS['vamtam_inside_cube'] = true;

?>

<section class="<?php echo esc_attr( implode( ' ', $wrapper_class ) ) ?>" id="<?php echo esc_attr( $main_id ) ?>">
	<?php
		if ( ! empty( $settings->type_filter ) ) {
			include locate_template( 'templates/portfolio/loop/filters.php' );

			$cube_options['filters'] = '#' . $main_id . '-filters';

			if ( $settings->title_filter ) {
				$cube_options['search'] = '#' . $main_id . '-search';
			}
		}
	?>
	<div class="portfolio-items vamtam-cubeportfolio cbp portfolio-items" data-columns="<?php echo intval( $settings->columns ) ?>" data-options="<?php echo esc_attr( json_encode( $cube_options ) ) ?>" data-hidden-by-filters="<?php esc_attr_e( 'New items were loaded, but they are hidden because of your choice of filters', 'mozo' ) ?>">
		<?php
			while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post();
				include locate_template( 'templates/portfolio/loop/item.php' );
			endwhile;
		?>
	</div>
	<?php
		if ( vamtam_sanitize_bool( $settings->pagination ) ) {
			VamtamTemplates::pagination( null, true, $settings, $portfolio_query );
		}
	?>
</section>

<?php

$GLOBALS['vamtam_inside_cube'] = false;

