<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<div style="display:none"><?php wc_print_notices();?></div>

<div class="mini-cart">
	<div class="spinner">
		<div class="wait mini">
			<div class="double-bounce1"></div>
			<div class="double-bounce2"></div>
		</div>
	</div>
	<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>	
		
		<div class="cart-total-count">
			<?php echo sprintf(__("You have <strong>%d items</strong> in your cart", 'woocommerce'), WC()->cart->get_cart_contents_count() ); ?>
		</div>	
		
		<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">
			<ul class="cart-list <?php echo !empty($args['list_class']) ? $args['list_class'] : ''; ?>">
				
			<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	
					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
	
						$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
	
						?>
						<li>
							<a href="<?php echo esc_url(get_permalink( $product_id )); ?>">
								<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) ?>
							</a>
							<div class="product-meta">
								<div class="product-title"><a href="<?php echo esc_url(get_permalink( $product_id )); ?>"><?php echo esc_html($product_name); ?></a></div>
								
								<?php //echo WC()->cart->get_item_data( $cart_item ); ?>
	
								<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input( array(
											'input_name'  => "cart[{$cart_item_key}][qty]",
											'input_value' => $cart_item['quantity'],
											'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
										), $_product, false );
									}
		
									echo apply_filters( 'woocommerce_widget_cart_item_quantity', $product_quantity, $cart_item_key );
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
								?>
						
							</div>
						</li>
						<?php
					}
				}
			?>
		
			</ul><!-- end product list -->
		
			<div class="cart-total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></div>
		
			<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
		
			<div class="cart-buttons">
				<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="button checkout"><?php _e( 'Checkout', 'woocommerce' ); ?> <i class="fa-long-arrow-right append"></i></a>
				<a href="<?php echo WC()->cart->get_cart_url(); ?>" class="button secondary"><?php _e( 'View Cart', 'woocommerce' ); ?> <i class="fa-long-arrow-right append"></i></a>
			</div>
		
			<input type="hidden" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" /> 
			<?php wp_nonce_field( 'woocommerce-cart' ); ?>
		
		</form>
	
		
	<?php else : ?>

		<div class="empty">
			<?php _e( 'No products in the cart.', 'woocommerce' ); ?>
			<span class="fa fa-shopping-cart"></span>
		</div>

	<?php endif; ?>	
		
		
</div>	

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
