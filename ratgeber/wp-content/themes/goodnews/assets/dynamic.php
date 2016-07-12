<?php

$xt_styles = new XT_Dynamic_Styles(XT_THEME_ID);

$cache_key = 'xt_custom_css_'.(!empty($post_id) ? $post_id : "global");

$xt_custom_css = wp_cache_get($cache_key);

if(empty($xt_custom_css)) {
	
	if(!empty($post_id)) {
	
		$layout = $xt_styles->get_option('main-layout');
	
		$xt_styles->useOptions(false);
		
		$parents = get_post_ancestors($post_id);
	
	
		// Body Background
		
		$background_id = get_field('background', $post_id);
		$background_color = get_field('background_color', $post_id);

		$background_repeat = get_field('background_repeat', $post_id);
		$background_size = get_field('background_size', $post_id);
		$background_position = get_field('background_position', $post_id);
		$background_attachment = get_field('background_attachment', $post_id);
			
		$post_parents = $parents;	
		while(empty($background_id) && empty($background_color) && !empty($post_parents)) {
	
			$p_post_id = array_pop($post_parents);
			$background_id = get_field('background', $p_post_id);
			$background_color = get_field('background_color', $p_post_id);
		}
	
		if(!empty($background_id) || !empty($background_color)) {
	
			if(!empty($background_id)) {
				$background_url = wp_get_attachment_image_src( $background_id, 'full' );
				$background_url = $background_url[0];
			}else{
				$background_url = 'none';
			}
	
			$background = array(
				'background-image' => $background_url,
				'background-repeat' => $background_repeat,
				'background-size' => $background_size,
				'background-position' => $background_position,
				'background-attachment' => $background_attachment,
				'background-color' => $background_color
			);
	
			if($layout == 'boxed') {
				
				$xt_styles->setBackground('body', $background);
				
			}else{
				
				$xt_styles->setBackground('#inner_wrapper', $background);
				
			}	
	
		}
	
		// Body Background Overlay
		
		$background_overlay_color = get_field('background_overlay_color', $post_id);
		$background_overlay_transparency = get_field('background_overlay_transparency', $post_id);
	
		$post_parents = $parents;	
		while(empty($background_overlay_color) && empty($background_overlay_transparency) && !empty($post_parents)) {
	
			$p_post_id = array_pop($post_parents);
			$background_overlay_color = get_field('background_overlay_color', $p_post_id);
			$background_overlay_transparency = get_field('background_overlay_transparency', $p_post_id);
		}
		
		if(!empty($background_overlay_color)) {
			
			if(!isset($background_overlay_transparency)) {
				$background_overlay_transparency = 0.7;
			} 
			$rgba = xt_hex2rgba($background_overlay_color, $background_overlay_transparency);
			$xt_styles->set('body .body_overlay', 'background-color', $rgba); 
		
		}

		if($layout == 'boxed') {
			
			// Inner Content Background
			
			$content_background_color = get_field('content_background_color', $post_id);
			$content_background_transparency = get_field('content_background_transparency', $post_id);
		
			$post_parents = $parents;	
			while(empty($content_background_color) && empty($content_background_transparency) && !empty($post_parents)) {
		
				$p_post_id = array_pop($post_parents);
				$content_background_color = get_field('content_background_color', $p_post_id);
				$content_background_transparency = get_field('content_background_transparency', $p_post_id);
			}
			
			if(!empty($content_background_color)) {
				
				if(!isset($content_background_transparency)) {
					$content_background_transparency = 0;
				} 
				$rgba = xt_hex2rgba($content_background_color, $content_background_transparency);
				$xt_styles->set('#inner_wrapper', 'background-color', $rgba); 
			}
		
		}
	}
	
	$global_custom_css = $xt_styles->get_option('custom_css');
	$xt_styles->setCustomCss($global_custom_css);
	
	$xt_custom_css = $xt_styles->render(true);
	if(!empty($xt_custom_css))
		wp_cache_add( $cache_key, $xt_custom_css, 'xt_custom_css');
		
}		