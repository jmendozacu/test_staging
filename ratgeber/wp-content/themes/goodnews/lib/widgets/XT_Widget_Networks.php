<?php

/**
 * Networks Widget Class
 *
 * @since 1.0
 * @todo  - Add options
 */

class XT_Widget_Networks extends XT_Widget
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
			  'classname'   => 'xt_widget_networks'
			, 'description' => _x('Social Networks', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title'           => ''
			, 'bordered' 		=> 0

		);

		add_shortcode( 'xt_networks', array($this, 'shortcode') );

		parent::__construct('xt-networks', XT_WIDGET_PREFIX . _x('Social Networks', 'Widget', XT_TEXT_DOMAIN), $widget_ops);
	}
	
    function shortcode( $atts ) {
   
		$atts = shortcode_atts( array(
    	  'title' => '',
    	  'bordered' => 0,
    	), $atts );
    	
    	extract( $atts );
    
        $this->instance = $atts;
     	
     	ob_start();   
     	$output = '<div class="vc_xt_widget_networks wpb_content_element">';  	
        $this->widget($this->get_default_args(), $this->instance); 
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
		extract($args);
						
		echo $before_widget;
		
		if ( $title )
			echo sprintf( $before_title, '' ) . wp_kses_post($title) . $after_title;


		$social_networks = xt_option('social_icons');
		if(!empty($social_networks)): ?>
		
			<!-- social-networks -->
			<ul class="social-networks">
			
				<?php foreach($social_networks as $network): ?>
				<?php 
					if(empty($network["name"])) 
						continue;
				?>
				<li style="background-color:<?php echo esc_attr($network["color"]); ?>">
					<a target="_blank" href="<?php echo esc_attr($network["url"]); ?>">
						<?php if(!empty($network["thumb"])): ?>
						<img src="<?php echo esc_url($network["thumb"]); ?>" alt="<?php echo esc_attr($network["name"]); ?>" style="max-height:50px;">
						<?php else: ?>
						<i class="fa fa-<?php echo esc_attr($network["icon"]); ?>" title="<?php echo esc_attr($network["name"]); ?>"></i>
						<?php endif; ?>
					</a>
				</li>
		
				<?php endforeach; ?>	
				
			</ul>
			
		<?php endif; 			

		echo $after_widget;
	}

	function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( (array) $old_instance, self::$widget_defaults );

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['bordered'] = !empty($new_instance['bordered']) ? 1 : 0;
		
		return $instance;
	}

	function form ( $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$bordered = !empty($instance['bordered']) ? 1 : 0;

?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , XT_TEXT_DOMAIN); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $bordered ); ?> id="<?php echo $this->get_field_id( 'bordered' ); ?>" name="<?php echo $this->get_field_name( 'bordered' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'bordered' ); ?>"><?php _e( 'Bordered Box' , XT_TEXT_DOMAIN); ?></label>
		</p>
		
<?php
	}
}

