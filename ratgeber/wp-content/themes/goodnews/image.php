<?php
the_post();
$metadata = wp_get_attachment_metadata();

if(!empty($_GET["format"]) && $_GET["format"] == "raw") {

	wp_redirect(esc_url(content_url()).'/uploads/'.$metadata['file']);
	exit;
}

get_header();
?>

<div class="row">
	<div class="medium-12">
	
		<!-- Main Content -->
		<div class="row">
			<div class="medium-12">
	
				<?php xt_post_title('h1'); ?>
				<img src="<?php echo esc_url(content_url()).'/uploads/'.$metadata['file'];?>" alt="<?php get_the_title(); ?>">	
	
			</div>
			<!-- End Main Content -->	
		</div>
	</div>	
</div>	

<?php

get_footer();