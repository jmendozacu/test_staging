<?php
include(locate_template('archive-settings.php'));	
?>

<?php

	if ( have_posts() ) :
		while ( have_posts() ) : 
			the_post(); 

			/*
			 * Include the post format-specific template for the content. If you want to
			 * use this in a child theme, then include a file called called content-___.php
			 * (where ___ is the post format) and that will be used instead.
			 */
			include(locate_template('parts/items/post-item.php')); 


		endwhile;
	else:
	
		include(locate_template('parts/items/post-none.php')); 
		
	endif;

?>


<?php 
	$next_link = get_next_posts_link(__('More', XT_TEXT_DOMAIN),''); 
	echo str_replace('<a ', '<a data-rel="post-list" '.implode(' ', $attr).' class="button-more '.$load_more_class.'" ', $next_link);
?>


	
