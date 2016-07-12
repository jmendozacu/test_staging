<?php

/**
 * Newsletter Widget Class
 *
 * @since 1.0
 * @todo  - Add options
 */

class XT_Widget_Newsletter extends XT_Widget
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
			  'classname'   => 'xt_widget_newsletter'
			, 'description' => _x('Mailchimp Newletter.', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title'           => ''
			, 'fid' 			=> '' 
			, 'description'		=> ''
	    	, 'bordered' => 0
		);

		add_shortcode( 'xt_newsletter', array($this, 'shortcode') );

		parent::__construct('xt-newsletter', XT_WIDGET_PREFIX . _x('Newsletter', 'Widget', XT_TEXT_DOMAIN), $widget_ops);
	}


    function shortcode( $atts ) {
   
		$atts = shortcode_atts( array(
		  'title' => '',
		  'description' => '',
		  'fid' => '',
		  'bordered' => '',
    	), $atts );
    	 
    	extract( $atts );
    
        $this->instance = $atts;
     	
     	ob_start();   
     	$output = '<div class="vc_xt_widget_newsletter wpb_content_element">';    	
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
				
		$fid = $instance['fid'];
		$description = $instance['description'];

		if(empty($fid))
			return;

	
				
		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo sprintf( $args['before_title'], '' ) . wp_kses_post($title) . $args['after_title'];

?>
		
			<div class="control-append">
				<div class="newsletter-wrap">
				
					<?php if(!empty($description)): ?>
						<p class="control-description">
							<?php echo $description; ?>
						</p>
					<?php endif; ?>	
					
					<?php echo do_shortcode('[nm-mc-form fid="'.$fid.'"]'); ?>
				</div>
			</div>

<?php

		echo $args['after_widget'];
	}

	function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( (array) $old_instance, self::$widget_defaults );

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['fid'] = $new_instance['fid'];
		$instance['description'] = $new_instance['description'];
		$instance['bordered'] = !empty($new_instance['bordered']) ? 1 : 0;

		return $instance;
	}

	function form ( $instance )
	{
		global $wpdb;
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$fid = $instance['fid'];
		$description = $instance['description'];
		$bordered = !empty($instance['bordered']) ? 1 : 0;

?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , XT_TEXT_DOMAIN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description:' , XT_TEXT_DOMAIN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" type="text" value="<?php echo esc_attr($description); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id( 'fid' ); ?>"><?php _e( 'Newsletter Form:' , XT_TEXT_DOMAIN); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('fid'); ?>" name="<?php echo $this->get_field_name('fid'); ?>">
			
<?php
	
		$results = $wpdb->get_results('SELECT form_id, form_name FROM '.$wpdb->prefix.'nm_mc_forms ORDER BY form_name');
		$newsletters = array();
		foreach($results as $result) {
		
			$name = !empty($result->form_name) ? $result->form_name : $result->form_id;
			$id = $result->form_id;
		
			echo '<option '.(($fid == $id) ? 'selected' : '').' value="'.$id.'">'.$name.'</option>';
		}
?>
		</select>
		</p>
		
		<p><input class="checkbox" type="checkbox" <?php checked( $bordered ); ?> id="<?php echo $this->get_field_id( 'bordered' ); ?>" name="<?php echo $this->get_field_name( 'bordered' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'bordered' ); ?>"><?php _e( 'Bordered Box' , XT_TEXT_DOMAIN); ?></label></p>
		
<?php
	}
}

