<?php

global $content_width;

$photo    = $module->get_data();
$classes  = $module->get_classes();
$src      = $module->get_src();
$link     = $module->get_link();
$alt      = $module->get_alt();
$filetype = pathinfo( $src, PATHINFO_EXTENSION );
$rel      = $module->get_rel();

if ( $filetype === 'svg' && isset( $module->data->sizes->full ) ) {
	$module->data->sizes = (object)array(
		'full' => $module->data->sizes->full,
	);
}

$attrs = '';

if ( isset( $settings->attributes ) ) {
	foreach ( $settings->attributes as $key => $val ) {
		$attrs .= $key . '="' . $val . '" ';
	}
}

if ( is_object( $photo ) && isset( $photo->sizes ) ) {
	foreach ( $photo->sizes as $size ) {
		if ( $size->url == $settings->photo_src && isset( $size->width ) && isset( $size->height ) ) {
			$attrs .= 'height="' . $size->height . '" width="' . $size->width . '" ';
			break; // prevents multiple width/height attrs for SVGs
		}
	}
}

if ( ! empty( $settings->link_type ) ) {
	$attrs .= 'data-link-type="' . esc_attr( $settings->link_type ) . '" ';
}

if ( ! empty( $photo->title ) ) {
	$attrs .= 'title="' . htmlspecialchars( $photo->title ) . '" ';
}

if ( FLBuilderModel::is_builder_active() ) {
	$attrs .= 'onerror="this.style.display=\'none\'" ';
}

if ( isset( $photo->sizes ) && count( get_object_vars( $photo->sizes ) ) > 1 ) {
	if ( $settings->vamtam_sizes_attr !== 'default' ) {
		$global_settings = FLBuilderModel::get_global_settings();

		if ( $settings->vamtam_sizes_attr === 'custom' ) {
			$attrs .= ' sizes="' . esc_attr( $settings->vamtam_sizes_attr_custom ) . '"';
		} elseif ( $settings->vamtam_sizes_attr === 'beaver' ) {
			// walk up the parent tree to calculate the size of this module
			$parent_size = 100;
			$row_size    = false;
			$parent      = FLBuilderModel::get_node( $module->parent );

			$expand_scroll_override = false;

			while ( $parent ) {
				if ( $parent->type === 'column' ) {
					$parent_size *= (float) $parent->settings->size / 100;
				} elseif ( $parent->type === 'row' ) {
					if ( $parent->settings->width === 'full' && $parent->settings->content_width === 'full' ) {
						$row_size = 'full';
					} elseif ( $parent->settings->content_width === 'fixed' ) {
						$row_size = isset( $parent->settings->max_content_width ) && $parent->settings->max_content_width !== '' ? (int) $parent->settings->max_content_width : $content_width;
					}

					if ( isset( $parent->settings->vamtam_animation_style ) && $parent->settings->vamtam_animation_style === 'expand-scroll' ) {
						$expand_scroll_override = true;
					}
				}

				$parent = FLBuilderModel::get_node( $parent->parent );
			}

			$parent_size = ceil( $parent_size );

			$row_limit = '';
			if ( $row_size && $row_size !== 'full' ) {
				$row_limit = "(min-width: {$row_size}px) " . round( $row_size * $parent_size / 100 ) . "px,";
			}

			if ( $expand_scroll_override ) {
				$attrs .= ' sizes="50vw"';
			} else {
				$attrs .= ' sizes="(max-width: ' . $global_settings->responsive_breakpoint . 'px) 100vw, ' . $row_limit . esc_attr( $parent_size ) . 'vw"';
			}
		} else {
			$global_settings = FLBuilderModel::get_global_settings();

			$attrs .= ' sizes="(max-width: ' . $global_settings->responsive_breakpoint . 'px) 100vw, ' . esc_attr( $settings->vamtam_sizes_attr ) . '"';
		}
	}
}

?>
<div class="fl-photo<?php if ( ! empty( $settings->crop ) ) { echo ' fl-photo-crop-' . $settings->crop ;} ?> fl-photo-align-<?php echo esc_attr( $settings->align ) ?>" itemscope itemtype="http://schema.org/ImageObject">
	<div class="fl-photo-content fl-photo-img-<?php echo esc_attr( $filetype ) ?>">
		<?php if ( ! empty( $link ) ) : ?>
		<a href="<?php echo esc_url( $link ) ?>" target="<?php echo esc_attr( isset( $settings->link_target ) ? $settings->link_target : $settings->link_url_target ); ?>"<?php echo $rel // xss ok ?> itemprop="url" title="<?php echo esc_attr( $alt ) ?>">
		<?php endif; ?>
		<?php if ( $settings->photo_source === 'library' && isset( $photo->width ) && isset( $photo->height ) ) : ?>
			<?php
				$classes .= ' vamtam-lazyload-noparent';
				$img = '<img class="' . esc_attr( $classes ) . '" src="' . esc_attr( $src ) . '" alt="' . esc_attr( $alt ) . '" itemprop="image" ' . $attrs . '/>';

				if ( function_exists( 'wp_filter_content_tags' ) ) {
					$img = wp_filter_content_tags( $img );
				}

				echo apply_filters( 'vamtam_maybe_lazyload', $img, $photo->id, array( $photo->width, $photo->height ), false );
			?>
		<?php else: ?>
			<img class="<?php echo esc_attr( $classes ) ?>" src="<?php echo esc_attr( $src ) ?>" alt="<?php echo esc_attr( $alt ) ?>" itemprop="image" <?php echo $attrs // xss ok ?> />
		<?php endif ?>
		<?php if ( ! empty( $link ) ) : ?>
		</a>
		<?php endif; ?>
		<?php if ( $photo && ! empty( $photo->caption ) && 'hover' == $settings->show_caption ) : ?>
		<div class="fl-photo-caption fl-photo-caption-hover" itemprop="caption"><?php echo wp_kses_post( $photo->caption ) ?></div>
		<?php endif; ?>
	</div>
	<?php if ( $photo && ! empty( $photo->caption ) && 'below' == $settings->show_caption ) : ?>
	<div class="fl-photo-caption fl-photo-caption-below" itemprop="caption"><?php echo wp_kses_post( $photo->caption ) ?></div>
	<?php endif; ?>
</div>
