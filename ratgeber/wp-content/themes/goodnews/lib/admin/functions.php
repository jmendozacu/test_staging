<?php

/*
 *
 * Get Theme Options Array
 *
 */

function xt_options() {

	$options = get_option(XT_THEME_ID);
	return $options;
}


/*
 *
 * Get Theme Option by ID
 *
 * Optional Params:
 * $key, if value is an array get by array key
 */

function xt_option($id, $key = null, $default = null) {

	$options = xt_options();
	

	$value = "";
	if(isset($options[$id]) && $options[$id] != "") {
		
		$value = $options[$id];
		
	}else if(!isset($options[$id]) && !empty($default)) {
		
		$value = $default;
	}
	
	
	if($key && is_array($value) && isset($value["$key"]))
		$value = $value[$key];

	if(empty($key)) {

		if(is_array($value)) {
								
			if(!empty($value["color"]) && empty($value["font-family"])) {
			
				if(!empty($value["rgba"])) {
					$value = $value["rgba"];
				}else{
					$value = $value["color"];
				}	
				
			}	
	
		}
	}	
		
	if(!is_array($value)) {
		$value = trim($value);
	}	
				
	return $value;

}

function xt_value_format($value, $type) {
	
	if($type == 'px') {
		
		$value = str_replace('px', '', $value).'px';
		
	}else if($type == 'px-int') {
		
		$value = (int)str_replace('px', '', $value);
		
	}
	
	return $value;
}

function xt_get_option_type($id) {
	
	global $reduxConfig;

	if(!empty($reduxConfig->fields_types[$id])) {
		return $reduxConfig->fields_types[$id];
	}
	
	return "";
}

function xt_page_for_content_update ( $option ) {

	set_transient(XT_THEME_ID . '_flush_rules', true);

}

add_action('update_option_' . XT_THEME_ID, 'xt_page_for_content_update', 10, 1);


function xt_get_sidebars() {
	
	global $wp_registered_sidebars;
	$options = array();
	$options[""] = __('- None -', XT_TEXT_DOMAIN);
	foreach ( $wp_registered_sidebars as $registered_sidebar ) {
		$options[ $registered_sidebar['id'] ] = $registered_sidebar['name'];
	}
	
	return $options;	
}

function xt_get_fontawsome_icons() {

	require_once(XT_ADMIN_DIR."/redux-extensions/extensions/fontawesome/field_fontawesome.php");
	$fontawesome = new Redux_Options_fontawesome(array('id'=>'fontawsome_vc_icons'), '', null);
	return $fontawesome->icons;
}

function xt_get_patterns($invertKeyVal = false) {

	$files = glob(XT_ASSETS_DIR.'/patterns/*.png');
	foreach($files as $file) {
	
		$key = str_replace(".png", "", basename($file));
		$value = ucwords(str_replace(array("-", "_"), " ", $key));
		
		if($invertKeyVal) {
		
			$patterns[$value] = $key;
			
		}else{
		
			$patterns[$key] = $value;
		}	
	}
	return $patterns;	
}


function xt_get_google_fonts() {
	
	$cache_key = 'xt_google_fonts';
	$cache_expire =  (60 * 60 * 24) * 7; // 1 week
	
	$fonts_data = get_transient($cache_key);
	if($fonts_data !== false) {
	
		$fonts_data = unserialize($fonts_data);

	}else{
		
		$api_key = xt_option('google_fonts_api_key');
		$url = "https://www.googleapis.com/webfonts/v1/webfonts?key=".$api_key;
		$json = xt_get_url_contents($url);

		$fonts_data = json_decode($json);
		if(!empty($fonts_data->items)) {
			$fonts_data = $fonts_data->items;
			set_transient($cache_key, serialize($fonts_data), $cache_expire);
		}else{
			$fonts_data	= array();
		}	
	}
		
	if(empty($fonts_data))
		return array();

	return $fonts_data;
}

function xt_get_css_animations() {

 	$css_animations = array(
	    'None' => '',
	    'Left to Right Effect' => 'wpb_animate_when_almost_visible wpb_left-to-right',
	    'Right to Left Effect' => 'wpb_animate_when_almost_visible wpb_right-to-left',
	    'Top to Bottom Effect' => 'wpb_animate_when_almost_visible wpb_top-to-bottom',
	    'Bottom to Top Effect' => 'wpb_animate_when_almost_visible wpb_bottom-to-top',
	    'Appear From Center' => 'wpb_animate_when_almost_visible wpb_appear'
	);
	
	return $css_animations;
}

	
function xt_get_css_filters() {
	
			
	$filters = array(
		__('No Filter', XT_TEXT_DOMAIN) => '',
		__('Grayscale', XT_TEXT_DOMAIN) => 'filter-grayscale',
		__('Grayscale On Hover', XT_TEXT_DOMAIN) => 'filter-grayscale-hover',
		__('Blur', XT_TEXT_DOMAIN) => 'filter-blur',
		__('Blur On Hover', XT_TEXT_DOMAIN) => 'filter-blur-hover',
		__('Saturate', XT_TEXT_DOMAIN) => 'filter-saturate',
		__('Saturate On Hover', XT_TEXT_DOMAIN) => 'filter-saturate-hover',
		__('Sepia', XT_TEXT_DOMAIN) => 'filter-sepia',
		__('Sepia On Hover', XT_TEXT_DOMAIN) => 'filter-sepia-hover',
		__('Hue Rotation', XT_TEXT_DOMAIN) => 'filter-hue-rotate',
		__('Hue Rotation On Hover', XT_TEXT_DOMAIN) => 'filter-hue-rotate-hover',
		__('Invert', XT_TEXT_DOMAIN) => 'filter-invert',
		__('Invert On Hover', XT_TEXT_DOMAIN) => 'filter-invert-hover',
		__('Brightness', XT_TEXT_DOMAIN) => 'filter-brightness',
		__('Brightness On Hover', XT_TEXT_DOMAIN) => 'filter-brightness-hover',
		__('Contrast', XT_TEXT_DOMAIN) => 'filter-contrast',
		__('Contrast On Hover', XT_TEXT_DOMAIN) => 'filter-contrast-hover'
	);

	return $filters;

}
	
function xt_get_opacities() {
	
	$opacity = array(100,90,80,70,60,50,40,30,20,10,0);
	return $opacity;
}	

function xt_get_link_targets() {

	$targets = array(
		__( 'Same window', 'js_composer' ) => '_self',
		__( 'New window', 'js_composer' ) => "_blank"
	);
	return $targets;
}	

function xt_get_widget_layouts() {

	$layouts = array(
		'1' => array( 'alt' => '1 Column', 'img' => ReduxFramework::$_url .'assets/img/column_1.png' ),
		'2' => array( 'alt' => '2 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_2.png' ),
		'3' => array( 'alt' => '3 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_3.png' ),
		'4' => array( 'alt' => '4 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_4.png' ),
		'6' => array( 'alt' => '6 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_6.png' ),
		'custom_10_2' => array( 'alt' => '10/12 | 2/10 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_10_2.png' ),
		'custom_2_10' => array( 'alt' => '2/10 | 10/12 Columns', 'img' =>ReduxFramework::$_url .'assets/img/column_2_10.png' ),		
		'custom_6_3_3' => array( 'alt' => '6/12 | 3/12 | 3/12 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_6_3_3.png' ),
		'custom_3_3_6' => array( 'alt' => '3/12 | 3/12 | 6/12 Columns', 'img' =>ReduxFramework::$_url .'assets/img/column_3_3_6.png' ),
		'custom_4_4_2_2' => array( 'alt' => '4/12 | 4/12 | 2/12 | 2/12 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_4_4_2_2.png' ),
		'custom_2_2_4_4' => array( 'alt' => '2/12 | 2/12 | 4/12 | 4/12 Columns', 'img' =>ReduxFramework::$_url .'assets/img/column_2_2_4_4.png' ),
		'custom_2_2_2_6' => array( 'alt' => '2/12 | 2/12 | 2/12 | 6/12 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_2_2_2_6.png' ),
		'custom_6_2_2_2' => array( 'alt' => '6/12 | 2/12 | 2/12 | 2/12 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_6_2_2_2.png' ),
		'custom_4_2_2_2_2' => array( 'alt' => '4/12 | 2/12 | 2/12 | 2/12 | 2/12 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_4_2_2_2_2.png' ),
		'custom_2_2_2_2_4' => array( 'alt' => '2/12 | 2/12 | 2/12 | 2/12 | 4/12 Columns', 'img' => ReduxFramework::$_url .'assets/img/column_2_2_2_2_4.png' ),
	);
	return $layouts;				
}


function xt_get_post_formats($invertKeyVal = false) {
	
	$post_formats = array();
	$formats = XT_Theme::$post_formats;
	foreach($formats as $format) {
		$key = 'post-format-'.$format;
		$value = ucfirst($format);
		
		if($invertKeyVal) {
			$post_formats[$value] = $key;
		}else{
			$post_formats[$key] = $value;
		}	
	}	
	
	return $post_formats;
}

function xt_get_post_types($invertKeyVal = false) {
	
	$post_types = get_post_types(array(
	   'public' => true
	));
	
	if($invertKeyVal) {
		$post_types = array_flip($post_types);
	}
	return $post_types;
}

function xt_get_taxonomies($invertKeyVal = false) {
	
	$taxonomies = get_taxonomies(); 
	
	if($invertKeyVal) {
		$taxonomies = array_flip($taxonomies);
	}
	return $taxonomies;
}

function xt_get_custom_image_sizes($invertKeyVal = false) {
	
	$image_sizes = array();
	$sizes = XT_Theme::$image_sizes;
	
	foreach($sizes as $key => $size) {
	
		$size = $size['name'].' - '.$size['width'].' x '.$size['height'];
		
		if($invertKeyVal) {
			$image_sizes[$size] = $key;
		}else{
			$image_sizes[$key] = $size;
		}	
	}
	return $image_sizes;
}

function xt_get_reset_array($array, $newValue) {
	
	foreach($array as $key => $value) {
		$array[$key] = $newValue;
	}
	
	return $array;
}

function xt_get_views_meta_key() {
	
	return xt_option('views_meta_key', null, 'xt_post_views_count');
}

function xt_views_reset_all() {
	check_ajax_referer( 'xt-views-reset-all', 'security' );
	global $wpdb;
	// reset postmeta fields
	
	$meta_key = xt_get_views_meta_key();
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = %d WHERE meta_key = %s", 0, $meta_key ) );

	die(__("Views have been reset", XT_TEXT_DOMAIN));
}
add_action('wp_ajax_xt_views_reset_all',  'xt_views_reset_all');