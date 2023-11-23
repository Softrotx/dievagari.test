<?php

/**
 * Purchase Helper
 *
 * @package vamtam/mozo
 */
/**
 * class VamtamPurchaseHelper
 */
class VamtamPurchaseHelper extends VamtamAjax {

	public static $storage_path;

	/**
	 * Hook ajax actions
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'admin_body_class', array( __CLASS__, 'vamtam_admin_body_class' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu_1'), 11 );

		add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_early_init' ), 5 );
		add_action( 'admin_notices', array( __CLASS__, 'notice_early' ), 5 ); // after TGMPA registers its notices, but before printing

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		add_filter( 'tgmpa_update_bulk_plugins_complete_actions', array( __CLASS__, 'tgmpa_plugins_complete_actions' ), 10, 2 );
	}

	public static function vamtam_admin_body_class( $classes )
	{
		// Adds a class to the body tag to hint for pending verification.
		if ( ! Version_Checker::is_valid_purchase_code() ) {
			$classes .= ' vamtam-not-verified';
		}
		return $classes;
	}

	public static function notice_early() {
		$screen = get_current_screen();
		if ( ! self::is_theme_setup_page() && $screen->id !== 'plugins' ) {
			remove_action( 'admin_notices', array( $GLOBALS['tgmpa'], 'notices' ), 10 );
		}
	}

	private static function server_tests() {
		$timeout = (int) ini_get( 'max_execution_time' );
		$memory  = ini_get( 'memory_limit' );
		$memoryB = str_replace( array( 'G', 'M', 'K' ), array( '000000000', '000000', '000' ), $memory );

		$tests = array(
			array(
				'name'  => esc_html__( 'PHP Version', 'mozo' ),
				'test'  => version_compare( phpversion(), '5.5', '<' ),
				'value' => phpversion(),
				'desc'  => esc_html__( 'While this theme works with all PHP versions supported by WordPress Core, PHP versions 5.5 and older are no longer maintained by their developers. Consider switching your server to PHP 5.6 or newer.', 'mozo' ),
			),
			array(
				'name'  => esc_html__( 'PHP Time Limit', 'mozo' ),
				'test'  => $timeout > 0 && $timeout < 30,
				'value' => $timeout,
				'desc'  => esc_html__( 'The PHP time limit should be at least 30 seconds. Note that in some configurations your server (Apache/nginx) may have a separate time limit. Please consult with your hosting provider if you get a time out while importing the demo content.', 'mozo' ),
			),
			array(
				'name'  => esc_html__( 'PHP Memory Limit', 'mozo' ),
				'test'  => (int) $memory > 0 && $memoryB < 96 * 1024 * 1024,
				'value' => $memory,
				'desc'  => esc_html__( 'You need a minimum of 96MB memory to use the theme and the bundled plugins. For non-US English websites you need a minimum of 128MB in order to accomodate the translation features which are otherwise disabled.', 'mozo' ),
			),
			array(
				'name'  => esc_html__( 'PHP ZipArchive Extension', 'mozo' ),
				'test'  => ! class_exists( 'ZipArchive' ),
				'value' => '',
				'desc'  => esc_html__( 'ZipArchive is a requirement for importing the demo sliders.', 'mozo' ),
			),
		);

		$fail = 0;

		foreach ( $tests as $test ) {
			$fail += (int) $test['test'];
		}

		return array(
			'fail'  => $fail,
			'tests' => $tests,
		);
	}

	private static function is_theme_setup_page() {
		return isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'vamtam_theme_setup' ) );
	}

	public static function admin_scripts() {
		$theme_version = VamtamFramework::get_version();

		wp_register_script( 'vamtam-check-license', VAMTAM_ADMIN_ASSETS_URI . 'js/check-license.js', array( 'jquery' ), $theme_version, true );
		wp_register_script( 'vamtam-import-buttons', VAMTAM_ADMIN_ASSETS_URI . 'js/import-buttons.js', array( 'jquery' ), $theme_version, true );
	}

	public static function tgmpa_plugins_complete_actions( $update_actions, $plugin_info ) {
		if ( isset( $update_actions['dashboard'] ) ) {
			$update_actions['dashboard'] = sprintf(
				esc_html__( 'All plugins installed and activated successfully. %1$s', 'mozo' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=vamtam_theme_setup_import_content' ) ) . '" class="button button-primary">' . esc_html__( 'Continue with theme setup.', 'mozo' ) . '</a>'
			);

			$update_actions['dashboard'] .= '
                <script>
                    window.scroll( 0, 10000000 );
                </script>
            ';
		}

		return $update_actions;
	}

	public static function admin_menu() {
		add_menu_page( esc_html__( 'VamTam', 'mozo' ), esc_html__( 'VamTam', 'mozo' ), 'edit_theme_options', 'vamtam_theme_setup', array( __CLASS__, 'page' ), '', 2 );
		add_submenu_page( 'vamtam_theme_setup', esc_html__( 'Dashboard', 'mozo' ), esc_html__( 'Dashboard', 'mozo' ), 'edit_theme_options', 'vamtam_theme_setup', array( __CLASS__, 'page' ) );
		remove_submenu_page('vamtam_theme_setup','vamtam_theme_setup');
		add_submenu_page( 'vamtam_theme_setup', esc_html__( 'Dashboard', 'mozo' ), esc_html__( 'Dashboard', 'mozo' ), 'edit_theme_options', 'vamtam_theme_setup', array( __CLASS__, 'page' ) );
	}

	public static function admin_menu_1() {
		//Called with a lower priority so 'Installed Plugins' menu item has been registered (tgmpa).
		add_submenu_page( 'vamtam_theme_setup', esc_html__( 'Import Demo Content', 'mozo' ), esc_html__( 'Import Demo Content', 'mozo' ), 'edit_theme_options', 'vamtam_theme_setup_import_content', array( __CLASS__, 'vamtam_theme_setup_import_content' ) );
	}

	public static function registration_warning() {
		$theme_name = ucfirst( wp_get_theme()->get_template() );
		?>
		<div class="vamtam-notice-wrap">
			<div class="vamtam-notice">
				<p>
					<?php echo sprintf( esc_html__( 'The %s theme needs to be registered.', 'mozo' ), $theme_name ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=vamtam_theme_setup' ) ); ?>">
						<?php echo esc_html__( 'Register Now', 'mozo' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	public static function vamtam_theme_setup_import_content() {
		wp_enqueue_script( 'vamtam-check-license' );
		$valid_key = Version_Checker::is_valid_purchase_code();
		?>
		<div id="vamtam-ts-import-content" class="vamtam-ts">
			<div id="vamtam-ts-side">
				<?php self::dashboard_navigation(); ?>
			</div>
			<div id="vamtam-ts-main">
				<?php if ( true || $valid_key ) : ?>
					<?php self::import_buttons() ?>
				<?php else : ?>
					<?php self::registration_warning(); ?>
				<?php endif ?>
			</div>
		</div>
		<?php
	}

	public static function after_setup_theme() {
		if ( self::is_theme_setup_page() ) {
			add_filter( 'heartbeat_settings', [ __CLASS__, 'heartbeat_settings' ] );
		}
	}

	public static function admin_early_init() {
		if ( self::is_theme_setup_page() ) {
			add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );
			add_filter( 'wp_redirect', array( __CLASS__, 'block_redirects_to_admin' ) ); // prevents redirects out of the theme setup page
		}

		if ( get_transient( '_fp_activation_redirect' ) ) {
			delete_transient( '_fp_activation_redirect' );
		}

		if ( get_transient( '_booked_welcome_screen_activation_redirect' ) ) {
			delete_transient( '_booked_welcome_screen_activation_redirect' );
		}
	}

	public static function block_redirects_to_admin( $location ) {
		if ( strpos( $location, 'wp-admin/' ) !== false ) {
			return false; // block redirect to other admin pages
		}

		return $location;
	}

	public static function admin_init() {
		$purchase_code_option_id = VamtamFramework::get_purchase_code_option_key();

		add_settings_section(
			'vamtam_purchase_settings_section',
			'',
			array( __CLASS__, 'settings_section' ),
			'vamtam_theme_setup'
		);
		add_settings_field(
			$purchase_code_option_id,
			esc_html__( 'Enter your purchase code from ThemeForest to receive theme updates and support.', 'mozo' ),
			array( __CLASS__, 'purchase_key' ),
			'vamtam_theme_setup',
			'vamtam_purchase_settings_section',
			array(
				$purchase_code_option_id,
			)
		);

		register_setting(
			'vamtam_theme_setup',
			$purchase_code_option_id,
			array( __CLASS__, 'sanitize_license_key' )
		);
	}

	public static function sanitize_license_key( $value ) {
		return preg_replace( '/[^-\w\d]/', '', $value );
	}

	public static function settings_section() {
	}

	public static function heartbeat_settings( $settings ) {
		$settings['interval'] = 15;
		return $settings;
	}

	public static function page() {
		wp_enqueue_script( 'vamtam-check-license' );

		$status = self::server_tests();
		$theme_name = ucfirst( wp_get_theme()->get_template() );
		$theme_version = VamtamFramework::get_version();
		$valid_key = Version_Checker::is_valid_purchase_code();

		?>
		<h2></h2>

		<div id="vamtam-ts-homepage" class="vamtam-ts">
			<div id="vamtam-ts-side">
				<?php self::dashboard_navigation(); ?>
			</div>
			<div id="vamtam-ts-main">
				<div id="vamtam-ts-dash-register">
					<div id="vamtam-ts-register-product">
						<?php
							if ( defined( 'ENVATO_HOSTED_SITE' ) ) :
								esc_html_e( 'All done.', 'mozo' );
							else :
						?>
							<form id="vamtam-register-form" method="post" action="options.php" autocomplete="off">
								<?php if ( $valid_key ) : ?>
									<div id="vamtam-verified-code">
										<p>
											<?php  esc_html_e( 'Thanks for the verification!', 'mozo' ) ?>
											<br />
											<?php echo esc_html( sprintf( __( 'You can now enjoy %s and build great websites.', 'mozo' ) , $theme_name ) ); ?>
										</p>
									</div>
								<?php else : ?>
									<svg id="vamtam-envato-logo" viewBox="0 0 178 34">
										<path d="M45.64 6.939c-7.58 0-13.08 5.64-13.08 13.4 0 7.76 5.44 13.29 13.34 13.29a12.75 12.75 0 0 0 9.61-3.79 2.81 2.81 0 0 0 .83-1.83 2.14 2.14 0 0 0-2.24-2.19 2.59 2.59 0 0 0-1.83.83 8.75 8.75 0 0 1-6.37 2.67 7.9 7.9 0 0 1-8-7.38H55c1.86 0 2.77-.87 2.77-2.66a9.61 9.61 0 0 0-.11-1.66c-.92-6.71-5.42-10.68-12.02-10.68zm0 4.16c4.11 0 6.75 2.62 6.91 6.84H37.89a7.64 7.64 0 0 1 7.75-6.84zm28.48-4.16a9.3 9.3 0 0 0-8.19 4.73v-1.66c0-2.63-2-2.76-2.45-2.76a2.44 2.44 0 0 0-2.48 2.76v20.49a2.62 2.62 0 1 0 5.22 0v-11c0-4.78 2.71-8.13 6.59-8.13s5.6 2.47 5.6 7.55v11.58a2.62 2.62 0 1 0 5.21 0v-13.23c-.02-4.99-2.51-10.33-9.5-10.33zm33.08.27a2.72 2.72 0 0 0-2.6 2.08l-7.14 17.94-7.08-17.94a2.76 2.76 0 0 0-2.65-2.08 2.56 2.56 0 0 0-2.61 2.5 3.56 3.56 0 0 0 .33 1.47l8.2 19.36c1 2.34 2.58 2.83 3.76 2.83 1.18 0 2.78-.49 3.76-2.82l8.26-19.47a3.86 3.86 0 0 0 .32-1.43 2.44 2.44 0 0 0-2.55-2.44zm15.16-.27a14.9 14.9 0 0 0-8.74 2.61 2.39 2.39 0 0 0-1.17 2.06 2 2 0 0 0 2 2.08 2.84 2.84 0 0 0 1.55-.55 10.25 10.25 0 0 1 5.86-1.94c3.85 0 6.06 2 6.06 5.38v.57c-8.65 0-17.45 1.06-17.45 8.59 0 5.42 4.63 7.84 9.22 7.84a9.72 9.72 0 0 0 8.44-4.19v1.32a2.4 2.4 0 0 0 2.45 2.66 2.35 2.35 0 0 0 2.42-2.66v-13.81c0-6.24-4-9.96-10.64-9.96zm4.5 14.07H128v1.2c0 4.4-2.79 7.23-7.12 7.23-1.18 0-5-.27-5-3.79-.06-4.18 6.24-4.64 10.98-4.64zm19.68-9.07a2.11 2.11 0 0 0 2.4-2.13 2.13 2.13 0 0 0-2.4-2.18h-4.69v-4.75a2.6 2.6 0 1 0-5.17 0v22.54c0 5.2 2.57 8 7.42 8a8.2 8.2 0 0 0 3.29-.6 2.34 2.34 0 0 0 1.44-2.06 2 2 0 0 0-2.08-2.08 3.92 3.92 0 0 0-.93.16 4.34 4.34 0 0 1-1.08.16c-2 0-2.89-1.29-2.89-4.06v-13h4.69zm17.24-5c-7.88 0-13.61 5.59-13.61 13.29a13.29 13.29 0 0 0 3.91 9.63 13.75 13.75 0 0 0 9.7 3.77c7.79 0 13.67-5.76 13.67-13.4 0-7.64-5.75-13.29-13.67-13.29zm0 22.27c-5.41 0-8.23-4.51-8.23-9 0-6.13 4.27-8.92 8.23-8.92 3.96 0 8.22 2.81 8.22 8.94 0 6.13-4.25 8.98-8.22 8.98z" fill="#141614"></path><path d="M23.64 4.569C19.45-.341 5.89 9.169 6 21.429a.58.58 0 0 1-.57.57.58.58 0 0 1-.49-.28 13.13 13.13 0 0 1-.52-9.65.53.53 0 0 0-.9-.52A13 13 0 0 0 0 20.439a13 13 0 0 0 13.15 13.15c18.5-.42 14.23-24.64 10.49-29.02z" fill="#80B341"></path>
									</svg>
								<?php endif ?>
							<?php
								settings_fields( 'vamtam_theme_setup' );
								do_settings_sections( 'vamtam_theme_setup' );
							?>
							</form>
						<?php endif; ?>
					</div>
				</div>
				<div id="vamtam-check-license-disclaimer">
					<h5><?php esc_html_e( 'Licensing Terms', 'mozo' ); ?></h5>
					<p>
						<?php esc_html_e( 'Please be advised, in order to use the theme in a legal manner, you need to purchase a separate license for each domain you are going to use the theme on. A single license is limited to a single domain/application. For more information please refer to the license included with the theme or ', 'mozo' ); ?>
						<a href="http://themeforest.net/licenses" target="_blank">
							<?php esc_html_e( 'Licensing Terms', 'mozo' ); ?>
						</a>
						<?php esc_html_e( ' on the ThemeForest site.', 'mozo'); ?>
					</p>
				</div>
				<?php if ( current_user_can( 'switch_themes' ) ) : ?>
					<?php if ( ! defined( 'ENVATO_HOSTED_SITE' ) ) : ?>
						<div id="vamtam-server-tests">
							<h3>
								<?php if ( $status['fail'] > 0 ) : ?>
									<?php esc_html_e( 'System Status', 'mozo' ) ?>
									<?php $fail = $status['fail']; ?>
									<small><?php printf( esc_html( _n( '(one potential issue)', '(%d potential issues)', $fail, 'mozo' ) ), $fail ) ?></small>
								<?php endif ?>
							</h3>
						</div>
					<?php endif ?>
				<?php endif ?>
			</div>
		</div>
		<?php
	}

	public static function dashboard_navigation()
	{
		$theme_name = strtoupper( wp_get_theme()->get_template() );
		$theme_version = VamtamFramework::get_version();
		$valid_key = Version_Checker::is_valid_purchase_code();
		$plugin_status = VamtamPluginManager::get_required_plugins_status();
		$content_imported = ! ! get_option( 'vamtam_last_import_map', false );

		$routes = [
		'vamtam_theme_setup',
		'tgmpa-install-plugins',
		'vamtam_theme_setup_import_content',
		'vamtam_theme_help',
		];
		$cur_route = get_current_screen()->id;
		?>
		<nav id="vamtam-ts-nav-menu">
			<div id="vamtam-theme-title">
				<span id="vamtam-ts-greeter"><?php esc_html_e( 'WELCOME TO', 'mozo' ); ?></span>
				<span id="vamtam-ts-greeter-title"><?php echo sprintf( esc_html__( '%s', 'mozo' ), $theme_name ); ?></span>
				<span id="vamtam-ts-greeter-ver"><?php echo sprintf( esc_html__( 'VER. %s', 'mozo' ), $theme_version ); ?></span>
			</div>
			<ul>
				<li class="<?php echo esc_attr( $cur_route === 'toplevel_page_' . $routes[0] ? 'is-active' : '' ); ?>" >
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $routes[0] ) ); ?>">
						<svg class="ts-icon" xmlns="http://www.w3.org/2000/svg" width="21" height="30" viewBox="0 0 21 30"><path fill-rule="evenodd" d="M2.4 11.3l-.1.1V6.5C2.5 2.7 5.5 0 9.6 0h.2c2.3 0 5 0 7.1 2.1 2.3 2.2 2 5.8 1.9 8.8v.6c-.8-.8-1.6-1.4-2.5-2V6.8l-.1-.1a3.2 3.2 0 0 0 0-.3L16 6v-.2l-.1-.3v-.2h-.1V5a4.3 4.3 0 0 0-.3-.5 1.7 1.7 0 0 0-.2-.3.7.7 0 0 0-.1-.1l-.2-.2c-1.4-1.4-2.7-1.4-5.3-1.4h-.2C6.9 2.5 5 4 4.9 6.5v3.1l-.6.3-.1.1-1 .6-.1.2H3l-.6.5zM10.5 30A10.5 10.5 0 0 1 0 19.9a9 9 0 0 1 2.5-6.4 11.4 11.4 0 0 1 8.3-3.7c1.3 0 2.6.3 3.9.8A10.5 10.5 0 0 1 21 20c.1 5.3-4.7 10-10.5 10.1zm0-12.3c-.9 0-1.6.7-1.6 1.6 0 .5.3 1 .8 1.3v1.9h1.6v-1.9c.5-.2.9-.8.9-1.3 0-1-.8-1.6-1.7-1.6z"/></svg>
						<span><?php echo esc_html__( 'Register' , 'mozo' ); ?></span>
						<span class="vamtam-step-status <?php echo esc_attr( $valid_key ? 'success' : 'error' ); ?>"></span>
					</a>
				</li>
				<?php $tgmpa_instance 	= call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) ); ?>
				<?php if ( isset( $tgmpa_instance ) && isset( $tgmpa_instance->page_hook ) ) : ?>
					<li class="<?php echo esc_attr( $cur_route === 'vamtam_page_' . $routes[1] ? 'is-active' : '' ); ?>" >
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $routes[1] ) ); ?>">
							<span class="ts-icon dashicons dashicons-admin-plugins"></span>
							<span><?php echo esc_html__( 'Install Plugins' , 'mozo' ); ?></span>
							<span class="vamtam-step-status <?php echo esc_attr( $plugin_status ); ?>"></span>
						</a>
					</li>
				<?php endif ?>
				<li class="<?php echo esc_attr( $cur_route === 'vamtam_page_' . $routes[2] ? 'is-active' : '' ); ?>" >
					<a <?php echo esc_attr( $plugin_status !== 'success' ? 'class=disabled' : '' ); ?> href="<?php echo esc_url( admin_url( 'admin.php?page=' . $routes[2] ) ); ?>">
						<svg class="ts-icon" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path fill-rule="evenodd" d="M25.6 25.6A15 15 0 0 0 4.4 4.4l2 2a12.2 12.2 0 1 1 0 17.2l-2 2a15 15 0 0 0 21.2 0zM0 13.7v2.8h16.7l-4.2 4.2 2 2 7.6-7.6-7.6-7.5-2 2 4.2 4.1H0z"/></svg>
						<span><?php echo esc_html__( 'Import Demo' , 'mozo' ); ?></span>
						<span class="vamtam-step-status <?php echo esc_attr( $content_imported ? 'success' : 'error' ); ?>"></span>
					</a>
				</li>
			</ul>
			<img id="vamtam-human-menu" src="<?php echo esc_attr( VAMTAM_ADMIN_ASSETS_URI . 'images/vamtam-human-admin.svg' ); ?>"></img>
			<a id="vamtam-hs-btn" class="<?php echo esc_attr( $cur_route === 'vamtam_page_' . $routes[3] ? 'is-active' : ''); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $routes[3] ) ); ?>">
				<svg class="ts-icon" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path fill-rule="evenodd" d="M9.5 7.8L5.2 3.6a15 15 0 0 1 19.6 0l-4.3 4.2a9 9 0 0 0-11 0zm16.9-2.6a15 15 0 0 1 0 19.6l-4.2-4.3a9 9 0 0 0 0-11l4.2-4.3zM7.8 20.5l-4.2 4.3a15 15 0 0 1 0-19.6l4.2 4.3a9 9 0 0 0 0 11zm12.7 1.7l4.3 4.2a15 15 0 0 1-19.6 0l4.3-4.2a9 9 0 0 0 11 0z"/></svg>
				<span><?php echo esc_html__( 'Help & Support' , 'mozo' ); ?></span>
			</a>
		</nav>
		<?php
	}

	public static function import_buttons() {
		wp_enqueue_script( 'vamtam-import-buttons' );

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$revslider  = defined( 'RS_PLUGIN_PATH' );
		$ninjaforms = function_exists( 'Ninja_Forms' );
		$jetpack    = class_exists( 'Jetpack' );
		$booked     = class_exists( 'booked_plugin' );

		$content_allowed = $jetpack;

		$content_imported = ! ! get_option( 'vamtam_last_import_map', false );
		$content_disable  = $content_imported ? '' : ' disabled content-disabled';

		$messages = array(
			'success-msg' => esc_html__( 'Imported.', 'mozo' ),
			'error-msg  ' => esc_html__( 'Failed to import. Please <a href="{fullimport}" target="_blank">click here</a> in order to see the full error message.', 'mozo' ),
		);

		$import_tests = array(
			array(
				'test'  => true,
				'title' => esc_html__( 'Posts and Pages', 'mozo' ),
			),
			array(
				'test'   => vamtam_has_woocommerce(),
				'title'  => esc_html__( 'Products', 'mozo' ),
				'failed' => wp_kses_data( __( '(Please install and activate <strong>WooCommerce</strong> first.)', 'mozo' ) ),
			),
			array(
				'test'   => class_exists( 'Tribe__Events__Main' ),
				'title'  => esc_html__( 'Events', 'mozo' ),
				'failed' => wp_kses_data( __( '(Please install and activate <strong>The Events Calendar</strong> first.)', 'mozo' ) ),
			),
			array(
				'test'   => class_exists( 'Mega_Menu' ),
				'title'  => esc_html__( 'Max Mega Menu settings', 'mozo' ),
				'failed' => wp_kses_data( __( '(Please install and activate <strong>Max Mega Menu</strong> first.)', 'mozo' ) ),
			),
		);

		$will_import = array();

		foreach ( $import_tests as $test ) {
			$will_import[] = '<li><span class="import-status ' . esc_attr( $test['test'] ? 'yes' : 'no' ) . '"></span><span>' . $test['title'] . '</span></li>';
		}

		$revslider_samples_dir = VAMTAM_SAMPLES_DIR . 'revslider/';
		$revslider_samples     = is_dir( $revslider_samples_dir ) ? iterator_count( new FilesystemIterator( $revslider_samples_dir , FilesystemIterator::SKIP_DOTS ) ) : 0;

		$attachments_todo   = get_option( 'vamtam_import_attachments_todo', [ 'attachments' => [] ] )['attachments'];
		$total_attachements = is_countable( $attachments_todo ) ? count( $attachments_todo ) : 0;

		$img_progress = $total_attachements > 0 && class_exists( 'Vamtam_Importers' ) && is_callable( [ 'Vamtam_Importers', 'get_attachment_progress' ] ) ?
			Vamtam_Importers::get_attachment_progress()['text'] :
			esc_html__( 'checking...', 'mozo' );

		$buttons = array(
			array(
				'label'          => esc_html__( 'Dummy Content Import', 'mozo' ),
				'id'             => 'content-import-button',
				'description'    => esc_html__( 'You are advised to use this importer only on new WordPress sites. Jetpack must be installed and active.', 'mozo' ),
				'button_title'   => esc_html__( 'Import', 'mozo' ),
				'href'           => $content_allowed ? wp_nonce_url( admin_url( 'admin.php?import=wpv&step=2' ), 'vamtam-import' ) : 'javascript:void( 0 )',
				'type'           => 'button',
				'class'          => $content_allowed ? 'vamtam-import-button' : 'disabled',
				'data'           => array_merge( $messages, [
					'content-imported' => $content_imported,
					'success-msg'      => sprintf( esc_html__( 'Main content imported. Image import progress: <span class="vamtam-image-import-progress">%s</span>.', 'mozo' ), $img_progress ),
					'fail-msg'         => esc_html__( 'Failed to import - error {statuscode}. <a href="{fullimport}" target="_blank">Click here</a> in order to see the full error message returned by your server. We recommend that you contact your hosting provider for advice, as solving this issue is often specific to each server.', 'mozo' ),
					'timeout-msg'      => esc_html__( 'Failed to import. This is most likely caused by a timeout. Please contact your hosting provider for advice as to how you can increase the time limit on your server.', 'mozo' ),
				] ),
				'disabled_msg'   => wp_kses( __( 'Please install and activate <strong>Jetpack</strong> first.', 'mozo' ), [ 'strong' => [] ] ),
				'additional_msg' => sprintf( wp_kses( __( '<ul id="import-statuses">%s</ul>Images will be downloaded in the background after the main import is complete. Depending on your server, this may take several minutes.<br>In the meantime you may notice that some images are not visible.', 'mozo' ), [ 'ul' => ['id'], 'br' ] ), implode( '<br>', $will_import ) ),
			),

			array(
				'label'             => esc_html__( 'Widget Import', 'mozo' ),
				'id'                => 'widget-import-button',
				'warning'           => esc_html__( 'Using this importer will overwrite your current sidebar settings', 'mozo' ),
				'button_title'      => esc_html__( 'Import', 'mozo' ),
				'href'              => wp_nonce_url( admin_url( 'admin.php?import=vamtam_widgets' ), 'vamtam-import' ),
				'type'              => 'button',
				'class'             => 'vamtam-import-button' . $content_disable,
				'data'              => $messages,
				'disabled_msg'      => esc_html__( 'You must import the demo content before the widgets.', 'mozo' ),
				'disabled_msg_href' => 'nolink',
				'data'              => array_merge( $messages, [
					'warning-msg'   => esc_html__( 'Using this importer will overwrite your current sidebar settings', 'mozo' ),
				] ),
			),

			array(
				'label'              => esc_html__( 'Slider Revolution', 'mozo' ),
				'description'        => '',
				'id'                 => 'slider-import-button',
				'button_title'       => esc_html__( 'Import', 'mozo' ),
				'href'               => $revslider && $revslider_samples > 0 ? wp_nonce_url( 'admin.php?import=vamtam_revslider', 'vamtam-import-revslider' ) : 'javascript:void( 0 )',
				'type'               => 'button',
				'class'              => $revslider && $revslider_samples > 0 ? 'vamtam-import-button' : 'disabled',
				'data'               => $messages,
				'disabled_msg'       => $revslider ? '' : wp_kses( __( 'Please install and activate <strong>Slider Revolution</strong> first. ', 'mozo' ), [ 'strong' => [] ] ),
				'disabled_msg_plain' => $revslider_samples > 0 ? '' : wp_kses( __( 'Slider samples are misssing. Please contact <a href="mailto:support@vamtam.com">VamTam Support</a>', 'mozo' ), 'vamtam-a-span' ),
			),

			array(
				'label'        => esc_html__( 'Booked', 'mozo' ),
				'id'           => 'booked-import-button',
				'description'  => esc_html__( 'All Booked Settings, calendars, etc.', 'mozo' ),
				'warning'	   => esc_html__( 'Using this importer will overwrite your current Booked settings', 'mozo' ),
				'button_title' => esc_html__( 'Import', 'mozo' ),
				'href'         => $booked ? wp_nonce_url( 'admin.php?import=vamtam_booked', 'vamtam-import-booked' ) : 'javascript:void( 0 )',
				'type'         => 'button',
				'class'        => $booked ? 'vamtam-import-button' : 'disabled',
				'data'         => $messages,
				'disabled_msg' => wp_kses( __( 'Please install and activate <strong>Booked</strong> first.', 'mozo' ), [ 'strong' => [] ] ),
			),
		);

		echo '<div class="main-content">';

		foreach ( $buttons as $button ) {
			self::render_button( $button );
		}

		echo '</div>';
	}

	public static function render_button( $button ) {
		echo '<div class="vamtam-box-wrap">';
		echo '<header><h3>' . esc_html( $button['label'] ) . '</h3></header>';

		$data = array();

		if ( isset( $button['data'] ) ) {
			foreach ( $button['data'] as $attr_name => $attr_value ) {
				$data[] = 'data-' . sanitize_title_with_dashes( $attr_name ) . '="' . esc_attr( $attr_value ) . '"';
			}
		}

		$data = implode( ' ', $data );

		echo '<div class="content">';

		if ( strpos( $button['class'], 'disabled' ) !== false ) {
			$href = isset( $button['disabled_msg_href'] ) ? $button['disabled_msg_href'] : admin_url( 'admin.php?page=tgmpa-install-plugins&plugin_status=required' );
			echo '<p class="vamtam-description">';
			if ( $href !== 'nolink' ) {
				echo '<a href="' . esc_html( $href ) . '">' . wp_kses_data( $button['disabled_msg'] ) . '</a>';
			} else {
				echo wp_kses_data( $button['disabled_msg'] );
			}
			echo '</p>';

			if ( isset( $button['disabled_msg_plain'] ) ) {
				echo '<p class="vamtam-description">' . wp_kses_data( $button['disabled_msg_plain'] ) . '</p>';
			}
		} else {
			if ( isset( $button['description'] ) ) {
				echo '<p class="vamtam-description">' . wp_kses_data( $button['description'] ) . '</p>';
			}
			if ( isset( $button['warning'] ) ) {
				echo '<p class="vamtam-description warning">' . $button['warning'] . '</p>'; // xss ok
			}
		}

		if ( isset( $button['additional_msg'] ) ) {
			echo '<p class="vamtam-description">' . $button['additional_msg'] . '</p>'; // xss ok
		}

		echo '<div class="import-btn-wrap">';
		echo '<a href="' . ( isset( $button['href'] ) ? esc_attr( $button['href'] ) : '#' ) . '" id="' . esc_attr( $button['id'] ) . '" title="' . esc_attr( $button['button_title'] ) . '" class="vamtam-ts-button ' . esc_attr( $button['class'] ) . '" ' . $data . '>' . esc_html( $button['button_title'] ) . '</a>'; // xss ok - $data escaped above
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	public static function purchase_key( $args ) {
		$valid_key = Version_Checker::is_valid_purchase_code();
		$option_value = get_option( $args[0] );
		$placeholder = __( 'XXXXXX-XXX-XXXX-XXXX-XXXXXXXX', 'mozo' );
		$plugin_status = VamtamPluginManager::get_required_plugins_status();
		$content_imported = ! ! get_option( 'vamtam_last_import_map', false );


		$button_data = '';

		$data = array(
			'nonce'     => wp_create_nonce( 'vamtam-check-license' ),
		);

		if ( ! defined( 'ENVATO_HOSTED_SITE' ) ) {
			echo '<div id="vamtam-check-license-result"></div>';
		}
		echo '<div class="vamtam-licence-wrap">';
		if ( $valid_key ) {
			echo '<span id="vamtam-license-result"';
			echo 'class="valid">';
			echo '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30"><path fill-rule="evenodd" d="M30 15a15 15 0 1 1-30 0 15 15 0 0 1 30 0zm-2.7-4.4L15.7 22.3a1 1 0 0 1-1.4 0L7 13.7a1 1 0 0 1 1.4-1.3l6.6 7.7L26.5 8.7a13 13 0 1 0 .8 1.9z"/></svg>';
			esc_html_e( 'Valid', 'mozo' );
			echo '</span>';
		} else {
			echo '<span id="vamtam-license-result-wrap">';
			echo '<span class="valid">';
			echo '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30"><path fill-rule="evenodd" d="M30 15a15 15 0 1 1-30 0 15 15 0 0 1 30 0zm-2.7-4.4L15.7 22.3a1 1 0 0 1-1.4 0L7 13.7a1 1 0 0 1 1.4-1.3l6.6 7.7L26.5 8.7a13 13 0 1 0 .8 1.9z"/></svg>';
			esc_html_e( 'Valid', 'mozo' );
			echo '</span>';
			echo '<span class="invalid">';
			echo '<span class="dashicons dashicons-no-alt"></span>';
			esc_html_e( 'Invalid', 'mozo' );
			echo '</span>';
			echo '</span>';
		}
		echo '<input type="text" id="vamtam-envato-license-key" name="' . esc_attr( $args[0] ) . '" value="' . ( $valid_key && vamtam_sanitize_bool( $option_value ) ? esc_attr( $option_value ) : '' ) . '" size="64" ' . ( defined( 'SUBSCRIPTION_CODE' ) ? 'disabled' : '' ) . 'placeholder="' . $placeholder . '"' . '/>';
		if ( $valid_key ) {
			echo '<button id="vamtam-check-license" class="button unregister" data-nonce="'. esc_attr( $data['nonce'] ) .'">';
			echo '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 20 20"><path fill="white" d="M15.6 3.1h-4.1V1.5c0-.4-.2-.7-.4-1-.3-.2-.6-.3-1-.3H6.9c-.4 0-.7.1-1 .4-.2.2-.4.5-.4 1V3H1.4l-.5.2-.1.4.1.4c.2.2.3.2.5.2h.8L3.5 18c0 .3.2.5.5.8.2.2.5.3.8.3h7.4a1.2 1.2 0 0 0 1.2-1.2l1.4-13.7h.8c.2 0 .3 0 .5-.2l.1-.4-.1-.4a.6.6 0 0 0-.5-.2zM6.7 1.5v-.1h3.6V3H6.8V1.5zm7 2.8L12.2 18v.1H4.7L3.3 4.2h10.2z"/></svg>';
			echo '</button>';
		}
		echo '</div>';

		if ( ! defined( 'ENVATO_HOSTED_SITE' ) ) {
			echo '<span style="display: block">';

			if ( ! $valid_key ) {
				echo '<p id="vamtam-code-help">' . wp_kses( sprintf( __( ' <a href="%s" target="_blank">Where can I find my Item Purchase Code?</a>', 'mozo' ), 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' ), 'vamtam-a-span' ) . '</p>';
				echo '<button id="vamtam-check-license" class="button" ';

				foreach ( $data as $key => $value ) {
					echo ' data-' . $key . '="' . esc_attr( $value ) . '"';
				}

				echo '>' . esc_html__( 'Register', 'mozo' );
				echo '</button>';
			} else if ( $plugin_status !== 'success' ) {
				echo '<a id="vamtam-plugin-step" class="button vamtam-ts-button" href="' . esc_url( admin_url( 'admin.php?page=tgmpa-install-plugins' ) ) . '">';
				echo __( 'Continue to required plguins', 'mozo' );
				echo '</a>';
			} elseif ( ! $content_imported ) {
				echo '<a id="vamtam-import-step" class="button vamtam-ts-button" href="' . esc_url( admin_url( 'admin.php?page=vamtam_theme_setup_import_content' ) ) . '">';
				echo __( 'Continue to demo import', 'mozo' );
				echo '</a>';
			}

			echo '</span>';
		}
	}
}

