<?php
/**
 * The template for displaying a "No posts found" message
 *
 * @package WordPress
 * @subpackage Good_News
 * @since Good News 1.0
 */
?>

<div class="page-content">
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

	<p class="alert-box warning"><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', XT_TEXT_DOMAIN ), admin_url( 'post-new.php' ) ); ?></p>

	<?php elseif ( is_search() ) : ?>

	<p class="alert-box warning"><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', XT_TEXT_DOMAIN ); ?></p>
	<?php get_search_form(); ?>

	<?php else : ?>

	<p class="alert-box warning"><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', XT_TEXT_DOMAIN ); ?></p>
	<?php get_search_form(); ?>

	<?php endif; ?>
</div><!-- .page-content -->
