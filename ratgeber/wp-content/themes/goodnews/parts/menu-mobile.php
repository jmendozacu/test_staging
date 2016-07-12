<?php 
$ubermenu_mobile_menu = xt_ubermenu_location_enabled('main-mobile-menu');
$social_networks_mobile_enabled = (bool)xt_option('topbar-mobile-menu-search');
$social_networks_mobile_enabled = (bool)xt_option('topbar-mobile-menu-followus');


// Mobile Nav Menu

if($ubermenu_mobile_menu) {
	echo '<div class="show-for-small-only clearfix has-ubermenu">';
}

echo xt_get_nav_menu('main-mobile-menu', array( 
	'menu_class' => 'left menu show-for-small-only clearfix', 
	'container' => false,
));

if($ubermenu_mobile_menu) {
	echo '</div>';
}	


if(!empty($social_networks_mobile_enabled)):
?>
<!-- Mobile Search Section -->
<ul class="search show-for-small-only clearfix">
    <li class="has-form">
    	<?php get_template_part('parts/searchform'); ?>
	</li>
</ul>
<?php endif; ?>



<?php if(!empty($social_networks_mobile_enabled)): ?>

<!-- Mobile social Networks Section -->
<div class="show-for-small-only clearfix">
	<?php get_template_part('parts/networks'); ?>
</div>

<?php endif; ?>
