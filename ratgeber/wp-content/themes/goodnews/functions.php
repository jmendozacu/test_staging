<?php
class XT_Theme{

	var $id;
	var $name;
	var $fullname;
	var $description;
	var $uri;
	var $author;
	var $author_uri;
	var $tags;
	
	var $menus = array();
	var $sidebars = array();
	var $widgets = array();
	
	var $updates = array();
	var $launch_date;
	var $parent_version;
		
	public static $sass_compiler;
	public static $assets = array();
	
	public static $post_formats = array( 'gallery', 'video');
	public static $image_sizes = array( 
		'th-small' => array('name'=>'Small', 'width' => 165, 'height' => 110, 'crop' => true), 
		'th-medium' => array('name'=>'Medium','width' => 270, 'height' => 180, 'crop' => true), 
		'th-large' => array('name'=>'Large','width' => 480, 'height' => 320, 'crop' => true), 
		'th-xlarge' => array('name'=>'XLarge','width' => 1132, 'height' => 670, 'crop' => true), 
	);

	function __construct($id, $name) {

		$this->setThemeData($id, $name);
		$this->constants();
		$this->loadUpdates();
		$this->includes();
		$this->classes();
		$this->modules();
		$this->redux();
		$this->acf();
		$this->actions();
		$this->filters();
		
	}	
	
	function setThemeData($id, $name) {
		
		$this->id = $id;
		$this->name = $name;
		$this->textdomain = $this->id;
		
		if ( is_child_theme() ) {
			$this->textdomain .= '-child';
		}

		$theme_data = wp_get_theme();
		$this->fullname = $theme_data->get('Name');
		$this->description = $theme_data->get('Description');
		$this->uri = $theme_data->get('ThemeURI');
		$this->author = $theme_data->get('Author');
		$this->author_uri = $theme_data->get('AuthorURI');
		$this->version = $theme_data->get('Version');
		$this->tags = $theme_data->get('Tags');			
	}
	
	function constants() {
		
		@define( 'XT_THEME_ID', $this->id );
		@define( 'XT_THEME_NAME', $this->name );
		@define( 'XT_TEXT_DOMAIN', $this->textdomain );
		@define( 'XT_THEME_VERSION', $this->version );
		@define( 'XT_THEME_OPTIONS_PAGE', XT_THEME_ID.'_options');
		@define( 'XT_WIDGET_PREFIX', $this->name.' : ' );
		@define( 'XT_VC_GROUP', 'XplodedThemes Widgets');
		@define( 'XT_SIDEBAR_PREFIX', $this->id . '_sidebar_' );
		
		@define( 'XT_PARENT_DIR', get_template_directory() );
		@define( 'XT_CHILD_DIR',  get_stylesheet_directory() );
		
		@define( 'XT_PARENT_URL', get_template_directory_uri() );
		@define( 'XT_CHILD_URL',  get_stylesheet_directory_uri() );
		
		@define( 'XT_ASSETS_DIR', XT_PARENT_DIR.'/assets');
		@define( 'XT_LIB_DIR', XT_PARENT_DIR.'/lib');
		@define( 'XT_ADMIN_DIR', XT_LIB_DIR.'/admin');
		@define( 'XT_WIDGETS_DIR', XT_LIB_DIR.'/widgets');
		@define( 'XT_CLASSES_DIR', XT_LIB_DIR.'/classes');
		@define( 'XT_MOD_DIR', XT_LIB_DIR.'/modules');

		@define( 'XT_ASSETS_URL', XT_PARENT_URL.'/assets');
		@define( 'XT_LIB_URL', XT_PARENT_URL.'/lib');
		@define( 'XT_ADMIN_URL', XT_LIB_URL.'/admin');
		@define( 'XT_WIDGETS_URL', XT_LIB_URL.'/widgets');
		@define( 'XT_CLASSES_URL', XT_LIB_URL.'/classes');
		@define( 'XT_MOD_URL', XT_LIB_URL.'/modules');
				
		@define( 'XT_DOCS_URL', 'http://docs.xplodedthemes.com/'.$this->id);
		@define( 'XT_IMPORT_URL', 'http://xplodedthemes.com/import/'.$this->id);
		
		if(!defined('FS_METHOD')) define('FS_METHOD','direct');
		if(!defined('FS_CHMOD_DIR')) define('FS_CHMOD_DIR', (0775 & ~ umask()));
		if(!defined('FS_CHMOD_FILE')) define('FS_CHMOD_FILE', (0664 & ~ umask()));
	}

	function actions() {
		
		add_action('after_setup_theme', array(&$this, 'setup'));
		add_action('after_switch_theme', array(&$this, 'activation'));
		add_action('admin_head',  array(&$this, 'admin_head'));
		add_action('admin_bar_menu', array(&$this, 'admin_bar'), 999 );

		add_action('init', array(&$this, 'export'));
		add_action('admin_init',  array(&$this, 'admin_init'));
		
		add_action('widgets_init', array(&$this, 'widgets'));
		
		add_action('wp_head', array(&$this, 'set_metadata'), 10);
		add_action('wp_head', array(&$this, 'set_comments_type'), 300);
	
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'), 100);
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_styles'),100);
		
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'), 100);
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_scripts'), 100);
		
		add_action('wp_footer', array(&$this, 'footer'), 10000);

		do_action('xt_optimum_speed_loaded'); //used by our plugins

	}
	
	function filters() {
		
		// Load Filters
		require_once(XT_LIB_DIR.'/filters.php');
		
	}
	
	function admin_init() {
		
		$this->write_updates();	
		$this->plugins_loaded();	
	}
	
	function export() {
		

		function export_revslider() {
		
			global $wpdb;
			
			$data = array();
	
			if(function_exists('is_plugin_active') && is_plugin_active('revslider/revslider.php')) {
			
				$data['revslider_css'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'revslider_css', ARRAY_A);
				$data['revslider_layer_animations'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'revslider_layer_animations', ARRAY_A);
				$data['revslider_sliders'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'revslider_sliders', ARRAY_A);
				$data['revslider_slides'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'revslider_slides', ARRAY_A);
	
			}	
			
			die(json_encode($data));
			
		}
	
		function export_essgrid() {
		
			global $wpdb;
			
			$data = array();
			
			if(function_exists('is_plugin_active') && is_plugin_active('essential-grid/essential-grid.php')) {
	
				$data['eg_grids'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eg_grids', ARRAY_A);
				$data['eg_item_elements'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eg_item_elements', ARRAY_A);
				$data['eg_item_skins'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eg_item_skins', ARRAY_A);
				$data['eg_navigation_skins'] = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eg_navigation_skins', ARRAY_A);
				
			}
			
			die(json_encode($data));	
		}
			
		function export_widgets() {
		
			global $wpdb;
			
			$widget_options = $wpdb->get_results('SELECT option_name, option_value FROM '.$wpdb->prefix.'options WHERE option_name LIKE "widget_%" || option_name = "sidebars_widgets" || option_name = "xt-dynamic-sidebar-settings"', ARRAY_A);
	
			die(json_encode($widget_options));
				
		}
		
						
		if(empty($_GET["export"])) 
			return false;
			
		
		$export = $_GET["export"];
		
		if($export == 'revslider') {
			
			export_revslider();
			
		}else if($export == 'widgets') {
			
			export_widgets();
			
		}else if($export == 'essgrid') {
			
			export_essgrid();
			
		}	

	
	}

	
	function setup() {
		
		global $content_width;
		
		if ( ! isset( $content_width ) ) 
			$content_width = 1132;
		
		$this->setAssetsPaths();
		$this->init_sass();
		$this->register_menus();
		$this->supports();		
		$this->language();
		$this->visual_composer();
		$this->add_editor_styles();

	}


	function init_sass() {

		global $pagenow;
		
		$main_style = self::getCssDir().'/style.css';

		if(!file_exists($main_style) || (current_user_can('manage_options') && isset($_GET["compile"]))) {
			
			self::sass_compile_flag();
		}
		
		if(current_user_can('manage_options')) {
			
			add_action( 'wp_ajax_xt_compile_sass', array('XT_Theme', 'sass_compile') );
			add_action( 'wp_ajax_nopriv_xt_compile_sass', array('XT_Theme', 'sass_compile') );
			
			add_action('wp_footer', array('XT_Theme', 'sass_compile_js') , 100);
			
			if(
				(!empty($_GET["page"]) && $_GET["page"] == XT_THEME_OPTIONS_PAGE && !empty($_GET["settings-updated"])) || 
				(!empty($pagenow) && $pagenow == 'options-general.php')
			) {

			 	add_action('admin_footer', array('XT_Theme', 'sass_compile_js') );		
			}	
		}
	}
	
	public static function sass_compile_flag() {

		if(!get_option('xt-compile')) {

			add_option('xt-compile', 1, '', 'no');
		}	
	}

	public static function sass_compile_js() {
	?>	
		<script id="xt-sass-compile" type="text/javascript">
		  if ( undefined !== window.jQuery ) {
			  	 
		  	jQuery(document).ready(function($) {
			  	 
			  	var xt_sass_is_admin = <?php echo (is_admin() ? 'true' : 'false');?>;
			  	var xt_sass_message = '<div id="sass-compilation-loading" class="redux-field-warnings notice-yellow"><strong><i class="fa fa-refresh fa-spin fa-fw"></i> SASS compilation in progress...</strong></div>';
				$(xt_sass_message).appendTo('#redux-sticky').fadeIn();
								
		    	$.ajax({
			        url: '<?php echo admin_url('admin-ajax.php'); ?>',
			        type: 'post',
			        data: {
			            'action':'xt_compile_sass'
			        },
			        success:function(data) {
				        
				        if(xt_sass_is_admin) {
					        $('#sass-compilation-loading').remove();
				        }
				        
				        if(data.search('xt-sass-error') != -1) {
					        if(xt_sass_is_admin) {
						        
						        xt_sass_message = '<div class="redux-field-errors notice-red"><strong>'+data+'</strong></div>';
								$(xt_sass_message).appendTo('#redux-sticky').fadeIn();
				        		
				        	}else{
					        	
					        	xt_sass_message = '<div style="padding:20px;background-color:#FFE0E0;"><strong>This message is only shown to logged in admin user.</strong><br>'+data+'</div>';
				        		$('body').prepend(xt_sass_message);
					        }	
					        
				        }else if(xt_sass_is_admin) {
					        
					        xt_sass_message = '<div class="saved_notice admin-notice notice-green"><strong><i class="fa fa-check"></i> Successfully compiled SASS files into CSS!</strong></div>';
							$(xt_sass_message).appendTo('#redux-sticky').fadeIn();
					    }	
			        },
			        error: function(errorThrown){}
			    });
		    
		    }); 
		  }
		</script>
	<?php	
	}
	
	
	public static function sass_compile() {

		$output = "";
		if(get_option('xt-compile')) {

			if(empty(self::$sass_compiler)) {	
				require_once XT_LIB_DIR."/wp-sass/sass-compiler.php";
				self::$sass_compiler = new XT_SassCompiler();
			}
	
			ob_start();
			//ignore_user_abort(true);
			
			self::$sass_compiler->compile(
				self::getScssDir()."/", 
				self::getCssDir()."/", 
				self::getDynamicScssFile(),
				array(
					'_settings.scss',
					'style.scss'
				),
				'style.css',
				'scss_formatter_compressed'
			);
			$output = ob_get_contents();
			ob_end_clean();
			
			delete_option('xt-compile');
		
		}
		
		die($output);

	}	
	
			
	function activation() {
		
		flush_rewrite_rules();

		$this->register_sidebars();
		
		//Redirect to options after activation

		if ( ! empty($_GET['activated']) && $_GET['activated'] == 'true' )
		{
			$this->set_default_menu();
		}
	}
	
	
	function admin_head() {
		
		if ( xt_option('meta_favicon') )
			echo '<link rel="shortcut icon" type="image/x-icon" href="' . xt_option('meta_favicon', 'url') . '" />';
	}
	
	
	function admin_bar ( $wp_toolbar )
	{
		global $redux_args;
	
		/**
		 * Add Theme Options to WordPress Toolbar
		 */
	 
		$wp_toolbar->add_node( array(
			  'parent' => 'appearance'
			, 'id'     => 'xt-options'
			, 'title'  => $redux_args['menu_title']
			, 'href'   => admin_url('/admin.php?page=' . $redux_args['page_slug'])
		) );
	}

	function loadUpdates() {
	
		$this->updates = include_once(XT_LIB_DIR.'/updates.php');
		
		if(!empty($this->updates)) {
			$first = end ( $this->updates );
			$last = reset ( $this->updates );
			$this->launch_date = $first['date'];
			$this->parent_version = $last['version'];
		}	
		
						
		// Load upgrades/migrations
		require_once(XT_LIB_DIR.'/migrations.php');
	}
	
	function write_updates() {
	
		global $wp_filesystem;
	
		$static_updates_file = XT_PARENT_DIR.'/changelog.txt';
		$updates_file = XT_LIB_DIR.'/updates.php';
		
		if(file_exists($static_updates_file) && is_writable($static_updates_file) && !empty($this->updates )) {
	
			$static_updates_file_time = filemtime($static_updates_file);
			$updates_file_time = filemtime($updates_file);
	
			if(($static_updates_file_time < $updates_file_time) || (@filesize($static_updates_file_time) == 0)) {
	
				$changelog = '';
				foreach($this->updates as $update) {
	
					$changelog .= '----------------------------------------------'."\r\n";
					$changelog .= 'V.'.$update["version"].' - '.$update["date"]."\r\n";
					$changelog .= '----------------------------------------------'."\r\n";
					
					foreach($update["changes"] as $key => $update) {
						
						if(is_array($update)) {
							foreach($update as $item) {
								$changelog .= '- '.strip_tags(str_replace("<br>", "\r\n  ", $item))."\r\n";
							}
						}else{
							$changelog .= '- '.strip_tags(str_replace("<br>", "\r\n  ", $update))."\r\n";
						}		
	
					}
					$changelog .= "\r\n";
				}
	
						
				if( empty( $wp_filesystem ) ) {
				  	require_once( ABSPATH .'/wp-admin/includes/file.php' );
				  	WP_Filesystem();
				}
		
				$wp_filesystem->put_contents($static_updates_file, $changelog, FS_CHMOD_FILE);

			}
		}
	
	}
	
	function set_comments_type() {
		
		global $post;
		
		$comments_enabled = xt_comments_enabled();
		$comments_system = xt_comments_system();
		
		$disqus = get_option('disqus_active');

		if(function_exists('is_product') && is_product()) {
		
			update_option('disqus_active', '0');
			
		}else if($post && !empty($comments_enabled)){

			if($comments_system != 'disqus') {
			
				update_option('disqus_active', '0');
				
			}else if($comments_system == 'disqus') {
			
				update_option('disqus_active', '1');
			}
		}
			
	}
	

	function register_menus() {
		
		$this->menus = array(
			'main-menu' => 'Main Menu', 
			'main-mobile-menu' => 'Main Mobile Menu', 
			'left-side-push-menu' => 'Left Side Push Menu', 
			'right-side-push-menu' => 'Right Side Push Menu'
		);
		
		foreach($this->menus as $id => $menu) {
			register_nav_menu($id, $menu);
		}	

	}
	
	function set_default_menu() {
		
		global $wpdb;
		
		$default_menu_id = 'main-menu';	
		$default_menu_name = 'Main Menu';	
		$menu_exists = wp_get_nav_menu_object( $default_menu_id );
		
		// If it doesn't exist, let's create it.
		if( !$menu_exists){
		    $menu_id = wp_create_nav_menu($default_menu_name);
		
			$pages = get_pages();
			
			foreach($pages as $page) {
				
				// Set up default menu items
			    wp_update_nav_menu_item($menu_id, 0, array(
			        'menu-item-title' =>  $page->post_title,
			        'menu-item-url' => get_permalink($page->ID), 
			        'menu-item-status' => 'publish'
			    ));
			}   

		}else{
			
			$menu_id = (int) $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE slug = %s", $default_menu_id));
		}
	
		$locations = get_theme_mod('nav_menu_locations');
		foreach($this->menus as $id => $menu) {
				
			$locations[$id] = $menu_id;
		}	
		set_theme_mod('nav_menu_locations', $locations); 
				
	}

	function supports() {
		
		if ( function_exists('add_theme_support') ) {
		
			add_theme_support('title-tag');
			add_theme_support('post-formats', self::$post_formats);
			add_theme_support('automatic-feed-links');
			add_theme_support('favicon');
			add_theme_support('html5', array('comment-list','search-form','comment-form','gallery','caption'));
			add_theme_support('post-thumbnails');
			add_theme_support( 'woocommerce' );
		}
		
		if ( function_exists('add_image_size') ) {

			if(!empty(self::$image_sizes)) {
				
				foreach(self::$image_sizes as $key => $size) {
					
					add_image_size($key, $size['width'], $size['height'], $size['crop']);
				}
			}	

		}
		
		// Fix bug with category pages not found after reseting option panel to default
		if ( ! empty($_GET['settings-updated']) ) {
			switch_theme( get_stylesheet() );
		}


	}

	function language() {
			
		// Load theme textdomain
		if ( !is_child_theme() ) {
			load_theme_textdomain( XT_TEXT_DOMAIN, XT_PARENT_DIR . '/languages' );
		}else{
			load_child_theme_textdomain( XT_TEXT_DOMAIN, XT_CHILD_DIR . '/languages' );
		}
		
		
	}
	
	function redux() {
		
		// Load Redux Extensions
		require_once(XT_ADMIN_DIR.'/redux-extensions/extensions-init.php');
 
		// Load Redux Framework
		require_once(XT_ADMIN_DIR.'/redux-framework/ReduxCore/framework.php' );

		// Load admin functions
	    require_once(XT_ADMIN_DIR.'/functions.php' );
	
		// Load the theme options
	    require_once(XT_ADMIN_DIR.'/options-init.php' );
		
	}
	
	function acf() {

		// Load ACF
		define( 'ACF_LITE', true );
		
		if ( ! class_exists('acf') )
			require_once(XT_LIB_DIR.'/advanced-custom-fields/acf.php');
			
		
		// Load Custom Fields
		require_once(XT_LIB_DIR.'/meta-boxes.php');
	
		
	}
	
	function classes() {

		// Load dynamic Aq Resizer class
		require_once(XT_CLASSES_DIR.'/aq_resizer.php');
		
		// Load dynamic styles class
		require_once(XT_CLASSES_DIR.'/styles.class.php');
		
		// Load Plugin installation and activation
		require_once(XT_CLASSES_DIR.'/tgmpa.class.php');		

		// Load Widgets Core Class
		require_once(XT_CLASSES_DIR.'/widget.class.php');
		
		// Load Advanced Widgets Core Class
		require_once(XT_CLASSES_DIR.'/advanced_widget.class.php');
	
		// Load Custom Nav Walker
		require_once(XT_CLASSES_DIR.'/menu.class.php');
		require_once(XT_CLASSES_DIR.'/walker.class.php');
		
		
		
	}
	
	function modules() {
		
		global $XT_SocialShare, $XT_Likes;

		// Load Likes Module
		require_once(XT_MOD_DIR.'/likes/module.php');

		// Load Social Share Module
		require_once(XT_MOD_DIR.'/social-share/module.php');
				
		// Load Login / Register Module
		require_once(XT_MOD_DIR.'/login-register/module.php');
		
		// Load Dynamic Sidebar Module
		require_once(XT_MOD_DIR.'/dynamic-sidebar/module.php');

		// Load Advanced Multiselect Module
		require_once(XT_MOD_DIR.'/advanced-multiselect/module.php');

		$this->woocommerce();	
		$this->buddypress();			
	}

	function register_sidebars() {

		if(is_admin()) {
		
			$sidebars = array(
	        	array(
	        		'sidebar_name' => 'Blog Sidebar',
	        	),
	        	array(
	        		'sidebar_name' => 'Videos Sidebar',
	        	),
	        	array(
		        	'sidebar_name' => 'Smart Content Sidebar Widget Zone',	
		        ),
	        	array(
	        		'sidebar_name' => 'Footer Widget Zone',
	        	),	
	        	array(
	        		'sidebar_name' => 'After Single Post Widget Zone',
	        	),        	
	        	array(
	        		'sidebar_name' => 'Single Post Bottom Widget Zone',
	        	),
	        	array(
	        		'sidebar_name' => 'After Single Video Widget Zone',
	        	),
	        	array(
	        		'sidebar_name' => 'Single Video Bottom Widget Zone',
	        	),       
	        );
	        
	        $this->sidebars = array_reverse($sidebars);

			foreach($this->sidebars as $sidebar) {
				save_xt_dynamic_sidebar_callback($sidebar);
			}
		
		}
		
	}
	
	function widgets() {
		
		global $xt_widgets;
		
		$widgets = glob(XT_WIDGETS_DIR.'/*.php');
		
		foreach($widgets as $widget) {
		
			require_once($widget);
		}
	
		$xt_widgets = array(
			  'XT_Widget_News'
			, 'XT_Widget_Categories'
			, 'XT_Widget_Comments'
			, 'XT_Widget_Text'
			, 'XT_Widget_Twitter'
			, 'XT_Widget_Networks'
			, 'XT_Widget_Newsletter'
		);

	
		foreach($xt_widgets as $widget) {
	
			register_widget($widget);
		}
		
		unregister_widget('nmMailChimp');
		
		$this->widgets = $xt_widgets;
			
	}


	
	function includes() {

		if(!is_admin()) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
			
		// Load helpers
		require_once(XT_LIB_DIR.'/helpers.php');

		// Load admin functions
    	require_once(XT_ADMIN_DIR.'/functions.php' );

		// Load functions
		require_once(XT_LIB_DIR.'/functions.php');
		
		// Load ajax functions 
		require_once(XT_LIB_DIR.'/functions-ajax.php');
		
		// Load template functions
		require_once(XT_LIB_DIR.'/template.php');

		// Load Shortcodes
		require_once(XT_LIB_DIR.'/shortcodes.php');
		
		// Setup News
		require_once(XT_LIB_DIR.'/setup-news.php');
		
		if(is_admin()) {
			
			// Load default data imports
			require_once(XT_LIB_DIR.'/import.php');
			
			// load required plugins
			require_once(XT_LIB_DIR.'/plugins.php');
		}	
		
	}
	
	function visual_composer() {
				
		// Load Visual Composer Addons
		
		require_once(XT_LIB_DIR.'/vc-extend/vc-layouts.php');
		require_once(XT_LIB_DIR.'/vc-extend/vc-filters.php');
		require_once(XT_LIB_DIR.'/vc-extend/vc-custom-params.php');
		require_once(XT_LIB_DIR.'/vc-extend/vc-map.php');
	
	}
	

	function woocommerce() {
	
		require_once(XT_LIB_DIR.'/woocommerce/functions.php');
		
		if(function_exists('is_woocommerce')) {
			require_once(XT_LIB_DIR.'/woocommerce/filters.php');
		}	
		
	}
	
	function buddypress() {
	
		if(function_exists('bp_is_blog_page')) {
			require_once(XT_LIB_DIR.'/buddypress.php');
		}
	}

	function enqueue_styles() {
	
		if ( is_admin() || xt_is_login_page() ) return;
	
		global $wp_styles, $wp_query, $post;
	
		// Styled by the theme
		wp_dequeue_style('contact-form-7');
		
		$protocol = is_ssl() ? 'https' : 'http';
		
		if(function_exists('is_plugin_active') && is_plugin_active('woocommerce/woocommerce.php')) {
		
			$woo_style = array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce.css',
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all'
			);
			wp_enqueue_style('woocommerce-general', $woo_style["src"], $woo_style["deps"], $woo_style["version"], $woo_style["media"]);
		}
		
		wp_deregister_style('font-awesome');
		xt_enqueue_style('font-awesome', XT_ASSETS_URL.'/vendors/fontawesome/css/font-awesome.min.css', false, '', 'all' );
		xt_enqueue_style('xt-vendors', XT_ASSETS_URL.'/css/vendors.min.css', false, '', 'all' );
		xt_enqueue_style('xt-style', self::getCssUrl().'/style.css', false, '', 'all' );
		
		if ( is_child_theme() ) {
			wp_enqueue_style( 'xt-child-style', get_stylesheet_uri() );
		}

		$this->enqueue_dynamic_styles();

	}
	
	function add_editor_styles() {
		
    	add_editor_style( self::getCssUrl().'/style.css' );
    	add_editor_style( XT_ASSETS_URL.'/admin/editor_styles.css' );
	}

	function enqueue_dynamic_styles() {
	
		global $wp_query, $post;
		
		$post_id = xt_get_page_ID();

		if($post_id !== false) {
				
			include_once(XT_ASSETS_DIR.'/dynamic.php');

			if(!empty($xt_custom_css)) {
				
				wp_add_inline_style( 'xt-style', $xt_custom_css );
			}	
		}
	}

	function enqueue_admin_styles() {
	
		wp_deregister_style('font-awesome');
		xt_enqueue_style('font-awesome', XT_ASSETS_URL.'/vendors/fontawesome/css/font-awesome.min.css', false, '', 'all' );
		xt_enqueue_style('xt-redux', XT_ADMIN_URL.'/assets/css/redux.css', false, '', 'all');
		xt_enqueue_style('xt-vc', XT_LIB_URL.'/vc-extend/assets/css/admin.css', false, '', 'all' );

	}
	

	function enqueue_scripts() {
	
		global $wp_scripts;
		
		if ( is_admin() || xt_is_login_page() ) 
			return;
	
		if ( is_singular() && comments_open() && get_option('thread_comments') )
			wp_enqueue_script('comment-reply');

	
		// MODERNIZR
		xt_enqueue_script('modernizr', XT_PARENT_URL.'/bower_components/modernizr/modernizr.min.js', null, null, true);
		
		// FOUNDATION
		xt_enqueue_script('xt-foundation', XT_PARENT_URL.'/bower_components/foundation/js/foundation.min.js', null, null, true);


		if(is_singular()) {
			// WAYPOINTS
			wp_dequeue_style('waypoints');
	        wp_enqueue_script( 'waypoints', XT_PARENT_URL.'/assets/vendors/waypoints/waypoints.js', array('jquery'), '1.0', true);
	    }   
	    
	    					
		// VENDORS
		xt_enqueue_script('xt-vendors', XT_PARENT_URL.'/assets/js/vendors.min.js', array('jquery'), null, true);

		// MAIN
		xt_enqueue_script('xt-theme', XT_PARENT_URL.'/assets/js/theme.min.js', array('jquery'), '1.0', true);
 
	
		// XT VARS
		$lang_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '';

		wp_localize_script('xt-theme', 'xt_vars', array(
			'ajaxurl' => admin_url('admin-ajax.php?lang='.$lang_code),
			'lang' => $lang_code,
			'theme_url' => XT_PARENT_URL,
			'assets_url' => XT_ASSETS_URL,
			'enable_nice_scroll' => (bool)xt_option('enable_nice_scroll', null, true),
			'enable_smooth_scroll' => (bool)xt_option('enable_smooth_scroll', null, false),
			'enable_sticky_sidebars' => (bool)xt_option('enable_sticky_sidebars', null, true),
			'hide_admin_bar' => (bool)xt_option('hide_admin_bar', null, false),
			'custom_js' => xt_option('custom_js')
		));
	
	}

	function enqueue_admin_scripts() {
	
		$lang_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '';
		$vars = array(
			'ajaxurl' => admin_url('admin-ajax.php?lang='.$lang_code),
			'lang' => $lang_code,
			'theme_url' => XT_PARENT_URL,
			'assets_url' => XT_ASSETS_URL
		);

		xt_enqueue_script('xt-redux-custom', XT_ADMIN_URL.'/assets/js/redux.custom.js', array('jquery'), null, true);
		xt_enqueue_script('xt-vc-custom', XT_LIB_URL.'/vc-extend/assets/js/admin.js', array('jquery'), null, true);

		wp_localize_script('xt-vc-custom', 'xt_vars', $vars);
	}
	

	function set_metadata () {
		$output = array();

		if ( xt_option('meta_apple_mobile_web_app_title') ) :
			$output[] = '<meta name="apple-mobile-web-app-title" content="' . esc_attr( xt_option('meta_apple_mobile_web_app_title') ) . '">';
		endif;
	
		if ( xt_option('meta_favicon', 'url') ) :
			$output[] = '<link rel="shortcut icon" type="image/x-icon" href="' . esc_url( xt_option('meta_favicon', 'url') ) . '">';
		endif;
	
		if ( xt_option('meta_apple_touch_icon', 'url') ) :
			$output[] = '<link rel="apple-touch-icon-precomposed" href="' . esc_url( xt_option('meta_apple_touch_icon', 'url') ) . '">';
		endif;
	
		if ( xt_option('meta_apple_touch_icon_72x72', 'url') ) :
			$output[] = '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="' . esc_url( xt_option('meta_apple_touch_icon_72x72', 'url') ) . '">';
		endif;
	
		if ( xt_option('meta_apple_touch_icon_114x114', 'url') ) :
			$output[] = '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="' . esc_url( xt_option('meta_apple_touch_icon_114x114', 'url') ) . '">';
		endif;
	
		if ( xt_option('meta_apple_touch_icon_144x144', 'url') ) :
			$output[] = '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="' . esc_url( xt_option('meta_apple_touch_icon_144x144', 'url') ) . '">';
		endif;
	
		if ( ! empty($output) )
			echo "\n\t" . implode("\n\t", $output);
	}
	
	
	function plugins_loaded() {
		
		if(function_exists('is_plugin_active') && is_plugin_active('easy-foundation-shortcodes/osc_foundation_shortcode.php')) {
			
			$efs_css = get_option('EFS_FOUNDATION_CSS_LOCATION');
			$efs_js = get_option('EFS_FOUNDATION_JS_LOCATION');
			
			if($efs_css !== '2') {
				delete_option('EFS_FOUNDATION_CSS_LOCATION');
				add_option('EFS_FOUNDATION_CSS_LOCATION', 2);
			}
			if($efs_js !== '2') {	
				delete_option('EFS_FOUNDATION_JS_LOCATION');
				add_option('EFS_FOUNDATION_JS_LOCATION', 2);
			}
		}
		
	}

	public static function getAssetsPaths() {

		return self::$assets;	
	}
	
	public static function getDynamicScssFile() {
		
		return self::$assets['dynamic_scss'];
	}
	
	public static function getScssDir() {
		
		return self::$assets['scss_dir'];
	} 
	
	public static function getScssUrl() {
		
		return self::$assets['scss_url'];
	} 
	
	public static function getCssDir() {
		
		return self::$assets['css_dir'];
	} 
	
	public static function getCssUrl() {
		
		return self::$assets['css_url'];
	} 
	
	public static function setAssetsPaths() {

		$xt_upload_dir = self::get_upload_dir();

		$assets = array();
		
		$assets['scss_dir'] = XT_ASSETS_DIR.'/scss';
		$assets['scss_url'] = XT_ASSETS_URL.'/scss';

		$assets['css_dir'] = $xt_upload_dir['dir'];
		$assets['css_url'] = $xt_upload_dir['url'];
		
		$assets['dynamic_scss'] = $assets['css_dir'].'/_dynamic.scss';

		self::$assets = $assets;

	}
	
	public static function get_upload_dir($folder = null) {

		global $wp_filesystem;
				
		if( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
				
		$upload_dir = wp_upload_dir();
		
		$dir = $upload_dir['basedir'].'/'.XT_THEME_ID;
		$url = set_url_scheme($upload_dir['baseurl'].'/'.XT_THEME_ID);
			
		if($folder) {
			$dir = $dir.$folder;
			$url = $url.$folder;
		}
		
		if(!$wp_filesystem->is_dir($dir)) { 
			wp_mkdir_p($dir);
		}	
		
		$xt_upload_dir = array(
			'dir' => $dir,
			'url' => $url
		);
		
		return $xt_upload_dir;
	}
	
	function footer() {
		
		echo '<!-- '.XT_THEME_NAME.' v'.XT_THEME_VERSION.' by XplodedThemes.com -->';
	}

}

global $XT_Theme;
$XT_Theme = new XT_Theme('goodnews', 'Good News');