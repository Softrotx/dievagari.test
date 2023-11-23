<?php
$show = vamtam_get_optionb( 'post-meta', 'tax' );

$categories_list = get_the_category_list( ', ' );

if ( $categories_list && ( $show || is_customize_preview() ) ) :
?>
	<div class="vamtam-meta-tax the-categories" <?php VamtamTemplates::display_none( $show ) ?>><span class="visuallyhidden"><?php esc_html_e( 'Category', 'mozo' ) ?> </span><?php echo $categories_list // xss ok ?></div>
<?php
endif;
