<?php
/**
* XT Dynamic Sidebar
* Plugin URI: http://wordpress.org/plugins/xt-dynamic-sidebar
* Description: Allows you to create widget areas easily and dynamically. You also can use shortcodes for each widget in posts or pages.
* Version: 1.0.4
* Author: Prayas Sapkota
* Modified By: XplodedThemes
* Author URI: http://prayas-sapkota.com.np
* License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if (!defined('ABSPATH'))
    exit('Restricted Access');

$plugin = plugin_basename(__FILE__);

global $wp_sidebar;
$wp_sidebar_version = "1.0.4";

register_activation_hook(__FILE__, 'xt_dynamic_sidebar_install');

add_action('admin_menu', 'xt_dynamic_sidebar_actions');

add_action( 'admin_print_scripts', 'xt_dynamic_sidebar_admin_scripts');

add_action( 'admin_print_styles', 'xt_dynamic_sidebar_admin_styles');

add_action( 'widgets_init', 'xt_dynamic_sidebar_generate' );

function xt_dynamic_sidebar_actions() {
    add_theme_page("Create Dynamic Sidebar", "Manage Sidebars", 'manage_options', "dynamic-sidebars.php", "xt_dynamic_sidebar_func");
}

function xt_dynamic_sidebar_admin_scripts() {
    wp_register_script('sidebar-scripts', XT_MOD_URL.'/'.basename(__DIR__).'/js/scripts.js', array('jquery'), '1.0', true);
    wp_enqueue_script('sidebar-scripts');
}

function xt_dynamic_sidebar_admin_styles() {
    wp_enqueue_style('sidebar-styles', XT_MOD_URL.'/'.basename(__DIR__).'/css/style.css', false, false, 'screen');
}

function xt_dynamic_sidebar_func() {
    ?>
    <div class="wrap wrap-dynamic-sidebar">
        <div id="icon-tools" class="icon32"><br></div>
        <h2>Wordpress Dynamic Sidebar</h2>
        <p>Here you can create widget areas. Also you can use shortcodes for each widget in posts or pages.</p>
        <div class="col-1">
            <h3>Create New Sidebar</h3>
            <form id="new-sidebar">
                <p>
                    <label for="sidebar_name">Sidebar Name <span>*</span></label>
                    <input type="text" class="text-box" placeholder="Unique Sidebar Name" id="sidebar_name" name="sidebar_name" />
                </p>
                <p>
                    <label for="sidebar_desc">Description</label>
                    <textarea class="text-area" placeholder="Sidebar Description" id="sidebar_desc" name="sidebar_desc"></textarea>
                </p>
                <p>
                    <label for="sidebar_class">Class</label>
                    <input type="text" class="text-box" placeholder="CSS Class Name" id="sidebar_class" name="sidebar_class" />
                </p>
                <p class="hidden">
                    <label for="sidebar_before_widget">Before Widget </label>
                    <textarea class="text-area" placeholder="&lt;aside id=&#34;%1$s&#34; class=&#34;widget %2$s&#34;&gt;" id="sidebar_before_widget" name="sidebar_before_widget">&lt;aside id=&#34;%1$s&#34; class=&#34;widget %2$s&#34;&gt;</textarea>
                </p>
                <p class="hidden">
                    <label for="sidebar_after_widget">After Widget </label>
                    <textarea class="text-area" placeholder="&lt;/aside&gt;" id="sidebar_after_widget" name="sidebar_after_widget">&lt;/aside&gt;</textarea>
                </p>
                <p class="hidden">
                    <label for="sidebar_before_title">Before Title </label>
                    <textarea class="text-area" placeholder="&lt;h2 class=&#34;widgettitle&#34;&gt" id="sidebar_before_title" name="sidebar_before_title">&lt;h2 class=&#34;widgettitle&#34;&gt</textarea>
                </p>
                <p class="hidden">
                    <label for="sidebar_after_title">After Title </label>
                    <textarea class="text-area" placeholder="&lt;/h2&gt" id="sidebar_after_title" name="sidebar_after_title">&lt;/h2&gt</textarea>
                </p>
                <input class="button" type="button" id="add-new-sidebar" value="Add Sidebar" />
                <span id="response"></span>
            </form>
        </div>
        <div class="col-2">
            <h3>Dynamic Sidebar(s)</h3>
            <table id="dynamic-sidebar" class="wp-list-table widefat fixed posts" cellspacing="0">
                <thead>
                    <tr>
                        <th class="name">Sidebar Name</th>
                        <th class="shortcode">Shortcode</th>
                        <th class="description">Description</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody id="the-list">
                    <?php
                    $sidebars = get_option('xt-dynamic-sidebar-settings');
                    if(!empty($sidebars)) {
                        $sidebars = @unserialize( $sidebars );
                    }
                    if (!empty($sidebars) && is_array($sidebars)) {
                        $counter = 0;
                        foreach($sidebars as $sidebar) {
                        ?>
                        <tr<?php if(($counter % 2) == 0) { echo ' class="alternate"'; } ?>>
                            <td class="name">
                                <strong><?php echo esc_html($sidebar['name']); ?></strong>
                            </td>
                            <td class="shortcode">
                                <div id="<?php echo esc_attr($sidebar['id']); ?>" onclick="fnSelect('<?php echo esc_js($sidebar['id']); ?>')">[xt-dynamic-sidebar id="<?php echo esc_attr($sidebar['id']); ?>"]</div>
                            </td>
                            <td class="description">
                                <?php echo esc_html($sidebar['description']); ?>
                            </td>
                            <td class="action">
                                <a href="javascript:void(0);" class="edit-dynamic-sidebar" data-id="<?php echo esc_attr($sidebar['id']); ?>">Edit</a> | <a href="javascript:void(0);" data-id="<?php echo esc_attr($sidebar['id']); ?>" class="delete-dynamic-sidebar">Delete</a>
                            </td>
                        </tr>
                        <?php
                            $counter++;
                        }
                    } else {
                    ?>
                    <tr class="no-sidebar">
                        <td colspan="4">You did not created the sidebar yet. You can create it from the form left.</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="popuup_message">This is a sample error/validation message to display.</div>
    <?php
}


function save_xt_dynamic_sidebar_callback($sidebar = array()) {

	$is_post = empty($sidebar);

	$sidebar_before_widget =
	$sidebar_after_widget =
	$sidebar_before_title =
	$sidebar_after_title =
	$sidebar_desc =
	$sidebar_class = '';
    
    if(!empty($_POST['elements']))
    	parse_str($_POST['elements'], $elements);
    
    if(!empty($sidebar)) {
	    $elements = $sidebar;
    }
    
    if(empty($elements))
    	return false;
    
    extract($elements);
    
    
    if(trim($sidebar_name) == '') {
        die('* fields are mandatory.');
    }
    
    if(trim($sidebar_before_widget) == '') {
         $sidebar_before_widget = '<aside id="%1$s" class="widget %2$s">';
    }
    if(trim($sidebar_after_widget) == '') {
         $sidebar_after_widget = '</aside>';
    }
    if(trim($sidebar_before_title) == '') {
         $sidebar_before_title = '<h2 class="widgettitle">';
    }
    if(trim($sidebar_after_title) == '') {
         $sidebar_after_title = '</h2>';
    }

    $s_name = $sidebar_name;
    $s_id = sanitize_title($sidebar_name);
    $s_desc = esc_textarea($sidebar_desc);
    $data = array(
        array(
            'name' => $sidebar_name,
            'id' => sanitize_title($sidebar_name),
            'description' => esc_textarea($sidebar_desc),
            'class' => $sidebar_class,
            'before_widget' => esc_textarea($sidebar_before_widget),
            'after_widget' => esc_textarea($sidebar_after_widget),
            'before_title' => esc_textarea($sidebar_before_title),
            'after_title' => esc_textarea($sidebar_after_title)
        )
    );
    
    $sidebars = get_option('xt-dynamic-sidebar-settings');
    if (!empty($sidebars)) {
        $sidebars = @unserialize( $sidebars );
        if(is_array($sidebars)) {
	        foreach($sidebars as $key => $sidebar) {
	            if( $sidebar['id'] === $s_id ) {
	               return false;
	            }
	        }
       		$data = array_merge($data, $sidebars);
       	}	
    }

    $serialize = serialize($data);
    if (get_option('xt-dynamic-sidebar-settings')) {
        update_option('xt-dynamic-sidebar-settings', $serialize);
    } else {
        add_option('xt-dynamic-sidebar-settings', $serialize, '', 'yes');
    }
    
	if($is_post) {
   	 	die('<tr class="alternate"><td class="name"><strong>'.$s_name.'</strong></td><td class="shortcode"><div id="'.$s_id.'" onclick="fnSelect(\''.$s_id.'\')">[xt-dynamic-sidebar id="'.$s_id.'"]</div></td><td class="description">'.$s_desc.'</td><td class="action"><a href="javascript:void(0);" class="edit-dynamic-sidebar" data-id="'.$s_id.'">Edit</a> | <a href="javascript:void(0);" data-id="'.$s_id.'" class="delete-dynamic-sidebar">Delete</a></td></tr>');
   	} 	
}

add_action('wp_ajax_save_xt_dynamic_sidebar', 'save_xt_dynamic_sidebar_callback');
add_action('wp_ajax_nopriv_save_xt_dynamic_sidebar', 'save_xt_dynamic_sidebar_callback');



function update_xt_dynamic_sidebar_callback() {

	$sidebar_before_widget =
	$sidebar_after_widget =
	$sidebar_before_title =
	$sidebar_after_title =
	$sidebar_desc =
	$sidebar_class = '';

    if(!empty($_POST['elements']))
    	parse_str($_POST['elements'], $elements);

    if(empty($elements))
    	return false;
    
    extract($elements);
        
    $s_id = sanitize_text_field($_POST['slug']);
    $c_id = sanitize_title($sidebar_name);
    $c_name = $sidebar_name;
    $c_desc = esc_textarea($sidebar_desc);

    if(trim($sidebar_name) == '') {
        die('* fields are mandatory.');
    }
    
    if(trim($sidebar_before_widget) == '') {
         $sidebar_before_widget = '<aside id="%1$s" class="widget %2$s">';
    }
    if(trim($sidebar_after_widget) == '') {
         $sidebar_after_widget = '</aside>';
    }
    if(trim($sidebar_before_title) == '') {
         $sidebar_before_title = '<h2 class="widgettitle">';
    }
    if(trim($sidebar_after_title) == '') {
         $sidebar_after_title = '</h2>';
    }
    
    if (get_option('xt-dynamic-sidebar-settings')) {
        $sidebars = unserialize( get_option('xt-dynamic-sidebar-settings') );
        //print_pre($sidebars);
        foreach($sidebars as $key => $sidebar) {
            if( $sidebar['id'] === $c_id ) {
                if($c_id !== $s_id) {
                    die('Insert unique sidebar.');
                }
                $current_key = $key;
                break;
            } elseif($sidebar['id'] === $s_id) {
                $current_key = $key;
                break;
            }
        }
        $data = array(
            $current_key => array(
                'name' => $sidebar_name,
                'id' => sanitize_title($sidebar_name),
                'description' => esc_textarea($sidebar_desc),
                'class' => $sidebar_class,
                'before_widget' => esc_textarea($sidebar_before_widget),
                'after_widget' => esc_textarea($sidebar_after_widget),
                'before_title' => esc_textarea($sidebar_before_title),
                'after_title' => esc_textarea($sidebar_after_title)
            )
        );
        //print_pre($data);
        $new_data = array_replace($sidebars, $data);
        //print_pre($new_data);
        $serialize = serialize($new_data);
        update_option('xt-dynamic-sidebar-settings', $serialize);
        die('<td class="name"><strong>'.$c_name.'</strong></td><td class="shortcode"><div id="'.$c_id.'" onclick="fnSelect(\''.$c_id.'\')">[xt-dynamic-sidebar id="'.$c_id.'"]</div></td><td class="description">'.$c_desc.'</td><td class="action"><a href="javascript:void(0);" class="edit-dynamic-sidebar" data-id="'.$c_id.'">Edit</a> | <a href="javascript:void(0);" data-id="'.$c_id.'" class="delete-dynamic-sidebar">Delete</a></td>');
    }
    die;
}

add_action('wp_ajax_update_xt_dynamic_sidebar', 'update_xt_dynamic_sidebar_callback');
add_action('wp_ajax_nopriv_update_xt_dynamic_sidebar', 'update_xt_dynamic_sidebar_callback');


function edit_xt_dynamic_sidebar_callback() {
    $s_id = sanitize_text_field($_POST['sidebar_id']);
    $sidebars = unserialize( get_option('xt-dynamic-sidebar-settings') );
    foreach($sidebars as $key => $sidebar) {
        $current_key = $key;
        if( $sidebar['id'] === $s_id ) {
           break;
        }
    }
    echo json_encode($sidebars[$current_key]);
    die();
}

add_action('wp_ajax_edit_xt_dynamic_sidebar', 'edit_xt_dynamic_sidebar_callback');
add_action('wp_ajax_nopriv_edit_xt_dynamic_sidebar', 'edit_xt_dynamic_sidebar_callback');



function delete_xt_dynamic_sidebar_callback() {
    $s_id = sanitize_text_field($_POST['sidebar_id']);
    $sidebars = unserialize( get_option('xt-dynamic-sidebar-settings') );
    foreach($sidebars as $key => $sidebar) {
        if( $sidebar['id'] === $s_id ) {
            $current_key = $key;
            break;
        }
    }
    unset($sidebars[$current_key]);
    $sidebars = array_values($sidebars);
    $serialize = serialize($sidebars);
    update_option('xt-dynamic-sidebar-settings', $serialize);
    die('Successfully deleted.');
}

add_action('wp_ajax_delete_xt_dynamic_sidebar', 'delete_xt_dynamic_sidebar_callback');
add_action('wp_ajax_nopriv_delete_xt_dynamic_sidebar', 'delete_xt_dynamic_sidebar_callback');


function xt_dynamic_sidebar_generate() {
    $sidebars = array();

	$sidebars = get_option('xt-dynamic-sidebar-settings');
	
    if(!empty($sidebars)) {
        $sidebars = @unserialize( $sidebars );
    }
    
    if(empty($sidebars) || !is_array($sidebars))
    	return false;
    	
    foreach($sidebars as $key => $sidebar) {
        register_sidebar(array(
            'name' => $sidebar['name'],
            'id' => $sidebar['id'],
            'description' => $sidebar['description'],
            'class' => $sidebar['class'],
            'before_widget' => html_entity_decode($sidebar['before_widget']),
            'after_widget' => html_entity_decode($sidebar['after_widget']),
            'before_title' => html_entity_decode($sidebar['before_title']),
            'after_title' => html_entity_decode($sidebar['after_title']),
        ));
    }
}

function xt_dynamic_sidebar_create( $atts ) {
    if(function_exists('dynamic_sidebar')) {
       $id = $atts['id'];
       dynamic_sidebar($id);
    }
}
     
add_shortcode( 'xt-dynamic-sidebar', 'xt_dynamic_sidebar_create' );

function xt_dynamic_sidebar_install() {
    global $wp_sidebar_version;
    add_option("xt_dynamic_sidebar_version", $wp_sidebar_version);
}

function xt_dynamic_sidebar_init() {
    load_plugin_textdomain( 'xt-dynamic-sidebar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'admin_init', 'xt_dynamic_sidebar_init' );

// Add settings link on plugin page
function xt_dynamic_sidebar_link($links) {
    //print_r($links);
    $settings_link = '<a href="options-general.php?page=xt-dynamic-sidebar.php">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

add_filter( 'plugin_action_links_' . $plugin, 'xt_dynamic_sidebar_link' );

function xt_dynamic_sidebar_activate() {
    add_option('xt_dynamic_sidebar_redirect_on_1st_activation', 'true');
}
register_activation_hook(__FILE__, 'xt_dynamic_sidebar_activate');

function xt_dynamic_sidebar_redirect() {
    if(get_option('xt_dynamic_sidebar_redirect_on_1st_activation') === 'true') {
        update_option('xt_dynamic_sidebar_redirect_on_1st_activation', 'false');
        wp_redirect(admin_url('options-general.php?page=xt-dynamic-sidebar.php'));
        exit;
    }
}

add_action('admin_init', 'xt_dynamic_sidebar_redirect');