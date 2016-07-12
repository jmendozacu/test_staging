<!-- Artcle Header -->		
<div class="article-header <?php echo esc_attr($single_post_featured_image_position);?>">

	<?php if($single_post_featured_image_position == 'above-title'): ?>
		<?php xt_post_featured_media();?>
	<?php endif; ?>
	
	<div class="meta">
		
		<?php if(($single_post_featured_image_position == 'behind-title' || $single_post_featured_image_position == 'behind-title-fullwidth')): ?>
		
			<?php 
			$asBackground = false;
			if($single_post_featured_image_position == 'behind-title-fullwidth')
				$asBackground = true;
			?>
			<div class="featured-image-behind-title">
				<?php xt_post_featured_media(true, $asBackground);?>
			</div>
			
		<?php else: ?>
		
			<?php xt_post_title('h1');?>
		
		<?php endif; ?>
		
		<?php if($single_post_featured_image_position == 'below-title'): ?>
			<?php xt_post_featured_media();?>
		<?php endif; ?>
	
		<?php if(($single_post_featured_image_position != 'behind-title' && $single_post_featured_image_position != 'behind-title-fullwidth') && $show_post_excerpt): ?>
			<?php xt_post_excerpt('h3', 'subheader');?>
		<?php endif; ?>
		
		<?php if($single_post_featured_image_position == 'below-excerpt'): ?>
			<br><?php xt_post_featured_media();?>
		<?php endif; ?>
		
		
		<div class="row in-container<?php echo ($single_post_featured_image_position != 'behind-title-fullwidth') ? ' collapse' : ''; ?>">
			<div class="xsmall-12 small-7 column">
				
				<span>
				<?php if($show_post_author): ?>
					<span class="<?php echo (xt_smart_sidebar_has('post-author') ? 'show-for-small' : ''); ?>">
						<?php xt_post_author();?>
					</span>
				<?php endif; ?>
				
			
				<?php if( $show_post_date): ?>
					<span class="<?php echo (xt_smart_sidebar_has('post-date') ? 'show-for-small' : ''); ?>">
						<?php xt_post_date();?>
					</span>	
				<?php endif ;?>
				</span>
				
			</div>
			<div class="xsmall-12 small-5 column">	
				
				<?php if($show_post_stats): ?>
					<div class="<?php echo (xt_smart_sidebar_has('post-stats') ? 'show-for-small' : ''); ?>">
						<?php xt_post_stats(true, array('mini','right left-for-xmall-only'));?>
					</div>	
				<?php endif ;?>
				
			</div>
		</div>	
		
		
	</div>

</div>

<!-- End Artcle Header -->	
