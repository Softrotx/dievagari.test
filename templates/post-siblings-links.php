<?php

/**
 * Prev/next/view all buttons for posts and projects
 *
 * @package vamtam/mozo
 */

$title = '<span class="sibling-title">%title</span>';

?>
<span class="post-siblings">
	<?php
		previous_post_link( '%link', vamtam_get_icon_html( array(
			'name' => 'vamtam-theme-arrow-left-sample',
			'link_hover' => false,
		) ) . $title );
		next_post_link( '%link', $title . vamtam_get_icon_html( array(
			'name' => 'vamtam-theme-arrow-right-sample',
			'link_hover' => false,
		) ) );
	?>
</span>

