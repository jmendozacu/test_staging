jQuery().ready(function(){
	
	XT_LIKES.init = function() {
		
		jQuery('a.xt-like_btn').off('click');
	    jQuery('a.xt-like_btn').on('click', function(){
	        var response_div = jQuery(this).parent().parent();
	        jQuery.ajax({
	            url         : XT_LIKES.base_url,
	            data        : {'xt-vote_like':jQuery(this).data('postid')},
	            success     : function(data){
	                response_div.html(data).fadeIn(900);
	            }
	        });
	    })
	    
	    jQuery('a.xt-dislike_btn').off('click');
	    jQuery('a.xt-dislike_btn').on('click', function(){
	        var response_div = jQuery(this).parent().parent();
	        jQuery.ajax({
	            url         : XT_LIKES.base_url,
	            data        : {'xt-vote_dislike':jQuery(this).data('postid')},
	            success     : function(data){
	                response_div.html(data).fadeIn(900);
	            }
	        });
	    });

	};
	
	XT_LIKES.refresh = function() {
		
	    jQuery('.xt-votes').each(function() {
	
			var response_div = jQuery(this);
	        jQuery.ajax({
	            url         : XT_LIKES.ajax_url,
	            data        : {'action':'refresh_likes', 'post_id':jQuery(this).data('postid')},
	            success     : function(data){
	                response_div.html(data).fadeIn(900);
	                XT_LIKES.init();
	            }
	        });
	       
		});
		
	};

	XT_LIKES.init();	    
	XT_LIKES.refresh();

});
