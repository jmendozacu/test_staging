<?php

/**
 * Markup needed for jQuery UI dialog, our form is actually loaded via AJAX
 */

?><div id="ajax-login-register-dialog" class="ajax-login-register-container" title="<?php _e('Register', XT_TEXT_DOMAIN); ?>" data-security="<?php echo wp_create_nonce( 'register_form' ); ?>" style="display: none;">
    <div id="ajax-login-register-target" class="ajax-login-register-dialog"><?php _e('Loading...',XT_TEXT_DOMAIN); ?></div>
</div>