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
 * @subpackage  Field_networks
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
if ( !class_exists ( 'ReduxFramework_networks' ) ) {

    /**
     * Main ReduxFramework_networks class
     *
     * @since       1.0.0
     */
    class ReduxFramework_networks {

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
            
			$this->icons = array("adjust","adn","align-center","align-justify","align-left","align-right","ambulance","anchor","android","angellist","angle-double-down","angle-double-left","angle-double-right","angle-double-up","angle-down","angle-left","angle-right","angle-up","apple","archive","area-chart","arrow-circle-down","arrow-circle-left","arrow-circle-o-down","arrow-circle-o-left","arrow-circle-o-right","arrow-circle-o-up","arrow-circle-right","arrow-circle-up","arrow-down","arrow-left","arrow-right","arrow-up","arrows","arrows-alt","arrows-h","arrows-v","asterisk","at","automobile","backward","ban","bank","bar-chart","bar-chart-o","barcode","bars","bed","beer","behance","behance-square","bell","bell-o","bell-slash","bell-slash-o","bicycle","binoculars","birthday-cake","bitbucket","bitbucket-square","bitcoin","bold","bolt","bomb","book","bookmark","bookmark-o","briefcase","btc","bug","building","building-o","bullhorn","bullseye","bus","buysellads","cab","calculator","calendar","calendar-o","camera","camera-retro","car","caret-down","caret-left","caret-right","caret-square-o-down","caret-square-o-left","caret-square-o-right","caret-square-o-up","caret-up","cart-arrow-down","cart-plus","cc","cc-amex","cc-discover","cc-mastercard","cc-paypal","cc-stripe","cc-visa","certificate","chain","chain-broken","check","check-circle","check-circle-o","check-square","check-square-o","chevron-circle-down","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-down","chevron-left","chevron-right","chevron-up","child","circle","circle-o","circle-o-notch","circle-thin","clipboard","clock-o","close","cloud","cloud-download","cloud-upload","cny","code","code-fork","codepen","coffee","cog","cogs","columns","comment","comment-o","comments","comments-o","compass","compress","connectdevelop","copy","copyright","credit-card","crop","crosshairs","css3","cube","cubes","cut","cutlery","dashboard","dashcube","database","dedent","delicious","desktop","deviantart","diamond","digg","dollar","dot-circle-o","download","dribbble","dropbox","drupal","edit","eject","ellipsis-h","ellipsis-v","empire","envelope","envelope-o","envelope-square","eraser","eur","euro","exchange","exclamation","exclamation-circle","exclamation-triangle","expand","external-link","external-link-square","eye","eye-slash","eyedropper","facebook","facebook-f","facebook-official","facebook-square","fast-backward","fast-forward","fax","female","fighter-jet","file","file-archive-o","file-audio-o","file-code-o","file-excel-o","file-image-o","file-movie-o","file-o","file-pdf-o","file-photo-o","file-picture-o","file-powerpoint-o","file-sound-o","file-text","file-text-o","file-video-o","file-word-o","file-zip-o","files-o","film","filter","fire","fire-extinguisher","flag","flag-checkered","flag-o","flash","flask","flickr","floppy-o","folder","folder-o","folder-open","folder-open-o","font","forumbee","forward","foursquare","frown-o","futbol-o","gamepad","gavel","gbp","ge","gear","gears","genderless","gift","git","git-square","github","github-alt","github-square","gittip","glass","globe","google","google-plus","google-plus-square","google-wallet","graduation-cap","gratipay","group","h-square","hacker-news","hand-o-down","hand-o-left","hand-o-right","hand-o-up","hdd-o","header","headphones","heart","heart-o","heartbeat","history","home","hospital-o","hotel","html5","ils","image","inbox","indent","info","info-circle","inr","instagram","institution","ioxhost","italic","joomla","jpy","jsfiddle","key","keyboard-o","krw","language","laptop","lastfm","lastfm-square","leaf","leanpub","legal","lemon-o","level-down","level-up","life-bouy","life-buoy","life-ring","life-saver","lightbulb-o","line-chart","link","linkedin","linkedin-square","linux","list","list-alt","list-ol","list-ul","location-arrow","lock","long-arrow-down","long-arrow-left","long-arrow-right","long-arrow-up","magic","magnet","mail-forward","mail-reply","mail-reply-all","male","map-marker","mars","mars-double","mars-stroke","mars-stroke-h","mars-stroke-v","maxcdn","meanpath","medium","medkit","meh-o","mercury","microphone","microphone-slash","minus","minus-circle","minus-square","minus-square-o","mobile","mobile-phone","money","moon-o","mortar-board","motorcycle","music","navicon","neuter","newspaper-o","openid","outdent","pagelines","paint-brush","paper-plane","paper-plane-o","paperclip","paragraph","paste","pause","paw","paypal","pencil","pencil-square","pencil-square-o","phone","phone-square","photo","picture-o","pie-chart","pied-piper","pied-piper-alt","pinterest","pinterest-p","pinterest-square","plane","play","play-circle","play-circle-o","plug","plus","plus-circle","plus-square","plus-square-o","power-off","print","puzzle-piece","qq","qrcode","question","question-circle","quote-left","quote-right","ra","random","rebel","recycle","reddit","reddit-square","refresh","remove","renren","reorder","repeat","reply","reply-all","retweet","rmb","road","rocket","rotate-left","rotate-right","rouble","rss","rss-square","rub","ruble","rupee","save","scissors","search","search-minus","search-plus","sellsy","send","send-o","server","share","share-alt","share-alt-square","share-square","share-square-o","shekel","sheqel","shield","ship","shirtsinbulk","shopping-cart","sign-in","sign-out","signal","simplybuilt","sitemap","skyatlas","skype","slack","sliders","slideshare","smile-o","soccer-ball-o","sort","sort-alpha-asc","sort-alpha-desc","sort-amount-asc","sort-amount-desc","sort-asc","sort-desc","sort-down","sort-numeric-asc","sort-numeric-desc","sort-up","soundcloud","space-shuttle","spinner","spoon","spotify","square","square-o","stack-exchange","stack-overflow","star","star-half","star-half-empty","star-half-full","star-half-o","star-o","steam","steam-square","step-backward","step-forward","stethoscope","stop","street-view","strikethrough","stumbleupon","stumbleupon-circle","subscript","subway","suitcase","sun-o","superscript","support","table","tablet","tachometer","tag","tags","tasks","taxi","tencent-weibo","terminal","text-height","text-width","th","th-large","th-list","thumb-tack","thumbs-down","thumbs-o-down","thumbs-o-up","thumbs-up","ticket","times","times-circle","times-circle-o","tint","toggle-down","toggle-left","toggle-off","toggle-on","toggle-right","toggle-up","train","transgender","transgender-alt","trash","trash-o","tree","trello","trophy","truck","try","tty","tumblr","tumblr-square","turkish-lira","twitch","twitter","twitter-square","umbrella","underline","undo","university","unlink","unlock","unlock-alt","unsorted","upload","usd","user","user-md","user-plus","user-secret","user-times","users","venus","venus-double","venus-mars","viacoin","video-camera","vimeo-square","vine","vk","volume-down","volume-off","volume-up","warning","wechat","weibo","weixin","whatsapp","wheelchair","wifi","windows","won","wordpress","wrench","xing","xing-square","yahoo","yelp","yen","youtube","youtube-play","youtube-square");


			sort($this->icons);
		   
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
                'show' => array(
                    'name' => true,
                    'icon' => true,
                    'url' => true,
                ),
                'content_name' => __ ( 'Network', 'redux-framework' )
            );

            $this->field = wp_parse_args ( $this->field, $defaults );

            echo '<div class="redux-networks-accordion" data-new-content-name="' . esc_attr ( sprintf ( __ ( 'New %s', 'redux-framework' ), $this->field[ 'content_name' ] ) ) . '">';

            $x = 0;

            $multi = ( isset ( $this->field[ 'multi' ] ) && $this->field[ 'multi' ] ) ? ' multiple="multiple"' : "";

            if ( isset ( $this->value ) && is_array ( $this->value ) && !empty ( $this->value ) ) {

                $networks = $this->value;

                foreach ( $networks as $network ) {

                    if ( empty ( $network ) ) {
                        continue;
                    }

                    $defaults = array(
                        'name' => 'network',
                        'icon' => '',
                        'sort' => '',
                        'color' => '',
                        'url' => '',
                        'image' => '',
                        'thumb' => '',
                        'attachment_id' => '',
                        'height' => '',
                        'width' => '',
                        'select' => array(),
                    );
                    $network = wp_parse_args ( $network, $defaults );

                    if ( empty ( $network[ 'thumb' ] ) && !empty ( $network[ 'attachment_id' ] ) ) {
                        $img = wp_get_attachment_image_src ( $network[ 'attachment_id' ], 'full' );
                        $network[ 'image' ] = $img[ 0 ];
                        $network[ 'width' ] = $img[ 1 ];
                        $network[ 'height' ] = $img[ 2 ];
                    }

                    echo '<div class="redux-networks-accordion-group"><fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '"><h3><i class="fa fa-'.(!empty($network[ 'icon' ]) ?  $network[ 'icon' ] : '').'"></i> <span class="redux-networks-header">' . (!empty($network[ 'name' ]) ?  $network[ 'name' ] : 'New '.$this->field[ 'content_name' ]). '</span></h3><div>';

                    echo '<ul id="' . $this->field[ 'id' ] . '-ul" class="redux-networks-list">';

                    if ( $this->field[ 'show' ][ 'name' ] ) {
                        $name_type = "text";
                    } else {
                        $name_type = "hidden";
                    }

                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'name' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'name' ] ) : __ ( 'Title', 'redux-framework' );
                    echo '<li><h4>'.$placeholder.'</h4><input type="' . $name_type . '" id="' . $this->field[ 'id' ] . '-name_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][name]' . $this->field['name_suffix'] . '" value="' . esc_attr ( $network[ 'name' ] ) . '" placeholder="' . $placeholder . '" class="full-text slide-name" /></li>';

				
					
                    $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'url' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'url' ] ) : __ ( 'URL', 'redux-framework' );
                    if ( $this->field[ 'show' ][ 'url' ] ) {
                        $url_type = "text";
                    } else {
                        $url_type = "hidden";
                    }
                    echo '<li><h4>'.$placeholder.'</h4><input type="' . $url_type . '" id="' . $this->field[ 'id' ] . '-url_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][url]' . $this->field['name_suffix'] .'" value="' . esc_attr ( $network[ 'url' ] ) . '" class="full-text" placeholder="' . $placeholder . '" /></li>';
                    

	                $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'color' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'color' ] ) : __ ( 'Color', 'redux-framework' );
	                echo '<li><h4>'.$placeholder.'</h4><input type="text" id="' . $this->field[ 'id' ] . '-color_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][color]' . $this->field['name_suffix'] .'" value="'.esc_attr ( $network[ 'color' ] ).'" class="network-color-field" data-default-color="'.esc_attr ( $network[ 'color' ] ) .'" /></li>';
                
                
					if ( $this->field[ 'show' ][ 'icon' ] ) {
	                	
				        echo '<li class="fontawsome-select">';
				        echo '<input type="hidden" name="' . $this->field[ 'name' ] . '[' . $x . '][icon]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-icon_' . $x . '" value="' . esc_attr ( $network[ 'icon' ] ) . '" />';
				        echo '<h4><i class="fa fa-'.(!empty($network[ 'icon' ]) ? esc_attr ( $network[ 'icon' ] ) : "caret-right").'"></i> '.(!empty($network[ 'icon' ]) ? esc_attr ( $network[ 'icon' ] ) : "Select Icon").'</h4>';
				        echo '<ul class="fa-icons">';
				        echo '	<li><a class="fontawsome-select-button" href="#">Browse Icons <i class="fa fa-angle-down"></i></a>';
				        echo '	<ul class="fa-icons-menu">';
				        
				        foreach($this->icons as $icon) {
					        
					        $selected = '';
					        if($icon == $this->value)
					        	$selected = ' class="selected"';
					        	
					        echo '<li'.$selected.'><a href="#" id="'.$icon.'"><i class="fa fa-'.$icon.'"></i> '.$icon.'</a></li>';
				        }
				        echo '	</ul>';
				        echo '	</li>';
				        echo '</ul>';
				        echo '</li>';

					}

   

                    $hide = '';
                    if ( empty ( $network[ 'image' ] ) ) {
                        $hide = ' hide';
                    }

					echo '<li><h4>Or Upload Icon</h4>';
                    echo '<div class="screenshot' . $hide . '">';
                    echo '<a class="of-uploaded-image" href="' . $network[ 'image' ] . '">';
                    echo '<img class="redux-networks-image" id="image_image_id_' . $x . '" src="' . $network[ 'thumb' ] . '" alt="" target="_blank" rel="external" />';
                    echo '</a>';
                    echo '</div>';

                    echo '<div class="redux_networks_add_remove">';

                    echo '<span class="button media_upload_button" id="add_' . $x . '">' . __ ( 'Upload', 'redux-framework' ) . '</span>';

                    $hide = '';
                    if ( empty ( $network[ 'image' ] ) || $network[ 'image' ] == '' ) {
                        $hide = ' hide';
                    }

                    echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $network[ 'attachment_id' ] . '">' . __ ( 'Remove', 'redux-framework' ) . '</span>';

                    echo '</div></li>' . "\n";


                    echo '<li><input type="hidden" class="slide-sort" name="' . $this->field[ 'name' ] . '[' . $x . '][sort]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-sort_' . $x . '" value="' . $network[ 'sort' ] . '" />';
                    echo '<li><input type="hidden" class="upload-id" name="' . $this->field[ 'name' ] . '[' . $x . '][attachment_id]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_id_' . $x . '" value="' . $network[ 'attachment_id' ] . '" />';
                    echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field[ 'name' ] . '[' . $x . '][thumb]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-thumb_url_' . $x . '" value="' . $network[ 'thumb' ] . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload" name="' . $this->field[ 'name' ] . '[' . $x . '][image]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_url_' . $x . '" value="' . $network[ 'image' ] . '" readonly="readonly" />';
                    echo '<input type="hidden" class="upload-height" name="' . $this->field[ 'name' ] . '[' . $x . '][height]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_height_' . $x . '" value="' . $network[ 'height' ] . '" />';
                    echo '<input type="hidden" class="upload-width" name="' . $this->field[ 'name' ] . '[' . $x . '][width]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_width_' . $x . '" value="' . $network[ 'width' ] . '" /></li>';
                    echo '<li><a href="javascript:void(0);" class="button deletion redux-networks-remove">' . sprintf ( __ ( 'Delete %s', 'redux-framework' ), $this->field[ 'content_name' ] ) . '</a></li>';
                    echo '</ul></div></fieldset></div>';
                    $x ++;
                }
            }

            if ( $x == 0 ) {
                echo '<div class="redux-networks-accordion-group"><fieldset class="redux-field" data-id="' . $this->field[ 'id' ] . '"><h3><i class="fa fa-'.(!empty($network[ 'icon' ]) ?  $network[ 'icon' ] : '').'"></i> <span class="redux-networks-header">New ' . $this->field[ 'content_name' ] . '</span></h3><div>';

                echo '<ul id="' . $this->field[ 'id' ] . '-ul" class="redux-networks-list">';
                if ( $this->field[ 'show' ][ 'name' ] ) {
                    $name_type = "text";
                } else {
                    $name_type = "hidden";
                }
                $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'name' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'name' ] ) : __ ( 'Title', 'redux-framework' );
                echo '<li><h4>'.$placeholder.'</h4><input type="' . $name_type . '" id="' . $this->field[ 'id' ] . '-name_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][name]' . $this->field['name_suffix'] .'" value="" placeholder="' . $placeholder . '" class="full-text slide-name" /></li>';
                
                $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'url' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'url' ] ) : __ ( 'URL', 'redux-framework' );
                if ( $this->field[ 'show' ][ 'url' ] ) {
                    $url_type = "text";
                } else {
                    $url_type = "hidden";
                }
                echo '<li><h4>'.$placeholder.'</h4><input type="' . $url_type . '" id="' . $this->field[ 'id' ] . '-url_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][url]' . $this->field['name_suffix'] .'" value="" class="full-text" placeholder="' . $placeholder . '" /></li>';
                
                
                $placeholder = ( isset ( $this->field[ 'placeholder' ][ 'color' ] ) ) ? esc_attr ( $this->field[ 'placeholder' ][ 'color' ] ) : __ ( 'Color', 'redux-framework' );
	            echo '<li><h4>'.$placeholder.'</h4><input type="text" id="' . $this->field[ 'id' ] . '-color_' . $x . '" name="' . $this->field[ 'name' ] . '[' . $x . '][color]' . $this->field['name_suffix'] .'" value="'.esc_attr ( $network[ 'color' ] ).'" class="network-color-field" data-default-color="'.esc_attr ( $network[ 'color' ] ) .'" /></li>';



                if ( $this->field[ 'show' ][ 'icon' ] ) {
                
			        echo '<li class="fontawsome-select">';
			        echo '<input type="hidden" name="' . $this->field[ 'name' ] . '[' . $x . '][icon]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-icon_' . $x . '" value="' . esc_attr($this->value) . '" />';
			        echo '<h4>Select Icon</h4>';
			        echo '<ul class="fa-icons">';
			        echo '	<li><a class="fontawsome-select-button" href="#">Browse Icons <i class="fa fa-angle-down"></i></a>';
			        echo '	<ul class="fa-icons-menu">';
			        
			        foreach($this->icons as $icon) {
				        
				        $selected = '';
				        if($icon == $this->value)
				        	$selected = ' class="selected"';
				        	
				        echo '<li'.$selected.'><a href="#" id="'.$icon.'"><i class="fa fa-'.$icon.'"></i> '.$icon.'</a></li>';
			        }
			        echo '	</ul>';
			        echo '	</li>';
			        echo '</ul>';
			        echo '</li>';

				}

                $hide = ' hide';

 				echo '<li><h4>Or Upload Icon</h4>';
                echo '<div class="screenshot' . $hide . '">';
                echo '<a class="of-uploaded-image" href="">';
                echo '<img class="redux-networks-image" id="image_image_id_' . $x . '" src="" alt="" target="_blank" rel="external" />';
                echo '</a>';
                echo '</div>';

                //Upload controls DIV
                echo '<div class="upload_button_div">';

                //If the user has WP3.5+ show upload/remove button
                echo '<span class="button media_upload_button" id="add_' . $x . '">' . __ ( 'Upload', 'redux-framework' ) . '</span>';

                echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $this->parent->args[ 'opt_name' ] . '[' . $this->field[ 'id' ] . '][attachment_id]">' . __ ( 'Remove', 'redux-framework' ) . '</span>';

                echo '</div></li>' . "\n";
                
                					
                echo '<li><input type="hidden" class="slide-sort" name="' . $this->field[ 'name' ] . '[' . $x . '][sort]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-sort_' . $x . '" value="' . $x . '" />';
                echo '<li><input type="hidden" class="upload-id" name="' . $this->field[ 'name' ] . '[' . $x . '][attachment_id]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_id_' . $x . '" value="" />';
                echo '<input type="hidden" class="upload" name="' . $this->field[ 'name' ] . '[' . $x . '][image]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_url_' . $x . '" value="" readonly="readonly" />';
                echo '<input type="hidden" class="upload-height" name="' . $this->field[ 'name' ] . '[' . $x . '][height]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_height_' . $x . '" value="" />';
                echo '<input type="hidden" class="upload-width" name="' . $this->field[ 'name' ] . '[' . $x . '][width]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-image_width_' . $x . '" value="" /></li>';
                echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field[ 'name' ] . '[' . $x . '][thumb]' . $this->field['name_suffix'] .'" id="' . $this->field[ 'id' ] . '-thumb_url_' . $x . '" value="" /></li>';
                echo '<li><a href="javascript:void(0);" class="button deletion redux-networks-remove">' . sprintf ( __ ( 'Delete %s', 'redux-framework' ), $this->field[ 'content_name' ] ) . '</a></li>';
                echo '</ul></div></fieldset></div>';
            }
            echo '</div><a href="javascript:void(0);" class="button redux-networks-add button-primary" rel-id="' . $this->field[ 'id' ] . '-ul" rel-name="' . $this->field[ 'name' ] . '[name][]' . $this->field['name_suffix'] .'">' . sprintf ( __ ( 'Add %s', 'redux-framework' ), $this->field[ 'content_name' ] ) . '</a><br/>';
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

			wp_enqueue_style( 'wp-color-picker' );
			
            wp_enqueue_script(
                'redux-field-media-js',
                ReduxFramework::$_url . 'assets/js/media/media' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );

            wp_enqueue_style (
                'redux-field-media-css', 
                ReduxFramework::$_url . 'inc/fields/media/field_media.css', 
                time (), 
                true
            );

            wp_enqueue_script (
                'redux-field-networks-js', 
                XT_ADMIN_URL . '/redux-extensions/extensions/networks/field_networks' . !Redux_Functions::isMin () . '.js', 
                array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'wp-color-picker'), 
                time (), 
                true
            );

            wp_enqueue_style (
                'redux-field-networks-css', 
                XT_ADMIN_URL . '/redux-extensions/extensions/networks/field_networks.css', 
                time (), 
                true
            );
            
            
            wp_enqueue_style(
	            'font-awesome',
				XT_ADMIN_URL . '/redux-extensions/extensions/fontawesome/css/font-awesome.min.css',
	            false,
	            '',
	            'all'
	        );

	        wp_enqueue_style(
	            'field_fontawsome.css',
	            XT_ADMIN_URL . '/redux-extensions/extensions/networks/field_fontawsome.css',
	            false,
	            '',
	            'all'
	        );
	        wp_enqueue_script(
	            'redux-opts-fontawesome-js',
	            XT_ADMIN_URL . '/redux-extensions/extensions/networks/jquery.fontawsome-select.js',
	            array('jquery'),
	            time(),
	            true
	        );
        
        }
    }

}