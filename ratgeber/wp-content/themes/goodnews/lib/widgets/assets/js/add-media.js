jQuery(document).ready(function($){

	jQuery.fn.extend({
		insertAtCursor: function(myValue){
		  return this.each(function(i) {
			if (document.selection) {
			  //For browsers like Internet Explorer
			  this.focus();
			  var sel = document.selection.createRange();
			  sel.text = myValue;
			  this.focus();
			}
			else if (this.selectionStart || this.selectionStart == '0') {
			  //For browsers like Firefox and Webkit based
			  var startPos = this.selectionStart;
			  var endPos = this.selectionEnd;
			  var scrollTop = this.scrollTop;
			  this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
			  this.focus();
			  this.selectionStart = startPos + myValue.length;
			  this.selectionEnd = startPos + myValue.length;
			  this.scrollTop = scrollTop;
			} else {
			  this.value += myValue;
			  this.focus();
			}
		  });
		}
	});
	
	$('.add-media').each(function() {
		
		var button = $(this);
	    var custom_uploader;
	    var target = button.data('target');
	    
	    $(button).click(function(e) {
	        e.preventDefault();
	        //If the uploader object has already been created, reopen the dialog
	        if (custom_uploader) {
	            custom_uploader.open();
	            return;
	        }
	        //Extend the wp.media object
	        custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: 'Choose Image',
	            button: {
	                text: 'Choose Image'
	            },
	            multiple: true
	        });
	        //When a file is selected, grab the URL and set it as the text field's value
	        custom_uploader.on('select', function() {
	            attachments = custom_uploader.state().get('selection');
	            attachments.each(function(item) {
		          item = item.toJSON();
		          $(target).insertAtCursor('<img src="'+item.url+'" alt="'+item.title+'">\n\r');  
	            })
	        });
	        //Open the uploader dialog
	        custom_uploader.open();
	    });
    
    })
});
