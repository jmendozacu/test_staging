<?php
class XT_Widget extends WP_Widget {

	public $widget_options = array();
	
	function __construct ($id_base, $name, $widget_options = array(), $control_options = array())
	{
		parent::__construct($id_base, $name, $widget_options, $control_options);
		$this->widget_options = $widget_options;
		
		add_filter('widget_title', array(&$this, 'html_title'), 10, 3);
	
	}
	
	function get_default_args($css_animation = '') {
		
		$class = $this->widget_options["classname"];
		
		return array(
			'widget_id'=>'arbitrary-instance-'.uniqid(), 
			'before_widget'=>'<div class="widget '.$class.' '.$css_animation.'">', 
			'after_widget'=>'</div>',
			'before_title' => '<h2 class="widgettitle">%1',
			'after_title' => '</h2>'
		);
		
	}
	
	function action_link ( $object_id = null, $ext_link = null, $title= '' ) {
	
		if ( $object_id || $ext_link )
		{
			$url = !empty($ext_link) ? $ext_link : get_permalink($object_id);
			$target = !empty($ext_link) ? "_blank" : "_self";
			
			if(!empty($url))
				return '<a target="'.$target.'" href="' . $url . '" class="widget-action"><i class="fa fa-angle-double-right"></i> ' . $title . '</a>';
		}
	
		return '';
	}
	
	function fix_args(&$args) {
		
		if(strpos($args['widget_id'],'xt-text') === false) {
			$args['before_title'] = str_replace(array('h2', '%1'),array('h3', ''),$args['before_title']);
			$args['after_title'] = str_replace('h2','h3',$args['after_title']);
		}	
		
	}

	function set_border(&$args) {
		
		$args['before_widget'] .= '<div class="panel">';
		$args['after_widget'] = '</div>'.$args['after_widget'];
		
	}	

	/**
	 * Render HTML output
	 */
	
	function html_title ( $title = '', $instance = array(), $id_base = 0 )
	{
		$title = htmlspecialchars_decode( $title );
		$title = strip_tags( $title, '<a><b><i><strong><em>' );
	
		return $title;
	}
	
	
	public static function get_object_options($selected = null, $post_type = 'page') {
		
		$posts = get_posts(array('post_type' => $post_type, 'posts_per_page' => -1, 'suppress_filters' => false));
		$options = '';
		
		$options .= '<option></option>';
		foreach($posts as $p) {
		
			$options .= '<option value="'.$p->ID.'"'.(($p->ID == $selected) ? ' selected="selected"' : '').'>'.esc_attr($p->post_title).'</option>';
		
		}

		return $options;
	}

	public static function get_taxonomy_options($selected = null, $taxonomy = 'category') {
		
		$terms = get_terms($taxonomy);
		$options = '';
		
		$options .= '<option></option>';
		foreach($terms as $t) {
		
			$options .= '<option value="'.$t->term_id.'"'.(!is_array($selected) && ($t->term_id == $selected) || is_array($selected) && in_array($t->term_id, $selected) ? ' selected="selected"' : '').'>'.esc_attr($t->name).'</option>';
		
		}

		return $options;
	}

	public static function get_post_format_options($selected = null) {
		
		$formats = xt_get_post_formats();
		
		$options = '';
		
		foreach($formats as $format => $format_name) {

			$options .= '<option value="'.$format.'"'.( is_array($selected) && in_array($format, $selected) ? ' selected="selected"' : '').'>'.esc_attr($format_name).'</option>';
		
		}

		return $options;
	}	

	
}