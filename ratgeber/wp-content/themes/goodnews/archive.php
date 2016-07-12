<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Good News
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Good_News
 * @since Good News 1.0
 */

global $has_sidebar, $sidebar_position, $post_settings, $current_page_title, $hide_page_title, $page_container_no_padding;
$is_ajax = !empty($_POST["ajax"]) ? true : false;

if($is_ajax) {

	include(locate_template('archive-ajax.php'));
	
}else{
	
	include(locate_template('archive-settings.php'));
	$current_page_title = $archive_title;
	$hide_page_title = isset($hide_page_title) ? $hide_page_title : false;
	$page_container_no_padding = isset($page_container_no_padding) ? $page_container_no_padding : false;
	get_header();	
?>


<div class="row<?php echo ($full_width_page ? ' full-width' : '')?>">
	
	<!-- Main Content -->	
	<div class="small-12">
	
		<?php if(!empty($archive_content) && get_query_var('paged') <= 1): ?>
		<div class="row">
			<div class="small-12 column">
				
				<div class="page-content">
					<?php echo $archive_content; ?>
				</div>
				
			</div>
		</div>		
		<?php endif; ?>
					
		<div class="row in-container<?php echo (($has_sidebar) ? ' has-sidebar has-sidebar-'.$sidebar_position : ''); ?>">
							
			<!-- Main Content -->	
			<div class="small-12 medium-<?php echo (($has_sidebar) ? '8' : '12'); ?> large-<?php echo (($has_sidebar) ? '8' : '12'); ?> column<?php echo (($sidebar_position === 'left') ? ' right' : ' left'); ?>">
	
	
			<?php if ( have_posts() ) : ?>

				<div class="widget <?php echo esc_attr($parent_class);?>">
	
					<!-- Posts 3 col grid -->
	
					<?php
					if ( $paginate_method == 'paginate_more' ) :
					?>
					
						<<?php echo esc_attr($tag); ?> id="post-list" class="<?php echo esc_attr($class); ?>"></<?php echo esc_attr($tag); ?>>
					
					<?php
					else :
					?>
					
						<<?php echo esc_attr($tag); ?> id="post-list" class="<?php echo esc_attr($class); ?>">
					
						<?php
						while ( have_posts() ) : the_post();
				
							include(locate_template('parts/items/post-item.php'));
				
						endwhile;
						?>
					
						</<?php echo esc_attr($tag); ?>>
					
					<?php
					endif;
					
					if ( $paginate_method == 'paginate_more' ) :
		
						xt_loadmore_nav($attr, $load_more_class);
	
					elseif ( $paginate_method == 'paginate_links' ) :
			
						xt_full_paging_nav();
	
					else :
				
						xt_paging_nav();
				
					endif;
					?>
	
				</div>
	
	
			<?php else : ?>
	
				<?php
					// If no content, include the "No posts found" template.
					include(locate_template('parts/items/post-none.php'));
				?>
	
			<?php endif; ?>
		
			</div>
			<!-- End Main Content -->	
		
	
			<?php if ( $has_sidebar ) : ?>
	
			<div data-margin_top="50" data-margin_bottom="50" class="hide-for-small archive-sidebar medium-4 large-4 column has-sticky-sidebar<?php echo (($sidebar_position === 'left') ? ' left' : ' right'); ?>">
				<?php xt_show_dynamic_sidebar($sidebar_area, 'archive.php', 'sidebar', 'sidebar position-'.$sidebar_position); ?>
			</div>	
			
			<?php endif; ?>
			
			
		</div>	
	</div>	
</div>

	<?php
	get_footer();
}	
