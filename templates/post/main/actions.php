<div class="post-actions-wrapper">
	<?php get_template_part( 'templates/post/meta/author' ) ?>

	<?php get_template_part( 'templates/post/meta/date' ) ?>

	<?php if ( ! post_password_required() ) :  ?>
		<?php get_template_part( 'templates/post/meta/comments' ) ?>
	<?php endif ?>

	<?php edit_post_link( '<span class="icon">' . vamtam_get_icon( 'pencil' ) . '</span><span class="visuallyhidden">' . esc_html__( 'Edit', 'mozo' ) . '</span>' ) ?>
</div>