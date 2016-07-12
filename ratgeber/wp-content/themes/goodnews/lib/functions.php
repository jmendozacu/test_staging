<?php	
function xt_get_page_ID() {
	
	global $wp_query, $post; 

	$page_id = false;
	
	if( is_home() && get_option('page_for_posts') ) { 
		
		$page_id = get_option('page_for_posts');
	
	} elseif( is_front_page() && get_option('page_on_front')) {
		
		$page_id = get_option('page_on_front');

	}else if(function_exists('is_shop') && is_shop() && get_option('woocommerce_shop_page_id') != '') {
		
		$page_id = get_option('woocommerce_shop_page_id');
	
	}else if(function_exists('is_cart') && is_cart() && get_option('woocommerce_cart_page_id') != '') {
		
		$page_id = get_option('woocommerce_cart_page_id');
	
	}else if(function_exists('is_checkout') && is_checkout() && get_option('woocommerce_checkout_page_id') != '') {
		
		$page_id = get_option('woocommerce_checkout_page_id');
	
	}else if(function_exists('is_account_page') && is_account_page() && get_option('woocommerce_myaccount_page_id') != '') {
		
		$page_id = get_option('woocommerce_myaccount_page_id');
			
	}else if($wp_query && !empty($wp_query->queried_object) && !empty($wp_query->queried_object->ID)) {
	
		$page_id = $wp_query->queried_object->ID;
		
	}else if(!empty($post->ID)) { 
		
		$page_id = $post->ID; 
		
	}

	return $page_id;
}


/**
 * Get the page assigned to be the content type archive.
 *
 * @since 1.5.0
 * @todo Swap `get_pages()` with `WP_Query` directly to
 *       use `meta_query` to query an array of template names.
 */

function xt_get_page_for_post_type ( $post_type = '', $page_template = '' ) {

	if ( $post_type )
	{
		$page_id = xt_option('page_for_' . $post_type . 's');

		if ( empty($page_id) && ! empty($page_template) ) {
			$page = get_pages(array(
				'meta_key' => '_wp_page_template',
				'meta_value' => $page_template,
				'number' => 1
			));

			if ( ! empty($page) )
				$page_id = $page[0]->ID;
		}

		if ( $page_id )
			return $page_id;
	}

	return 0;
}


/**
 * Check if is login page
 */
 
function xt_is_login_page() {
	return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}


/**
 * Check if is endless template
 */
 
function xt_is_endless_template() {

	return (is_page_template('tpl-endless-posts.php')) || (!empty($_POST["template"]) && $_POST["template"] == 'endless-posts');
}


function xt_get_current_category() {
	
	$type = get_post_type();
	$category = false;
	
	if($type == 'post') 
		$category = get_query_var('cat');
	else{
		$term_slug = get_query_var( 'term' );
		$taxonomy = get_query_var( 'taxonomy' );
		$term = get_term_by('slug', $term_slug, $taxonomy);
		if(!empty($term))
			$category = $term->term_id;
	}
	return $category;		
}


/**
 * Check if page is used by any plugin and has special output
 * Ex: Woocommerce / Buddypress pages
 */
 
function xt_page_is_special() {
	
	return (function_exists('bp_is_blog_page') && !bp_is_blog_page()) || xt_is_woocommerce_page();
	
}


/**
 * Check if page has visual composer rows
 */
 
function xt_page_has_composer($p = null) {
		
	global $post;
	
	if(!empty($p)) {
		if(is_numeric($p)) {
			$p = get_post($p);
		}
		$content = $p->post_content;
	}else{
		$content = $post->post_content;
	}
	
	if(strpos($content, "vc_row") !== false) {
		return true;
	}
	return false;
}


/**
 * Check if comments are enabled
 */
 
function xt_comments_enabled() {
	
	global $post;
	
	$post_type = get_post_type();
	$post_id = get_the_ID();
	
	if(!empty($post_id)) {
		$comments_enabled = (bool)xt_option('comments_enabled') && comments_open($post_id);
	}else{
		$comments_enabled = (bool)xt_option('comments_enabled');
	}
	
	if($post_type == 'page') {
		$comments_on_pages = (bool)xt_option('comments_on_pages');
		$comments_enabled = (!$comments_on_pages) ? false : $comments_enabled;
	}	
	
	if(!empty($post_id) && xt_page_has_composer($post_id)) {
		$comments_on_vc_pages = (bool)xt_option('comments_on_vc_pages');
		$comments_enabled = (!$comments_on_vc_pages) ? false : $comments_enabled;
	}
	
	return $comments_enabled;
}

/**
 * Get selected comment system
 */
 
function xt_comments_system() {
	
	return $comments_system = xt_option('comments_type');
}


/**
 * Outputs a notice that a subject is deprecated.
 *
 * There is a hook `xt_deprecated` that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated
 * function.
 *
 * This function is to be used in every field that is deprecated.
 *
 * @since 1.6.0
 *
 * @param string $subject The function that was called
 * @param string $version The version of WordPress that deprecated the function
 * @param string $replacement Optional. The function that should have been called
 */

function xt_deprecated_notice( $subject, $version, $replacement = null ) {

	do_action( 'xt_deprecated', $subject, $replacement, $version );

	if ( ! is_null( $replacement ) )
		return sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.', XT_TEXT_DOMAIN), $subject, $version, $replacement );
	else
		return sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.', XT_TEXT_DOMAIN), $subject, $version );
}


function xt_get_posts($params) {

	$params = array_merge(array(
		"direction" => "prev",
		"cpage" => 1,
        "per_page" => 10, 
        "query" => "most-recent",
        "query_post_formats" => "0",
        "format" => "",
        "include_posts" => "",
        "orderby" => "date",
        "post_type" => 'post',
    ), $params);
    
    extract($params);

	$query_args = array(
		  'post_type'           => 'post'
		, 'no_found_rows'       => true
		, 'post_status'         => 'publish'
		, 'ignore_sticky_posts' => true
		, 'posts_per_page' 		=> $per_page
	);

	if(!empty($cpage)) {
	    $cpage = intval($cpage);
	    if(false === $cpage) {
	        $cpage = 1;
	    }
	    $query_args["paged"] = $cpage;
	}
		
	if(!empty($direction)) {
			    
	 	if($direction == "prev") {
			$direction = "DESC";
		}else{
			$direction = "ASC";
		}
	    $query_args["order"] = $direction;
	}	

	if(!empty($query)) {

		if($query == 'most-viewed') {
		
			$query_args['meta_key'] = xt_get_views_meta_key();
			$query_args['orderby'] = 'meta_value_num';
			
		}else if($query == 'most-liked') {
		
			$query_args['meta_key'] = '_votes_likes';
			$query_args['orderby'] = 'meta_value_num';
			
		}else if($query == 'most-discussed') {
		
			$query_args['orderby'] = 'comment_count';
			
			if(!empty($must_have_comments)) 
				$xt_global_where = "AND $wpdb->posts.comment_count > 0";
			
		}else if($query == 'selection') {
		
			if(empty($include_posts))
				return array();
				
			if(!is_array($include_posts)) {
				$include_posts = explode(",", $include_posts);
			}
			$query_args["post__in"] = $include_posts;

			$category = false;
			$query_args['posts_per_page'] = -1;
			$query_args['post_type'] = 'any';
			$query_args['orderby'] = 'post__in';
				
			$query_post_formats = false;
			$format = false;
		}
	}


	if(!empty($query_post_formats)) {

		if(!empty($format)) {
		            
			if(!is_array($format)) {
				$format = explode(",", $format);
			}
			
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => $format,
					'operator' => 'IN'
				)
			);
	
		} 
	
	}else if($query != 'selection') {
		
		$exclude_formats = xt_get_post_formats(true);

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => $exclude_formats,
				'operator' => 'NOT IN'
			)
		);
			
	}
		
		
	if(!empty($category)) {

		if(is_array($category)) {
			$category = implode(",", $category);
		}
		$query_args["cat"] = $category;
	}

	 	
	$query = new WP_Query( $query_args );

	return $query->posts;
}


function xt_get_post_views($postID){
    $count_key = xt_get_views_meta_key();
    $count = get_post_meta($postID, $count_key, true);
    if(empty($count)){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}
function xt_set_post_views($postID) {
	$uip = xt_get_user_ip();
    $count_key = xt_get_views_meta_key();
    $cookie_key = 'view_'.$postID.'_'.md5($uip);
	$month  = time() + 60 * 60 * 24;
 
    if(empty($_COOKIE[$cookie_key])) {
	    $count = get_post_meta($postID, $count_key, true);
	    if(empty($count)){
	        $count = 1;
	        delete_post_meta($postID, $count_key);
	        add_post_meta($postID, $count_key, $count);
	    }else{
	        $count++;
	        update_post_meta($postID, $count_key, $count);
	    }
	    setcookie($cookie_key, 1, $month, '/');
    }
     
}
function xt_get_post_likes($postID){

    $likes = get_post_meta( $postID, '_votes_likes', true );
    if(empty($likes)){
        $likes = 0;
    }
    return $likes;
}

function xt_get_post_settings($type = 'post', $location = 'archive') {
	
	$post_settings = array();
	
	switch($type) {
		case 'post':
		
			$post_settings["show_post_category"] = (bool)xt_option($type.'_'.$location.'_show_post_category');
			$post_settings["show_post_excerpt"] = (bool)xt_option($type.'_'.$location.'_show_post_excerpt');
			$post_settings["show_post_date"] = (bool)xt_option($type.'_'.$location.'_show_post_date');
			$post_settings["show_post_author"] = (bool)xt_option($type.'_'.$location.'_show_post_author');
			$post_settings["show_post_stats"] = (bool)xt_option($type.'_'.$location.'_show_post_stats');
			
			if($location == 'archive') {
			
				$category = xt_get_current_category();
					
				if(!empty($category)) {
					
					$category_settings = xt_option($type.'_'.$location.'_category_settings');

					if(!empty($category_settings) && is_array($category_settings)) {
					
						foreach($category_settings as $setting) {
							
							if(!empty($setting["category"]) && ($setting["category"] == $category)) {
	
								$post_settings["show_post_category"] = (bool)$setting["show_post_category"];
								$post_settings["show_post_excerpt"] = (bool)$setting["show_post_excerpt"];
								$post_settings["show_post_date"] = (bool)$setting["show_post_date"];
								$post_settings["show_post_author"] = (bool)$setting["show_post_author"];
								$post_settings["show_post_stats"] = (bool)$setting["show_post_stats"];
								
								break;
							}
						}
					}
				}
			
			}
	
			break;

			
	}
	return $post_settings;
}

function xt_get_single_settings($type = 'post') {
	
	$global_post_settings = array();
	$post_settings = array();
	
	switch($type) {
		case 'post':


			$global_post_settings["show_post_categories"] = (bool)xt_option('show_post_categories') ? '1' : '';
			$global_post_settings["show_post_tags"] = (bool)xt_option('show_post_tags') ? '1' : '';
			$global_post_settings["show_post_excerpt"] = (bool)xt_option('show_post_excerpt') ? '1' : '';
			$global_post_settings["show_post_date"] = (bool)xt_option('show_post_date') ? '1' : '';
			$global_post_settings["show_post_author"] = (bool)xt_option('show_post_author') ? '1' : '';
			$global_post_settings["show_post_stats"] = (bool)xt_option('show_post_stats') ? '1' : '';
			
			
			$post_settings["show_post_categories"] = get_field('show_post_categories');
			$post_settings["show_post_tags"] = get_field('show_post_tags');
			$post_settings["show_post_excerpt"] = get_field('show_post_excerpt');
			$post_settings["show_post_date"] = get_field('show_post_date');
			$post_settings["show_post_author"] = get_field('show_post_author');
			$post_settings["show_post_stats"] = get_field('show_post_stats');

			break;
			
	}

	$post_settings = array_filter($post_settings, 'xt_post_settings_filter');
	$post_settings = array_merge($global_post_settings, $post_settings);

	return $post_settings;
}

function xt_post_settings_filter($key) {
	return ($key != 'inherit' && $key !== false);
}

function xt_get_template_settings($type = 'post') {
	
	global $post;

	$page_id = xt_get_page_ID();
	
	$template_settings = array();
	
	switch($type) {

		case 'post':
		
			$template_settings["query_post_formats"] = get_field('query_post_formats', $page_id) ? '1' : '';
			$template_settings["format"] = get_field('format', $page_id);

			$template_settings["show_post_category"] = get_field('show_post_category', $page_id);
			$template_settings["show_post_excerpt"] = get_field('show_post_excerpt', $page_id);
			$template_settings["show_post_date"] = get_field('show_post_date', $page_id);
			$template_settings["show_post_author"] = get_field('show_post_author', $page_id);
			$template_settings["show_post_stats"] = get_field('show_post_stats', $page_id);

			break;

	}
	
	$template_settings = array_filter($template_settings, 'xt_template_settings_filter');

	return $template_settings;
}

function xt_template_settings_filter($key) {
	return ($key != 'inherit');
}
	
	
	
function xt_get_archive_template($type = 'post') {
	
	global $post;
	
	$category = xt_get_current_category();
	
	$default_template = xt_option($type.'_archive_default_template');
	$category_settings = xt_option($type.'_archive_category_settings');

	if(!empty($category) && !empty($category_settings) && is_array($category_settings)) {
		
		foreach($category_settings as $setting) {
			if((!empty($setting["category"]) && $setting["category"] == $category) && !empty($setting["template"])) {
				$default_template = $setting["template"];
				break;
			}
		}
	}
	return $default_template;
	
}

function xt_get_archive_sidebar($type = 'post') {
	
	global $post;
	
	$category = xt_get_current_category();

	$default_sidebar_position = xt_option($type.'_archive_sidebar_position');
	$default_sidebar_area = xt_option($type.'_archive_sidebar_area');
	$category_settings = xt_option($type.'_archive_category_settings');

	if(!empty($category) && !empty($category_settings) && is_array($category_settings)) {
	
		foreach($category_settings as $setting) {
			
			if(!empty($setting["category"]) && ($setting["category"] == $category)) {
			
				if(	!empty($setting["sidebar_position"])) {
					$default_sidebar_position = $setting["sidebar_position"];
				}	
				if(	!empty($setting["sidebar_area"])) {
					$default_sidebar_area = $setting["sidebar_area"];
				}	
				break;
			}
		}
	}

	return array(
		'position' => $default_sidebar_position,
		'area' => $default_sidebar_area
	);	
	
}

function xt_template_redirect() {
	global $wp_query;
	
	if ( is_single() && !empty($wp_query->post->ID)) {

		$postID = $wp_query->post->ID;
		xt_set_post_views($postID);
		
	}	
	
}
add_action('template_redirect', 'xt_template_redirect');


function xt_get_menu_by_location($location_id) {

	$locations = get_registered_nav_menus();
	$menus = wp_get_nav_menus();
	$menu_locations = get_nav_menu_locations();
	
	if (isset($menu_locations[ $location_id ])) {
		foreach ($menus as $menu) {
			// If the ID of this menu is the ID associated with the location we're searching for
			if ($menu->term_id == $menu_locations[ $location_id ]) {
				
				return $menu;
			}
		}
	}
	return false;
}

function xt_generate_wpml_config() {
    
    if ( ! is_admin() ) return;

    $writer = new XMLWriter();  
    $writer->openURI( get_template_directory() . '/wpml-config.xml');   
    $writer->setIndent(4); 
    $writer->startElement( 'wpml-config' );  
    $writer->startElement( 'admin-texts' );  
    
    /**
     * user_meta_fields
     */
    $fields = get_option(XT_THEME_ID);
    if ( is_array( $fields ) ) {
        $writer->startElement( 'key' );
        $writer->writeAttribute( 'name', XT_THEME_ID );
        foreach ( $fields as $id => $field ) {

	        if(!is_array($field) && !is_numeric($field)) {
	            $writer->startElement( 'key' );
	            $writer->writeAttribute( 'name', $id );
	            $writer->endElement();
            }
        }
        $writer->endElement();
    }
    
    $writer->endElement();
    $writer->endElement();
    
    $writer->flush(); 

}


/*
| -------------------------------------------------------------------
| Enqueue Latest Script based on timestamp.
| This Avoids flushing browser cache
| -------------------------------------------------------------------
| */

function xt_enqueue_script($handle, $src, $deps = array(), $ver = false, $in_footer = false ) {

	$src_path = str_replace(XT_PARENT_URL, XT_PARENT_DIR, $src);
	$file_time = @filemtime($src_path);
	$src = $src."?t=".$file_time;

	wp_register_script($handle, $src, $deps, $ver, $in_footer);
	wp_enqueue_script($handle);
}

/*
| -------------------------------------------------------------------
| Enqueue Latest Style based on timestamp.
| This Avoids flushing browser cache
| -------------------------------------------------------------------
| */

function xt_enqueue_style($handle, $src, $deps = array(), $ver = false, $media = "all") {

	if(strpos($src, XT_PARENT_URL) === false) {	
		$css_locations = XT_Theme::getAssetsPaths();
		$src_path = str_replace($css_locations['css_url'], $css_locations['css_dir'], $src);
	}else{
		$src_path = str_replace(XT_PARENT_URL, XT_PARENT_DIR, $src);
	}		
	$file_time = @filemtime($src_path);
	$src = $src."?t=".$file_time;

	wp_register_style($handle, $src, $deps, $ver, $media);
	wp_enqueue_style($handle);
}

	