<?php
/**
 * Site sub-header. Includes a slider, page title, etc.
 *
 * @package vamtam/mozo
 */

$page_title = VamtamFramework::get( 'page_title', null );

if ( ! is_404() ) {
	if ( vamtam_has_woocommerce() ) {
		if ( is_woocommerce() && ! is_single() ) {
			if ( is_product_category() ) {
				$page_title = single_cat_title( '', false );
			} elseif ( is_product_tag() ) {
				$page_title = single_tag_title( '', false );
			} else {
				$page_title = wc_get_page_id( 'shop' ) ? get_the_title( wc_get_page_id( 'shop' ) ) : '';
			}
		} elseif ( is_cart() || is_checkout() ) {
			$cart_id     = wc_get_page_id( 'cart' );
			$checkout_id = wc_get_page_id( 'checkout' );

			$cart_title     = get_the_title( $cart_id );
			$checkout_title = get_the_title( $checkout_id );
			$complete_title = esc_html__( 'Order Complete', 'mozo' );

			if ( is_cart() ) {
				$checkout_title = '<a href="' . esc_url( get_permalink( $checkout_id ) ) . '" title="' . esc_attr( $checkout_title ) . '">' . $checkout_title . '</a>';
			} else {
				$cart_title = '<a href="' . esc_url( get_permalink( $cart_id ) ) . '" title="' . esc_attr( $cart_title ) . '">' . $cart_title . '</a>';
			}

			$cart_state     = is_cart() ? 'active' : 'inactive';
			$checkout_state = is_checkout() && ! is_order_received_page() ? 'active' : 'inactive';
			$complete_state = is_order_received_page() ? 'active' : 'inactive';

			$page_title = "
				<span class='checkout-breadcrumb'>
					<span class='title-part-{$cart_state}'>$cart_title</span>" .
					vamtam_get_icon_html( array(
						'name' => 'vamtam-theme-arrow-right-sample',
					) ) .
					"<span class='title-part-{$checkout_state}'>$checkout_title</span>" .
					vamtam_get_icon_html( array(
						'name' => 'vamtam-theme-arrow-right-sample',
					) ) .
					"<span class='title-part-{$complete_state}'>$complete_title</span>
				</span>
			";
		}
	}
}

$sub_header_class = array( 'layout-' . VamtamTemplates::get_layout() );

$page_header_bg = VamtamTemplates::page_header_background();

$sub_header_bg_image = vamtam_post_meta( null, 'local-page-title-background-image', true );

// $has_header_bg should be true for non-transparent backgrounds
$sub_header_bg_str = str_replace(
	'background-color:transparent;background-image:none;',
	'',
	$page_header_bg . $sub_header_bg_image . vamtam_get_option( 'page-title-background', 'background-color'
) );

if ( ! empty( $sub_header_bg_str ) ) {
	$sub_header_class[] = 'has-background';
}

if ( ! empty( $sub_header_bg_image ) ) {
	$sub_header_class[] = 'has-background-image';
}

if ( ! VamtamTemplates::has_page_header() || is_404() ) return;
if ( is_page_template( 'page-blank.php' ) ) return;

$has_text_shadow = is_singular( VamtamFramework::$complex_layout ) && vamtam_sanitize_bool( get_post_meta( get_the_ID(), 'has-page-title-shadow', true ) );

if ( $has_text_shadow ) {
	$sub_header_class[] = 'has-text-shadow';
}

$hide_lowres_bg = vamtam_get_optionb( 'page-title-background-hide-lowres' ) ? 'vamtam-hide-bg-lowres' : '';

?>
<div id="sub-header" class="<?php echo esc_attr( implode( ' ', $sub_header_class ) ) ?>">
	<div class="meta-header <?php echo esc_attr( $hide_lowres_bg ) ?>" style="<?php echo esc_attr( $page_header_bg ) ?>">
		<?php if ( $has_text_shadow ) : ?>
			<div class="text-shadow"> </div>
		<?php endif ?>
		<div class="limit-wrapper vamtam-box-outer-padding">
			<div class="meta-header-inside">
				<?php
					VamtamTemplates::page_header( false, $page_title );
				?>
			</div>
		</div>
	</div>
</div>

