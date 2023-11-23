<?php

$biography = trim( $settings->biography );
$icons_map = array(
	'googleplus' => 'vamtam-theme-google-plus',
	'linkedin'   => 'vamtam-theme-linkedin',
	'facebook'   => 'vamtam-theme-facebook',
	'twitter'    => 'vamtam-theme-twitter',
	'youtube'    => 'vamtam-theme-youtube',
	'pinterest'  => 'vamtam-theme-pinterest',
	'lastfm'     => 'vamtam-theme-last-fm',
	'instagram'  => 'vamtam-theme-instagram',
	'dribble'    => 'vamtam-theme-dribbble',
	'vimeo'      => 'vamtam-theme-vimeo',
);

$used_icons = array();

foreach ( $icons_map as $icon => $icon_name ) {
	if ( ! empty( $settings->$icon ) ) {
		$used_icons[] = array(
			'url'  => $settings->$icon,
			'name' => $icon_name,
		);
	}
}

?>
<div class="team-member <?php echo ( ! empty( $biography ) ? 'has-content' : '' ) ?>">
	<?php if ( ! empty( $settings->picture_src ) ) :  ?>
		<div class="thumbnail">
			<?php if ( ! empty( $settings->url ) ) : ?>
				<a href="<?php echo esc_url( $settings->url ) ?>" title="<?php echo esc_attr( $settings->name ) ?>">
			<?php endif ?>
				<?php echo VamtamTemplates::lazyload_image( $settings->picture, 'full' ); ?>
			<?php if ( ! empty( $settings->url ) ) : ?>
				</a>
			<?php endif ?>

			<?php if ( ! empty( $used_icons ) ): ?>
				<div class="share-icons clearfix">
					<?php foreach ( $used_icons as $icon ) : ?>
						<a href="<?php echo esc_url( $icon['url'] )?>"><?php echo vamtam_get_icon_html( array( // xss ok
							'name' => $icon['name'],
						) ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif ?>
		</div>
	<?php endif ?>
	<div class="team-member-info">
		<?php if ( ! empty( $settings->position ) ) :  ?>
			<h5 class="regular-title-wrapper team-member-position"><?php echo wp_kses_post( $settings->position ) ?></h5>
		<?php endif ?>

		<h3>
			<?php if ( ! empty( $settings->url ) ) : ?>
				<a href="<?php echo esc_url( $settings->url ) ?>" title="<?php echo esc_attr( $settings->name ) ?>">
			<?php endif ?>
				<?php echo wp_kses_post( $settings->name ) ?>
			<?php if ( ! empty( $settings->url ) ) : ?>
				</a>
			<?php endif ?>
		</h3>

		<?php if ( ! empty( $settings->phone ) ) : ?>
			<div class="team-member-phone"><a href="tel:<?php echo esc_attr( $settings->phone ) ?>" title="<?php echo esc_attr( sprintf( 'Call %s', $settings->name ) ) ?>"><?php esc_html_e( 'Tel:', 'mozo' ) ?> <?php echo wp_kses_post( $settings->phone ) ?></a></div>
		<?php endif ?>
		<?php if ( ! empty( $settings->email ) ) : ?>
			<div  class="team-member-email"><a href="mailto:<?php echo esc_attr( $settings->email ) ?>" title="<?php echo esc_attr( sprintf( __( 'email %s', 'mozo' ), $settings->name ) ) ?>"><?php echo wp_kses_post( $settings->email ); ?></a></div>
		<?php endif ?>

		<?php if ( ! empty( $settings->biography ) ) :  ?>
			<div class="team-member-bio">
				<?php echo do_shortcode( $settings->biography ) ?>
			</div>
		<?php endif ?>
	</div>
</div>
