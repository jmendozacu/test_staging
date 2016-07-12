<?php

/**
 * Upgrade
 *
 * All the functionality for upgrading XT Themes
 *
 * @since 1.0.0
 */

function xt_upgrade () {
	global $XT_Theme, $wpdb, $wp_filesystem, $post; 

	$xt_version_option_key = XT_THEME_ID. '_version';

	$xt_theme  = wp_get_theme();
	$old_version = get_option( $xt_version_option_key, $XT_Theme->parent_version ); // false
	$new_version = $XT_Theme->parent_version; 

	if ( $new_version !== $old_version )
	{

		/*
		 * 1.0.4
		 *
		 * @created 2015-02-21
		 */

		if ( $old_version < '1.0.4' )
		{
			$xt_upload_dir = XT_Theme::get_upload_dir();

			if( empty( $wp_filesystem ) ) {
				require_once( ABSPATH .'/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
		
			if($wp_filesystem->is_dir($xt_upload_dir['dir'].'/assets/')) { 
				 $wp_filesystem->delete( $xt_upload_dir['dir'].'/assets/', true, 'd' );
				 $wp_filesystem->delete( $xt_upload_dir['dir'].'/bower_components/', true, 'd' );
				 $wp_filesystem->delete( $xt_upload_dir['dir'].'/cache/', true, 'd' );
			}
			
			update_option($xt_version_option_key, '1.0.4');
			
		}
		

		update_option($xt_version_option_key, $new_version);
		
		XT_Theme::sass_compile_flag();
		
		xt_redirect_after_migration();
		
	}else{
		
		update_option($xt_version_option_key, $new_version);
	}
}

add_action('init', 'xt_upgrade', 10);


function xt_redirect_after_migration() {

	global $post, $wp;
	
	if(is_admin()) {
		
		$redirect = admin_url('admin.php?page='.XT_THEME_OPTIONS_PAGE);
		
	}else{
		
		$link = get_permalink();
		if(!empty($link)) {
			$redirect = $link;
		}else{
			$redirect = home_url('/');
		}
	}
	
	wp_redirect($redirect);
	exit;
}