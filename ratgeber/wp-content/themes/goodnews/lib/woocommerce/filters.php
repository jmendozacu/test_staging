<?php

remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);
remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_rating', 10);
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 15 );
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);


function xt_wc_add_to_cart_message($message, $product_id = null) {
	
	$message = '<i class="fa fa-check"></i> &nbsp; '.$message;
	
	return $message;
}
add_filter('wc_add_to_cart_message', 'xt_wc_add_to_cart_message');


function xt_image_size_shop_catalog( $size ){

	$size['width'] = '240';
    $size['height'] = '240';
 
    return $size;
 
} 
add_filter( 'woocommerce_get_image_size_shop_catalog', 'xt_image_size_shop_catalog' );


function xt_image_size_shop_single( $size ){

	$size['width'] = '545';
    $size['height'] = '545';
 
    return $size;
 
} 
add_filter( 'woocommerce_get_image_size_shop_single', 'xt_image_size_shop_single' );


function xt_woocommerce_header_add_to_cart_fragment( $fragments ) {

	$fragments['a.cart-contents'] = xt_woocommerce_get_cart_link();
	
	return $fragments;
	
}

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'xt_woocommerce_header_add_to_cart_fragment');


 
function xt_woocommerce_billing_fields( $fields ) {
 
     $fields['billing_address_1']['class'] = array( 'form-row-first' );
     $fields['billing_address_2']['class'] = array( 'form-row-last' );
     $fields['billing_address_2']['label'] = '&nbsp;';
 
     return $fields;
}
add_filter('woocommerce_billing_fields', 'xt_woocommerce_billing_fields');


function xt_woocommerce_product_has_gallery( $classes ) {
	global $product;

	$post_type = get_post_type( get_the_ID() );

	if ( ! is_admin() ) {

		if ( $post_type == 'product' ) {

			$attachment_ids = $product->get_gallery_attachment_ids();

			if ( $attachment_ids ) {
				$classes[] = 'has-gallery';
			}
		}

	}

	return $classes;
}
add_filter( 'post_class', 'xt_woocommerce_product_has_gallery' );


// Display the second thumbnails
function xt_woocommerce_template_loop_second_product_thumbnail() {
	global $product, $woocommerce;

	$attachment_ids = $product->get_gallery_attachment_ids();

	if ( $attachment_ids ) {
		$secondary_image_id = $attachment_ids['0'];
		echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'secondary-image attachment-shop-catalog' ) );
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'xt_woocommerce_template_loop_second_product_thumbnail', 11 );



function xt_woocommerce_login_redirect()
{
    if( is_account_page() && !is_user_logged_in() )
    {
	    global $wp;
		if ( is_page( wc_get_page_id( 'myaccount' ) ) && isset( $wp->query_vars['lost-password'] ) )
			return false;

    
    	$pages = get_posts(array(
        	'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'tpl-login.php'
		));
	
		if(!empty($pages)) {
		
			foreach($pages as $page) {
				$login_url = get_permalink($page->ID);
				$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
				if ( $myaccount_page_id ) {
				  $myaccount_page_url = get_permalink( $myaccount_page_id );
				  $login_url = add_query_arg( array('redirect' => urlencode($myaccount_page_url)), $login_url );
				}

				wp_redirect( $login_url );
				exit();
			}	
		}
	
        
    }
}
add_action( 'template_redirect', 'xt_woocommerce_login_redirect' );


function xt_woocommerce_order_button_html() {
	
	return '<input type="submit" class="button" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( __('Place Order', XT_TEXT_DOMAIN) ) . '" data-value="' . esc_attr( __('Place Order', XT_TEXT_DOMAIN) ) . '" />';
	
}
add_filter( 'woocommerce_order_button_html', 'xt_woocommerce_order_button_html'); 
