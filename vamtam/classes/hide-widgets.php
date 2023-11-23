<?php

class VamtamHideWidgets {
	private static $instance;
	private $hidden;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->hidden = array();

		add_filter( 'widget_update_callback', array( $this, 'widget_update' ), 10, 4 );
		add_filter( 'widget_display_callback', array( $this, 'widget_display' ), 10, 3 );
		add_action( 'in_widget_form', array( $this, 'widget_conditions_admin' ), 10, 3 );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	public function widget_update( $instance, $new_instance, $old_instance, $widget ) {
		$instance['vamtam_hide_low_res'] = isset( $new_instance['vamtam_hide_low_res'] );

		return $instance;
	}

	public function widget_display( $instance, $widget, $args ) {
		if ( isset( $instance['vamtam_hide_low_res'] ) && $instance['vamtam_hide_low_res'] ) {
			$this->hidden[] = $widget->id;
		}

		return $instance;
	}

	public function widget_conditions_admin( $widget, $return, $instance ) {
		$value = isset( $instance['vamtam_hide_low_res'] ) ? $instance['vamtam_hide_low_res'] : false;
	?>
		<p>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $widget->get_field_name( 'vamtam_hide_low_res' ) ); ?>" <?php checked( $value, true ) ?> />
				<?php esc_html_e( 'Hide on low resolutions', 'mozo' ); ?>
			</label>
		</p>
	<?php
	}

	public function wp_footer() {
		if ( ! empty( $this->hidden ) ) {
			wp_enqueue_script( 'vamtam-hide-widgets' );
			wp_localize_script( 'vamtam-hide-widgets', 'VAMTAM_HIDDEN_WIDGETS', $this->hidden );
		}
	}
}
