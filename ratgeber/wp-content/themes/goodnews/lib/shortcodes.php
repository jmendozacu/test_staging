<?php
function xt_shortcode_logo($atts) {
	
	extract( shortcode_atts( array(
	  'link' => false,
	  'align' => 'text-left',
	  'width' => 'auto',
	), $atts ) );
	
	
	$header_logo = xt_option('header_logo', 'url');
	$retina_header_logo = xt_option('retina_header_logo', 'url');
		
	$logo_output = "";
				
	if( !empty($header_logo) ) {

		if(!empty($link))
			$logo_output .= '<a href="'.home_url('/').'>" class="site-logo">';
	  	
	  	$logo_output = '<img class="logo-desktop regular" src="'.$header_logo.'" data-interchange="['.esc_url( $retina_header_logo ).', (retina)]" alt="'.esc_attr( get_bloginfo('name') ).'">';

	  	if(!empty($link))
	  		$logo_output .= '</a>';
				
	}else{
	
		if(!empty($link))
			$logo_output .= '<h2 class="widgettitle"><a href="'.home_url('/').'">'.esc_attr( get_bloginfo('name') ).'</a></h1>';
		
		else
			$logo_output .= '<h2 class="widgettitle">'.esc_attr( get_bloginfo('name') ).'</h1>';
	}

    ob_start();
    ?>
    
    <?php if(!empty($logo_output)): ?>
    <div class="vc_xt_logo wpb_content_element">
    <div class="xt_logo">
    	<div class="<?php echo esc_attr($align); ?>" style="width:<?php echo esc_attr($width); ?>">
    		<?php echo esc_html($logo_output);?>
    	</div>
    </div>
    </div>
    <?php endif; ?>
    
    <?php
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
add_shortcode( 'xt_logo', 'xt_shortcode_logo' );

function xt_shortcode_divider( $atts ) {

	extract( shortcode_atts( array(
	  'title' => '',
	  'heading' => '',
	  'align' => 'left',
	  'padding_top' => '0',
	  'padding_bottom'=> '0',
	  'margin_top' => '0',
	  'margin_bottom'=> '18',
	  'css_animation' => '',
	), $atts ) );

   	ob_start();
   	
   	$heading_num = $heading;
   	if($heading_num == 1) {
	   	$heading_num = "";
   	}
    ?>
    
    <?php if(!empty($title)): ?>
    <div class="vc_xt_title_divider wpb_content_element">
    <div class="widget xt_title_divider <?php echo esc_attr($css_animation); ?>">
    	<div style="text-align:<?php echo esc_attr($align); ?>; padding-top:<?php echo esc_attr($padding_top); ?>px; padding-bottom:<?php echo esc_attr($padding_bottom); ?>px;">
    		<span class="heading-t<?php echo esc_attr($heading_num);?>"></span>
			<h<?php echo esc_attr($heading);?> class="widgettitle" style="padding-top:<?php echo esc_attr($margin_top); ?>px; padding-bottom:<?php echo esc_attr($margin_bottom); ?>px;"><?php echo esc_attr($title); ?></h<?php echo esc_attr($heading);?>>
			<span class="heading-b<?php echo esc_attr($heading_num);?>"></span>
    	</div>
    </div>
    </div>
    <?php endif; ?>
    
    <?php
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
add_shortcode( 'xt_divider', 'xt_shortcode_divider' );


function xt_shortcode_button( $atts ) {

	extract( shortcode_atts( array(
	  'button_text' => '',
	  'button_align' => '',
	  'button_icon' => '',
	  'button_icon_position' => '',
	  'button_link_page' => '',
	  'button_link_product' => '',
	  'button_link_external' => '',
	  'button_size' => '',
	  'button_expanded' => '',

	  'button_vertical_padding'=> '15',
	  'button_horizontal_padding'=> '25',
	  
	  'button_border_width' => '1',
	  'button_border_radius' => '3',
	  'button_rounded' => '',
	  
	  'button_bg_color' => xt_option('primary-color', 'color'),
	  'button_border_color' => '',
	  'button_text_color' => '#fff',
	  
	  'button_hover_bg_color' => '',
	  'button_hover_border_color' => '',
	  'button_hover_text_color' => '',
	  	  
	  'button_animation' => '',
	  'button_animation_speed' => '',
	  'css_animation' => '',
	), $atts ) );



	$link = '';
	if(!empty($button_link_page)) {
	
		$link = get_permalink($button_link_page);
		
	} else if(!empty($button_link_product)) {
	
		$link = get_permalink($button_link_product);
		
	} else if(!empty($button_link_external)){
	
		$link = $button_link_external;
	}


	// Set Widget Styles
			
	$widgetStyle = '';
	if(!empty($button_align)) {
	
		$widgetStyle .= 'text-align: '.$button_align.';';
	}
	
		
	// Set Styles
	
	$style = '';
	$icon_class = '';

	if(empty($button_text) && !empty($button_icon)) {
		$icon_class .= ' loner';
	}

	if(!empty($button_vertical_padding)) {
	
		$button_vertical_padding = str_replace('px', '', $button_vertical_padding);
		$style .= 'padding-top: '.$button_vertical_padding.'px;';
		$style .= 'padding-bottom: '.$button_vertical_padding.'px;';
	}
	if(!empty($button_horizontal_padding)) {
	
		$button_horizontal_padding = str_replace('px', '', $button_horizontal_padding);
		$style .= 'padding-right: '.$button_horizontal_padding.'px;';
		$style .= 'padding-left: '.$button_horizontal_padding.'px;';
	}	
			
	if(!empty($button_border_width)) {
	
		$button_border_width = str_replace('px', '', $button_border_width);
		$style .= 'border-width: '.$button_border_width.'px;';
	}
	
	if(empty($button_rounded) && !empty($button_border_radius)) {
		
		$button_border_radius = str_replace('px', '', $button_border_radius);
		
		$style .= '-moz-border-radius: '.$button_border_radius.'px;';
		$style .= '-webkit-border-radius: '.$button_border_radius.'px;';
		$style .= 'border-radius: '.$button_border_radius.'px;';
	}

	if(!empty($button_bg_color)) {
	
		$style .= 'background-color: '.$button_bg_color.';';
	}
	if(!empty($button_border_color)) {
	
		$style .= 'border-color: '.$button_border_color.';';
	}
	if(!empty($button_text_color)) {
	
		$style .= 'color: '.$button_text_color.';';
	}		

	// Set Data Attr
	
	$data = '';
	
	if(!empty($button_hover_bg_color)) {
	
		$data .= 'data-hover_bg_color = "'.esc_attr($button_hover_bg_color).'" ';
	}
	if(!empty($button_hover_border_color)) {
	
		$data .= 'data-hover_border_color = "'.esc_attr($button_hover_border_color).'" ';
	}
	if(!empty($button_hover_text_color)) {
	
		$data .= 'data-hover_text_color = "'.esc_attr($button_hover_text_color).'" ';
	}	


	
   	ob_start();
   	?>
   	<div class="vc_xt_button wpb_content_element">
   	<div class="widget xt_button <?php echo esc_attr($css_animation); ?>" style="<?php echo esc_attr($widgetStyle); ?>">
		<a class="button <?php echo esc_attr($button_size);?> <?php echo !empty($button_expanded) ? 'expand' : '';?>  <?php echo !empty($button_rounded) ? 'round' : '';?> <?php echo esc_attr($button_animation); ?> <?php echo esc_attr($button_animation_speed); ?>" href="<?php echo esc_url($link); ?>" <?php echo ($data); ?> style="<?php echo esc_attr($style); ?>">
			<?php if(!empty($button_icon) && $button_icon_position == 'left'): ?>
				<span class="fa fa-<?php echo esc_attr($button_icon); ?> <?php echo esc_attr($icon_class);?>"></span>
			<?php endif; ?>
			
			<?php echo esc_html($button_text); ?>
			
			<?php if(!empty($button_icon) && $button_icon_position == 'right'): ?>
				<span class="append fa fa-<?php echo esc_attr($button_icon); ?> <?php echo esc_attr($icon_class);?>"></span>
			<?php endif; ?>
		</a>
   	</div>
   	</div>
	<?php						
    $output = ob_get_contents();
    ob_end_clean();

    return $output;

}
add_shortcode( 'xt_button', 'xt_shortcode_button' );




function xt_shortcode_ads( $atts ) {

	extract( shortcode_atts( array(
	  'title' => '',
	  'ad_type' => '',
	  'ad_zone' => '',
	  'ad_block' => '',
	  'ad_place' => '',
	  'bordered' => '',
	  'css_animation' => '',
	), $atts ) );

	$shortcode = "";
	
	if($ad_type == 'place' && isset($ad_place)) {

		$shortcode = '[sam id='.$ad_place.']';

	}else if($ad_type == 'block' && isset($ad_block)) {
		
		$shortcode = '[sam_block id='.$ad_block.']';

	}else if($ad_type == 'zone' && isset($ad_zone)) {
		
		$shortcode = '[sam_zone id='.$ad_zone.']';
		
	}
	
	if(empty($shortcode))
		return false;
	
   	ob_start();
   	?>
   	<div class="vc_xt_ads wpb_content_element">
   	<div class="widget xt_ads <?php echo esc_attr($css_animation); ?>">
	   	
	   	<?php if(!empty($bordered)):?>
	   		<div class="panel">
	   	<?php endif; ?>
	   	
	   	<?php if(!empty($title)):?>
		<h2 class="widgettitle"><?php wp_kses_post($title); ?></h2>
		<?php endif; ?>
		
		<?php if(!empty($bordered)):?>
	   		</div>
	   	<?php endif; ?>
		
		<?php echo do_shortcode($shortcode); ?>
   	</div>
   	</div>
	<?php						
    $output = ob_get_contents();
    ob_end_clean();

    return $output;

}
add_shortcode( 'xt_ads', 'xt_shortcode_ads' );
