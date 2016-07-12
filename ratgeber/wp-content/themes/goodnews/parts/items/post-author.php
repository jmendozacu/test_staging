<?php 
$author_bio = get_the_author_meta( 'description', $post->post_author ); 
	
if(!empty($author_bio)):	
?> 
<div class="about-author" itemprop="author" itemscope="" itemtype="http://schema.org/Person">

	<?php if(is_author()): ?>
	
		<div class="author-avatar">
			<?php echo (get_avatar( $post->post_author, 150 )); ?>
		</div>
	
	<?php else: ?>
	
		<a class="author-avatar th" href="<?php echo get_author_posts_url($post->post_author); ?>">
			<?php echo (get_avatar( $post->post_author, 300 )); ?>
		</a>
	
	<?php endif; ?>
	
	<div class="author-meta">
	
		<?php if(is_author()): ?>
		
			<div class="author-name" itemprop="name"><?php echo get_the_author(); ?></div>

		<?php else: ?>
		
			<span itemprop="name"><a class="author-name" itemprop="url" href="<?php echo esc_url(get_author_posts_url($post->post_author)); ?>"><?php echo get_the_author(); ?></a></span>
		
		<?php endif; ?>
		
		<p class="author-bio" itemprop="description"><?php echo wp_kses_post($author_bio); ?> </p>
	</div>

</div>
<?php endif; ?>