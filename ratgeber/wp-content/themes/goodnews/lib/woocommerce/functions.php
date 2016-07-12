<?php

function xt_woocommerce_get_cart_link($class="") {
	
	global $woocommerce;
	
	$enable_cart_count = (int)xt_option('woo_header_enable_cart_count');
	$cart_title = xt_option('woo_header_cart_menu_title');
	if(!empty($cart_title)) {
		$cart_title .= ' &nbsp;';
	}else{
		$cart_title = "";
	}
	
	$cart_url = ($woocommerce->cart->get_cart_url() != '') ? $woocommerce->cart->get_cart_url() : '#';	
	$cart_count = $woocommerce->cart->cart_contents_count; 
	
	$cart_hidden = false;
	if(($enable_cart_count == 0) || ($enable_cart_count == 2 && $cart_count == 0)) {
		$cart_hidden = true;
	}
		
	if(!empty($cart_title)) {
		$cart_count = "(".$cart_count.")";
	}
	
	ob_start();
	?>
	<a class="cart-contents <?php echo esc_attr($class);?>" <?php echo ($cart_hidden ? 'style="display:none"' : ''); ?> href="<?php echo esc_url($cart_url); ?>" title="<?php _e('View your shopping cart', XT_TEXT_DOMAIN); ?>"><span class="fa fa-shopping-cart"></span>&nbsp; <?php echo wp_kses_post($cart_title); ?><?php echo esc_html($cart_count); ?></a>
	<?php
	$link = ob_get_clean();
	return $link;	
}

/**
* xt_is_woocommerce_page - Returns true if on a page which uses WooCommerce templates (cart and checkout are standard pages with shortcodes and which are also included)
*
* @access public
* @return bool
*/
function xt_is_woocommerce_page () {
        if(  function_exists ( "is_woocommerce" ) && is_woocommerce()){
                return true;
        }
        $woocommerce_keys   =   array ( "woocommerce_shop_page_id" ,
                                        "woocommerce_terms_page_id" ,
                                        "woocommerce_cart_page_id" ,
                                        "woocommerce_checkout_page_id" ,
                                        "woocommerce_pay_page_id" ,
                                        "woocommerce_thanks_page_id" ,
                                        "woocommerce_myaccount_page_id" ,
                                        "woocommerce_edit_address_page_id" ,
                                        "woocommerce_view_order_page_id" ,
                                        "woocommerce_change_password_page_id" ,
                                        "woocommerce_logout_page_id" ,
                                        "woocommerce_lost_password_page_id" ) ;
        foreach ( $woocommerce_keys as $wc_page_id ) {
                if ( get_the_ID () == get_option ( $wc_page_id , 0 ) ) {
                        return true ;
                }
        }
        return false;
}