<?php
$show         = vamtam_get_optionb( 'post-meta', 'comments' ) && comments_open();
$comment_icon = vamtam_get_icon_html( array(
	'name' => 'vamtam-theme-bubble',
) );
?>
<?php if ( $show || is_customize_preview() ) : ?>
	<div class="comment-count vamtam-meta-comments" <?php VamtamTemplates::display_none( $show ) ?>>
		<?php
			comments_popup_link(
				$comment_icon . wp_kses( __( '0 <span class="comment-word ">Comments</span>', 'mozo' ), 'vamtam-a-span' ),
				$comment_icon . wp_kses( __( '1 <span class="comment-word ">Comment</span>', 'mozo' ), 'vamtam-a-span' ),
				$comment_icon . wp_kses( __( '% <span class="comment-word ">Comments</span>', 'mozo' ), 'vamtam-a-span' )
			);
		?>
	</div>
<?php endif; ?>
