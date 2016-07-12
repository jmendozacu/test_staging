<?php global $post, $current_post, $is_active, $tab_post_author, $tab_post_date, $tab_post_category, $tab_post_stats; ?>

<li <?php echo ((!empty($current_post) && $current_post->ID == $post->ID) || $is_active) ? 'class="active"' : ''?>>
	<div class="active-border"></div>
	<a href="<?php echo esc_url(get_permalink($post->ID)); ?>" data-postid="<?php echo esc_attr($post->ID); ?>">
	
		<div class="meta">	
		<?php if(!empty($tab_post_category)): ?>
			<?php echo xt_post_category(false); ?>
		<?php endif; ?>	
		</div>
		
		<?php echo esc_html(get_the_title()); ?>
		
		<div class="meta">
		
		<?php if(!empty($tab_post_author)): ?>
			<?php echo xt_post_author(false); ?>
		<?php endif; ?>
		
		<?php if(!empty($tab_post_date)): ?>
			<?php echo xt_post_date(); ?>
		<?php endif; ?>	
		
		<?php if(!empty($tab_post_stats)): ?>
			<?php echo xt_post_stats(false, array('mini')); ?>
		<?php endif; ?>	
		
		</div>
	</a>	
</li>