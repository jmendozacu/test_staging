<?php

/**
 * Twitter Widget Class
 *
 * @since 1.0
 * @todo  - Add options
 */

class XT_Widget_Twitter extends XT_Widget
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
			  'classname'   => 'xt_widget_twitter'
			, 'description' => _x('The most recent tweets.', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title'           => ''
			, 'screen_name'     => ''
			, 'count'           => 3
			, 'show_avatar'     => true
			, 'show_date'       => true
			, 'bordered' 		=> 0
			, 'action_title'    => ''
			, 'action_obj_id'   => ''
			, 'action_ext_link'  => ''
		);

		add_shortcode( 'xt_twitter', array($this, 'shortcode') );

		parent::__construct('xt-twitter', XT_WIDGET_PREFIX . _x('Twitter', 'Widget', XT_TEXT_DOMAIN), $widget_ops);
	}


    function shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
		  'title' => '',
		  'screen_name' => '',
    	  'count' => 3,
    	  'show_avatar' => true,
    	  'show_date' => true,
    	  'bordered' => 0,
		  'action_title' => '',
		  'action_obj_id' => '',
		  'action_ext_link' => '',
		  'css_animation' => '',
    	), $atts );
    	
    	extract( $atts );
    
        $this->instance = $atts;
     	
     	ob_start();     
     	$output = '<div class="vc_xt_widget_twitter wpb_content_element">'; 	
        $this->widget($this->get_default_args($this->instance["css_animation"]), $this->instance); 
        $output .= ob_get_contents();
        $output .= '</div>'; 
        ob_end_clean();

        return $output;
    }
    
    
	/**
	 * Front-end display of widget.
	 */

	public function widget ( $args, $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );
		
		$this->fix_args($args);
		
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		
		$bordered = !empty($instance['bordered']) ? 1 : 0;

		if($bordered) {
			$this->set_border($args);
		}
		
		$username = str_replace('@', '', $instance['screen_name']);
		$count = $instance['count'];
		$show_date = !empty($instance['show_date']) ? 1 : 0;
		$show_avatar = !empty($instance['show_avatar']) ? 1 : 0;

		if ( ! $username )
			return;

		$action = $this->action_link( 'twitter', 0, 'https://twitter.com/' . $username );
		
		$action_title = apply_filters( 'xt_widget_action_title', $instance['action_title'], $instance, $this->id_base );
		$action_obj_id = apply_filters( 'xt_widget_action_obj_id', $instance['action_obj_id'], $instance, $this->id_base );
		$action_ext_link = apply_filters( 'xt_widget_action_ext_link', $instance['action_ext_link'], $instance, $this->id_base );

		/***/

		$action = $this->action_link( $action_obj_id, $action_ext_link, $action_title);
				

		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . wp_kses_post($title) . $action . $args['after_title'];;

		echo '
			<div class="panel__body">
				<div class="twitter-center">
					<div class="query" data-username="'.esc_attr($username).'" data-count="'.esc_attr($count).'" data-showdate="'.esc_attr($show_date).'" data-showavatar="'.esc_attr($show_avatar).'" data-loading="'.__('Loading tweets...', XT_TEXT_DOMAIN).'"></div>
					<div style="clear:both;"></div>
				</div>
			</div>';
			
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 */

	public function form ( $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title = esc_attr( $instance['title'] );
		$username = esc_attr( $instance['screen_name'] );
		$count = $instance['count'];
		$show_date = !empty($instance['show_date']) ? 1 : 0;
		$show_avatar = !empty($instance['show_avatar']) ? 1 : 0;
		$bordered = !empty($instance['bordered']) ? 1 : 0;

		$action_title = $instance['action_title'];
		$action_obj_id = $instance['action_obj_id'];
		$action_ext_link = $instance['action_ext_link'];
		

?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _ex('Title:', 'Widget', XT_TEXT_DOMAIN); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" placeholder="<?php _e('Live from Twitter', XT_TEXT_DOMAIN); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('screen_name'); ?>"><?php _ex('Screen Name:', 'Widget', XT_TEXT_DOMAIN); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('screen_name'); ?>" name="<?php echo $this->get_field_name('screen_name'); ?>" value="<?php echo esc_attr($username); ?>" placeholder="@envato" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _ex('Count:', 'Widget', XT_TEXT_DOMAIN); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo esc_attr($count); ?>" placeholder="" />
		</p>	
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display date?' , XT_TEXT_DOMAIN); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_avatar ); ?> id="<?php echo $this->get_field_id( 'show_avatar' ); ?>" name="<?php echo $this->get_field_name( 'show_avatar' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_avatar' ); ?>"><?php _e( 'Display avatar?' , XT_TEXT_DOMAIN); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $bordered ); ?> id="<?php echo $this->get_field_id( 'bordered' ); ?>" name="<?php echo $this->get_field_name( 'bordered' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'bordered' ); ?>"><?php _e( 'Bordered Box' , XT_TEXT_DOMAIN); ?></label>
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
			
<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */

	public function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( $old_instance, self::$widget_defaults );

		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['screen_name'] = str_replace('@', '', strip_tags( stripslashes($new_instance['screen_name']) ));
		$instance['count']  = (int)$new_instance['count'];
		$instance['show_date'] = !empty($new_instance['show_date']) ? 1 : 0;
		$instance['show_avatar'] = !empty($new_instance['show_avatar']) ? 1 : 0;
		$instance['bordered'] = !empty($new_instance['bordered']) ? 1 : 0;
		$instance['action_title']  = $new_instance['action_title'];
		$instance['action_obj_id']  = $new_instance['action_obj_id'];
		$instance['action_ext_link']  = $new_instance['action_ext_link'];
		return $instance;
	}

} 