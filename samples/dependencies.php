<?php

/**
 * Declare plugin dependencies
 *
 * @package vamtam/mozo
 */

/**
 * Declare plugin dependencies
 */
function vamtam_register_required_plugins() {
	// this is for setting the minimum required version of a bundled plugin (not from store)
	// so tgmpa can see the new version and handle the plugin as updatable.
	$updates = get_site_transient( 'update_plugins' );

	$plugins = array(
		array(
			'name'     => esc_html__( 'SVG Support', 'mozo' ),
			'slug'     => 'svg-support',
			'required' => true,
			'category' => 'required',
		),

		array(
			'name'     => esc_html__( 'Jetpack', 'mozo' ),
			'slug'     => 'jetpack',
			'required' => true,
			'category' => 'required',
		),

		array(
			'name'     => esc_html__( 'Vamtam Offline Jetpack', 'mozo' ),
			'slug'     => 'vamtam-offline-jetpack',
			'source'   => VAMTAM_PLUGINS . 'vamtam-offline-jetpack.zip',
			'required' => true,
			'category' => 'required',
			'version'  => '1.0.0',
		),

		array(
			'name'     => esc_html__( 'Beaver Builder - WordPress Page Builder', 'mozo' ),
			'slug'     => 'beaver-builder-lite-version',
			'required' => true,
			'category' => 'required',
		),

		array(
			'name'     => esc_html__( 'WP Retina 2x', 'mozo' ),
			'slug'     => 'wp-retina-2x',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Max Mega Menu', 'mozo' ),
			'slug'     => 'megamenu',
			'required' => true,
			'category' => 'required',
		),

		array(
			'name'     => esc_html__( 'Contact Form by WPForms', 'mozo' ),
			'slug'     => 'wpforms-lite',
			'required' => true,
			'category' => 'required',
		),

		array(
			'name'     => esc_html__( 'WooCommerce', 'mozo' ),
			'slug'     => 'woocommerce',
			'required' => true,
			'category' => 'required',
		),

		array(
			'name'     => esc_html__( 'The Events Calendar', 'mozo' ),
			'slug'     => 'the-events-calendar',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Event Tickets', 'mozo' ),
			'slug'     => 'event-tickets',
			'required' => false,
			'category' => 'recommended',
		),


		array(
			'name'     => esc_html__( 'Vamtam Elements (B)', 'mozo' ),
			'slug'     => 'vamtam-elements-b',
			'source'   => VAMTAM_PLUGINS . 'vamtam-elements-b.zip',
			'required' => true,
			'category' => 'required',
			'version'  => ( $updates !== false && isset( $updates->response[ 'vamtam-elements-b/vamtam-elements.php' ] ) )
				? $updates->response[ 'vamtam-elements-b/vamtam-elements.php' ]->new_version
				: '1.17.1',
		),

		array(
			'name'     => esc_html__( 'Vamtam Importers', 'mozo' ),
			'slug'     => 'vamtam-importers',
			'source'   => VAMTAM_PLUGINS . 'vamtam-importers.zip',
			'required' => true,
			'category' => 'required',
			'version'  => ( $updates !== false && isset( $updates->response[ 'vamtam-importers/vamtam-importers.php' ] ) )
				? $updates->response[ 'vamtam-importers/vamtam-importers.php' ]->new_version
				: '2.17.0',
		),

		array(
			'name'     => esc_html__( 'Revolution Slider', 'mozo' ),
			'slug'     => 'revslider',
			'source'   => VAMTAM_PLUGINS . 'revslider.zip',
			'required' => true,
			'category' => 'required',
			'version'  => ( $updates !== false && isset( $updates->response[ 'revslider/revslider.php' ] ) )
				? $updates->response[ 'revslider/revslider.php' ]->new_version
				: '6.1.5',
		),

		array(
			'name'         => 'Booked',
			'slug'         => 'booked',
			'required'     => true,
			'category'     => 'required',
			'version'      => '2.4',
			'source'       => VAMTAM_PLUGINS . 'booked.zip',
		),

		array(
			'name'     => esc_html__( 'Wordpress SEO', 'mozo' ),
			'slug'     => 'wordpress-seo',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Really Simple SSL', 'mozo' ),
			'slug'     => 'really-simple-ssl',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Loco Translate', 'mozo' ),
			'slug'     => 'loco-translate',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'UpDraftPlus', 'mozo' ),
			'slug'     => 'updraftplus',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'WP Super Cache', 'mozo' ),
			'slug'     => 'wp-super-cache',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Limit Login Attempts Reloaded', 'mozo' ),
			'slug'     => 'limit-login-attempts-reloaded',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Wordfence', 'mozo' ),
			'slug'     => 'wordfence',
			'required' => false,
			'category' => 'recommended',
		),

		array(
			'name'     => esc_html__( 'Instagram Feed', 'mozo' ),
			'slug'     => 'instagram-feed',
			'required' => false,
			'category' => 'recommended',
 		),

 		array(
			'name'     => esc_html__( 'Cookie Notice for GDPR & CCPA', 'mozo' ),
			'slug'     => 'cookie-notice',
			'required' => false,
			'category' => 'recommended',
		),

		[
			'name'     => esc_html__( 'Classic Widgets', 'mozo' ),
			'slug'     => 'classic-widgets',
			'required' => true,
			'category' => 'required',
		],
	);

	$config = array(
		'default_path' => '',    // Default absolute path to pre-packaged plugins
		'is_automatic' => true,  // Automatically activate plugins after installation or not
		'parent_slug' => 'vamtam_theme_setup',
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'vamtam_register_required_plugins' );

function vamtam_tgmpa_table_columns( $columns ) {
	//Filter the header columns for the plugin page
	$columns = array(
		'cb'     => esc_html__( '<input type="checkbox" />', 'mozo' ),
		'img'     => '',
		'plugin' => esc_html__( 'Name', 'mozo' ),
		'description' => esc_html__( 'Description', 'mozo' ),
		'status'   => esc_html__( 'Status', 'mozo' ),
		'version'   => esc_html__( 'Version', 'mozo' ),
	);
	return $columns;
}
add_filter( 'tgmpa_table_columns', 'vamtam_tgmpa_table_columns' );

function vamtam_tgmpa_table_data_item( $item, $plugin )
{
	$thumbnail_size = '128x128';

	$plugins_no_store_img = array(
		'classic-widgets',
		'booked',
		'booked-calendar-feeds',
		'booked-frontend-agents',
		'booked-woocommerce-payments',
		'revslider',
		'vamtam-offline-jetpack',
		'vamtam-elements-b',
		'vamtam-importers',
		'limit-login-attempts-reloaded',
		'updraftplus',
		'woocommerce-product-archive-customiser',
		'easy-charts',
	);


	$plugins_gif_img = [
		'the-events-calendar',
	];

	// Plugin image
	if ( in_array( $plugin['slug'], $plugins_no_store_img ) ) {
		$thumbnail = VAMTAM_ADMIN_ASSETS_URI . 'images/def-plugin.png';
	} elseif (in_array( $plugin['slug'], $plugins_gif_img ) ) {
		$thumbnail = 'https://ps.w.org/'. $plugin['slug'] .'/assets/icon-'. $thumbnail_size .'.gif';
	} else {
		$thumbnail = 'https://ps.w.org/'. $plugin['slug'] .'/assets/icon-'. $thumbnail_size .'.png';
	}

	$item['img'] = '<img src="' . esc_url( $thumbnail ) . '" width="64" height="64" data-category="' . esc_attr( $plugin['category'] ) . '" >';

	$tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );

	if( $tgmpa_instance->is_plugin_installed( $plugin['slug'] ) ) {
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin['file_path'] );
	} else {
		// Get from store
		$plugin_data = VamtamPluginManager::get_plugin_data_by_slug( $plugin['slug'] );
	}

	// Plugin Version
	if ( ! isset( $item['available_version'] ) || empty( $item['available_version'] ) ) {
		$item['available_version'] = ! empty( $plugin_data ) && $plugin_data['Version'];
	}


	// Plugin description
	if( isset( $plugin_data['Description'] ) ) {
		$item['description'] = $plugin_data['Description'];
	} else {
		$item['description'] =  esc_html__( 'A simple WordPress Plugin.', 'mozo' );
	}
	if ( strpos( $plugin['slug'], 'mozo' ) !== false ) {
		$item['description'] =  esc_html__( 'Theme Exclusive.', 'mozo' );
	}

	return $item;
}
add_filter( 'tgmpa_table_data_item', 'vamtam_tgmpa_table_data_item', 10, 2 );

// vamtam-tgmpa.js will gracefully hide the recommended plugins notice.
function vamtam_hide_tgmpa_notice() {
	$screen = get_current_screen();
	if ( is_admin() && $screen->id === 'plugins' ) {
		wp_enqueue_script( 'vamtam-tgmpa', VAMTAM_ADMIN_ASSETS_URI . 'js/vamtam-tgmpa.js', array( 'jquery' ), VamtamFramework::get_version(), true );
	}
}
add_action('admin_enqueue_scripts', 'vamtam_hide_tgmpa_notice');

/**
 * Essentially a copy of the standard tgmpa plugins page but with our dashboard and validation.
 *
 * This displays the admin page and form area where the user can select to install and activate the plugin.
 * Aborts early if we're processing a plugin installation action.
 *
 * Important!!
 * 	For this to work we need to make tgmpa's do_plugin_install public ( from protected ).
 *
 * @return null Aborts early if we're processing a plugin installation action.
 */
function vamtam_install_plugins_page() {
	$tgmpa = TGM_Plugin_Activation::get_instance();
	// Store new instance of plugin table in object.
	$plugin_table = new TGMPA_List_Table;

	// Single item, row actions are handled by tgmpa's do_plugin_install().
	$row_actions_active = is_callable( [ $tgmpa, 'do_plugin_install' ] );

	// Return early if processing a plugin installation action.
	if ( ( ( 'tgmpa-bulk-install' === $plugin_table->current_action() || 'tgmpa-bulk-update' === $plugin_table->current_action() ) && $plugin_table->process_bulk_actions() ) || ( $row_actions_active && $tgmpa->do_plugin_install() ) ) {
		return;
	}

	// Force refresh of available plugin information so we'll know about manual updates/deletes.
	wp_clean_plugins_cache( false );
	$valid_key = Version_Checker::is_valid_purchase_code();
	?>
	<div id="vamtam-ts-tgmpa" class="vamtam-ts">
			<div id="vamtam-ts-side">
				<?php VamtamPurchaseHelper::dashboard_navigation(); ?>
			</div>
			<div id="vamtam-ts-main">
				<?php if ( true || $valid_key ) : ?>
				<?php $plugin_table->prepare_items(); ?>

				<?php
				if ( ! empty( $tgmpa->message ) && is_string( $tgmpa->message ) ) {
					echo wp_kses_post( $tgmpa->message );
				}
				?>
				<?php $plugin_table->views(); ?>

				<form id="tgmpa-plugins" action="" method="post" class="<?php echo esc_attr( ! $row_actions_active ? 'no-row-actions' : '' ); ?>">
					<?php VamtamPluginManager::filter_tabs(); ?>
					<input type="hidden" name="tgmpa-page" value="<?php echo esc_attr( $tgmpa->menu ); ?>" />
					<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />
					<?php $plugin_table->display(); ?>
				</form>
				<?php else : ?>
					<?php VamtamPurchaseHelper::registration_warning(); ?>
				<?php endif ?>
			</div>
	</div>
	<?php
}

function vamtam_tgmpa_admin_menu_args( $args ) {
	$args['page_title'] = esc_html__( 'Install Plugins', 'mozo' );
	$args['function'] = 'vamtam_install_plugins_page';

	return $args;
}
add_filter( 'tgmpa_admin_menu_args', 'vamtam_tgmpa_admin_menu_args' );

// Hide all notices on theme setup pages.
function vamtam_in_admin_header() {
	if ( isset ( $_GET['page'] ) ) {
		$is_vamtam_ts_page = in_array( $_GET['page'], array(
			'vamtam_theme_setup',
			'tgmpa-install-plugins',
			'vamtam_theme_setup_import_content',
			'vamtam_theme_help',
		) );
		if ( $is_vamtam_ts_page ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}
}
add_action('in_admin_header', 'vamtam_in_admin_header', 1000 );

function vamtam_tgmpa_bulk_install_setup() {
	if ( isset( $_GET['page'] ) && $_GET['page'] === 'tgmpa-install-plugins' ) {
		// this disables the fastcgi buffering for nginx servers
		header('X-Accel-Buffering: no');
	}
}
add_action('admin_init', 'vamtam_tgmpa_bulk_install_setup');
