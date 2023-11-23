<?php

class VamtamElementorBridge {

	/** Refers to a single instance of this class. */
	private static $instance = null;

	/**
	 * Returns an instance of this class.
	 *
	 * @return  VamtamElementorBridge A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Constructor function
	 */
	private function __construct() {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'hide_elementors_content_width_settings_option' ] );
		add_action( 'init', [ __CLASS__, 'set_elementor_site_max_width_option_value_from_customizer' ] );

		add_action( 'elementor/theme/register_locations', [ __CLASS__, 'register_locations' ] );
	}

	/**
	 * Hide elementors content width settings option by adding some CSS
	 *
	 * @param [type] $hook
	 * @return void
	 */
	public static function hide_elementors_content_width_settings_option( $hook ) {
		if( $hook != 'toplevel_page_elementor') {
			return;
		}

		$css = 'tr.elementor_container_width { display: none; }';
		$css = wp_strip_all_tags( $css );
		wp_add_inline_style( 'forms', $css );
	}

	/**
	 * Set elementor_site_max_width option value from customizer
	 * option Global Layout:->Maximum Page Width
	 *
	 * @return void
	 */
	public static function set_elementor_site_max_width_option_value_from_customizer() {
		global $vamtam_theme;

		$site_max_width = $vamtam_theme['site-max-width'];
		$elementor_container_width = get_option( 'elementor_container_width', null );

		if( $site_max_width != $elementor_container_width ) {
			update_option( 'elementor_container_width', $site_max_width );
		}
	}


	/**
	 * Register theme locations
	 */
	public static function register_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_all_core_location();

		$elementor_theme_manager->register_location(
			'top-bar',
			[
				'label' => esc_html__( 'Top Bar', 'mozo' ),
				'multiple' => true,
				'edit_in_content' => true,
			]
		);

		$elementor_theme_manager->register_location(
			'header-featured-area',
			[
				'label' => esc_html__( 'Header Featured Area', 'mozo' ),
				'multiple' => true,
				'edit_in_content' => true,
			]
		);

		$elementor_theme_manager->register_location(
			'page-title-location',
			[
				'label' => esc_html__( 'Page Title', 'mozo' ),
				'multiple' => true,
				'edit_in_content' => true,
			]
		);
	}
}
