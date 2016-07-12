<?php

$enable_frontend_register = (bool)xt_option('enable_frontend_register');
$frontend_register_type = xt_option('frontend_register_type');
$frontend_register_page = xt_option('frontend_register_page');
$frontend_register_page_url = !empty($frontend_register_page) ? get_permalink($frontend_register_page) : '#';

$placeholders = true;
if ( is_page_template( 'tpl-login.php' ) ) {
	$frontend_register_type = 'page';
	$placeholders = false;
}

?>		

<!-- Login Form -->
<div class="ajax-login-register-login-container">
	<div class="row collapse login-register-header">
		<div class="small-4 column">
			<h2><?php _e("Login", XT_TEXT_DOMAIN); ?></h2>
        </div>
        <?php if($enable_frontend_register): ?>
		<div class="small-8 column">
            <div class="login-register-action">
            	<?php echo apply_filters( 'ajax_login_not_a_member_text', __('No account yet?',XT_TEXT_DOMAIN) ); ?> <a href="<?php echo esc_url($frontend_register_page_url);?>" class="<?php echo ($frontend_register_type == 'lightbox') ? 'not-a-member-handle' : ''; ?>"><?php echo apply_filters( 'ajax_login_sign_up_text', __('Sign Up',XT_TEXT_DOMAIN) ); ?></a>
            </div>
        </div>
        <?php endif; ?>
	</div>	
    <?php if ( is_user_logged_in() ) : ?>
        <div class="alert-box warning radius no-margin-bottom"><?php printf("%s <a href=%s title='%s'>%s</a>", __('You are already logged in! ',XT_TEXT_DOMAIN), wp_logout_url( site_url() ), __('Logout',XT_TEXT_DOMAIN), __('Logout',XT_TEXT_DOMAIN) );?></div>
    <?php else : ?>
        <form action="javascript://" class="ajax-login-default-form-container login_form default">
            <?php if ( xt_option('enable_facebook_connect') && get_option('users_can_register') ) : ?>
                
                <div class="fb-login-container">
                    <a href="#" class="button expand fb-login"><i class="fa fa-facebook"></i> <?php _e('Login with Facebook',XT_TEXT_DOMAIN); ?></a>
                </div>
                <div class="row collapse">
	                <div class="small-12 column">
		                <div class="title_divider">
						  <span><?php _e("Or", XT_TEXT_DOMAIN); ?></span>
						</div>
	                </div>
                </div>
		
            <?php endif; ?>
            <div class="form-wrapper">
                <?php
                wp_nonce_field( 'facebook-nonce', 'facebook_security' );
                wp_nonce_field( 'login_submit', 'security' );
                ?>
                <div class="ajax-login-register-status-container">
                    <div class="alert-box ajax-login-register-msg-target"></div>
                </div>
                <div class="noon">
                
                	<label><?php _e('Username / Email',XT_TEXT_DOMAIN); ?></label>
                	<div class="prefix-wrap">
						  <span class="prefix"><i class="fa fa-user"></i></span>
						  <input class="has-prefix" type="text" name="user_login" size="30" placeholder="<?php echo ($placeholders) ? __('Username / Email',XT_TEXT_DOMAIN) : ''; ?>" required />
					</div>
					
				</div>
                <div class="noon">
                	<label><?php _e('Password',XT_TEXT_DOMAIN); ?></label>
                	<div class="prefix-wrap">
						 <span class="prefix"><i class="fa fa-lock"></i></span>
						 <input class="has-prefix" type="password" name="password" size="30" placeholder="<?php echo ($placeholders) ? __('Password',XT_TEXT_DOMAIN) : ''; ?>" required />
					</div>
					
				</div>

                <?php
                $keep_logged_in = (bool)xt_option('frontend_login_remember_me');
                if ( $keep_logged_in ) : ?>

				<div class="nice-checkbox">
					<input id="rememberme" type="checkbox" name="rememberme" value="rememberme">
					<label for="rememberme"><?php echo apply_filters( 'ajax_login_remember_me_text', __('Remember me',XT_TEXT_DOMAIN) ); ?></label>

				</div>
	
                <?php endif; ?>
                <span class="meta right"><a href="<?php echo wp_lostpassword_url(); ?>" title="<?php _e('I forgot my password',XT_TEXT_DOMAIN ); ?>"><?php _e('I forgot my password',XT_TEXT_DOMAIN ); ?></a></span>
                <div class="button-container">
                    <button class="button expand login_button" type="submit" accesskey="p" name="submit"><?php _e('Login',XT_TEXT_DOMAIN); ?></button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<!-- End Login Form -->