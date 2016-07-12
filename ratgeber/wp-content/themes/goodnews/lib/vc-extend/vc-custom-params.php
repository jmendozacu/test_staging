<?php

if(function_exists('add_shortcode_param')) {

	function xt_vc_setting_field_post_multiselect($settings, $value) {
	
		$settings["type"] = "dropdown";
		$show_id =  (!empty($settings["show_id"]) ? $settings["show_id"] : false); 
		$dependency = vc_generate_dependencies_attributes($settings);
		
		$value_exploded = array();
	    if(!empty($value)) {
		    
		    $value_exploded = explode(",", $value);
	    }
	    
		$all_items = get_posts(array(
			  'post_type' => (!empty($settings["post_type"]) ? $settings["post_type"] : 'page')
			, 'posts_per_page' => -1
			, 'no_found_rows'  => true
			, 'suppress_filters' => false
		));
		
		$dropdown = '<div class="post_multiselect_block">';
		$dropdown .= '<select name="'.$settings['param_name'].'_select" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].'" multiple="multiple">';
				
		foreach($all_items as $item) {
				
			$dropdown .= '<option value="'.$item->ID.'"'.(is_array($value_exploded) && in_array($item->ID, $value_exploded) ? ' selected="selected"' : '').'">'.($show_id ? ' ['.$item->ID.'] ' : '').esc_attr($item->post_title).'</option>';
				
		}
				
		$dropdown .= '</select>';
		$dropdown .= '<input name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$value.'" '.$dependency.' />';		
		$dropdown .= '</div>';	
		
         	

		return $dropdown;
		
	}
	add_shortcode_param('post_multiselect', 'xt_vc_setting_field_post_multiselect', XT_PARENT_URL.'/lib/vc-extend/assets/settings/post_multiselect.js');
	

	
	function xt_setting_field_post_select($settings, $value) {
	
		$settings["type"] = "dropdown";
		$show_id =  (!empty($settings["show_id"]) ? $settings["show_id"] : false); 
		$dependency = vc_generate_dependencies_attributes($settings);
	    
		$all_items = get_posts(array(
			  'post_type' => (!empty($settings["post_type"]) ? $settings["post_type"] : 'page') 
			, 'posts_per_page' => -1
			, 'no_found_rows'  => true
			, 'suppress_filters' => false
		));
		
		$dropdown = '<div class="post_multiselect_block">';
		$dropdown .= '<select name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].'" '.$dependency.'>';
			
		$dropdown .= '<option></option>';		
		foreach($all_items as $item) {
				
			$dropdown .= '<option value="'.$item->ID.'"'.(($item->ID == $value) ? ' selected="selected"' : '').'">'.($show_id ? ' ['.$item->ID.'] ' : '').esc_attr($item->post_title).'</option>';
				
		}
				
		$dropdown .= '</select>';
		$dropdown .= '</div>';	

		return $dropdown;
		
	}
	add_shortcode_param('post_select', 'xt_setting_field_post_select', XT_PARENT_URL.'/lib/vc-extend/assets/settings/post_multiselect.js');



	function xt_vc_setting_field_taxonomy_multiselect($settings, $value) {
	
		$settings["type"] = "dropdown";
		$dependency = vc_generate_dependencies_attributes($settings);
		
		$value_exploded = array();
	    if(!empty($value)) {
		    
		    $value_exploded = explode(",", $value);
	    }
	    

		$taxonomy = (!empty($settings["taxonomy"]) ? $settings["taxonomy"] : 'category');
		$all_items = get_terms($taxonomy);
		
		$dropdown = '<div class="taxonomy_multiselect_block">';
		$dropdown .= '<select name="'.$settings['param_name'].'_select" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].'" multiple="multiple">';
				
		foreach($all_items as $item) {
				
			$dropdown .= '<option value="'.$item->term_id.'"'.(is_array($value_exploded) && in_array($item->term_id, $value_exploded) ? ' selected="selected"' : '').'">'.esc_attr($item->name).'</option>';
				
		}
				
		$dropdown .= '</select>';
		$dropdown .= '<input name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$value.'" '.$dependency.' />';		
		$dropdown .= '</div>';	
		
         	

		return $dropdown;
		
	}
	add_shortcode_param('taxonomy_multiselect', 'xt_vc_setting_field_taxonomy_multiselect', XT_PARENT_URL.'/lib/vc-extend/assets/settings/post_multiselect.js');
	
	
	function xt_vc_setting_field_post_formats($settings, $value) {

		$settings["type"] = "dropdown";
		$dependency = vc_generate_dependencies_attributes($settings);

		$value_exploded = array();
	    if(!empty($value)) {
		    
		    $value_exploded = explode(",", $value);
	    }
	    
				
		$formats = xt_get_post_formats();
		
		$dropdown = '<div class="post_formats_block">';
		$dropdown .= '<select name="'.$settings['param_name'].'_select" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].'" multiple="multiple">';
		
		foreach($formats as $format => $format_name) {

			$dropdown .= '<option value="'.$format.'"'.( is_array($value_exploded) && in_array($format, $value_exploded) ? ' selected="selected"' : '').'>'.esc_attr($format_name).'</option>';
		
		}
		$dropdown .= '</select>';
		$dropdown .= '<input name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-textinput '.$settings['type'].'_field" type="hidden" value="'.$value.'" '.$dependency.' />';		
		$dropdown .= '</div>';	
		
		return $dropdown;

		
	}
	add_shortcode_param('post_formats', 'xt_vc_setting_field_post_formats', XT_PARENT_URL.'/lib/vc-extend/assets/settings/post_multiselect.js');			
}	