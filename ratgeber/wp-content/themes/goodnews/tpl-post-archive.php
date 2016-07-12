<?php
/**
 * Template Name: Posts
 */

$post_type = 'post';
$page_id = xt_get_page_ID();
$item_template = get_field('template_layout', $page_id);

get_template_part('archive'); 
?>