<?php

$enable_frontend_login = (bool)xt_option('enable_frontend_login');
$frontend_login_type = xt_option('frontend_login_type');
$frontend_login_page = xt_option('frontend_login_page');
$frontend_login_page_url = !empty($frontend_login_page) ? get_permalink($frontend_login_page) : '#';

$placeholders = true;
if ( is_page_template( 'tpl-signup.php' ) ) {
	$frontend_login_type = 'page';
	$placeholders = false;
}

?>				

<!-- Register Modal -->
<?php if ( get_option('users_can_register') ) : ?>
    <div class="ajax-login-register-login-container">
		<div class="row collapse login-register-header">
			<div class="small-4 column">
				<h2><?php _e("Sign Up", XT_TEXT_DOMAIN); ?></h2>
	        </div>
	        <?php if($enable_frontend_login): ?>
			<div class="small-8 column">
	            <div class="login-register-action">
	            	<?php echo apply_filters( 'ajax_login_register_already_registered_text', __('Already a member?',XT_TEXT_DOMAIN) ); ?> <a href="<?php echo esc_url($frontend_login_page_url);?>" class="<?php echo ($frontend_login_type == 'lightbox') ? 'already-registered-handle' : '';?>"><?php echo apply_filters( 'ajax_login_register_already_login_text', __('Login',XT_TEXT_DOMAIN) ); ?></a>
	            </div>
	        </div>
	        <?php endif; ?>
		</div>	
	    
        <?php if ( is_user_logged_in() ) : ?>
            <div class="alert-box warning radius no-margin-bottom"><?php printf('%s <a href="%s" title="%s">%s</a>', __('You are already registered! ',XT_TEXT_DOMAIN), wp_logout_url( site_url() ), __('Logout', XT_TEXT_DOMAIN), __('Logout', XT_TEXT_DOMAIN) ); ?></div>
        <?php else : ?>
            <form action="javascript://" name="registerform" class="ajax-login-default-form-container register_form default">

                <?php if ( xt_option('enable_facebook_connect') ) : ?>
	                <div class="fb-login-container">
	                    <a href="#" class="button expand fb-login"><i class="fa fa-facebook"></i> <?php _e('Sign Up with Facebook',XT_TEXT_DOMAIN); ?></a>
	                </div>
	                <div class="row collapse">
		                <div class="small-12 column">
			                <div class="title_divider">
							  <span><?php _e("Or", XT_TEXT_DOMAIN); ?></span>
							</div>
		                </div>
	                </div>
                <?php endif; ?>

                <div class="form-wrap">
                    <?php
                    wp_nonce_field( 'facebook-nonce', 'facebook_security' );
                    wp_nonce_field( 'register_submit', 'security' );
                    ?>
                    <div class="ajax-login-register-status-container">
                        <div class="alert-box ajax-login-register-msg-target"></div>
                    </div>
	                <div class="noon">
	                
	                	<label><?php _e('Username',XT_TEXT_DOMAIN); ?></label>
	                	<div class="prefix-wrap">
							  <span class="prefix"><i class="fa fa-user"></i></span>
							  <input class="has-prefix user_login" type="text" name="login" size="30" placeholder="<?php echo ($placeholders) ? __('Username',XT_TEXT_DOMAIN) : ''; ?>" required />
						</div>
						
					</div>
					<div class="noon">
	                
	                	<label><?php _e('Email',XT_TEXT_DOMAIN); ?></label>
	                	<div class="prefix-wrap">
							  <span class="prefix"><i class="fa fa-envelope"></i></span>
							  <input class="has-prefix user_email ajax-login-register-validate-email" type="text" name="email" size="30" placeholder="<?php echo ($placeholders) ? __('Email',XT_TEXT_DOMAIN) : ''; ?>" required />
						</div>
						
					</div>
					
					<?php do_action( 'xt_ajax_login_register_below_email_field' ); ?>
					
	                <div class="noon">
	                
	                	<label><?php _e('Password',XT_TEXT_DOMAIN); ?></label>
	                	<div class="prefix-wrap">
							  <span class="prefix"><i class="fa fa-lock"></i></span>
							  <input class="has-prefix user_password" type="password" name="password" size="30" placeholder="<?php echo ($placeholders) ? __('Password',XT_TEXT_DOMAIN) : ''; ?>" required />
						</div>
						
					</div>
					<div class="noon">
	                
	                	<label><?php _e('Confirm Password',XT_TEXT_DOMAIN); ?></label>
	                	<div class="prefix-wrap">
							  <span class="prefix"><i class="fa fa-refresh"></i></span>
							  <input class="has-prefix user_confirm_password" type="password" name="confirm_password" size="30" placeholder="<?php echo ($placeholders) ? __('Confirm Password',XT_TEXT_DOMAIN) : ''; ?>" required />
						</div>
						
					</div>

                    <div class="button-container">
                    	<button class="button expand register_button" type="submit" accesskey="p" name="register"><?php _e('Sign Up',XT_TEXT_DOMAIN); ?></button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php else : ?>
    <p><?php _e('Registration is currently closed.',XT_TEXT_DOMAIN); ?></p>
<?php endif; ?>
<!-- End 'modal' -->