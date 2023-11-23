<?php

/**
 * LESSPHP wrapper
 *
 * @package vamtam/mozo
 */

/**
 * class VamtamLessBridge
 */
class VamtamLessBridge {

	/**
	 * List of option names which are known to be percentages
	 *
	 * @var array
	 */
	public static $percentages = array(
		'left-sidebar-width',
		'right-sidebar-width',
	);

	/**
	 * List of option names which are known to be numbers
	 *
	 * @var array
	 */
	public static $numbers = array(
	);

	public static function prepare_vars_for_export( $vars_raw, $deprecated = '' ) {
		global $wpdb, $vamtam_defaults;

		$vars_raw = self::flatten_vars( apply_filters( 'vamtam_less_vars', $vars_raw ) );

		$vars = array();

		foreach ( $vars_raw as $name => $value ) {
			if ( trim( $value ) === '' && preg_match( '/\bbackground-image\b/i', $name ) ) {
				$vars[ $name ] = '';
				continue;
			}

			if ( preg_match( '/^[-\w\d]+$/i', $name ) ) {
				$vars[ $name ] = self::prepare( $name, $value );
			}
		}

		$vars = array_merge(
			$vars,
			apply_filters( 'vamtam-additional-css-variables', include( VAMTAM_CSS_DIR . 'additional-css-variables.php' ) )
		);

		// -----------------------------------------------------------------------------
		$out = array();

		foreach ( $vars as $name => $value_raw ) {
			$value = $value_raw;

			if ( ! $value_raw ) {
				$value = '';

				if ( strpos( $name, 'background-attachment' ) !== false ) {
					$value = 'scroll';
				} elseif ( strpos( $name, 'background-position' ) !== false ) {
					$value = 'left top';
				} elseif ( strpos( $name, 'background-repeat' ) !== false ) {
					$value = 'no-repeat';
				} elseif ( strpos( $name, 'background-size' ) !== false ) {
					$value = 'auto';
				}
			}

			if ( ! is_null( $value_raw ) ) {
				if ( preg_match( '/-variant$/', $name ) ) {
					$weight = 'normal';
					$style  = 'normal';

					$value = explode( ' ', $value );

					if ( count( $value ) === 2 ) {
						list( $weight, $style ) = $value;
					} elseif ( $value[0] === 'italic' ) {
						$style = 'italic';
					} else {
						$weight = $value[0];
					}

					$name = str_replace( 'variant', '', $name );
					$out[ $name . 'font-weight' ] = $weight;
					$out[ $name . 'font-style' ] = $style;
				} elseif ( preg_match( '/-background-image$/', $name ) ) {
					$out[ $name ] = $value === '' ? 'none' : "url({$value})";
				} elseif ( ! preg_match( '/-(weight|style)$/', $name ) ) {
					$out[ $name ] = $value;
				}
			}
		}

		return $out;
	}

	private static function flatten_vars( $vars, $prefix = '' ) {
		$flat_vars = array();

		foreach ( $vars as $key => $var ) {
			if ( is_array( $var ) ) {
				$flat_vars = array_merge( $flat_vars, self::flatten_vars( $var, $prefix . $key . '-' ) );

				unset( $flat_vars[ $key ] );
			} else {
				$flat_vars[ $prefix . $key ] = $var;
			}
		}

		return $flat_vars;
	}

	/**
	 * Sanitizes a variable
	 *
	 * @param  string  $name           option name
	 * @param  string  $value          option value from db
	 * @param  boolean $returnOriginal whether to return the db value if no good sanitization is found
	 * @return int|string|null         sanitized value
	 */
	private static function prepare( $name, $value, $returnOriginal = false ) {
		$good          = true;
		$name          = preg_replace( '/^vamtam_/', '', $name );
		$originalValue = $value;

		// duck typing values
		if ( preg_match( '/(^share|^has-|^show|-last$|-subsets$|-google$)/i', $name ) ) {
			$good = false;
		} elseif ( preg_match( '/(%|px|em)$/i', $value ) ) { // definitely a number, leave it as is

		} elseif ( is_numeric( $value ) ) { // most likely dimensions, must differentiate between percentages and pixels
			if ( in_array( $name, self::$percentages ) ) {
				$value .= '%';
			} elseif ( in_array( $name, self::$numbers ) || strpos( $name, 'line-height' ) !== false ) {
				// as it is
			} elseif ( preg_match( '/(size|width|height)$/', $name ) || preg_match( '/padding|margin/', $name ) ) { // treat as px
				$value .= 'px';
			}
		} elseif ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value ) || ( $value === '' && preg_match( '/-color$/', $name ) ) ) { // colors
			// as is
		} elseif ( preg_match( '/-font-family$/', $name ) && strpos( $value, ',' ) !== false ) {
			// as is
		} elseif ( preg_match( '/^http|^url/i', $value ) || ( preg_match( '/(family|weight)$/', $name ) && isset( $value[0] ) && ! in_array( $value[0], array( '"', "'" ) ) ) ) { // urls and other strings
			$value = "'" . str_replace( "'", '"', $value ) . "'";
		} elseif ( preg_match( '/^accent(?:-color-)?\d$/', $value ) ) { // accents
			$value = vamtam_sanitize_accent( $value );
		} else {
			if ( ! preg_match( '/\bfamily\b|\burl\b|\bcolor\b/i', $name ) ) {
				// check keywords
				$keywords   = explode( ' ', 'top right bottom left fixed static scroll cover contain auto repeat repeat-x repeat-y no-repeat center normal italic bold 100 200 300 400 500 600 700 800 900 transparent' );
				$sub_values = explode( ' ', $value );
				foreach ( $sub_values as $s ) {
					if ( ! in_array( $s, $keywords ) ) {
						$good = false;
						break;
					}
				}
			}
		}

		return $good ? $value : ( $returnOriginal ? $originalValue : null );
	}
}

