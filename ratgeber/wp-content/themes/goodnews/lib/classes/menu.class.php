<?php

class xt_custom_menu {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'add_custom_nav_fields' ) );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_custom_nav_fields'), 10, 3 );
		
		// edit menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_walker'), 10, 2 );

	} // end constructor
	

	
	/**
	 * Add custom fields to $item nav object
	 * in order to be used in custom Walker
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function add_custom_nav_fields( $menu_item ) {
	
	    $menu_item->enable_megamenu = (bool)get_post_meta( $menu_item->ID, '_menu_item_enable_megamenu_value', true );
	    return $menu_item;
	    
	}
	
	/**
	 * Save menu custom fields
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
	
	    // Check if element is properly sent
	    if ( !empty($_REQUEST['menu-item-enable-cat-preview']) && !empty($_REQUEST['menu-item-enable-cat-preview'][$menu_item_db_id])) {
	
	        update_post_meta( $menu_item_db_id, '_menu_item_enable_megamenu_value', 1 );
	        
	    }else{
		    
		    update_post_meta( $menu_item_db_id, '_menu_item_enable_megamenu_value', 0 );
	    }
	    
	}
	
	/**
	 * Define new Walker edit
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	*/
	function edit_walker($walker,$menu_id) {
	
	    return 'xt_nav_edit_walker';
	    
	}

}

// instantiate plugin's class
$GLOBALS['xt_custom_menu'] = new xt_custom_menu();

