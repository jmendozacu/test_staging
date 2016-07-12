<?php

class xt_nav_walker extends Walker_Nav_Menu
{
	/**
	 * What the class handles.
	 *
	 * @see Walker::$tree_type
	 * @since 3.0.0
	 * @var string
	 */

	var $category_sub_menus = array();
	 
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	
	/**
	 * Database fields to use.
	 *
	 * @see Walker::$db_fields
	 * @since 3.0.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );


	function __construct($menu_id = null) {

		add_filter('nav_menu_css_class', array($this, 'category_nav_class'), 10, 2 );
		add_filter('wp_nav_menu', array($this, 'append_category_submenus'), 10, 2 );
	}
	

	
	function category_nav_class( $classes, $item ){
	
		$item->category = array();
		
	    if( 'category' == $item->object ){
	        $category = get_category( $item->object_id );
	        $classes[] = 'menu-category-slug-' . $category->slug;
	        $classes[] = 'menu-category-id-' . $item->object_id;
	    }
	    return $classes;
	}

	function append_category_submenus( $nav_menu, $args ){

		if(($args->theme_location == 'main-menu') && !empty($this->category_sub_menus)) {
		
	        $nav_menu = $nav_menu.implode("", $this->category_sub_menus);
	    }
	    return $nav_menu;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		
		$dropdown_class = "";
		if($depth == 0 && !empty($args->first_dropdown_class)) {
			$dropdown_class .= $args->first_dropdown_class;
		}
		if(!empty($args->dropdown_class)) {
			$dropdown_class .= $args->dropdown_class;
		}

		if(!empty($args->megamenu_enabled) && !empty($args->has_category_dropdown)) {
		
			$dropdown_class .= ' hide-dropdown';
		}
		$output .= "\n$indent<ul class=\"dropdown ".$dropdown_class."\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";

		$args->has_category_dropdown = false;
	}


	function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
    {
        // check, whether there are children for the given ID and append it to the element with a (new) ID
        $element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]);
		$element->childs = $element->hasChildren ? $children_elements[$element->ID] : false;
		
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
    
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
	
		global $post;
		
		$backupPost = $post;
	
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		$args->has_category_dropdown = false;
			
		if(!empty($item->enable_megamenu) && !empty($args)) {
			
			$args->megamenu_enabled = (bool)$item->enable_megamenu;
			
			if( !empty($args->megamenu_enabled) && ($args->theme_location == 'main-menu') && (in_array("menu-item-type-taxonomy", $classes)) && ($depth == 0)) {
	
				$classes[] = 'has-category-dropdown';
				$args->has_category_dropdown = $item->object_id;
	
				if($item->object == 'category') {
				
					$query = new WP_Query(array( 'posts_per_page' => 6, 'cat' => $item->object_id ));
					
				}else{
				
					$query = new WP_Query(array( 
						'posts_per_page' => 6, 
						'tax_query' => array( 
							array(
								'taxonomy' => $item->object, 
								'field' => 'id',  
								'terms' => array( $item->object_id ),
								'include_children' => true, 
								'operator' => 'IN'
							)
						)
					));
				}
					
				$posts = $query->posts;
				
				$category_sub_menu = "\n$indent<div id=\"category_posts_sub_menu_".$item->object_id."\" class=\"category_posts_sub_menu inactive\" data-cid=\"".$item->object_id."\"><div class=\"row in-container\"><div class=\"medium-12 column\">";
				
				ob_start();
	   
	   
				$show_tags = (bool)xt_option('megamenu-show-tags-links', null, 1);
				$show_subcategories = (bool)xt_option('megamenu-show-subcategory-links', null, 1);
				$show_submenus = (bool)xt_option('megamenu-show-submenu-links', null, 1);


				$all_tags = array();
				foreach($posts as $post) {
					setup_postdata($post);
					
					// Get Tags
					if($show_tags) {
						$tags = wp_get_post_tags($post->ID);
						foreach($tags as $tag) {
							$all_tags[$tag->term_id] = $tag;
						}
					}		
					?>
					<div class="category_submenu_post" itemscope="" itemtype="http://schema.org/BlogPosting">
					<?php
					
					xt_post_thumbnail('th-medium', true);
					xt_post_title('h5', '', true);
					
					?>
					</div>
					<?php
				}
				wp_reset_postdata();
	
	
				// Get Related Categories
				$subcategories = array();
				if($show_subcategories) {
					$args = array('child_of' => $item->object_id);
					$subcategories = get_categories( $args );
				}

				if($show_submenus) {
					$submenu_items = $item->hasChildren && !empty($item->childs) ? $item->childs : false;
				}
				
				
				$post = $backupPost;
				
				$category_sub_menu .= ob_get_clean();
				
				
				$category_sub_menu .= "</div></div><div class=\"row in-container\"><div class=\"medium-12 column\">";
				
				if(!empty($all_tags) || !empty($subcategories) || !empty($submenu_items)) {
					$category_sub_menu .= '<div class="category_links_wrap">';
				}
				
				if(!empty($all_tags)) {
				
					$category_sub_menu .= '<span class="category_links megamenu-tags">';
					$category_sub_menu .= '<span>'.__("In this category", XT_TEXT_DOMAIN).': </span>';
					
					foreach($all_tags as $tag) {
						$category_sub_menu .=' <a href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a> ';
					}
					$category_sub_menu .= '</span>';
					$category_sub_menu .='<span class="spacer"></span>';
				}
				
				if(!empty($subcategories)) {
					
					$category_sub_menu .= '<span class="category_links megamenu-subcategories">';
					$category_sub_menu .= '<span>'.__("Subcategories", XT_TEXT_DOMAIN).': </span>';
					
					foreach($subcategories as $cat) {
						$category_sub_menu .=' <a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a> ';
					}
					$category_sub_menu .= '</span>';
					$category_sub_menu .='<span class="spacer"></span>';
				
				}

				if(!empty($submenu_items)) {
					
					$category_sub_menu .= '<span class="category_links megamenu-submenu-items">';
					$category_sub_menu .= '<span>'.__("Other Links", XT_TEXT_DOMAIN).': </span>';
					
					foreach( $submenu_items as $menu_item ) {
		           		$category_sub_menu .= '<a href="' . $menu_item->url . '">' . $menu_item->title . '</a>';
		        	}
		        	
					$category_sub_menu .= '</span>';
		        	$category_sub_menu .='<span class="spacer"></span>';
				
				}
				
				if(!empty($all_tags) || !empty($subcategories) || !empty($submenu_items)) {
					$category_sub_menu .= '</div>';
				}
				
				
				$category_sub_menu .= "</div></div></div>";
				
				$this->category_sub_menus[$item->object_id] = $category_sub_menu;
			}
		
		}
		
		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = str_replace('menu-item-has-children', 'has-dropdown not-click', $class_names);

		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';


		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @since 3.0.1
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .' data-itemid="'.$item->ID.'" data-objectid="'.$item->object_id.'">';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @since 3.6.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of wp_nav_menu() arguments.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = !empty($args->before) ? $args->before : '';
		$item_output .= '<a'. $attributes .'>';
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= (!empty($args->link_before) ? $args->link_before : ''). apply_filters( 'the_title', $item->title, $item->ID ) . (!empty($args->link_after) ? $args->link_after : '');
		$item_output .= '</a>';
		$item_output .= !empty($args->after) ? $args->after : '';

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Page data object. Not used.
	 * @param int    $depth  Depth of page. Not Used.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
	
}


class xt_nav_edit_walker extends Walker_Nav_Menu  {
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl(&$output, $depth = 0, $args = array()) {	
	}
	
	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl(&$output, $depth = 0, $args = array()) {
	}
	
	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
	    global $_wp_nav_menu_max_depth;
	   
	    $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
	
	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
	    ob_start();
	    $item_id = esc_attr( $item->ID );
	    $removed_args = array(
	        'action',
	        'customlink-tab',
	        'edit-menu-item',
	        'menu-item',
	        'page-tab',
	        '_wpnonce',
	    );
	
	    $original_title = '';
	    if ( 'taxonomy' == $item->type ) {
	        $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
	        if ( is_wp_error( $original_title ) )
	            $original_title = false;
	    } elseif ( 'post_type' == $item->type ) {
	        $original_object = get_post( $item->object_id );
	        $original_title = $original_object->post_title;
	    }
	
	    $classes = array(
	        'menu-item menu-item-depth-' . $depth,
	        'menu-item-' . esc_attr( $item->object ),
	        'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
	    );
	
	    $title = $item->title;
	
	    if ( ! empty( $item->_invalid ) ) {
	        $classes[] = 'menu-item-invalid';
	        /* translators: %s: title of menu item which is invalid */
	        $title = sprintf( __( '%s (Invalid)', XT_TEXT_DOMAIN ), $item->title );
	    } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
	        $classes[] = 'pending';
	        /* translators: %s: title of menu item in draft status */
	        $title = sprintf( __('%s (Pending)', XT_TEXT_DOMAIN), $item->title );
	    }
	
	    $title = empty( $item->label ) ? $title : $item->label;
	
	    ?>
	    <li id="menu-item-<?php echo esc_attr($item_id); ?>" class="<?php echo implode(' ', $classes ); ?>">
	        <dl class="menu-item-bar">
	            <dt class="menu-item-handle">
	                <span class="item-title"><?php echo esc_html( $title ); ?></span>
	                <span class="item-controls">
	                    <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
	                    <span class="item-order hide-if-js">
	                        <a href="<?php
	                            echo wp_nonce_url(
	                                add_query_arg(
	                                    array(
	                                        'action' => 'move-up-menu-item',
	                                        'menu-item' => $item_id,
	                                    ),
	                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                                ),
	                                'move-menu_item'
	                            );
	                        ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
	                        |
	                        <a href="<?php
	                            echo wp_nonce_url(
	                                add_query_arg(
	                                    array(
	                                        'action' => 'move-down-menu-item',
	                                        'menu-item' => $item_id,
	                                    ),
	                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                                ),
	                                'move-menu_item'
	                            );
	                        ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
	                    </span>
	                    <a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
	                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
	                    ?>"><?php _e( 'Edit Menu Item' , XT_TEXT_DOMAIN); ?></a>
	                </span>
	            </dt>
	        </dl>
	
	        <div class="menu-item-settings" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
	            <?php if( 'custom' == $item->type ) : ?>
	                <p class="field-url description description-wide">
	                    <label for="edit-menu-item-url-<?php echo esc_attr($item_id); ?>">
	                        <?php _e( 'URL' , XT_TEXT_DOMAIN); ?><br />
	                        <input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
	                    </label>
	                </p>
	            <?php endif; ?>
	            <p class="description description-thin">
	                <label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
	                    <?php _e( 'Navigation Label' , XT_TEXT_DOMAIN); ?><br />
	                    <input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
	                </label>
	            </p>
	            <p class="description description-thin">
	                <label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
	                    <?php _e( 'Title Attribute' , XT_TEXT_DOMAIN); ?><br />
	                    <input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
	                </label>
	            </p>
	            <p class="field-link-target description">
	                <label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
	                    <input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->target, '_blank' ); ?> />
	                    <?php _e( 'Open link in a new window/tab' , XT_TEXT_DOMAIN); ?>
	                </label>
	            </p>
	            <p class="field-css-classes description description-thin">
	                <label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
	                    <?php _e( 'CSS Classes (optional)' , XT_TEXT_DOMAIN); ?><br />
	                    <input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
	                </label>
	            </p>
	            <p class="field-xfn description description-thin">
	                <label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
	                    <?php _e( 'Link Relationship (XFN)' , XT_TEXT_DOMAIN); ?><br />
	                    <input type="text" id="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
	                </label>
	            </p>
	            <p class="field-description description description-wide">
	                <label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
	                    <?php _e( 'Description' , XT_TEXT_DOMAIN); ?><br />
	                    <textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
	                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', XT_TEXT_DOMAIN); ?></span>
	                </label>
	            </p>        
	            <?php
	            /* New fields insertion starts here */
	            if($depth == 0 && !empty($item->object) && ($item->type == 'taxonomy')):
	            ?>      
	            <p class="field-custom description description-wide">
	                <label for="edit-menu-item-enable-cat-preview-<?php echo esc_attr($item_id); ?>">
	                    <input type="checkbox" id="edit-menu-item-enable-cat-preview-<?php echo esc_attr($item_id); ?>" value="1" name="menu-item-enable-cat-preview[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->enable_megamenu, '1' ); ?> />
	                    <?php _e( 'Enable category posts submenu' , XT_TEXT_DOMAIN); ?><br />
	                </label>	                
	            </p>
	            <?php
	            endif;
	            /* New fields insertion ends here */
	            ?>
	            <div class="menu-item-actions description-wide submitbox">
	                <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
	                    <p class="link-to-original">
	                        <?php printf( __('Original: %s', XT_TEXT_DOMAIN), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
	                    </p>
	                <?php endif; ?>
	                <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
	                echo wp_nonce_url(
	                    add_query_arg(
	                        array(
	                            'action' => 'delete-menu-item',
	                            'menu-item' => $item_id,
	                        ),
	                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                    ),
	                    'delete-menu_item_' . $item_id
	                ); ?>"><?php _e('Remove', XT_TEXT_DOMAIN); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
	                    ?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php _e('Cancel', XT_TEXT_DOMAIN); ?></a>
	            </div>
	
	            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
	            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
	            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
	            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
	            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
	            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
	        </div><!-- .menu-item-settings-->
	        <ul class="menu-item-transport"></ul>
	    <?php
	    
	    $output .= ob_get_clean();

	 }

}


