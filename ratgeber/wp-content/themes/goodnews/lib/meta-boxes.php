<?php
/***
 *  Install Add-ons
 *
 *  The following code will include all 4 premium Add-Ons in your theme.
 *  Please do not attempt to include a file which does not exist. This will produce an error.
 *
 *  All fields must be included during the 'acf/register_fields' action.
 *  Other types of Add-ons (like the options page) can be included outside of this action.
 *
 *  The following code assumes you have a folder 'add-ons' inside your theme.
 *
 *  IMPORTANT
 *  Add-ons may be included in a premium theme as outlined in the terms and conditions.
 *  However, they are NOT to be included in a premium / free plugin.
 *  For more information, please read http://www.advancedcustomfields.com/terms-conditions/
 */

// Fields

function xt_register_acf_fields()
{
	if ( ! class_exists('acf_field_repeater') )
		include_once(XT_LIB_DIR.'/acf-addons/acf-repeater/repeater.php');

	if ( ! class_exists('acf_field_widget_area') )
		include_once(XT_LIB_DIR.'/acf-addons/acf-widget-area/widget-area-v4.php');

	if ( ! class_exists('acf_field_relationship_formats') )
		include_once(XT_LIB_DIR.'/acf-addons/acf-relationship-formats/acf-relationship-formats.php');


	xt_check_acf_lite_switch();

}
add_action('acf/register_fields', 'xt_register_acf_fields');


/**
* If ACF_LITE is on, update all acf group fields in DB to draft
*/


function xt_check_acf_lite_switch()
{

	if(isset($_GET["settings-updated"])) {

		global $wpdb;

		if(ACF_LITE)
			$status = "draft";
		else
			$status = "publish";

		$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET post_status = %s WHERE post_type = 'acf'", $status));
	}

}



/**
 *  xt_acf_helpers_get_dir
 *
 * If the theme is used as a symlinked folder, this should help.
 *
 *  @since: 1.6.0
 *  @see helpers_get_dir
 */

function xt_acf_helpers_get_dir ( $dir ) {

	if ( false === strpos($dir, WP_CONTENT_DIR) )
	{
		$output = XT_LIB_URL . '/advanced-custom-fields/';

		if ( false !== strpos($dir, 'acf-addons/acf-repeater') )
			$output .= '../acf-addons/acf-repeater/';

		if ( false !== strpos($dir, 'acf-addons/acf-widget-area') )
			$output .= '../acf-addons/acf-widget-area/';

		if ( false !== strpos($dir, 'acf-addons/acf-relationship-formats') )
			$output .= '../acf-addons/acf-relationship-formats/';
			
		$dir = $output;
	}

	return $dir;
}

add_filter('acf/helpers/get_dir', 'xt_acf_helpers_get_dir', 2, 1);




/**
 *  Register Field Groups
 *
 *  The register_field_group function accepts 1 array which holds the relevant data to register a field group
 *  You may edit the array as you see fit. However, this may result in errors if the array is not compatible with ACF
 */



	
function xt_register_field_group() {
	
	if(function_exists("register_field_group") && ACF_LITE)
	{
		
		$rtl_mode = is_rtl();
		
		register_field_group(array (
			'id' => 'acf_sidebar-options',
			'title' => 'Sidebar Options',
			'fields' => array (
				array (
					'key' => 'field_526d6ec715ee9',
					'label' => 'Sidebar Position',
					'name' => 'sidebar-position',
					'type' => 'radio',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'disabled' => 'Disabled',
						'left' => $rtl_mode ? 'Right' : 'Left',
						'right' => $rtl_mode ? 'Left' : 'Right'
					),
					'other_choice' => 0,
					'save_other_choice' => 0,
					'default_value' => 'inherit',
					'layout' => 'vertical',
				),
				array (
					'key' => 'field_526d6c0da8219',
					'label' => 'Widget Area',
					'name' => 'sidebar-area_id',
					'type' => 'widget_area',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_526d6ec715ee9',
								'operator' => '!=',
								'value' => 'disabled',
							),
							array (
								'field' => 'field_526d6ec715ee9',
								'operator' => '!=',
								'value' => 'inherit',
							),
						),
						'allorany' => 'all',
					),
					'allow_null' => 1,
					'default_value' => '',
				)
	
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'page',
						'order_no' => 0,
						'group_no' => 1,
					),
					array (
						'param' => 'page_template',
						'operator' => '!=',
						'value' => 'tpl-endless-posts.php',
						'order_no' => 0,
						'group_no' => 2,
					),
				),
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'product',
						'order_no' => 0,
						'group_no' => 0,
					),
				)
				
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 1,
		));
		
		
		register_field_group(array (
			'id' => 'single-post-options',
			'title' => 'Post Display Options',
			'fields' => array (
				array (
					'key' => 'field_526d6c0da8220',
					'label' => 'Sidebar Below Post Title',
					'name' => 'sidebar-below-title',
					'type' => 'select',
					'choices' => array(
						'inherit' => 'Inherit from global settings',
						'enabled' => 'Yes',
						'disabled' => 'No',	
					),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_526d6ec715ee9',
								'operator' => '!=',
								'value' => 'disabled',
							),
							array (
								'field' => 'field_526d6ec715ee9',
								'operator' => '!=',
								'value' => 'inherit',
							),
							array (
								'field' => 'field_526d6ec715ea9',
								'operator' => '!=',
								'value' => 'behind-title-fullwidth',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => 'inherit',
				),	
				array (
					'key' => 'field_723382c925a83',
					'label' => 'Enable Post Content Smart Sidebar',
					'name' => 'enable_smart_sidebar',
					'instructions' => "<b>Note:</b> The Smart Sidebar needs to be activated globally within the theme panel in order to override it's position on this page.",
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'enabled' => 'Enable',
						'disabled' => 'Disable',
					),
					'default_value' => 'inherit',
				),	
				array (
					'key' => 'field_723382c925a84',
					'label' => 'Enable Post Content Smart Sidebar',
					'name' => 'smart_sidebar_position',
					'type' => 'select',
					'choices' => array (
						'left' => $rtl_mode ? 'Right' : 'Left',
						'right' => $rtl_mode ? 'Left' : 'Right'
					),
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_723382c925a83',
								'operator' => '==',
								'value' => 'enabled',
							),
						),
						'allorany' => 'all',
					),
					'default_value' => 'inherit',
				),								
				array (
					'key' => 'field_723382c925a87',
					'label' => 'Show Post Excerpt',
					'name' => 'show_post_excerpt',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),				
				array (
					'key' => 'field_723382c925a85',
					'label' => 'Show Post Categories',
					'name' => 'show_post_categories',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_723382c925a86',
					'label' => 'Show Post Tags',
					'name' => 'show_post_tags',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_723382c925a88',
					'label' => 'Show Post Date',
					'name' => 'show_post_date',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_723382c925a89',
					'label' => 'Show Post Author',
					'name' => 'show_post_author',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_723382c925a90',
					'label' => 'Show Post Stats',
					'name' => 'show_post_stats',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
						'order_no' => 0,
						'group_no' => 0,
					)
				)
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		));			
				

		register_field_group(array (
			'id' => 'featured-image-options',
			'title' => 'Featured Media Options',
			'fields' => array (
				array (
					'key' => 'field_526d6ec715ef9',
					'label' => 'Featured Image Size',
					'name' => 'single_post_featured_image',
					'type' => 'select',
					'choices' => array(
						'inherit' => 'Inherit from global settings',
						'fullwidth' => 'Full Width',
						'original' => 'Original',
						'none' => 'None'
					),
					'other_choice' => 0,
					'save_other_choice' => 0,
					'default_value' => 'inherit',
					'layout' => 'vertical',
				),
				array (
					'key' => 'field_526d6ec715ea9',
					'label' => 'Featured Media Position',
					'name' => 'single_post_featured_image_position',
					'instructions' => '<b>Note:</b> "Behind Title" only works on standard post formats',
					'type' => 'select',
					'choices' => array(
						'inherit' => 'Inherit from global settings',
						'above-title' => __('Above title', XT_TEXT_DOMAIN),
						'below-title' => __('Below Title', XT_TEXT_DOMAIN),
						'behind-title' => __('Behind Title', XT_TEXT_DOMAIN),
						'behind-title-fullwidth' => __('Behind Title Fullwidth', XT_TEXT_DOMAIN),
						'below-excerpt' => __('Below Excerpt', XT_TEXT_DOMAIN),
						'above-content' => __('Above Content', XT_TEXT_DOMAIN)
					),
					'other_choice' => 0,
					'save_other_choice' => 0,
					'default_value' => 'inherit',
					'layout' => 'vertical',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_526d6ec715ef9',
								'operator' => '!=',
								'value' => 'none',
							),
						),
						'allorany' => 'all',
					),
				),
				array (
					'key' => 'field_526d6ec715ec9',
					'label' => 'Featured Media Vertical Crop',
					'instructions' => '<b>Note:</b> This only applies to standard post formats<br>Enter Image Image Height in pixels.<br>The image will be cropped based on this height',
					'name' => 'single_post_featured_image_crop',
					'type' => 'number',
					'column_width' => '',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'min' => '',
					'max' => '',
					'step' => '',
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_526d6ec715ef9',
								'operator' => '!=',
								'value' => 'none',
							),
						),
						'allorany' => 'all',
					),
				)
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
						'order_no' => 0,
						'group_no' => 0,
					),
				)
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 3,
		));			
				

		register_field_group(array (
			'id' => 'acf_post-template-settings',
			'title' => 'Template Settings',
			'fields' => array (	
				array (
					'key' => 'field_523382c925a73',
					'label' => 'Query post formats',
					'name' => 'query_post_formats',
					'type' => 'true_false',
					'default_value' => 0
				),	
				array (
					'key' => 'field_523382c925a74',
					'label' => 'Filter By Post Formats',
					'name' => 'format',
					'type' => 'checkbox',
					'choices' => xt_get_post_formats(),
					'default_value' => '',
					'allow_null' => 0,
					'multiple' => 1,
					'conditional_logic' => array (
						'status' => 1,
						'rules' => array (
							array (
								'field' => 'field_523382c925a73',
								'operator' => '==',
								'value' => '1',
							),
						),
						'allorany' => 'all',
					),
				),				
				array (
					'key' => 'field_523382c925a75',
					'label' => 'Template Layout',
					'name' => 'template_layout',
					'type' => 'select',
					'choices' => array (
						'grid-1' => 'Classic',
						'list-small' => 'List (Small Thumbs)',
						'list' => 'List (Large Thumbs)',
						'grid-2' => 'Grid (2 Columns)',
						'grid-3' => 'Grid (3 Columns)',
						'grid-4' => 'Grid (4 Columns)',
						'grid-5' => 'Grid (5 Columns)',
					),
					'default_value' => 'list-small',
				),
				array (
					'key' => 'field_523382c925a86',
					'label' => 'Show Post Category',
					'name' => 'show_post_category',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_523382c925a87',
					'label' => 'Show Post Excerpt',
					'name' => 'show_post_excerpt',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_523382c925a88',
					'label' => 'Show Post Date',
					'name' => 'show_post_date',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_523382c925a89',
					'label' => 'Show Post Author',
					'name' => 'show_post_author',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
				array (
					'key' => 'field_523382c925a90',
					'label' => 'Show Post Stats',
					'name' => 'show_post_stats',
					'type' => 'select',
					'choices' => array (
						'inherit' => 'Inherit from global settings',
						'1' => 'Show',
						'' => 'Hide',
					),
					'default_value' => 'inherit',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'page_template',
						'operator' => '==',
						'value' => 'tpl-post-archive.php',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
				array (
					array (
						'param' => 'page_template',
						'operator' => '==',
						'value' => 'tpl-video-archive.php',
						'order_no' => 0,
						'group_no' => 0,
					),
				)
				
			),
			'options' => array (
				'position' => 'side',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 10,
		));
		
					
		register_field_group(array (
			'id' => 'acf_page-settings',
			'title' => 'Page Settings',
			'fields' => array (
				array (
					'key' => 'field_page_settings_general',
					'label' => 'General Options',
					'name' => 'tab-details',
					'type' => 'tab',
				),
				array (
					'key' => 'field_523382c955a73',
					'label' => 'Hide Page Title Bar',
					'name' => 'hide_page_title',
					'instructions' => 'This will hide the title bar including the breadcrumbs',
					'type' => 'true_false',
					'default_value' => 0,
					'placeholder' => 0,
				),	
				array (
					'key' => 'field_523382c955a78',
					'label' => 'Remove Container Top / Bottom Padding',
					'name' => 'page_container_no_padding',
					'instructions' => 'This will remove the padding from the main container.',
					'type' => 'true_false',
					'default_value' => 0,
					'placeholder' => 0,
				),			

				array (
					'key' => 'field_page_settings_background',
					'label' => 'Body Background Options',
					'name' => 'tab-details',
					'type' => 'tab',
				),
				
				array (
					'key' => 'field_523384ce55a79',
					'label' => 'Background Color',
					'name' => 'background_color',
					'type' => 'color_picker',
					'default_value' => '',
				),
				array (
					'key' => 'field_523382c955a74',
					'label' => 'Background Image',
					'name' => 'background',
					'type' => 'image',
					'save_format' => 'id',
					'preview_size' => 'medium',
					'library' => 'all',
				),
				array (
					'key' => 'field_523382f555a75',
					'label' => 'Background Repeat',
					'name' => 'background_repeat',
					'type' => 'select',
					'choices' => array (
						'repeat' => 'Repeat',
						'no-repeat' => 'No Repeat',
						'repeat-x' => 'Repeat X',
						'repeat-y' => 'Repeat Y',
						'inherit' => 'Inherit',
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5233837455a76',
					'label' => 'Background Size',
					'name' => 'background_size',
					'type' => 'select',
					'choices' => array (
						'cover' => 'Cover',
						'contain' => 'Contain',
						'inherit' => 'Inherit',
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),
				array (
					'key' => 'field_5233842d55a78',
					'label' => 'Background Position',
					'name' => 'background_position',
					'type' => 'select',
					'choices' => array (
						'left top' => 'left top',
						'left center' => 'left center',
						'left bottom' => 'left bottom',
						'right top' => 'right top',
						'right center' => 'right center',
						'right bottom' => 'right bottom',
						'center top' => 'center top',
						'center center' => 'center center',
						'center bottom' => 'center bottom',
						'inherit' => 'Inherit',
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),		

				array (
					'key' => 'field_5233842d55a79',
					'label' => 'Background Attachment',
					'name' => 'background_attachment',
					'type' => 'select',
					'choices' => array (
						'fixed' => 'Fixed',
						'scroll' => 'Scroll',
					),
					'default_value' => '',
					'allow_null' => 1,
					'multiple' => 0,
				),	
				
				array (
					'key' => 'field_523384ce55a80',
					'label' => 'Background Overlay Color',
					'name' => 'background_overlay_color',
					'type' => 'color_picker',
					'default_value' => '',
				),
				array (
					'key' => 'field_523384ce55a81',
					'label' => 'Background Overlay Transparency',
					'name' => 'background_overlay_transparency',
					'instructions' => __('Set the between 0 and 100% transparent', XT_TEXT_DOMAIN),
					'type' => 'select',
					'choices' => array (
						'1' => '100%',
						'0.9' => '90%',
						'0.8' => '80%',
						'0.7' => '70%',
						'0.6' => '60%',
						'0.5' => '50%',
						'0.4' => '40%',
						'0.3' => '30%',
						'0.2' => '20%',
						'0.1' => '10%',
						'0' => '0%',
					),
					'default_value' => '0.7',
					'allow_null' => 0,
					'multiple' => 0,
				),
				array (
					'key' => 'field_523384ce55a82',
					'label' => 'Inner Content Background Color',
					'name' => 'content_background_color',
					'instructions' => __('This will only be applied in "Boxed Layout" mode', XT_TEXT_DOMAIN),
					'type' => 'color_picker',
					'default_value' => '',
				),
				array (
					'key' => 'field_523384ce55a83',
					'label' => 'Inner Content Background Transparency',
					'name' => 'content_background_transparency',
					'instructions' => __('Set the between 0 and 100% transparent<br>This will only be applied in "Boxed Layout" mode', XT_TEXT_DOMAIN),
					'type' => 'select',
					'choices' => array (
						'1' => '0%',
						'0.9' => '10%',
						'0.8' => '20%',
						'0.7' => '30%',
						'0.6' => '40%',
						'0.5' => '50%',
						'0.4' => '60%',
						'0.3' => '70%',
						'0.2' => '80%',
						'0.1' => '90%',
						'0' => '100%',
					),
					'default_value' => '0',
					'allow_null' => 0,
					'multiple' => 0,
				),
				
				
/*
				array (
					'key' => 'field_page_settings_header',
					'label' => 'header Options',
					'name' => 'tab-details',
					'type' => 'tab',
				),
				
				array (
					'key' => 'field_523382c945a70',
					'label' => 'Top Bar Over Cotnent',
					'name' => 'hide_page_title',
					'instructions' => 'This will hide the title bar including the breadcrumbs',
					'type' => 'select',
					'choices' => array (
						'all' => 'Top Bar & Main Menu Over Content',
						'mainmenu' => 'Main Menu Over Content'
					),
					'default_value' => '0',
					'allow_null' => 0,
					'multiple' => 0,
				)	
*/
				
				
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'page',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 2,
		));
		
		
		register_field_group(array (
			'id' => 'acf_video',
			'title' => 'Video Details',
			'fields' => array (
				array (
					'key' => 'field_51b8d3ffdfe45',
					'label' => 'Video Url',
					'name' => 'video_url',
					'type' => 'text',
					'instructions' => '<a target="_blank" href="http://codex.wordpress.org/Embeds">Click Here</a> to see the supported providers',
					'default_value' => '',
					'placeholder' => 'https://www.youtube.com/watch?v=aHjpOzsQ9YI',
					'maxlength' => '',
				)
			),
			'location' => array (
				array (
					array (
						'param' => 'post_format',
						'operator' => '==',
						'value' => 'video',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (
					0 => 'excerpt',
					1 => 'custom_fields',
					2 => 'discussion',
					3 => 'revisions',
				),
			),
			'menu_order' => 0,
		));
		
		
		$gallery_fields = array();
		
		if(class_exists('XT_Galleria')) {
			$gallery_fields[] = array (
				'key' => 'field_51c4d5b5f6474',
				'label' => 'Gallery Theme',
				'name' => 'gallery_theme',
				'type' => 'radio',
				'choices' => XT_Galleria::getThemes(),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => XT_Galleria::getDefaultTheme(),
				'layout' => 'vertical',
			);
			$gallery_fields[] = array (
				'key' => 'field_51c4d5b5f6470',
				'label' => 'Number of columns',
				'name' => 'gallery_columns',
				'type' => 'select',
				'choices' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9'
				),
				'default_value' => 3,
				'layout' => 'vertical',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_51c4d5b5f6474',
							'operator' => '==',
							'value' => 'native',
						),
					),
					'allorany' => 'all',
				),
			);	
			$gallery_fields[] = array (
				'key' => 'field_51c4d5b5f6478',
				'label' => 'Max Height (px)',
				'name' => 'gallery_height',
				'type' => 'text',
				'default_value' => 300,
				'layout' => 'vertical',
			);					
		}
		
		$gallery_fields[] = array (
			'key' => 'field_51c4d5b5f6475',
			'label' => 'Photos',
			'name' => 'gallery_photos',
			'type' => 'repeater',
			'sub_fields' => array (
				array (
					'key' => 'field_51c4d6b5f6477',
					'label' => 'Photo Upload',
					'name' => 'photo_file',
					'type' => 'image',
					'column_width' => '',
					'save_format' => 'object',
					'preview_size' => 'medium',
					'library' => 'all',
				),				
				array (
					'key' => 'field_51c4d622f6476',
					'label' => 'Photo Title',
					'name' => 'photo_title',
					'type' => 'text',
					'column_width' => '',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				)
				
			),
			'row_min' => 0,
			'row_limit' => '',
			'layout' => 'row',
			'button_label' => 'Add Photo',
		);
				
		register_field_group(array (
			'id' => 'acf_gallery',
			'title' => 'Photo Gallery',
			'fields' => $gallery_fields,
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
						'order_no' => 0,
						'group_no' => 0,
					),
					array (
						'param' => 'post_format',
						'operator' => '==',
						'value' => 'gallery',
						'order_no' => 0,
						'group_no' => 0,
					),
				)
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (),
			),
			'menu_order' => 0,
		));
		
	
		register_field_group(array (
			'id' => 'acf_endless_posts_settings',
			'title' => 'Endless Posts Settings',
			'fields' => array (
				array (
					'key' => 'field_547258419f53b',
					'label' => 'Endless Scroll Animation',
					'name' => 'endless_animation',
					'type' => 'select',
					'choices' => array(
						'none' => __('None', XT_TEXT_DOMAIN),
						'cards' => __('Cards', XT_TEXT_DOMAIN),
						'grow' => __('Grow', XT_TEXT_DOMAIN),
						'flip' => __('Flip', XT_TEXT_DOMAIN),
						'fly' => __('Fly', XT_TEXT_DOMAIN),
						'fly-simplified' => __('Fly Simplified', XT_TEXT_DOMAIN),
						'fly-reverse' => __('Fly Reverse', XT_TEXT_DOMAIN),
						'skew' => __('Skew', XT_TEXT_DOMAIN),
						'helix' => __('Helix', XT_TEXT_DOMAIN),
						'wave' => __('Wave', XT_TEXT_DOMAIN),
						'fan' => __('Fan', XT_TEXT_DOMAIN),
						'tilt' => __('Tilt', XT_TEXT_DOMAIN),
						'curl' => __('Curl', XT_TEXT_DOMAIN),
						'papercut' => __('Papercut', XT_TEXT_DOMAIN),
						'zipper' => __('Zipper', XT_TEXT_DOMAIN),
						'fade' => __('Fade', XT_TEXT_DOMAIN),
						'twirl' => __('Twirl', XT_TEXT_DOMAIN)
					),
					'default_value' => 'none',
					'layout' => 'vertical'
				),	
				array (
					'key' => 'field_547258419f54a',
					'label' => 'Update Page Title / Url on scroll',
					'name' => 'update_state',
					'type' => 'true_false',
					'column_width' => '',
					'message' => '',
					'default_value' => 0,
				),							
				array (
					'key' => 'field_547258419f53c',
					'label' => 'Endless Tabs',
					'name' => 'endless_tabs',
					'type' => 'repeater',
					'sub_fields' => array (
						array (
							'key' => 'field_5472587c9f53d',
							'label' => 'Tab Name',
							'name' => 'name',
							'type' => 'text',
							'required' => 1,
							'column_width' => '',
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'formatting' => 'html',
							'maxlength' => '',
						),
						array (
							'key' => 'field_5472588a9f53e',
							'label' => 'Posts Query',
							'name' => 'query',
							'type' => 'select',
							'column_width' => '',
							'choices' => array (
								'most-recent' => 'Most Recent',
								'most-viewed' => 'Most Viewed',
								'most-liked' => 'Most Liked',
								'most-discussed' => 'Most Discussed',
								'selection' => 'Manual Selection',
							),
							'other_choice' => 0,
							'save_other_choice' => 0,
							'default_value' => 'most-recent',
						),
						
						array (
							'key' => 'field_5472588a9f53f',
							'label' => 'Query Post Formats',
							'name' => 'query_post_formats',
							'type' => 'true_false',
							'column_width' => '',
							'message' => '',
							'default_value' => 0,
							'conditional_logic' => array (
								'status' => 1,
								'rules' => array (
									array (
										'field' => 'field_5472588a9f53e',
										'operator' => '!=',
										'value' => 'selection',
									),
								),
								'allorany' => 'all',
							),
						),
						array (
							'key' => 'field_5472588a9f54f',
							'label' => '"Filter By Post Format',
							'name' => 'format',
							'type' => 'checkbox',
							'column_width' => '',
							'choices' => xt_get_post_formats(),
							'other_choice' => 0,
							'save_other_choice' => 0,
							'default_value' => '',
							'layout' => 'horizontal',
							'conditional_logic' => array (
								'status' => 1,
								'rules' => array (
									array (
										'field' => 'field_5472588a9f53f',
										'operator' => '==',
										'value' => '1',
									),
									array (
										'field' => 'field_5472588a9f53e',
										'operator' => '!=',
										'value' => 'selection',
									),
								),
								'allorany' => 'all',
							),
						),
						array (
							'key' => 'field_5472592c9f53f',
							'label' => 'Manually select posts',
							'name' => 'include_posts',
							'type' => 'relationship_formats',
							'return_format' => 'id',
							'post_type' => array (
								0 => 'post'
							),
							'post_format' => array (
								0 => 'all'
							),
							'taxonomy' => array (
								0 => 'all',
							),
							'filters' => array (
								0 => 'search',
								1 => 'post_format',
							),
							'result_elements' => array (
								0 => 'featured_image',
								1 => 'post_format',
							),
							'max' => 10,
							'conditional_logic' => array (
								'status' => 1,
								'rules' => array (
									array (
										'field' => 'field_5472588a9f53e',
										'operator' => '==',
										'value' => 'selection',
									),
								),
								'allorany' => 'all',
							),
						),
						array (
							'key' => 'field_547259859f540',
							'label' => 'Posts Per Page',
							'name' => 'posts_per_page',
							'type' => 'number',
							'column_width' => '',
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'min' => '',
							'max' => '',
							'step' => '',
							'conditional_logic' => array (
								'status' => 1,
								'rules' => array (
									array (
										'field' => 'field_5472588a9f53e',
										'operator' => '!=',
										'value' => 'selection',
									),
								),
								'allorany' => 'all',
							),
						),
						array (
							'key' => 'field_54725a0e9f541',
							'label' => 'Show post category within sidebar',
							'name' => 'show_post_category',
							'type' => 'true_false',
							'column_width' => '',
							'message' => '',
							'default_value' => 1,
						),
						array (
							'key' => 'field_54725a4d9f542',
							'label' => 'Show post author within sidebar',
							'name' => 'show_post_author',
							'type' => 'true_false',
							'column_width' => '',
							'message' => '',
							'default_value' => 0,
						),
						array (
							'key' => 'field_54725a759f543',
							'label' => 'Show post date within sidebar',
							'name' => 'show_post_date',
							'type' => 'true_false',
							'column_width' => '',
							'message' => '',
							'default_value' => 1,
						),
						array (
							'key' => 'field_54725a909f544',
							'label' => 'Show post stats within sidebar',
							'name' => 'show_post_stats',
							'type' => 'true_false',
							'column_width' => '',
							'message' => '',
							'default_value' => 0,
						),
					),
					'row_min' => '',
					'row_limit' => '4',
					'layout' => 'row',
					'button_label' => 'Add New Endless Tab',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'page_template',
						'operator' => '==',
						'value' => 'tpl-endless-posts.php',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'default',
				'hide_on_screen' => array (
					0 => 'the_content',
					1 => 'excerpt',
					2 => 'custom_fields',
					3 => 'discussion',
					4 => 'comments',
					5 => 'revisions',
					6 => 'slug',
					7 => 'format',
					8 => 'featured_image',
					9 => 'categories',
					10 => 'tags',
					11 => 'send-trackbacks',
				),
			),
			'menu_order' => 0,
		));	
	
	}

}
add_action('init', 'xt_register_field_group');