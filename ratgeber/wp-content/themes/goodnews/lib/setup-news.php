<?php

/*-----------------------------------------------------------------------------------*/
/* News Management
/*-----------------------------------------------------------------------------------*/

function xt_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'News';
    $submenu['edit.php'][5][0] = 'News';
    $submenu['edit.php'][10][0] = 'Add New';
    $submenu['edit.php'][16][0] = 'News Tags';
    echo '';
}
function xt_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'News';
    $labels->singular_name = 'News';
    $labels->add_new = 'Add New';
    $labels->add_new_item = 'Add New Article';
    $labels->edit_item = 'Edit Article';
    $labels->new_item = 'Article';
    $labels->view_item = 'View Article';
    $labels->search_items = 'Search Articles';
    $labels->not_found = 'No News found';
    $labels->not_found_in_trash = 'No news found in Trash';
    $labels->all_items = 'All News';
    $labels->menu_name = 'News';
    $labels->name_admin_bar = 'Article';
}
 
add_action( 'admin_menu', 'xt_change_post_label' );
add_action( 'init', 'xt_change_post_object' );



function xt_manage_post_columns ($columns)
{
	$xt_cols = array(
		'icon' => ''
	);

	if ( function_exists('xt_array_insert') )
		$columns = xt_array_insert($columns, $xt_cols, 'title', 'before');
	else
		$columns = array_merge($columns, $xt_cols);

	$columns['date'] = __('Published', XT_TEXT_DOMAIN);	// Renamed date column

	return $columns;
}

add_filter('manage_posts_columns', 'xt_manage_post_columns');


function xt_manage_post_custom_column ($column, $post_id)
{
	switch ($column)
	{
		case 'icon':
			$att_title = _draft_or_post_title();
?>
				<a href="<?php echo esc_url(get_edit_post_link( $post_id, true )); ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', XT_TEXT_DOMAIN ), $att_title ) ); ?>"><?php
					if ( $thumb = get_the_post_thumbnail( $post_id, array(80, 60) ) )
						echo ($thumb);
					else
						echo '<img width="46" height="60" src="' . wp_mime_type_icon('image/jpeg') . '" alt="">';
				?></a>
<?php
			break;
	}
}

add_action('manage_posts_custom_column', 'xt_manage_post_custom_column', 10, 2);



function xt_admin_posts_filter( &$query )
{
    if ( 
        is_admin() 
        AND 'edit.php' === $GLOBALS['pagenow']
        AND isset( $_GET['p_format'] )
        AND '-1' != $_GET['p_format']
        )
    {
	    
	    $format = intVal($_GET['p_format']);
	    $term = get_term($format, 'post_format');
	    
        $query->query_vars['tax_query'] = array( array(
             'taxonomy' => 'post_format'
            ,'field'    => 'slug'
            ,'terms'    => array( $term->slug )
        ) );
    }
}
add_filter( 'parse_query', 'xt_admin_posts_filter' );


function xt_restrict_manage_posts_format()
{
	global $typenow;
    if ( $typenow != 'post' )
    	return false;
    
	$selected = isset($_GET['p_format']) ? intVal($_GET['p_format']) : null;

    wp_dropdown_categories( array(
         'taxonomy'         => 'post_format'
        ,'hide_empty'       => 0
        ,'name'             => 'p_format'
        ,'show_option_none' => __('Select Post Format', XT_TEXT_DOMAIN)
        ,'selected'			=> $selected
    ) );
}
add_action( 'restrict_manage_posts', 'xt_restrict_manage_posts_format' );




/*-----------------------------------------------------------------------------------*/
/* Page Management
/*-----------------------------------------------------------------------------------*/

// Register Custom Columns & Unregister Default Columns

function xt_manage_pages_columns ( $columns )
{
	$xt_cols = array(
		'template' => __('Page Template', XT_TEXT_DOMAIN)
	);

	if ( function_exists('xt_array_insert') )
		$columns = xt_array_insert($columns, $xt_cols, 'title', 'after');
	else
		$columns = array_merge($columns, $xt_cols);

	return $columns;
}
add_filter('manage_pages_columns', 'xt_manage_pages_columns');



// Display Custom Columns
function xt_manage_pages_custom_column ( $column, $post_id )
{
	switch ($column)
	{
		case 'template':
			$output = ''; // __('Default')
			$tpl = get_post_meta( $post_id, '_wp_page_template', true);
			$templates = get_page_templates();
			ksort($templates);
			foreach ( array_keys($templates) as $template )
			{
				if ( $tpl == $templates[$template] ) {
					$output = $template;
					break;
				}
			}
			echo esc_html($output);
		break;

	}
}
add_action('manage_pages_custom_column', 'xt_manage_pages_custom_column', 10, 2);



