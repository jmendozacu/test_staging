<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_category_settings
 * @author      Luciano "WebCaos" Ubertini
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) ) {
    exit;
}

// Don't duplicate me!
if ( !class_exists ( 'ReduxFramework_category_settings' ) ) {

    /**
     * Main ReduxFramework_category_settings class
     *
     * @since       1.0.0
     */
    class ReduxFramework_category_settings {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct ( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;
            $this->options = $this->field["options"];
            
            if(!empty($this->field["taxonomy"]))
            	$this->taxonomy = $this->field["taxonomy"];
            
            $this->categories = array();
            if(!empty($this->taxonomy) && $this->taxonomy != 'category') {
	            $categories = get_terms($this->taxonomy, array('hide_empty'=>false));
            }else{
         		$categories = get_categories(array('hide_empty'=>false));
         	}	
         	
         	foreach($categories as $cat) {
	         	$this->categories[$cat->term_id] = $cat->name;
         	}
  
         	$this->templates = !empty($this->options["templates"]) ? $this->options["templates"] : array();
         	$this->sidebar_positions = !empty($this->options["sidebar_positions"]) ? $this->options["sidebar_positions"] : array();

	
		 	global $wp_registered_sidebars;
	
			foreach ( $wp_registered_sidebars as $registered_sidebar ) {
				$this->sidebar_areas[ $registered_sidebar['id'] ] = $registered_sidebar['name'];
			}
				   
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */


        public function render () {

            $defaults = array(
                'content_name' => __ ( 'Category Settings', 'redux-framework' )
            );

            $this->field = wp_parse_args ( $this->field, $defaults );

            echo '<div class="redux-category_settings-accordion" data-new-content-name="' . esc_attr ( sprintf ( __ ( 'New %s', 'redux-framework' ), $this->field[ 'content_name' ] ) ) . '">';

            $x = 0;

            if ( isset ( $this->value ) && is_array ( $this->value ) && !empty ( $this->value ) ) {

                $category_settings = $this->value;
				$defaults = array(
                    'sort' => 0,
                    'category' => 'category',
                    'template' => '',
                    'category_name' => '',
                    'template_name' => '',
                    'sidebar_position_name' => '',
                    'sidebar_area_name' => '',
                    'show_post_category' => (bool)xt_option('show_post_category'),
                    'show_post_author' => (bool)xt_option('show_post_author'),
                    'show_post_date' => (bool)xt_option('show_post_date'),
                    'show_post_excerpt' => (bool)xt_option('show_post_excerpt'),
                    'show_post_stats' => (bool)xt_option('show_post_stats')
                );
                
                foreach ( $category_settings as $category ) {

                    if ( empty ( $category ) ) {
                        continue;
                    }

                    
                    $category = wp_parse_args ( $category, $defaults );
                    $this->category = $category;
                     

                    echo '<div class="redux-category_settings-accordion-group"><fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '"><h3> <span class="redux-category_settings-header">' . (!empty($category[ 'category_name' ]) ?  $category[ 'category_name' ]. ' &nbsp;/&nbsp; '.$category[ 'template_name' ] : 'New '.$this->field[ 'content_name' ]). '</span></h3><div>';

                    echo '<ul id="' . $this->field[ 'id' ] . '-ul" class="redux-category_settings-list">';

					$this->createFieldSelect('Category', 'category', $this->categories, $x, 'slide-name');
					$this->createFieldSelect('Template', 'template', $this->templates, $x);
					$this->createFieldSelect('Sidebar Position', 'sidebar_position', $this->sidebar_positions, $x);
					$this->createFieldSelect('Sidebar Area', 'sidebar_area', $this->sidebar_areas, $x);
					
					$this->createFieldSwitch('Show Post Category', 'show_post_category', $x);
					$this->createFieldSwitch('Show Post Excerpt', 'show_post_excerpt', $x);
					$this->createFieldSwitch('Show Post Date', 'show_post_date', $x);
					$this->createFieldSwitch('Show Post Author', 'show_post_author', $x);
					$this->createFieldSwitch('Show Post Stats', 'show_post_stats', $x);
					
					
					$this->createFieldHidden('sort', $x);
					
                    echo '<li><a href="javascript:void(0);" class="button deletion redux-category_settings-remove">' . sprintf ( __ ( 'Delete %s', 'redux-framework' ), $this->field[ 'content_name' ] ) . '</a></li>';
                    echo '</ul></div></fieldset></div>';
                    $x ++;
                }
            }


            echo '</div><a href="javascript:void(0);" class="button redux-category_settings-add button-primary" rel-id="' . $this->field[ 'id' ] . '-ul" rel-name="' . $this->field[ 'name' ] . '[name][]' . $this->field['name_suffix'] .'">' . sprintf ( __ ( 'Add %s', 'redux-framework' ), $this->field[ 'content_name' ] ) . '</a><br/>';
        }


		protected function createFieldSelect($label, $name, $options, $index) {
			
			echo '<li class="'.$name.'-select">';
	        echo '<h4>Select '.$label.'</h4>';
	        echo '<select onchange="jQuery(this).next().val(jQuery(this).find(\'option:selected\').text())" name="' . $this->field[ 'name' ] . '[' . $index . ']['.$name.']' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-'.$name.'_' . $index . '"  class="slide-'.$name.'">';
			echo '<option value=""> --- </option>';
	        foreach($options as $key => $value) {
		        $selected = '';
		        if($key == $this->category[$name])
		        	$selected = ' selected="selected"';
		        	
		        echo '<option'.$selected.' value="'.$key.'">'.$value.'</option>';
	        }
	        echo '</select>';
            echo '<input class="slide-name" type="hidden" name="' . $this->field[ 'name' ] . '[' . $index . ']['.$name.'_name]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-'.$name.'_name_' . $index . '" value="' . $this->category[ $name.'_name' ] . '" />';
	        echo '</li>';			
		}

		protected function createFieldSwitch($label, $name, $index) {
			
			echo '<li class="'.$name.'-switch">';
	        echo '
	        <fieldset style="padding:0" class="redux-field-container redux-field redux-field-init redux-container-switch" data-id="' . $this->field[ 'id' ] . '-'.$name.'_name_' . $index . '" data-type="switch">
	        	<h4>'.$label.'</h4>
	        	<div class="switch-options">
	        		<label class="cb-enable '.(((bool)$this->category[ $name ]) ? 'selected' : '').'" data-id="' . $this->field[ 'id' ] . '-'.$name.'_name_' . $index . '"><span>On</span></label>
					<label class="cb-disable '.(((bool)$this->category[ $name ]) ? '' : 'selected').'" data-id="' . $this->field[ 'id' ] . '-'.$name.'_name_' . $index . '"><span>Off</span></label>
					<input type="hidden" class="checkbox checkbox-input"  name="' . $this->field[ 'name' ] . '[' . $index . ']['.$name.']' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-'.$name.'_' . $index . '" value="' . (bool)$this->category[ $name ] . '">
	        	</div>
	        </fieldset>';

	        echo '</li>';			
		}
		
		protected function createFieldHidden($name, $index) {
			
            echo '<li><input type="hidden" class="slide-'.$name.'" name="' . $this->field[ 'name' ] . '[' . $index . ']['.$name.']' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-'.$name.'_' . $index . '" value="' . $this->category[ $name ] . '" />';
			
		}
		
		
        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue () {

            wp_enqueue_script (
                'redux-field-category_settings-js', 
                XT_ADMIN_URL . '/redux-extensions/extensions/category_settings/field_category_settings' . !Redux_Functions::isMin () . '.js', 
                array( 'jquery'), 
                time (), 
                true
            );
            
            wp_enqueue_style (
                'redux-field-category_settings-css', 
                XT_ADMIN_URL . '/redux-extensions/extensions/category_settings/field_category_settings.css', 
                '2', 
                true
            );
 
            wp_enqueue_style (
                    'redux-field-checkbox-css', ReduxFramework::$_url . 'inc/fields/checkbox/field_checkbox.css', time (), true
            );

            wp_enqueue_script (
                    'redux-field-checkbox-js', ReduxFramework::$_url . 'inc/fields/checkbox/field_checkbox' . Redux_Functions::isMin () . '.js', array( 'jquery', 'redux-js' ), time (), true
            );
                       
        }
    }

}