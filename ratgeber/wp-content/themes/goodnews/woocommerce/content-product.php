<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;


?>
<li <?php post_class(); ?> data-equalizer-watch>
	<div class="product-wrap radius">
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	
		<a href="<?php the_permalink(); ?>">
	
			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
		</a>	
		<div class="product-info">
			<?php 
			woocommerce_template_loop_rating();
			?>
	
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	
			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				woocommerce_template_loop_price();
				
			?>
	
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
	
		</div>
	</div>
</li>