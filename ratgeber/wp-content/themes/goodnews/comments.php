<?php

/**
 * @from WordPress TwentyThirteen
 */

if ( post_password_required() )
	return;
?>

		  		
<div id="comments" class="comments-area" data-thankyou="<?php echo __(xt_option('comments_thankyou_msg'), XT_TEXT_DOMAIN);?>" data-thankyou-pending="<?php echo __(xt_option('comments_thankyou_pending_msg'), XT_TEXT_DOMAIN);?>">

	<?php
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	
	$args = array(
		'id_form'           => 'commentform',
		'id_submit'         => 'submit',
		'title_reply'       => __(xt_option('comments_title_reply'), XT_TEXT_DOMAIN),
		'title_reply_to'    => __(xt_option('comments_title_reply_to'), XT_TEXT_DOMAIN),
		'cancel_reply_link' => __(xt_option('comments_cancel_reply_link'), XT_TEXT_DOMAIN),
		'label_submit'      => __(xt_option('comments_label_submit'), XT_TEXT_DOMAIN),

		'comment_field' =>  '<p class="comment-form-comment">' . 
			'<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="'.__('Your Comment', XT_TEXT_DOMAIN).'"></textarea></p>',

		'must_log_in' => '<p class="must-log-in">' .
			sprintf(
			  __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
			  wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
			) . '</p>',

		'logged_in_as' => '<p class="logged-in-as">' .
			sprintf(
			__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),
			  admin_url( 'profile.php' ),
			  $user_identity,
			  wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
			) . '</p>',

		'comment_notes_before' => '',

		'comment_notes_after' => '',

		'fields' => apply_filters( 'comment_form_default_fields', array(

			'author' =>
			  '<p class="comment-form-author">' .
			  '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
			  '" size="30"' . $aria_req . ' placeholder="'. __( 'Your Name', XT_TEXT_DOMAIN ) . ( $req ? ' *' : '' ) .'"/></p>',

			'email' =>
			  '<p class="comment-form-email">' .
			  '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
			  '" size="30"' . $aria_req . ' placeholder="' . __( 'Your Email', XT_TEXT_DOMAIN ) . ( $req ? ' *' : '' ) . '" /> <span class="email-notes">Wird nicht veröffentlicht.</span></p>',
			  
			'url' =>
				'<p class="comment-form-url"><label for="url">' . __( 'Website', XT_TEXT_DOMAIN ) . '</label>' .
				'<input id="url" name="url" type="text" size="30" placeholder="'.__('Your Website', XT_TEXT_DOMAIN).'" value="' . esc_attr( $commenter['comment_author_url'] ) .'" /></p>',
				
		  )),
		);
	?>


	<?php comment_form($args); ?>

	
	<?php	if ( have_comments() ) : ?>
			<h3 class="comments-title">
			<?php
				$comments_count = get_comments_number(get_the_ID());
				printf( __( '%1$s comment%2$s', XT_TEXT_DOMAIN ), number_format_i18n( $comments_count ), ($comments_count > 1 ? 's' : ''));
			?></h3>
	

			<ol class="commentlist">
				<?php
					
					$comments_perpage = get_option('comments_per_page');
					$comments_pages = ceil($comments_count / $comments_perpage);
					$comments_order = strtoupper(get_option('comment_order'));
					$reverse = $comments_order == 'ASC';
				
					$args = array(
						'post_id' => get_the_ID(),
						'order'	  => $comments_order,
						'status'  => 'approve'
					);
					
					if(get_option('page_comments')) {
						$args['number'] = $comments_perpage;
					}
					$comments = get_comments($args);
			
					wp_list_comments( array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 60,
						'callback'	  => 'xt_comment',
						'reverse_top_level' => $reverse,
						'reverse_children' => $reverse
					), $comments);
				?>
			</ol><!-- .comment-list -->
		
	
	<?php	
			if ( $comments_pages > 1 && get_option('page_comments') ) : ?>
			<nav class="navigation comment-navigation" role="navigation">
				<h1 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', XT_TEXT_DOMAIN ); ?></h1>
				<a class="load-more-comments button secondary" href="#" data-postid="<?php echo esc_attr(get_the_ID());?>" data-page="1"><span class="fa fa-caret-down"></span> <?php echo __( 'Older Comments', XT_TEXT_DOMAIN ); ?></a>
			</nav>
			<!-- .comment-navigation -->
	<?php	endif; // Check for comment navigation ?>
	
	<?php	if ( ! comments_open() && get_comments_number(get_the_ID()) ) : ?>
			<p class="no-comments"><?php _e( 'Comments are closed.' , XT_TEXT_DOMAIN ); ?></p>
	<?php	endif; ?>
	
	<?php	endif; // have_comments() ?>


</div>