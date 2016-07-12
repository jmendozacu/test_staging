<?php
$show_nav_links = false;

if(is_singular('post') && !xt_is_endless_template()) {

	$show_nav_links = (int)xt_option('show_post_nav_links');
}
	
if(!empty($show_nav_links)) {

	$class = ($show_nav_links == 2) ? 'show-on-scroll' : 'show-always';
	xt_post_nav($class);

}