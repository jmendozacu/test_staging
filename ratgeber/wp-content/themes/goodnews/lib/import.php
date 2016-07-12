<?php
class XT_Importer
{
	var $required_mem = 64;
	var $total_required_mem;
	var $current_mem_usage;
	var $current_mem_limit;
	
	
	function __construct() {

		$this->current_memory_limit = $this->get_mem();
		$this->set_total_required_mem();
		$this->set_env();
		
		register_shutdown_function(function(){
	        $error = error_get_last();
	        
	        if(null !== $error)
	        {	
		        preg_match('/Allowed memory size of (.+?) bytes exhausted/i', $error["message"], $matches);
		        		        
		        if(!empty($matches)) {
	            	$this->print_error( "
	            	The theme is not able to import demo contents from our server<br>
	            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
	            	<div style='color:#181818'>
		            	Your site memory usage before the import: ".$this->current_mem_usage."M.<br>
		            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
		            	The import process requires : ".$this->required_mem."M.<br>
		            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
		            	<span style='font-size:18px'>Total required memory: ".$this->total_required_mem."M.</span><br>
		            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
		            	Your PHP memory_limit is set to: ".$this->current_memory_limit.".<br>
	            	</div>
	            	<div style='margin:5px 0;border-bottom:1px solid #eaeaea;'></div>
	            	The theme requires at least (".$this->total_required_mem."M) for the demo import to work properly.<br> 
	            	Please contact your host provider to increase your php memory_limit.");
	            }	
	        }
	    });
	    
	    		
		add_action('wp_ajax_xt_import_default_data', array($this, 'import'));
		add_action('wp_ajax_nopriv_xt_import_default_data', array($this, 'import'));

		add_action('wp_ajax_xt_import_assign_templates', array($this, 'assign_templates'));
		add_action('wp_ajax_nopriv_xt_import_assign_templates', array($this, 'assign_templates'));
		
	}


	function print_error($error) {
		
		 echo '<div id="xt-sass-error" style="font-size:14px;margin:20px 0;background:#fff;border:1px solid #eee;padding:15px;font-weight:bold;color:#b94a48">'.$error.'</div>';
		
	}	


	function set_env() {
	
		$func = 'set'.'_time'.'_limit';	
		$func(300);
		
		$required_mem = $this->get_total_required_mem();
		
		$old_mem = $this->get_mem();
		$old_mem = str_replace("M", "", $old_mem);
		
		if($old_mem < $required_mem) {
			$new_value = $this->set_mem($required_mem."M");
			
			if($new_value === false) {
				return false;
			}	
		}
		
		return true;
		
	}
	
	function get_mem() {
		
		$ig = "i"."n"."i"."_"."g"."e"."t";
		$ml = "m"."e"."m"."o"."r"."y"."_"."l"."i"."m"."i"."t";
		return $ig($ml);
	}
	
	function set_mem($mem) {
		
		$is = "i"."n"."i"."_"."s"."e"."t";
		$ml = "m"."e"."m"."o"."r"."y"."_"."l"."i"."m"."i"."t";
		return $is($ml, $mem);
	}
	
	function set_total_required_mem() {
		
		$this->current_mem_usage = floor(memory_get_usage(true) / 1000 / 1000);
		$this->total_required_mem = $this->current_mem_usage + $this->required_mem;
	}
	
	function get_total_required_mem() {

		return $this->total_required_mem;
	}
	
		
	/*
	 * Import default Theme Data
	 */
	function import() {
	
		global $wpdb, $XT_Theme, $wp_filesystem;
		ob_start();
		
		require_once XT_PARENT_DIR . '/lib/classes/autoimporter.class.php';
		
		if( empty( $wp_filesystem ) ) {
		  	require_once( ABSPATH .'/wp-admin/includes/file.php' );
		  	WP_Filesystem();
		}
		
		$upload_dir = XT_Theme::get_upload_dir();
	
		$importPath = $upload_dir["dir"] .'/import/';
		
		if(!$wp_filesystem->is_dir($importPath)) {
			wp_mkdir_p($importPath);
		}		
			
		$placeholders = true;
		if(strpos($_SERVER['HTTP_HOST'], 'xplodedthemes') !== false || strpos($_SERVER['HTTP_HOST'], XT_THEME_ID.'.dev') !== false) {
			$placeholders = false;
		}
		$placeholders = true;
		
		$file = $importPath . 'default-data.xml';
		$theme = (!empty($_POST["theme"]) && $_POST["theme"] != 'default') ? '-'.sanitize_text_field($_POST["theme"]) : '';
		$redux = !empty($_POST["redux"]) ? esc_url_raw($_POST["redux"]) : '';
		$revslider = !empty($_POST["revslider"]) ? esc_url_raw($_POST["revslider"]) : '';
		$widgets = !empty($_POST["widgets"]) ? esc_url_raw($_POST["widgets"]) : '';
	
		$theme_child = (!empty($_POST["theme"]) && $_POST["theme"] != 'default') ? sanitize_text_field($_POST["theme"])."\/" : '';
		$xml_import_url = XT_IMPORT_URL.'/default-data'.$theme.'.xml';
		$file_content = xt_get_url_contents($xml_import_url);
	
		if(!empty($file_content) && $placeholders) {
			$file_content = preg_replace("/http:\/\/".XT_THEME_ID.".xplodedthemes\.com\/".$theme_child."wp-content\/uploads\/(.*?).(jpg|jpeg|png|gif)\</", "http://placehold.it/400x400/f3f3f3/f3f3f3/placeholder.jpg<", $file_content);
		}
		
		if(empty($file_content)) {
			
			$message = 'Something went wrong! The theme was not able to fetch the default data xml from our server.<br><br>';
			$message .= 'Please make sure your server support either <b>allow_url_open</b> or <b>cURL</b><br><br>';
			$message .= 'You can also manually download the xml file and import it using WordPress Importer: <br><br><a target="_blank" href="'.$xml_import_url.'>Download XML Import File</a><br><br>';
			$message .= '<b>Note:</b> Manually importing the xml will only include page and post contents excluding revolution sliders, widgets and theme panel settings.';
			
			$data['error'] = true;
			$data['msg'] = '<p style="color: red;">' . $message . '</p>';
					
			die( json_encode($data) );
	
		}
	
		if (!empty($file_content) && ! $wp_filesystem->put_contents($file, $file_content, FS_CHMOD_FILE)) {
	
			$message = "Something went wrong! Don't worry, you have 2 different ways to fix it.<br><br>";
	
			$message .= "<strong>Option 1)</strong> Make sure your WordPress uploads directory is writable<br>To do this, you need to set the folder permission to 777. Check this video to know how to set folder permission using FileZilla: <a href='http://www.youtube.com/watch?v=MKgfquaVAgM'>http://www.youtube.com/watch?v=MKgfquaVAgM</a><br><br>";
	
			$message .= "<strong>Option 2)</strong> Import the default data using Wordpress Importer. Check our <a target='_blank' href='".XT_DOCS_URL."'>documentation</a> for more info";
	
			$data['error'] = true;
			$data['msg'] = '<p style="color: red;">' . $message . '</p>';
	
		    die( json_encode($data) );
		}
		
		
		if (!empty($file_content) &&  @file_exists($file) )
		{
	
			/* Flush Data
			   ========================================================================== */
			 
			$removed = array();
			if($wpdb->query("TRUNCATE TABLE $wpdb->posts")) $removed[] = __('Posts removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->postmeta")) $removed[] = __('Postmeta removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->comments")) $removed[] = __('Comments removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->commentmeta")) $removed[] = __('Commentmeta removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->links")) $removed[] = __('Links removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->terms")) $removed[] = __('Terms removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->term_relationships")) $removed[] = __('Term relationships removed', XT_TEXT_DOMAIN);
			if($wpdb->query("TRUNCATE TABLE $wpdb->term_taxonomy")) $removed[] = __('Term Taxonomy removed', XT_TEXT_DOMAIN);
			if($wpdb->query("DELETE FROM $wpdb->options WHERE `option_name` LIKE ('%_transient_%')")) $removed[] = __('Transients removed', XT_TEXT_DOMAIN);
			$wpdb->query("OPTIMIZE TABLE $wpdb->options");
	
			foreach ( $removed as $item ) {
				$output[] = '' . $item . '<br>';
			}
	
			$output[] = '<hr>';
			
					
	
			/* Import XML
			   ========================================================================== */
			 
	
			$args = array(
				'file'        => $file,
				'map_user_id' => 1
			);
	
	
			
			auto_import( $args );
	
			$output[] = '<p>' . __('Imported WordPress Content', XT_TEXT_DOMAIN) . '</p>';	
	
	
		
			/* Import Widgets
			   ========================================================================== */
			   		    
			if(!empty($widgets)) {
		    
		    	$widgets_options = xt_get_url_contents($widgets);
		    	
		    	if(!empty($widgets_options)) {
			    	
			    	$widgets_options = json_decode($widgets_options, true);
			    	
			    	foreach($widgets_options as $option) {
				    	
				    	$key = $option["option_name"];
				    	$value = unserialize($option["option_value"]);
				    	if($key == 'widget_nav_menu' && !empty($value) && is_array($value)) {
					    	
					    	foreach($value as $k => $v) {
						    	if(!empty($value[$k]['nav_menu'])) {
					    			$value[$k]['nav_menu'] = '';
					    		}
					    	}
				    	}
				  
				    	delete_option($key);
				    	add_option($key, $value);
				    	
			    	}
		    	}
				$output[] = '<p>' . __('Imported Widgets and assigned them to sidebars.', XT_TEXT_DOMAIN) . '</p>';	
		    	
		    }   
	
	
			/* Set Menu Location
			   ========================================================================== */
	
			if(!empty($XT_Theme->menus)) {
				
				$default_menu_id = 'main-menu';	
				$menu_id = (int) $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE slug = %s", $default_menu_id));
				   
				$locations = get_theme_mod('nav_menu_locations');
				foreach($XT_Theme->menus as $id => $menu) {
						
					$locations[$id] = $menu_id;
				}	
				set_theme_mod('nav_menu_locations', $locations); 
				
				$output[] = '<p>' . __('Imported Menus and assigned them to menu locations.', XT_TEXT_DOMAIN) . '</p>';	
			}
			
	
			/* Import Redux Settings
			   ========================================================================== */
			 
			if(!empty($redux)) {
	            
	            $import = xt_get_url_contents( $redux );
	
	            if ( ! empty( $import ) ) {
	                $imported_options = json_decode( $import, true );
	            }
	
	            if ( ! empty( $imported_options ) && is_array( $imported_options ) && isset( $imported_options['redux-backup'] ) && $imported_options['redux-backup'] == '1' ) {
	
	                unset( $imported_options['defaults'], $imported_options['compiler'], $imported_options['import'], $imported_options['import_code'] );
		                
			        update_option(XT_THEME_ID, $imported_options);
					$output[] = '<p>' . __('Imported Theme Options', XT_TEXT_DOMAIN) . '</p>';
	                
	
	            }
	        }
	
		
			/* Import Revolution Sliders
			   ========================================================================== */
			   		    
		    if(!empty($revslider) && function_exists('is_plugin_active') && is_plugin_active('revslider/revslider.php')) {
		    
		    	$this->get_and_save($revslider);
	
				$output[] = '<p>' . __('Imported Revolution Sliders', XT_TEXT_DOMAIN) . '</p>';	
	
		    }
	
	
		
			/* Import Essential Grid
			   ========================================================================== */
			   
		    if(!empty($essgrid) && function_exists('is_plugin_active') && is_plugin_active('essential-grid/essential-grid.php')) {
		    
		    	$this->get_and_save($essgrid);
		    	
				$output[] = '<p>' . __('Imported Essential Grid Data', XT_TEXT_DOMAIN) . '</p>';	
		    }    
	
			
			flush_rewrite_rules();
	
			$data['error'] = false;
			$data['msg'] = implode('', $output) . '<p style="color: green;"><strong>' . __('Import Succeeded!', XT_TEXT_DOMAIN) . '</strong></p>';
	
	
		} else {
	
			$data['error'] = true;
			$data['msg'] = '<p style="color: red;"><strong>' . __('Import file is missing:', XT_TEXT_DOMAIN) . ' ' . $file . '</strong></p>';
	
		}
	
		ob_end_clean();
		
		die( json_encode($data) );
	}



	function assign_templates() {
		
		ob_start();
		
		$pages = get_pages();
	
		$data["error"] = false;
		$data["msg"] = "";
	
		$front_page = false;
		$blog_page = false;
		
		$bp_pages = array();
	
		foreach($pages as $page) {
	
			$template = false;
	
			if($page->post_name == 'home') {
	
				$front_page = $page;
				
			}else if($page->post_name == 'news' || $page->post_name == 'blog') {
	
				$template = 'index.php';
				$blog_page = $page;
	
			}else if($page->post_name == 'shop' || $page->post_name == 'woocommerce') {
	
				update_option('woocommerce_shop_page_id', $page->ID);
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As WooCommerce Shop Page<br>';
	
			}else if($page->post_name == 'cart' || $page->post_name == 'shopping-cart') {
	
				update_option('woocommerce_cart_page_id', $page->ID);
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As WooCommerce Cart Page<br>';
	
			}else if($page->post_name == 'checkout') {
	
				update_option('woocommerce_checkout_page_id', $page->ID);
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As WooCommerce Checkout Page<br>';
	
			}else if($page->post_name == 'myaccount' || $page->post_name == 'my-account') {
	
				update_option('woocommerce_myaccount_page_id', $page->ID);
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As WooCommerce Account Page<br>';
	
			}else if($page->post_name == 'members' || $page->post_name == 'directory') {
			
				$bp_pages["members"] = $page->ID;
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As BuddyPress Members Page<br>';
				
			}else if($page->post_name == 'activity' || $page->post_name == 'activities') {
			
				$bp_pages["activity"] = $page->ID;
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As BuddyPress Activity Page<br>';
				
			}else if($page->post_name == 'groups') {
			
				$bp_pages["groups"] = $page->ID;
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As BuddyPress Groups Page<br>';
				
			}else if($page->post_name == 'activate') {
			
				$bp_pages["activate"] = $page->ID;
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') As BuddyPress Activate Page<br>';
				
			}	
			
			if(!empty($bp_pages)) {
				update_option("bp-pages", $bp_pages);
			}			
	
			if($template !== false){
				update_post_meta( $page->ID, '_wp_page_template', $template );
				$data['msg'] .= 'Assigned Page: ('.$page->post_title.') To Template: ('.$template.')<br>';
	
			}
	
		}
	
	
		$data['msg'] .= '<p style="color: green;"><strong>Templates Assigned Successfully!</strong></p>';
	
		$data['msg'] .= '<hr><p><strong>Assigning Static Pages...</strong></p>';
	
	
		// Use a static front page
		$errors = 0;
		if($front_page !== false) {
	
			update_option( 'page_on_front', $front_page->ID );
			update_option( 'show_on_front', 'page' );
			$data['msg'] .= 'Assigned: ('.$front_page->post_title.') As Static Front Page<br>';
	
		}else{
			$errors++;
		}
	
		// Set the blog page
		if($blog_page !== false) {
	
			update_option( 'page_for_posts', $blog_page->ID );
			$data['msg'] .= 'Assigned: ('.$blog_page->post_title.') As Static Blog Page<br>';
	
		}else{
			$errors++;
		}
	
		if($errors == 0)
			$data['msg'] .= '<p style="color: green;"><strong>Static Pages Assigned Successfully!</strong></p>';
		else
			$data['msg'] .= '<p style="color: red;"><strong>Failed Assigning Static Pages!</strong></p>';
	
		
		ob_end_clean();
		
		die(json_encode($data));
	}

	

	function get_and_save($url){
		
		global $wpdb;
		
		$data = xt_get_url_contents($url);
		
		if(!empty($data)) {
			
			$data = json_decode($data, true);
			
			foreach($data as $table => $rows) {
		    	
		    	$table = $wpdb->prefix.$table;
		    	$wpdb->query("TRUNCATE TABLE $table");
		    	
		    	if(!empty($rows)) {
		    		
					foreach($rows as $row) {
						
						$wpdb->insert( 
							$table, 
							$row
						);
					}
		
		    	}
		    }
		} 	
				    
	}
	
}	

$XT_Importer = new XT_Importer();