<?php

/*
 * Register recommended plugins for this theme.
 */

function xt_register_required_plugins ()
{
	$plugins = array(
	    array(
	        'name' 					=> 'Envato WordPress Toolkit',
	        'slug' 					=> 'envato-wordpress-toolkit',
	        'source' 				=> XT_LIB_DIR . '/plugins/envato-wordpress-toolkit.zip',
	        'required' 				=> false,
	        'version' 				=> '1.7.1',
	        'force_activation' 		=> false,
	        'force_deactivation' 	=> false,
	        'external_url' 			=> '',
	    ),
        array(
            'name'                  => 'Visual Composer', // The plugin name
            'slug'                  => 'js_composer', // The plugin slug (typically the folder name)
            'file_path'				=> 'js_composer/js_composer.php',
            'source'                => XT_LIB_DIR . '/plugins/js_composer.zip', // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '4.4.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        ),                    
        array(
            'name'                  => 'Slider Revolution', // The plugin name
            'slug'                  => 'revslider', // The plugin slug (typically the folder name)
            'file_path'				=> 'revslider/revslider.php',
            'source'                => XT_LIB_DIR . '/plugins/revslider.zip', // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '4.6.5', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        ), 
        array(
            'name'                  => 'XT Galleria', // The plugin name
            'slug'                  => 'xt-galleria', // The plugin slug (typically the folder name)
            'file_path'				=> 'xt-galleria/xt-galleria.php',
            'source'                => XT_LIB_DIR . '/plugins/xt-galleria.zip', // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '1.0.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        ),   
        array(
            'name'                  => 'XT Optimum Speed', // The plugin name
            'slug'                  => 'xt-optimum-speed', // The plugin slug (typically the folder name)
            'file_path'				=> 'xt-optimum-speed/xt-optimum-speed.php',
            'source'                => XT_LIB_DIR . '/plugins/xt-optimum-speed.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        ), 
		array(
            'name'                  => 'MailChimp Widget', // The plugin name
            'slug'                  => 'mailchimp-widget', // The plugin slug (typically the folder name)
            'file_path'				=> 'mailchimp-widget/nm_mailchimp.php',
            'source'                => XT_LIB_DIR . '/plugins/mailchimp-widget.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '3.2.4', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        ),    
		array(
            'name'                  => 'Simple Ads Manager', // The plugin name
            'slug'                  => 'simple-ads-manager', // The plugin slug (typically the folder name)
            'file_path'				=> 'simple-ads-manager/simple-ads-manager.php',
            'source'                => XT_LIB_DIR . '/plugins/simple-ads-manager.zip', // The plugin source
            'required'              => false, // If false, the plugin is only 'recommended' instead of required
            'version'               => '2.5.94', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        ),                  
		array(
			'name'		=>	'WooCommerce',
			'slug'		=>	'woocommerce',
			'required'	=>	false
		),            
		array(
			'name'     => 'BuddyPress',
			'slug'     => 'buddypress',
			'required' => false
		),
		array(
			'name'     => 'Easy Foundation Shortcodes',
			'slug'     => 'easy-foundation-shortcodes',
			'required' => false
		),			
		array(
			'name'     => 'Disqus Comment System',
			'slug'     => 'disqus-comment-system',
			'required' => false
		),  
		array(
			'name'     => 'Video Thumbnails',
			'slug'     => 'video-thumbnails',
			'required' => false
		),
		array(
			'name'     => 'Contact Form 7',
			'slug'     => 'contact-form-7',
			'required' => false
		),
		array(
			'name'     => 'Simple Page Ordering',
			'slug'     => 'simple-page-ordering',
			'required' => false
		),
		array(
			'name'     => 'Duplicate Post',
			'slug'     => 'duplicate-post',
			'required' => false
		),
		array(
			'name'		=>	'Wordpress SEO',
			'slug'		=>	'wordpress-seo',
			'required'	=>	false
		),
       
	);

	$config = array(
		'domain'       => XT_TEXT_DOMAIN,
		'has_notices'  => true, // Show admin notices or not
		'is_automatic' => true // Automatically activate plugins after installation or not
	);


	if(is_admin() && function_exists('get_plugin_data')) {

		foreach($plugins as $plugin) {

			if(!empty($plugin['file_path']) && is_plugin_active($plugin['file_path']) && !empty($plugin["version"])) {
			
				$version = $plugin["version"];
				$plugin_path = WP_PLUGIN_DIR.'/'.$plugin['slug'];
				$plugin_file = WP_PLUGIN_DIR.'/'.$plugin['file_path'];
				$plugin_source = $plugin['source'];
				$data = get_plugin_data($plugin_file);
				if(!empty($data["Version"]) && ($data["Version"] < $version) && empty($_GET["tgmpa-install"])) {
			
					deactivate_plugins($plugin_file);
					xt_delete_dir($plugin_path);

				}
			}
		}
	}
	
	tgmpa($plugins, $config);

}

add_action('tgmpa_register', 'xt_register_required_plugins');



function xt_delete_dir($dirname) {
     if (is_dir($dirname))
          $dir_handle = @opendir($dirname);
	 if (!$dir_handle)
	      return false;
	 while($file = readdir($dir_handle)) {
	       if ($file != "." && $file != "..") {
	            if (!is_dir($dirname."/".$file))
	                 @unlink($dirname."/".$file);
	            else
	                 xt_delete_dir($dirname.'/'.$file);
	       }
	 }
	 @closedir($dir_handle);
	 @rmdir($dirname);
	 return true;
}

