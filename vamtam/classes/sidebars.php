<?php
/**
 * Sidebar helpers
 *
 * @package vamtam/mozo
 */
/**
 * class VamtamSidebars
 *
 * register right/left, header and footer sidebars
 * also provides a function which outputs the correct right/left sidebar
 */
class VamtamSidebars {

	/**
	 * List of widget areas
	 * @var array
	 */
	private $sidebars = array();

	/**
	 * List of sidebar placements
	 * @var array
	 */
	private $places = array();

	/**
	 * Singleton instance
	 * @var VamtamSidebars
	 */
	private static $instance;

	/**
	 * Set the available widgets area
	 */
	public function __construct() {
		$this->sidebars = array(
			'page' => esc_html__( 'Main Widget Area', 'mozo' ),
		);

		if ( vamtam_has_woocommerce() )
			$this->sidebars['vamtam-woocommerce'] = esc_html__( 'WooCommerce Widget Area', 'mozo' );

		$this->places = array( 'left', 'right' );
	}

	/**
	 * Get singleton instance
	 * @return VamtamSidebars singleton instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Register sidebars
	 */
	public function register_sidebars() {
		unregister_sidebar( 'sidebar-event' );

		foreach ( $this->sidebars as $id => $name ) {
			foreach ( $this->places as $place ) {
				register_sidebar( array(
					'id'            => $id . '-' . $place,
					'name'          => $name . " ( $place )",
					'description'   => $name . " ( $place )",
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
				) );
			}
		}

		register_sidebar( array(
			'id'            => 'overlay-menu-sidebar',
			'name'          => esc_html__( 'Overlay Menu Widget Area', 'mozo' ),
			'description'   => esc_html__( 'Displayed below the menu items in the overlay menu.', 'mozo' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
		) );
	}

	private function get_sidebar_name( $place = 'left' ) {
		global $post;

		if ( vamtam_has_woocommerce() && is_woocommerce() ) {
			$sidebar = 'vamtam-woocommerce';
		}

		if ( isset( $sidebar ) ) {
			return $sidebar . '-' . $place;
		}

		return 'page-' . $place;
	}

	/**
	 * Output the correct sidebar
	 *
	 * @uses dynamic_sidebar()
	 *
	 * @param  string $place one of $this->placements
	 * @return bool          result of dynamic_sidebar()
	 */
	public function get_sidebar( $place = 'left' ) {
		$name = $this->get_sidebar_name( $place );

		dynamic_sidebar( $name );
	}

	/**
	 * Check if we should show a sidebar
	 *
	 * @uses is_active_sidebar()
	 *
	 * @param  string $place one of $this->placements
	 * @return bool          result of dynamic_sidebar()
	 */
	public function has_sidebar( $place = 'left' ) {
		if ( vamtam_has_woocommerce() && ( is_cart() || is_checkout() ) ) {
			return false;
		}

		$name = $this->get_sidebar_name( $place );

		return is_active_sidebar( $name );
	}
};

