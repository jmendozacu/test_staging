<?php

/**
 * Markup needed for jQuery UI dialog, our form is actually loaded via AJAX
 */

?>
<div id="ajax-login-register-login-dialog" class="ajax-login-register-container" title="<?php _e('Login',XT_TEXT_DOMAIN); ?>" data-security="<?php echo wp_create_nonce( 'login_form' ); ?>" style="display:none;">
    <div id="ajax-login-register-login-target" class="ajax-login-register-login-dialog"><?php _e('Loading...',XT_TEXT_DOMAIN); ?></div>
</div>
