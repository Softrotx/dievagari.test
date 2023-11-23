<?php

$show = vamtam_get_optionb( 'post-meta', 'author' );
if ( $show || is_customize_preview() ) :

?>
	<div class="author vamtam-meta-author" <?php VamtamTemplates::display_none( $show ) ?>><?php VamtamTemplates::the_author_posts_link_with_icon()?></div>
<?php endif ?>
