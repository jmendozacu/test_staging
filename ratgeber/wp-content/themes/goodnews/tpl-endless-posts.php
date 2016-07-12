<?php
/**
 * Template Name: Endless Posts
 */

global $post;
$backup_post = $post;

//$hide_page_title = (bool)get_field('hide_page_title', $post->ID);
$page_container_no_padding = (bool)get_field('page_container_no_padding', $post->ID);
//$current_page_title = get_the_title();

$stroll_animation = get_field('endless_animation');
$update_state = get_field('update_state');
$endless_tabs = get_field('endless_tabs');

$tab_posts = array();

$is_endless_template = true;

get_header();
?>

	
	<!-- Main Content -->
	<div class="row" id="endless-articles-wrapper" data-postid="<?php echo esc_attr($post->ID);?>" data-updatestate="<?php echo esc_attr($update_state);?>">

		<div class="wait">
			<div class="double-bounce1"></div>
			<div class="double-bounce2"></div>
		</div>
	
		<!-- Sidebar-->
		<div class="medium-4 column">
				
			<div class="sidebar border-left border-right">
			
				<aside class="widget xt_tabs">
			
					<dl class="tabs nb-<?php echo count($endless_tabs); ?>" data-tab>
						<?php $i = 0; foreach($endless_tabs as $tab): ?>
					  	<dd class="l-radius<?php echo (($i == 0) ? ' active' : ''); ?>"><a href="#tab_<?php echo esc_attr($i);?>"><?php echo esc_attr($tab["name"]); ?></a></dd>
					  	<?php $i++; endforeach; ?>
					</dl>
					<div class="tabs-content">

						<?php 
						$i = 0; 
						foreach($endless_tabs as $tab):
						
							$per_page = $tab['posts_per_page'];
							$query = $tab['query'];
							
							$include_posts = false;
							$query_post_formats = false;
							$format = false;
							
							if($query == 'selection') {
								$include_posts = $tab['include_posts'];
							}else{
								
								$query_post_formats = $tab['query_post_formats'];
								if($query_post_formats)
									$format = $tab['format'];
							}	
							
							$tab_post_category = $tab['show_post_category'];
							$tab_post_author = $tab['show_post_author'];
							$tab_post_date = $tab['show_post_date'];
							$tab_post_stats = $tab['show_post_stats'];
		
							
							$posts = xt_get_posts(array(
								'direction' => 'prev',
								'per_page' => $per_page,
								'query' => $query,
								'include_posts' => $include_posts,
								'query_post_formats' => $query_post_formats,
								'format' => $format
							));
							
							$tab_posts[$i] = $posts;
 						?>
					  
	 						
						<div class="content<?php echo (($i == 0) ? ' active' : ''); ?>" id="tab_<?php echo esc_attr($i);?>" data-action="xt_ajax_get_posts" data-settings="<?php echo esc_attr(xt_64_encode(serialize($tab)));?>">
						
							<aside class="widget xt_news">
								<ul class="news-list list<?php echo ($stroll_animation != 'none' ? ' stroll '.$stroll_animation : '')?>">
								
									<?php 
									foreach ($posts as $post) {
										setup_postdata($post);
										include(locate_template('parts/items/post-tab-item.php')); 
									}	
									wp_reset_postdata();
									?>	
																								
								</ul>
							</aside>
							
						</div>
							
						<?php $i++; endforeach; ?>
						
					</div>
				</aside>
																								
			</div>	
					
		</div>
		<!-- End Sidebar-->
		
					
		<div class="medium-7 column left">
					
			<!--Endless Articles -->			
			<div class="endless-articles">		
					
				<?php 
				$i = 0; 
				foreach($endless_tabs as $key => $tab) {
				
					$posts = $tab_posts[$i];
				?>
				
					<div id="tab_<?php echo esc_attr($i);?>-tabs-fullcontent" class="tabs-fullcontent<?php echo (($i == 0) ? ' active' : ''); ?>" data-active="0">
					<?php 
					foreach ($posts as $post) {
						setup_postdata($post);
						include(locate_template('parts/items/post-full.php')); 
					}	
					wp_reset_postdata();
					?>
					</div>
		
				<?php 
					$i++; 
				}
				?>

				
			</div>
			<!--End Endless Wrap -->										
			
		</div>
		<!-- End Main Content -->	

		
	</div>	

<?php
$post = $backup_post;
get_footer();