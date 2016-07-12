<?php
global $post_type, $item_template, $post, $wp_query, $post_settings, $paged;

$extra_args = array();

if ( $item_template === null )
	$item_template = 'list';

if(get_query_var('paged')) {
	$paged = get_query_var('paged');
}else if(!empty($wp_query->query["paged"])) {
	$paged = $wp_query->query["paged"];
}else{
	$paged = 1;
}

if(is_tax( 'post_format' )) {
	$post_type = 'post';
}

if ( empty( $post_type ) )
	$post_type = get_post_type();

$post_type_object = get_post_type_object( $post_type );

if ( empty($page_for_archive) ) {
	$page_for_archive = get_option('page_for_posts');
	$page_for_archive = get_post( $page_for_archive );
}

$attr = array(
	'data-type="' . $post_type . '"',
	'data-page="' . $paged . '"'
);

$archive_content = "";
$hide_page_title = false;
$page_container_no_padding = false;
$full_width_page = false;


if(is_author()) :	
			
	$archive_title = sprintf( __( 'All posts by %s', XT_TEXT_DOMAIN ), get_the_author() );
	
	if ( get_the_author_meta( 'description' ) ) :
		$archive_content = xt_get_template_part('parts/items/post', 'author'); 
	endif;
	
elseif ( is_tag() ) :

	$archive_title = sprintf( __( 'Tag Archives: %s', XT_TEXT_DOMAIN ), single_tag_title( '', false )  );
	$term_description = term_description();
	
	if ( ! empty( $term_description ) ) :
		$archive_content = $term_description;
	endif;
				
elseif ( is_day() ) :

	$archive_title = sprintf( __('Daily Archives: %s', XT_TEXT_DOMAIN), get_the_date() );

elseif ( is_month() ) :

	$archive_title = sprintf( __('Monthly Archives: %s', XT_TEXT_DOMAIN), get_the_date( _x('F Y', 'monthly archives date format', XT_TEXT_DOMAIN) ) );

elseif ( is_year() ) :

	$archive_title = sprintf( __('Yearly Archives: %s', XT_TEXT_DOMAIN), get_the_date( _x('Y', 'yearly archives date format', XT_TEXT_DOMAIN) ) );

elseif(is_search() || !empty($_GET["s"])) :

	$post_type = "post";
	$archive_title = sprintf( __('Search Results for "%s"', XT_TEXT_DOMAIN), get_query_var('s'));


elseif (is_tax( 'post_format' )) :
 
	$term = get_term_by( 'slug', get_query_var('term'), 'post_format' );
	$archive_content = "";

elseif ( is_tax() ) :

	$taxonomy = get_query_var('taxonomy');
	$term = get_term_by( 'slug', get_query_var('term'), $taxonomy );

	$archive_content = term_description ($term->term_id, $taxonomy);

elseif ( is_category() ) :

	$taxonomy = 'category';
	$term = get_category( get_query_var('cat') );

	$archive_content = category_description($term->term_id);
	
elseif ( is_tag() ) :

	$taxonomy = 'post_tag';
	$term = get_term_by( 'slug', get_query_var('tag'), $taxonomy );
	$archive_content = "";
	
elseif ( ! empty($post_type_object->label) ) :

	if ( isset($wp_query->queried_object) && isset($wp_query->queried_object->ID) ) {
	
		$archive_title = get_the_title( $wp_query->queried_object->ID );
		$archive_content = apply_filters('the_content', $wp_query->queried_object->post_content);
		$hide_page_title = (bool)get_field('hide_page_title', $wp_query->queried_object->ID);
		$page_container_no_padding = (bool)get_field('page_container_no_padding', $wp_query->queried_object->ID);
		$full_width_page = xt_page_has_composer($wp_query->queried_object);

	}else{
	
		$archive_title = get_the_title( $post->ID );
		$archive_content = apply_filters('the_content', $post->post_content);
		$hide_page_title = get_field('hide_page_title', $post->ID);
		$page_container_no_padding = (bool)get_field('page_container_no_padding', $post->ID);
		$full_width_page = xt_page_has_composer($post);

	}	

endif;



if ( ! empty($term) && ! is_wp_error( $term ) )
{
	$archive_title = $term->name;

	$attr[] = 'data-taxonomy="' . $taxonomy . '"';
	$attr[] = 'data-term="' . $term->term_id . '"';
}



$paginate_method = xt_option('paginate_method');
$attr[] = 'data-paginate="' . $paginate_method . '"';
$attr[] = 'data-template="' . $item_template . '"';

$load_more_class = $paginate_method;

if ( $paginate_method == 'paginate_scroll' )
	$paginate_method = "paginate_more";



/**
 * Setup Dynamic Sidebar
 */

if(!empty($page_for_archive))
	list( $has_sidebar, $sidebar_position, $sidebar_area ) = xt_setup_dynamic_sidebar( $page_for_archive->ID );


if ( is_page_template( 'tpl-post-archive.php' ) ) {

	$post_settings = xt_get_post_settings('post', 'archive');
	$template_settings = xt_get_template_settings('post');
	$post_settings = array_merge($post_settings, $template_settings);

	if(!empty($post_settings['query_post_formats'])) {

		if(!empty($post_settings['format'])) {
		
            $format = $post_settings['format'];
            
			if(!is_array($format)) {
				$format = explode(",", $format);
			}
			
			$extra_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' => $format,
					'operator' => 'IN'
				)
			);
	
		} 
	
	}else{
		
		$exclude_formats = xt_get_post_formats(true);

		$extra_args['tax_query'] = array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => $exclude_formats,
				'operator' => 'NOT IN'
			)
		);
			
	}	
	
}

if(is_category() || is_tax( 'post_format' )) {

	$post_settings = xt_get_post_settings('post', 'archive');
	
	$has_sidebar = false;
	$category_sidebar = xt_get_archive_sidebar();

	$sidebar_area = $category_sidebar["area"];
	$sidebar_position = $category_sidebar["position"];
	if(!empty($sidebar_position) && $sidebar_position != 'disabled' && !empty($sidebar_area)) {
		$has_sidebar = true;
	}
	
}else if(is_tag()) {

	$post_settings = xt_get_post_settings('post', 'tag');
	
	$has_sidebar = false;
	$sidebar_area = xt_option('post_tag_sidebar_area');
	$sidebar_position = xt_option('post_tag_sidebar_position');
	if(!empty($sidebar_position) && $sidebar_position != 'disabled' && !empty($sidebar_area)) {
		$has_sidebar = true;
	}
	
}else if(is_author()) {

	$post_settings = xt_get_post_settings('post', 'author');
	
	$has_sidebar = false;
	$sidebar_area = xt_option('post_author_sidebar_area');
	$sidebar_position = xt_option('post_author_sidebar_position');
	if(!empty($sidebar_position) && $sidebar_position != 'disabled' && !empty($sidebar_area)) {
		$has_sidebar = true;
	}
	
}else if(is_search()) {

	$post_settings = xt_get_post_settings('post', 'search');
	
	$has_sidebar = false;
	$sidebar_area = xt_option('post_search_sidebar_area');
	$sidebar_position = xt_option('post_search_sidebar_position');
	if(!empty($sidebar_position) && $sidebar_position != 'disabled' && !empty($sidebar_area)) {
		$has_sidebar = true;
	}

}else if (is_archive() || is_home()) {
	
	$obj = get_post_type_object( 'post' );
	if(empty($archive_title) || is_home())
		$archive_title = $obj->labels->name;
		
	$archive_content = "";
	
	$post_settings = xt_get_post_settings('post', 'archive');
	
	$has_sidebar = false;
	$category_sidebar = xt_get_archive_sidebar('post');
	$sidebar_area = $category_sidebar["area"];
	$sidebar_position = $category_sidebar["position"];
	if(!empty($sidebar_position) && $sidebar_position != 'disabled' && !empty($sidebar_area)) {
		$has_sidebar = true;
	}

}

// Prevent extra database query by enabling permalink structure
if ( $post_type !== get_post_type())
{

	if( empty($_GET["s"]) ) {
		$page_for_archive = $post;
	
		$args = array(
			'post_type' => $post_type,
			'paged' => $paged
		);
		
		if(!empty($extra_args)) {
			$args = array_merge_recursive($args, $extra_args);
		}
		
		query_posts( $args );

	}
}



/**
 * Setup Post Type Structure
 */

		
switch ( $post_type ) {

	default:
	
		$tag = 'ul';
		$parent_class = 'xt_news';
		
		if(empty($item_template))
			$item_template = 'list-small';

		if($item_template == 'list' || $item_template == 'list-small') {
		
			$class = 'news-list posts-list-large-thumbs list';

		}else{
		
			$class = 'news-list posts-grid small-block-grid-1 small-grid-offset';
			
			if($item_template == 'grid-2')
				$class .= ' medium-block-grid-2 large-block-grid-2';
				
			else if($item_template == 'grid-3')
				$class .= ' medium-block-grid-2 large-block-grid-3';
				
			else if($item_template == 'grid-4')
				$class .= ' medium-block-grid-2 large-block-grid-4';	
				
			else if($item_template == 'grid-5')
				$class .= ' medium-block-grid-3 large-block-grid-5';	
		}

}

$wp_query->query_vars["is_archive"] = true;

if ( empty($archive_title) )
	$archive_title = __('Archives', XT_TEXT_DOMAIN);

	
if(!have_posts() && is_search()) {
	$archive_title = sprintf( __('Nothing Found', XT_TEXT_DOMAIN));
}
		
