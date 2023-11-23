<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
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
		<?php foreach ( $notices as $notice ) : ?>
			<div class="woocommerce-message"<?php echo wc_get_notice_data_attr( $notice ); ?> role="alert">
				<?php echo wc_kses_notice( $notice['notice'] ); ?>
			</div>
		<?php endforeach; ?>
<?php if ( class_exists( 'Vamtam_Elements_B' ) && ! Vamtam_Elements_B::had_limit_wrapper() ) :  ?>
	</div>
<?php endif ?>
