<?php

/**
 * Text Widget Class
 *
 * @since 1.0
 * @todo  - Add options
 */

class XT_Widget_Text extends XT_Widget
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
			  'classname'   => 'xt_widget_text'
			, 'description' => _x('Text / HTML', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title'           => ''
			, 'heading'         => '' 
			, 'description'		=> ''
			, 'textsize'        => 'normal' 
			, 'bordered' 		=> 0

		);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

		//add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode');

		parent::__construct('xt-text', XT_WIDGET_PREFIX . _x('Text / HTML', 'Widget', XT_TEXT_DOMAIN), $widget_ops);
	}


	function enqueue_scripts() {
	
	    wp_enqueue_media();
	    wp_register_script('add-media', XT_WIDGETS_URL.'/assets/js/add-media.js', array('jquery'));
	    wp_enqueue_script('add-media');
	}

	/**
	 * Front-end display of widget.
	 */

	public function widget ( $args, $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );
		$heading = $instance['heading'];
		
		$this->fix_args($args);


		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$textsize = $instance['textsize'];
		$bordered = !empty($instance['bordered']) ? 1 : 0;
		
		if($bordered) {
			$this->set_border($args);
		}
		extract($args);
		
		
		$description =  wpautop($instance['description']);
		$description = shortcode_unautop($description);
		
		$description = apply_filters( 'widget_text',$description, $instance, $this->id_base );
				
		echo $before_widget;
		
		if ( $title )
			echo sprintf( $before_title, '' ) . wp_kses_post($title) . $after_title;

?>

		<div class="textwidget<?php echo ($textsize == 'large' ? ' large-text' : '');?>">
			<?php if(!empty($description)): ?>
					<?php echo wp_kses_post($description); ?>
			<?php endif; ?>			
		</div>

<?php

		echo $after_widget;
	}

	function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( (array) $old_instance, self::$widget_defaults );

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['heading'] = $new_instance['heading'];
		$instance['description'] = $new_instance['description'];
		$instance['textsize'] = $new_instance['textsize'];

		$instance['bordered'] = !empty($new_instance['bordered']) ? 1 : 0;
		
		return $instance;
	}

	function form ( $instance )
	{
		global $wpdb;
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$heading = $instance['heading'];
		$description = $instance['description'];
		$textsize = $instance['textsize'];
		$bordered = !empty($instance['bordered']) ? 1 : 0;

?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , XT_TEXT_DOMAIN); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'heading' ); ?>"><?php _e( 'Title Heading:' , XT_TEXT_DOMAIN); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('heading'); ?>" name="<?php echo $this->get_field_name('heading'); ?>">
				<option <?php echo ($heading == 'h1' ? 'selected' : ''); ?> value="h1"><?php _ex('H1', 'Widget', XT_TEXT_DOMAIN); ?></option>
				<option <?php echo ($heading == 'h2' ? 'selected' : ''); ?> value="h2"><?php _ex('H2', 'Widget', XT_TEXT_DOMAIN); ?></option>
				<option <?php echo ($heading == 'h3' ? 'selected' : ''); ?> value="h3"><?php _ex('H3', 'Widget', XT_TEXT_DOMAIN); ?></option>
				<option <?php echo ($heading == 'h4' ? 'selected' : ''); ?> value="h4"><?php _ex('H4', 'Widget', XT_TEXT_DOMAIN); ?></option>
				<option <?php echo ($heading == 'h5' ? 'selected' : ''); ?> value="h5"><?php _ex('H5', 'Widget', XT_TEXT_DOMAIN); ?></option>
				<option <?php echo ($heading == 'h6' ? 'selected' : ''); ?> value="h6"><?php _ex('H6', 'Widget', XT_TEXT_DOMAIN); ?></option>
			</select>						
		<p>
		
		<p>
		    <input class="button add-media" data-target="#<?php echo $this->get_field_id( 'description' ); ?>" type="button" value="<?php echo __("Add Media", XT_TEXT_DOMAIN); ?>" />
		</p>

		<p><label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description:' , XT_TEXT_DOMAIN); ?></label>
		<textarea rows="6" class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_html($description); ?></textarea></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'textsize' ); ?>"><?php _e( 'Text Size:' , XT_TEXT_DOMAIN); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('textsize'); ?>" name="<?php echo $this->get_field_name('textsize'); ?>">
				<option <?php echo ($textsize == 'normal' ? 'selected' : ''); ?> value="normal"><?php _ex('Normal', 'Widget', XT_TEXT_DOMAIN); ?></option>
				<option <?php echo ($textsize == 'large' ? 'selected' : ''); ?> value="large"><?php _ex('Large', 'Widget', XT_TEXT_DOMAIN); ?></option>
			</select>						
		<p>
			
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $bordered ); ?> id="<?php echo $this->get_field_id( 'bordered' ); ?>" name="<?php echo $this->get_field_name( 'bordered' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'bordered' ); ?>"><?php _e( 'Bordered Box' , XT_TEXT_DOMAIN); ?></label>
		</p>
		
<?php
	}
}

