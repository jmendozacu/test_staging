<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}

$info_message = apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>' );
wc_print_notice( $info_message, 'notice' );
?>

<form class="checkout_coupon coupon box-grey" method="post" style="display:none">

	<h3 for="coupon_code"><?php _e( 'Promotional Code', 'woocommerce' ); ?>:</h3> 
	<div class="row">
		<div class="medium-8 column">
			<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Enter your promotional code here', 'woocommerce' ); ?>" /> 
		</div>
		<div class="medium-4 column">
			<input type="submit" class="button secondary" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />
		</div>
	</div>	

</form>
