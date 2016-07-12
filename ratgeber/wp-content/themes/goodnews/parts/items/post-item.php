<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Good_News
 * @since Good News 1.0
 */
 
global $item_template, $has_sidebar, $sidebar_position, $post_settings;

@extract($post_settings);

$has_thumb = has_post_thumbnail();

if($has_sidebar && $item_template != 'grid-1') {
	$thumb_size = "th-medium";
}else{
	$thumb_size = "th-large";
}
?>

<?php if($item_template == "list" || $item_template == "list-small"): ?>

	<?php
	
	if($has_sidebar && $sidebar_position === "left") {
	
		if($item_template == "list") {
		
			$col1_medium = 6;
			$col2_medium = 6;
			
			$col1_large = 6;
			$col2_large = 6;
			
		}else{
		
			$col1_medium = 4;
			$col2_medium = 8;
			
			$col1_large = 4;
			$col2_large = 8;
		}
			
	}else if($has_sidebar) {
	
		if($item_template == "list") {
		
			$col1_medium = 6;
			$col2_medium = 6;
			
			$col1_large = 6;
			$col2_large = 6;
			
		}else{
		
			$col1_medium = 4;
			$col2_medium = 8;
			
			$col1_large = 4;
			$col2_large = 8;
		}
			
	}else{
	
		if($item_template == "list") {
		
			$col1_medium = 5;
			$col2_medium = 7;
			
			$col1_large = 6;
			$col2_large = 6;
			
		}else{
		
			$col1_medium = 4;
			$col2_medium = 8;
			
			$col1_large = 4;
			$col2_large = 7;
		}
	
	}	
	?>
	
	<li id="post-<?php the_ID(); ?>" data-type="<?php echo esc_attr(get_post_type()); ?>" <?php post_class(); ?>>
		<div class="row <?php echo (!$has_thumb ? 'collapse' : ''); ?>">
			<?php if($has_thumb): ?>
			<div class="small-12 medium-<?php echo esc_attr($col1_medium);?> large-<?php echo esc_attr($col1_large);?> column first">
				<?php xt_post_thumbnail($thumb_size); ?>
			</div>
			<?php endif; ?>
			<div class="small-12 medium-<?php echo esc_attr($col2_medium);?> large-<?php echo esc_attr($col2_large);?> column last end">
				<div class="meta">
				
					<?php if($show_post_category): ?>
						<?php xt_post_category(); ?>
					<?php endif; ?>
					
					<?php xt_post_title('h3', 'bold'); ?>
					
					<?php if($show_post_excerpt): ?>
						<?php xt_post_excerpt('h4', 'spaced'); ?>
					<?php endif; ?>
					
					<?php if($show_post_author): ?>
						<?php xt_post_author();?>
					<?php endif; ?>
					
					<?php if($show_post_date): ?>
						<?php xt_post_date();?>
					<?php endif; ?>
						
					<?php if($show_post_stats): ?>	
						<?php xt_post_stats();?>
					<?php endif; ?>
				</div>	
			</div>	
		</div>
	</li>

<?php else: ?>

	<?php 
	$col = str_replace("grid-","",$item_template);

	if($col <= (($has_sidebar) ? 2 : 3)) {
		
		$title_heading = 'h3';
		$title_class = 'bold';
		
		$excerpt_heading = 'h4';
		$excerpt_class = 'spaced';
		
	}else if($col >= (($has_sidebar) ? 4 : 5)) {
		
		$title_heading = 'h5';
		$title_class = 'bold';
		
		$excerpt_heading = 'h6';
		$excerpt_class = '';
			
	}else{
		
		$title_heading = 'h4';
		$title_class = 'bold';
		
		$excerpt_heading = 'h5';
		$excerpt_class = '';
	}

	?>	
	<li id="post-<?php the_ID(); ?>" data-type="<?php echo esc_attr(get_post_type()); ?>" <?php post_class(); ?>>
		<div class="row <?php echo (!$has_thumb ? 'collapse' : ''); ?>">
			<?php if($has_thumb): ?>
			<div class="thumb">
				<?php xt_post_thumbnail($thumb_size); ?>
			</div>
			<?php endif; ?>
			<div class="meta">	
			
				<?php if($show_post_category): ?>
					<?php xt_post_category(); ?>
				<?php endif; ?>
	
				<?php xt_post_title($title_heading, $title_class); ?>
				
				<?php if($show_post_excerpt): ?>
					<?php xt_post_excerpt($excerpt_heading, $excerpt_class); ?>
				<?php endif; ?>
						
				<?php if($show_post_author): ?>
					<?php xt_post_author();?>
				<?php endif; ?>
				
				<?php if($show_post_date): ?>
					<?php xt_post_date();?>
				<?php endif; ?>
						
				<?php if($show_post_stats): ?>	
					<?php xt_post_stats();?>
				<?php endif; ?>
			</div>	
		</div>	
	</li>

<?php endif; ?>
