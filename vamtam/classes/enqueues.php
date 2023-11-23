<?php

/**
 * Enqueue styles and scripts used by the theme
 *
 * @package vamtam/mozo
 */

/**
 * class VamtamEnqueues
 */
class VamtamEnqueues {
	private static $use_min;

	private static $widget_styles = array(
		'WP_Nav_Menu_Widget'       => 'nav-menu',
		'WP_Widget_Tag_Cloud'      => 'tagcloud',
		'WP_Widget_RSS'            => 'rss',
		'WP_Widget_Search'         => 'search',
		'WC_Widget_Product_Search' => 'search',
		'WP_Widget_Calendar'       => 'calendar',
	);

	/**
	 * Hook the relevant actions
	 */
	public static function actions() {
		self::$use_min = ! ( WP_DEBUG || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'VAMTAM_SCRIPT_DEBUG' ) && VAMTAM_SCRIPT_DEBUG ) );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'styles' ), 999 );

		add_action( 'fl_builder_ui_enqueue_scripts', [ __CLASS__, 'fl_builder_ui_enqueue_scripts' ], 999 );

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

		add_action( 'wp', array( __CLASS__, 'preload_styles' ) );

		if ( ! is_admin() ) {
			add_action( 'the_widget', array( __CLASS__, 'widget_styles' ) );
			add_action( 'dynamic_sidebar', array( __CLASS__, 'widget_styles_dynamic_sidebar' ) );
		}

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_styles' ), 999 );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'customize_controls_enqueue_scripts' ) );
		add_action( 'customize_preview_init', array( __CLASS__, 'customize_preview_init' ) );
	}

	private static function is_our_admin_page() {
		if ( ! is_admin() ) return false;

		$screen = get_current_screen();

		return
			in_array( $screen->base, array( 'post', 'widgets', 'themes', 'upload' ) ) ||
			strpos( $screen->base, 'vamtam_' ) !== false ||
			strpos( $screen->base, 'toplevel_page_vamtam' ) === 0 ||
			strpos( $screen->base, 'toplevel_page_vamtam' ) === 0 ||
			$screen->base === 'media_page_vamtam_icons';
	}

	private static function inject_dependency( $handle, $dep ) {
		global $wp_scripts;

		$script = $wp_scripts->query( $handle, 'registered' );

		if ( ! $script )
			return false;

		if ( ! in_array( $dep, $script->deps ) ) {
			$script->deps[] = $dep;
		}

		return true;
	}

	/**
	 * Prints the <link> tag immediately after enqueueing the style
	 *
	 * @param  string $handle passed to wp_enqueue_style
	 */
	public static function enqueue_style_and_print( $handle ) {
		wp_enqueue_style( $handle );

		// print late styles, otherwise Beaver will skip over some of them
		if ( ! doing_filter( 'get_the_excerpt' ) ) {
			print_late_styles();
		}
	}

	/**
	 * Front-end scripts
	 */
	public static function scripts() {
		global $content_width;

		if ( is_admin() ) return;

		$cache_timestamp = get_option( 'vamtam-css-cache-timestamp' );

		if ( is_singular() && comments_open() ) {
			wp_enqueue_script( 'comment-reply', false, false, false, true );
		}

		wp_register_script( 'vamtam-ls-height-fix', VAMTAM_JS . 'layerslider-height.js', array( 'jquery' ), $cache_timestamp, true );

		wp_register_script( 'vamtam-splash-screen', VAMTAM_JS . 'splash-screen.js', array( 'jquery', 'imagesloaded' ), $cache_timestamp, true );

		$cube_path = VAMTAM_ASSETS_URI . 'cubeportfolio/';
		wp_register_script( 'cubeportfolio', $cube_path . 'js/jquery.cubeportfolio' . ( self::$use_min ? '.min' : '' ) . '.js', array( 'jquery' ), '4.4.0', true );

		wp_register_script( 'vamtam-hide-widgets', VAMTAM_JS . 'hide-widgets.js', array(), $cache_timestamp, true );

		$all_js_path = self::$use_min ? 'all.min.js' : 'all.js';
		$all_js_deps = array(
			'jquery',
		);

		if ( ! is_archive() && ! is_search() && class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_enabled() ) {
			$all_js_deps[] = 'fl-builder-layout-' . get_the_ID();
		}

		wp_enqueue_script( 'vamtam-all', VAMTAM_JS . $all_js_path, $all_js_deps, $cache_timestamp, true );

		self::inject_dependency( 'wc-cart-fragments', 'vamtam-all' );

		$script_vars = array(
			'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
			'jspath'                   => VAMTAM_JS,
			'mobile_header_breakpoint' => vamtam_get_mobile_header_breakpoint(),
			'cube_path'                => $cube_path,
			'beaver_responsive'        => class_exists( 'FLBuilderModel' ) ? (int) FLBuilderModel::get_global_settings()->medium_breakpoint : 768,
			'beaver_small'             => class_exists( 'FLBuilderModel' ) ? (int) FLBuilderModel::get_global_settings()->responsive_breakpoint : 768,
			'content_width'            => (int) $content_width,
		);

		wp_localize_script( 'vamtam-all', 'VAMTAM_FRONT', $script_vars );

		$sticky_header_js_path = self::$use_min ? 'sticky-header.min.js' : 'sticky-header.js';

		wp_register_script( 'vamtam-sticky-header', VAMTAM_JS . 'build/' . $sticky_header_js_path, array( 'vamtam-all' ), $cache_timestamp, true );

		if ( FLBuilderModel::is_builder_active() ) {
			// Fix for a conflict between underscore and bandsintown widget (embedded script).
			wp_add_inline_script( 'underscore', 'jQuery(window).load(function(){if(jQuery(document).find("script[src=\"https://widget.bandsintown.com/main.min.js\"]").length){window.vBandsInTown = _.noConflict();}});','after' );
		}
	}

	/**
	 * Admin scripts
	 */
	public static function admin_scripts() {
		if ( ! self::is_our_admin_page() ) return;

		$cache_timestamp = VamtamFramework::get_version();

		wp_enqueue_script( 'jquery-magnific-popup', VAMTAM_JS . 'plugins/thirdparty/jquery.magnific.js', array( 'jquery' ), $cache_timestamp, true );

		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'editor' );
		wp_enqueue_script( 'jquery-ui-tabs' );

		wp_enqueue_script( 'farbtastic' );

		wp_enqueue_media();

		wp_enqueue_script( 'vamtam-admin', VAMTAM_ADMIN_ASSETS_URI . 'js/admin-all.js', array( 'jquery', 'underscore', 'backbone' ), $cache_timestamp, true );

		wp_localize_script(
			'vamtam-admin', 'VAMTAM_ADMIN', array(
				'addNewIcon' => esc_html__( 'Add New Icon', 'mozo' ),
				'iconName'   => esc_html__( 'Icon', 'mozo' ),
				'iconText'   => esc_html__( 'Text', 'mozo' ),
				'iconLink'   => esc_html__( 'Link', 'mozo' ),
				'iconChange' => esc_html__( 'Change', 'mozo' ),
				'fonts'      => $GLOBALS['vamtam_fonts'],
			)
		);
	}

	/**
	 * Front-end styles
	 */
	public static function styles() {
		global $content_width;

		if ( is_admin() ) return;

		$cache_timestamp = get_option( 'vamtam-css-cache-timestamp' );

		$preview = is_customize_preview() || ( isset( $_POST['wp_customize'] ) && $_POST['wp_customize'] == 'on' && isset( $_POST['customized'] ) && ! empty( $_POST['customized'] ) && ! isset( $_POST['action'] ) ? '-preview' : '' );

		$fonts_url = empty( $preview ) ? vamtam_get_option( 'google_fonts' ) : vamtam_customizer_preview_fonts_url();

		wp_enqueue_style( 'vamtam-gfonts', $fonts_url . '&display=swap', array(), $cache_timestamp );

		wp_register_style( 'cubeportfolio', VAMTAM_ASSETS_URI . 'cubeportfolio/css/cubeportfolio' . ( self::$use_min ? '.min' : '' ) . '.css', array( 'vamtam-front-all' ), '4.4.0' );
		wp_register_style( 'vamtam-not-found', VAMTAM_ASSETS_URI . 'css/dist/not-found.css' , array( 'vamtam-front-all' ), $cache_timestamp );
		wp_register_style( 'vamtam-wc-cart-checkout', VAMTAM_ASSETS_URI . 'css/dist/woocommerce/cart-checkout.css' , array( 'vamtam-front-all' ), $cache_timestamp );

		$generated_deps = array();

		if ( vamtam_has_woocommerce() ) {
			$generated_deps[] = 'woocommerce-layout';
			$generated_deps[] = 'woocommerce-smallscreen';
			$generated_deps[] = 'woocommerce-general';

			if ( is_cart() || is_checkout() ) {
				wp_enqueue_style( 'vamtam-wc-cart-checkout' );
			}
		}

		wp_enqueue_style( 'vamtam-front-all', VAMTAM_ASSETS_URI . 'css/dist/all.css', $generated_deps, $cache_timestamp );

		// this is added so that we don't break customers' child themes after upgrading the parent
		if ( is_child_theme() ) {
			wp_register_style( 'front-all', null, array( 'vamtam-front-all' ) );
		}

		wp_add_inline_style( 'vamtam-front-all', self::get_theme_icons_css() );

		// content paddings
		ob_start();

		$small_breakpoint  = class_exists( 'FLBuilderModel' ) ? (int) FLBuilderModel::get_global_settings()->responsive_breakpoint : 768;
		$medium_breakpoint = class_exists( 'FLBuilderModel' ) ? (int) FLBuilderModel::get_global_settings()->medium_breakpoint : 992;

		include VAMTAM_CSS_DIR . 'beaver.php';

		include VAMTAM_CSS_DIR . 'header-slider.php';

		wp_add_inline_style( 'vamtam-front-all', ob_get_clean() );

		$responsive_stylesheets = array(
			'mobile-header'    => '(max-width: ' . vamtam_get_mobile_header_breakpoint() . ')',
			'layout-max-low'   => '(min-width: ' . ( $medium_breakpoint + 1 ) . "px) and (max-width: {$content_width}px)",
			'layout-max'       => '(min-width: ' . ( $medium_breakpoint + 1 ) . 'px)',
			'layout-below-max' => "(max-width: {$medium_breakpoint}px)",
			'layout-small'     => "(max-width: {$small_breakpoint}px)",
		);

		$url_prefix = VAMTAM_ASSETS_URI . 'css/dist/responsive/';
		foreach ( $responsive_stylesheets as $file => $media ) {
			wp_enqueue_style( 'vamtam-theme-'. $file, $url_prefix . $file . '.css', array( 'vamtam-front-all' ), $cache_timestamp, $media );
		}

		if ( vamtam_has_woocommerce() ) {
			$wc_small_screen_media = 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')';
			wp_enqueue_style( 'vamtam-theme-wc-small-screen', $url_prefix . 'wc-small-screen.css', array( 'vamtam-front-all' ), $cache_timestamp, $wc_small_screen_media );
		}

		wp_register_style( 'vamtam-widgets-general', VAMTAM_ASSETS_URI . 'css/dist/widgets/general.css' , array( 'vamtam-front-all' ), $cache_timestamp );

		foreach ( array_unique( self::$widget_styles ) as $class => $file ) {
			wp_register_style( 'vamtam-widget-' . $file, VAMTAM_ASSETS_URI . 'css/dist/widgets/' . $file . '.css' , array( 'vamtam-front-all', 'vamtam-widgets-general' ), $cache_timestamp );
		}

		self::print_theme_options();
	}

	public static function fl_builder_ui_enqueue_scripts() {
		$cache_timestamp = get_option( 'vamtam-css-cache-timestamp' );

		wp_enqueue_style( 'vamtam-icomoon', VAMTAM_ASSETS_URI . 'fonts/icons/style.css', [ 'vamtam-front-all' ], $cache_timestamp );
		wp_enqueue_style( 'vamtam-theme-icons', VAMTAM_ASSETS_URI . 'fonts/theme-icons/style.css', [ 'vamtam-front-all' ], $cache_timestamp );
	}

	/**
	 * Gutenberg styles
	 */
	public static function admin_init() {
		add_editor_style( 'vamtam/assets/css/dist/editor.css' );
	}

	/**
	 * Output Link rel=preload headers for critical styles
	 */
	public static function preload_styles() {
		add_editor_style( 'vamtam/assets/css/dist/editor.css' );
		$cache_timestamp = get_option( 'vamtam-css-cache-timestamp' );
		$url_prefix      = VAMTAM_ASSETS_URI . 'css/dist/responsive/';

		if ( wp_is_mobile() ) {
			header( "Link: <{$url_prefix}layout-below-max.css?ver={$cache_timestamp}>; rel=preload; as=style", false );
			header( "Link: <{$url_prefix}mobile-header.css?ver={$cache_timestamp}>; rel=preload; as=style", false );
		} else {
			header( "Link: <{$url_prefix}layout-max.css?ver={$cache_timestamp}>; rel=preload; as=style", false );
		}
	}

	/**
	 * Enqueue widget styles, hooked to the_widget
	 */
	public static function widget_styles( $widget ) {
		// this one is for all widgets, anywhere
		wp_enqueue_style( 'vamtam-widgets-general' );

		// some widgets have their own style sheets
		if ( isset( self::$widget_styles[ $widget ] ) ) {
			wp_enqueue_style( 'vamtam-widget-' . self::$widget_styles[ $widget ] );
		}

		// avoids FOUT
		if ( ! doing_filter( 'get_the_excerpt' ) ) {
			print_late_styles();
		}
	}

	/**
	 * Enqueue widget styles, hooked to dynamic_sidebar
	 */
	public static function widget_styles_dynamic_sidebar( $widget ) {
		self::widget_styles( get_class( $widget['callback'][0] ) );
	}

	/**
	 * Admin styles
	 */
	public static function admin_styles() {
		wp_enqueue_style( 'vamtam-admin-all', VAMTAM_ADMIN_ASSETS_URI . 'css/vamtam-admin-all.css' );

		if ( ! self::is_our_admin_page() ) return;

		$cache_timestamp = VamtamFramework::get_version();

		wp_enqueue_style( 'magnific', VAMTAM_ADMIN_ASSETS_URI . 'css/magnific.css' );
		wp_enqueue_style( 'vamtam-admin', VAMTAM_ADMIN_ASSETS_URI . 'css/vamtam-admin.css' );
		wp_enqueue_style( 'farbtastic' );

		wp_enqueue_style( 'vamtam-gfonts', vamtam_get_option( 'google_fonts' ), array(), $cache_timestamp );

		wp_add_inline_style( 'vamtam-admin', self::get_theme_icons_css() );

		self::print_theme_options();
	}

	/**
	 * Customizer styles
	 */
	public static function customize_controls_enqueue_scripts() {
		$cache_timestamp = VamtamFramework::get_version();

		wp_enqueue_style( 'vamtam-customizer', VAMTAM_ADMIN_ASSETS_URI . 'css/customizer.css', array(), $cache_timestamp );

		wp_enqueue_script( 'vamtam-customize-controls-conditionals', VAMTAM_ADMIN_ASSETS_URI . 'js/customize-controls-conditionals.js', array( 'jquery', 'customize-controls' ), $cache_timestamp, true );
	}

	public static function customize_preview_init() {
		$cache_timestamp = VamtamFramework::get_version();

		wp_enqueue_script( 'vamtam-customizer-preview', VAMTAM_ADMIN_ASSETS_URI . 'js/customizer-preview.js', array( 'jquery', 'customize-preview' ), $cache_timestamp, true );

		wp_localize_script(
			'vamtam-customizer-preview', 'VAMTAM_CUSTOMIZE_PREVIEW', array(
				'compiler_options' => vamtam_custom_css_options(),
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'percentages'      => VamtamLessBridge::$percentages,
				'numbers'          => VamtamLessBridge::$numbers,
			)
		);
	}

	/**
	 * Generates the @font-face blocks for the icons fonts
	 *
	 * @return string
	 */
	public static function get_theme_icons_css() {
		$theme_url       = VAMTAM_THEME_URI;
		$theme_icons_css = "
			@font-face {
				font-family: 'icomoon';
				src: url({$theme_url}vamtam/assets/fonts/icons/icomoon.woff2) format('woff2'),
				     url( {$theme_url}vamtam/assets/fonts/icons/icomoon.woff) format('woff'),
				     url({$theme_url}vamtam/assets/fonts/icons/icomoon.ttf) format('ttf');
				font-weight: normal;
				font-style: normal;
				font-display: swap;
			}
		";

		if ( current_theme_supports( 'vamtam-split-icons' ) ) {
			$theme_icon_ranges = include VAMTAM_ASSETS_DIR . 'fonts/theme-icons/split/ranges.php';

			foreach ( $theme_icon_ranges as $name => $ranges ) {
				$theme_icons_css .= "
					@font-face {
						font-family: 'theme';
						src: url({$theme_url}vamtam/assets/fonts/theme-icons/split/{$name}.woff2) format('woff2'),
							url({$theme_url}vamtam/assets/fonts/theme-icons/split/{$name}.woff) format('woff');
						font-weight: normal;
						font-style: normal;
						font-display: swap;
						unicode-range: {$ranges},U+20;
					}
				";
			}
		} else {
			$theme_icons_css .= "
				@font-face {
					font-family: 'theme';
					src: url({$theme_url}vamtam/assets/fonts/theme-icons/theme-icons.woff2) format('woff2'),
						url({$theme_url}vamtam/assets/fonts/theme-icons/theme-icons.woff) format('woff');
					font-weight: normal;
					font-style: normal;
					font-display: swap;
				}
			";
		}

		return $theme_icons_css;
	}

	/**
	 * Check if the auto contarast on for accent color
	 * if so it will add the default accent hc colors
	 *
	 * @param [type] $options
	 * @return array
	 */
	public static function set_default_accent_hc_colors_for_autocontrast( $options ) {
		if( isset( $options['accent-color']['auto-contrast'] ) ) {
			if( $options['accent-color']['auto-contrast'] == '1' ) {
				$accent_colors   = $options['accent-color'];

				for( $i=1; $i <= 8; $i++ ) {
					$accent_color = $accent_colors[$i];
					$color                 = new VamtamColor( $accent_color );
					$hc_color              = '';
					if ( $color->luminance > 0.4 ) {
						$hc_color = '#000000';
					} else {
						$hc_color = '#ffffff';
					}
					$accent_colors[$i .'-hc'] = $hc_color;
				}
				$options['accent-color'] = $accent_colors;
			}
		 }

		 return $options;
	}

	public static function print_theme_options() {
		$vars_raw    = $GLOBALS['vamtam_theme_customizer']->get_options();
		$vars_raw    = self::set_default_accent_hc_colors_for_autocontrast( $vars_raw );
		$option_defs = $GLOBALS['vamtam_theme_customizer']->get_fields_by_id();

		$options_to_export = array();

		foreach ( $option_defs as $option ) {
			if ( isset( $option['compiler'] ) && $option['compiler'] ) {
				$options_to_export[ $option['id'] ] = apply_filters( 'vamtam_get_option', $vars_raw[ $option['id'] ], $option['id'] );
			}
		}

		$options = VamtamLessBridge::prepare_vars_for_export( $options_to_export );

		if( isset( $options['accent-color-auto-contrast'] ) ) {
			unset( $options['accent-color-auto-contrast'] );
		}

		$options['left-sidebar-width'] = ( (int)$options['left-sidebar-width'] + 5 ) . '%';
		$options['right-sidebar-width'] = ( (int)$options['right-sidebar-width'] + 5 ) . '%';

		echo '<style id="vamtam-theme-options">';
		echo ':root {';

		foreach ( $options as $name => $value ) {
			echo '--vamtam-' . esc_html( $name ) . ':' . wp_kses_data( $value ) . ";\n";
		}

		echo "--vamtam-loading-animation:url('" . esc_attr( VAMTAM_IMAGES . 'loader-ring.gif') . "');\n";

		for ( $i = 1; $i <= 8; $i++ ) {
			$name  = "accent-color-$i";
			$color = new VamtamColor( $options[ $name ] );

			echo '--vamtam-' . esc_html( $name ) . '-rgb:' . wp_kses_data( implode( ',', $color->getRgb() ) ) . ";\n";
		}

		echo '}';
		echo '</style>';
	}
}

