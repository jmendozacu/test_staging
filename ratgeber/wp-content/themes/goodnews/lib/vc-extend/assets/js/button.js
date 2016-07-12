/* Widget Button Hover */
jQuery(document).ready(function($){
	$('.widget.xt_button a.button').each(function() {
		
		var initial_bg_color = $(this).css('background-color');
		var initial_border_color = $(this).css('border-color');
		var initial_color = $(this).css('color');

		$(this).mouseover(function() {
			
			var hover_bg_color = $(this).data('hover_bg_color');
			var hover_border_color = $(this).data('hover_border_color');
			var hover_text_color = $(this).data('hover_text_color');
		
			$(this).css({
				'background-color': hover_bg_color,
				'border-color': hover_border_color,
				'color': hover_text_color
			});

		});
		
		$(this).mouseout(function() {

			$(this).css({
				'background-color': initial_bg_color,
				'border-color': initial_border_color,
				'color': initial_color
			});
		});
	})
});