<?php

/**
 * Terms Widget Class
 *
 * @since 1.0
 */
class XT_Widget_Categories extends XT_Widget
{
	/**
	 * Widget Defaults
	 */

	public static $widget_defaults;
	
	
	/**
	 * Register widget with WordPress.
	 */

	function __construct ()
	{
		$widget_ops = array(
			  'classname'   => 'xt_widget_terms'
			, 'description' => _x('A list or dropdown of categories / terms', 'Widget', XT_TEXT_DOMAIN)
		);

		self::$widget_defaults = array(
			  'title'        => ''
			, 'taxonomy'     => 'category'
			, 'count'        => 0
			, 'hierarchical' => 0
			, 'dropdown'     => 0
		);

		parent::__construct('xt-categories', XT_WIDGET_PREFIX . __('Categories / Terms', XT_TEXT_DOMAIN), $widget_ops);
	}

	function widget ( $args, $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );
		$this->fix_args($args);
		
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$taxonomy = $instance['taxonomy'];
		$c = $instance['count'];
		$h = $instance['hierarchical'];
		$d = $instance['dropdown'];

		echo $before_widget;
		if ( $title )
			echo sprintf( $before_title, '' ) . wp_kses_post($title) . $after_title;

		$term_args = array(
			  'taxonomy'           => $taxonomy
			, 'orderby'            => 'name'
			, 'order'              => 'ASC'
			, 'hide_empty'         => 1
			, 'show_count'         => $c
			, 'hierarchical'       => $h
			, 'title_li'           => false
			, 'depth'              => 0
			, 'style'              => 'list'
			, 'orderby'            => 'name'
			, 'use_desc_for_title' => 1
			, 'child_of'           => 0
			, 'exclude'            => ''
			, 'exclude_tree'       => ''
			, 'current_category'   => 0
		);

		$terms = get_terms( $taxonomy, array( 'orderby' => 'name', 'hierarchical' => $h ) );

		if ( $d ) :
			$term_args['show_option_none'] = __('Select Category', XT_TEXT_DOMAIN);

?>
<select id="tax-<?php echo esc_attr($taxonomy); ?>" class="terms-dropdown" name="<?php echo $this->get_field_name('taxonomy'); ?>">
	<option><?php echo $term_args['show_option_none']; ?></option>
<?php
			foreach ( $terms as $term ) {
				
				$term_link = get_term_link($term);
				
				if ( is_wp_error( $term_link ) ) {
			        continue;
			    }
    
				echo '<option value="' . $term_link . '">'. $term->name . ( $c ? ' (' . $term->count . ')' : '' ) . '</option>';
			}
?>
</select>

<script>
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$('#tax-<?php echo esc_js($taxonomy); ?>').on('change', function(){
		if ( $(this).val() != '') {
			location.href = $(this).val();
		}
	});
});	
/* ]]> */
</script>

<?php
		else :
			$taxonomy_object = get_taxonomy( $taxonomy );

			$term_args['show_option_all'] = $taxonomy_object->labels->all_items;
			$posts_page = ( 'page' == get_option('show_on_front') && get_option('page_for_posts') && $taxonomy_object->object_type[0] == 'post' ) ? get_permalink( get_option('page_for_posts') ) : get_post_type_archive_link($taxonomy_object->object_type[0]);
			$posts_page = esc_url( $posts_page );
?>
		<ul class="terms-list">
			<li><a href="<?php echo esc_url($posts_page); ?>"><?php echo $term_args['show_option_all']; ?></a></li>
<?php
			foreach ( $terms as $term ) {
				
				$term_link = get_term_link($term);
				
				if ( is_wp_error( $term_link ) ) {
			        continue;
			    }
			    
				echo '<li><a href="' . $term_link . '">' . $term->name . ( $c ? ' <small>(' . $term->count . ')</small>' : '' ) . '</a></li>';
			}
?>
		</ul>
<?php
		endif;

		echo $after_widget;
	}

	function update ( $new_instance, $old_instance )
	{
		$instance = wp_parse_args( $old_instance, self::$widget_defaults );

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		// $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

		return $instance;
	}

	function form ( $instance )
	{
		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		$title = esc_attr( $instance['title'] );
		$count = (bool) $instance['count'];
		// $hierarchical = (bool) $instance['hierarchical'];
		$dropdown = (bool) $instance['dropdown'];
		$taxonomy = esc_attr( $instance['taxonomy'] );

		# Get taxonomiues
		$taxonomies = get_taxonomies( array( 'public' => true ) );

		# If no taxonomies exists
		if ( ! $taxonomies ) {
			echo '<p>'. __('No taxonomies have been created yet.', XT_TEXT_DOMAIN) .'</p>';
			return;
		}

?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' , XT_TEXT_DOMAIN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="<?php _e('Categories', XT_TEXT_DOMAIN); ?>" /></p>

		<p>
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e( 'Select Taxonomy:' , XT_TEXT_DOMAIN); ?></label>
			<select id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>">
		<?php
			foreach ( $taxonomies as $tax ) {
				$tax = get_taxonomy($tax);
				echo '<option value="' . $tax->name . '"' . selected( $taxonomy, $tax->name, false ) . '>'. $tax->label . '</option>';
			}
		?>
			</select>
		</p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown' , XT_TEXT_DOMAIN); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' , XT_TEXT_DOMAIN); ?></label><br />
<?php /*
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' , XT_TEXT_DOMAIN); ?></label></p>
<?php
*/
	}

}

