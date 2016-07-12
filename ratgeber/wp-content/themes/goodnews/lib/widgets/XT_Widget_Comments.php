<?php

/**
 * Comments Widget Class
 *
 * @since 1.0
 * @see   XT_Widget_Comments
 * @todo  - Add advanced options
 */

class XT_Widget_Comments extends XT_Widget
{
	/**
	 * Widget Defaults
	 */
	public static $widget_defaults;



	/**
	 * Register widget with WordPress.
	 */

	function __construct ()
	{
		$widget_ops = array(
			  'classname'   => 'xt_comments'
			, 'description' => _x('The most recent comments on your site.', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title' => '',
	    	  'number' => '',
	    	  'show_date' => 1,
	    	  'bordered' => 0,
		);

		add_shortcode( 'xt_comments', array($this, 'shortcode') );

		parent::__construct('xt-comments', XT_WIDGET_PREFIX . _x('Recent Comments', 'Widget', XT_TEXT_DOMAIN), $widget_ops);
		
	}
	
	
    function shortcode( $atts ) {
   
		$atts = shortcode_atts( array(
    	  'title' => '',
    	  'number' => '5',
    	  'show_date' => 1,
    	  'bordered' => 0,
    	), $atts );
    	
    	extract( $atts );
    
        $this->instance = $atts;
        
     	ob_start();     	
        $this->widget($this->get_default_args(), $this->instance); 
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
    
	function widget ( $args, $instance )
	{

		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );
		$this->fix_args($args);


		$title      = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number     = $instance['number'];

		$show_date   = (bool)$instance['show_date'];
		$bordered   = (bool)$instance['bordered'];
		
		if($bordered) {
			$this->set_border($args);
		}

		extract($args);


		$comments = get_comments(array(
			'number' => $number,
			'post_type' => 'post',
			'status' => 'approve'
		));

		if ( !empty($comments) ) :

			echo $before_widget;

			if ( ! empty( $title ) )
				echo $before_title . wp_kses_post($title) . $after_title;


				
			?>


			<ul class="recent_comments" itemscope itemtype="http://schema.org/UserComments">
				<?php foreach($comments as $comment): ?>
				<?php
				$comment_url = get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID;
				?>
				<li>
					<a class="th left" href="<?php echo esc_url($comment_url);?>"><?php echo (get_avatar($comment->user_id, 60, '', $comment->comment_author)); ?></a>
					<div class="comment">
						<h6>
							<a href="<?php echo esc_url($comment_url);?>">
								<span itemprop="author" class="author"><?php echo $comment->comment_author;?> </span>
								<?php if($show_date): ?><time itemprop="commentTime" datetime="<?php echo esc_attr(get_comment_date( 'c', $comment->comment_ID )); ?>" class="time"> &nbsp;|&nbsp; <?php echo xt_time_elapsed_DE($comment->comment_date); ?></time><?php endif; ?>
								<br>
								<span itemprop="commentText"><?php echo xt_trim_text(wp_kses_post($comment->comment_content));?></span>
							</a> 
							
						</h6>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>

			<?php

			echo $after_widget;

		else:
		
			__("No comments yet!", XT_TEXT_DOMAIN);
		
		endif;

	}

		
	function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( (array) $old_instance, self::$widget_defaults );

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = !empty($new_instance['show_date']) ? 1 : 0;
		$instance['bordered'] = !empty($new_instance['bordered']) ? 1 : 0;
		

		return $instance;
	}

	function form ( $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = !empty($instance['show_date']) ? 1 : 0;

		$bordered = !empty($instance['bordered']) ? 1 : 0;

		?>
		
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , XT_TEXT_DOMAIN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" placeholder="<?php _e('Recent Comments', XT_TEXT_DOMAIN); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' , XT_TEXT_DOMAIN); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' , XT_TEXT_DOMAIN); ?></label></p>


		<p><input class="checkbox" type="checkbox" <?php checked( $bordered ); ?> id="<?php echo $this->get_field_id( 'bordered' ); ?>" name="<?php echo $this->get_field_name( 'bordered' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'bordered' ); ?>"><?php _e( 'Bordered Box' , XT_TEXT_DOMAIN); ?></label></p>

		
		<?php
	}


} 
