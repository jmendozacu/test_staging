<?php
/**
* The Template for displaying product archives, including the main shop page which is a post type archive.
*
* Override this template by copying it to yourtheme/woocommerce/archive-product.php
*
* @author WooThemes
* @package WooCommerce/Templates
* @version 2.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


$is_ajax = !empty($_POST["ajax"]) ? true : false;
$shop_page_id = get_option('woocommerce_shop_page_id');
$hide_page_title = (bool)get_field('hide_page_title', $shop_page_id);
$page_container_no_padding = (bool)get_field('page_container_no_padding', $shop_page_id);
$full_width_page = xt_page_has_composer($shop_page_id);
$paginate_method = xt_option('paginate_method');
$attr[] = 'data-paginate="' . esc_attr($paginate_method) . '"';

$load_more_class = $paginate_method;

if ( $paginate_method == 'paginate_scroll' )
	$paginate_method = "paginate_more";


if($is_ajax) {
?>
					
	<?php
	while ( have_posts() ) : the_post();

		wc_get_template_part( 'content', 'product' );

	endwhile;
	?>

	<?php 
		$next_link = get_next_posts_link(__('More', 'woocommerce'),''); 
		echo str_replace('<a ', '<a data-rel="post-list" '.implode(' ', $attr).' class="button-more '.esc_attr($load_more_class).'" ', $next_link);
	?>


<?php
	
}else{
	
	$current_page_title = woocommerce_page_title(false);
	
	get_header( 'shop' );	

	list( $has_sidebar, $sidebar_position, $sidebar_area ) = xt_setup_dynamic_sidebar( $shop_page_id );
	
?>

	<div class="row in-container">
						
		<!-- Main Content -->	
		<div class="small-12 medium-<?php echo (($has_sidebar) ? '6' : '12'); ?> large-<?php echo (($has_sidebar) ? '8' : '12'); ?> column">

			<div class="row collapse">
				<div class="small-12 medium-12 column<?php echo (($sidebar_position === 'left') ? ' right' : ' left'); ?>">
	
	
				<?php if ( have_posts() ) : ?>

					
					<div class="woocontent">

						<?php do_action( 'woocommerce_archive_description' ); ?>
						
						<?php
						/**
						* woocommerce_before_shop_loop hook
						*
						* @hooked woocommerce_result_count - 20
						* @hooked woocommerce_catalog_ordering - 30
						*/
						do_action( 'woocommerce_before_shop_loop' );
						?>

						
						<?php
						if ( $paginate_method == 'paginate_more' ) :
						?>
						
							<?php woocommerce_product_loop_start(); ?><?php woocommerce_product_loop_end(); ?>
						
						<?php
						else :
						?>
						
							<?php woocommerce_product_loop_start(); ?>
					
							<?php woocommerce_product_subcategories(); ?>
						
							<?php
							while ( have_posts() ) : the_post();
				
							wc_get_template_part( 'content', 'product' );
				
							endwhile;
							?>
						
							<?php woocommerce_product_loop_end(); ?>
						
						<?php
						endif;
						?>
						
						<?php

						
						if ( $paginate_method == 'paginate_more' ) :
			
							xt_loadmore_nav($attr, $load_more_class);

						elseif ( $paginate_method == 'paginate_links' ) :
				
							xt_full_paging_nav();
		
						else :
					
							xt_paging_nav();
					
						endif;
						?>
						
						
						
					</div>


					<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
		
					<?php
						// If no content, include the "No posts found" template.
						wc_get_template( 'loop/no-products-found.php' ); 
					?>

				<?php endif; ?>
			
				</div>
			</div>
			
		</div>
		<!-- End Main Content -->	
	

		<?php if ( $has_sidebar ) : ?>

		<div data-margin_top="50" data-margin_bottom="50" class="hide-for-small medium-5 large-4 column has-sticky-sidebar<?php echo (($sidebar_position === 'left') ? ' left' : ' right'); ?>">
			<?php xt_show_dynamic_sidebar($sidebar_area, 'archive-product.php', 'sidebar', 'sidebar position-'.$sidebar_position); ?>
		</div>	
		
		<?php endif; ?>
		
		
	</div>	


	<?php
get_footer( 'shop' ); 

}	
