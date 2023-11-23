<?php
/**
 * Sortable portfolio filter header
 *
 * @package vamtam/mozo
 */
?>
<nav class="portfolio-filters clearfix" data-for="#<?php echo esc_attr( $main_id ) ?>">
	<div class="cbp-l-filters-alignCenter cbp-l-filters-dropdown" id="<?php echo esc_attr( $main_id ) ?>-filters">
		<div class="cbp-l-filters-dropdownWrap">
			<div class="cbp-l-filters-dropdownHeader"><?php esc_html_e( 'All', 'mozo' ) ?></div>
			<div class="cbp-l-filters-dropdownList">
				<span class="inner-wrapper">
					<span data-filter="*" class="cbp-filter-item-active cbp-filter-item"><?php esc_html_e( 'All', 'mozo' )?> <span class="cbp-filter-counter"></span></span>
					<?php
						// show the categories present in this listing
						$terms = array();
						$key = 'tax_' . Jetpack_Portfolio::CUSTOM_POST_TYPE . '_' . Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE;
						if ( ! empty( $settings->$key ) ) {
							foreach ( explode( ',', $settings->$key ) as $term_slug ) {
								$term = get_term_by( 'id', $term_slug, 'jetpack-portfolio-type' );

								if ( $term ) {
									$terms[] = $term;
								}
							}
						} else {
							$terms = get_terms( 'jetpack-portfolio-type', 'hide_empty=1' );
						}
					?>
					<?php foreach ( $terms as $term ) :  ?>
						<?php $filter = '[data-type~="' . esc_attr( preg_replace( '/[\pZ\pC]+/u', '-', $term->slug ) ) . '"]'; ?>
						<span data-filter="<?php echo esc_attr( $filter ) ?>" class="cbp-filter-item"><span data-text="<?php echo esc_attr( $term->name ) ?>"><?php echo esc_html( $term->name ) ?> <span class="cbp-filter-counter"></span></span></span>
					<?php endforeach ?>
				</span>
			</div>
		</div>
	</div>

	<?php if ( $settings->title_filter ) : ?>
		<div class="cbp-search">
			<input id="<?php echo esc_attr( $main_id ) ?>-search" type="text" placeholder="<?php esc_attr_e( 'Filter by title', 'mozo' ) ?>" autocomplete="off" data-search=".project-title" class="cbp-search-input">
			<div class="cbp-search-icon"></div>
			<div class="cbp-search-nothing"><?php echo wp_kses( __( 'No projects matching <i>{{query}}</i>', 'mozo' ), [ 'i' => [] ] ); ?></div>
		</div>
	<?php endif ?>
</nav>

