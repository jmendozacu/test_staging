<?php
/**
 * Custom template tags for Good News
 *
 * @package WordPress
 * @subpackage Good_News
 * @since Good News 1.0
 */



/**
 * Get Cached Nav Menu
 *
 * @since Good News 1.0
 */
 
function xt_get_nav_menu($menu_id, $args) {
	
	$menu_location = $menu_id;

	if ( has_nav_menu( $menu_location ) ) {

		$args['menu_id'] = $menu_location;
		$args['theme_location'] = $menu_location;
		$args['walker'] = new xt_nav_walker();
		$args['echo'] = 0;
		
		$nav_menu = wp_nav_menu( $args );
		
	}else{
		$args['theme_location'] = $menu_location;
		$args['echo'] = 0;
		$nav_menu = wp_nav_menu( $args );
	}

	return $nav_menu;
		
}

function xt_get_enabled_ubermenu_locations() {

	$active_locations = array();
	
	if(!function_exists('ubermenu'))
		return $active_locations;
	
	$active_locations = wp_cache_get('ubermenu_locations', 'xt_ubermenu');
	
	if($active_locations === false) {

		$custom_menus = get_option('ubermenu_menus');
		$main_configs = get_option('ubermenu_main');
	
		if(!empty($main_configs['auto_theme_location']))
			$active_locations = $main_configs['auto_theme_location'];
		
		foreach($custom_menus as $menu) {
			
			$config = get_option('ubermenu_'.$menu);
			
			if(!empty($config['auto_theme_location']))
				$active_locations = array_merge($active_locations, $config['auto_theme_location']);
		}
		
		wp_cache_set('ubermenu_locations', $active_locations, 'xt_ubermenu');
	
	}

	return $active_locations;
	
}

function xt_ubermenu_location_enabled($location) {
	
	$active_locations = xt_get_enabled_ubermenu_locations();
	if(!empty($active_locations))
		return in_array($location, $active_locations);
		
	return false;	
}


function xt_page_menu($menu, $args = array()) {

	$menu_class = "";
	if(!empty($args["menu_class"])) {
		$menu_class = $args["menu_class"];
	}


	preg_match('/\<ul\>(.+)\<\/ul\>/i', $menu, $matches);

	if(!empty($matches[0])) {
		$menu = $matches[0];
	}
	
	$menu = str_replace(
		array(
			'<ul>',
			'page_item_has_children',
			'children'
		), 
		array(
			'<ul class="'.esc_attr($menu_class).'">',
			'has-dropdown',
			'dropdown'
		), 
	$menu);
	
	return $menu;
}
add_filter('wp_page_menu', 'xt_page_menu', 10, 2); 



/**
 * Flush Cached Nav Menus on menu updates
 *
 * @since Good News 1.0
 */
function xt_flush_menu_transients() {
	
	global $XT_Theme;
	
	$cache_enabled = false;
	//$cache_enabled = (bool)xt_option('cache_nav_menus');
	
	if($cache_enabled && !empty($XT_Theme) && !empty($XT_Theme->menus)) {
		
		$menu_locations = array_keys($XT_Theme->menus);
		
		foreach($menu_locations as $location) {
			$menu_cache_key = 'xt_menu_'.$location;
			delete_transient( $menu_cache_key);
		}	
	}
}

add_action( 'wp_update_nav_menu', 'xt_flush_menu_transients' );


function xt_is_sidebar_below_title($page_id) {
	
	$sidebar_position = get_field('sidebar-position', $page_id, 'inherit');
	$sidebar_below_title = get_field('sidebar-below-title', $page_id, false);
	if(!isset($sidebar_below_title) || $sidebar_below_title == 'inherit' || $sidebar_position == 'inherit') {
		$sidebar_below_title = (bool)xt_option('single_post_default_sidebar_below_title');
	}else{
		$sidebar_below_title = ($sidebar_below_title == 'enabled' || $sidebar_below_title == '1');
	}
	
	return $sidebar_below_title;
}


function xt_get_featured_image_settings($page_id) {
	
	$post_format = get_post_format();
	$is_endless_template = xt_is_endless_template();
	$single_post_featured_image = get_field('single_post_featured_image', $page_id);
	
	if(empty($single_post_featured_image) || $single_post_featured_image == 'inherit') {
		$single_post_featured_image = xt_option('single_post_featured_image');
	}
	
	if(!$single_post_featured_image)
		$single_post_featured_image = 'fullwidth';
		
		
	$single_post_featured_image_position = get_field('single_post_featured_image_position', $page_id);
	
	if(empty($single_post_featured_image_position) || $single_post_featured_image_position == 'inherit') {
		$single_post_featured_image_position = xt_option('single_post_featured_image_position');
	}	
	
	if($single_post_featured_image == "none") {
		$single_post_featured_image_position = "none";
	}
	
	if(
		$is_endless_template ||
		(empty($single_post_featured_image_position)) || 
		(!empty($post_format) && ($single_post_featured_image_position == 'behind-title' || $single_post_featured_image_position == 'behind-title-fullwidth')) || 
		(($single_post_featured_image_position == 'behind-title' || $single_post_featured_image_position == 'behind-title-fullwidth') && $single_post_featured_image == 'none')) {
		
			$single_post_featured_image_position = 'above-content';
	}	

	return array(
		  $single_post_featured_image
		, $single_post_featured_image_position
	);	

}

/**
 * Setup Dynamic Sidebar
 *
 * @since Good News 1.0
 */

function xt_setup_dynamic_sidebar ( $page_id )
{

	$has_sidebar = false;
	$sidebar_area = false;
	$sidebar_position = get_field('sidebar-position', $page_id, 'inherit');
	$post_type = get_post_type();
	
	if ( 'disabled' === $sidebar_position )
		$sidebar_position = false;

	if ( $post_type == 'post' && ($sidebar_position === "" || $sidebar_position === 'inherit') ) {
		
		$sidebar_area = xt_option('single_post_default_sidebar');
		$sidebar_position = xt_option('single_post_default_sidebar_position');
		
		if ( 'disabled' === $sidebar_position )
			$sidebar_position = false;
			
	}	
				
	if ( !empty($sidebar_position) )
	{
		if(empty($sidebar_area))
			$sidebar_area = get_field('sidebar-area_id', $page_id, false);
			
		$has_sidebar = is_active_sidebar( $sidebar_area );
	}
	
	return array(
		  $has_sidebar
		, $sidebar_position
		, $sidebar_area
	);
}


/**
 * Show Dynamic Sidebar
 *
 * @since Good News 1.0
 */


function xt_show_dynamic_sidebar($sidebar_area, $template, $id = "sidebar", $class = "sidebar") {
	
	?>
	<aside id="<?php echo esc_attr($id);?>" class="<?php echo esc_attr($class);?> content__side widget-area widget-area--<?php echo esc_attr( $sidebar_area ); ?>">
	<?php
	do_action('before_xt_sidebar_dynamic_sidebar', $template);

	dynamic_sidebar( $sidebar_area );

	do_action('after_xt_sidebar_dynamic_sidebar', $template);
	?>
	</aside>
	<?php		
}


/**
 * Show Dynamic Sidebar
 *
 * @since Good News 1.0
 */


function xt_show_custom_dynamic_sidebar($sidebar_area_id, $template, $doubleRow = false, $id = null, $class = null) {
	
	$widgets = wp_get_sidebars_widgets();
	$sidebar_area = xt_option($sidebar_area_id.'_zone');
	$sidebar_widgets = !empty($widgets[$sidebar_area]) ? $widgets[$sidebar_area] : false;
	$sidebar_layout = xt_option($sidebar_area_id.'_zone_layout');	
	
	$sidebar_id = $sidebar_area."-".$template;
	if(!empty($id)) {
		$sidebar_id = $id;
	}
	
	$sidebar_class = "";
	if(!empty($class)) {
		$sidebar_class = $class;
	}
	
	if (is_active_sidebar( $sidebar_area ) && $sidebar_widgets ) {
		
	?>
	
	<?php if($doubleRow): ?>
	<div class="row vc_row in-container">
		<div class="medium-12 column">
	<?php endif; ?>
			
			<div class="widget-area widget-area--<?php echo esc_attr( $sidebar_id );?> <?php echo esc_attr( $sidebar_class );?>" id="<?php echo esc_attr( $sidebar_id );?>">
				<hr>
				<div class="row stretch collapse">
			
				<?php
				do_action('xt_before_'.$sidebar_area_id.'_zone', $template);
			
				if(is_numeric($sidebar_layout)) : 
				
					$total = (int)$sidebar_layout;
					$column_class = 12 / $total;
					$i = 0;
					foreach($sidebar_widgets as $widget) :
					?>
						<div class="medium-<?php echo esc_attr($column_class);?> large-<?php echo esc_attr($column_class);?> column">
							<?php xt_widget_instance($widget); ?>
						</div>
						
					<?php
					$i++;
					endforeach;
				
				else:
				
					$sidebar_layout = str_replace("custom_", "", $sidebar_layout);
					$columns = explode("_", $sidebar_layout);
					$total = count($columns);
		
					$i = 0;
					foreach($sidebar_widgets as $widget) :
							
						$column_class = (int)$columns[$i];	
					?>
						<div class="medium-<?php echo esc_attr($column_class);?> large-<?php echo esc_attr($column_class);?> column">
							<?php xt_widget_instance($widget); ?>
						</div>
						
					<?php
					$i++;
					endforeach;
					
					
				endif; 
				
				do_action('xt_after_'.$sidebar_area_id.'_zone', $template);
				?>
				
				</div>
			</div>
			
	<?php if($doubleRow): ?>
		</div>
	</div>	
	<?php endif; ?>
	
	<?php
	}

}


function xt_widget_instance($widget_id, $format=true) {
	global $wp_registered_widgets, $wp_registered_sidebars, $sidebars_widgets;
	
	// validation
	if (!array_key_exists($widget_id, $wp_registered_widgets)) {
	  echo 'No widget found with that id'; return;
	}
	
	// find sidebar 
	if(empty($sidebars_widgets) || !is_array($sidebars_widgets)) 
		return false;
		
	foreach($sidebars_widgets as $sidebar => $sidebar_widget){
		
		if(empty($sidebar_widget) || !is_array($sidebar_widget)) 
			continue;
		
		foreach($sidebar_widget as $widget){
			if ($widget==$widget_id) $current_sidebar = $sidebar;
	  	}
	}
	
	$presentation = (!empty($wp_registered_sidebars[$current_sidebar])) ? $wp_registered_sidebars[$current_sidebar] : 
	  array('name' => '', 
	        'id' => '',
	        'description' => '',
	        'class' => '',
	        'before_widget'=> '',
	        'after_widget'=> '',
	        'before_title'=> '',
	        'after_title' => '');
	
	// Clear formatting unless required

	if (!$format) { 
	  $presentation['before_widget'] = '';
	  $presentation['after_widget'] = '';
	}
	
	$params = array_merge(
	  array( array_merge( $presentation, array('widget_id' => $widget_id, 'widget_name' => $wp_registered_widgets[$widget_id]['name']) ) ),
	  (array) $wp_registered_widgets[$widget_id]['params']
	);
	
	// Substitute HTML id and class attributes into before_widget
	$classname_ = '';
	foreach ( (array) $wp_registered_widgets[$widget_id]['classname'] as $cn ) {
	  if ( is_string($cn) )
	    $classname_ .= '_' . $cn;
	  elseif ( is_object($cn) )
	    $classname_ .= '_' . get_class($cn);
	}
	$classname_ = ltrim($classname_, '_');
	$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $widget_id, $classname_);
	
	$params = apply_filters( 'dynamic_sidebar_params', $params ); // doesnt't add/minus from data
	
	$callback = $wp_registered_widgets[$widget_id]['callback'];
	
	$id_base = $callback[0]->id_base;
	
	if(!in_array($id_base, array('xt-text'))) {
		$params[0]['before_title'] = str_replace('h2', 'h3', $params[0]['before_title']);
		$params[0]['after_title'] = str_replace('h2', 'h3', $params[0]['after_title']);
	}
	
	if ( is_callable($callback) ) {
	  call_user_func_array($callback, $params);
	}

}
  
  
function xt_loadmore_nav($attr = array(), $load_more_class = '') {
	
	$position = xt_option('paginate_position');
	
	$loading_animation = xt_get_loading('wait '.$load_more_class);
	
	echo ($loading_animation);
	
	echo '
	<nav class="navigation paging-navigation '.$position.'" role="navigation">
		<a href="#" onclick="this.href = location.href" data-rel="post-list" '.implode(' ', $attr).' class="button-more '.esc_attr($load_more_class).'">'.__('More', XT_TEXT_DOMAIN).'</a>
	</nav>';

}


/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Good News 1.0
 */
function xt_full_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	

	if(!is_rtl()) {
		$left_icon = 'fa-angle-left';
		$right_icon = 'fa-angle-right';
	}else{
		$left_icon = 'fa-angle-right';
		$right_icon = 'fa-angle-left';
	}

	$position = xt_option('paginate_position');

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => '<span class="fa '.$left_icon.'"></span>',
		'next_text' => '<span class="fa '.$right_icon.'"></span>'
	) );

	if ( $links ) :

	?>
	<nav class="navigation paging-navigation <?php echo esc_attr($position); ?>" role="navigation">
		<div class="pagination loop-pagination">
			<?php echo ($links); ?>
		</div><!-- .pagination -->
	</nav><!-- .navigation -->
	<?php
	endif;
}


function xt_post_pages() {

	if(!is_rtl()) {
		$left_icon = 'fa-angle-left';
		$right_icon = 'fa-angle-right';
	}else{
		$left_icon = 'fa-angle-right';
		$right_icon = 'fa-angle-left';
	}
		
	$pagination_position = xt_option('paginate_position');
	wp_link_pages( array( 
		'before' => '<nav class="navigation paging-navigation link_pages '.$pagination_position.'" role="navigation"><div class="pagination loop-pagination">', 
		'after' => '</div></div>', 
		'link_before' => '<span>', 
		'link_after' => '</span>',
		'previouspagelink' => '<span class="fa '.$left_icon.'"></span>',
		'nextpagelink' => '<span class="fa '.$right_icon.'"></span>'
	)); 
							
}


function xt_paging_nav() {

	$position = xt_option('paginate_position');

	if(!is_rtl()) {
		$left_icon = 'fa-angle-left';
		$right_icon = 'fa-angle-right';
	}else{
		$left_icon = 'fa-angle-right';
		$right_icon = 'fa-angle-left';
	}
	
	?>
	
	<nav class="navigation paging-navigation <?php echo esc_attr($position); ?>" role="navigation">
		<div class="pagination loop-pagination">
			<?php previous_posts_link('<span class="fa '.$left_icon.'"></span> '.__('Previous', XT_TEXT_DOMAIN), ''); ?>
			<?php next_posts_link(__('Next', XT_TEXT_DOMAIN).' <span class="fa '.$right_icon.'"></span>',''); ?>
		</div>
	</nav>

	<?php
}


/**
 * Display navigation to next/previous post when applicable.
 *
 * @since Good News 1.0
 */
function xt_post_nav($class="") {

	$post_type = get_post_type();
	$post_format = get_post_format();
	$prev = get_previous_post(false);
	$next = get_next_post(false);
	$icon_class = "fa-search";
	
	if($post_format == 'video') {
		
		$icon_class = "fa-youtube-play";
		
	}else if($post_type == 'product') {
		
		$icon_class = "fa-cart";
	}

	if(!empty($prev)) {
		echo '
		<div class="post-nav prev '.$class.'">
			<a href="'.get_permalink($prev->ID).'"><span class="thumb"><span class="fa '.$icon_class.'"></span>'.get_the_post_thumbnail($prev->ID, 'th-small').'</span> '.strip_tags($prev->post_title).'</a>
			<div class="nav-label">'.__("Previous", XT_TEXT_DOMAIN).'</div>
		</div>';
	}	
	
	if(!empty($next)) {
		echo '
		<div class="post-nav next '.$class.'">
			<a href="'.get_permalink($next->ID).'"><span class="thumb"><span class="fa '.$icon_class.'"></span>'.get_the_post_thumbnail($next->ID, 'th-small').'</span> '.strip_tags($next->post_title).'</a>
			<div class="nav-label">'.__("Next", XT_TEXT_DOMAIN).'</div>
		</div>';
	}

}


function xt_get_loading($class="") {
	
	$loading = '
	<div class="'.$class.'">
	  <div class="double-bounce1"></div>
	  <div class="double-bounce2"></div>
	</div>';
	
	return $loading;
}


function xt_breadcrumbs($id = 'breadcrumbs', $class = 'breadcrumbs')
{	
    $output = "";
    $breadcrumbs = "";
    
    if ( function_exists('yoast_breadcrumb') ) {
	    
        $yoast_links_options = get_option( 'wpseo_internallinks' );
        $yoast_bc_enabled=$yoast_links_options['breadcrumbs-enable'];
        
        if ($yoast_bc_enabled) {
            ob_start();
            yoast_breadcrumb('<nav id="'.$id.'" class="'.$class.'">','</nav>');
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }
	
	
	$markup_type = ' itemscope itemtype="http://schema.org/BreadcrumbList"';
	$markup_item_type = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
      
	if ( is_paged() || is_singular() || is_author() || is_tax() || is_category() || is_tag() || is_date() || (function_exists('is_shop') && is_shop())) {
		$breadcrumbs .= '<li'.$markup_item_type.'><a href="' . home_url('/') . '">' . __('Home', XT_TEXT_DOMAIN) . '</a></li>';
	}

	if ( is_archive() )
	{
		# Term
		if ( is_tax() )
		{
			$taxonomy = get_query_var('taxonomy');
			$term = get_term_by( 'slug', get_query_var('term'), $taxonomy );

		} elseif ( is_category() ) {
			$taxonomy = 'category';
			$term = get_category( get_query_var('cat') );

		} elseif ( is_tag() ) {
			$taxonomy = 'post_tag';
			$term = get_term_by( 'slug', get_query_var('tag'), $taxonomy );
			
		}

		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
			$breadcrumbs .=  '<li'.$markup_item_type.'><a itemprop="item" href="' . get_term_link( $term->slug, $taxonomy ) . '"><span itemprop="name">' . $term->name . '</span></a></li>';
		}

		# A Date/Time Page
		if ( is_day() ) {

			$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . sprintf( __('Archive for %s', XT_TEXT_DOMAIN), get_the_date() ) . '</span></li>';

		} elseif ( is_month() ) {

			$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . sprintf( __('Archive for %s', XT_TEXT_DOMAIN), get_the_date( _x('F Y', 'monthly archives date format', XT_TEXT_DOMAIN) ) ) . '</span></li>';

		} elseif ( is_year() ) {

			$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . sprintf( __('Archive for %s', XT_TEXT_DOMAIN), get_the_date( _x('Y', 'yearly archives date format', XT_TEXT_DOMAIN) ) ) . '</span></li>';

		} elseif ( is_author() ) {

			$author = get_userdata( get_query_var('author') );
			$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . sprintf( __('Archive for %s', XT_TEXT_DOMAIN), $author->display_name ) . '</span></li>';

		}
		
		
		if(function_exists('is_shop') && is_shop()) {
			
			$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . __('Shop', XT_TEXT_DOMAIN) . '</span></li>';
			
		}

	} elseif ( is_search() ) {

		$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . __('Results', XT_TEXT_DOMAIN) . '</span></li>';

	}

	// Index Pagination
	if ( is_paged() ) {

		$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . sprintf( __('Page %s', XT_TEXT_DOMAIN), get_query_var('paged') ) . '</span></li>';

	}


	# A Single Page, Single Post or Attachment
	if ( is_singular() ) {

		$breadcrumbs .=  '<li'.$markup_item_type.'><span itemprop="name">' . get_the_title() . '</span></li>';

		// Post Pagination
		$paged = get_query_var('page');

		if ( is_single() && $paged > 1 ) {

			$breadcrumbs .= '<li'.$markup_item_type.'><span itemprop="name">' . sprintf( __('Page %s', XT_TEXT_DOMAIN), $paged ) . '</span></li>';

		}

	}

	if(!empty($breadcrumbs)) {
		$output = '<nav id="' . $id . '" class="' . $class . '"><ul'.$markup_type.'>'.$breadcrumbs.'</ul></nav>';
	}
	
	return $output;
}



function xt_socialshare($params = array()) {
	
	global $post, $XT_SocialShare;
	
	$is_endless_template = xt_is_endless_template();
    $enabled = (bool)xt_option('xtss_enabled');
    
    $xtss_box = null;

    if($enabled) {

		$networks = array();
		$networks_data = xt_option('xtss_networks');

    	$params = shortcode_atts( array(
	      'show_title' => xt_option('xtss_show_title'),
	      'title' => xt_option('xtss_title'),
		  'skin' => xt_option('xtss_skin'),
		  'align' => xt_option('xtss_align'),
		  'layout' => xt_option('xtss_layout'),
		  'is_fullwidth' => xt_option('xtss_is_fullwidth'),
		  'size' => $is_endless_template ? 'small' : xt_option('xtss_size'),
		  'radius' => xt_option('xtss_radius'),
		  'rounded' => xt_option('xtss_rounded'),
		  'show_names' => xt_option('xtss_show_names'),
		  'show_shares' => xt_option('xtss_show_shares'),
		  'show_total_shares' => xt_option('xtss_show_total_shares'),
		), $params );
	
	
		if(!empty($networks_data))
			$networks = $networks_data['enabled'];
		
		$post_id = get_the_ID();
		
		$blogname = get_bloginfo('name');
		$url = get_permalink($post_id);
		$title = get_the_title($post_id);
		$excerpt = get_the_excerpt();
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'th-large');
		$media = '';
		if(!empty($thumb[0]))
			$media = $thumb[0];
		
		$config = array(
			'twitter' => array('via' => xt_option('xtss_twitter_username'), 'text' => $title),
			'pinterest' => array('description' => $title, 'media' => $media),
			'linkedin' => array('title' => $title, 'summary' => $excerpt, 'source' => $blogname) 
		);
		
		if(empty($XT_SocialShare)) {
			$XT_SocialShare = new XT_SocialShare($params, $networks);
		}else{
			$XT_SocialShare->setParams($params);
			$XT_SocialShare->setSelected($networks);
		}	
	
		$xtss_box = $XT_SocialShare->render($url, $config, true);

	}

	return $xtss_box;
}



function xt_smart_sidebar_position() {
	
	$is_endless_template = xt_is_endless_template();
	$enabled = (bool)xt_option('single_post_smart_sidebar');
	if(!$enabled || $is_endless_template)
		return false;

	$items = xt_option('single_post_smart_sidebar_items');
	if(empty($items["enabled"]))
		return false;

	$position = xt_option('single_post_smart_sidebar_position');
	
	
	$page_enabled = get_field('enable_smart_sidebar', get_the_ID());
	if($page_enabled == "enabled") {
		
		$position = get_field('smart_sidebar_position', get_the_ID());
		
	}else if($page_enabled == "disabled") {
		
		return false;
	}

	return $position;
}

function xt_smart_sidebar_has($key) {
	
	$is_endless_template = xt_is_endless_template();
	$enabled = (bool)xt_option('single_post_smart_sidebar');
	$page_enabled = get_field('enable_smart_sidebar', get_the_ID());
	if(!$enabled || $page_enabled == "disabled" || $is_endless_template)
		return false;

	$items = xt_option('single_post_smart_sidebar_items');
	if(empty($items["enabled"][$key]))
		return false;
		
	return true;
}

function xt_smart_sidebar() {
	
	global $post;
	
	$post_settings = xt_get_single_settings('post');
	extract($post_settings);

	$is_endless_template = xt_is_endless_template();
	$enabled = (bool)xt_option('single_post_smart_sidebar');
	if(!$enabled || $is_endless_template)
		return false;
		
	$position = xt_option('single_post_smart_sidebar_position');
	$items = xt_option('single_post_smart_sidebar_items');
	
	if(empty($items["enabled"]))
		return false;

	echo '<div class="smart-sidebar">';
	
	foreach($items["enabled"] as $key => $item) {
		
		echo '<div class="smart-sidebar-item">';
		
		if(strpos($key, 'divider') !== false) {
			
			echo '<div class="smart-sidebar-divider"></div>';
			
		}else if($key == 'social-share') {
			
			echo xt_socialshare();
			
		}else if($key == 'post-date' && $show_post_date) {
			
			xt_post_date();
			
		}else if($key == 'post-author' && $show_post_author) {
			
			xt_post_author();
			
		}else if($key == 'post-stats' && $show_post_stats) {
			
			echo '<div class="meta">';
			xt_post_stats(true, array('mini'));
			echo '</div>';
			
		}else if($key == 'widget-zone' && $show_post_author) {
			
			$widget_zone = xt_option('single_post_smart_sidebar_widget_zone');
			$widget_zone_active = is_active_sidebar( $widget_zone );
			
			if($widget_zone_active)
				xt_show_dynamic_sidebar($widget_zone, 'single-post.php', "smart-sidebar", "smart-sidebar position-".$position);
		}	
		
		echo '</div>';
	}
	
	echo '</div>';
	
}

/**
 * Custom comment listing
 */
function xt_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
<<?php echo esc_attr($tag); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">

	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID(); ?>" class="comment_container">
	<?php endif; ?>
	
		<?php if ( $args['avatar_size'] != 0 ) : ?>
			<?php echo (get_avatar( $comment, $args['avatar_size'] )); ?>
		<?php endif; ?>

		<div class="comment-text" itemscope itemtype="http://schema.org/UserComments">

			<?php if ( $comment->comment_approved == '0' ) : ?>

				<p class="meta"><em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' , XT_TEXT_DOMAIN); ?></em></p>

			<?php else : ?>

				<p class="meta">
					<span itemprop="author"><?php echo xt_get_comment_author_link(); ?> &nbsp;&nbsp;</span>
					<time datetime="<?php echo esc_attr(get_comment_date( 'c' )); ?>"><?php echo __('ago', XT_TEXT_DOMAIN). ' ' .human_time_diff( get_comment_time('U'), current_time('timestamp') ); ?></time>
					&nbsp;&nbsp;
					<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					<?php edit_comment_link( __( 'Edit', XT_TEXT_DOMAIN ), ' &nbsp; | &nbsp; ', '' ); ?>
	
				</p>

			<?php endif; ?>

			
			<div itemprop="commentText" class="description clear">
				<?php comment_text(); ?>
			</div>

			
		</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php
}

function xt_get_comment_author_link () {
    global $comment;

    if ($comment->user_id == '0') {
        if (!empty ($comment->comment_author_url)) {
            $url = $comment->comment_author_url;
        } else {
            $url = '#';
        }
    } else {
        $url = get_author_posts_url($comment->user_id);
    }

    echo "<a href=\"" . $url . "\">" .get_comment_author () . "</a>";
}

/**
 * Find out if blog has more than one category.
 *
 * @since Good News 1.0
 *
 * @return boolean true if blog has more than 1 category
 */
function xt_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'xt_category_count' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'xt_category_count', $all_the_cool_cats );
	}

	if ( 1 !== (int) $all_the_cool_cats ) {
		// This blog has more than 1 category so xt_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so xt_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in xt_categorized_blog.
 *
 * @since Good News 1.0
 */
function xt_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'xt_category_count' );
}
add_action( 'edit_category', 'xt_category_transient_flusher' );
add_action( 'save_post',     'xt_category_transient_flusher' );

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index
 * views, or a div element when on single views.
 *
 * @since Good News 1.0
 */
function xt_post_thumbnail($size = 'th-medium', $link = null, $thumb_filter = null) {
	if ( post_password_required() || ! has_post_thumbnail() ) {
		return;
	}

	$post_format = get_post_format();
	$post_type = get_post_type();

	if ( (is_single() && empty($link)) || ($link === false)) :
	?>

	<div class="th post-thumbnail">
	<?php
		the_post_thumbnail($size, array(
			"itemprop" => "image"
		));
	?>
	</div>
    		
	<?php else : ?>

	<?php 
		$thumb_class = "attachment-$size";
		$zoom = (bool)xt_option('enable_thumbnail_zoom'); 
		$highlight = (bool)xt_option('enable_thumbnail_border_highlight');
		
		if(!empty($thumb_filter)) {
			$thumb_class .= " ".$thumb_filter;
		}
	?>
	<a class="th post-thumbnail<?php echo ($zoom ? ' zoom' : ''); ?>" href="<?php echo esc_url(get_permalink());?>">
		
		<?php the_post_thumbnail($size, array(
			"itemprop" => "image",
			"class" => $thumb_class,
		)); ?>
		
		<?php if($highlight): ?>
			<div class="border-tb th-border"></div>
			<div class="border-tr th-border"></div>
			<div class="border-bt th-border"></div>
			<div class="border-bl th-border"></div>
		<?php endif; ?>
		
		<?php
			
		$icon = '<div class="icon-overlay"><span class="fa {icon}"></span></div>';
		
		if($post_format === 'video') {
				
			$icon = 'fa-youtube-play';
			
		}else if($post_format === 'gallery') {
			
			$icon = 'fa-camera';
			
		}else if($post_type == 'product') {
			
			$icon = 'fa-cart';
				
		}else{
			
			$icon = '';
		}
	
		if(!empty($icon)):
		?>
		<div class="icon-overlay"><span class="fa <?php echo esc_attr($icon); ?>"></span></div>	
		<?php 
		endif; 
		?>	
	</a>
    		
	<?php endif; // End is_singular()
}


function xt_get_post_thumbnail_src($size = 'th-medium') {
	
	$thumb_id = get_post_thumbnail_id();
	$thumb_url = wp_get_attachment_image_src($thumb_id, $size);

	if(!empty($thumb_url)) {	
		return $thumb_url[0];
	}
	
	return false;

}

function xt_post_featured_media($withTitle = false, $asBackground = false) {
	
	global $post;
	
	$post_id = get_the_ID();
	$post_format = get_post_format();
	
	if($post_format === 'video') {
		
		xt_single_post_video();
		
	}else if($post_format === 'gallery') {
		
		xt_single_post_gallery();
			
	}else{
		
		xt_single_post_image($withTitle, $asBackground);
	}
		
}

function xt_single_post_image($withTitle = false, $asBackground = false) {

	global $post;
	
	$post_id = get_the_ID();
	
	if(!has_post_thumbnail($post_id))
		return false;

	list( $single_post_featured_image, $single_post_featured_image_position) = xt_get_featured_image_settings( $post_id );

	if($single_post_featured_image != 'none') {
		
		$is_endless_template = xt_is_endless_template();
		$featured_image_crop_height = (int)get_field('single_post_featured_image_crop');

		if($asBackground && empty($featured_image_crop_height)) {
			$featured_image_crop_height = 600;
		}
		
		$image_size = 'th-xlarge';
		
		if($is_endless_template) {
			
			$image_size = 'th-large';
			$featured_image_crop_height = false;
			
		}
		
		if(!empty($featured_image_crop_height)) {

			$image = get_post( get_post_thumbnail_id( $post_id , $image_size) );
			
			$image_width = XT_Theme::$image_sizes[$image_size]['width'];
			$image_url = $image->guid;
		
			if($single_post_featured_image != 'fullwidth')
				$image_width = null;
				
			$resized_image = aq_resize( $image_url, $image_width, $featured_image_crop_height, true, false, true); 
		
			if(!$asBackground && $resized_image !== false) {
				
				$featured_image = '<img itemprop="image" alt="'.esc_attr($image->post_title).'" src="'.esc_url($resized_image[0]).'" width="'.esc_attr($resized_image[1]).'" height="'.esc_attr($resized_image[2]).'" class="wp-post-image wp-featured-image '.esc_attr($single_post_featured_image).'">';
			
			}else if($asBackground && $resized_image !== false) {
				
				$featured_image = '<div itemprop="image" content="'.esc_url($resized_image[0]).'" class="featured-background '.esc_attr($single_post_featured_image).'" style="background-image:url('.esc_url($resized_image[0]).'); height:'.$resized_image[2].'px">';
			}
			
		}else if(!$asBackground){
			
			$featured_image = get_the_post_thumbnail( $post_id, $image_size, array('itemprop'=>'image', 'class' => 'wp-featured-image '.esc_attr($single_post_featured_image) ) ); 
		}
			

		if($featured_image !== false) {

			if(!$asBackground) {
				echo '<div class="th '.esc_attr($single_post_featured_image).'">';
			}
			
			echo ($featured_image);
			
			if($withTitle) {
				?>
				<div class="featured-image-behind-title-wrap gradient-wrap">
					<div class="vc_row in-container l-padding-10 r-padding-10">
						<?php xt_post_title('h1');?>
					</div>
				</div>
				<?php	
			}
		
			echo '</div>';		
		}
		
	}
}


function xt_single_post_video() {
	
	global $post;
	
	$post_id = get_the_ID();
	$video_url = get_field('video_url', $post_id);
	$video_embed = wp_oembed_get($video_url);

	$video_embed = xt_oembed_extra_params($video_embed);
	
	if(!empty($video_embed)):
	?>
	<div class="video-wrap flex-video widescreen"><?php echo ($video_embed); ?></div>
	<?php
	endif;
}
	
function xt_oembed_extra_params($video_embed) {
	
	$extra_params = 'src="$1';
	
	if(strpos($video_embed, "?") === false) {
		$extra_params .= '?rel=0';
	}else{
		$extra_params .= '&rel=0';
	}
	
	$is_endless_articles = xt_is_endless_template();
	
	if(!$is_endless_articles) {
		$autoplay = (bool)xt_option('single_video_player_autoplay', null, 1);
		if($autoplay)
			$extra_params .= '&autoplay=1';
		
	}	

	$extra_params .= '"';
	
	$video_embed = preg_replace('/src\=\"(.+?)\"/i', $extra_params, $video_embed);
	
	return $video_embed;
}	

function xt_single_post_gallery() {
	
	global $post;
	
	$post_id = get_the_ID();
	$album_theme = get_field('gallery_theme', $post_id);
	$photos = get_field('gallery_photos', $post_id);
	$height = get_field('gallery_height', $post_id);
	
	$photos_ids = array(); 
	
	if(!empty($photos)) {
		
		foreach($photos as $photo) :
		
			$photos_ids[] = $photo["photo_file"]["id"];
			
			$title = $photo["photo_title"];
			if(empty($title)) {
				$title = $photo["photo_file"]["title"];
			}
			
			$photos_titles[] = $title;

		endforeach;
	
		
		$size = "th-large";
		if(!class_exists('XT_Galleria')) {
			$size = "th-thumbnail";
		}
		
		$columns = '';
		if($album_theme == 'native') {
			$columns = get_field('columns', $post_id);
			$columns = 'columns="'.esc_attr($columns).'"';
		}
		
		if(!empty($height)) {
			$height = 'height="'.esc_attr($height).'"';
		}
		
		$shortcode = '[gallery ids="'.implode(",",$photos_ids).'" titles="'.implode("\r\n",$photos_titles).'" '.$columns.' '.$height.' size="'.esc_attr($size).'" link="lightbox" theme="'.esc_attr($album_theme).'"]';
		echo do_shortcode($shortcode);
	
	}
}


	
function xt_post_title($tag = 'h3', $class = '', $link = null, $limit = null) {

	$class .= ' xt-post-title';
	
    if(!empty($class)) {
        $class = ' class="'.esc_attr($class).'"';
    }
	    	
	if ( (is_single() && empty($link)) || ($link === false)) :

		?>
		<<?php echo esc_attr($tag);?><?php echo ($class);?> itemprop="name headline">
			
			<?php echo esc_html(get_the_title()); ?>
			
		</<?php echo esc_attr($tag);?>>
		
		<?php
		
	else: 

		$title = esc_html(get_the_title());

		if(!empty($limit))
			$title = xt_trim_words($title, $limit);
		?>
		<<?php echo esc_attr($tag);?><?php echo ($class);?> itemprop="name headline"><a itemprop="url" rel="bookmark" title="<?php echo esc_attr($title); ?>" href="<?php echo esc_url(get_permalink());?>"><?php echo esc_html($title); ?></a></<?php echo esc_attr($tag);?>>
		<?php 
		
	endif; 

}


function xt_post_excerpt($tag = 'h5', $class = '', $limit = null, $suffix = "...", $showReadMore = false) {
	
	global $post, $XT_Likes;
	
	$class .= ' xt-post-excerpt';
	
	remove_filter('the_content', array(&$XT_Likes, 'add_to_content'), 100);
	if(!empty($limit)) {
		
		if(!empty($post->post_excerpt)) {
			$excerpt = get_the_excerpt();
		}else{
			$excerpt = $post->post_content;
		}
		$excerpt = xt_trim_words($excerpt, $limit, $suffix);
		
	}else{
		$excerpt = get_the_excerpt();
	}
	if(is_single() && !empty($post->post_content))
		add_filter('the_content', array(&$XT_Likes, 'add_to_content'), 100);
	
	if(!empty($excerpt)) {
		
		if(!empty($class)) {
	        $class = ' class="'.esc_attr($class).'"';
	    }
		?>
	    <<?php echo esc_attr($tag);?><?php echo ($class);?> itemprop="description">
	    
	    	<?php echo wp_kses_post($excerpt); ?>
			
			<?php if($showReadMore): ?>
			&nbsp; <a class="read-more" href="<?php the_permalink();?>"><?php echo __("Read More", XT_TEXT_DOMAIN); ?></a>
			<?php endif; ?>
			
	    </<?php echo esc_attr($tag);?>>
        
    <?php  
	}      
}

function xt_post_content($class = '') {
	
	$class .= ' xt-post-content';
	
	?>
	<div class="post-body <?php echo esc_attr($class); ?>" itemprop="articleBody">
	
	<?php the_content(); ?>	
		
	</div>
	<?php
}

function xt_get_post_content($class = '') {
	
	ob_start();
    xt_post_content($class);	
    return ob_get_clean();

}


function xt_post_author($link = true, $class = '') {

	$class .= ' xt-post-author';
	
	?>
	<span class="author <?php echo esc_attr($class); ?>" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
		<?php echo __("By", XT_TEXT_DOMAIN); ?> 
		<span itemprop="name">
		<?php if($link) : ?>
		
			<?php the_author_posts_link(); ?>
			
		<?php else: ?>
		
				<?php the_author(); ?>
			
		<?php endif; ?>
		</span>
	</span>
    
    <?php 

}

function xt_post_date($class = '') {
	
	$class .= ' xt-post-author';
	$format = get_option('date_format');
	?>
    <time class="<?php echo esc_attr($class); ?>" datetime="<?php the_time('c'); ?>" itemprop="datePublished"><?php the_time( $format ); ?></time>
    <meta itemprop="dateModified" content="<?php the_modified_time('c') ?>">
	<?php
}

function xt_post_category($link = true, $max = 1) {

	$post_id = get_the_ID();
	$post_type = get_post_type();
	$count = 0;

	?>
	
	<span class="category xt-post-category">
	
		<?php if($post_type == 'post'): ?>
		
			<?php
			$categories = get_the_category();
			$separator = ', ';
			$output = '';
			if($categories){
				foreach($categories as $category) {
					
					if($count >= $max)
						break;
					
					if($link) {
						$output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ), XT_TEXT_DOMAIN ) . '"><span itemprop="articleSection">'.$category->cat_name.'</span></a>'.$separator;
					}else{
						$output .= '<span itemprop="articleSection">'.$category->cat_name.'</span>'.$separator;
					}
					$count++;
				}
			}
			echo trim($output, $separator);
			?>
			
		<?php elseif(taxonomy_exists($post_type.'-category')): ?>
		
			<?php
			$terms = wp_get_post_terms($post_id, $post_type.'-category');
			$i = 0; 
			$total = count($terms) - 1;
			foreach($terms as $term) {
				
				if($count >= $max)
					break;
						
				if($link) {
					
					$term_link = get_term_link( $term );
   
				    // If there was an error, continue to the next term.
				    if ( is_wp_error( $term_link ) ) {
				        continue;
				    }
    
					echo '<a href="'.$term_link.'" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $term->name ) ) . '"><span itemprop="articleSection">'.($term->name).'</span></a>';
					
				}else{
					
					echo '<span itemprop="articleSection">'.($term->name).'</span>';
				}	
				
				if($i < $total)
					echo ', &nbsp;';
				
				$i++;
				$count++;
			}
			?>

		<?php endif; ?>	
			
	</span>

	<?php

}

function xt_post_stats($linkComments = true, $classes=array()) {

	$classes[] = 'xt-post-stats';
	
	$post_id = get_the_ID();
	
	$comments_enabled = xt_comments_enabled();
	$comments_system = xt_comments_system();
	$likes_enabled = (bool)xt_option('likes_enabled');
	
	$viewsCount = xt_get_post_views($post_id);
	?>
	
	<div class="stats <?php echo implode(" ", $classes); ?>">
		<span class="stats-wrap">
			
			<?php
			if($likes_enabled):	
				$likesCount = xt_get_post_likes($post_id);
			?>
			<span class="likes"><i class="fa fa-thumbs-up"></i> <?php echo $likesCount;?></span>
			<meta itemprop="interactionCount" content="UserLikes:<?php echo $likesCount;?>"/>
			<?php endif; ?>

			<span class="views"><i class="fa fa-eye"></i> <?php echo $viewsCount; ?></span>
			<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $viewsCount;?>"/>
			
			<?php if($comments_enabled && $comments_system != 'facebook'): ?>
			<span class="comments">
				<?php 
				$commentsCount = get_comments_number();
				?>	
				<?php if($linkComments): ?>
					<a href="<?php echo esc_url(get_comments_link($post_id)); ?>"><i class="fa fa-comment"></i> <?php echo $commentsCount; ?></a>
				<?php else: ?>
					<i class="fa fa-comment"></i> <?php echo $commentsCount; ?>
				<?php endif; ?>
				<meta itemprop="interactionCount" content="UserComments:<?php echo $commentsCount;?>"/>
			</span>
			<?php endif; ?>
			
		</span>
	</div>
	
    <?php 
}

function xt_parse_attributes($attr = array()) {
	
	$output = '';
	
	if(empty($attr))
		return $output;
		
	$output .= ' ';
		
	foreach($attr as $key => $value) {
		
		if(is_array($value)) {
			
			$output .= $key.'="';
			
			foreach($value as $k => $v) {
				$output .= $k.':'.$value.';';
			}
			
			$output .= '"';
			
		}else{
			
			$output .= $key.'="'.$value.'" ';
		}
		
	}
	return $output;
}

function xt_get_template_part($slug, $name = null){
    ob_start();
    get_template_part($slug, $name);	
    return ob_get_clean();
}

