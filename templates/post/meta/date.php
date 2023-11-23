<?php if ( vamtam_get_optionb( 'post-meta', 'date' ) || is_customize_preview() ) : ?>
	<div class="post-date vamtam-meta-date" <?php VamtamTemplates::display_none( vamtam_get_optionb( 'post-meta', 'date' ) ) ?>>
		<a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>">
			<?php the_time( get_option( 'date_format' ) ); ?>
		</a>
	</div>
<?php endif ?>