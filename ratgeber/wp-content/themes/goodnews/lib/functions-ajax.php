<?php
	
	
function xt_ajax_get_posts() {

	global $post, $tab_post_category, $tab_post_author, $tab_post_date, $tab_post_stats;

	$tab = array();
	if(!empty($_POST["settings"])) {
		$tab = unserialize(xt_64_decode($_POST["settings"]));
	}
		
	$cpage = 1;
	if(!empty($_POST["cpage"])) {
		$cpage = filter_input(INPUT_POST, 'cpage', FILTER_VALIDATE_INT);
	}
	
	$direction = 'prev';
	if(!empty($_POST["direction"])) {
		$direction = sanitize_text_field($_POST["direction"]);
	}
	
	
	$per_page = $tab['posts_per_page'];
	$query = $tab['query'];
	$query_post_formats = $tab['query_post_formats'];
	$format = $tab['format'];
	
	$include_posts = false;
	if($query == 'selection') {
		$include_posts = $tab['include_posts'];
	}	
	

	$tab_post_category = $tab['show_post_category'];
	$tab_post_author = $tab['show_post_author'];
	$tab_post_date = $tab['show_post_date'];
	$tab_post_stats = $tab['show_post_stats'];

	
	$posts = xt_get_posts(array(
		'direction' => 'prev',
		'per_page' => $per_page,
		'query' => $query,
		'query_post_formats' => $query_post_formats,
		'format' => $format,
		'include_posts' => $include_posts,
		'cpage' => $cpage
	));

	$result = array();

	ob_start();
	foreach ($posts as $post) {
		setup_postdata($post);
		include(locate_template('parts/items/post-tab-item.php'));  
	}	
	
	$result[$direction."_tab_items"] = ob_get_contents();
    ob_end_clean();

    
    ob_start();
	
	foreach ($posts as $post) {
		setup_postdata($post);
		include(locate_template('parts/items/post-full.php')); 
	}
	
	$result[$direction."_content_items"] = ob_get_contents();	
	ob_end_clean();

	die(json_encode($result));									
}
add_action( 'wp_ajax_xt_ajax_get_posts', 'xt_ajax_get_posts' );
add_action( 'wp_ajax_nopriv_xt_ajax_get_posts', 'xt_ajax_get_posts' );



function xt_ajax_comment($comment_ID, $comment_status) {

	// If it's an AJAX-submitted comment
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		
		
		if ( 'spam' !== $comment_status ) { // If it's spam save it silently for later crunching
			if ( '0' == $comment_status ) {
				wp_notify_moderator( $comment_ID );
			}
			// wp_notify_postauthor() checks if notifying the author of their own comment.
			// By default, it won't, but filters can override this.
			if ( get_option( 'comments_notify' ) && $comment_status ) {
				wp_notify_postauthor( $comment_ID );
			}
		}

		if(!empty($_POST["comment_post_ID"])) {
			$post_id = filter_input(INPUT_POST, 'comment_post_ID', FILTER_VALIDATE_INT);
			$comments_order = strtoupper(get_option('comment_order'));
			$reverse = $comments_order == 'ASC';

			$args = array(
				'post_id' => $post_id,
				'order'	  => $comments_order,
				'status'  => 'approve'
			);
			
			if(get_option('page_comments')) {
				$args['number'] = get_option('comments_per_page');
			}
			
			$comments = get_comments($args);
					
			$comments_list = wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 60,
				'callback'	  => 'xt_comment',
				'echo'		  => false,
				'reverse_top_level' => $reverse,
				'reverse_children' => $reverse
				
			), $comments);
		}	
		
		// Kill the script, returning the comment HTML
		die(json_encode(array('id' => $comment_ID, 'status' => $comment_status, 'list'=>$comments_list)));
	}
}
add_action('comment_post', 'xt_ajax_comment', 20, 2);


function xt_ajax_comments() {

 	global $withcomments, $wp_query, $comment_alt, $comment_depth, $comment_thread_alt, $overridden_cpage, $in_comment_loop;
 	
 	$withcomments = true;
 
	if(!empty($_POST["post_id"])) {
		$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
		$page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT);

		$comments_perpage = get_option('comments_per_page');
		$comments_order = strtoupper(get_option('comment_order'));
		$reverse = $comments_order == 'ASC';

		$offset = $page * $comments_perpage;
		
		$args = array(
			'post_id' => $post_id,
			'order'	  => $comments_order,
			'status'  => 'approve',
			'offset'  => $offset,
			'reverse_top_level' => $reverse,
			'reverse_children' => $reverse
		);
		
		if(get_option('page_comments')) {
			$args['number'] = $comments_perpage;
		}
		
		$comments = get_comments($args);
			
		wp_list_comments( array(
			'style'       => 'ol',
			'short_ping'  => true,
			'avatar_size' => 60,
			'callback'	  => 'xt_comment'
		), $comments);
	}			
	die();
	
}
add_action( 'wp_ajax_xt_ajax_comments', 'xt_ajax_comments' );
add_action( 'wp_ajax_nopriv_xt_ajax_comments', 'xt_ajax_comments' );