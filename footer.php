<?php
/**
 * Footer template
 *
 * @package vamtam/mozo
 */

$footer_onepage = ( ! is_page_template( 'onepage.php' ) || vamtam_get_optionb( 'one-page-footer' ) );

$beaver_footer_ids  = class_exists( 'FLThemeBuilderLayoutData' ) ? FLThemeBuilderLayoutData::get_current_page_footer_ids() : array();
$footer_template_id = vamtam_get_option( 'footer-beaver-template' );

?>

<?php if ( ! defined( 'VAMTAM_NO_PAGE_CONTENT' ) ) : ?>
	<?php if ( VamtamTemplates::had_limit_wrapper() ) :  ?>
					</div> <!-- .limit-wrapper -->
	<?php endif ?>

			</div><!-- #main -->

		</div><!-- #main-content -->

		<?php if ( ! is_page_template( 'page-blank.php' ) ) : ?>
			<?php if ( ! empty( $beaver_footer_ids ) ) : ?>
				<div class="footer-wrapper">
					<footer id="main-footer" class="main-footer">
						<?php FLThemeBuilderLayoutRenderer::render_footer(); ?>
					</footer>
				</div>
			<?php elseif ( ( $footer_onepage || is_customize_preview() ) && ! empty( $footer_template_id ) ) : ?>
				<div class="footer-wrapper" style="<?php VamtamTemplates::display_none( $footer_onepage, false ) ?>">
					<footer id="main-footer" class="main-footer">
						<?php
							if ( class_exists( 'FLBuilderShortcodes' ) ) {
								echo FLBuilderShortcodes::insert_layout( array( // xss ok
									'slug' => $footer_template_id,
								) );
							}
						?>
					</footer>
				</div>
			<?php endif ?>
		<?php endif ?>

<?php endif // VAMTAM_NO_PAGE_CONTENT ?>
</div><!-- / #page -->

<?php wp_footer(); ?>
<!-- W3TC-include-js-head -->
</body>
</html>

