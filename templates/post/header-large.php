<?php

/**
 * Post heade template
 *
 * @package vamtam/mozo
 */

global $post;

$title = get_the_title();

$show = ! has_post_format( 'status' ) && ! has_post_format( 'aside' ) && ! empty( $title );

if ( $show ) :
	$link = has_post_format( 'link' ) ?
				get_post_meta( $post->ID, 'vamtam-post-format-link', true ) :
				get_permalink();
	?>
		<header class="single">
			<div class="content">
				<h2>
					<a href="<?php echo esc_url( $link ) ?>" title="<?php the_title_attribute()?>"><?php the_title(); ?></a>
				</h2>
			</div>
		</header>
	<?php
endif;

