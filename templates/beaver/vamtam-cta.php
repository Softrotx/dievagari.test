<div class="<?php echo esc_attr( $module->get_classname() ) ?>">
	<?php if ( ! empty( $settings->image ) ) : ?>
		<div class="fl-cta-image">
			<?php echo wp_get_attachment_image( $settings->image ) ?>
		</div>
	<?php endif ?>
	<div class="fl-cta-text">
		<<?php echo esc_html( $settings->title_tag ) ?> class="fl-cta-title"><?php echo wp_kses_post( $settings->title ) ?></<?php echo esc_html( $settings->title_tag ) ?>>
		<span class="fl-cta-text-content"><?php echo wp_kses_post( $settings->text ) ?></span>
	</div>
	<div class="fl-cta-button">
		<?php $module->render_button(); ?>
	</div>
</div>
<style>
	<?php if ( ! empty( $settings->text_color ) ) : ?>
		.fl-node-<?php echo esc_html( $id ) ?>,
		.fl-node-<?php echo esc_html( $id ) ?> * {
			color: <?php echo esc_html( vamtam_sanitize_accent( $settings->text_color ) ); ?>;
		}
	<?php endif; ?>
	<?php if ( ! empty( $settings->bg_color ) ) : ?>
		.fl-node-<?php echo esc_html( $id ) ?> .fl-module-content {
			background-color: <?php echo esc_html( vamtam_sanitize_accent( $settings->bg_color ) ); ?>;
		}
	<?php endif; ?>
</style>
