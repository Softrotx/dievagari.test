<?php

/**
 * Framework admin enhancements
 *
 * @author Nikolay Yordanov <me@nyordanov.com>
 * @package vamtam/mozo
 */

/**
 * class VamtamAdmin
 */
class VamtamAdmin {
	/**
	 * Initialize the theme admin
	 */
	public static function actions() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			add_action( 'admin_init', array( 'VamtamUpdateNotice', 'check' ) );
		}

		add_action( 'admin_footer', array( __CLASS__, 'icons_selector' ) );

		add_filter( 'admin_notices', array( __CLASS__, 'update_warning' ) );

		add_action( 'admin_init', array( __CLASS__, 'setup_settings' ) );

		add_action( 'admin_init', [ __CLASS__, 'admin_init' ] );

		self::load_functions();

		new VamtamPurchaseHelper;
		new VamtamHelpPage;

		require_once VAMTAM_ADMIN_HELPERS . 'updates/version-checker.php';

		if ( ! get_option( VAMTAM_THEME_SLUG . '_vamtam_theme_activated', false ) ) {
			update_option( VAMTAM_THEME_SLUG . '_vamtam_theme_activated', true );
			delete_option( 'default_comment_status' );
		}
	}

	public static function admin_init() {
		if (
			class_exists( 'FortAwesome\FontAwesome_Release_Provider' ) &&
			'not-exists' === get_option( FortAwesome\FontAwesome_Release_Provider::OPTIONS_KEY, 'not-exists' )
		) {
			FortAwesome\FontAwesome_Release_Provider::load_releases();
		}
	}

	public static function setup_settings() {
		add_settings_field( 'vamtam_custom_font_families', esc_html__( 'Custom Font Families', 'mozo' ), array( __CLASS__, 'custom_font_families_settings_field' ), 'general' );

		register_setting( 'general', 'vamtam_custom_font_families' );

		add_settings_field( 'vamtam_featured_images_ratio', esc_html__( 'Featured images width-to-height ratio', 'mozo' ), array( __CLASS__, 'featured_images_settings_field' ), 'media' );

		register_setting( 'media', 'vamtam_featured_images_ratio' );
	}

	public static function custom_font_families_settings_field() {
		$value = get_option( 'vamtam_custom_font_families' );

?>
		<textarea name="vamtam_custom_font_families" class="large-text" aria-describedby="vamtam_custom_font_families-description" rows="5"><?php echo esc_textarea( $value ) ?></textarea>
		<p class="description" id="vamtam_custom_font_families-description"><?php esc_html_e( 'If you need any custom font families in the VamTam typography options, list one font family per line here. Quotes are not necessary.', 'mozo' ) ?></p>
<?php
	}

	public static function featured_images_settings_field() {
		$values = wp_parse_args( get_option( 'vamtam_featured_images_ratio', array() ), array(
			VAMTAM_THUMBNAIL_PREFIX . 'loop'   => 1.3,
			VAMTAM_THUMBNAIL_PREFIX . 'single' => 1.3,
		) );
?>
		<fieldset><legend class="screen-reader-text"><span>Large size</span></legend>
		<label for="vamtam_featured_images_ratio[<?php echo esc_attr( VAMTAM_THUMBNAIL_PREFIX ) ?>loop]"><?php esc_html_e( 'Listing', 'mozo' ) ?></label>
		<input name="vamtam_featured_images_ratio[<?php echo esc_attr( VAMTAM_THUMBNAIL_PREFIX ) ?>loop]" step="0.05" min="0" id="vamtam_featured_images_ratio[<?php echo esc_attr( VAMTAM_THUMBNAIL_PREFIX ) ?>loop]" value="<?php echo esc_attr( $values[ VAMTAM_THUMBNAIL_PREFIX . 'loop' ] ) ?>" class="small-text" type="number">
		<label for="vamtam_featured_images_ratio[<?php echo esc_attr( VAMTAM_THUMBNAIL_PREFIX ) ?>single]"><?php esc_html_e( 'Single', 'mozo' ) ?></label>
		<input name="vamtam_featured_images_ratio[<?php echo esc_attr( VAMTAM_THUMBNAIL_PREFIX ) ?>single]" step="0.05" min="0" id="vamtam_featured_images_ratio[<?php echo esc_attr( VAMTAM_THUMBNAIL_PREFIX ) ?>single]" value="<?php echo esc_attr( $values[ VAMTAM_THUMBNAIL_PREFIX . 'single' ] ) ?>" class="small-text" type="number">
		<p class="description"><?php esc_html_e( 'Set this to 0 if you prefer not to crop the images.', 'mozo' ) ?></p>
		<p class="description"><?php echo wp_kses( sprintf( __( 'If you have changed any of these options, please use the <a href="%s" title="Regenerate thumbnails" target="_blank">Regenerate thumbnails</a> plugin in order to update your images.', 'mozo' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ), 'vamtam-a-span' ) ?>
		</fieldset>
<?php
	}

	public static function update_warning() {
		if ( did_action( 'load-update-core.php' ) ) {
			echo '<div class="updated notice fade is-dismissible"><p><strong>';
;
			esc_html_e( 'Hey, just a polite reminder that if you update WordPress you will also need to update your theme and plugins.', 'mozo' );
			echo '</strong>';
			echo '</p></div>';
		}

		if ( did_action( 'load-update-core.php' ) || did_action( 'load-themes.php' ) ) {
			echo '<div class="notice notice-success is-dismissible"><p><strong>';
;
			esc_html_e( 'VamTam theme resources: ', 'mozo' );
			echo '</strong>';
			echo '<a href="https://vamtam.com/child-themes" target="_blank">';
			esc_html_e( 'Sample child themes', 'mozo' );
			echo '</a>; ';
			echo '<a href="https://vamtam.com/changelog" target="_blank">';
			esc_html_e( 'Changelog', 'mozo' );
			echo '</a>';
			echo '</p></div>';
		}
	}

	public static function icons_selector() {
		?>
		<div class="vamtam-config-icons-selector hidden">
			<input type="search" placeholder="<?php esc_attr_e( 'Filter icons', 'mozo' ) ?>" class="icons-filter"/>
			<div class="icons-wrapper spinner">
				<input type="radio" value="" checked="checked"/>
			</div>
		</div>
		<?php
	}

	/**
	 * Admin helper functions
	 */
	private static function load_functions() {
		require_once VAMTAM_ADMIN_HELPERS . 'base.php';
	}
}

