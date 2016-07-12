<?php get_header(); ?>

<?php 

$title = xt_option('404_page_title'); 
$content = xt_option('404_page_content'); 
	
?>

<div class="row">
	<div class="medium-6 small-centered column">

		<?php if(!empty($title)): ?>
		<h1>
			<?php echo wp_kses_post($title); ?>					
		</h1>
		<?php endif; ?>
		
		<div class="page-content t-padding">
			<?php echo wp_kses_post($content); ?>					
		</div>
		
		<?php get_search_form(); ?>
		
		<div class="title_divider">
		  <span><?php _e("Or", XT_TEXT_DOMAIN); ?></span>
		</div>

		<a class="button" href="<?php echo home_url("/"); ?>"><?php _e("Go To Homepage", XT_TEXT_DOMAIN); ?></a>
			
	</div>
</div>	


<?php get_footer(); ?>