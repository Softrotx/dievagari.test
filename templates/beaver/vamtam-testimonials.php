<?php

$content = get_the_content();
$cite    = get_post_meta( get_the_ID(), 'testimonial-author', true );
$link    = get_post_meta( get_the_ID(), 'testimonial-link', true );
$rating  = (int) get_post_meta( get_the_ID(), 'testimonial-rating', true );
$summary = get_post_meta( get_the_ID(), 'testimonial-summary', true );
$title   = get_the_title();

$rating_str = '';

if ( ! empty( $link ) && ! empty( $cite ) ) {
	$cite = '<a href="' . esc_url( $link ) . '" target="_blank">' . $cite . '</a>';
}

if ( ! empty( $title ) ) {

	if ( vamtam_sanitize_bool( $settings->show_rating ) && $rating > 0 ) {
		$rating_str = str_repeat(
			vamtam_get_icon_html( array(
				'name' => 'star3',
			) ),
			$rating
		);

		$rating_str .= ' &mdash; ';
	}

	if ( ! empty( $cite ) ) {
		$cite = " <span class='company-name'>( $cite )</span>";
	}

	$title = "<div class='quote-title'>$rating_str<span class='the-title'>$title</span>$cite</div>";
} elseif ( ! empty( $cite ) ) {
	$title = "<div class='quote-title'>$cite</div>";
}

?>

<blockquote <?php post_class( 'clearfix small simple alignment-' . $settings->alignment ) ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="quote-thumbnail">
			<?php the_post_thumbnail( 'thumbnail' ); ?>
		</div>
	<?php endif ?>

	<div class='quote-text'>
		<?php if ( ! empty( $summary ) ) : ?>
			<h3 class="quote-summary"><?php echo wp_kses_post( $summary ) ?></h3>
		<?php endif ?>

		<div class="quote-title-wrapper clearfix">
			<?php echo wp_kses_post( $title ) ?>
		</div>

		<div class="quote-content">
			<?php echo wpautop( do_shortcode( $content ) ) // xss ok ?>
		</div>
	</div>
</blockquote>
