<?php

/**
 * XT_Advanced_Widget Class
 *
 * @since 1.0
 * @see   XT_Advanced_Widget
 */

class XT_Advanced_Widget
{
	/**
	 * Widget Defaults
	 */

	protected $id;
	protected $class;
	protected $query;
	protected $instance;
	protected $instance_id;
	protected $cache_enabled = false;
	protected $cache_expire; // 1 hour
	protected $cache_prefix = 'xt_vc_widget_';

	/**
	 * Register widget with WordPress.
	 */

	function __construct ()
	{
		$this->cache_enabled = (bool)xt_option('cache_vc_widgets');
		$this->cache_expire = ((int)xt_option('cache_vc_widgets_timeout') * 60);
		
		add_shortcode( $this->id, array($this, 'shortcode') );
		
		if (function_exists('vc_map')) {
			add_action('vc_before_init', array($this, 'vcMap') );
		}	
		
		add_action( 'save_post', array($this, 'flushCache'), 10, 3 );
				
	}
	
	function setInstance($params = array()) {
		
		$this->instance = $params;
		$this->instance_id = $this->cache_prefix.$this->id.'_'.hash('md5', serialize($params));
	}
	
	function getCache() {
		
		if(!$this->cache_enabled)
			return false;
			
		return get_transient($this->instance_id);
       
	}
	
	function setCache($output) {
		
		if(!$this->cache_enabled)
			return false;
			
		set_transient($this->instance_id, $output, $this->cache_expire );
	}
	
	function flushCache($post_id, $post, $update)  {
		
		if(!$this->cache_enabled)
			return false;
			
		global $wpdb;
		
		if ( $post->post_type != 'page' && $post->post_type != 'post') {
	        return;
	    }
    
		$transients = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_'.$this->cache_prefix));
		foreach ($transients as $transient) {

			$key = str_replace('_transient_', '', $transient);
			delete_transient($key);
		}
	}	
	
	function widgetStart() {
		
		if(!empty($this->instance['bordered'])) {
			echo '<div class="panel">';
		}
		
	}	
	
	function widgetEnd() {
		
		if(!empty($this->instance['bordered'])) {
			echo '</div>';
		}		
	}	

	function getCssAnimation() {
		
		$css_animations = array(
			'left-to-right' 	=> 'wpb_animate_when_almost_visible wpb_left-to-right',
			'right-to-left' 	=> 'wpb_animate_when_almost_visible wpb_right-to-left',
			'top-to-bottom' 	=> 'wpb_animate_when_almost_visible wpb_top-to-bottom',
			'bottom-to-top' 	=> 'wpb_animate_when_almost_visible wpb_bottom-to-top',
			'appear' 			=> 'wpb_animate_when_almost_visible wpb_appear'
		);
		
		if(!empty($this->instance['css_animation'])) {
			return $this->instance['css_animation'];
		}
		
		return '';
		
	}

    function getView() {
        $viewData = $this->instance["view"];
        $viewData = explode("|", $viewData);
        if(!empty($viewData[1])) {
            return $viewData[0];
        }
        if(is_array($viewData)) {
	        return $viewData[0];
        }
        return $viewData;
    }
    
    function getViewSettings() {
    
        $viewData = $this->instance["view"];
        $viewData = explode("|", $viewData);
        if(!empty($viewData[1])) {
        
            $viewSettings = explode(",", $viewData[1]);
            $settings = array();
            
            foreach($viewSettings as $setting) {
            
                $keyval = explode(":", $setting);
                $key = $keyval[0];
                $val = $keyval[1];
                $settings[$key] = $val;
                
            }
            return $settings;
        }
        
        return false;
    }

	function actionLink ( $object_id = null, $ext_link = null, $title= '' ) {
	
		if ( $object_id || $ext_link )
		{
			$url = !empty($ext_link) ? $ext_link : get_permalink($object_id);
			$target = !empty($ext_link) ? "_blank" : "_self";
			
			if(!empty($url))
				return '<a target="'.$target.'" href="' . $url . '" class="widget-action"><i class="fa fa-angle-double-right"></i> ' . $title . '</a>';
		}
	
		return '';
	}


}
