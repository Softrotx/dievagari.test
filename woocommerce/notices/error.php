<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $notices ) {
	return;
}

?>
<?php if ( class_exists( 'Vamtam_Elements_B' ) && ! Vamtam_Elements_B::had_limit_wrapper() ) :  ?>
	<div class="limit-wrapper vamtam-box-outer-padding">
<?php endif ?>
		<ul class="woocommerce-error">
			<?php foreach ( $notices as $notice ) : ?>
				<li<?php echo wc_get_notice_data_attr( $notice ); ?>>
					<?php echo wc_kses_notice( $notice['notice'] ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
<?php if ( class_exists( 'Vamtam_Elements_B' ) && ! Vamtam_Elements_B::had_limit_wrapper() ) :  ?>
	</div>
<?php endif ?>