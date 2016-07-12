<?php
global $withcomments;

$withcomments = true; 

$is_endless_template = xt_is_endless_template();

$comments_enabled = xt_comments_enabled();
$comments_system = xt_comments_system();
$has_composer = xt_page_has_composer();

if($comments_enabled) {
?>

	<?php if(!empty($has_composer)): ?>
	<div class="vc_row wpb_row vc_row-fluid in-container">
		<div class="vc_col-sm-12 vc_col-md-12 vc_col-xs-12 wpb_column vc_column_container">
			<article>
				<div class="article-content">
	<?php endif; ?>							
			
								
		<div class="comments-top" style="width:100%; height:1px;"></div>
		
		<div id="comments_<?php the_ID(); ?>">
		
		<?php
		if($comments_system == 'facebook') {
		?>
			
			<h3 class="comment-reply-title"><?php echo xt_option('comments_title_reply'); ?></h3>	
			<div class="fb-comments article-comments post-<?php the_ID(); ?>" data-href="<?php echo esc_url(get_permalink()); ?>" data-numposts="5" data-colorscheme="light"></div>
		
		<?php	
		}else{
		?>
		
			<?php if($comments_system == 'disqus'): ?>
	
				<?php if($is_endless_template): ?>
				
					<a class="button toggle comments-toggle" data-toggle=".article-comments.post-<?php the_ID(); ?>" data-callback="loadDisqus"><i class="fa fa-comment"></i> <?php echo __(xt_option('comments_title_reply'), XT_TEXT_DOMAIN); ?> (<?php echo (get_comments_number(get_the_ID())); ?>)</a>
					
					<div class="article-comments post-<?php the_ID(); ?> fade" style="display:none">
						<h3 class="comment-reply-title"><?php echo __(xt_option('comments_title_reply'), XT_TEXT_DOMAIN); ?></h3>									
						<div class="discus-wrap"></div>
					</div>
					
				<?php else: ?>
					
					<h3 class="comment-reply-title"><?php echo __(xt_option('comments_title_reply'), XT_TEXT_DOMAIN); ?></h3>									
					<div class="article-comments post-<?php the_ID(); ?>">
				
						<?php comments_template(); ?>
	
					</div>
					
				<?php endif; ?>
				
												
			<?php else: ?>
			
				<?php if($is_endless_template): ?>
				
					<a class="button toggle comments-toggle" data-toggle=".article-comments.post-<?php the_ID(); ?>" data-callback="showComments"><i class="fa fa-comment"></i> <?php echo __(xt_option('comments_title_reply'), XT_TEXT_DOMAIN); ?> (<?php echo (get_comments_number(get_the_ID())); ?>)</a>
					
					<div class="article-comments post-<?php the_ID(); ?> fade" style="display:none">
						<?php comments_template(); ?>
					</div>
					
				<?php else: ?>
					
					<div class="article-comments post-<?php the_ID(); ?>">
					
						<?php comments_template(); ?>
	
					</div>
					
				<?php endif; ?>
				
				
				
			<?php endif; ?>
				
		</div>
			
		<?php
		}
	?>
		
	<?php if(!empty($has_composer)): ?>
				</div>
			</article>
		</div>
	</div>
	<?php endif; ?>	

<?php	
}	
?>