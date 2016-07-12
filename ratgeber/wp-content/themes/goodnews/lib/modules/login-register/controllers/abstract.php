<?php

/**
 * Our abstract class for zM Ajax Login.
 *
 * This class is designed to reduce and factor out the shared details between our classes.
 * Thus allowing us to focus on as few concepts at a time.
 */
abstract Class AjaxLogin {

    public $scripts = array();

    /**
     * WordPress hooks to be ran during init
     */
    public function __construct(){

        add_action( 'wp_head', array( &$this, 'header' ) );

        add_action( 'wp_ajax_nopriv_validate_email', array( &$this, 'validate_email' ) );
        add_action( 'wp_ajax_validate_email', array( &$this, 'validate_email' ) );

        add_action( 'wp_ajax_nopriv_validate_username', array( &$this, 'validate_username' ) );
        add_action( 'wp_ajax_validate_username', array( &$this, 'validate_username' ) );

        add_action( 'wp_ajax_nopriv_load_template', array( &$this, 'load_template' ) );
        add_action( 'wp_ajax_load_template', array( &$this, 'load_template' ) );
    }


    /**
     * Any additional code to be ran during wp_head
     *
     * Prints the ajaxurl in the html header.
     * Prints the meta tags template.
     */
    public function header(){
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . "views/meta-tags.php" );
    }


    /**
     * Check if an email is "valid" using PHPs filter_var & WordPress
     * email_exists();
     *
     * @param $email (string) Emailt to be validated
     * @param $is_ajax (bool)
     * @todo check ajax refer
     */
    public function validate_email( $email=null, $is_ajax=true ) {

        $email = is_null( $email ) ? $email : $_POST['email'];

        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $msg = $this->status('email_invalid');
        } else if ( email_exists( $email ) ){
            $msg = $this->status('email_in_use');
        } else {
            $msg = $this->status('email_valid');
        }

        if ( $is_ajax ){
            print json_encode( $msg );
            die();
        } else {
            return $msg;
        }
    }


    /**
     * Process request to pass variables into WordPress' validate_username();
     *
     * @uses validate_username()
     * @param $username (string)
     * @param $is_ajax (bool) Process as an AJAX request or not.
     */
    public function validate_username( $username=null, $is_ajax=true ) {

        $username = empty( $_POST['login'] ) ? esc_attr( $username ) : esc_attr($_POST['login']);

        if ( validate_username( $username ) ) {
            $user_id = username_exists( $username );
            if ( $user_id ){
                $msg = $this->status('username_exists');
            } else {
                $msg = $this->status('valid_username');
            }
        } else {

            $msg = $this->status('invalid_username');
        }

        if ( $is_ajax ){
            wp_send_json( $msg );
        } else {
            return $msg;
        }
    }


    /**
     * Load the login form via an AJAX request.
     *
     * @package AJAX
     */
    public function load_template(){
        check_ajax_referer( $_POST['referer'],'security');
        load_template( plugin_dir_path( dirname( __FILE__ ) ) . "views/" . $_POST['template'] . '.php' );
        die();
    }


    /**
     * Validation status responses
     */
    static function status( $key=null, $value=null ){

        $status = array(

            'valid_username' => array(
                'description' => null,
                'cssClass' => 'success',
                'code' => 'success'
                ),
            'username_exists' => array(
                'description' => __('Invalid username', XT_TEXT_DOMAIN),
                'cssClass' => 'error',
                'code' => 'error'
                ),
            'invalid_username' => array(
                'description' => __( 'Invalid username', XT_TEXT_DOMAIN ),
                'cssClass' => 'error',
                'code' => 'error'
                ),
            'username_does_not_exists' => array(
                'description' => __( 'Invalid username', XT_TEXT_DOMAIN ),
                'cssClass' => 'error',
                'code' => 'error'
                ),

            'incorrect_password' => array(
                'description' => __( 'Invalid', XT_TEXT_DOMAIN ),
                'cssClass' => 'error',
                'code' => 'error'
                ),
            'empty_password' => array(
                'description' => __( 'Empty Password', XT_TEXT_DOMAIN ),
                'cssClass' => 'error',
                'code' => 'error'
                ),    
            'passwords_do_not_match' => array(
                'description' => __('Passwords do not match.',XT_TEXT_DOMAIN),
                'cssClass' =>'error',
                'code' => 'error'
                ),

            'email_valid' => array(
                'description' => null,
                'cssClass' => 'success',
                'code' => 'success'
                ),
            'email_invalid' => array(
                'description' => __( 'Invalid Email', XT_TEXT_DOMAIN ),
                'cssClass' => 'error',
                'code' => 'error'
                ),
            'email_in_use' => array(
                'description' => __( 'Invalid Email', XT_TEXT_DOMAIN ),
                'cssClass' => 'error',
                'code' => 'error'
                ),

            'success_login' => array(
                'description' => __( 'Success! One moment while we log you in...', XT_TEXT_DOMAIN ),
                'cssClass' => 'success',
                'code' => 'success_login'
                ),
            'success_registration' => array(
                'description' => __( 'Success! One moment while we log you in...', XT_TEXT_DOMAIN ),
                'cssClass' => 'success',
                'code' => 'success_registration'
                )
            );

        $status = apply_filters( 'ajax_login_register_status_codes', $status );

        if ( empty( $value ) ){
            return $status[ $key ];
        } else {
            return $status[ $key ][ $value ];
        }
    }
}