<?php
/**
* The Template for displaying all single products.
*
* Override this template by copying it to yourtheme/woocommerce/single-product.php
*
* @author WooThemes
* @package WooCommerce/Templates
* @version 1.6.4
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$hide_page_title = (bool)xt_option('woo_hide_single_product_page_title');
$current_page_title = woocommerce_page_title(false);

/**
 * Setup Dynamic Sidebar
 */

list( $has_sidebar, $sidebar_position, $sidebar_area ) = xt_setup_dynamic_sidebar( $post->ID );



get_header( 'shop' ); 
?>
<?php
/**
* woocommerce_before_main_content hook
*
* @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
* @hooked woocommerce_breadcrumb - 20
*/
do_action( 'woocommerce_before_main_content' );
?>

	
	<div class="row">
		<div class="small-12 medium-<?php echo (($has_sidebar) ? '8' : '12'); ?> column<?php echo (($sidebar_position === 'left') ? ' right' : ' left'); ?>">

			<div class="woocontent">
				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'single-product' ); ?>
			
				<?php endwhile; // end of the loop. ?>
			
				<?php
				/**
				* woocommerce_after_main_content hook
				*
				* @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
				*/
				do_action( 'woocommerce_after_main_content' );
				?>
			
				<?php
				/**
				* woocommerce_sidebar hook
				*
				* @hooked woocommerce_get_sidebar - 10
				*/
				//do_action( 'woocommerce_sidebar' );
				?>
			</div>
			
		</div>

		<?php if ( $has_sidebar ) : ?>

		<div data-margin_top="50" data-margin_bottom="50" class="medium-4 column has-sticky-sidebar<?php echo (($sidebar_position === 'left') ? ' left' : ' right'); ?>">
			<?php xt_show_dynamic_sidebar($sidebar_area, 'single-post.php', 'sidebar', 'sidebar position-'.$sidebar_position); ?>
		</div>	
		
		<?php endif; ?>
								
	</div>
	
<?php get_footer( 'shop' ); ?>