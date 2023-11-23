<?php

class VamtamGallery {
	public static function gallery( $attr ) {
		// Allow plugins/child themes to override the default gallery template.
		$output = apply_filters( 'vamtam_post_gallery', '', $attr );

		if ( '' != $output ) {
			return $output;
		}

		$attachments = self::get_attachments( $attr );

		if ( empty( $attachments ) )
			return '';

		if ( is_feed() ) {
			$output      = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
			return $output;
		}

		$inside_cube = isset( $GLOBALS['vamtam_inside_cube'] ) && $GLOBALS['vamtam_inside_cube'];

		if ( $inside_cube ) {
			$output .= '<div class="cbp-slider-inline">';
			$output .= '<div class="cbp-slider-wrapper">';

			VamtamOverrides::unlimited_image_sizes(); // check for matching VamtamOverrides::limit_image_sizes(); below
		} else {
			if ( VamtamTemplates::early_cube_load() ) {
				wp_enqueue_style( 'cubeportfolio' );
				wp_enqueue_script( 'cubeportfolio' );
			}

			$slider_options = array(
				'layoutMode'       => 'slider',
				'drag'             => true,
				'auto'             => false,
				'autoTimeout'      => 5000,
				'autoPauseOnHover' => true,
				'showNavigation'   => true,
				'showPagination'   => true,
				'rewindNav'        => true,
				'gridAdjustment'   => 'responsive',
				'mediaQueries'     => array(
					array(
						'width' => 1,
						'cols'  => 1,
					),
				),
				'gapHorizontal' => 0,
				'gapVertical'   => 0,
				'caption'       => '',
				'displayType'   => 'default',
			);

			$output .= '<div class="vamtam-cubeportfolio cbp cbp-slider-edge" data-options="' . esc_attr( json_encode( $slider_options ) ) . '">';
		}

		foreach ( $attachments as $id => $attachment ) {

			$image = wp_get_attachment_image( $id, $attr['size'] );

			if ( ! empty( $image ) ) {
				if ( $inside_cube ) {
					$output .= '<div class="cbp-slider-item cbp-slider-item--active">';
					$output .= '<a href="' . esc_url( get_permalink() ) . '" title="' . the_title_attribute( array(
						'echo' => false,
					) ) . '">';
					$output .= $image;
					$output .= '</a>';
					$output .= '</div>';
				} else {
					$output .= '<div class="cbp-item">';
					$output .= $image;
					$output .= '</div>';
				}
			}
		}

		if ( $inside_cube ) {
			VamtamOverrides::limit_image_sizes();

			$output .= '</div>
				<div class="cbp-slider-controls">
					<div class="cbp-slider-prev"></div>
					<div class="cbp-slider-next"></div>
				</div>
			</div>';
		} else {
			$output .= '</div>';
		}

		return $output;
	}

	public static function gallery_lightbox( $attr ) {
		// Allow plugins/child themes to override the default gallery template.
		$output = apply_filters( 'vamtam_post_gallery_lightbox', '', $attr );
		if ( '' != $output ) {
			return $output;
		}

		$attachments = self::get_attachments( $attr );

		if ( empty( $attachments ) )
			return '';

		if ( is_feed() ) {
			return '';
		}

		foreach ( $attachments as $id => $attachment ) {
			$image_src = wp_get_attachment_image_src( $id, 'full' );

			if ( ! empty( $image_src ) && ! empty( $image_src[0] ) ) {
				$output .= '<a href="' . esc_url( $image_src[0] ) . '" title="' . esc_attr__( 'View Gallery Item', 'mozo' ) . '" class="cbp-lightbox vamtam-lightbox-gallery"  data-title="' . esc_attr( get_the_title() ) . '"></a>';
			}
}

		return $output;
	}

	public static function get_attachments( $attr ) {
		extract( self::process_atts( $attr ) );

		$id = intval( $id );
		if ( 'RAND' == $order ) {
			$orderby = 'none';
		}

		if ( ! empty( $include ) ) {
			$_attachments = get_posts( array(
				'include' => $include,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby,
			) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[ $val->ID ] = $_attachments[ $key ];
			}
		} elseif ( ! empty( $exclude ) ) {
			$attachments = get_children( array(
				'post_parent' => $id,
				'exclude' => $exclude,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby,
			) );
		} else {
			$attachments = get_children( array(
				'post_parent' => $id,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $order,
				'orderby' => $orderby,
			) );
		}

		return $attachments;
	}

	public static function process_atts( $attr ) {
		$post = get_post();

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}

			$attr['include'] = $attr['ids'];
		}

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );

			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		return shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'pausetime'  => 3000,
			'direction'  => 'none',
			'where'      => 'single',
		), $attr, 'gallery');
	}
}
