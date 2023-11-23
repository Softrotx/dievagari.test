<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/review.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class( 'comment' ); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="single-comment-wrapper">

		<div class="comment-author">
			<?php
			/**
			 * The woocommerce_review_before hook
			 *
			 * @hooked woocommerce_review_display_gravatar - 10
			 */
				do_action( 'woocommerce_review_before', $comment );
			?>
		</div>
		<div class="comment-content">
			<div class="comment-meta">
				<div class="comment-meta-inner comment-meta-left">
					<div class="comment-author-link"><?php comment_author_link(); ?></div>
					<div title="<?php comment_time(); ?>" class="comment-time"><?php comment_date(); ?></div>

					<?php
						/**
						 * The woocommerce_review_before_comment_meta hook.
						 *
						 * @hooked woocommerce_review_display_rating - 10
						 */
						do_action( 'woocommerce_review_before_comment_meta', $comment );

						/**
						 * The woocommerce_review_meta hook.
						 *
						 * @hooked woocommerce_review_display_meta - 10
						 */
						do_action( 'woocommerce_review_meta', $comment );
					?>
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

			<?php
				do_action( 'woocommerce_review_before_comment_text', $comment );

				/**
				 * The woocommerce_review_comment_text hook
				 *
				 * @hooked woocommerce_review_display_comment_text - 10
				 */
				do_action( 'woocommerce_review_comment_text', $comment );

				do_action( 'woocommerce_review_after_comment_text', $comment );
			?>
		</div>
	</div>
