<?php

/**
 * Vamtam Theme Framework base class
 *
 * @author Nikolay Yordanov <me@nyordanov.com>
 * @package vamtam/mozo
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * This is the first loaded framework file
 *
 * VamtamFramework does the following ( in this order ):
 *  - sets constants for the frequently used paths
 *  - loads translations
 *  - loads the plugins bundled with the theme
 *  - loads some functions and helpers used in various places
 *  - sets the custom post types
 *  - if this is wp-admin, load admin files
 *
 * This class also loads the custom widgets and sets what the theme supports ( + custom menus )
 */

class VamtamFramework {

	/**
	 * Cache the result of some operations in memory
	 *
	 * @var array
	 */
	private static $cache = array();

	/**
	 * Post types with double sidebars
	 */
	public static $complex_layout = array( 'page', 'post', 'jetpack-portfolio', 'product' );

	/**
	 * Initialize the Vamtam framework
	 * @param array $options framework options
	 */
	public function __construct( $options ) {
		// Autoload classes on demand
		if ( function_exists( '__autoload' ) )
			spl_autoload_register( '__autoload' );
		spl_autoload_register( array( $this, 'autoload' ) );

		self::$complex_layout = apply_filters( 'vamtam_complex_layout', self::$complex_layout );

		$this->set_constants( $options );
		$this->load_languages();
		$this->load_functions();
		$this->load_admin();

		require_once VAMTAM_DIR . 'classes/class-tgm-plugin-activation.php';
		require_once VAMTAM_SAMPLES_DIR . 'dependencies.php';

		add_action( 'after_setup_theme', array( __CLASS__, 'theme_supports' ) );
		add_action( 'init', array( __CLASS__, 'late_init' ), 100 );
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		add_filter( 'vamtam_purchase_code', array( __CLASS__, 'get_purchase_code' ) );
		add_filter( 'wpv_purchase_code', array( __CLASS__, 'get_purchase_code' ) );

		VamtamLoadMore::get_instance();
		VamtamHideWidgets::get_instance();

		VamtamSitemap::setup();
		VamtamMaintenanceMode::setup();
		VamtamElementorBridge::get_instance();
	}

	/**
	 * Autoload classes when needed
	 *
	 * @param  string $class class name
	 */
	public function autoload( $class ) {
		$class = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', str_replace( '_', '', $class ) ) );

		if ( strpos( $class, 'vamtam-' ) === 0 ) {
			$path = trailingslashit( get_template_directory() ) . 'vamtam/classes/';
			$file = str_replace( 'vamtam-', '', $class ) . '.php';

			if ( is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}

			if ( is_admin() ) {
				$admin_path = VAMTAM_ADMIN_DIR . 'classes/';

				if ( is_readable( $admin_path . $file ) ) {
					include_once( $admin_path . $file );
					return;
				}
			}
		}

	}

	/**
	 * Sets self::$cache[ $key ] = $value
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public static function set( $key, $value ) {
		self::$cache[ $key ] = $value;
	}

	/**
	 * Returns self::$cache[ $key ]
	 *
	 * @param  mixed $key
	 * @return mixed        value
	 */
	public static function get( $key, $default = false ) {
		return isset( self::$cache[ $key ] ) ? self::$cache[ $key ] : $default;
	}

	/**
	 * Get the theme version
	 *
	 * @return string theme version as defined in style.css
	 */
	public static function get_version() {
		if ( isset( self::$cache['version'] ) )
			return self::$cache['version'];

		$the_theme = wp_get_theme();
		if ( $the_theme->parent() ) {
			$the_theme = $the_theme->parent();
		}

		self::$cache['version'] = $the_theme->get( 'Version' );

		return self::$cache['version'];
	}

	/**
	 * Defines constants used by the theme
	 *
	 * @param array $options framework options
	 */
	private function set_constants( $options ) {
		define( 'VAMTAM_THEME_NAME', $options['name'] );
		define( 'VAMTAM_THEME_SLUG', $options['slug'] );
		define( 'VAMTAM_THUMBNAIL_PREFIX', 'vamtam-' );

		// theme dir and uri
		define( 'VAMTAM_THEME_DIR', get_template_directory() . '/' );
		define( 'VAMTAM_THEME_URI', get_template_directory_uri() . '/' );

		// framework dir and uri
		define( 'VAMTAM_DIR', VAMTAM_THEME_DIR . 'vamtam/' );
		define( 'VAMTAM_URI', VAMTAM_THEME_URI . 'vamtam/' );

		// common assets dir and uri
		define( 'VAMTAM_ASSETS_DIR', VAMTAM_DIR . 'assets/' );
		define( 'VAMTAM_ASSETS_URI', VAMTAM_URI . 'assets/' );

		// common file paths
		define( 'VAMTAM_FONTS_URI',  VAMTAM_ASSETS_URI . 'fonts/' );
		define( 'VAMTAM_HELPERS',    VAMTAM_DIR . 'helpers/' );
		define( 'VAMTAM_JS',         VAMTAM_ASSETS_URI . 'js/' );
		define( 'VAMTAM_OPTIONS',    VAMTAM_DIR . 'options/' );
		define( 'VAMTAM_PLUGINS',    VAMTAM_DIR . 'plugins/' );
		define( 'VAMTAM_CSS',        VAMTAM_ASSETS_URI . 'css/' );
		define( 'VAMTAM_CSS_DIR',    VAMTAM_ASSETS_DIR . 'css/' );
		define( 'VAMTAM_IMAGES',     VAMTAM_ASSETS_URI . 'images/' );
		define( 'VAMTAM_IMAGES_DIR', VAMTAM_ASSETS_DIR . 'images/' );

		// sample content
		define( 'VAMTAM_SAMPLES_DIR',   VAMTAM_THEME_DIR . 'samples/' );
		define( 'VAMTAM_SAMPLES_URI',   VAMTAM_THEME_URI . 'samples/' );

		// cache
		define( 'VAMTAM_CACHE_DIR', VAMTAM_THEME_DIR . 'cache/' );
		define( 'VAMTAM_CACHE_URI', VAMTAM_THEME_URI . 'cache/' );

		// admin
		define( 'VAMTAM_ADMIN_DIR', VAMTAM_DIR . 'admin/' );
		define( 'VAMTAM_ADMIN_URI', VAMTAM_URI . 'admin/' );

		define( 'VAMTAM_ADMIN_AJAX',       VAMTAM_ADMIN_URI . 'ajax/' );
		define( 'VAMTAM_ADMIN_AJAX_DIR',   VAMTAM_ADMIN_DIR . 'ajax/' );
		define( 'VAMTAM_ADMIN_ASSETS_URI', VAMTAM_ADMIN_URI . 'assets/' );
		define( 'VAMTAM_ADMIN_HELPERS',    VAMTAM_ADMIN_DIR . 'helpers/' );
		define( 'VAMTAM_ADMIN_METABOXES',  VAMTAM_ADMIN_DIR . 'metaboxes/' );
		define( 'VAMTAM_ADMIN_TEMPLATES',  VAMTAM_ADMIN_DIR . 'templates/' );
	}

	/**
	 * 'init' action, but with a higher (later) priority
	 */
	public static function late_init() {
		if ( class_exists( 'Jetpack_Portfolio' ) ) {
			$GLOBALS['_wp_additional_image_sizes']['jetpack-portfolio-admin-thumb'] = array(
				'width' => 100,
				'height' => 100,
				'crop' => true,
			);
		}

		/**
		 * Remove Publicize support from all CPT
		 * @see https://github.com/Automattic/jetpack/issues/10727
		 */
		$post_types = get_post_types_by_support( 'publicize' );

		foreach ( $post_types as $post_type ) {
			remove_post_type_support( $post_type, 'publicize' );
		}
	}

	/**
	 * Register theme support for various features
	 */
	public static function theme_supports() {
		global $content_width;

		self::set( 'is_responsive', apply_filters( 'vamtam-theme-responsive-mode', true ) );

		/**
		 * the max content width the css is built for should equal the actual content width,
		 * for example, the width of the text of a page without sidebars
		 */
		if ( ! isset( $content_width ) ) $content_width = vamtam_get_option( 'site-max-width' );

		if ( is_customize_preview() ) {
			$content_width = 1400;
		}

		$post_formats = apply_filters( 'vamtam_post_formats', array( 'aside', 'link', 'image', 'video', 'audio', 'quote', 'gallery' ) );
		self::set( 'post_formats', $post_formats );

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'post-formats', $post_formats );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );

		add_theme_support( 'vamtam-ajax-siblings' );
		add_theme_support( 'vamtam-page-title-style' );
		add_theme_support( 'vamtam-tribe-events' );
		add_theme_support( 'vamtam-scroll-pinning' );

		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support( 'fl-theme-builder-headers' );
		add_theme_support( 'fl-theme-builder-footers' );

		add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => $content_width / get_option( 'woocommerce_catalog_columns', 4 ),
			'single_image_width'    => $content_width / 2,
		) );

		if ( class_exists( 'Jetpack_Portfolio' ) ) {
			add_post_type_support( Jetpack_Portfolio::CUSTOM_POST_TYPE, 'excerpt' );
		}

		if ( function_exists( 'register_nav_menus' ) ) {
			register_nav_menus(
				array(
					'menu-header'     => esc_html__( 'Menu Header', 'mozo' ),
					'menu-top'        => esc_html__( 'Menu Top', 'mozo' ),
					'overlay-menu'    => esc_html__( 'Overlay Menu', 'mozo' ),
					'additional-menu' => esc_html__( 'Additional Menu (use in widgets)', 'mozo' ),
				)
			);
		}

		$size_info  = array();

		$wth = wp_parse_args( get_option( 'vamtam_featured_images_ratio', array() ), array(
			VAMTAM_THUMBNAIL_PREFIX . 'loop'   => 1.3,
			VAMTAM_THUMBNAIL_PREFIX . 'single' => 1.3,
		) );

		foreach ( $wth as $name => $ratio ) {
			$size_info[ $name ] = (object) array(
				'wth' => abs( floatval( $wth[ $name ] ) ),
				'crop' => true,
			);
		}

		$width = $content_width;

		$single_sizes     = array( VAMTAM_THUMBNAIL_PREFIX . 'single' );
		$columnated_sizes = array( VAMTAM_THUMBNAIL_PREFIX . 'loop' );

		foreach ( $single_sizes as $name ) {
			$height = $size_info[ $name ]->wth ? $width / $size_info[ $name ]->wth : false;
			add_image_size( $name, $width, $height, $size_info[ $name ]->crop );
		}

		for ( $num_columns = 1; $num_columns <= 4; $num_columns++ ) {
			$col_width = $width / $num_columns;

			add_image_size( VAMTAM_THUMBNAIL_PREFIX . 'normal-' . $num_columns, $col_width, 0 ); // special case where we always use the original proportions

			if ( $num_columns > 1 ) {
				add_image_size( VAMTAM_THUMBNAIL_PREFIX . 'normal-featured-' . $num_columns, $col_width * 2, 0 ); // same, but double width
			}

			foreach ( $columnated_sizes as $name ) {
				$height = $size_info[ $name ]->wth ? $col_width / $size_info[ $name ]->wth : false;

				add_image_size( $name . '-' . $num_columns, $col_width, $height, $size_info[ $name ]->crop );

				if ( $num_columns > 1 ) {
					add_image_size( $name . '-featured-' . $num_columns, $col_width * 2, $height * 2, $size_info[ $name ]->crop );
				}
			}
		}
	}

	/**
	 * Load interface translations
	 */
	private function load_languages() {
		load_theme_textdomain( 'mozo', VAMTAM_THEME_DIR . 'languages' );
	}

	/**
	 * Loads the main php files used by the framework
	 */
	private function load_functions() {
		global $vamtam_defaults, $vamtam_fonts;
		$vamtam_defaults = include VAMTAM_SAMPLES_DIR . 'default-options.php';
		$vamtam_fonts    = include VAMTAM_HELPERS . 'fonts.php';

		require_once VAMTAM_HELPERS . 'init.php';

		$custom_fonts = get_option( 'vamtam_custom_font_families', '' );
		if ( ! empty( $custom_fonts ) ) {
			$custom_fonts = explode( "\n", $custom_fonts );

			$vamtam_fonts['-- Custom fonts --'] = array(
				'family' => '',
			);

			foreach ( $custom_fonts as $font ) {
				$font = preg_replace( '/["\']+/', '', trim( $font ) );

				$vamtam_fonts[ $font ] = array(
					'family' => '"' . $font . '"',
					'weights' => array( '300', '300 italic', 'normal', 'italic', '600', '600 italic', 'bold', 'bold italic', '800', '800 italic' ),
				);
			}
		}

		require_once VAMTAM_HELPERS . 'woocommerce-integration.php';
		require_once VAMTAM_HELPERS . 'the-events-calendar-integration.php';
		require_once VAMTAM_HELPERS . 'megamenu-integration.php';

		require_once VAMTAM_HELPERS . 'icons.php';

		require_once VAMTAM_HELPERS . 'base.php';
		require_once VAMTAM_HELPERS . 'template.php';
		require_once VAMTAM_HELPERS . 'css.php';

		// frontend wrappers
		require_once VAMTAM_HELPERS . 'frontend-wrappers.php';

		VamtamOverrides::filters();
		VamtamEnqueues::actions();

		if ( file_exists( VAMTAM_HELPERS . 'migrations.php' ) ) {
			require_once VAMTAM_HELPERS . 'migrations.php';
		}
	}

	/**
	 * Register sidebars
	 */
	public static function widgets_init() {
		$vamtam_sidebars = VamtamSidebars::get_instance();

		$vamtam_sidebars->register_sidebars();
	}

	/**
	 * Loads the theme administration code
	 */
	private function load_admin() {
		if ( ! is_admin() ) return;

		VamtamAdmin::actions();
	}

	/**
	 * Return the option_name used for the purchase code option
	 * Backwards-compatible with the old option_name used before July 2018
	 */
	public static function get_purchase_code_option_key() {
		return defined( 'VAMTAM_ENVATO_THEME_ID' ) ? 'envato_purchase_code_' . VAMTAM_ENVATO_THEME_ID : 'vamtam-envato-license-key';
	}

	/**
	 * Return the purchase code, if set
	 * Also, automatically migrate the old option to use the new option_name
	 */
	public static function get_purchase_code() {
		$purchase_code_option_key = self::get_purchase_code_option_key();

		// if the old purchase code option is present and a THEME ID is set - migrate to the new purchase code option key
		if ( defined( 'VAMTAM_ENVATO_THEME_ID' ) && get_option( 'vamtam-envato-license-key', false ) !== false ) {
			update_option( $purchase_code_option_key, get_option( 'vamtam-envato-license-key' ) );
			delete_option( 'vamtam-envato-license-key' );
		}

		return get_option( $purchase_code_option_key );
	}

	public static function license( $state = '' ) {
		if ( ! empty( $state ) ) {
			// Set.
			update_option( '_vamtam_license', $state );
		} else {
			// Get.
			return get_option( '_vamtam_license' );
		}
	}
}

