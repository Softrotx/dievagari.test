<div class="grid-1-2 lowres-width-override lowres-grid-1-2" id="top-nav-social">
	<?php if ( vamtam_get_option( 'top-bar-social-lead' ) !== '' ) :  ?>
		<span class="top-bar-social-lead"><?php echo vamtam_get_option( 'top-bar-social-lead' ) // xss ok ?></span>
	<?php endif ?>
	<?php
		$map = array(
			'fb'        => 'facebook',
			'twitter'   => 'twitter',
			'gplus'     => 'google',
			'linkedin'  => 'linkedin2',
			'pinterest' => 'pinterest2',
			'flickr'    => 'flickr',
			'dribbble'  => 'dribbble',
			'instagram' => 'instagram',
			'youtube'   => 'youtube',
			'vimeo'     => 'vimeo',
		);

		foreach ( $map as $option => $icon ) :  ?>
			<?php if ( vamtam_get_option( "top-bar-social-$option" ) !== '' ) :  ?>
				<a href="<?php echo esc_url( vamtam_get_option( "top-bar-social-$option" ) ) ?>" target="_blank"><?php
					echo vamtam_get_icon_html( array( // xss ok
						'name'       => $icon,
						'link_hover' => false,
					) );
				?></a>
			<?php endif ?>
		<?php endforeach; ?>
</div>

