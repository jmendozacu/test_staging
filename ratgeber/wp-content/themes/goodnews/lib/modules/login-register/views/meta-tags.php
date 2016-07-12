<?php if ( xt_option('enable_facebook_connect') ) : ?>
    <!-- Start: Ajax Login Register Facebook meta tags -->
    <?php
    $a = New ajax_login_register_Login;
	$facebook_app_id = xt_option('facebook_appid');
	?>
	
    <?php if ( ! empty( $facebook_app_id ) ) : ?>
	    <meta property="fb:<?php echo esc_attr($facebook_app_id);?>" content="<?php echo esc_attr($facebook_app_id);?>" />
	    <meta property="og:<?php echo home_url();?>" content="<?php echo home_url();?>" />
	    <meta property="og:title" content="<?php wp_title(); ?>" />
	    <!-- End: Ajax Login Register Facebook meta tags -->
	
	    <!-- Start: Ajax Login Register Facebook script -->
	    <script type="text/javascript">
	        window.fbAsyncInit = function() {
	            FB.init({
	                appId      : <?php echo esc_js($facebook_app_id); ?>, // App ID
	                cookie     : true,  // enable cookies to allow the server to access the session
	                xfbml      : true,  // parse XFBML
	                version    : 'v2.0' // use version 2.0
	            });
	        };
	
	        // Load the SDK asynchronously
	        // This is updated as the old version went to all.js
	        (function(d, s, id) {
	            var js, fjs = d.getElementsByTagName(s)[0];
	            if (d.getElementById(id)) return;
	            js = d.createElement(s); js.id = id;
	            js.src = "//connect.facebook.net/en_US/sdk.js";
	            fjs.parentNode.insertBefore(js, fjs);
	        }(document, 'script', 'facebook-jssdk'));
	    </script>
	    <!-- End: Ajax Login Register Facebook script -->
    <?php endif; ?>
<?php endif; ?>

