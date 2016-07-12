<?php

function xt_advms_init ()
{

	wp_register_style('jquery.multi-select', XT_MOD_URL . '/advanced-multiselect/assets/css/multi-select.css', '');

	wp_register_script('jquery.multi-select', XT_MOD_URL . '/advanced-multiselect/assets/js/jquery.multi-select.js', '', false, true);
	wp_register_script('jquery.quicksearch', XT_MOD_URL . '/advanced-multiselect/assets/js/jquery.quicksearch.js', '', false, true);
	wp_register_script('advms', XT_MOD_URL . '/advanced-multiselect/assets/js/init.js', '', false, true);

}

add_action('init', 'xt_advms_init');


function xt_advms_assets ()
{
	wp_enqueue_style('jquery.multi-select');
	wp_enqueue_script('jquery.multi-select');
	wp_enqueue_script('jquery.quicksearch');
	wp_enqueue_script('advms');

	wp_localize_script('advms', 'advms_vars', array(
		'is_admin' => is_admin()
	));		
}

add_action('wp_enqueue_scripts', 'xt_advms_assets', 1);
add_action('admin_enqueue_scripts', 'xt_advms_assets', 1);
