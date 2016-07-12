<?php

/* Return option page data */

class XT_Likes{
    
    /**
     * Setup the Environment for the Plugin
     */
    function __construct() {


        add_action('wp_enqueue_scripts', array(&$this,'init'), 1);
        add_action('init', array(&$this, 'vote'));
        
        add_action('wp_head', array(&$this, 'inline_styles'));
        add_action('wp_head', array(&$this, 'scripts'));

        // Add the users custom CSS if they wish to add any.
        if ( xt_option('likes_custom-css') ) {
            add_action( 'wp_head', 'custom_styles' );
        }
                
        add_filter('the_content', array(&$this, 'add_to_content'), 100);
        
		add_action('manage_posts_custom_column', array(&$this, 'manage_post_columns'), 10, 2 );
		add_action('pre_get_posts', array(&$this, 'order_by_votes' ));
        
        add_filter('manage_posts_columns' , array(&$this, 'add_votes_columns'));
        
		add_action('wp_ajax_xt_likes_reset_all',  array(&$this, 'xt_likes_reset_all'));
	

		add_action( 'wp_ajax_refresh_likes', array(&$this, 'refresh_likes'));
		add_action( 'wp_ajax_nopriv_refresh_likes', array(&$this, 'refresh_likes'));

    }
    
    function init(){
        
        wp_register_style('xt_likes_frontend_styles', XT_MOD_URL.'/likes/css/xt_likes.css', array());
        wp_register_script('xt_likes_js', XT_MOD_URL.'/likes/js/xt_likes.js', array('jquery'), false, true);

        if ( !is_admin() ) {
            wp_enqueue_style('xt_likes_frontend_styles');
            wp_enqueue_script('xt_likes_js');
        } else {
            wp_enqueue_style('xt_likes_admin_styles');
        }
    }
    

	
	
	function manage_post_columns( $column, $post_id ) {
	    global $post;
	
	    switch( $column ) {
	
	        case 'likes' :
	
	            $likes = get_post_meta( $post_id, '_votes_likes', true );
	            if ( empty( $likes ) )
	                echo '—';
	            else
	                echo esc_html($likes);
	            break;
	
	        case 'dislikes' :
	
	            $dislikes = get_post_meta( $post_id, '_votes_dislikes', true );
	            if ( empty( $dislikes ) )
	                echo '—';
	            else
	                echo esc_html($dislikes);
	            break;
	
	        default :
	            break;
	    }
	}
	
	
	function order_by_votes( $query ) {
	    if( ! is_admin() )
	        return;
	
	    $orderby = $query->get( 'orderby');
	
	    if( '_votes_likes' == $orderby ) {
	        $query->set('meta_key','_votes_likes');
	        $query->set('orderby','meta_value_num');
	    }
	
	    if( '_votes_dislikes' == $orderby ) {
	        $query->set('meta_key','_votes_dislikes');
	        $query->set('orderby','meta_value_num');
	    }
	
	}

    
    /* add vote columns to posts */
	function add_votes_columns($columns) {
		
		$like_cols = array(

			'likes' => '<a href="' . admin_url('edit.php?orderby=_votes_likes&order=desc') . '">' . __('Likes', XT_TEXT_DOMAIN) . '</a>', 
			'dislikes' => '<a href="' . admin_url('edit.php?orderby=_votes_dislikes&order=desc') . '">' . __('Dislikes', XT_TEXT_DOMAIN) . '</a>' 
		);

		if ( function_exists('xt_array_insert') )
			$columns = xt_array_insert($columns, $like_cols, 'date', 'before');
		else
			$columns = array_merge($columns, $like_cols);
			
			
	    return $columns;
	}


	/*-----------------------------------------------------------------------------------*/
	/* Options CSS */
	/*-----------------------------------------------------------------------------------*/
	
	function inline_styles() {
	
	
	    echo '<style type="text/css" id="voting-style-css">' . "\n";

		$likes_bgcolor = xt_option('likes_likes_bgcolor');
		$likes_color = xt_option('likes_likes_color');
		$dislikes_bgcolor = xt_option('likes_dislikes_bgcolor');
		$dislikes_color = xt_option('likes_dislikes_color');
		
		$likes_position = xt_option('likes_position');
		
		if(is_rtl() && $likes_position != 'center') {
			
			if($likes_position == 'left') {
				$likes_position = 'right';
			}else{
				$likes_position = 'left';
			}
		}	
		
		$likes_font_size = xt_option('likes_font_size');
		
		
		
	    if ( $likes_bgcolor ) {
	        echo '.xt-votes .xt-likes { background-color: ' . sanitize_text_field( $likes_bgcolor) . "!important}\n" ;
	    }
	    if ( $likes_color ) {
	    	echo '.xt-votes .xt-likes{ color: ' . sanitize_text_field( $likes_color) . "!important}\n" ;
	        echo '.xt-votes .xt-likes a{ color: ' . sanitize_text_field( $likes_color) . "!important}\n" ;
	    }
	    
	    if ( $dislikes_bgcolor ) {
	        echo '.xt-votes .xt-dislikes { background-color: ' . sanitize_text_field( $dislikes_bgcolor) . "!important}\n" ;
	    }
	    if ( $dislikes_color ) {
	        echo '.xt-votes .xt-dislikes{ color: ' . sanitize_text_field( $dislikes_color) . "!important}\n" ;
	        echo '.xt-votes .xt-dislikes a{ color: ' . sanitize_text_field( $dislikes_color) . "!important}\n" ;
	    }
	    
	    if ( $likes_position ) {
	        echo '.xt-votes { text-align: ' . sanitize_text_field( $likes_position ) . "!important}\n" ;
	    }
	    if ( $likes_font_size ) {
	        echo '.xt-likes, .xt-dislikes, .xt-votes i { font-size: ' . sanitize_text_field( $likes_font_size ) . "!important}\n" ;
	    }
	       
	    echo "</style>\n";
	}
	
	function custom_styles() {
	
	    echo '<style text="text/css" id="voting-custom-css">' . "\n" . sanitize_text_field( xt_option('likes_custom-css') ) . "\n</style>\n";
	}
	
	function add_to_content($content) {

		$post_type = get_post_type();

		$xt_likes_enabled = (bool)xt_option('likes_enabled');
		
	    if ( $post_type == 'post' && is_singular($post_type) && $xt_likes_enabled ) {
	        $content .= $this->votes();
	    }
	    return $content;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* Voting */
	/*-----------------------------------------------------------------------------------*/
	
	function votes($is_ajax = FALSE) {
	
		global $post;
		
		if(!empty($post)){
			$post = get_post();
		}
		
	    $post_type = get_post_type();
	    
		$xt_likes_enabled = (bool)xt_option('likes_enabled');

		if(empty($xt_likes_enabled))
			return false;
			
			
	    $votes_like = (int) get_post_meta($post->ID, '_votes_likes', true);
	    $votes_dislike = (int) get_post_meta($post->ID, '_votes_dislikes', true);
	    
	    if (xt_option('likes_vote_like_link')) {
	        $vote_like_link = xt_option('likes_vote_like_link');
	    } else {
	        $vote_like_link = __("I found this helpful", XT_TEXT_DOMAIN);
	    }
	    if (xt_option('likes_vote_dislike_link')) {
	        $vote_dislike_link = xt_option('likes_vote_dislike_link');
	    } else {
	        $vote_dislike_link = __("I did not find this helpful", XT_TEXT_DOMAIN);
	    }
	
	    if (xt_option('likes_voted_like_single')) {
	        $voted_like_single = xt_option('likes_voted_like_single');
	    } else {
	        $voted_like_single = __("person found this helpful", XT_TEXT_DOMAIN);
	    }
	    if (xt_option('likes_voted_dislike_single')) {
	        $voted_dislike_single = xt_option('likes_voted_dislike_single');
	    } else {
	        $voted_dislike_single = __("person did not find this helpful", XT_TEXT_DOMAIN);
	    }
	
	    if (xt_option('likes_voted_like_plural')) {
	        $voted_like_plural = xt_option('likes_voted_like_plural');
	    } else {
	        $voted_like_plural = __("people found this helpful", XT_TEXT_DOMAIN);
	    }
	    if (xt_option('likes_voted_dislike_plural')) {
	        $voted_dislike_plural = xt_option('likes_voted_dislike_plural');
	    } else {
	        $voted_dislike_plural = __("people did not find this helpful", XT_TEXT_DOMAIN);
	    }
	
	    $voted_like = sprintf(_n('%s ' . $voted_like_single, '%s ' . $voted_like_plural, $votes_like, XT_TEXT_DOMAIN), $votes_like);
	    
	    $voted_dislike  = sprintf(_n('%s ' . $voted_dislike_single, '%s ' . $voted_dislike_plural, $votes_dislike, XT_TEXT_DOMAIN), $votes_dislike);
	
	    $cookie_vote_count      = '';
	    if (xt_option('likes_icon') == 'thumbs') {
	    
	        $like_icon 		= '<span class="fa fa-thumbs-o-up"></span> ';
	        $dislike_icon 	= '<span class="fa fa-thumbs-o-down"></span> ';
	        
	    } elseif (xt_option('likes_icon') == 'angle') {
	    
	        $like_icon 		= '<span class="fa fa-angle-up"></span> ';
	        $dislike_icon 	= '<span class="fa fa-angle-down"></span> ';
	        
	    } elseif (xt_option('likes_icon') == 'caret') {
	    
	        $like_icon 		= '<span class="fa fa-caret-up"></span> ';
	        $dislike_icon 	= '<span class="fa fa-caret-down"></span> ';
	        
	    } else {
	    
	        $like_icon = '';
	        $dislike_icon = '';
	    }
	
	    if(isset($_COOKIE['vote_count'])){
	        $cookie_vote_count = @unserialize($_COOKIE['vote_count']);
	    }
	    
	    if(!is_array($cookie_vote_count) && isset($cookie_vote_count)){
	        $cookie_vote_count = array();
	    }
	   
	    $html = (($is_ajax)?'':'<div class="xt-votes" data-postid="'.$post->ID.'">');
	                            
	    if (is_user_logged_in() || xt_option('likes_voting') == 1 ) :
	        
	            if(is_user_logged_in())
	                $vote_count = (array) get_user_meta(get_current_user_id(), 'vote_count', true);
	            else
	                $vote_count = $cookie_vote_count;
	            
	            if (!in_array( $post->ID, $vote_count )) :
	
	                    $html .= '<p class="xt-likes"><a class="xt-like_btn" href="javascript:" data-postid="' . $post->ID . '">'. $like_icon . $vote_like_link . '</a></p>';
	                    $html .= '<p class="xt-dislikes"><a class="xt-dislike_btn" href="javascript:" data-postid="' . $post->ID . '">' . $dislike_icon . $vote_dislike_link . '</a></p>';
	
	            else :
	                    // already voted
	                    $html .= '<p class="xt-likes">'. $like_icon  . $voted_like . '</p> ';
	                    $html .= '<p class="xt-dislikes">' . $dislike_icon . $voted_dislike . '</p> ';
	            endif;
	       
	    else :
	            // not logged in
	            $html .= '<p class="xt-likes">'. $like_icon  . $voted_like . '</p> ';
	            $html .= '<p class="xt-dislikes">' . $dislike_icon . $voted_dislike . '</p> ';
	    endif;
	    
	    $html .= (($is_ajax)?'':'</div>');
		
		
		if ( $is_ajax ) {
			die($html);
		} else {
			return ($html);
		}
	}
	
	function refresh_likes() {
		
		global $post;
		
		$post_id = intVal($_GET['post_id']);
		$post = get_post($post_id);
		
		$this->votes(true);
	}
	
	function vote() {
	    global $post;
	
	    if (is_user_logged_in()) {
	        
	        $vote_count = (array) get_user_meta(get_current_user_id(), 'vote_count', true);
	        
	        if (isset( $_GET['xt-vote_like'] ) && intval($_GET['xt-vote_like']) > 0) :
	                
	                $post_id = intval($_GET['xt-vote_like']);
	                $the_post = get_post($post_id);
	                
	                if ($the_post && !in_array( $post_id, $vote_count )) :
	                        $vote_count[] = $post_id;
	                        update_user_meta(get_current_user_id(), 'vote_count', $vote_count);
	                        $post_votes = (int) get_post_meta($post_id, '_votes_likes', true);
	                        $post_votes++;
	                        update_post_meta($post_id, '_votes_likes', $post_votes);
	                        $post = get_post($post_id);
	                        $this->votes(true);
	                        die('');
	                endif;
	                
	        elseif (isset( $_GET['xt-vote_dislike'] ) && intVal($_GET['xt-vote_dislike']) > 0) :
	                
	                $post_id = intVal($_GET['xt-vote_dislike']);
	                $the_post = get_post($post_id);
	                
	                if ($the_post && !in_array( $post_id, $vote_count )) :
	                        $vote_count[] = $post_id;
	                        update_user_meta(get_current_user_id(), 'vote_count', $vote_count);
	                        $post_votes = (int) get_post_meta($post_id, '_votes_dislikes', true);
	                        $post_votes++;
	                        update_post_meta($post_id, '_votes_dislikes', $post_votes);
	                        $post = get_post($post_id);
	                        $this->votes(true);
	                        die('');
	                        
	                endif;
	                
	        endif;
	
	    } elseif (!is_user_logged_in() && xt_option('likes_voting') == 1) {
	
	        // ADD VOTING FOR NON LOGGED IN USERS USING COOKIE TO STOP REPEAT VOTING ON AN ARTICLE
	        $vote_count = '';
	        
	        if(isset($_COOKIE['vote_count'])){
	            $vote_count = @unserialize($_COOKIE['vote_count']);
	        }
	        
	        if(!is_array($vote_count) && isset($vote_count)){
	            $vote_count = array();
	        }
	        
	        if (isset( $_GET['xt-vote_like'] ) && intVal($_GET['xt-vote_like']) > 0) :
	                
	                $post_id = intVal($_GET['xt-vote_like']);
	                $the_post = get_post($post_id);
	                
	                if ($the_post && !in_array( $post_id, $vote_count )) :
	                        $vote_count[] = $post_id;
	                        $_COOKIE['vote_count']  = serialize($vote_count);
	                        setcookie('vote_count', $_COOKIE['vote_count'] , time()+(10*365*24*60*60),'/');
	                        $post_votes = (int) get_post_meta($post_id, '_votes_likes', true);
	                        $post_votes++;
	                        update_post_meta($post_id, '_votes_likes', $post_votes);
	                        $post = get_post($post_id);
	                        $this->votes(true);
	                        die('');
	                endif;
	                
	        elseif (isset( $_GET['xt-vote_dislike'] ) && intVal($_GET['xt-vote_dislike']) > 0) :
	                
	                $post_id = intVal($_GET['xt-vote_dislike']);
	                $the_post = get_post($post_id);
	                
	                if ($the_post && !in_array( $post_id, $vote_count )) :
	                        $vote_count[] = $post_id;
	                        $_COOKIE['vote_count']  = serialize($vote_count);
	                        setcookie('vote_count', $_COOKIE['vote_count'] , time()+(10*365*24*60*60),'/');
	                        $post_votes = (int) get_post_meta($post_id, '_votes_dislikes', true);
	                        $post_votes++;
	                        update_post_meta($post_id, '_votes_dislikes', $post_votes);
	                        $post = get_post($post_id);
	                        $this->votes(true);
	                        die('');
	                        
	                endif;
	                
	        endif;
	
	    } elseif (!is_user_logged_in() && xt_option('likes_voting') == 2) {
	
	        return;
	        
	    }
	        
	}

	
	function scripts(){
	    $xt_likes = array(
	        'base_url'  => esc_url(home_url()),
	        'ajax_url'  => esc_url(admin_url('admin-ajax.php'))
	    );
	    ?>
		<script type="text/javascript">
	    	XT_LIKES = <?php echo json_encode($xt_likes); ?>;
		</script>
	    <?php
	}


	function xt_likes_reset_all(){
		check_ajax_referer( 'xt-likes-reset-all', 'security' );
		global $wpdb;
		// reset postmeta fields
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = %d WHERE meta_key = %s", 0, '_votes_likes' ) );
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = %d WHERE meta_key = %s", 0, '_votes_dislikes' ) );
		
		// reset usermeta fields
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE meta_key = %s", 'vote_count' ) );
		
		die(__("All votes have been reset", XT_TEXT_DOMAIN));
	}
	
	    
}

$XT_Likes = new XT_Likes();

