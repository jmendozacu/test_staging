jQuery(document).ready(function($){
	
	var xt_widget_conditional = function() {

		var first_load = true;
		
		$('.widgets-php .widget-content [data-showif],.widgets-php .widget-content [data-hideif]').each(function() {
			
			$(this).hide();
			
			var $this = $(this);
			var widget = $this.closest('.widget-content');
			var type = $this.data('showif') ? 'show' : 'hide';
			
			var data = '';
			if(type == 'show')
				data = $this.data('showif');
			else
				data = $this.data('hideif');
				
				
			if(data.length > 0) {
				
				var field = '';
				var field_data = [];
				var field_name = '';
				var field_dependency = [];
				
				data = data.trim();
				var fields = data.split(',');
				for(var i = 0 ; i < fields.length ; i++) {
					
					field = fields[i];
					field = field.trim();
					
					field_data = field.split(':');
					field_name = field_data[0];
					field_dependency = field_data[1];
					field_dependency = field_dependency.trim();
					field_dependency = field_dependency.split('|');
					
					field = widget.find('[id*='+field_name+']');
					
					if(field.length > 0) {
	
						field.on('change', function() {
							
							if($.inArray(field.val(), field_dependency) !== -1) {
								(type == 'show') ? $this.show() : $this.hide();
							}else{
								(type == 'show') ? $this.hide() : $this.show();
							}
						
						});
						
						if(first_load) 
							field.change();
					}
				}
				
			}
		});
			
		first_load = false;	
		
	}
	
	xt_widget_conditional();
	
	$(document).on('ajaxComplete', function() {
		xt_widget_conditional();
	});

});