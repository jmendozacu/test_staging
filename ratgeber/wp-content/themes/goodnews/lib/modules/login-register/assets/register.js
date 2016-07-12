jQuery( document ).ready(function( $ ){

    /**
     * Close our dialog box when the user clicks
     * cancel/exit/close.
     */
    $( document ).on('click', '.ajax-login-register-container .cancel', function(){
        $(this).closest('.ajax-login-register-container').dialog('close');
    });

    window.XT_AjaxLoginRegisterDialog = {
        open: function(){
            $('#ajax-login-register-dialog').dialog('open');

            var data = {
                action: 'load_template',
                template: 'register-form',
                referer: 'register_form',
                security:  $('#ajax-login-register-dialog').attr('data-security')
            };

            $.ajax({
                data: data,
                type: "POST",
                url: _ajax_login_settings.ajaxurl,
                success: function( msg ){
                
                 	if(msg == '-1') {
                    
	                    location.reload();
	                    
                    } else{
                    
	                    msg = '<a href="" class="ui-dialog-close"><i class="fa fa-times"></i></a>'+msg;
	                    $( "#ajax-login-register-target" ).fadeIn().html( msg ); // Give a smooth fade in effect
	                    $( "#ajax-login-register-dialog" ).dialog( "option", "position", {
						    my: "center top",
						    at: "center top",
						    of: 'body'
						    
						});
						
								    
					    $('.ui-dialog-close').off('click');
					    $('.ui-dialog-close').on('click',  function(e) {
					    	e.preventDefault();
					    	$(this).closest('.ui-dialog-content').dialog('close');
						});
					}
                }
            });
        }
    };


    /**
     * Confirms that two input fields match
     */
    $( document ).on('keyup', '.user_confirm_password', function(){
        $form = $(this).parents('form');

        if ( $(this).val() == '' ){
            $( '.register_button', $form ).attr('disabled',true);
            $( '.register_button', $form ).animate({ opacity: 0.5 });
        } else {
            $( '.register_button', $form ).removeAttr('disabled');
            $( '.register_button', $form ).animate({ opacity: 1 });
        }
     });


    /**
     * Our form is loaded via AJAX, so we need to attach our event to the document.
     * When the form is submitted process the AJAX request.
     */
    $( document ).on('submit', '.register_form', function( event ){
        event.preventDefault();
		var $this = $(this);
        passwords_match = XT_AjaxLoginRegister.confirm_password('.user_confirm_password');

        if ( passwords_match.code == 'error' ){
            ajax_login_register_show_message( $this, msg );
        } else {
            $.ajax({
                data: "action=register_submit&" + $this.serialize(),
                dataType: 'json',
                type: "POST",
                url: _ajax_login_settings.ajaxurl,
                success: function( msg ) {
                    ajax_login_register_show_message( $this, msg );
                }
            });
        }
    });

    $( document ).on('click', '.already-registered-handle', function(e){
    	e.preventDefault();
	
        $('#ajax-login-register-dialog').dialog('close');
        XT_AjaxLoginDialog.open();
    });
});