<?php 
global $post, $sidebar_below_title, $is_endless_template;


$post_format = get_post_format();

$is_endless_template = xt_is_endless_template();
$sidebar_below_title = xt_is_sidebar_below_title($post->ID);

//Featured image settings
list( $single_post_featured_image, $single_post_featured_image_position) = xt_get_featured_image_settings( $post->ID );

$behind_title_fw = $single_post_featured_image_position == 'behind-title-fullwidth';

if($behind_title_fw && !$sidebar_below_title) {
	$sidebar_below_title = true;
}


// Post settings
$post_settings = xt_get_single_settings('post');
extract($post_settings);

// Smart Sidebar
$smart_sidebar_position = xt_smart_sidebar_position();
$smart_sidebar_enabled = $smart_sidebar_position !== false;
?>

<!--Single Article -->	
	
<article id="post-<?php the_ID(); ?>" data-postid="<?php echo esc_attr($post->ID); ?>" data-guid="<?php echo esc_attr(get_the_guid($post->ID)); ?>" data-permalink="<?php echo esc_url(get_permalink($post->ID)); ?>">

	<?php if(!$sidebar_below_title || $is_endless_template): ?>
	<div class="row collapse">
		<div class="medium-12 column">
			<?php include(locate_template('parts/items/post-header.php')); ?>
		</div>
	</div>
	<?php endif; ?>
		
	<!-- Artcle Content -->	
	<div class="row collapse article-content-wrap">
		
		<div class="article-start"></div>
		
		<?php 
		$cols = array(12,12);
		if($smart_sidebar_enabled): 
		
			$cols = array(3,9);
		?>
		<div class="has-sticky-sidebar medium-<?php echo esc_attr($cols[0]); ?> column hide-for-small <?php echo ($smart_sidebar_position == 'left') ? 'left' : 'right';?>" data-margin_top="50" data-margin_bottom="50">	
			<div class="b-padding-10 <?php echo ($smart_sidebar_position == 'left') ? 'r-padding-30' : 'l-padding-30';?>">
				<?php 
				xt_smart_sidebar();
				?>
			</div>	
		</div>
		<?php endif; ?>
		<div class="small-12 <?php if($smart_sidebar_enabled): ?>medium-<?php echo esc_attr($cols[1]); ?> right<?php endif;?> column">		
			
			<?php if(get_the_content()): ?>
			<div class="article-content">

				<?php if(($single_post_featured_image_position == 'behind-title' || $single_post_featured_image_position == 'behind-title-fullwidth') && $show_post_excerpt): ?>
					<?php xt_post_excerpt('h3', 'subheader image-behind-title');?>
				<?php endif; ?>

				<?php if($single_post_featured_image_position == 'above-content'): ?>
					<?php xt_post_featured_media();?>
				<?php endif; ?>

				<?php xt_post_content(); ?>
				
				<div class="<?php echo (xt_smart_sidebar_has('social-share') ? 'show-for-small' : ''); ?>">
				<?php
				$xtss_box = xt_socialshare(); 
				if($xtss_box) {
					 echo $xtss_box; 
				}
				?>
				</div>
			

				<?php 
				xt_post_pages();
				?>
			
				
				<?php if($show_post_categories || $show_post_tags): ?>						
					
					<?php
					$categories_list = false;
					if($show_post_categories)
						$categories_list = get_the_category_list( '</li><li itemprop="articleSection">' );
						
					$tag_list = false;	
					if($show_post_tags)	
						$tag_list = get_the_tag_list('<li itemprop="keywords">','</li><li itemprop="keywords">','</li>');
					
					if ( $categories_list || $tag_list) {
						echo '<div class="article-categories"><h5>' .  __('In this article', XT_TEXT_DOMAIN) .'</h5>' . '<ul><li>'.($categories_list ? $categories_list : '').'</li>'.($tag_list ? $tag_list : '').'</ul></div>';
					}
					?>
						
				<?php endif; ?>
	
				<?php
				$show_post_author_bio = (bool)xt_option('show_post_author_bio', null, true);
				?>
				<?php if($show_post_author_bio && !$is_endless_template): ?>
					<?php get_template_part('parts/items/post', 'author'); ?>	
				<?php endif; ?>

			</div>
		
			<?php get_template_part('parts/items/comments'); ?>
			
			<?php endif; ?>
		</div>
	</div>
	<!-- End Artcle Content -->
	
	<?php if($is_endless_template): ?>
	<div class="small-12  column"><hr></div>
	<?php endif; ?>
	
	<div class="article-end"></div>
	
</article>
<!--End Single Article -->	
		