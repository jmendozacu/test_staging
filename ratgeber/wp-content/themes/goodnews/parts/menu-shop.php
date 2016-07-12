<?php if(function_exists('is_shop') && function_exists('xt_woocommerce_get_cart_link')): ?>
	
	<?php 
	global $woocommerce; 
	$enable_cart_count = (int)xt_option('woo_header_enable_cart_count');
	$enable_cart_dropdown = (bool)xt_option('woo_header_enable_cart_dropdown');

	$cart_count = $woocommerce->cart->cart_contents_count;
	
	$cart_enabled = false;
	if($enable_cart_count > 0)
		$cart_enabled = true;

	?>
	<?php if ($cart_enabled): ?>
		
		<li class="flat canhover shopping-cart-menu show-for-medium-up <?php echo ($enable_cart_dropdown) ? 'has-dropdown' : '';?>">
			<?php echo xt_woocommerce_get_cart_link(); ?>
			<?php if($enable_cart_dropdown) : ?>
			<ul class="woo-cart-dropdown-wrap dropdown animate-drop open-top">
				<li>
					<div class="widget_shopping_cart_content">
						<?php woocommerce_get_template( 'cart/mini-cart.php' ); ?>
					</div>
				</li>
			</ul>	
			<?php endif; ?>
		</li>
		<li class="shopping-cart-menu show-for-small-only">
			<?php echo xt_woocommerce_get_cart_link(); ?>
		</li>
	
	<?php endif; ?>

<?php endif; ?>