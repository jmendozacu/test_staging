<?php

class acf_field_relationship_formats extends acf_field
{
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'relationship_formats';
		$this->label = __("Relationship Formats",'acf');
		$this->category = __("Relational",'acf');
		$this->defaults = array(
			'post_type'			=>	array('all'),
			'max' 				=>	'',
			'taxonomy' 			=>	array('all'),
			'filters'			=>	array('search'),
			'result_elements' 	=>	array('post_title', 'post_type', 'post_format'),
			'return_format'		=>	'object'
		);
		$this->l10n = array(
			'max'		=> __("Maximum values reached ( {max} values )",'acf'),
			'tmpl_li'	=> '
							<li>
								<a href="#" data-post_id="<%= post_id %>"><%= title %><span class="acf-button-remove"></span></a>
								<input type="hidden" name="<%= name %>[]" value="<%= post_id %>" />
							</li>
							'
		);

    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.1.0'
		);
		
				
		parent::__construct();

    	// extra
		add_action('wp_ajax_acf/fields/relationship_formats/query_posts', array($this, 'query_posts'));
		add_action('wp_ajax_nopriv_acf/fields/relationship_formats/query_posts', array($this, 'query_posts'));
	}
	
	
	/*
	*  load_field()
	*  
	*  This filter is appied to the $field after it is loaded from the database
	*  
	*  @type filter
	*  @since 3.6
	*  @date 23/01/13
	*  
	*  @param $field - the field array holding all the field options
	*  
	*  @return $field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// validate post_type
		if( !$field['post_type'] || !is_array($field['post_type']) || in_array('', $field['post_type']) )
		{
			$field['post_type'] = array( 'all' );
		}

		// validate post_type
		if( !$field['post_format'] || !is_array($field['post_format']) || in_array('', $field['post_format']) )
		{
			$field['post_format'] = array( 'all' );
		}
				
		// validate taxonomy
		if( !$field['taxonomy'] || !is_array($field['taxonomy']) || in_array('', $field['taxonomy']) )
		{
			$field['taxonomy'] = array( 'all' );
		}
		
		
		// validate result_elements
		if( !is_array( $field['result_elements'] ) )
		{
			$field['result_elements'] = array();
		}
		
		if( !in_array('post_title', $field['result_elements']) )
		{
			$field['result_elements'][] = 'post_title';
		}
		
		
		// filters
		if( !is_array( $field['filters'] ) )
		{
			$field['filters'] = array();
		}
		
		
		// return
		return $field;
	}
		

	/*
   	*  posts_where
   	*
   	*  @description: 
   	*  @created: 3/09/12
   	*/
   	
   	function posts_where( $where, &$wp_query )
	{
	    global $wpdb;
	    
	    if ( $title = $wp_query->get('like_title') )
	    {
	        $where .= " AND " . $wpdb->posts . ".post_title LIKE '%" . esc_sql( wpdb::esc_like(  $title ) ) . "%'";
	    }
	    
	    return $where;
	}
	
	
	/*
	*  query_posts
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 27/01/13
	*/
	
	function query_posts()
   	{
   		// vars
   		$r = array(
   			'next_page_exists' => 1,
   			'html' => ''
   		);
   		
   		
   		// options
		$options = array(
			'post_type'					=>	'all',
			'post_format'				=>	'all',
			'taxonomy'					=>	'all',
			'posts_per_page'			=>	10,
			'paged'						=>	1,
			'orderby'					=>	'title',
			'order'						=>	'ASC',
			'post_status'				=>	'any',
			'suppress_filters'			=>	false,
			's'							=>	'',
			'lang'						=>	false,
			'update_post_meta_cache'	=>	false,
			'field_key'					=>	'',
			'nonce'						=>	'',
			'ancestor'					=>	false,
		);
		
		$options = array_merge( $options, $_POST );
		
		
		// validate
		if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
		{
			die();
		}
		
		
		// WPML
		if( $options['lang'] )
		{
			global $sitepress;
			
			if( !empty($sitepress) )
			{
				$sitepress->switch_lang( $options['lang'] );
			}
		}
		
		
		// convert types
		$options['post_type'] = explode(',', $options['post_type']);
		$options['taxonomy'] = explode(',', $options['taxonomy']);
		$options['post_format'] = explode(',', $options['post_format']);
		
		
		// load all post types by default
		if( in_array('all', $options['post_type']) )
		{
			$options['post_type'] = apply_filters('acf/get_post_types', array());
		}
		
		
		// attachment doesn't work if it is the only item in an array???
		if( is_array($options['post_type']) && count($options['post_type']) == 1 )
		{
			$options['post_type'] = $options['post_type'][0];
		}
		

		// create tax queries
		if( ! in_array('all', $options['taxonomy']) )
		{
			// vars
			$taxonomies = array();
			$options['tax_query'] = array();
			
			foreach( $options['taxonomy'] as $v )
			{
				
				// find term (find taxonomy!)
				// $term = array( 0 => $taxonomy, 1 => $term_id )
				$term = explode(':', $v); 
				
				
				// validate
				if( !is_array($term) || !isset($term[1]) )
				{
					continue;
				}
				
				
				// add to tax array
				$taxonomies[ $term[0] ][] = $term[1];
				
			}
			
			
			// now create the tax queries
			foreach( $taxonomies as $k => $v )
			{
				$options['tax_query'][] = array(
					'taxonomy' => $k,
					'field' => 'id',
					'terms' => $v,
				);
			}
		}
		
		unset( $options['taxonomy'] );
		


		// create tax queries
		if( ! in_array('all', $options['post_format']) )
		{
			if(!isset($options['tax_query'])) {
				$options['tax_query'] = array();
			}		
			$options['tax_query'][] = array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => $options['post_format'],
				'operator' => 'IN',
				'include_children' => false
			);
		}
		
		unset( $options['post_format'] );
		
		// search
		if( $options['s'] )
		{
			$options['like_title'] = $options['s'];
			
			add_filter( 'posts_where', array($this, 'posts_where'), 10, 2 );
		}
		
		unset( $options['s'] );
		
		
		// load field
		$field = array();
		if( $options['ancestor'] )
		{
			$ancestor = apply_filters('acf/load_field', array(), $options['ancestor'] );
			$field = acf_get_child_field_from_parent_field( $options['field_key'], $ancestor );
		}
		else
		{
			$field = apply_filters('acf/load_field', array(), $options['field_key'] );
		}
		
		
		// get the post from which this field is rendered on
		$the_post = get_post( $options['post_id'] );
		

		// query
		$wp_query = new WP_Query( $options );

		
		// global
		global $post;
		
		
		// loop
		while( $wp_query->have_posts() )
		{
			$wp_query->the_post();
			
			
			// right aligned info
			$title = '<span class="relationship-item-info">';
				
				$has_post_type = false;
				if( in_array('post_type', $field['result_elements']) )
				{
					$post_type_object = get_post_type_object( get_post_type() );
					$title .= $post_type_object->labels->singular_name;
					$has_post_type = true;
				}
				if( in_array('post_format', $field['result_elements']) )
				{	
					if($has_post_type) {
						$title .= ' - ';
					}
					$title .= $this->get_post_format();
				}
				
				// WPML
				if( $options['lang'] )
				{
					$title .= ' (' . $options['lang'] . ')';
				}
				
			$title .= '</span>';
			
			
			// featured_image
			if( in_array('featured_image', $field['result_elements']) )
			{
				$image = get_the_post_thumbnail( get_the_ID(), array(21, 21) );
				
				$title .= '<div class="result-thumbnail">' . $image . '</div>';
			}
			
			
			// title
			$title .= get_the_title();
			
			
			// status
			if( get_post_status() != "publish" )
			{
				$title .= ' (' . get_post_status() . ')';
			}
				
			
			// filters
			$title = apply_filters('acf/fields/relationship_formats/result', $title, $post, $field, $the_post);
			$title = apply_filters('acf/fields/relationship_formats/result/name=' . $field['_name'] , $title, $post, $field, $the_post);
			$title = apply_filters('acf/fields/relationship_formats/result/key=' . $field['key'], $title, $post, $field, $the_post);
			
			
			// update html
			$r['html'] .= '<li><a href="' . get_permalink() . '" data-post_id="' . get_the_ID() . '">' . $title .  '<span class="acf-button-add"></span></a></li>';
		}
		
		
		if( (int)$options['paged'] >= $wp_query->max_num_pages )
		{
			$r['next_page_exists'] = 0;
		}
		
		
		wp_reset_postdata();
		
		
		// return JSON
		echo json_encode( $r );
		
		die();
			
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// global
		global $post;

		
		// no row limit?
		if( !$field['max'] || $field['max'] < 1 )
		{
			$field['max'] = 9999;
		}
		
		
		// class
		$class = '';
		if( $field['filters'] )
		{
			foreach( $field['filters'] as $filter )
			{
				$class .= ' has-' . $filter;
			}
		}
		
		$attributes = array(
			'max' => $field['max'],
			's' => '',
			'paged' => 1,
			'post_type' => implode(',', $field['post_type']),
			'taxonomy' => implode(',', $field['taxonomy']),
			'field_key' => $field['key']
		);
		
		
		// Lang
		if( defined('ICL_LANGUAGE_CODE') )
		{
			$attributes['lang'] = ICL_LANGUAGE_CODE;
		}
		
		
		// parent
		preg_match('/\[(field_.*?)\]/', $field['name'], $ancestor);
		if( isset($ancestor[1]) && $ancestor[1] != $field['key'])
		{
			$attributes['ancestor'] = $ancestor[1];
		}
				
		?>
<div class="acf_relationship<?php echo $class; ?>"<?php foreach( $attributes as $k => $v ): ?> data-<?php echo $k; ?>="<?php echo $v; ?>"<?php endforeach; ?>>
	
	
	<!-- Hidden Blank default value -->
	<input type="hidden" name="<?php echo $field['name']; ?>" value="" />
	
	
	<!-- Left List -->
	<div class="relationship_left">
		<table class="widefat">
			<thead>
				<?php if(in_array( 'search', $field['filters']) ): ?>
				<tr>
					<th>
						<input class="relationship_search" placeholder="<?php _e("Search...",'acf'); ?>" type="text" id="relationship_<?php echo $field['name']; ?>" />
					</th>
				</tr>
				<?php endif; ?>
				<?php if(in_array( 'post_type', $field['filters']) ): ?>
				<tr>
					<th>
						<?php 
						
						// vars
						$choices = array(
							'all' => __("Filter by post type",'acf')
						);
						
						
						if( in_array('all', $field['post_type']) )
						{
							$post_types = apply_filters( 'acf/get_post_types', array() );
							$choices = array_merge( $choices, $post_types);
						}
						else
						{
							foreach( $field['post_type'] as $post_type )
							{
								$choices[ $post_type ] = $post_type;
							}
						}
						
						
						// create field
						do_action('acf/create_field', array(
							'type'	=>	'select',
							'name'	=>	'',
							'class'	=>	'select-post_type',
							'value'	=>	'',
							'choices' => $choices,
						));
						
						?>
					</th>
				</tr>
				<?php endif; ?>
				<?php if(in_array( 'post_format', $field['filters']) ): ?>
				<tr>
					<th>
						<?php 
						
						// vars
						$choices = array(
							'all' => __("Filter by post format",'acf')
						);
						
						
						if( in_array('all', $field['post_format']) )
						{
							$post_formats = $this->get_post_formats();
    
							$choices = array_merge( $choices, $post_formats);
						}
						else
						{
							foreach( $field['post_format'] as $post_format )
							{
								$choices[ $post_format ] = $post_format;
							}
						}
						
						
						// create field
						do_action('acf/create_field', array(
							'type'	=>	'select',
							'name'	=>	'',
							'class'	=>	'select-post_format',
							'value'	=>	'',
							'choices' => $choices,
						));
						
						?>
					</th>
				</tr>
				<?php endif; ?>
			</thead>
		</table>
		<ul class="bl relationship_list">
			<li class="load-more">
				<div class="acf-loading"></div>
			</li>
		</ul>
	</div>
	<!-- /Left List -->
	
	<!-- Right List -->
	<div class="relationship_right">
		<ul class="bl relationship_list">
		<?php

		if( $field['value'] )
		{
			foreach( $field['value'] as $p )
			{
				// right aligned info
				$title = '<span class="relationship-item-info">';
					
					$has_post_type = false;
					if( in_array('post_type', $field['result_elements']) )
					{
						$post_type_object = get_post_type_object( get_post_type($p) );
						$title .= $post_type_object->labels->singular_name;
						$has_post_type = true;
					}
					
					if( in_array('post_format', $field['result_elements']) )
					{
						if($has_post_type) {
							$title .= ' - ';
						}
						$title .= $this->get_post_format($p);
					}
					
					
					// WPML
					if( defined('ICL_LANGUAGE_CODE') )
					{
						$title .= ' (' . ICL_LANGUAGE_CODE . ')';
					}
					
				$title .= '</span>';
				
				
				// featured_image
				if( in_array('featured_image', $field['result_elements']) )
				{
					$image = get_the_post_thumbnail( $p->ID, array(21, 21) );
					
					$title .= '<div class="result-thumbnail">' . $image . '</div>';
				}
				
				
				// find title. Could use get_the_title, but that uses get_post(), so I think this uses less Memory
				$title .= apply_filters( 'the_title', $p->post_title, $p->ID );

				// status
				if($p->post_status != "publish")
				{
					$title .= " ($p->post_status)";
				}

				
				// filters
				$title = apply_filters('acf/fields/relationship_formats/result', $title, $p, $field, $post);
				$title = apply_filters('acf/fields/relationship_formats/result/name=' . $field['_name'] , $title, $p, $field, $post);
				$title = apply_filters('acf/fields/relationship_formats/result/key=' . $field['key'], $title, $p, $field, $post);
				
				
				echo '<li>
					<a href="' . get_permalink($p->ID) . '" class="" data-post_id="' . $p->ID . '">' . $title . '<span class="acf-button-remove"></span></a>
					<input type="hidden" name="' . $field['name'] . '[]" value="' . $p->ID . '" />
				</li>';
				
					
			}
		}
			
		?>
		</ul>
	</div>
	<!-- / Right List -->
	
</div>
		<?php
	}
	
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Return Format",'acf'); ?></label>
		<p><?php _e("Specify the returned value on front end",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][return_format]',
			'value'		=>	$field['return_format'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'object'	=>	__("Post Objects",'acf'),
				'id'		=>	__("Post IDs",'acf')
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Post Type",'acf'); ?></label>
	</td>
	<td>
		<?php 
		
		$choices = array(
			'all'	=>	__("All",'acf')
		);
		$choices = apply_filters('acf/get_post_types', $choices);
		
		
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][post_type]',
			'value'	=>	$field['post_type'],
			'choices'	=>	$choices,
			'multiple'	=>	1,
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Post Formats",'acf'); ?></label>
	</td>
	<td>
		<?php 
		
		$choices = array(
			'all'	=>	__("All",'acf')
		);
		$choices = $this->get_post_formats();
		
		
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][post_format]',
			'value'	=>	$field['post_format'],
			'choices'	=>	$choices,
			'multiple'	=>	1,
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Filter from Taxonomy",'acf'); ?></label>
	</td>
	<td>
		<?php 
		$choices = array(
			'' => array(
				'all' => __("All",'acf')
			)
		);
		$simple_value = false;
		$choices = apply_filters('acf/get_taxonomies_for_select', $choices, $simple_value);
		
		
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][taxonomy]',
			'value'	=>	$field['taxonomy'],
			'choices' => $choices,
			'multiple'	=>	1,
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Filters",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'checkbox',
			'name'	=>	'fields['.$key.'][filters]',
			'value'	=>	$field['filters'],
			'choices'	=>	array(
				'search'	=>	__("Search",'acf'),
				'post_type'	=>	__("Post Type Select",'acf'),
				'post_format'	=>	__("Post Format Select",'acf'),
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Elements",'acf'); ?></label>
		<p><?php _e("Selected elements will be displayed in each result",'acf') ?></p>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'checkbox',
			'name'	=>	'fields['.$key.'][result_elements]',
			'value'	=>	$field['result_elements'],
			'choices' => array(
				'featured_image' => __("Featured Image",'acf'),
				'post_title' => __("Post Title",'acf'),
				'post_type' => __("Post Type",'acf'),
				'post_format' => __("Post Format",'acf'),
			),
			'disabled' => array(
				'post_title'
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Maximum posts",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'number',
			'name'	=>	'fields['.$key.'][max]',
			'value'	=>	$field['max'],
		));
		?>
	</td>
</tr>
		<?php
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// empty?
		if( !empty($value) )
		{
			// Pre 3.3.3, the value is a string coma seperated
			if( is_string($value) )
			{
				$value = explode(',', $value);
			}
			
			
			// convert to integers
			if( is_array($value) )
			{
				$value = array_map('intval', $value);
				
				// convert into post objects
				$value = $this->get_posts( $value );
			}
			
		}
		
		
		// return value
		return $value;	
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		// empty?
		if( !$value )
		{
			return $value;
		}
		
		
		// Pre 3.3.3, the value is a string coma seperated
		if( is_string($value) )
		{
			$value = explode(',', $value);
		}
		
		
		// empty?
		if( !is_array($value) || empty($value) )
		{
			return $value;
		}
		
		
		// convert to integers
		$value = array_map('intval', $value);
		
		
		// return format
		if( $field['return_format'] == 'object' )
		{
			$value = $this->get_posts( $value );	
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  get_posts
	*
	*  This function will take an array of post_id's ($value) and return an array of post_objects
	*
	*  @type	function
	*  @date	7/08/13
	*
	*  @param	$post_ids (array) the array of post ID's
	*  @return	(array) an array of post objects
	*/
	
	function get_posts( $post_ids )
	{
		// validate
		if( empty($post_ids) )
		{
			return $post_ids;
		}
		
		
		// vars
		$r = array();
		
		
		// find posts (DISTINCT POSTS)
		$posts = get_posts(array(
			'numberposts'	=>	-1,
			'post__in'		=>	$post_ids,
			'post_type'		=>	apply_filters('acf/get_post_types', array()),
			'post_status'	=>	'any',
		));

		
		$ordered_posts = array();
		foreach( $posts as $p )
		{
			// create array to hold value data
			$ordered_posts[ $p->ID ] = $p;
		}
		
		
		// override value array with attachments
		foreach( $post_ids as $k => $v)
		{
			// check that post exists (my have been trashed)
			if( isset($ordered_posts[ $v ]) )
			{
				$r[] = $ordered_posts[ $v ];
			}
		}
		
		
		// return
		return $r;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// validate
		if( empty($value) )
		{
			return $value;
		}
		
		
		if( is_string($value) )
		{
			// string
			$value = explode(',', $value);
			
		}
		elseif( is_object($value) && isset($value->ID) )
		{
			// object
			$value = array( $value->ID );
			
		}
		elseif( is_array($value) )
		{
			// array
			foreach( $value as $k => $v ){
			
				// object?
				if( is_object($v) && isset($v->ID) )
				{
					$value[ $k ] = $v->ID;
				}
			}
			
		}
		
		
		// save value as strings, so we can clearly search for them in SQL LIKE statements
		$value = array_map('strval', $value);
						
		
		return $value;
	}



	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
		
		
		// register ACF scripts
		wp_register_script( 'acf-input-relationship_formats', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );

		
		// scripts
		wp_enqueue_script(array(
			'acf-input-relationship_formats',	
		));
		// styles
		wp_enqueue_style(array(
			'acf-input-relationship_formats',	
		));
		
		
	}
	
	function get_post_format($p = null) {
		
		$format = get_post_format($p);
		if ( false === $format ) {
			$format = 'standard';
		}
		
		return $format;
	}
	
	function get_post_formats() {
		
		$post_formats = array();
		$formats = get_theme_support( 'post-formats' );
							
	    if ( is_array( $formats[0] ) ) {
	       $formats = $formats[0];
	       
	       foreach($formats as $format) {
		       
		       $post_formats['post-format-'.$format] = ucfirst($format);
	       }
	    }
		
		return $post_formats;				    
	}
		
}

new acf_field_relationship_formats();

?>