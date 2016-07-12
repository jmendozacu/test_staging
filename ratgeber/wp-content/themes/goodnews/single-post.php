<?php
get_header();

global $post;

if ( have_posts() ) 
	the_post();
	

$post_format = get_post_format();
$is_endless_template = xt_is_endless_template();
$sidebar_below_title = xt_is_sidebar_below_title($post->ID);


/**
 * Featured image settings
 */
 
list( $single_post_featured_image, $single_post_featured_image_position) = xt_get_featured_image_settings( $post->ID );

$behind_title_fw = $single_post_featured_image_position == 'behind-title-fullwidth';

if($behind_title_fw && !$sidebar_below_title) {
	$sidebar_below_title = true;
}

/**
 * Setup Dynamic Sidebar
 */

list( $has_sidebar, $sidebar_position, $sidebar_area ) = xt_setup_dynamic_sidebar( $post->ID );


if($post_format == 'video' || $post_format == 'gallery') {
	
	$widget_zone_type = $post_format;
	
}else{
	
	$widget_zone_type = 'post';
}


$post_settings = xt_get_single_settings('post');
extract($post_settings);

?>


<!-- Main Content -->
<div class="row<?php echo (($behind_title_fw) ? ' full-width' : ''); ?>">
			
	<div class="inner_content">
	
		<div class="row<?php echo (($has_sidebar) ? ' has-sidebar' : ''); ?>">
			
			<div itemscope="" itemtype="http://schema.org/BlogPosting">
			
				<?php if($sidebar_below_title && !$is_endless_template) : ?>
				<div class="medium-12">
					<article id="post-header-<?php the_ID(); ?>" class="article-header-above" data-postid="<?php echo esc_attr($post->ID); ?>" data-guid="<?php echo esc_attr(get_the_guid($post->ID)); ?>" data-permalink="<?php echo esc_url(get_permalink($post->ID)); ?>">
	
						<div class="row<?php echo (($behind_title_fw) ? ' collapse' : ''); ?>">
							<div class="medium-12 column">
						
							<?php include(locate_template('parts/items/post-header.php')); ?>
							
							</div>
						</div>
					</article>
						
				</div>
				<?php endif; ?>
	
		
				<?php if($behind_title_fw): ?>
				<div class="row vc_row in-container">
				<?php endif; ?>
										
				<div class="medium-<?php echo (($has_sidebar) ? '8' : '12'); ?> column<?php echo (($sidebar_position === 'left') ? ' right' : ' left'); ?>">
	
					<?php include(locate_template('parts/items/post-full.php')); ?>
					
					<!-- After Content Widget Zone-->
					
					<?php xt_show_custom_dynamic_sidebar('single_'.$widget_zone_type.'_after_content', 'single-post.php'); ?>
					
					<!-- End After Content Widget Zone-->	
	
				</div>
				<!-- End Main Content -->	
				
				<?php if ( $has_sidebar ) : ?>
		
				<div data-margin_top="50" data-margin_bottom="50" class="medium-4 column has-sticky-sidebar<?php echo (($sidebar_position === 'left') ? ' left' : ' right'); ?>">
					<?php xt_show_dynamic_sidebar($sidebar_area, 'single-post.php', 'sidebar', 'sidebar position-'.$sidebar_position); ?>
				</div>	
				
				<?php endif; ?>
				
								
				<?php if($behind_title_fw): ?>
				</div>
				<?php endif; ?>
				
			</div>
		</div>
		
			
		<!-- Post Bottom Widget Zone-->
		
		<?php xt_show_custom_dynamic_sidebar('single_'.$widget_zone_type.'_bottom', 'single-post.php', true); ?>
		
		<!-- Post Bottom Widget Zone-->
			
				
	</div>

</div>



<?php

get_footer();