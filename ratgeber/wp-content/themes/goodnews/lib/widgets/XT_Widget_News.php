<?php

/**
 * News Widget Class
 *
 * @since 1.0
 * @see   XT_Widget_News
 * @todo  - Add advanced options
 */

class XT_Widget_News extends XT_Widget
{
	/**
	 * Widget Defaults
	 */
	protected $query;
	public static $widget_defaults;


	/**
	 * Register widget with WordPress.
	 */

	function __construct ()
	{
		$widget_ops = array(
			  'classname'   => 'xt_news'
			, 'description' => _x('The most recent news on your site.', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title' => '',
			  'bold_title' => 0,
			  'query_type' => '',
			  'include_posts' => '',
			  'related_to' => '',
			  'related_to_type' => '',
	    	  'number' => get_option('posts_per_page'),
	    	  'offset' => 0,
	    	  'date_range' => '',
	    	  'show_date' => 0,
	    	  'show_author' => 0,
	    	  'show_category' => 0,
	    	  'show_excerpt' => 0,
	    	  'show_stats' => 0,
	    	  'title_length' => 12,
	    	  'excerpt_length' => apply_filters( 'excerpt_length', 55 ),
	    	  'category' => '',
              'format' => '',	    	  
	    	  'view' => 'list',
	    	  'bordered' => 0,
	    	  'post_type' => 'post',
			  'action_title' => '',
			  'action_obj_id'  => '',
			  'action_ext_link'  => ''
		);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		parent::__construct('xt-news', XT_WIDGET_PREFIX . _x('News', 'Widget', XT_TEXT_DOMAIN), $widget_ops);
		
	}
	
	function enqueue_scripts() {
	
	    wp_enqueue_script('xt-widget-conditional', XT_WIDGETS_URL.'/assets/js/conditional.js', array('jquery'));
	    wp_enqueue_style('xt-widget-style',  XT_WIDGETS_URL.'/assets/css/style.css');

	}


	function widget ( $args, $instance )
	{
		global $xt_global_where, $wpdb;

		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );
		$this->fix_args($args);

		$title      	= apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$post_type  	= apply_filters( 'widget_post_type', $instance['post_type'], $instance, $this->id_base );
		$query_type 	= $instance['query_type'];
		$include_posts 	= $instance['include_posts'];
		$related_to 	= $instance['related_to'];
		$related_to_type = $instance['related_to_type'];
		$number     	= $instance['number'];
		$offset     	= $instance['offset'];
		$category   	= $instance['category'];
		$date_range		= $instance['date_range'];
		$format   		= $instance['format'];
		$view     		= $instance['view'];
		$bordered   	= (bool)$instance['bordered'];
		
		if($bordered) {
			$this->set_border($args);
		}
		
		$this->instance = $instance;
		
		
		extract($args);
		

		$query_args = array(
			  'post_type'           => $post_type
			, 'posts_per_page'      => $number
			, 'offset'      		=> $offset
			, 'no_found_rows'       => true
			, 'post_status'         => 'publish'
			, 'ignore_sticky_posts' => true
		);
		
		$exclude_posts = array();


		if(!empty($query_type)) {

			if($query_type == 'most-viewed') {
			
				$query_args['meta_key'] = xt_get_views_meta_key();
				$query_args['orderby'] = 'meta_value_num';
				$query_args['order'] = 'DESC';
				
			}else if($query_type == 'most-liked') {
			
				$query_args['meta_key'] = '_votes_likes';
				$query_args['orderby'] = 'meta_value_num';
				$query_args['order'] = 'DESC';
				
			}else if($query_type == 'most-discussed') {
			
				$query_args['orderby'] = 'comment_count';
				$query_args['order'] = 'DESC';
				$xt_global_where = "AND $wpdb->posts.comment_count > 0";
				
			}else if($query_type == 'random') {
			
				$query_args['orderby'] = 'rand';
				unset($query_args["offset"]);
				
			}else if($query_type == 'related') {
			
				$post_id = false;
				
				if(!empty($related_to)) {
					
					$post_id = intval($related_to);
					
				}else if(is_single()) {
					
					$post_id = get_the_ID();
					
				}
				
				if($post_id !== false) {	
					
					$exclude_posts[] = $post_id;
					
					if($related_to_type == "tags") {
						
						$tag_ids = wp_get_post_tags($post_id, array('fields'=>'ids'));
						
						if ($tag_ids) {
							
							$query_args['tag__in'] = $tag_ids;
							
						}else{
							
							$query_args['tag__in'] = array(-1);
						}
						
					}else{
						
						$cat_ids = wp_get_post_categories($post_id, array('fields'=>'ids'));
						
						if ($cat_ids) {
	
							$category = $cat_ids;
							
						}else{
							
							$category = array(-1);
						}
						
					}
				}		
				
			}else if($query_type == 'selection' && !empty($include_posts)) {
			
				$selection = $include_posts;
				if(!is_array($selection)) {
					$selection = explode(",", $selection);
				}
				
				//reset
				$query_args["post__in"] = $selection;
				$query_args['orderby'] = 'post__in';
				$query_args["posts_per_page"] = -1;
				
				unset($query_args["offset"]);
				
				$category = false;
				$number = false;
				$offset = false;
				$query_post_formats = false;
				$format = false;
				$date_range = false;
				
				
			}
		}
			
					
		if(!empty($date_range)) {
		
			$query_args['date_query'] = array(
            	array(
            		'after' => $date_range,
					'inclusive' => true
            	)
	        );
	    }     
	    
	    
		if(!empty($category)) {
			
			if(is_array($category)) {
				$category = implode(",", $category);
			}
			$query_args["cat"] = $category;
		}

		if(!empty($format)) {
			
			if(!is_array($format)) {
				$format = explode(",", $format);
			}
			
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => $format,
					'operator' => 'IN'
				)
			);
	
		}
		

		if(!empty($format)) {
		
			if(!is_array($format)) {
				$format = explode(",", $format);
			}
			
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => $format,
					'operator' => 'IN'
				)
			);
	
	
		}else if($query_type != 'selection') {
			
			$exclude_formats = xt_get_post_formats(true);

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => $exclude_formats,
					'operator' => 'NOT IN'
				)
			);
				
		}
		               
                        
		
		if(is_single()) {
			$post_id = get_the_ID();
			$exclude_posts[] = $post_id;
		}
		
		if(!empty($exclude_posts)) {
			$query_args["post__not_in"] = $exclude_posts;
		}
		
			
		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) :

            $this->query = $query;
            
			$action_title = $instance['action_title'];
			$action_obj_id = $instance['action_obj_id'];
			$action_ext_link = $instance['action_ext_link'];
	
			/***/
	
			$action = $this->action_link( $action_obj_id, $action_ext_link, $action_title);

			echo $before_widget;

			if ( ! empty( $title ) )
				echo $before_title . $title . $action . $after_title;

			?>
			<div class="recent-posts <?php echo esc_attr($view); ?>">
			<?php				
					
				if($view == 'grid-1col') {
		
					$this->renderNewsGrid(1);
				
				}else if($view == 'grid-2col') {
				
					$this->renderNewsGrid(2);
					
				}else if($view == 'grid-3col') {
				
					$this->renderNewsGrid(3);
					
				}else if($view == 'grid-4col') {
				
					$this->renderNewsGrid(4);

				}else if($view == 'grid-5col') {
				
					$this->renderNewsGrid(5);
							
				}else if($view == 'list') {

					$this->renderNewsList();
        
				}else if($view == 'ranking') {

					$this->renderNewsRankingList();
        
				}

			?>				
			</div>

			<?php

			echo $after_widget;

			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();

		endif;

	}

	
	function renderBegin($class = '') {
    	
    	?>
    	
    	<ul class="news-list <?php echo esc_attr($class); ?>">
    	
    	<?php
	}
	
	function renderEnd() {
	    ?>
    	</ul>
	    <?php
	}
	
		
	function renderNewsGrid($col) {
		

    	$this->renderBegin('posts-grid small-block-grid-1 small-grid-offset medium-block-grid-'.esc_attr(ceil($col/2)).' large-block-grid-'.$col.'');
		
		$thumb_size = 'th-medium';
			
		if($col < 3) {
			$thumb_size = 'th-large';
		}	
			
		while ( $this->query->have_posts() ) : $this->query->the_post();
		?>
			
    	<li <?php post_class(); ?> data-equalizer-watch  itemscope="" itemtype="http://schema.org/BlogPosting">
    	
    		<div class="row">
	    		
	        	<?php if(has_post_thumbnail()): ?>
		        	<?php $this->renderThumb($thumb_size); ?>
		        <?php endif; ?>
	        	
	        	<div class="meta">	
	        		
	        		<?php $this->renderCategory(); ?>
					
					<?php if($col < 2): ?>
	        		
	        			<?php $this->renderTitle('h3', 'bold'); ?>
						<?php $this->renderExcerpt('h4', 'spaced'); ?>
	        		
	        		<?php elseif($col >= 5): ?>
	        		
	        			<?php $this->renderTitle('h5', 'bold'); ?>
						<?php $this->renderExcerpt('h6', ''); ?>
						
	        		<?php else: ?>
	        		
	        			<?php $this->renderTitle('h4', 'bold'); ?>
						<?php $this->renderExcerpt('h5'); ?>
						
	        		<?php endif; ?>
	        		
	        		<?php $this->renderAuthor(); ?>
	                <?php $this->renderDate(); ?>
	                <?php $this->renderStats(); ?>
	        		
	        	</div>	
    		</div>
        </li>
        
        <?php
		endwhile;
			
        $this->renderEnd();
	
	}
	
	function renderNewsList() {
	
		$this->renderBegin('news-list posts-list-medium-thumbs list');

		while ( $this->query->have_posts() ) : $this->query->the_post();
		?>

         	<li <?php post_class(); ?>  itemscope="" itemtype="http://schema.org/BlogPosting">
				<div class="row collapse">
					<div class="small-12 medium-6 large-4 column first">
						<?php $this->renderThumb('th-medium'); ?>
					</div>
					<div class="small-12 medium-6 large-8 column last">
						<div class="meta">
							<?php $this->renderCategory(); ?>
							
							<?php if($this->instance["show_excerpt"]) : ?>
								<?php $this->renderTitle('h4', 'bold'); ?>
							<?php $this->renderExcerpt('p'); ?>
							<?php else: ?>
								<?php $this->renderTitle('h5'); ?>
							<?php endif; ?>
							
							<?php $this->renderAuthor(); ?>
							<?php $this->renderDate(); ?>
							<?php $this->renderStats(); ?>
						</div>
					</div>	
				</div>
			</li>  
			 
        <?php
		endwhile;
			
		$this->renderEnd();
	}

	function renderNewsRankingList() {
	
		$this->renderBegin('numeric-list list');

		while ( $this->query->have_posts() ) : $this->query->the_post();
		?>

         	<li itemscope="" itemtype="http://schema.org/BlogPosting">
				<div class="meta">
					
					<?php $this->renderCategory(); ?>
						
					<?php if($this->instance["show_excerpt"]) : ?>
						<?php $this->renderTitle('h5', 'bold'); ?>
						<?php $this->renderExcerpt('p'); ?>
					<?php else: ?>
						<?php $this->renderTitle('h5'); ?>
					<?php endif; ?>
							
					<?php $this->renderAuthor(); ?>
					<?php $this->renderDate(); ?>
					<?php $this->renderStats(true, array('mini')); ?>
					
				</div>
			</li>  
			 
        <?php
		endwhile;
			
		$this->renderEnd();
	
	}
	
	function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( (array) $old_instance, self::$widget_defaults );

		$instance['title'] 				= strip_tags($new_instance['title']);
		$instance['query_type'] 		= $new_instance['query_type'];
		$instance['include_posts']  	= preg_replace('/\s+/', '', $new_instance['include_posts']);
		$instance['related_to']  		= $new_instance['related_to'];
		$instance['related_to_type']  	= $new_instance['related_to_type'];
		$instance['number'] 			= (int) $new_instance['number'];
		$instance['offset'] 			= (int) $new_instance['offset'];
		$instance['date_range'] 		= $new_instance['date_range'];
		$instance['title_length'] 		= (int) $new_instance['title_length'];
		$instance['excerpt_length'] 	= (int) $new_instance['excerpt_length'];
		
		$instance['view'] 				= $new_instance['view'];
		$instance['category'] 			= $new_instance['category'];
        $instance['format'] 			= $new_instance['format'];		
		$instance['show_date'] 			= !empty($new_instance['show_date']) ? 1 : 0;
		$instance['show_author'] 		= !empty($new_instance['show_author']) ? 1 : 0;
		$instance['bordered'] 			= !empty($new_instance['bordered']) ? 1 : 0;
		
		$instance["show_category"] 		= !empty($new_instance['show_category']) ? 1 : 0;
		$instance["show_excerpt"] 		= !empty($new_instance['show_excerpt']) ? 1 : 0;
		$instance["show_stats"] 		= !empty($new_instance['show_stats']) ? 1 : 0;
		$instance["bold_title"] 		= !empty($new_instance['bold_title']) ? 1 : 0;

		$instance['action_title']  		= $new_instance['action_title'];
		$instance['action_obj_id']  	= $new_instance['action_obj_id'];
		$instance['action_ext_link']  	= $new_instance['action_ext_link'];
				
		return $instance;
	}

	function form ( $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title     			= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$query_type 		= $instance['query_type'];
		$include_posts 		= $instance['include_posts'];
		$related_to 		= $instance['related_to'];
		$related_to_type 	= $instance['related_to_type'];
		$number    			= isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$offset    			= isset( $instance['offset'] ) ? absint( $instance['offset'] ) : 0;
		$date_range 		= $instance['date_range'];
		
		$title_length    	= isset( $instance['title_length'] ) ? absint( $instance['title_length'] ) : 12;
		$excerpt_length    	= isset( $instance['excerpt_length'] ) ? absint( $instance['excerpt_length'] ) : apply_filters( 'excerpt_length', 55 );

		$show_date 			= !empty($instance['show_date']) ? 1 : 0;
		$show_author 		= !empty($instance['show_author']) ? 1 : 0;
		$show_category 		= !empty($instance['show_category']) ? 1 : 0;
		$show_excerpt 		= !empty($instance['show_excerpt']) ? 1 : 0;
		$show_stats 		= !empty($instance['show_stats']) ? 1 : 0;
		$bold_title 		= !empty($instance['bold_title']) ? 1 : 0;

		$view 				= $instance['view'];
		$category 			= $instance['category'];
        $format 			= $instance['format'];		
		$bordered 			= !empty($instance['bordered']) ? 1 : 0;
		
		$action_title 		= $instance['action_title'];
		$action_obj_id 		= $instance['action_obj_id'];
		$action_ext_link 	= $instance['action_ext_link'];
		
		
		$query_types = array(
			 'most-recent' => __("Most Recent", XT_TEXT_DOMAIN),
			 'most-viewed' => __("Most Viewed", XT_TEXT_DOMAIN),
			 'most-liked' => __("Most Liked", XT_TEXT_DOMAIN),
			 'most-discussed' => __("Most Discussed", XT_TEXT_DOMAIN),
			 'random' => __("Random", XT_TEXT_DOMAIN),
			 'related' => __("Related to post", XT_TEXT_DOMAIN),
			 'selection' => __("Manual Selection", XT_TEXT_DOMAIN),
		);
		
		$related_to_types = array(
			'tags' => __("Post Tags", XT_TEXT_DOMAIN),
            'categories' => __("Post Categories", XT_TEXT_DOMAIN)
		);
		
		$date_ranges = array(
			'' => __("Any time", XT_TEXT_DOMAIN),
			'1 week ago' => __("Within last week", XT_TEXT_DOMAIN),
            '1 month ago' => __("Within last month", XT_TEXT_DOMAIN),
            '1 year ago' => __("Within last year", XT_TEXT_DOMAIN),
		);
		
		?>
		
		<br>
		
		<div class="greybox">
			<h3><?php _e('General', XT_TEXT_DOMAIN); ?></h3>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , XT_TEXT_DOMAIN); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="<?php _e('Latest News', XT_TEXT_DOMAIN); ?>" />
			</p>
	
	
			<p>
				<label for="<?php echo $this->get_field_id('action_title'); ?>"><?php _ex('Call To Action Title:', 'Widget', XT_TEXT_DOMAIN); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('action_title'); ?>" name="<?php echo $this->get_field_name('action_title'); ?>" value="<?php echo esc_attr($action_title); ?>" placeholder="<?php _e('View More', XT_TEXT_DOMAIN); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('action_obj_id'); ?>"><?php _ex('Call To Call To Action Page:', 'Widget', XT_TEXT_DOMAIN); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('action_obj_id'); ?>" name="<?php echo $this->get_field_name('action_obj_id'); ?>">
					<?php echo $this->get_object_options($action_obj_id); ?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('action_ext_link'); ?>"><?php _ex('Call To Action External Link:', 'Widget', XT_TEXT_DOMAIN); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('action_ext_link'); ?>" name="<?php echo $this->get_field_name('action_ext_link'); ?>" value="<?php echo esc_attr($action_ext_link); ?>" />
			</p>
		</div>
		<br>
		
		<div class="greybox">
			<h3><?php _e('Query', XT_TEXT_DOMAIN); ?></h3>
			<p>
				<select class="widefat" id="<?php echo $this->get_field_id('query_type'); ?>" name="<?php echo $this->get_field_name('query_type'); ?>">
					<?php foreach($query_types as $id => $type): ?>
						
						<option value="<?php echo $id;?>" <?php echo ($query_type == $id ? "selected" : ""); ?>><?php echo $type;?></option>
						
					<?php endforeach; ?>
				</select>						
			</p>

			
			<p data-hideif="query_type:selection">
				<label for="<?php echo $this->get_field_id( 'date_range' ); ?>"><?php _e( 'Show posts from' , XT_TEXT_DOMAIN); ?></label>
				<select class="widefat logic" id="<?php echo $this->get_field_id('date_range'); ?>" name="<?php echo $this->get_field_name('date_range'); ?>">
					<?php foreach($date_ranges as $id => $range): ?>
						
						<option value="<?php echo $id;?>" <?php echo ($date_range == $id ? "selected" : ""); ?>><?php echo $range;?></option>
						
					<?php endforeach; ?>
				</select>	
			</p>	
					
						
			<p data-showif="query_type:selection">
				<label for="<?php echo $this->get_field_id( 'include_posts' ); ?>"><?php _e( 'Include Post IDs (comma separated)' , XT_TEXT_DOMAIN); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'include_posts' ); ?>" name="<?php echo $this->get_field_name( 'include_posts' ); ?>" type="text" value="<?php echo esc_attr($include_posts); ?>" />
			</p>
	
			<p data-showif="query_type:related">
				<label for="<?php echo $this->get_field_id( 'related_to' ); ?>"><?php _e( 'Related to post' , XT_TEXT_DOMAIN); ?><br><small><?php _e( 'Enter Post ID or leave empty to detect current single post' , XT_TEXT_DOMAIN); ?></small></label>
				
				<input class="widefat" id="<?php echo $this->get_field_id( 'related_to' ); ?>" name="<?php echo $this->get_field_name( 'related_to' ); ?>" type="text" value="<?php echo esc_attr($related_to); ?>" />
			</p>
			
			<p data-showif="query_type:related">
				<label for="<?php echo $this->get_field_id( 'related_to_type' ); ?>"><?php _e( 'Related based on' , XT_TEXT_DOMAIN); ?></label>
				<select class="widefat logic" id="<?php echo $this->get_field_id('related_to_type'); ?>" name="<?php echo $this->get_field_name('related_to_type'); ?>">
					<?php foreach($related_to_types as $id => $type): ?>
						
						<option value="<?php echo $id;?>" <?php echo ($related_to_type == $id ? "selected" : ""); ?>><?php echo $type;?></option>
						
					<?php endforeach; ?>
				</select>	
			</p>
					
			<p data-hideif="query_type:related|selection">
				<label for="<?php echo $this->get_field_id('category'); ?>"><?php _ex('Filter by category:', 'Widget', XT_TEXT_DOMAIN); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>[]" multiple="mutiple">
					<?php echo $this->get_taxonomy_options($category); ?>
				</select>
			</p>
			
			<p data-hideif="query_type:selection">
				<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Filter by post format:', XT_TEXT_DOMAIN ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>[]" multiple="mutiple">
					<?php echo $this->get_post_format_options($format); ?>
				</select>
			</p>
							
			<p data-hideif="query_type:selection">
				<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' , XT_TEXT_DOMAIN); ?></label>
				<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3" />
			</p>
				
			<p data-hideif="query_type:random|selection">
				<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php _e( 'Number of posts to offset' , XT_TEXT_DOMAIN); ?></label>
				<input id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" type="text" value="<?php echo esc_attr($offset); ?>" size="3" />
			</p>
		
		</div>
		<br>

		
		<div class="greybox">	
			<h3><?php _e('Layout', XT_TEXT_DOMAIN); ?></h3>

			<p>
				<select class="widefat" id="<?php echo $this->get_field_id('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>">
					<option <?php echo ($view == 'grid-1col' ? 'selected' : ''); ?> value="grid-1col"><?php _ex('Classic', 'Widget', XT_TEXT_DOMAIN); ?></option>
					<option <?php echo ($view == 'list' ? 'selected' : ''); ?> value="list"><?php _ex('List', 'Widget', XT_TEXT_DOMAIN); ?></option>
					<option <?php echo ($view == 'grid-2col' ? 'selected' : ''); ?> value="grid-2col"><?php _ex('Grid 2 Columns' , 'Widget', XT_TEXT_DOMAIN); ?></option>			
					<option <?php echo ($view == 'grid-3col' ? 'selected' : ''); ?> value="grid-3col"><?php _ex('Grid 3 Columns' , 'Widget', XT_TEXT_DOMAIN); ?></option>			
					<option <?php echo ($view == 'grid-4col' ? 'selected' : ''); ?> value="grid-4col"><?php _ex('Grid 4 Columns' , 'Widget', XT_TEXT_DOMAIN); ?></option>
					<option <?php echo ($view == 'grid-5col' ? 'selected' : ''); ?> value="grid-4col"><?php _ex('Grid 5 Columns' , 'Widget', XT_TEXT_DOMAIN); ?></option>			
					<option <?php echo ($view == 'ranking' ? 'selected' : ''); ?> value="ranking"><?php _ex('Ranking List' , 'Widget', XT_TEXT_DOMAIN); ?></option>
	
				</select>						
			</p>
			
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $bordered ); ?> id="<?php echo $this->get_field_id( 'bordered' ); ?>" name="<?php echo $this->get_field_name( 'bordered' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'bordered' ); ?>"><?php _e( 'Bordered Box' , XT_TEXT_DOMAIN); ?></label>
			</p>
			
		</div>
		<br>
	
		
		<div class="greybox">		
			<h3><?php _e('Show / Hide', XT_TEXT_DOMAIN); ?></h3>	
			<p>
				<label for="<?php echo $this->get_field_id( 'title_length' ); ?>"><?php _e( 'Title Length:' , XT_TEXT_DOMAIN); ?></label>
				<input id="<?php echo $this->get_field_id( 'title_length' ); ?>" name="<?php echo $this->get_field_name( 'title_length' ); ?>" type="text" value="<?php echo esc_attr($title_length); ?>" size="3" />
			</p>
	
			<p>
				<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length:' , XT_TEXT_DOMAIN); ?></label>
				<input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="text" value="<?php echo esc_attr($excerpt_length); ?>" size="3" />
			</p>
	
	
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $bold_title ); ?> id="<?php echo $this->get_field_id( 'bold_title' ); ?>" name="<?php echo $this->get_field_name( 'bold_title' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'bold_title' ); ?>"><?php _e( 'Bold Title' , XT_TEXT_DOMAIN); ?></label>
			</p>
	
			
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_excerpt ); ?> id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Display Excerpt' , XT_TEXT_DOMAIN); ?></label>
			</p>
	
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date' , XT_TEXT_DOMAIN); ?></label>
			</p>
	
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_author ); ?> id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>"><?php _e( 'Display author' , XT_TEXT_DOMAIN); ?></label>
			</p>
	
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_category ); ?> id="<?php echo $this->get_field_id( 'show_category' ); ?>" name="<?php echo $this->get_field_name( 'show_category' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_category' ); ?>"><?php _e( 'Display Category' , XT_TEXT_DOMAIN); ?></label>
			</p>
	
			<p>
				<input class="checkbox" type="checkbox" <?php checked( $show_stats ); ?> id="<?php echo $this->get_field_id( 'show_stats' ); ?>" name="<?php echo $this->get_field_name( 'show_stats' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_stats' ); ?>"><?php _e( 'Display Stats' , XT_TEXT_DOMAIN); ?></label>
			</p>
		</div>
		
		<br>
		
		<?php
	}


    function renderThumb($size = 'th-medium') {
    
	    xt_post_thumbnail($size, true);
	}

	function renderCategory() {
	
	    if($this->instance["show_category"]) {
	    
    		xt_post_category();
    		
    	}
	}


	function renderTitle($tag = 'h3', $class = '') {
	
		$length = null;
		
		if(!empty($this->instance["title_length"])) {
			
			$length = $this->instance["title_length"];
			xt_post_title($tag, $class, true, $length);
			
		}else{
			
			xt_post_title($tag, $class, true);
		}
	}

	function renderExcerpt($tag = 'h5', $class = '') {
	
		if(!empty($this->instance["show_excerpt"])) {
			
			if(!empty($this->instance["excerpt_length"])) {
				
				$length = $this->instance["excerpt_length"];
				xt_post_excerpt($tag, $class, $length);
				
			}else{
				
				xt_post_excerpt($tag, $class);
			}	
	    }	
	    
	}
			
	function renderAuthor() {
	
    	if($this->instance["show_author"]) {
    		
    		xt_post_author();
        }
	}
	
	function renderDate() {
	
    	if($this->instance["show_date"]) {
    	
    		xt_post_date(); 
        }
	}

	function renderStats($linkComments = true, $classes=array()) {
	
    	if($this->instance["show_stats"]) {
    	
    		xt_post_stats($linkComments, $classes);
        }
	}

} 