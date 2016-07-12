jQuery(document).ready(function($) {
	
	$('#vc_properties-panel').ajaxComplete(function( event, xhr, settings ) {
	
		$('.wpb_vc_param_value.query_type').off('change', xt_vc_query_type_changed);
		$('.wpb_vc_param_value.query_type').on('change',  xt_vc_query_type_changed);
	
		$('select.xt_bg_preview').off('change', xt_vc_toggle_overlay_preview);
		$('select.xt_bg_preview').on('change', xt_vc_toggle_overlay_preview).change();
		
		$('.vc-custom-design').off('click', function() { setTimeout(xt_vc_update_overlay_preview, 300) });
		$('.vc-custom-design').on('click', function() { setTimeout(xt_vc_update_overlay_preview, 300) });
		
		$('select.xt_overlay_pattern').off('change', xt_vc_update_overlay_preview);
		$('select.xt_overlay_pattern').on('change', xt_vc_update_overlay_preview);
		
		$('select.xt_pattern_opacity').off('change', xt_vc_update_overlay_preview);
		$('select.xt_pattern_opacity').on('change', xt_vc_update_overlay_preview);
		
		$('select.vc_background-style').off('change', xt_vc_update_overlay_preview);
		$('select.vc_background-style').on('change', xt_vc_update_overlay_preview);

		$('select.xt_bg_attach').off('change', xt_vc_update_overlay_preview);
		$('select.xt_bg_attach').on('change', xt_vc_update_overlay_preview);
		
	});	
	
	function xt_vc_query_type_changed() {
			
		var $wrapper = $(this).closest('.wpb_edit_form_elements');
		var value = $(this).val();
		var select;
		var count;
		
		if(value == 'selection') {
			
			select = $wrapper.find('.wpb_vc_param_value.category');
			
		}else{
		
			select = $wrapper.find('.wpb_vc_param_value.include_posts');
			count = $wrapper.find('.wpb_vc_param_value.number');
			count.val('');
		}
		
		select.val('');
		
		var select_id = select.attr('id');
		$('#ms-'+select_id+' .ms-selection ul li').each(function() {
			$(this).click();
		});
	}
	
	function xt_vc_toggle_overlay_preview() {
		
		if($(this).val() == 'yes') {
			xt_vc_update_overlay_preview();
		}else{
			xt_vc_hide_overlay_preview();
		}
		
	}
	
	function xt_vc_hide_overlay_preview() {
		
		var wrap = $('select.xt_bg_preview');
		
		var currentPattern = wrap.parent().find('.xt_row_bg_preview');
		if(currentPattern.length > 0) 
			currentPattern.remove();
			
	}
	function xt_vc_update_overlay_preview() {
	
		var wrap = $('select.xt_bg_preview');
		
		xt_vc_hide_overlay_preview();
		
		if(wrap.val() == 'no')
			return false;
				
		var bg_image = $('.vc_image img').attr('src');
		var bg_style = $('select.vc_background-style').val();	
		
		var bg_size = 'inherit';
		var bg_repeat = 'no-repeat;';
		
		if(bg_style == 'cover' || bg_style == 'contain') {
			bg_size = bg_style;
			
		}else if(bg_style == 'no-repeat' || bg_style == 'repeat') {
			bg_repeat = bg_style;
		}
		
		var bg_attach = $('.xt_bg_attach').val();
		var overlay_color = $('input.xt_overlay_color').val();
		var overlay_pattern = $('select.xt_overlay_pattern').val();
		var pattern_opacity = parseInt($('select.xt_pattern_opacity').val());
		pattern_opacity = parseFloat(pattern_opacity / 100);
		
		$('input.xt_overlay_color').trigger('blur');
		
		var output = '<div class="xt_row_bg_preview" style="background-image:url('+bg_image+'); background-repeat:'+bg_repeat+'; background-size:'+bg_size+'; background-attachment: '+bg_attach+'">';
		output += '<div class="overlay" style="background-color: '+overlay_color+';"></div>';
		output += '<div class="pattern" style="opacity: '+pattern_opacity+'; background-image:url('+xt_vars.assets_url+'/patterns/'+overlay_pattern+'.png);"></div>';
		output += '</div>';
		
		$('select.xt_bg_preview').after(output);
	}

	
});

