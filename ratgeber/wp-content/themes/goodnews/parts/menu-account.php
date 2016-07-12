<?php
$enable_frontend_login = (bool)xt_option('enable_frontend_login');
$frontend_login_label = xt_option('frontend_login_label');
$frontend_login_type = xt_option('frontend_login_type');
$frontend_login_page = xt_option('frontend_login_page');
$frontend_login_page_url = !empty($frontend_login_page) ? get_permalink($frontend_login_page) : '#';

$enable_frontend_register = (bool)xt_option('enable_frontend_register');
$frontend_register_label = xt_option('frontend_register_label');
$frontend_register_type = xt_option('frontend_register_type');
$frontend_register_page = xt_option('frontend_register_page');
$frontend_register_page_url = !empty($frontend_register_page) ? get_permalink($frontend_register_page) : '#';

$show_profile_link = (bool)xt_option('account_menu_show_profile_link');

?>
	
<?php if($enable_frontend_login || $enable_frontend_register): ?>
<ul class="account-menu right">

	<?php if(!is_user_logged_in()): ?>
	
		<?php if($enable_frontend_login): ?>
		<li><a href="<?php echo esc_url($frontend_login_page_url); ?>" class="<?php echo ($frontend_login_type == 'lightbox') ? 'already-registered-handle' : '' ?>"><?php echo wp_kses_post($frontend_login_label); ?></a></li>
		<?php endif; ?>
		
		<?php if($enable_frontend_register): ?> 
		<li><a href="<?php echo esc_url($frontend_register_page_url); ?>" class="button <?php echo ($frontend_register_type == 'lightbox') ? 'not-a-member-handle' : '' ?>"><?php echo wp_kses_post($frontend_register_label); ?></a></li>
		<?php endif; ?>
	
		<li class="space-if-flat"></li>
		
		<?php get_template_part('parts/menu', 'shop'); ?>
		
	<?php else: ?>
	
		<?php
		global $current_user;
	    get_currentuserinfo();
	    
	    $userid = $current_user->ID;
	    $username = $current_user->user_login;
		$fname = $current_user->user_firstname;
		$lname = $current_user->user_lastname;
		
		if(!empty($fname)){
			$display_name = $fname;
			$short_name = $fname;
			
			if( !empty($lname) ) {
				$display_name .= '<br>'.$lname;
			}
		}else{
			$display_name = $username;
			$short_name = $username;
		}
		
	    ?>
		<li class="flat canhover has-dropdown show-for-medium-up">
			<a href="#"><strong><?php echo wp_kses_post($short_name);?></strong> &nbsp; <i class="fa fa-caret-down"></i></a>
			<ul class="dropdown animate-drop open-top">
			
				<li class="user-info">
					<a href="<?php echo ($show_profile_link) ? get_edit_user_link() : '#'; ?>">
						<?php echo get_avatar($userid, 65, null, $display_name);?>
						<span class="user-name"><?php echo wp_kses_post($display_name);?></span>
					</a>
				</li>
				
				<li class="has-submenu divide">
				
					<ul class="submenu">
						<?php if($show_profile_link): ?>
						<li><a href="<?php echo esc_url(get_edit_user_link()); ?>"><?php echo __("My Profile", XT_TEXT_DOMAIN); ?></a></li>
						<?php endif; ?>
						
						<?php if (function_exists('is_plugin_active') && is_plugin_active('woocommerce/woocommerce.php')) : ?>
						<li><a href="<?php echo esc_url(get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>"><?php echo __("My Account", XT_TEXT_DOMAIN); ?></a></li>
						<?php endif; ?>
						
						<li><a class="button secondary" href="<?php echo wp_logout_url(home_url('/')); ?>"><?php echo __("Logout", XT_TEXT_DOMAIN); ?></a></li>
					</ul>
					
				</li>
			</ul>
		</li>
		
			
		<li class="show-for-small-only user-info">
			<a class="no-border" href="<?php echo ($show_profile_link) ? get_edit_user_link() : '#'; ?>">
				<?php echo get_avatar($userid, 85, null, $display_name);?> &nbsp;
				<span class="user-name"><?php echo wp_kses_post(str_replace('<br>', ' ', $display_name));?></span>
			</a>
		</li>
		
		<?php if($show_profile_link): ?>
		<li class="show-for-small-only"><a href="<?php echo esc_url(get_edit_user_link()); ?>"><?php echo __("My Profile", XT_TEXT_DOMAIN); ?></a></li>
		<?php endif; ?>
		
		<?php if (function_exists('is_plugin_active') && is_plugin_active('woocommerce/woocommerce.php')) : ?>
		<li class="show-for-small-only"><a href="<?php echo esc_url(get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>"><?php echo __("My Account", XT_TEXT_DOMAIN); ?></a></li>
		<?php endif; ?>

		<?php get_template_part('parts/menu', 'shop'); ?>
		
		<li class="show-for-small-only"><a class="button" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><?php echo __("Logout", XT_TEXT_DOMAIN); ?></a></li>

		
	<?php endif; ?>	
		
</ul>
<?php endif; ?>
