<?php

/**
 * Plugin initial setup
 */
function xt_ajax_login_register_enqueue_scripts(){


    $dependencies = array(
        'jquery',
        'jquery-ui-dialog'
    );
    
    wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-dialog');
		

    // Generic
    wp_enqueue_style( 'jquery-ui-custom', XT_MOD_URL."/".basename(__DIR__)."/assets/jquery-ui.css");
    wp_enqueue_style( 'ajax-login-register-style', XT_MOD_URL."/".basename(__DIR__)."/assets/style.css");
    wp_enqueue_script( 'ajax-login-register-script', XT_MOD_URL."/".basename(__DIR__)."/assets/scripts.js", $dependencies, false, true);

    // Login
    wp_enqueue_style( 'ajax-login-register-login-style', XT_MOD_URL."/".basename(__DIR__)."/assets/login.css" );
    wp_enqueue_script( 'ajax-login-register-login-script', XT_MOD_URL."/".basename(__DIR__)."/assets/login.js", $dependencies, false, true);

    // Register
    wp_enqueue_style( 'ajax-login-register-register-style', XT_MOD_URL."/".basename(__DIR__)."/assets/register.css" );
    wp_enqueue_script( 'ajax-login-register-register-script', XT_MOD_URL."/".basename(__DIR__)."/assets/register.js", $dependencies, false, true);

    wp_localize_script( 'ajax-login-register-script', '_ajax_login_settings', xt_ajax_login_register_localized_js() );
}
add_action( 'wp_enqueue_scripts', 'xt_ajax_login_register_enqueue_scripts', 1);


function xt_ajax_login_register_localized_js(){
	if(!empty($_GET["redirect"])) {
		$redirect_url = urldecode($_GET["redirect"]);
	}else{
	    $redirect_page = xt_option('frontend_login_redirect_page');
	    $redirect_url = get_permalink($redirect_page);
	    $redirect_url = empty( $redirect_page ) ? $_SERVER['REQUEST_URI'] : $redirect_url;
	    $redirect_url = apply_filters( 'frontend_login_redirect_page', $redirect_url );
    }
    $width = array(
        'default' => 565,
        'wide' => 440,
        'extra_buttons' => 666,
        'mobile' => 300
        );

    $fb_button = xt_option('enable_facebook_connect');

    if( wp_is_mobile() ) {
        $key = 'mobile';
    } else {
        $key = 'default';
    }

    $defaults = array(
        'ajaxurl' => admin_url("admin-ajax.php"),
        'redirect' => $redirect_url,
        'dialog_width' => $width[ $key ],
        'match_error' => AjaxLogin::status('passwords_do_not_match','description'),
        'is_user_logged_in' => is_user_logged_in() ? 1 : 0,
        'wp_logout_url' => wp_logout_url( site_url() ),
        'logout_text' => __('Logout', XT_TEXT_DOMAIN ),
        'close_text' => __('Close', XT_TEXT_DOMAIN )
        );

    $localized = apply_filters( 'xt_ajax_login_register_localized_js', $defaults );

    return $localized;
}


/**
 * Include our abstract which is a Class of shared Methods for our Classes.
 */
require_once 'controllers/abstract.php';


/**
 * If users are allowed to register we require the registration class
 */
require_once 'controllers/register.php';


/**
 * Load the login class
 */
require_once 'controllers/login.php';