<?php
	$comment_class = array( 'clearfix' );

	if ( $args['has_children'] ) {
		$comment_class[] = 'has-children';
	}

	if ( 'pings' === $args['type'] ) {
		$comment_class[] = 'comment';
	}
?>
<div id="comment-<?php comment_ID() ?>" <?php comment_class( implode( ' ', $comment_class ) ) ?>>
	<div id="div-comment-<?php comment_ID() ?>" class="single-comment-wrapper">
		<?php if ( $comment->comment_type === 'comment' ) : ?>
			<div class="comment-author">
				<?php echo get_avatar( get_comment_author_email(), 73 ); ?>
			</div>
		<?php endif ?>
		<div class="comment-content">
			<div class="comment-meta">
				<div class="comment-meta-inner comment-meta-left">
					<div class="comment-author-link"><?php comment_author_link(); ?></div>
					<div title="<?php comment_time(); ?>" class="comment-time"><?php comment_date(); ?></div>
				</div>
				<div class="comment-meta-inner comment-meta-right">
					<?php edit_comment_link( sprintf( '[%s]', esc_html__( 'Edit', 'mozo' ) ) ) ?>
					<?php
						if ( $args['type'] == 'all' || get_comment_type() == 'comment' ) :
							comment_reply_link( array_merge( $args, array(
								'reply_text' => esc_html__( 'Reply', 'mozo' ),
								'login_text' => esc_html__( 'Log in to reply.', 'mozo' ),
								'depth'      => $depth,
								'before'     => '<h6 class="comment-reply-link">',
								'after'      => '</h6>',
							) ) );
						endif;
					?>
				</div>
			</div>
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<span class='unapproved'><?php esc_html_e( 'Your comment is awaiting moderation.', 'mozo' ); ?></span>
			<?php endif ?>
			<?php comment_text() ?>
		</div>
	</div>
