<?php
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = $css = $xt_border_top = $xt_row_id = $xt_row_type = $xt_parallax = $xt_smooth_scroll =  $xt_overlay_color = $xt_overlay_pattern = $xt_pattern_opacity = $xt_overlay_gradient_top = $xt_overlay_gradient = $xt_overlay_gradient_direction = $xt_overlay_gradient_color = $xt_bg_position = $xt_bg_attach = $xt_bg_video = $xt_bg_video_mp4 = $xt_bg_video_webm = $xt_bg_video_ogv = $xt_bg_video_poster = $xt_bg_video_position = '';

extract(shortcode_atts(array(
    'el_class'        			=> '',
    'bg_image'        			=> '',
    'bg_color'        			=> '',
    'bg_image_repeat' 			=> '',
    'font_color'      			=> '',
    'padding'         			=> '',
    'margin_bottom'   			=> '',
    'css' 			  			=> '',
    'xt_row_id'   	  			=> '',
    'xt_row_type'     			=> '',
    'xt_border_top'   			=> '',
    'xt_parallax'       		=> '',
    'xt_smooth_scroll'			=> '',
    'xt_overlay_color' 			=> '',
    'xt_overlay_pattern' 		=> '',
    'xt_pattern_opacity' 		=> '',
    'xt_bg_position'			=> '',
    'xt_overlay_gradient' 		=> '',
    'xt_overlay_gradient_direction' => '',
    'xt_overlay_gradient_color' 	=> '',
    'xt_bg_attach' 				=> '',
    'xt_bg_video'   			=> '',
    'xt_bg_video_mp4'   		=> '',
    'xt_bg_video_webm'	 		=> '',
    'xt_bg_video_ogv'	 		=> '',
    'xt_bg_video_poster' 		=> '',
    'xt_bg_video_position' 		=> '',    
), $atts));


wp_enqueue_script( 'wpb_composer_front_js' );


if(empty($xt_row_type))
	$xt_row_type = "in-container";

$el_class = $this->getExtraClass($el_class);

$custom_css_class = vc_shortcode_custom_css_class( $css, ' ' );
$custom_styles = "";

if($xt_parallax == "yes") {
	$replacements = array(
		".".trim($custom_css_class)."{",
		"}",
		"!important"
	);
	$custom_css_class = '';
	$css = str_replace($replacements, '', $css);
	$custom_styles = ' style="'.$css.'"';
}

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_row wpb_row '. ( $this->settings('base')==='vc_row_inner' ? 'vc_inner ' : '' ) . get_row_css_class() . $el_class . $custom_css_class, $this->settings['base'], $atts );


$css_class .= " ".$xt_row_type;

$has_background = false;
if(!empty($css) && strpos($css, "background") !== false) {
	$css_class .= " has-background";
	$has_background = true;
}


if($xt_smooth_scroll == 'yes') {
	$css_class .= " enable_smooth_scroll";
}

if($has_background) {
	if(!empty($xt_bg_position)) {
		$css_class .= " bg-position-".$xt_bg_position;
	}
	
	if(!empty($xt_bg_attach)) {
		$css_class .= " bg-attach-".$xt_bg_attach;
	}
}

if( !empty($xt_overlay_color) || !empty($xt_overlay_pattern)){

	$css_class .= " has-overlay";
	$xt_overlay_color = 'background-color: '.$xt_overlay_color.';';
	
	if(!empty($xt_overlay_gradient)) {
		
		$css_class .= ' has-gradient '.$xt_overlay_gradient_direction;
	
	}
	
}

if(!empty($xt_overlay_pattern)) {
	$xt_overlay_pattern = 'background-image: url('.XT_ASSETS_URL.'/patterns/'.$xt_overlay_pattern.'.png);';
}

if(!empty($xt_pattern_opacity)) {
	$xt_pattern_opacity = floatval($xt_pattern_opacity / 100);
	$xt_pattern_opacity = 'opacity: '.$xt_pattern_opacity.';';
}


if(!empty($xt_bg_video)) {

	include_once(XT_PARENT_DIR.'/lib/classes/Mobile_Detect.php');
	$detect = new Mobile_Detect;
		
	$xt_parallax = "";
	
	$css_class .= " has-video";

	$video_image = "";
	$video_poster = "";
	if(!empty($xt_bg_video_poster)) {
		$data = wp_get_attachment_image_src($xt_bg_video_poster, 'large');
		if(!empty($data[0])) {
			$video_image = $data[0];
			$video_poster = 'poster="'.esc_url($video_image).'"';
		}	
	}

	$xt_bg_video = '';
	
	if (!$detect->isMobile() && !$detect->isTablet()) {
	
		$xt_bg_video .= '<video class="row-video" data-position="'.esc_attr($xt_bg_video_position).'" width="100%" height="100%" '.$video_poster.' preload="auto" loop="loop" autoplay="autoplay">';

		if(!empty($xt_bg_video_mp4)) {
			$xt_bg_video .= '<source type="video/mp4" src="'.esc_url($xt_bg_video_mp4).'">';
		}

		if(!empty($xt_bg_video_webm)) {
			$xt_bg_video .= '<source type="video/webm" src="'.esc_url($xt_bg_video_webm).'">';
		}
		
		if(!empty($xt_bg_video_ogv)) {
			$xt_bg_video .= '<source type="video/ogg" src="'.esc_url($xt_bg_video_ogv).'">';
		}
								
		$xt_bg_video .= '</video>';
		
	
	}else if(!empty($video_poster)){
		
		$xt_bg_video .= '<div class="row-video image-fallback" style="background-image:url('.esc_url($video_image).');"></div>';
	}
	
}	
	

if($xt_parallax == "yes") {
	$css_class .= " has-parallax";
}

	
		
$output = '<div '.(!empty($xt_row_id) ? 'id="scrollto_'.$xt_row_id.'"' : '').' class="'.esc_attr($css_class).'"'.$custom_styles.'>';

	if(!empty($xt_border_top)) {
		$output .= '<hr>';
	}

    if( !empty($xt_bg_video) || !empty($xt_overlay_color) || !empty($xt_overlay_pattern)) {
    	$output .= '<div class="row-wrap">';


		if(!empty($xt_bg_video)){
			$output .= $xt_bg_video;
		}
		
		if( !empty($xt_overlay_color)){
       	 	$output .= '	<div class="row-overlay" style="'.$xt_overlay_color.';"></div>';
       	}
       	if( !empty($xt_overlay_pattern)){
	 		$output .= '	<div class="row-pattern" style="'.$xt_overlay_pattern.' '.$xt_pattern_opacity.';"></div>';
       	}
       	if( !empty($xt_overlay_gradient)){
	 		$output .= '	<div class="row-gradient"></div>';
       	}
    }
    
	$output .= wpb_js_remove_wpautop($content);

    if( !empty($xt_bg_video) || !empty($xt_overlay_color) || !empty($xt_overlay_pattern)) {
    
    	$output .= '</div>';
    }
       
$output .= '</div>';

$output .= $this->endBlockComment('row');

echo $output;