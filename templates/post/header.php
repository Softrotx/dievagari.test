<?php

/**
 * Post heade template
 *
 * @package vamtam/mozo
 */

global $post;

$title = get_the_title();

$show = ! $blog_query->is_single() && ! has_post_format( 'status' ) && ! has_post_format( 'aside' ) && ! empty( $title );

if ( $show ) :
	$link = has_post_format( 'link' ) ?
				get_post_meta( $post->ID, 'vamtam-post-format-link', true ) :
				get_permalink();
	?>
		<header class="single">
			<div class="content">
				<h3>
					<a href="<?php echo esc_url( $link ) ?>" title="<?php the_title_attribute()?>" class="entry-title"><?php the_title(); ?></a>
				</h3>
			</div>
		</header>
	<?php
endif;

