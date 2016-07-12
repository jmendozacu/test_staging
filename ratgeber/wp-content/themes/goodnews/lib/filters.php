<?php
		
/**
 *  Hide / Show Admin Bar
 */

function xt_admin_bar() {
	$hide_admin_bar = (bool)xt_option('hide_admin_bar');
	if($hide_admin_bar) {
		add_filter('show_admin_bar', '__return_false');
	}
}	
add_action('init', 'xt_admin_bar');
								
/**
 *  Modify excerpts length
 */

function xt_excerpt_length( $length ) {

	if(is_single())
		return 28;
		
	return 15;
}
add_filter( 'excerpt_length', 'xt_excerpt_length', 999 );

/**
 *  Modify excerpts more
 */
 
function xt_excerpt_more( $excerpt ) {

	return str_replace( array('[', ']'), '', $excerpt );
}
add_filter( 'wp_trim_excerpt', 'xt_excerpt_more' );


/**
 *  Adjust widgets semantics
 */

function xt_adjust_widget_areas ($params) {
	global $xt_widgets;

	if ( is_admin() ) {
		return $params;
	}

	$params[0]['before_title'] = str_replace('%1$s', '', $params[0]['before_title']);
	$params[0]['before_widget'] = str_replace(' atoll', '', $params[0]['before_widget']);

	if(!empty($params[0]["before_widget"])) {

		$params[0]["before_widget"] = str_replace('h2', 'h3', $params[0]["before_widget"]);
	}

	return $params;
}
add_filter('dynamic_sidebar_params', 'xt_adjust_widget_areas', 10);


/**
 *  Limit Tag Cloud Widget Results
 */

function xt_widget_tag_cloud_args( $args ) {
	$args['number'] = 15;
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'xt_widget_tag_cloud_args', 10);


/**
 * Disable inline CSS injected by WordPress.
 *
 * Always apply your styles from an external file.
 */

add_filter('use_default_gallery_style', '__return_false');



function xt_embed_oembed_html($html, $url, $attr, $post_id) {
	
	$post_format = get_post_format($post_id);
	if($post_format == 'video')
		return '<div class="video-wrap flex-video widescreen">' . $html . '</div>';
		
	return $html;	
}
add_filter('embed_oembed_html', 'xt_embed_oembed_html', 99, 4);



/**
 * Extend Mime Uploads
 */
	 
function xt_upload_mimes ( $existing_mimes=array() ) {
 
    // add the file extension to the array
    $existing_mimes['ico'] = 'image/x-icon';
 
    // call the modified list of extensions
    return $existing_mimes;
 
}
add_filter('upload_mimes', 'xt_upload_mimes');



function xt_posts_link_attributes() {
    return 'class="page-numbers"';
}
add_filter('next_posts_link_attributes', 'xt_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'xt_posts_link_attributes');


function xt_html_class($classes = "") {

	$hide_admin_bar = (bool)xt_option('hide_admin_bar');
	if(!$hide_admin_bar && is_user_logged_in()) {
		$admin_bar_position = xt_option('admin_bar_position');
		
		$classes .= " admin-bar-".esc_attr($admin_bar_position);

	}
	
	if(!empty($classes)) {
		$output = 'class="'.esc_attr($classes).'"';
	}
			
	echo ($output);
}

function xt_body_class_names($classes) {

	global $post;
	
	$main_layout = xt_option('main-layout');
	$enable_nice_scroll = (bool)xt_option('enable_nice_scroll');
	$enable_smooth_scroll = (bool)xt_option('enable_smooth_scroll');
	$rtl_support = is_rtl();
	
	$hide_admin_bar = (bool)xt_option('hide_admin_bar');
	if(!$hide_admin_bar && is_user_logged_in()) {
		$admin_bar_position = xt_option('admin_bar_position');
		
		$classes[] = "admin-bar-".esc_attr($admin_bar_position);

	}
	
    if (is_page_template('tpl-endless-posts.php')) {
     
		 $classes[] = 'tpl-endless-articles';
		 $classes[] = 'no-padding';
		 
	}else if(is_singular('post') && !empty($post->ID)){
		
		list( $single_post_featured_image, $single_post_featured_image_position) = xt_get_featured_image_settings( $post->ID );

		if($single_post_featured_image_position == 'behind-title-fullwidth') {
			$classes[] = 'no-padding';
		}
			
	}
	
	if($enable_nice_scroll) {
		$classes[] = 'nice-scroll-enabled';
	}	 
	if($enable_smooth_scroll) {
		$classes[] = 'smooth-scroll-enabled';
	}
	if(!empty($main_layout)) {
		$classes[] = 'layout-'.$main_layout;
	}
	if($rtl_support) {
		$classes[] = 'rtl';
	}
		
	if(!empty($post)) {
		if(strpos($post->post_content, 'vc_row') !== false) {
			$classes[] = 'has-wpb-js-composer';
		} 
	}
	return $classes;
}
add_filter('body_class','xt_body_class_names');



function xt_post_class_names( $classes ) {
	global $post;
	foreach ( $classes as $i => $class ) {
		if($class == 'sticky')
			$classes[$i] = 'sticky-post';
	}
	return $classes;
}
add_filter( 'post_class', 'xt_post_class_names' );



function xt_the_author_posts_link($link) {
	
	$link = str_replace('rel=', 'itemprop="url" rel=', $link);
	return $link;
}
add_filter('the_author_posts_link', 'xt_the_author_posts_link');


/**
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */

function xt_wp_title ( $title, $sep, $seplocation ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	$sep = ' - ';
	$seplocation = 'right';
	
	if ( is_post_type_archive() )
	{
		$post_type_obj = get_queried_object();
		$title = get_the_title( xt_option('page_for_' . $post_type_obj->name . 's') );

		$prefix = '';
		if ( !empty($title) )
			$prefix = " $sep ";

		$t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

		// Determines position of the separator and direction of the breadcrumb
		if ( 'right' == $seplocation ) { // sep on right, so reverse the order
			$title_array = explode( $t_sep, $title );
			$title_array = array_reverse( $title_array );
			$title = implode( " $sep ", $title_array ) . $prefix;
		} else {
			$title_array = explode( $t_sep, $title );
			$title = $prefix . implode( " $sep ", $title_array );
		}
	}

	// Add the site name.
	$title .= get_bloginfo('name');

	// Add the site description for the home/front page.
	$site_description = get_bloginfo('description', 'display');
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __('Page %s', XT_TEXT_DOMAIN), max($paged, $page) );

	return $title;
}


if ( ! function_exists( '_wp_render_title_tag' ) ) :
    function xt_render_wp_title() {
	?>
		<title><?php wp_title( ' - ', true, 'right' ); ?></title>
	<?php
    }
    add_action( 'wp_head', 'xt_render_wp_title' );
    add_filter('wp_title', 'xt_wp_title', 5, 3);
endif;



/**
 * Append archive template to stack of taxonomy templates.
 *
 * If no taxonomy templates can be located, WordPress
 * falls back to archive.php, though it should try
 * archive-{$post_type}.php before.
 *
 * @see get_taxonomy_template(), get_archive_template()
 */
function xt_archive_template_include ( $templates ) {
	$term = get_queried_object();
	$post_types = array_filter( (array) get_query_var( 'post_type' ) );

	if ( empty($post_types) ) {
		$taxonomy = get_taxonomy( $term->taxonomy );

		$post_types = $taxonomy->object_type;

		$templates = array();

		if ( count( $post_types ) == 1 ) {
			$post_type = reset( $post_types );
			$templates[] = "archive-{$post_type}.php";
		}
	}

	return locate_template( $templates );
}

add_filter('taxonomy_template', 'xt_archive_template_include');



function xt_get_avatar($avatar) {
	$avatar = str_replace('avatar ', 'avatar round ', $avatar);
	$avatar = str_replace('class=', 'itemprop="image" class=', $avatar);
	return $avatar;
}
add_filter('get_avatar','xt_get_avatar');


function xt_attachment_link_class($html){
    $postid = get_the_ID();
    
	$zoom = (bool)xt_option('enable_thumbnail_zoom'); 
	$highlight = (bool)xt_option('enable_thumbnail_border_highlight');
    
    $html = str_replace('<a','<a class="th post-thumbnail'.($zoom ? ' zoom' : '').'"',$html);
		   
    $append = "";
    if($highlight) {
    
		$append .= '
		<div class="border-tb th-border"></div>
		<div class="border-tr th-border"></div>
		<div class="border-bt th-border"></div>
		<div class="border-bl th-border"></div>
		';
		
		$html = str_replace('</a>', $append.'</a>', $html);
	}

    return $html;
}
add_filter('wp_get_attachment_link','xt_attachment_link_class',10,1);


/**
 * Tames DISQUS comments so that it only outputs JS on specified
 * pages in the site.
 */
function xt_tame_disqus_comments() {

	/** Tame Disqus from outputting JS on pages where comments are not available */
	remove_action( 'loop_end', 'dsq_loop_end' );
	remove_action( 'wp_footer', 'dsq_output_footer_comment_js' );
 
}
add_action( 'wp_head', 'xt_tame_disqus_comments' );


/**
 *  Get comments link
 */	

function xt_get_comments_link( $link) {
    
    $post_id = get_the_ID();
    return $link.'_'.$post_id;
}
add_filter( 'get_comments_link', 'xt_get_comments_link', 10 );



/**
 *  Custom where queries
 */	
function xt_custom_where($where) {
   global $xt_global_where;

   if ($xt_global_where) $where = "$where $xt_global_where";
   return $where;
}
add_filter('posts_where', 'xt_custom_where');



/**
 *  Custom search form
 */
function xt_search_form( $form ) {
	$form = '
	<form role="search" method="get" class="search-form" action="' . home_url( '/' ) . '" >
		<label for="s">
			<input type="text" value="' . get_search_query() . '" class="search-field" name="s" id="s" />
		</label>
		<button type="submit" class="search-submit"><i class="fa fa-search loner"></i></button>
	</form>';

	return $form;
}
add_filter( 'get_search_form', 'xt_search_form' );




/**
 * Retina images
 *
 * This function is attached to the 'wp_generate_attachment_metadata' filter hook.
 */
function xt_retina_support_attachment_meta( $metadata, $attachment_id ) {
    foreach ( $metadata as $key => $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $image => $attr ) {
                if ( is_array( $attr ) )
                    xt_retina_support_create_images( get_attached_file( $attachment_id ), $attr['width'], $attr['height'], true );
            }
        }
    }
 
    return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'xt_retina_support_attachment_meta', 10, 2 );


/**
 * Create retina-ready images
 *
 * Referenced via retina_support_attachment_meta().
 */
function xt_retina_support_create_images( $file, $width, $height, $crop = false ) {
    if ( $width || $height ) {
        $resized_file = wp_get_image_editor( $file );
        if ( ! is_wp_error( $resized_file ) ) {
            $filename = $resized_file->generate_filename( $width . 'x' . $height . '@2x' );
 
            $resized_file->resize( $width * 2, $height * 2, $crop );
            $resized_file->save( $filename );
 
            $info = $resized_file->get_size();
 
            return array(
                'file' => wp_basename( $filename ),
                'width' => $info['width'],
                'height' => $info['height'],
            );
        }
    }
    return false;
}


/**
 * Delete retina-ready images
 *
 * This function is attached to the 'delete_attachment' filter hook.
 */
function xt_delete_retina_support_images( $attachment_id ) {
    $meta = wp_get_attachment_metadata( $attachment_id );
    $upload_dir = wp_upload_dir();
    if(!empty($meta['file'])) {
	    $path = pathinfo( $meta['file'] );
	    foreach ( $meta as $key => $value ) {
	        if ( 'sizes' === $key ) {
	            foreach ( $value as $sizes => $size ) {
	                $original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
	                $retina_filename = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );
	                if ( file_exists( $retina_filename ) )
	                    unlink( $retina_filename );
	            }
	        }
	    }
	} 
}
add_filter( 'delete_attachment', 'xt_delete_retina_support_images' );
