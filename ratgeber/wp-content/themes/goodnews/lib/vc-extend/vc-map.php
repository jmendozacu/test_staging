<?php

if (function_exists('vc_map')) {
    
    function xt_register_js_composer()
    {
        
        global $wpdb;
        
        $font_icons = xt_get_fontawsome_icons();
        $patterns   = xt_get_patterns(true);
        $target_arr = xt_get_link_targets();
		$opacity = xt_get_opacities();
		$filters = xt_get_css_filters();
        $css_animations = xt_get_css_animations();
        $sticky_sidebars = (bool)xt_option('enable_sticky_sidebars', null, true); 
        $rtl_mode = is_rtl();


        
        /* VC Row block
        ---------------------------------------------------------- */
                
        $row_params = array(
            array(
                "type" => "textfield",
                "heading" => __("ID Name for Navigation", XT_TEXT_DOMAIN),
                "param_name" => "xt_row_id",
                "description" => __("If this row wraps the content of one of your sections, set an ID. You can then use it for navigation. Ex: work", XT_TEXT_DOMAIN)
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Type", XT_TEXT_DOMAIN),
                "param_name" => "xt_row_type",
                "description" => __("You can specify whether the row is displayed fullwidth or in container.", XT_TEXT_DOMAIN),
                "value" => array(
                    
                    __("In Container", XT_TEXT_DOMAIN) => "in-container",
                    __("Fullwidth", XT_TEXT_DOMAIN) => "full-width"
                )
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Top (Divider)", XT_TEXT_DOMAIN),
                "param_name" => "xt_border_top",
                "description" => __("This will add a top border divider.", XT_TEXT_DOMAIN),
                "value" => array(
                    
                    __("No", XT_TEXT_DOMAIN) => "0",
                    __("Yes", XT_TEXT_DOMAIN) => "1"
                )
            ),
            array(
                'type' => 'dropdown',
                'heading' => __('Show Background Live Preview', XT_TEXT_DOMAIN),
                'param_name' => 'xt_bg_preview',
                "value" => array(
                    
                    __("No", XT_TEXT_DOMAIN) => "no",
                    __("Yes", XT_TEXT_DOMAIN) => "yes"
                ),
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-12 vc-custom-design border-bottom"
            ),
            array(
		      "type" => "dropdown",
		      "heading" => __('Background Vertical Position', 'js_composer'),
		      "param_name" => "xt_bg_position",
		      "value" => array(
	            __("Middle", 'js_composer') => 'middle',
	            __("Top", 'js_composer') => 'top',
	            __("Bottom", 'js_composer') => 'bottom'
	          ),
              "group" => __("Design options", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"        
		    ),           
            array(
                "type" => "dropdown",
                "heading" => __("Background Attachment", XT_TEXT_DOMAIN),
                "param_name" => "xt_bg_attach",
                "description" => __("The background-attachment property sets whether a background image is fixed or scrolls with the rest of the page.", XT_TEXT_DOMAIN),
                "value" => array(
                    __("Fixed", XT_TEXT_DOMAIN) => "fixed",
                    __("Scroll", XT_TEXT_DOMAIN) => "scroll"
                ),
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-12 vc-custom-design border-bottom"
            ),
            array(
                "type" => "colorpicker",
                "heading" => __("Overlay Color", "wpb"),
                "param_name" => "xt_overlay_color",
                "description" => __("Select an overlay color", "wpb"),
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-4 vc-custom-design border-right border-bottom "
            ),
            array(
                "type" => "dropdown",
                "heading" => __('Overlay Pattern', XT_TEXT_DOMAIN),
                "param_name" => "xt_overlay_pattern",
                "description" => __("Set an overlay pattern", XT_TEXT_DOMAIN),
                "value" => array('' => '') + $patterns,
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-4 vc-custom-design border-right border-bottom"
            ),
            array(
                "type" => "dropdown",
                "heading" => __('Overlay Pattern Opacity', XT_TEXT_DOMAIN),
                "param_name" => "xt_pattern_opacity",
                "description" => __("Adjust pattern opacity", XT_TEXT_DOMAIN),
                "value" => $opacity,
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-4 vc-custom-design border-right border-bottom last"
            ),
            array(
                "type" => "checkbox",
                "heading" => __('Add Overlay Gradient', XT_TEXT_DOMAIN),
                "param_name" => "xt_overlay_gradient",
                'value' => array(
                    __('Yes', XT_TEXT_DOMAIN) => 'yes'
                ),
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-4 vc-custom-design border-right"
            ),
            array(
                "type" => "dropdown",
                "heading" => __('Overlay Gradient Direction', XT_TEXT_DOMAIN),
                "param_name" => "xt_overlay_gradient_direction",
                'value' => array(
                    __('Top -> Bottom', XT_TEXT_DOMAIN) => 'top-to-bottom',
                    __('Bottom -> Top', XT_TEXT_DOMAIN) => 'bottom-to-top',
                    ($rtl_mode ? __("Right -> Left", XT_TEXT_DOMAIN) : __("Left -> Right", XT_TEXT_DOMAIN)) => "left",
					($rtl_mode ? __("Left -> Right", XT_TEXT_DOMAIN) : __("Right -> Left", XT_TEXT_DOMAIN)) => "right",
                ),
                "dependency" => array(
                  "element" => "xt_overlay_gradient",
                  "value" => array(
                  	"yes"
                  )	
                ),
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-8 vc-custom-design border-right"
            ),
/*
            array(
                "type" => "colorpicker",
                "heading" => __('Overlay Gradient Color', XT_TEXT_DOMAIN),
                "param_name" => "xt_overlay_gradient_color",
                "value" => "",
                "group" => __("Design options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-4 vc-custom-design border-right last"
            ),
*/
            array(
		      "type" => "dropdown",
		      "heading" => __('Enable Background Video', 'js_composer'),
		      "param_name" => "xt_bg_video",
		      "value" => array(
	                        
	            __("No", 'js_composer') => '',
	            __("Yes", 'js_composer') => 'bg_video'
	          ),
	          "group" => __("Background Video", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"      
		    ),
		    array(
		      "type" => "dropdown",
		      "heading" => __('Video Vertical Position', 'js_composer'),
		      "param_name" => "xt_bg_video_position",
		      "value" => array(
	            __("Middle", 'js_composer') => 'middle',
	            __("Top", 'js_composer') => 'top',
	            __("Bottom", 'js_composer') => 'bottom'
	          ),
		      "dependency" => array(
                  "element" => "xt_bg_video",
                  "value" => array(
                  	"bg_video"
                  )	
              ),
              "group" => __("Background Video", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"        
		    ),		    
		    array(
		      "type" => "textfield",
		      "heading" => __('Video Url (Self Hosted MP4)', 'js_composer'),
		      "param_name" => "xt_bg_video_mp4",
		      "value" => '', 
			  "dependency" => array(
                  "element" => "xt_bg_video",
                  "value" => array(
                  	"bg_video"
                  )	
              ),
              "group" => __("Background Video", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"      
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __('Video Url (Self Hosted WebM)', 'js_composer'),
		      "param_name" => "xt_bg_video_webm",
		      "value" => '',
		      "dependency" => array(
                  "element" => "xt_bg_video",
                  "value" => array(
                  	"bg_video"
                  )	
              ),
              "group" => __("Background Video", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"        
		    ),
		    array(
		      "type" => "textfield",
		      "heading" => __('Video Url (Self Hosted OGV)', 'js_composer'),
		      "param_name" => "xt_bg_video_ogv",
		      "value" => '',
		      "dependency" => array(
                  "element" => "xt_bg_video",
                  "value" => array(
                  	"bg_video"
                  )	
              ),
              "group" => __("Background Video", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"        
		    ),
		    array(
		      "type" => "attach_image",
		      "heading" => __('Video Image Fallback', 'js_composer'),
		      "param_name" => "xt_bg_video_poster",
		      "value" => '',
		      "dependency" => array(
                  "element" => "xt_bg_video",
                  "value" => array(
                  	"bg_video"
                  )	
              ),
              "group" => __("Background Video", XT_TEXT_DOMAIN),
              "edit_field_class" => "vc_col-sm-12 vc-custom-design"        
		    ),
            array(
                "type" => "dropdown",
                "heading" => __("Enable Parallax", XT_TEXT_DOMAIN),
                "param_name" => "xt_parallax",
                "description" => __("This will enable parallax if a background image has been selected within the Design Tab.", XT_TEXT_DOMAIN),
                "value" => array(

                    __("No", XT_TEXT_DOMAIN) => "no",
                    __("Yes", XT_TEXT_DOMAIN) => "yes"
                ),
                "group" => __("Parallax options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-12 vc-custom-design"
            ),	
            array(
                "type" => "dropdown",
                "heading" => __("Enable Smooth Scroll", XT_TEXT_DOMAIN),
                "param_name" => "xt_smooth_scroll",
                "description" => __("This will enable smooth scrolling for a better parallax experience. You only have to activate this in at least 1 row to enable smooth scrolling.", XT_TEXT_DOMAIN),
                "value" => array(

                    __("No", XT_TEXT_DOMAIN) => "no",
                    __("Yes", XT_TEXT_DOMAIN) => "yes"
                ),
                "group" => __("Parallax options", XT_TEXT_DOMAIN),
                "edit_field_class" => "vc_col-sm-12 vc-custom-design"
            ),	    
        );
                
        foreach ($row_params as $param) {
            vc_add_param("vc_row", $param);
        }
        
        
        
        /* VC Inner Row block
        ---------------------------------------------------------- */
                
        $row_inner_params = array(
            array(
                "type" => "textfield",
                "heading" => __("ID Name for Navigation", XT_TEXT_DOMAIN),
                "param_name" => "xt_row_id",
                "description" => __("If this row wraps the content of one of your sections, set an ID. You can then use it for navigation. Ex: work", XT_TEXT_DOMAIN)
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Top (Divider)", XT_TEXT_DOMAIN),
                "param_name" => "xt_border_top",
                "description" => __("This will add a top border divider.", XT_TEXT_DOMAIN),
                "value" => array(
                    
                    __("No", XT_TEXT_DOMAIN) => "0",
                    __("Yes", XT_TEXT_DOMAIN) => "1"
                )
            )
        );

        foreach ($row_inner_params as $param) {
            vc_add_param("vc_row_inner", $param);
        }
        
 
         
        /* VC Column block
        ---------------------------------------------------------- */
               
        $column_params = array(
	        'sticky_sidebars' => array(
	            "type" => "dropdown",
	            "holder" => "div",
	            "class" => "",
	            "heading" => _x("Make Sticky Column", 'VC', XT_TEXT_DOMAIN),
	            "param_name" => "is_sticky",
	            "value" => array(
                    
                    __("No", XT_TEXT_DOMAIN) => "",
                    __("Yes", XT_TEXT_DOMAIN) => "yes"
                ),
	            "description" => ''
	        ),
	        array(
	            "type" => "dropdown",
	            "holder" => "div",
	            "class" => "",
	            "heading" => _x("Style as a", 'VC', XT_TEXT_DOMAIN),
	            "param_name" => "is_sidebar",
	            "value" => array(
                    __("Normal Column", XT_TEXT_DOMAIN) => "",
                    ($rtl_mode ? __("Right Sidebar", XT_TEXT_DOMAIN) : __("Left Sidebar", XT_TEXT_DOMAIN)) => "left",
                    ($rtl_mode ? __("Left Sidebar", XT_TEXT_DOMAIN) : __("Right Sidebar", XT_TEXT_DOMAIN)) => "right"
                ),
	            "description" => ''
	        ),	        
	        array(
	            "type" => "dropdown",
	            "holder" => "div",
	            "class" => "",
	            "heading" => _x("CSS Animation", 'VC', XT_TEXT_DOMAIN),
	            "param_name" => "css_animation",
	            "value" => $css_animations,
	            "description" => ''
	        )
        );
        
        if(!$sticky_sidebars) {
	        unset($column_params['sticky_sidebars']);
        }
        
        foreach ($column_params as $param) {
        
             vc_add_param("vc_column", $param);
			 vc_add_param("vc_column_inner", $param);
        }
        
       
 
 
         /* VC Custom Heading block
        ---------------------------------------------------------- */
               
        vc_remove_param('vc_custom_heading', 'google_fonts');
		vc_add_param('vc_custom_heading', array(
            'type' => 'google_fonts',
            'param_name' => 'google_fonts',
            'value' => '',
            'settings' => array(
                //'no_font_style' // Method 1: To disable font style
                //'no_font_style'=>true // Method 2: To disable font style
                'fields'=>array(
                    'font_family'=>'Roboto',   
                    // Default font style. Name:weight:style, example: "800 bold regular:800:normal"                  
                    'font_style'=>'400 regular:400:normal', 
                    'font_family_description' => __('Select font family.','js_composer'),
                    'font_style_description' => __('Select font styling.','js_composer')
                )
            ),
        ));

        
        
        /* Twitter block
        ---------------------------------------------------------- */
        vc_map(array(
            "name" => _x("Twitter", 'VC', XT_TEXT_DOMAIN),
            "base" => "xt_twitter",
            "class" => "",
            "icon" => "xt_vc_icon_twitter",
            "category" => _x(XT_VC_GROUP, 'VC', XT_TEXT_DOMAIN),
	        "description" => _x("Twitter feed widget", 'VC', XT_TEXT_DOMAIN),
            "params" => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Title", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "title",
                    "value" => "",
                    "description" => ''
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x('Screen Name (ex: @envato)', 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "screen_name",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Number of tweets to show", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "count",
                    "value" => '3',
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Show Avatar", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "show_avatar",
                    "value" => array(
                        _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                    ),
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Show Date", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "show_date",
                    "value" => array(
                        _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                    ),
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Bordered Box", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "bordered",
                    "value" => array(
                        _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                    ),
                    "description" => ''
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Call To Action Title", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "action_title",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "post_select",
                    "post_type" => "page",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Call To Action Page", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "action_obj_id",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Call To Action External Link", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "action_ext_link",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("CSS Animation", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "css_animation",
                    "value" => $css_animations,
                    "description" => ''
                )
            )
            
        ));

        
        
        /* Recent Comments block
        ---------------------------------------------------------- */
        vc_map(array(
            "name" => _x("Recent Comments", 'VC', XT_TEXT_DOMAIN),
            "base" => "xt_comments",
            "class" => "",
            "icon" => "xt_vc_icon_comments",
            "category" => _x(XT_VC_GROUP, 'VC', XT_TEXT_DOMAIN),
	        "description" => _x("Recent comments list", 'VC', XT_TEXT_DOMAIN),
            "params" => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Title", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "title",
                    "value" => "",
                    "description" => ''
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Number of comments", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "number",
                    "value" => "5",
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Show Date", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "show_date",
                    "value" => array(
                        _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                    ),
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Bordered Box", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "bordered",
                    "value" => array(
                        _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                    ),
                    "description" => ''
                )
                
            )
            
        ));
        
        /* Social Networks block
        ---------------------------------------------------------- */
        vc_map(array(
            "name" => _x("Social Networks", 'VC', XT_TEXT_DOMAIN),
            "base" => "xt_networks",
            "class" => "",
            "icon" => "xt_vc_icon_networks",
            "category" => _x(XT_VC_GROUP, 'VC', XT_TEXT_DOMAIN),
	        "description" => _x("Social Networks Icons / Links", 'VC', XT_TEXT_DOMAIN),
            "params" => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Title", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "title",
                    "value" => "",
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Bordered Box", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "bordered",
                    "value" => array(
                        _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                    ),
                    "description" => ''
                )
                
            )
            
        ));
    
        
        /* Button block
        ---------------------------------------------------------- */
        
        vc_map(array(
            'name' => __('Button', XT_TEXT_DOMAIN),
            'base' => 'xt_button',
            'icon' => 'xt_vc_icon_button',
            'category' => _x(XT_VC_GROUP, 'VC', XT_TEXT_DOMAIN),
            'front_enqueue_js' => XT_PARENT_URL.'/vc_extend/assets/js/button.js',
	        "description" => _x("Multi options button widget", 'VC', XT_TEXT_DOMAIN),
            'params' => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Text", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_text",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Button Align", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_align",
                    "value" => array(
                        ($rtl_mode ? __("Right", XT_TEXT_DOMAIN) : __("Left", XT_TEXT_DOMAIN)) => "left",
						($rtl_mode ? __("Left", XT_TEXT_DOMAIN) : __("Right", XT_TEXT_DOMAIN)) => "right",
                        _x("Center", 'VC', XT_TEXT_DOMAIN) => 'center'
                    ),
                    "description" => ''
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Button Icon", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_icon",
                    "value" => array('') + $font_icons,
                    "description" => 'Select a font awesome icon'
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Button Icon Position", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_icon_position",
                    "value" => array(
                        ($rtl_mode ? __("Right", XT_TEXT_DOMAIN) : __("Left", XT_TEXT_DOMAIN)) => "left",
						($rtl_mode ? __("Left", XT_TEXT_DOMAIN) : __("Right", XT_TEXT_DOMAIN)) => "right",
                    ),
                    "description" => '',
                    "dependency" => array(
                        "element" => "button_icon",
                        "value" => $font_icons
                    )
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Button Size", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_size",
                    "value" => array(
                        _x("Small", 'VC', XT_TEXT_DOMAIN) => 'small',
                        _x("Medium", 'VC', XT_TEXT_DOMAIN) => 'medium',
                        _x("Large", 'VC', XT_TEXT_DOMAIN) => 'large'
                    ),
                    "description" => '' 
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __('Expanded?', XT_TEXT_DOMAIN),
                    'param_name' => 'button_expanded',
                    'value' => array(
                        __('Yes', XT_TEXT_DOMAIN) => 'yes'
                    )
                ),
                array(
                    "type" => "post_select",
                    "post_type" => "page",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Link Page", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_link_page",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "post_select",
                    "post_type" => "product",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Link Product", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_link_product",
                    "value" => '',
                    "description" => ''
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("Link External", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "button_link_external",
                    "value" => '',
                    "description" => 'External Link or anchor to scroll to a section. Ex: #products'
                ),
                array(
		         	"type" => "textfield",
				 	"holder" => "div",
				 	"class" => "",
				 	"heading" => _x("Button Vertical Padding (px)", 'VC', XT_TEXT_DOMAIN),
				 	"param_name" => "button_vertical_padding",
				 	"value" => '15',
				 	"description" => '',
				 	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-6 vc-custom-design border-bottom border-right"  
				),	
		      	array(
		         	"type" => "textfield",
		         	"holder" => "div",
		         	"class" => "",
				 	"heading" => _x("Button Horizontal Padding (px)", 'VC', XT_TEXT_DOMAIN),
		         	"param_name" => "button_horizontal_padding",
		         	"value" => '25',
		         	"description" => '',
		         	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-6 vc-custom-design border-bottom last"  
				),							
				array(
		         	"type" => "textfield",
				 	"holder" => "div",
				 	"class" => "",
				 	"heading" => _x("Button Border Width (px)", 'VC', XT_TEXT_DOMAIN),
				 	"param_name" => "button_border_width",
				 	"value" => '',
				 	"description" => '',
				 	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom border-right"  
				),
		      	array(
		         	"type" => "textfield",
		         	"holder" => "div",
		         	"class" => "",
				 	"heading" => _x("Button Border Radius (px)", 'VC', XT_TEXT_DOMAIN),
		         	"param_name" => "button_border_radius",
		         	"value" => '',
		         	"description" => '',
		         	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom border-right"  
				),
				array(
		         	"type" => "checkbox",
		         	"holder" => "div",
		         	"class" => "",
				 	"heading" => _x("Button Rounded", 'VC', XT_TEXT_DOMAIN),
		         	"param_name" => "button_rounded",
		         	"value" => array(
                        _x("Yes", 'VC', XT_TEXT_DOMAIN) => 'rounded',
                    ),    
		         	"description" => '',
		         	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom last"  
				),
		      	array(
			      	"type" => "colorpicker",
				  	"heading" => _x("Button Border Color", 'VC', XT_TEXT_DOMAIN),
				  	"param_name" => "button_border_color",
				  	"description" => '',
				  	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom border-right" 
			    ),
			    array(
			      	"type" => "colorpicker",
				  	"heading" => _x("Button Background Color", 'VC', XT_TEXT_DOMAIN),
				  	"param_name" => "button_bg_color",
				  	"description" => '',
				  	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom border-right" 
			    ),
			    array(
			      	"type" => "colorpicker",
				  	"heading" => _x("Button Text Color", 'VC', XT_TEXT_DOMAIN),
				  	"param_name" => "button_text_color",
				  	"description" => '',
				  	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom last" 
			    ),
			    array(
			      	"type" => "colorpicker",
				  	"heading" => _x("Hover Border Color", 'VC', XT_TEXT_DOMAIN),
			      	"param_name" => "button_hover_border_color",
			      	"description" => '',
			      	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom border-right" 
			    ),
			    array(
			      	"type" => "colorpicker",
				  	"heading" => _x("Hover Background Color", 'VC', XT_TEXT_DOMAIN),
			      	"param_name" => "button_hover_bg_color",
				  	"description" => '',
				  	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom border-right" 
			    ),
			    array(
			      	"type" => "colorpicker",
				  	"heading" => _x("Hover Text Color", 'VC', XT_TEXT_DOMAIN),
				  	"param_name" => "button_hover_text_color",
				  	"description" => '',
				  	"group" => __("Design options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-4 vc-custom-design border-bottom last" 
			    ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Button Animation Loop', XT_TEXT_DOMAIN),
                    'param_name' => 'button_animation',
                    "value" => array(
                    	_x("", 'VC', XT_TEXT_DOMAIN) => '',
                        _x("Pulse", 'VC', XT_TEXT_DOMAIN) => 'animated-infinite pulse',
                        _x("Shake", 'VC', XT_TEXT_DOMAIN) => 'animated-infinite shake',
                         _x("Jump", 'VC', XT_TEXT_DOMAIN) => 'animated-infinite jump',
                    ),
                    "group" => __("Animation options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-12 vc-custom-design border-bottom" 
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Button Animation Speed', XT_TEXT_DOMAIN),
                    'param_name' => 'button_animation_speed',
                    "value" => array(
                        _x("Normal", 'VC', XT_TEXT_DOMAIN) => 'animation-speed-normal',
                        _x("Slow", 'VC', XT_TEXT_DOMAIN) => 'animation-speed-slow',
                        _x("Very Slow", 'VC', XT_TEXT_DOMAIN) => 'animation-speed-veryslow',
                        _x("Fast", 'VC', XT_TEXT_DOMAIN) => 'animation-speed-fast',
                    ),
                    "group" => __("Animation options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-12 vc-custom-design border-bottom" 
                ),
                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "class" => "",
                    "heading" => _x("CSS Animation", 'VC', XT_TEXT_DOMAIN),
                    "param_name" => "css_animation",
                    "value" => $css_animations,
                    "description" => '',
                    "group" => __("Animation options", XT_TEXT_DOMAIN),
				 	"edit_field_class" => "vc_col-sm-12 vc-custom-design" 
                )
            )
        ));
        
        
        if (function_exists('is_plugin_active') && is_plugin_active('mailchimp-widget/nm_mailchimp.php')) {
            
            $results = $wpdb->get_results('SELECT form_id, form_name FROM ' . $wpdb->prefix . 'nm_mc_forms ORDER BY form_name');
            $newsletters = array();
            foreach ($results as $result) {
                
                $name = !empty($result->form_name) ? $result->form_name : $result->form_id;
                $id   = $result->form_id;
                
                $newsletters[$name] = $id;
            }
            
            vc_map(array(
                "name" => _x("Newsletter", 'VC', XT_TEXT_DOMAIN),
                "base" => "xt_newsletter",
                "class" => "",
                "icon" => "xt_vc_icon_newsletter",
                "category" => _x(XT_VC_GROUP, 'VC', XT_TEXT_DOMAIN),
				"description" => _x("MailChimp newsletter form", 'VC', XT_TEXT_DOMAIN),
                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Title", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "title",
                        "value" => _x("Newsletter", 'VC', XT_TEXT_DOMAIN),
                        "description" => ''
                    ),
                    array(
                        "type" => "textarea",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Description", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "description",
                        "value" => _x("Get monthly fresh updates in your mailbox", 'VC', XT_TEXT_DOMAIN),
                        "description" => ''
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Newsletters", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "fid",
                        "value" => $newsletters,
                        "description" => ''
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Bordered Box", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "bordered",
                        "value" => array(
                            _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                            _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                        ),
                        "description" => ''
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("CSS Animation", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "css_animation",
                        "value" => $css_animations,
                        "description" => ''
                    )
                )
                
            ));
            
        }
 
         if (function_exists('is_plugin_active') && is_plugin_active('simple-ads-manager/simple-ads-manager.php')) {
            
            $zones_results =  $wpdb->get_results('SELECT id, name, description FROM ' . $wpdb->prefix . 'sam_zones WHERE trash != 1 ORDER BY id');
            $blocks_results = $wpdb->get_results('SELECT id, name, description  FROM ' . $wpdb->prefix . 'sam_blocks WHERE trash != 1 ORDER BY id');
            $places_results = $wpdb->get_results('SELECT id, name, description  FROM ' . $wpdb->prefix . 'sam_places WHERE trash != 1 ORDER BY id');

            $zones = array();
            foreach ($zones_results as $result) {
                
                $name = !empty($result->name) ? $result->name : !empty($result->description) ? $result->description : $result->id;
                $id   = $result->id;
                
                $zones[$name] = $id;
            }
            
            $blocks = array();
            foreach ($blocks_results as $result) {
                
                $name = !empty($result->name) ? $result->name : !empty($result->description) ? $result->description : $result->id;
                $id   = $result->id;
                
                $blocks[$name] = $id;
            }
            
            $places = array();
            foreach ($places_results as $result) {
                
                $name = !empty($result->name) ? $result->name : !empty($result->description) ? $result->description : $result->id;
                $id   = $result->id;
                
                $places[$name] = $id;
            }
            
            vc_map(array(
                "name" => _x("Advertisement", 'VC', XT_TEXT_DOMAIN),
                "base" => "xt_ads",
                "class" => "",
                "icon" => "xt_vc_icon_ads",
                "category" => _x(XT_VC_GROUP, 'VC', XT_TEXT_DOMAIN),
				"description" => _x("Advertisement widget based on Simple Ads Management", 'VC', XT_TEXT_DOMAIN),
                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Title", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "title",
                        "value" => _x("", 'VC', XT_TEXT_DOMAIN),
                        "description" => ''
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Insert an", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "ad_type",
                        "value" => array(
                            _x("Ad Place", 'VC', XT_TEXT_DOMAIN) => 'place',
                            _x("Ad Block", 'VC', XT_TEXT_DOMAIN) => 'block',
                            _x("Ad Zone", 'VC', XT_TEXT_DOMAIN) => 'zone'
                        ),
                        "description" => ''
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Select an Ad Block", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "ad_block",
                        "value" => $blocks,
                        "description" => '',
                        "dependency" => array(
		                  "element" => "ad_type",
		                  "value" => array(
		                  	"block"
		                  )	
		                )
                    ), 
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Select an Ad Block", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "ad_zone",
                        "value" => $zones,
                        "description" => '',
                        "dependency" => array(
		                  "element" => "ad_type",
		                  "value" => array(
		                  	"zone"
		                  )	
		                )
                    ),                     
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Select an Ad Place", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "ad_place",
                        "value" => $places,
                        "description" => '',
                        "dependency" => array(
		                  "element" => "ad_type",
		                  "value" => array(
		                  	"place"
		                  )	
		                )
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("Bordered Box", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "bordered",
                        "value" => array(
                            _x("No", 'VC', XT_TEXT_DOMAIN) => '',
                            _x("Yes", 'VC', XT_TEXT_DOMAIN) => 1
                        ),
                        "description" => ''
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => _x("CSS Animation", 'VC', XT_TEXT_DOMAIN),
                        "param_name" => "css_animation",
                        "value" => $css_animations,
                        "description" => ''
                    )
                )
                
            ));
            
        }

       
    }
    add_action('vc_before_init', 'xt_register_js_composer');
}