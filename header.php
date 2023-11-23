<?php
/**
 * Header template
 *
 * @package vamtam/mozo
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo sanitize_hex_color( vamtam_get_option( 'accent-color', 1 ) ) ?>">

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="top"></div>
	<?php
		do_action( 'wp_body_open' );
		do_action( 'vamtam_body' );

		$slider_above_header = is_singular( VamtamFramework::$complex_layout ) && vamtam_post_meta( null, 'sticky-header-type', true ) === 'below';

		if ( $slider_above_header ) {
			get_template_part( 'templates/header/middle' );
		}

		get_template_part( 'templates/header' );
	?>
	<div id="page" class="main-container">
		<?php
			if ( ! $slider_above_header ) {
				get_template_part( 'templates/header/middle' );
			}
		?>

		<div id="main-content">
			<?php
				if ( ! is_single() || VamtamTemplates::get_layout() === 'full' ) {
					get_template_part( 'templates/header/sub-header' );
				}
			?>

			<?php $hide_lowres_bg = vamtam_get_optionb( 'main-background-hide-lowres' ) ? 'vamtam-hide-bg-lowres' : ''; ?>
			<div id="main" role="main" class="vamtam-main layout-<?php echo esc_attr( VamtamTemplates::get_layout() ) ?>  <?php echo esc_attr( $hide_lowres_bg ) ?>">
				<?php do_action( 'vamtam_inside_main' ) ?>

				<?php if ( VamtamTemplates::had_limit_wrapper() ) : ?>
					<div class="limit-wrapper vamtam-box-outer-padding">
				<?php endif ?>

