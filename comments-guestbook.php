<?php
/**
 * Comments template
 *
 * @package vamtam/mozo
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Please do not load this page directly. Thanks!' );
}

?>

<div class="limit-wrapper vamtam-box-outer-padding">
<?php if ( 'open' === $post->comment_status ) : ?>
	<div id="comments">
		<div class="respond-box">
			<?php
				$req = get_option( 'require_name_email' );

				// cookies consent
				$commenter = wp_get_current_commenter();
				$consent   = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

				comment_form( array(
					'title_reply'    => '',
					'title_reply_to' => '',
					'fields'         => array(
							'author' => '<div class="second-row"><div class="comment-form-author form-input"><label for="author" class="visuallyhidden">' . esc_html__( 'Name:', 'mozo' ) . '</label>' . ( $req ? ' <span class="required">*</span>' : '' ) .
							'<input id="author" autocomplete="name" name="author" type="text" ' . ( $req ? 'required="required"' : '' ) . ' value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" placeholder="' . esc_attr__( 'John Doe', 'mozo' ) . '" /></div>',
							'email'  => '<div class="comment-form-email form-input"><label for="email" class="visuallyhidden">' . esc_html__( 'Email:', 'mozo' ) . '</label> ' . ( $req ? ' <span class="required">*</span>' : '' ) . '<span class="comment-note">' . esc_html__( 'Your email address will not be published.', 'mozo' ) . '</span>
							<input id="email" autocomplete="email" name="email" type="email" ' . ( $req ? 'required="required"' : '' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" placeholder="' . esc_attr__( 'email@example.com', 'mozo' ) . '" /></div></div>',
							'cookies' => '<p class="comment-form-cookies-consent"><label for="wp-comment-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' /> ' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'mozo' ) . '</label></p>',
					),
					'comment_field'        => '<div class="comment-form-comment"><label for="comment" class="visuallyhidden">' . esc_html__( 'Message:', 'mozo' ) . '</label><textarea id="comment" name="comment" required placeholder="' . esc_attr__( 'Write your comments', 'mozo' ) . '" rows="2"></textarea></div>',
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'label_submit'         => esc_html__( 'Add message', 'mozo' ),
					'format'               => 'xhtml', // otherwise we get novalidate on the form
				) );
			?>
		</div><!-- .respond-box -->

		<?php
			$req = get_option( 'require_name_email' ); // Checks if fields are required.

			if ( ! empty( $post->post_password ) ) :
				if ( post_password_required() ) :
		?>
					</div><!-- #comments -->

	<?php
					return;
				endif;
			endif;
	?>

	<?php if ( have_comments() ) : ?>
		<?php // numbers of pings and comments
		$ping_count = $comment_count = 0;
		foreach ( $comments as $comment ) {
			get_comment_type() == 'comment' ? ++$comment_count : ++$ping_count;
		}
		?>

		<h5 class="comments-title"><?php
			comments_popup_link(
				wp_kses( __( '0 <span class="comment-word">People wrote to us:</span>', 'mozo' ), 'vamtam-a-span' ),
				wp_kses( __( '1 <span class="comment-word">Person wrote to us:</span>', 'mozo' ), 'vamtam-a-span' ),
				wp_kses( __( '% <span class="comment-word">People wrote to us:</span>', 'mozo' ), 'vamtam-a-span' )
			);
		?></h5>

		<?php if ( $comment_count ) : ?>
			<?php
				$cube_options = array(
					'layoutMode'        => 'grid',
					'sortToPreventGaps' => true,
					'defaultFilter'     => '*',
					'animationType'     => 'quicksand',
					'gapHorizontal'     => 30,
					'gapVertical'       => 30,
					'gridAdjustment'    => 'responsive',
					'mediaQueries'      => VamtamTemplates::scrollable_columns( 3 ),
					'displayType'       => 'bottomToTop',
					'displayTypeSpeed'  => 100,
				);

				wp_enqueue_script( 'cubeportfolio' );
				wp_enqueue_style( 'cubeportfolio' );
			?>
			<div id="comments-list" class="comments vamtam-comments-small vamtam-cubeportfolio cbp" data-columns="3" data-options="<?php echo esc_attr( json_encode( $cube_options ) ) ?>">
				<?php
					wp_list_comments( array(
						'avatar_size'       => 0,
						'type'              => 'comment',
						'reply_allowed'     => false,
						'max_depth'         => 0,
						'vamtam-layout'     => 'small',
						'callback'          => array( 'VamtamTemplates', 'comments' ),
						'reverse_top_level' => true,
						'reverse_children'  => true,
						'style'             => 'div',
					) );
				?>
			</div><!-- #comments-list.comments -->
		<?php endif; /* if ( $comment_count ) */ ?>
	<?php endif /* if ( $comments ) */ ?>

	<?php
		the_comments_pagination( array(
			'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous', 'mozo' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'mozo' ) . '</span>',
		) );
	?>
</div><!-- #comments -->

<?php endif /* if ( 'open' == $post->comment_status ) */ ?>
</div>

