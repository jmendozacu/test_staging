<?php
	
function xt_theme_vc_widget_sidebar($atts, $content = null) {

	$content = preg_replace('\<h2 class\=\"widgettitle\"\>(.+?)\<\/h2\>', '<h3 class="widgettitle">$1</h3>', $content);
	return $content;
}
add_filter('vc_theme', 'xt_theme_vc_widget_sidebar');


function xt_vc_set_as_theme() {
    vc_set_as_theme(true);
}
add_action( 'vc_before_init', 'xt_vc_set_as_theme' );


function xt_vc_google_fonts() {
	
	$fonts_data = xt_get_google_fonts();
	
	$fonts = array();	
	foreach($fonts_data as $item) {
		
		$font = new stdClass;
		$font->font_family = $item->family;
		$font->font_styles = implode(",", $item->variants);
		
		$styles = array();
		foreach($item->variants as $variant) {
		
			if($variant == "regular") {
			
				$styles[] = '400 regular:400:normal';
				
			}else if($variant == "italic") {	
			   
				$styles[] = '400 italic:400:italic';
				
			}else if(is_numeric(substr($variant, 0, 2))) {
			
				if(strlen($variant) == 3) {
					
					if($variant <= 300) {
						$weight = "light";
					}else{
						$weight = "bold";
					}
					$styles[] = $variant.' '.$weight.' regular:'.$variant.':normal';
					
				}else{
				
					$variant_prefix = substr($variant, 0, 3);
					$variant_suffix = substr($variant, 3);
					
					$styles[] = $variant_prefix.' '.$variant_suffix.':'.$variant_prefix.':'.$variant_suffix;
				}
			
			}
		}
		$font->font_types = implode(",", $styles);
		
		$fonts[] = $font;
		
	}
	
	return $fonts;
}

$google_fonts_api_key = xt_option('google_fonts_api_key');
if(!empty($google_fonts_api_key)) {
	add_filter('vc_google_fonts_get_fonts_filter', 'xt_vc_google_fonts');
}	