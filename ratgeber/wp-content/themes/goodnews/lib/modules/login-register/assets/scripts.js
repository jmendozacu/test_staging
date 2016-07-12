var XT_AjaxLoginRegister = {

    reload: function( my_obj ){
	    
	    var redirect_url = '';
	    
        if ( my_obj.hasClass('login_form') &&  typeof _ajax_login_settings.redirect.login !== 'undefined' ){
            redirect_url= _ajax_login_settings.redirect.login.url;
        } else if ( my_obj.hasClass('register_form') && typeof _ajax_login_settings.redirect.registration !== 'undefined' ){
            redirect_url = _ajax_login_settings.redirect.registration.url;
        } else {
            redirect_url = _ajax_login_settings.redirect;
        }
        
        redirect_url += '?t='+new Date().getTime();
        location.href = redirect_url;
    },

    // Confirm passwords match
    confirm_password: function( my_obj ){

        $obj = jQuery( my_obj );
        value = $obj.val().trim();

        if ( value == '' ) return;

        $form = $obj.parents('form');

        match_value = jQuery('.user_password', $form).val();

        if ( value == match_value ) {
            msg = {
                "cssClass": "success",
                "description": null,
                "code": "success"
            };
        } else {
            msg = {
                "cssClass": "error",
                "description": _ajax_login_settings.match_error,
                "code": "error"
            };
        }

        return msg;
    }
};


jQuery( document ).ready(function( $ ){

    window.XT_AjaxLoginDialog = {
        open: function(){
            $('#ajax-login-register-login-dialog').dialog('open');

            $.ajax({
                type: "POST",
                url: _ajax_login_settings.ajaxurl,
                data: {
                    action: 'load_template',
                    referer: 'login_form',
                    template: 'login-form',
                    security: $('#ajax-login-register-login-dialog').attr('data-security')
                },
                success: function( msg ){
                    $( "#ajax-login-register-login-target" ).fadeIn().html( msg ); // Give a smooth fade in effect

                }
            });

        }
    };


    window.ajax_login_register_show_message = function( form_obj, msg ) {
    
    	jQuery('.ajax-login-register-msg-target', form_obj).removeClass('success error');
    	
        if ( msg.code == 'success_login' || msg.code == 'success_registration' ){
            jQuery('.ajax-login-register-msg-target', form_obj).addClass( msg.cssClass );
            jQuery('.ajax-login-register-msg-target', form_obj).fadeIn().html( msg.description );
            XT_AjaxLoginRegister.reload( form_obj );
        } else if ( msg.description == '' ){
            XT_AjaxLoginRegister.reload( form_obj );
        } else {
            if ( msg.cssClass == 'success' ){
                jQuery('.ajax-login-register-status-container').hide();
            } else {
                jQuery('.ajax-login-register-status-container').show();
            }
            jQuery('.ajax-login-register-msg-target', form_obj).addClass( msg.cssClass );
            jQuery('.ajax-login-register-msg-target', form_obj).fadeIn().html( msg.description );
        }
    };


    /**
     * Server side email validation.
     */
    window.ajax_login_register_validate_email = function( myObj ){
        $this = myObj;

        if ( $.trim( $this.val() ) == '' ) return;

        $form = $this.parents('form');

        $.ajax({
            data: "action=validate_email&email=" + $this.val(),
            dataType: 'json',
            type: "POST",
            url: _ajax_login_settings.ajaxurl,
            success: function( msg ){
                ajax_login_register_show_message( $form, msg );
            }
        });
    }


    /**
     * Validate email
     */
    $( document ).on('blur', '.ajax-login-register-validate-email', function(){
        ajax_login_register_validate_email( $(this) );
    });


    /**
     * Check that username is valid
     */
    $( document ).on('blur', '.user_login', function(){

        if ( $.trim( $(this).val() ) == '' ) return;

        $form = $(this).parents('form');

        $.ajax({
            data: "action=validate_username&login=" + $( this ).val(),
            dataType: 'json',
            type: "POST",
            url: _ajax_login_settings.ajaxurl,
            success: function( msg ){
       
                ajax_login_register_show_message( $form, msg );
            }
        });
    });

    /**
     * Set-up our default dialog box with the following
     * parameters.
     */
    $('.ajax-login-register-container').dialog({
        autoOpen: false,
        width: _ajax_login_settings.dialog_width,
        resizable: false,
        draggable: false,
        modal: true,
        closeText: _ajax_login_settings.close_text,
        open: function() {
	        
	        $('.ui-widget-overlay').off('click');
		    $('.ui-widget-overlay').on('click', function() {
		       $('#ajax-login-register-login-dialog').dialog('close'); 
		       $('#ajax-login-register-dialog').dialog('close'); 
		    });

        }
    });


	    
});