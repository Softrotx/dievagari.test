<?php
/**
 * Post metadata template
 *
 * @package vamtam/mozo
 */

?>
<div class="post-meta">
	<nav>
		<?php
			$show = vamtam_get_optionb( 'post-meta', 'tax' );

			if ( $show || is_customize_preview() ) :
				$tags = get_the_tags();

				if ( $tags ) : ?>
					<div class="the-tags vamtam-meta-tax" <?php VamtamTemplates::display_none( $show ) ?>>
						<?php the_tags( '<strong>' . esc_html__( 'Tags:', 'mozo' ) . '</strong> ', ', ', '' ); ?>
					</div>
			<?php
				endif;
			endif;
		?>
	</nav>
</div>

