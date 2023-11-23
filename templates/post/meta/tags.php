<?php
$show = vamtam_get_optionb( 'post-meta', 'tax' );

if ( $show || is_customize_preview() ) :
	$tags = get_the_tags();

	if ( $tags ) : ?>
		<div class="the-tags vamtam-meta-tax" <?php VamtamTemplates::display_none( $show ) ?>>
			<?php the_tags( '<strong class="' . ( is_single() ? '' : 'visuallyhidden' ) . '">' . esc_html__( 'Tags:', 'mozo' ) . '</strong> ', ', ', '' ); ?>
		</div>
<?php
	endif;
endif;
