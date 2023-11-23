<?php
	$show_if_empty = vamtam_get_option( 'show-empty-header-cart' );
?>
<div class="cart-dropdown vamtam-header-cart-wrapper <?php echo esc_attr( $show_if_empty ? 'show-if-empty' : 'hidden' ) ?>">
	<div class="cart-dropdown-inner">
		<a class="vamtam-cart-dropdown-link" href="<?php echo esc_url( vamtam_wc_get_cart_url() ) ?>">
			<span class="icon theme"><?php vamtam_icon( 'basket-thin' ) ?></span>
			<span class="products cart-empty">...</span>
		</a>
		<div class="widget woocommerce widget_shopping_cart">
			<div class="widget_shopping_cart_content"></div>
		</div>
	</div>
</div>
