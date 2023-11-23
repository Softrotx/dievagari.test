<?php
	$columns = count( $settings->pricing_columns );
?>
<div class="fl-pricing-table fl-pricing-table-spacing-<?php echo esc_attr( $settings->spacing ) ?> fl-pricing-table-border-<?php echo esc_attr( $settings->border_size ) ?> fl-pricing-table-<?php echo esc_attr( $settings->border_radius ) ?>">
	<?php

	for ( $i = 0; $i < count( $settings->pricing_columns ); $i++ ) :

		if ( ! is_object( $settings->pricing_columns[ $i ] )) continue;

		$pricing_column = $settings->pricing_columns[ $i ];

		// inner-wrap style
		$inner_style = '';

		if ( ! empty( $pricing_column->background ) ) {
			$inner_style .= 'background:' . vamtam_sanitize_accent( $pricing_column->background ) . ';';
		}

		if ( ! empty( $pricing_column->border ) ) {
			$inner_style .= 'border-color:' . vamtam_sanitize_accent( $pricing_column->border ) . ';';
		}

		if ( ! empty( $pricing_column->text_color ) ) {
			$inner_style .= 'color:' . vamtam_sanitize_accent( $pricing_column->text_color ) . ';';
		}

		// highlight style
		$highlight_style =
			'background:' . vamtam_sanitize_accent( $pricing_column->highlight_background ) . ';' .
			'color:' . vamtam_sanitize_accent( $pricing_column->highlight_color ) . ';';
	?>
	<div class="fl-pricing-table-col-<?php echo esc_attr( $columns ) ?>">
		<div class="fl-pricing-table-column fl-pricing-table-column-<?php echo esc_attr( $i ) ?>">
			<div class="fl-pricing-table-inner-wrap" style="<?php echo esc_attr( $inner_style ) ?>">
				<h2 class="fl-pricing-table-title" <?php if ( 'title' === $settings->highlight ) echo 'style="' . esc_attr( $highlight_style ) . '"' ?>><?php echo wp_kses_post( $pricing_column->title ) ?></h2>
				<div class="fl-pricing-table-price" <?php if ( 'price' === $settings->highlight ) echo 'style="' . esc_attr( $highlight_style ) . '"' ?>>
					<?php echo wp_kses_post( $pricing_column->price ) ?>

					<?php if ( ! empty( $pricing_column->duration ) ) : ?>
						<span class="fl-pricing-table-duration"><?php echo wp_kses_post( $pricing_column->duration ) ?></span>
					<?php endif ?>
				</div>
				<ul class="fl-pricing-table-features">
					<?php if ( ! empty( $pricing_column->features ) ) foreach ( $pricing_column->features as $feature ) : ?>
					<li><?php echo trim( $feature ); // xss ok ?></li>
					<?php endforeach; ?>
				</ul>

				<?php $module->render_button( $i ); ?>

				<br />

			</div>
		</div>
	</div>
	<?php

	endfor;

	?>
</div>
