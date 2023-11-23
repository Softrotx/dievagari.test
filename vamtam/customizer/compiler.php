<?php

function vamtam_recompile_css() {
	global $vamtam_theme;

	vamtam_customizer_compiler( $vamtam_theme );
}

// "clear cache" implementation
function vamtam_actions() {
	if ( isset( $_GET['vamtam_action'] ) ) {
		if ( 'clear_cache' === $_GET['vamtam_action'] ) {
			vamtam_recompile_css();

			wp_redirect( admin_url() );
		}
	}
}
add_action( 'admin_init', 'vamtam_actions' );

// we need font-style and font-weight to be in a single variable
function vamtam_customizer_normalize_typography( $options ) {
	foreach ( $options as $name => $value ) {
		if ( is_array( $value ) && isset( $value['font-family'] ) ) {
			$options[ $name ]['font-weight'] = isset( $value['variant'] ) ? $value['variant'] : 'normal';

			unset( $options[ $name ]['variant'] );
		}
	}

	return $options;
}
add_filter( 'vamtam_customizer_compiler_options', 'vamtam_customizer_normalize_typography' );

function vamtam_customizer_compiler( $options ) {
	if ( is_network_admin() ) {
		if ( class_exists( 'FLBuilderAdminSettings' ) ) {
			FLBuilderAdminSettings::clear_cache_for_all_sites();
		}
	} else {
		if ( class_exists( 'FLBuilderModel' ) ) {
			// Clear builder cache.
			FLBuilderModel::delete_asset_cache_for_all_posts();
		}

		// Clear theme cache.
		if ( class_exists( 'FLCustomizer' ) && method_exists( 'FLCustomizer', 'clear_all_css_cache' ) ) {
			FLCustomizer::clear_all_css_cache();
		}
	}

	update_option( 'vamtam-css-cache-timestamp', time() );
}
add_action( 'vamtam_customizer/' . $opt_name . '/compiler', 'vamtam_customizer_compiler', 10, 1 );

function vamtam_export_beaver_options_to_less( $options ) {
	$settings = array(
		'module_margins' => 10,
	);

	if ( class_exists( 'FLBuilderModel' ) ) {
		$global_settings = (array)FLBuilderModel::get_global_settings();
		$settings = array_filter( $global_settings, 'is_string' );

		foreach ( [ 'top', 'right', 'bottom', 'left' ] as $dir ) {
			$name = 'module_margins_' . $dir;

			$value = preg_replace( FLBuilder::regex( 'css_unit' ), '', strtolower( $global_settings[ $name ] ) );
			$unit  = $global_settings[ 'module_margins_unit' ];

			$previous = '';

			unset( $settings[ $name ] );

			if ( is_numeric( $value ) && ! empty( $unit ) ) {
				$previous = $value . $unit;
			}

			if ( $global_settings['responsive_enabled'] ) {
				foreach ( [ 'large', 'medium', 'responsive' ] as $size ) {
					$name = "module_margins_{$dir}_{$size}";

					$value = preg_replace( FLBuilder::regex( 'css_unit' ), '', strtolower( $global_settings[ $name ] ) );
					$unit  = $global_settings[ "module_margins_{$size}_unit" ];

					if ( is_numeric( $value ) && ! empty( $unit ) ) {
						$previous = $settings[ $name ] = $value . $unit;
					} else {
						$settings[ $name ] = $previous;
					}
				}
			} else {
				$settings[ $name ] = $previous;
			}
		}


		// $settings['module_margins_left'] = (int)( $global_settings['module_margins_left'] ?? 0 ) . ( $global_settings['module_margins_left'] ?? 0 )
	}

	$options['beaver-global'] = $settings;

	return $options;
}
add_filter( 'vamtam_less_vars', 'vamtam_export_beaver_options_to_less' );
