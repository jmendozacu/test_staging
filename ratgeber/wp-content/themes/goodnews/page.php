<?php
/**
 * Setup Dynamic Sidebar
 */

$page_id = xt_get_page_ID();

list( $has_sidebar, $sidebar_position, $sidebar_area ) = xt_setup_dynamic_sidebar( $page_id );

$hide_page_title = (bool)get_field('hide_page_title', $page_id);
$page_container_no_padding = (bool)get_field('page_container_no_padding', $page_id);
$has_composer = xt_page_has_composer();
$special_page = xt_page_is_special();
$full_width_page = $has_composer;
$current_page_title = get_the_title();

get_header();

if ( have_posts() ) :
	while ( have_posts() ) : the_post();
?>
	

<div class="row<?php echo (!empty($full_width_page) ? ' full-width' : '')?><?php echo (($has_sidebar) ? ' has-sidebar' : ''); ?>">
 
	<div class="medium-<?php echo (($has_sidebar) ? '8' : '12'); ?>  column<?php echo (($sidebar_position === 'left') ? ' right' : ' left'); ?>">
		
		<?php if($special_page || $has_composer): ?>
		
			<?php the_content(); ?>
			
			<?php if(!$special_page): ?>
				<?php get_template_part('parts/items/comments'); ?>
			<?php endif; ?>	

		<?php else: ?>
			
			<article>
				<div class="article-content">
					<?php the_content(); ?>
				</div>
				<?php get_template_part('parts/items/comments'); ?>
			</article>
		
		<?php endif; ?>
		
		<div itemscope="" itemtype="http://schema.org/WebPage">
			<meta itemprop="name" content="<?php echo esc_attr($current_page_title);?>">
			<meta itemprop="description" content="<?php echo esc_attr(get_the_excerpt());?>">
		</div>
	</div>
	
	<?php if ( $has_sidebar ): ?>
	<div data-margin_top="50" data-margin_bottom="50" class="medium-4 column has-sticky-sidebar<?php echo (($sidebar_position === 'left') ? ' left' : ' right'); ?>">
		<?php xt_show_dynamic_sidebar($sidebar_area, 'page.php', 'sidebar', 'sidebar position-'.$sidebar_position); ?>
	</div>
	<?php endif; ?>
			
</div>	

<?php

	endwhile;
endif;

get_footer();