<?php

function vamtam_generate_missing_hc_accent_color_defaults() {
	global $vamtam_defaults, $vamtam_theme_customizer;
	$options               = get_option( 'vamtam_theme' );
	$option_defs           = $vamtam_theme_customizer->get_fields_by_id();
	$option_defs_choices   = $option_defs['accent-color']['choices'];
	$options_value_changed = false;

	if ( empty( $options ) ) {
		update_option( 'vamtam_theme', $vamtam_defaults );
		$options = $vamtam_theme_customizer->get_options();
	}

	if ( ! empty( $options['accent-color'] ) && ! empty( $option_defs_choices ) ) {
		foreach ( $option_defs_choices as $key => $choice ) {
			if ( ! isset( $options['accent-color'][ $key . '-hc' ] ) ) {
				$options_value_changed = true;
				$color                 = new VamtamColor( $options['accent-color'][ $key ] );
				$hc                    = '';
				if ( $color->luminance > 0.4 ) {
					$hc = '#000000';
				} else {
					$hc = '#ffffff';
				}
				$options['accent-color'][ $key . '-hc' ] = $hc;
			}
		}
	}

	if ( $options_value_changed ) {
		update_option( 'vamtam_theme', $options );
	}
}


add_action( 'init', 'vamtam_generate_missing_hc_accent_color_defaults' );