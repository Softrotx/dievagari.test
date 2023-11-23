<?php

function vamtam_get_mobile_header_breakpoint() {
	$breakpoint = '959px';

	if ( class_exists( 'Mega_Menu' ) ) {
		$settings               = get_option( 'megamenu_settings' );
		$current_theme_location = 'menu-header';

		$locations = get_nav_menu_locations();

		if ( isset( $settings[ $current_theme_location ]['enabled'] ) && $settings[ $current_theme_location ]['enabled'] == true ) {

			if ( ! isset( $locations[ $current_theme_location ] ) ) {
				return $breakpoint;
			}

			$menu_id = $locations[ $current_theme_location ];

			if ( ! $menu_id ) {
				return $breakpoint;
			}

			$style_manager = new Mega_Menu_Style_Manager();
			$themes        = $style_manager->get_themes();

			$menu_theme = isset( $themes[ $settings[ $current_theme_location ]['theme'] ] ) ? $themes[ $settings[ $current_theme_location ]['theme'] ] : $themes['default'];

			$breakpoint = absint( $menu_theme['responsive_breakpoint'] ) . 'px';
		}
	}

	return $breakpoint;
}

function vamtam_mobile_header_less_var( $variables ) {
	$variables['mobile-top-bar-resolution'] = vamtam_get_mobile_header_breakpoint();

	return $variables;
}
add_filter( 'vamtam_less_vars', 'vamtam_mobile_header_less_var' );

/**
 * Mobile menu bar
 *
 * @param string $nav_menu
 * @param object $args
 * @return string
 */
function vamtam_add_mobile_top_bar( $nav_menu, $args ) {
	if ( ! is_a( $args->walker, 'Mega_Menu_Walker' ) || $args->theme_location !== 'menu-header' ) {
		return $nav_menu;
	}

	$megamenu_settings = get_option( 'megamenu_settings' );

	$mobile_top_bar = isset( $megamenu_settings['vamtam-mobile-top-bar'] ) ? stripslashes( $megamenu_settings['vamtam-mobile-top-bar'] ) : '';
	$mobile_top_bar = do_shortcode( $mobile_top_bar );

	$replace = '<div class="mobile-top-bar">' . $mobile_top_bar . '</div>';

	return preg_replace( '!(?=</div>$)!', $replace, $nav_menu );
}

function vamtam_megamenu_general_settings( $saved_settings ) {
	$mobile_search  = isset( $saved_settings['vamtam-mobile-search'] ) ? $saved_settings['vamtam-mobile-search'] : '';
	$mobile_cart    = isset( $saved_settings['vamtam-mobile-cart'] ) ? $saved_settings['vamtam-mobile-cart'] : '';
	$mobile_top_bar = isset( $saved_settings['vamtam-mobile-top-bar'] ) ? stripslashes( $saved_settings['vamtam-mobile-top-bar'] ) : '';
?>
	<h4 class="first"><?php esc_html_e( 'VamTam Additions', 'mozo' ); ?></h4>
	<table>
		<tr>
			<td class='mega-name'>
				<?php esc_html_e( 'Enable Search in Mobile Header', 'mozo' ); ?>
				<div class='mega-description'></div>
			</td>
			<td class='mega-value'>
				<label>
					<input type='radio' name='settings[vamtam-mobile-search]' value="on" <?php checked( $mobile_search, 'on' ); ?> />
					<?php esc_html_e( 'On', 'mozo' ) ?>
				</label>
				<label>
					<input type='radio' name='settings[vamtam-mobile-search]' value="off" <?php checked( $mobile_search, 'off' ); ?> />
					<?php esc_html_e( 'Off', 'mozo' ) ?>
				</label>
			</td>
		</tr>
		<tr>
			<td class='mega-name'>
				<?php esc_html_e( 'Enable WooCommerce Cart in Mobile Header', 'mozo' ); ?>
				<div class='mega-description'></div>
			</td>
			<td class='mega-value'>
				<label>
					<input type='radio' name='settings[vamtam-mobile-cart]' value="on" <?php checked( $mobile_cart, 'on' ); ?> />
					<?php esc_html_e( 'On', 'mozo' ) ?>
				</label>
				<label>
					<input type='radio' name='settings[vamtam-mobile-cart]' value="off" <?php checked( $mobile_cart, 'off' ); ?> />
					<?php esc_html_e( 'Off', 'mozo' ) ?>
				</label>
			</td>
		</tr>
		<tr>
			<td class='mega-name'>
				<?php esc_html_e( 'Mobile Top Bar', 'mozo' ); ?>
				<div class='mega-description'></div>
			</td>
			<td class='mega-value'>
				<textarea name='settings[vamtam-mobile-top-bar]'><?php echo esc_textarea( $mobile_top_bar ) ?></textarea>
			</td>
		</tr>
	</table>
<?php
}
add_action( 'megamenu_general_settings', 'vamtam_megamenu_general_settings', 10, 1 );
