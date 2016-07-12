var XT = {};


(function($) {
  "use strict";

	XT.vars = {
		breakpoints: {
			mobile: 480,
			tablet: 782
		},
		bodyNiceScroll: null,
		menuNiceScroll: null,
		animating: false,
		pushMenuActive: false,
		animationEnd: "transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd"
	};

	XT.init = function() {
		
		XT.initClasses();
		XT.initFoundation();
		XT.initContentHeight();		
		XT.initBodyNiceScroll();
		XT.initTopMenu();
		XT.initSmoothScroll();
		XT.initMainSearch();
		XT.initMobileSearch();
		XT.initDropAnimations();
		XT.initAnimations();
		XT.initOffCanvasMenu();
		XT.initCategoryPostsMenu();
		XT.initUberMenu();
		XT.initLights();
		XT.initToggle();
		XT.initInputs();
		XT.initPagination();
		XT.initStickySidebar();
		XT.initPostNav();
		XT.initTwitter();
		XT.initComments();
		XT.initStackView();
		XT.initOrbit();
		XT.initEndlessArticles();
		XT.initStollJs();
		XT.initChosenSelect();
		XT.initLabelToPlaceholder();
		XT.initBackToTop();
		XT.initScrollToSection();
		XT.initParallax();
		XT.initBgVideos();
		XT.hideWidgetTitleIfEmpty();
		XT.initWoocommerce();
		XT.initCustomJS();

		$(window).load(function() {
			$(this).resize();
			$(this).scroll();
		});
		
	};
	
	XT.initClasses = function() {

		if($('.page-header').length > 0) {
			
			$('body').addClass('has-page-header');
		}
		
		if(XT.isTouch() || !XT.canDoAnimations()) {
			$('.wpb_animate_when_almost_visible').removeClass('wpb_animate_when_almost_visible');
        }    
            
	};

	XT.initFoundation = function() {
	
		$(document).foundation();
		
		if($('html').hasClass('no-flexbox')) {
			$('html').removeClass('flexbox');
		}
		
		var doc = document.documentElement;
		doc.setAttribute('data-useragent', navigator.userAgent);

	};
	
	
	XT.initBodyNiceScroll = function() {
	
		
		$(window).on("scroll mousedown DOMMouseScroll mousewheel keyup", function(e){
			
	       	if(XT.vars.animating)
	       		e.preventDefault();	
		});
   		
		if(XT.isTouch() || !xt_vars.enable_nice_scroll)
			return false;

		XT.vars.bodyNiceScroll = $("body").niceScroll({
			cursorborderradius: '3px', 
			railpadding: {top:0,right:1,left:0,bottom:0}, 
			cursorborder: 'rgba(255,255,255, 0.7)', 
			cursoropacitymax: 0.8,
			scrollspeed: 60,
			mousescrollstep: 60 
		});
		XT.initSmoothScroll(true);
	};

	XT.initTopMenu = function() {
	
		var initial_body_bgcolor = $('body').css('background-color');
		var initial_menu_bgcolor = $('.top-menu .top-bar .top-bar-section').css('background-color');
		var topbar_has_sticky = $('.top-menu').hasClass('sticky');
		
		$('.toggle-topbar.menu-icon').off('click');
		$('.toggle-topbar.menu-icon').on('click', function() {
			
			setTimeout(function() {
				
				if(XT.isMobileMenuOpen()) {
					
					if(topbar_has_sticky)
						$('.top-menu').removeClass('sticky');
					
					$('.hide-on-mobile-menu').fadeOut();
					$('body').css('background-color', initial_menu_bgcolor);
					
				}else{
					
					if(topbar_has_sticky)
						$('.top-menu').addClass('sticky');
						
					$('.hide-on-mobile-menu').fadeIn("fast");
					$('body').css('background-color', initial_body_bgcolor);
				}
				
			}, 50);
				
			setTimeout(function() {
				
				XT.initSmoothScroll(true);
				
			}, 500);	
		});	
		
		$(window).on('resize', function() {
		
			if($(this).width() > XT.vars.breakpoints.tablet) {	
				if(!$('#inner_wrapper').is(":visible")) {
					$('body').css('background-color', initial_body_bgcolor);
					$('#inner_wrapper').fadeIn();
				}
			}
		});
	};
	


	XT.initSmoothScroll = function (afterAjax, disable){
	
		if($('.enable_smooth_scroll').length > 0) 
			xt_vars.enable_smooth_scroll = true;
		
		if(XT.isTouch() || !xt_vars.enable_smooth_scroll)
			return false;
	
	    if ( $('.gambit_parallax_enable_smooth_scroll').length === 0) {

	        if ( typeof $.easing.easeOutQuint === 'undefined' ) {
	            $.extend(jQuery.easing, {
	                easeOutQuint: function(x, t, b, c, d) {
	                    return c * ((t = t / d - 1) * t * t * t * t + 1) + b;
	                }
	            });
	        }
	
	        var wheel = false,
	            $docH = $(document).height() - $(window).height(),
	            $scrollTop = $(window).scrollTop();
	
			var on_scroll = function() {
	            if (wheel === false) {
	                $scrollTop = $(this).scrollTop();
	            }
	        };
	        
	        var on_mousewheel = function(e, delta) {
	            delta = delta || -e.originalEvent.detail / 3 || e.originalEvent.wheelDelta / 120;
	            wheel = true;
	
	            $scrollTop = Math.min($docH, Math.max(0, parseInt($scrollTop - delta * 120)));
	            $(navigator.userAgent.indexOf('AppleWebKit') !== -1 ? 'body' : 'html').stop().animate({
	                scrollTop: $scrollTop + 'px'
	            }, 1600, 'easeOutQuint', function() {
	                wheel = false;
	            });
	            return false;
	        };

	        var on_resize = function() {
	        	XT.initSmoothScroll(true);
	        };
	        
	        var on_load = function() {
	        	setTimeout(function() {
					XT.initSmoothScroll(true);
				},1000);
	        };
	        

	    	$(window).off('scroll', on_scroll);
	    	$(document).off('DOMMouseScroll mousewheel');

			if(disable)
				return false;
		
	        $(window).on('scroll', on_scroll);
	        $(document).on('DOMMouseScroll mousewheel', on_mousewheel);

			
			if(afterAjax)
				return;
			
			$( document ).ajaxComplete(function() {
			 	setTimeout(function() {
					XT.initSmoothScroll(true);
				},500);
			});
			
			
			$(window).off('resize', on_resize);
			$(window).on('resize', on_resize);
		
			$(window).off('load', on_load);
			$(window).on('load', on_load);

	    }

	};

	
	XT.initParallax = function (){
			
		$(".vc_row.has-parallax").each(function() {
			
			var bgimage = $(this).css('background-image');

			if(bgimage && bgimage !== '' && bgimage !== 'none') {
	
				// Parallax Fix for Mobile Devices
				if(XT.isTouch()) {
					$(this).css({'background-attachment': 'scroll'});
				}
			
			}
			
		});

		setTimeout(function() {
			
			$(".vc_row.has-parallax").parallax("50%", 0.15, true);
			$(".vc_row.has-parallax,.vc_row.has-overlay").addClass('loaded');
		
		}, 10);

	};

	XT.initBgVideos = function (){


		setTimeout(function() {
	
			$(".vc_row.has-video").each(function() {
			
				var row = $(this);
				
			    //var preloader = row.find('.preloader');
			
			    function checkLoad() {
			    
			    	var video = row.find('.row-video');
			    	
			        if ($(video).get(0).readyState === 4) {
			        
						/*
			            preloader.fadeOut(function() {
			            	$(this).remove();
			            });	
						*/

						var row_height = row.outerHeight(true);
						
						video = row.find('video.row-video');
						var video_position = video.data('position');
						var video_height = video.outerHeight(true);
						var video_top = 0;
						
						if(video_position === 'bottom') {
						
							if(video_height > row_height)
								video_top = (video_height - row_height);
							else
								video_top = (row_height - video_height);	
							
						}else if(video_position === 'middle') {
						
							if(video_height > row_height)
								video_top = (video_height - row_height) / 2;
							else
								video_top = (row_height - video_height) / 2;	
						}
						
						video.css('margin-top', -video_top);
						
						row.addClass('loaded');
				
				
			        } else {
			        
			            setTimeout(checkLoad, 100);
			        }
			    }
			
			    checkLoad();

			});	
			
		},800);	
	};
		
	XT.isMobileMenuOpen = function() {
	
		return $('.top-menu .top-bar').hasClass('expanded');	
	};
	
	XT.initOffCanvasMenu = function() {
		
		var wrapper = '#wrapper';
		var off_canvas_wrap = '.off-canvas-wrap';
		var off_canvas = '.off-canvas-menu';
		var top_menu = '.top-menu';
		var main_menu = '.main-menu';
		var top_menu_fixed = '.top-menu.sticky.fixed';
		var main_menu_fixed = '.main-menu.sticky.fixed';
		var toggle_sidebar = '.toggle-sidebar a';
		var exit_off_canvas = '.exit-off-canvas';

		XT.vars.pushMenuActive = false;
		
		update_pushmenu_position();
		update_sticky_topmenu_position();
		update_sticky_mainmenu_position();
		

		$(toggle_sidebar).off('click', on_toggle_sidebar);
		$(toggle_sidebar).on('click', on_toggle_sidebar);	
		
		$(exit_off_canvas).off('click', on_close_menu);
		$(exit_off_canvas).on('click', on_close_menu);

		function on_toggle_sidebar(e) {
			
			if(XT.vars.pushMenuActive) {
				
				on_close_menu();
				
			}else{
			
				if(XT.vars.bodyNiceScroll) {
					XT.vars.bodyNiceScroll.hide();
				}else{
					$('body').css('overflow', 'hidden');
				}
		
				XT.initSmoothScroll(false, true);
				
				$(off_canvas_wrap).removeClass('closing closed').addClass('opening');
				
				update_pushmenu_position();
				update_sticky_topmenu_position();
				update_sticky_mainmenu_position();
				
				if(XT.canDoAnimations()) {

					$(wrapper).one(XT.vars.animationEnd, process_update);
					
				}else{
					
					process_update();
				}	
				
				function process_update() {
		
					update_toggle_icon();
					update_pushmenu_position();
					update_sticky_topmenu_position();
					update_sticky_mainmenu_position();
					XT.vars.pushMenuActive = true;	
					
					setTimeout(function() {
						
						$(off_canvas_wrap).removeClass('closed opening').addClass('opened');
						
					},50);
				}
			}
		}
		
		function on_close_menu() {
		
			$('body').css('overflow', '');

			$(off_canvas_wrap).removeClass('opening opened').addClass('closing');
			
			if(XT.canDoAnimations()) {
				
				$(wrapper).one(XT.vars.animationEnd, process_close);
				
			}else{
				
				process_close();
			}
			
			function process_close() {
				
				reset_toggle_icon();
				reset_sticky_topmenu_position();
				reset_sticky_mainmenu_position();
			
				if(XT.vars.bodyNiceScroll) {
					XT.vars.bodyNiceScroll.show();
				}
				XT.initSmoothScroll(true);
				
				XT.vars.pushMenuActive = false;
				
				setTimeout(function() {
				
					$(off_canvas_wrap).removeClass('opened closing').addClass('closed');
					
				},50);
				
			}

		}

		function update_toggle_icon() {
			
			$(toggle_sidebar).find('i').removeClass('fa-bars').addClass('fa-times');
		}
		
		function update_pushmenu_position() {
			$(off_canvas).css('top', $(window).scrollTop());
		}
		
		function update_sticky_topmenu_position() {
			if($(top_menu_fixed).length > 0)
				$(top_menu_fixed).css('top', $(window).scrollTop());
		}
		
		function update_sticky_mainmenu_position() {

			if($(main_menu_fixed).length > 0) {
			
				var top = $(window).scrollTop();
				
				if($(top_menu_fixed).length > 0) {
				
					top = top + $(top_menu_fixed).height();
				}
				
				$(main_menu_fixed).css('top', top);		
			}	
		}
		
		function reset_toggle_icon() {
			
			$(toggle_sidebar).find('i').removeClass('fa-times').addClass('fa-bars');
		}
		
		function reset_sticky_topmenu_position() {
			
			if($(top_menu).length > 0) {
				$(top_menu).removeAttr('style');
			}	
		}
		
		function reset_sticky_mainmenu_position() {

			if($(main_menu).length > 0) {
				$(main_menu).removeAttr('style');
			}	
		}

		$(off_canvas+' .dropdown').each(function() {
			
			var dropdown = $(this);
			var label = dropdown.prev().html();
			dropdown.prepend('<li class="has_label"><label>'+label+'</label></li>');

		});


	};
	
	XT.initCategoryPostsMenu = function() {
		
		$('.category_posts_sub_menu').each(function() {
			
			$(this).addClass('animate-drop slide-top');
			$(this).detach().prependTo('#main-header');

		});
		
		var timer;
		var delay = 50;
		var menu_active = false;
		
		$('.menu-item.has-category-dropdown').on('mouseenter', function() {
			
			if(timer)
				clearTimeout(timer);
				
			var menu_id = $(this).data('objectid');

			$('#category_posts_sub_menu_'+menu_id).off('mouseenter');
			$('#category_posts_sub_menu_'+menu_id).on('mouseenter', function() {

				$(this).addClass('open').removeClass('close');
				
				XT.lightsOff();
				
				menu_active = true;
			});
			
			timer = setTimeout(function() {

				$('.category_posts_sub_menu').removeClass('open').addClass('close');
				$('#category_posts_sub_menu_'+menu_id).removeClass('close').addClass('open');
				
				XT.lightsOff();
				
			}, delay);
		});
		
		$('.menu-item.has-category-dropdown').on('mouseleave', function() {
			
			if(timer)
				clearTimeout(timer);
				
			var menu_id = $(this).data('objectid');
			
			timer = setTimeout(function() {
			
				if(menu_active)
					return false;
					
				$('#category_posts_sub_menu_'+menu_id).removeClass('open').addClass('close');
				
				XT.lightsOn();
				
				$('#category_posts_sub_menu_'+menu_id).off('mouseenter');
				
			}, delay);
			
			$('#category_posts_sub_menu_'+menu_id).off('mouseleave');
			$('#category_posts_sub_menu_'+menu_id).on('mouseleave', function() {
			
				var box = $(this);
				box.removeClass('open');
				
				setTimeout(function() {
					box.addClass('close');
				}, 300);
					
				XT.lightsOn();
				
				$('#category_posts_sub_menu_'+menu_id).off('mouseenter');
				
				menu_active = false;
				
			});

		});
		
		$('.category_submenu_post a').off('click');

	};

	XT.initUberMenu = function() {
	
		var $ubermenu = $('.ubermenu-loc-main-menu');
		
		if($ubermenu.length > 0) {
			
			$ubermenu.find('.ubermenu-item-has-children').on({
			    mouseenter: function(){
			       
			        XT.lightsOff();
			      
			    },
			    mouseleave: function(){
				    
			        XT.lightsOn();
			
			    }
			});
		
		}
		
	};
	
		
	XT.initMainSearch = function() {
	
		var doneAutoType = false;
		$('.main-menu ul.search .search-button').on('click', function(e) {
		
			e.preventDefault();
			
			var input = $(this).parent().find('.search-input');

			if(input.val() === '' && !doneAutoType && !input.hasClass('autotyping')) {
				var placeholder = input.attr("placeholder");
				input.addClass('autotyping');
				input.autotype(placeholder, {delay: 80}).bind('autotyped', function(){
				
					setTimeout(function() {
						input.val('').focus();
						doneAutoType = true;
						input.removeClass('autotyping');
					},50);
				   
				});
			}else if(input.val() === ''){
				input.focus();
			}else{
				$(this).closest('form').submit();
			}

		});
				
		$('.main-menu ul.search .search-close-button').on('click', function(e) {
			
			e.preventDefault();
			
			var input = $(this).parent().find('.search-input');
			input.val('').blur();
		});

		
		$('.main-menu ul.search .search-input').on('keypress change blur', function() {
			
			var $input = $(this);
			var $searchwrap  = $input.closest('ul.search');
			
			setTimeout(function() {
				
				var searchval = $input.val();
				if(searchval !== '') {
					
					$searchwrap.addClass('is-searching');
					$('body').addClass('is-searching');
					
				}else{
					
					$searchwrap.addClass('is-closing');
					setTimeout(function() {
						$searchwrap.removeClass('is-searching is-closing');
						$('body').removeClass('is-searching is-closing');
						
					},1);	
				}
					
			},10);
			
		}).trigger('keypress');
		
		
		$('.main-menu ul.search .search-input').on('focus', function() {
		
			var $searchwrap  = $(this).closest('ul.search');
			$searchwrap.addClass('is-searching');
			$('body').addClass('is-searching');
			
		});
		
		$('.main-menu ul.search form').on('submit', function(e) {
			
			var input = $(this).find('.search-input');
			var searchval = input.val();
			
			if(searchval === '' || input.hasClass('autotyping')) {
				e.preventDefault();
				input.focus();
			}
			
		});

	};
	
	
	XT.initMobileSearch = function() {
		
		var $search = $('.search.show-for-small-only');
		
		if($search.length > 0) {
		
			var $search_input = $search.find('.search-input');
			var $search_button = $search.find('.search-button');
			var $search_close_button = $search.find('.search-close-button');
			
			$search_button.on('click', function(e) {
				
				if(!$search_input.closest('.has-form').hasClass('open')) {
					e.preventDefault();
					e.stopPropagation();
					$search_button.parent().removeClass('small-centered');
					$search_button.closest('.has-form').addClass('open');
					$search_input.focus();
				}else{
					$search_button.closest('form').submit();
				}
		
			});
			
			$search_input.on('blur', function() {
				if($(this).val() === '') {
					$(this).closest('.has-form').removeClass('open');
				}	
			});
			
			$search_close_button.on('click', function(e) {
				e.preventDefault();
				$search_input.val('').blur();
			});
		}	
	
	};
	
	XT.initDropAnimations = function() {
		
		var timeout;
		$('.animate-drop').each(function() {
			$(this).closest('.has-dropdown').on('mouseover', function() {
				$('.animate-drop').closest('.has-dropdown').not($(this)).addClass('close').removeClass('open active');
				clearTimeout(timeout);
				$(this).removeClass('close').addClass('open active');
			});
			$(this).closest('.has-dropdown').on('mouseleave', function() {
				var drop = $(this);
				timeout = setTimeout(function () {
					drop.addClass('close');
					drop.removeClass('open active');
				}, 300);	
			});
		});
	};

	XT.initAnimations = function() {
	
		$('.animated').each(function() {
			
			var $elem = $(this);
			var animation = $elem.data('animation');

			$elem.removeClass('hidden-for-small-up');
			$elem.addClass(animation);	
			
		});
	};

	XT.initToggle = function() {
		
		$('a.toggle,button.toggle').off('click');
		$('a.toggle,button.toggle').on('click', function(e) {
			
			var $toggle = $(this);
			var target = $toggle.data('toggle');
			var callbacks = $toggle.data('callback');
	     
			if($(target).hasClass('in')) {
				$(target).hide().removeClass('in');
			}else{
				$(target).addClass('in').show();
	
				if (callbacks) {
	                callbacks = callbacks.split(',');
	                for (var i = 0; i < callbacks.length; i++) {
	                    var callback = XT[callbacks[i]];
	                    if (typeof callback === 'function') {
	                        callback($toggle, target);
	                    }
	                }
	            }
	                			
			}
			
		});	
	};
	
	XT.initInputs = function(afterAjax) {

		$('.has-prefix').each(function() {
			var $input = $(this);
			$input.on('focus', function() {
				$(this).closest('.prefix-wrap').addClass('focused');
			});
			$input.on('blur', function() {
				$(this).closest('.prefix-wrap').removeClass('focused');
			});
		});	
		$('.has-postfix').each(function() {
			var $input = $(this);
			$input.on('focus', function() {
				$(this).closest('.postfix-wrap').addClass('focused');
			});
			$input.on('blur', function() {
				$(this).closest('.postfix-wrap').removeClass('focused');
			});
		});	
		
		$('input[type="file"]').each(function() {
			$(this).on('change', function() {
				var value = $(this).val();
				var html = '<span style="display:block;margin-bottom:10px;" class="file-input-value">'+value+'</span>';
				$(this).parent().find('.file-input-value').remove();
				$(this).parent().append(html);
			});
		});
		
		if(afterAjax)
			return;
			
		$( document ).ajaxComplete(function() {
		 	XT.initInputs(true);
		});

	};
	
	XT.initWoocommerce = function() {

		initQuantities();
		initSecondImage();

			
		$( document ).ajaxComplete(function() {

			initQuantities();
			initSecondImage();
			
		});
		
		function initQuantities() {
			
					 	
		 	$( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).each(function() {
		 		$(this).addClass( 'buttons_added' ).append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );
		 	});	
		
			// Target quantity inputs on product pages
			$( 'input.qty:not(.product-quantity input.qty)' ).each( function() {
				var min = parseFloat( $( this ).attr( 'min' ) );
		
				if ( min && min > 0 && parseFloat( $( this ).val() ) < min ) {
					$( this ).val( min );
				}
			});

			
			$( document ).off( 'click', '.plus, .minus');
			$( document ).on( 'click', '.plus, .minus', function() {

				// Get values
				var $qty		= $( this ).closest( '.quantity' ).find( '.qty' ),
					currentVal	= parseFloat( $qty.val() ),
					max			= parseFloat( $qty.attr( 'max' ) ),
					min			= parseFloat( $qty.attr( 'min' ) ),
					step		= $qty.attr( 'step' );
		
				// Format values
				if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
				if ( max === '' || max === 'NaN' ) max = '';
				if ( min === '' || min === 'NaN' ) min = 0;
				if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;
		
				// Change the value
				if ( $( this ).is( '.plus' ) ) {
		
					if ( max && ( max === currentVal || currentVal > max ) ) {
						$qty.val( max );
					} else {
						$qty.val( currentVal + parseFloat( step ) );
					}
		
				} else {
		
					if ( min && ( min === currentVal || currentVal < min ) ) {
						$qty.val( min );
					} else if ( currentVal > 0 ) {
						$qty.val( currentVal - parseFloat( step ) );
					}
		
				}
		
				// Trigger change event
				$qty.trigger( 'change' );
				
				var form = $(this).closest('form');
				var params = form.serialize();
				$('.mini-cart').addClass('loading');
				$.ajax({
					url: xt_vars.ajaxurl,
					data: params,
					type: 'post', 
					success: function() {
						$.ajax($fragment_refresh);
					}
				});	
				
			});
	
	
		}
		
		
		function initSecondImage() {

			$( 'ul.products li.has-gallery a:first-child' ).each(function() {
			
				$(this).hover(function() {
					$( this ).children( '.wp-post-image' ).removeClass( 'fadeInDown' ).addClass( 'animated fadeOutUp' );
					$( this ).children( '.secondary-image' ).removeClass( 'fadeOutUp' ).addClass( 'animated fadeInDown' );
				}, function() {
					$( this ).children( '.wp-post-image' ).removeClass( 'fadeOutUp' ).addClass( 'fadeInDown' );
					$( this ).children( '.secondary-image' ).removeClass( 'fadeInDown' ).addClass( 'fadeOutUp' );
				});
				
			});
		}
	};
	
	XT.initPagination = function() {
	
		XT.XHR = {
	        settings: {
	            url: xt_vars.ajaxurl,
	            type: 'POST'
	        }
	    };
    
	    XT.pagination = {
	        XHR: {},
	        $: {},
	        loadingClass: 'ajax-load',
	        loadedClass: 'ajax-loaded',
	        ajaxBusy: false,
	        isotope: false
	    };
	
	    XT.pagination.XHR = {
	        done: function(response, status, xhr) { // success : data, status, xhr
	
	            var PAGING = XT.pagination;
	
	            if (response)
	            {
	            
	            	if(PAGING.isotope) {
		            	var $newItems = $(response);
						PAGING.$.container.append( $newItems ).isotope( 'addItems', $newItems ).isotope( 'reLayout' );
						PAGING.$.container.fadeIn();
	            	}else{
	                	PAGING.$.container.append(response).fadeIn();
					}
					
					var newMoreButton = PAGING.$.container.find('.button-more');
					PAGING.$.waiting = $('.wait');
					
					if(newMoreButton.length > 0) {
						PAGING.$.loadButton.replaceWith(newMoreButton[0].outerHTML );
						newMoreButton.remove();
						PAGING.$.loadButton = $('.button-more');
						
					}else{
						PAGING.$.loadButton.parent().remove();
						if(PAGING.$.waiting)
							PAGING.$.waiting.remove();
					}
	
	
	                PAGING.ajaxBusy = false;
	
	                //XT.initTouchNav();
	
	                var callbacks = PAGING.$.loadButton.data('callback');
	                if (callbacks) {
	                    callbacks = callbacks.split(',');
	
	                    for (var i = 0; i < callbacks.length; i++)
	                    {
	                        var callback = XT[callbacks[i]];
	
	                        if (typeof callback === 'function') {
	                            callback();
	                        }
	                    }
	                }
	
	                if (PAGING.method === 'paginate_scroll') {
	
	                    $(window).on('scroll', function(event) {

	                        if (!PAGING.ajaxBusy) {
	                            var $win = $(this),
	                                $doc = $(document),
	                                $foot = $('body footer');
	
	                            if ($win.scrollTop() >= ($doc.height() - $win.height() - ($foot.height()))) {
	                                PAGING.$.loadButton.click();
	                            }
	                        }
	                    });
	
	                } else {
	                    PAGING.$.loadButton.css('visibility', 'visible').fadeIn();
	                }
	                
					XT.updatePageInfo('', XT.XHR.settings.url);
	                XT.initAjaxBlocksLoadEvent();
	
	            } else {
	
	                PAGING.$.loadButton.parent().remove();
	                if(PAGING.$.waiting)
	                	PAGING.$.waiting.remove();
	                	
	                PAGING.XHR.fail(xhr, 'error', '');
	            }
	        }, 
	        fail: function(xhr, status, error) { // error : xhr, status, error
	
	            var PAGING = XT.pagination;
	
	            setTimeout(function() {
	            	PAGING.$.container.append(error).fadeIn();
	            }, 100);
	        }, 
	        always: function() { // complete : data|xhr, status, xhr|error
	
	            var PAGING = XT.pagination;
	            PAGING.$.loadButton.prop('disabled', false);
	
	            PAGING.$.container.removeClass(PAGING.loadingClass);
				PAGING.$.container.addClass(PAGING.loadedClass);
	
	            if (XT.vars.bodyNiceScroll && xt_vars.enable_nice_scroll && !XT.isTouch())
	                XT.vars.bodyNiceScroll.resize();
	        },
	        before: function(xhr) {
	
	            var PAGING = XT.pagination;
	            PAGING.$.container.addClass(PAGING.loadingClass);
	            PAGING.$.container.removeClass(PAGING.loadedClass);
	            PAGING.$.loadButton.prop('disabled', true);
	        }
	    };
	    
	    XT.initAjaxBlocksLoad();
	
	};
	
	XT.initAjaxBlocksLoad = function() {

        XT.pagination.XHR.request = {
            dataType: 'text',
            data: {
	            ajax: 1
            },
			beforeSend: XT.pagination.XHR.before
        };

        XT.pagination.XHR.request = $.extend(true, XT.pagination.XHR.request, XT.XHR.settings);
		XT.initAjaxBlocksLoadEvent(); 

        $('a.button-more').trigger('click');

    };
    
    XT.initAjaxBlocksLoadEvent = function() {
	    

		$(document).off('click', 'a.button-more');
        $(document).on('click', 'a.button-more', function(e) {
            e.preventDefault();

            var PAGING = XT.pagination;
            var $this = $(this);

			PAGING.isotope = ($('.isotope-wrap').length > 0);
		
            if (PAGING.ajaxBusy)
                return;

            PAGING.$.loadButton = $this;
            PAGING.$.container = $('#' + PAGING.$.loadButton.data('rel'));
            XT.pagination.XHR.request.url = PAGING.$.loadButton.attr('href');
            XT.XHR.settings.url = PAGING.$.loadButton.attr('href');

            PAGING.method = $this.data('paginate');

            $.ajax(PAGING.XHR.request)
                    .done(PAGING.XHR.done)
                    .fail(PAGING.XHR.fail)
                    .always(PAGING.XHR.always);
        });	    
    };
		
	XT.initStackView = function() {
	
		$('.stack').each(function() {
		
			var stack = $(this),
				view = stack.find('.stack_view'),
				listwrap = view.find('.stack_wrap'),
				list = listwrap.find('li'),
				prev = stack.find('.controls .prev'),
				next = stack.find('.controls .next');
			
			var z_index = list.length,
				current_index = 1,
				current;
	
	
			list.each(function() {
	
				this.style['z-index'] = z_index;
		
				$(this).removeClass('visible');

				z_index--;
				
			});

			next.bind('click', function(e) {
				e.preventDefault();
				if($(this).attr('disabled')) return false;
				list.removeClass('visible');
				current = list.filter(':nth-child(' + (current_index + 1) + ')');
				current.addClass('visible');
				listwrap.css('height', current.outerHeight());
				current_index ++;
				check_buttons();
			});
			
			prev.bind('click', function(e) {
				e.preventDefault();
				if($(this).attr('disabled')) return false;
				list.removeClass('visible');
				current = list.filter(':nth-child(' + (current_index - 1) + ')');
				current.addClass('visible');
				listwrap.css('height', current.outerHeight());
				current_index --;
				check_buttons();	
			});

			function check_buttons() {
				if(current_index === 1) {
					prev.attr('disabled', true);
				}
				else {
					prev.attr('disabled', false);
				}
				
				if(current_index === list.length) {
					next.attr('disabled', true);				
				}
				else {
					next.attr('disabled', false);
				}
			}


			function on_load() {
				current = list.filter(':nth-child(' + (current_index) + ')');
				current.addClass('visible');
				listwrap.css('height', current.outerHeight());
				stack.addClass('loaded');
			}
			
			setTimeout(on_load, 100);
			$(window).on('load', on_load);
		
		});
	
	};
	
	XT.initOrbit = function() {

		function on_load() {
			$('.orbit-wrap').each(function() {
				$(this).addClass('loaded');
				$(this).find('.orbit-caption').addClass('gradient-wrap');
			});
		}
					
		setTimeout(on_load, 100);
		$(window).on('load', on_load);
	};
	
	XT.initEndlessArticles = function() {
		
		if($('.tpl-endless-articles').length === 0)
			return false;

		var updatestate = $('#endless-articles-wrapper').data('updatestate');
		var location_hash = location.hash;
		var has_hash = location_hash ? true : false;
		var $sidebar = $('.tpl-endless-articles .sidebar');
		var $nav = $sidebar.find('.tabs-content .content .news-list');
		var $active_nav = getActiveNav();
		var $tabs = $sidebar.find('.tabs-content .tabs');
		var $content = $('.tpl-endless-articles #inner_wrapper');
		var $active_content = getActiveContent();
		var w_width = $(window).width();
		var w_height = $(window).height() - $('header').height();
		var bottom_offset = 84;
		var items = $("article");
		var loading = false;
		var nomore_prev = [];
		var nomore_next = [];
		var page = 1;


		if(has_hash) {
			XT.vars.animating = true;
		}
	
		// Add nice scroll to tab sidebars and main content

		$('html,body').css('overflow', 'hidden').scrollTop(0);
		
		var main_scroll = $content.niceScroll();

		setTimeout(function() {
		
			$nav.each(function() {
				var nav_scroll = $(this).niceScroll();
				$(nav_scroll.rail[0]).detach().appendTo($sidebar).addClass('sidebar-scroll');
			});	

			
		}, 20);
		
		setTimeout(function() {
			
			$(main_scroll.rail[0]).addClass('main-scroll');
			$(window).resize();
			
		},150);


		initOnResize();
		initOnTabToggled();
		initOnNavClick();
		initOnScroll();
		initActiveArticleOnScroll();
		initOnLoaded();
		

		function initOnResize() {
			
			$(window).off('resize');
			$(window).on('resize', function() {
				var w_width = $(window).width();
				w_height = $(this).height() - $('header').height();
				var sidebar_parent_w = $sidebar.parent().width();
				$sidebar.height(w_height);
				$sidebar.width(sidebar_parent_w);
				$content.height(w_height);
				
				if(w_width > XT.vars.breakpoints.tablet) {
					$content.find('.endless-articles').parent().css('margin-left', sidebar_parent_w + 45);
				}else{
					$content.find('.endless-articles').parent().css('margin-left', 'inherit');
				}
	
				$nav.each(function() {
					var nav_scroll = $(this).getNiceScroll();
					nav_scroll.resize();
					$(this).height(w_height - bottom_offset);
				});	
				main_scroll.resize();
	
			});
			$(window).resize();
		}
		
		
		function initOnTabToggled() {
			
			$('.tpl-endless-articles .sidebar .tabs').off('toggled');
			$('.tpl-endless-articles .sidebar .tabs').on('toggled', function (event, tab) {

				setTimeout(function() {
				
					$active_nav = getActiveNav();

					$(tab).find('.news-list').getNiceScroll().resize();
					$(tab).find('.news-list li').removeClass('past');
					setTimeout(function() {
						$(tab).find('.news-list li').removeClass('past');
					}, 100);
	
		
					var target = $(tab).attr('id');
	
					$('.tabs-fullcontent').removeClass('active');
					target = '#'+target+'-tabs-fullcontent';
					$(target).addClass('active');
					
					$active_nav.find('li.active a').data('force', 1).click().removeData('force');
					
					initOnScroll();
					initActiveArticleOnScroll();

				}, 20);

			
			});
		
		}
		
		
		function initOnNavClick() {
			
			$nav.find('li a').off('click');
			$nav.find('li a').on('click', function(e) {
			
				e.preventDefault();
				$active_nav = getActiveNav();
				$active_content = getActiveContent();
				
				
					
				var $link = $(this);
				var force = $link.data('force');
				var skipstate = $link.data('skipstate');
				var link_post_id = $link.data('postid');
				
				$active_nav.find('li').removeClass('active');
				$link.parent().addClass('active');
				
				var article_index = $link.parent().index();
				var active_article = $active_content.data('active');
				
				if(active_article !== article_index || force === 1) {
					
					$active_content.data('active', article_index);
					var $article = $active_content.find('article').eq(article_index);
					if($article) {
						var article_post_id = $article.data('postid');
					
						if(link_post_id !== article_post_id) {
							location.href = $link.attr('href');
							return false;
						}	
					}
					
					var article_position = 0;
					for(var i = 0 ; i < article_index ; i++) { 
						
						var article = $active_content.find('article').eq(i);
						article_position += article.outerHeight();
					}

					if(force === 1) {
					
						$content.scrollTop(article_position);
						
						if(skipstate !== 1 && updatestate)
							XT.updatePageInfo($link.text(), $link.attr('href'), '');
						
					}else{
					
						XT.vars.animating = true;
				
						$content.animate( { scrollTop: article_position + 'px' }, 700, function() {
						
							setTimeout(function() {
								XT.vars.animating = false;
								
								if(skipstate !== 1 && updatestate)
									XT.updatePageInfo($link.text(), $link.attr('href'), '');
									
							},100);
							
						});
					}
				
				}
			});
		
		}	
		
		
		function initOnScroll() {

			var lastNavScrollTop = 0;
			var lastContentScrollTop = 0;
			var navDirection = '';
			var contentDirection = '';
			var mousewheelDela = 30;
			
			
			$active_nav = getActiveNav();
			$active_nav.off('scroll');
			$active_nav.on('scroll', function() {

				var top = $(this).scrollTop();
				if (top > lastNavScrollTop){
				   navDirection = 'down';
				} else {
				   navDirection = 'up';
				}
				lastNavScrollTop = top;

		        if($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
		        
		           	loadMore('nav', 'prev');
		           
		        }
		        
		    });

		    $content.off('scroll');
		    $content.on('scroll', function(event) {
		    
		    	if(XT.vars.animating)
	       			event.preventDefault();	
	       		
		    	var top = $(this).scrollTop();
				if (top > lastContentScrollTop){
				   contentDirection = 'down';
				} else {
				   contentDirection = 'up';
				}
				lastContentScrollTop = top;

		        if($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
		        
		           loadMore('content', 'prev');
		           
		        }else if(contentDirection === 'up' && $(this).scrollTop() === 0) {
		        	
		        	//loadMore('content', 'next');
		        }
		    });

		    		    
		    $active_nav.off('mousewheel');
		    $active_nav.on('mousewheel', function(event, delta) {
		    
			    if(event.originalEvent.wheelDelta > mousewheelDela && $(this).scrollTop() === 0) {
			    
				    //loadMore('nav', 'next');
				    
			    }else if(event.originalEvent.wheelDelta < mousewheelDela && $(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
				    
				    loadMore('nav', 'prev');
			    }
			});
			
					    
		    $content.off('mousewheel');
		    $content.on('mousewheel', function(event, delta) {
		    
		    	if(XT.vars.animating)
	       			event.preventDefault();	
	       			
			    if(event.originalEvent.wheelDelta > mousewheelDela && $(this).scrollTop() === 0) {
			    
				    //loadMore('content', 'next');
				    
			    }else if(event.originalEvent.wheelDelta < mousewheelDela && $(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
				    
				    loadMore('content', 'prev');
			    }
			});
	    
		}	
		
		function initActiveArticleOnScroll() {
			
			$active_nav = getActiveNav();
			$active_content = getActiveContent();
			$active_content.find('article').off();
		    $active_content.find('article').on('inview', function(event, isInView, visiblePartX, visiblePartY) {
				
				if (isInView) {

					// element is now visible in the viewport
					if (visiblePartY === 'bottom')  {
					 
					 	var target_id = $(this).data('postid');
					 	var target = $active_nav.find('[data-postid="'+target_id+'"]').parent();
					 	
					 	if(!$(target).hasClass('active') && !XT.vars.animating) {
					
						 	XT.vars.animating = true;
						 	
					 		// set active state
					 		$active_nav.find('li').removeClass('active');
							$(target).addClass('active');
					
					
							var article_index = $(target).index();
							var article_position = 0;
							for(var i = 0 ; i < article_index ; i++) { 
								
								var article = $active_nav.find('li').eq(i);
								article_position += article.outerHeight();
							}
							
							
							$active_nav.animate( { scrollTop: article_position+ 'px' }, 700, function() {
								
								XT.vars.animating = false;
								
								// set active page title / url
								var $link = $(target).find('a');
								
								if(updatestate)
									XT.updatePageInfo($link.text(), $link.attr('href'), '');
							});
					
					    }  
					    
					}
				}
			});
			
		}	
		
		function initOnLoaded() {
			
	
			$('#endless-articles-wrapper').addClass('loaded');
			
			$active_content = getActiveContent();
			$active_nav = getActiveNav();
			
			$nav.each(function() {
				
				$(this).find('li').first().addClass('active');
			});
			
			if(has_hash) {
			
				$('body.admin-bar.admin-bar-bottom').css({'margin-top': 0, 'padding-bottom': 0});
				
				$active_content.find(location_hash).find('.comments-toggle').trigger('click');
				XT.vars.animating = false;
				
			}
		

			var popped = ('state' in window.history), initialURL = location.href;
			$(window).on('popstate', function(event){
			    var initialPop = !popped && location.href === initialURL;
			    popped = true;
			    if ( initialPop ) return;
			    var state = event.originalEvent.state;
			
			    $active_nav = getActiveNav();
				var $link = $active_nav.find("[href='"+location.href+"']");
				if($link.length > 0) {
					$link.data('skipstate', 1).trigger('click').removeData('skipstate');
				}
			});
			
		
		}
		
		function getActiveNav() {
		
			return $('.tpl-endless-articles .sidebar .tabs-content .content.active .news-list');
		}
		
		function getActiveContent() {
		
			return $content.find('.tabs-fullcontent.active');
		}
		
		function showLoading(container, direction) {
			
			loading = true;
			$active_nav = getActiveNav();
			$active_content = getActiveContent();
			
			var loading_html = '<div class="wait mini">';
			loading_html += '<div class="double-bounce1"></div>';
			loading_html += '<div class="double-bounce2"></div>';
			loading_html += '</div>';
	
			var nav_loading = '<li class="loading ajax-load">'+loading_html+'</li>';
			var content_loading = '<article class="loading ajax-load">'+loading_html+'</article>';
			
			if(direction === 'prev') {
				$active_nav.append(nav_loading);
				$active_content.append(content_loading);
			}else{	
				$active_nav.prepend(nav_loading);
				$active_content.prepend(content_loading);
			}		
			
			$active_nav.addClass("loading");	
			$active_content.addClass("loading");
			
			var scrollTop; 
			
			if(container === 'nav') {
			
				scrollTop = $active_nav[0].scrollHeight;
				if(direction === 'next')
					scrollTop = 0;
					
				XT.vars.animating = true;	
				$active_nav.animate( { scrollTop: scrollTop}, 700, function() {
					XT.vars.animating = false;
				});
			
			}else if(container === 'content') {
			
				scrollTop = $content[0].scrollHeight;
				if(direction === 'next')
					scrollTop = 0;
				
				XT.vars.animating = true;
				$content.animate( { scrollTop: scrollTop}, 700, function() {
					XT.vars.animating = false;
				});
				
			}	
		}
		
		function hideLoading(container, direction) {
		
			$active_nav = getActiveNav();
			$active_content = getActiveContent();

			XT.vars.animating = true;
			var $nav_loading = $active_nav.find('li.loading');
			$nav_loading.animate({opacity:0, height:0}, 500, function() {
				
				if(container === 'nav') {
					
					var index = $nav_loading.index();
					var nav_top = 0;
					var article;
					
					$nav_loading.remove();
					
					for(var i = 0 ; i < index ; i++) { 
						
						article = $active_nav.find('li').eq(i);
						nav_top += article.outerHeight();
					}
							
					$active_nav.animate({scrollTop: nav_top }, 700, function() {
						
						$active_nav.removeClass('loading');
						XT.vars.animating = false;
						var next_article = article.next();
						if(next_article)
							next_article.find('a').trigger('click');
					});
					
				}else{
				
					$nav_loading.remove();
					$active_nav.removeClass('loading');
					XT.vars.animating = false;
				}	

			});
			
			
				
			XT.vars.animating = true;	
			var $content_loading = $active_content.find('article.loading');
			$content_loading.animate({opacity:0, height:0}, 500, function() {

				if(container === 'content') {
					
					var content_top = $content_loading.position().top;
					
					$content.animate({scrollTop: content_top }, 700, function() {
					
						$content_loading.remove();
						$active_content.removeClass('loading');
						XT.vars.animating = false;
					});
					
				}else{
					
					$content_loading.remove();
					$active_content.removeClass('loading');
					XT.vars.animating = false;
					
				}
			});
			
			
			
			setTimeout(function() {
				loading = false;
			},1000);
			
		}
		
		function loadMore(container, direction) {
	
			$active_nav = getActiveNav();
			$active_content = getActiveContent();
			var tabid = $active_nav.closest('.content').index();
			var action = $active_nav.closest('.content').data('action');
			var settings = $active_nav.closest('.content').data('settings');
			
			if(
				loading || (XT.vars.animating && container === 'nav') ||
				(nomore_prev[tabid] && direction === 'prev') || 
				(nomore_next[tabid] && direction === 'next')
			) {
				return false;
			}
			
			
			page++;

		
			showLoading(container, direction);
	        $.ajax({
		        url: xt_vars.ajaxurl,
		        type: 'post',
		        dataType: 'json',
		        data: {
			        action: action,
			        cpage: page,
			        direction: direction,
			        settings: settings,
			        template: 'endless-posts'
		        },
		        success: function(data) {
	
					setTimeout(function() {

				        if(direction === 'prev' && data.prev_content_items !== "" && data.prev_tab_items.length !== "") {
		
						        $active_content.append(data.prev_content_items);
						        $active_nav.append(data.prev_tab_items);
						        
						}else if(direction === 'next' && data.next_content_items !== "" && data.next_tab_items.length !== "") {
						
							    $active_content.prepend(data.next_content_items);
						        $active_nav.prepend(data.next_tab_items);
			
				        }else{
				        
				        	setNoMore(tabid, direction);
				        }
		
						hideLoading(container, direction);
						
						initOnResize();
						initOnNavClick();
						initActiveArticleOnScroll();
						XT.initToggle();
						XT.initComments();
		
					},700);

			       
		        },
		        error: function(e) {
			       
			        hideLoading(container, direction);
			        setNoMore(tabid, direction);
		        }
	        });
		                    
		}
						
	
		function setNoMore(tabid, direction) {
		
        	if(direction === 'prev')
	       	 	nomore_prev[tabid] = true;
	       	else
	       		nomore_next[tabid] = true; 	
		   
		}
	

		
	};
	
	XT.initComments = function() {

	
		$('form.comment-form').off('submit');
		$('form.comment-form').on('submit', function(e) {
			
			e.preventDefault();
			
			var top_menu_fixed = '.top-menu.sticky.fixed';
			var main_menu_fixed = '.main-menu.sticky.fixed';
											
			var $form = $(this);
			var $wrap = $form.closest('.comments-area');
			var $article = $form.closest('article');
			var thankyou_msg = $wrap.data('thankyou');
			var thankyou_pending_msg = $wrap.data('thankyou-pending');

			var url = $form.attr('action');
			var post = $form.serialize();
			var post_id = $form.find('#comment_post_ID').val();

			var $list = $article.find('#comments_'+post_id+' .commentlist');
			var $loading = $('<span class="loading fa fa-refresh fa-spin"></span>');
			var $cancel = $form.find('#cancel-comment-reply-link');
			
			var is_endless_template = $('.tpl-endless-articles').length > 0;
							
			if($form.find('.alert-box').length > 0)
				$form.find('.alert-box').remove();

			$form.find('.form-submit').append($loading);
					
			$.ajax({
				url: url, 
				data: post, 
				type: 'post',
				dataType: 'json',
				success: function(res) {
				
					var comment_id = res.id;
					
					if(res.status === 1) {

						$list.html(res.list);
						
						var scroll_top = $article.find('#comment-'+comment_id).offset().top;
						
						if($(top_menu_fixed).length > 0) {
							scroll_top = scroll_top - $(top_menu_fixed).outerHeight(true); 
						}
						if($(main_menu_fixed).length > 0) {
							scroll_top = scroll_top - $(main_menu_fixed).outerHeight(true); 
						}

						var animate_selector = 'html, body';
						$(animate_selector).animate({scrollTop: scroll_top}, 600);
						$form.find('.form-submit').prepend('<div class="alert-box success">'+thankyou_msg+'</div>');
						
					}else if(res.status === 0) {
						$form.find('.form-submit').prepend('<div class="alert-box success">'+thankyou_pending_msg+'</div>');
					}	
					
					$form.find('#comment').val('');
					$form.find('.loading').remove();
				},
				error: function(e) {
					
					var response = $(e.responseText);
					response.each(function(i, item) {
			
						if(item.toString() === '[object HTMLParagraphElement]') {
							
							var error = $(item).html();
							$form.find('.form-submit').prepend('<div class="alert-box warning">'+error+'</div>');
						}
					});
					
					$form.find('.loading').remove();
					
				}
			});
			
		});
		
		$('.comment-reply-link').off('click');
		$('.comment-reply-link').on('click', function() {
			
			//addComment.moveForm("div-comment-141", "141", "respond", "908");
		
		});

		$('.load-more-comments').off('click');
		$('.load-more-comments').on('click', function(e) {
			
			e.preventDefault();
			
			var top_menu_fixed = '.top-menu.sticky.fixed';
			var main_menu_fixed = '.main-menu.sticky.fixed';
			
			var $button = $(this);
			var post_id = $button.data('postid');
			var page = $button.data('page');

			var $list = $('#comments_'+post_id+' .commentlist');
			
			
			$button.find('span').removeClass('fa-caret-down');
			$button.find('span').addClass('fa-refresh fa-spin');
			
			$.ajax({
				url: xt_vars.ajaxurl,
				data: {action: 'xt_ajax_comments', post_id: post_id, page: page},
				type: 'post', 
				success: function(data) {
					
					if(data) {
						
						var last_comment = $list.find(' > li.comment').last();
						$list.append(data);
						
						if($(last_comment).length > 0) {
							
							var scroll_top = $(last_comment).offset().top;
							
							if($(top_menu_fixed).length > 0) {
								scroll_top = scroll_top - $(top_menu_fixed).outerHeight(true); 
							}
							if($(main_menu_fixed).length > 0) {
								scroll_top = scroll_top - $(main_menu_fixed).outerHeight(true); 
							}
		
							$('html, body').animate({scrollTop: scroll_top}, 600);
						}
						
						page = page+1;
						$button.data('page', page);
						
						$button.find('span').removeClass('fa-refresh fa-spin');
						$button.find('span').addClass('fa-caret-down');
						
					}else{
						
						$button.find('span').removeClass('fa-refresh fa-spin');
						$button.find('span').addClass('fa-caret-down');
						$button.fadeOut();
						
					}	
				}
			});
						
			
		});
	};

	var disqus_shortname = 'xt-good-news';
	var disqus_identifier; //made of post id and guid
	var disqus_url; //post permalink
	
	XT.loadDisqus = function(toggle, target) {
	
		var $content = $('.tpl-endless-articles #inner_wrapper');
		
		var article = $(target).closest('article');
		var source = toggle;
		var identifier = article.data('postid')+ ' '+article.data('guid');
		var url = article.data('permalink');
		var discus_wrap = $(target).find('.discus-wrap');

		var loading_html = '<div class="wait mini">';
			loading_html += '<div class="double-bounce1"></div>';
			loading_html += '<div class="double-bounce2"></div>';
			loading_html += '</div>';
			
		$(discus_wrap).html(loading_html);
	
		if (!window.DISQUS) {

		   //insert a wrapper in HTML after the relevant "show comments" link
		   $(discus_wrap).html('<div id="disqus_thread"></div>');
		   
		   disqus_identifier = identifier; //set the identifier argument
		   disqus_url = url; //set the permalink argument
		
		   //append the Disqus embed script to HTML
		   var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		   dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
		   jQuery('head').append(dsq);
		
		}
		
		setTimeout(function() {


			DISQUS.reset({
			  reload: true,
			  config: function () {
			  	this.page.identifier = identifier;
			  	this.page.url = url;
			  }
			});
			
				
			$(toggle).fadeOut(function(){
			
				$(discus_wrap).html($('#disqus_thread')); //append the HTML after the link
			
				var comments_top = $(target).closest('article').position().top + $(target).closest('article').outerHeight() - $(target).outerHeight() - $('header').outerHeight() + 75;
				
				$content.animate({scrollTop: comments_top }, 500, function() {

					$content.find('.article-comments.fade.in').not(target).each(function() {

						$(this).hide().removeClass('in');
						$(this).closest('article').find('.comments-toggle').show();
						
						comments_top = $(target).closest('article').position().top + $(target).closest('article').outerHeight() - $(target).outerHeight() - $('header').outerHeight() + 75;
						$content.scrollTop(comments_top);
						
					});
				
				});
			});
	
				
					
		},500);

	};

	XT.showComments = function(toggle, target) {
		
		var $content = $('.tpl-endless-articles #inner_wrapper');

		$(toggle).fadeOut(function(){
			$(this).remove();
			var comments_top = $(target).closest('article').position().top + $(target).closest('article').outerHeight() - $(target).outerHeight() - $('header').outerHeight() + 75;
			$content.animate({scrollTop: comments_top }, 500);
		});

	};
	
	
	XT.initStollJs = function() {
		
		if(XT.isTouch()) {
			return false;
		}
		if(typeof(stroll) !== 'undefined') {
		
			stroll.bind( 'ul.stroll', { live: true } );
			$('ul.stroll li.future').on('mouseenter', function() {
				$(this).removeClass('future');
			});
			
		}	
	};
	
	XT.initChosenSelect = function() {
		
		$("select").chosen({disable_search_threshold: 10});
	};

	XT.initLabelToPlaceholder = function() {
	
		var selectors = '.nm_mc_form,.comment-form';
		
	 	$(selectors).find(":input[type='text'],textarea").each(function(index, elem) {
		    var eId = $(elem).attr("id");
		    var label = null;
		    if (eId && (label = $(elem).parents("form").find("label[for="+eId+"]")).length === 1) {
		        $(elem).attr("placeholder", $(label).html());
		        $(label).remove();
		    }
		 });
	};

			
	XT.initBackToTop = function() {
		
		$('#back-to-top').on('click', function(e) {
			e.preventDefault();
			$('body,html').animate(
				{
					scrollTop: 0
				}, 
				{
			        duration: 1200, // how fast we are animating
			        easing: 'easeInOutCirc',
			    }
			);
		});
	};
	
		
	XT.initScrollToSection = function() {
		
		// Iterate over all nav links, setting the "selected" class as-appropriate.
		$("a[href*=#]").on('click', function(e){
		
			var target = $(this).attr('href');

			if(target.charAt(0) === '#') {

				if(XT.vars.animating)
					return false;
	
				target = target.replace('#', '#scrollto_');
				
				if($(target).length > 0) {
					e.preventDefault();
					scrollToTarget(target);
				}
			  	
			}else if(target === location.href) {
				
				var current_url = location.href;
				target = current_url.split('#');
			
				if(target.length > 1) {
				
					target = '#scrollto_'+target[1];
					scrollToTarget(target);
					
				}
				
			}	
		  	
		});
		
		
		$(window).on('load', function() {
			
			var current_url = location.href;
			var target = current_url.split('#');
			
			if(target.length > 1) {
				target = '#scrollto_'+target[1];
				scrollToTarget(target);
					
			}

		});
		
		
		function scrollToTarget(target) {
			
			var top_menu_fixed = '.top-menu.sticky';
			var main_menu_fixed = '.main-menu.sticky';
			var w_width = $(window).width();
			
			if(XT.vars.animating)
				return false;
		
		  	if($(target).length > 0) {
		  	
		  		XT.vars.animating = true;
		
		  		var top = $(target).offset().top;

		  		if(w_width > XT.vars.breakpoints.tablet) {
					if($(top_menu_fixed).length > 0) {
						top = top - $(top_menu_fixed).find('.top-bar').data('sticky_height'); 
					}
					if($(main_menu_fixed).length > 0) {
						top = top - $(main_menu_fixed).find('.top-bar').data('sticky_height'); 
					}
				}
					
		  		$('body,html').animate({
					scrollTop: top
				}, 800, function() {
		
					var top2 = $(target).offset().top;
					
					if(w_width > XT.vars.breakpoints.tablet) {
						if($(top_menu_fixed).length > 0) {
							top2 = top2 - $(top_menu_fixed).find('.top-bar').data('sticky_height'); 
						}
						if($(main_menu_fixed).length > 0) {
							top2 = top2 - $(main_menu_fixed).find('.top-bar').data('sticky_height'); 
						}
					}
					
					$('body,html').animate({
						scrollTop: top2
					}, 800, function() {
						XT.vars.animating = false;
					});
				});

			}
				
		}
		
	};
	
	
	XT.initTwitter = function () {

		$('.xt_widget_twitter').each(function() {
			
			var $twitter = $(this).find('.query');
			var username = $twitter.data('username');
			var count = $twitter.data('count');
			var showdate = $twitter.data('showdate');
			var showavatar = $twitter.data('showavatar');
			var template = "{join}{text}";

			if(username === '') {
				return false;
			}
			
			if(showdate === 1) {
				template = "{time}"+template;
			}
			
			if(showavatar === 1) {
				template = "{avatar}"+template;
			}
							
	        var options = {
				modpath: xt_vars.theme_url+'/assets/vendors/twitter/',
				username: username,
				avatar_size: 32,
		        page: 1,
				count: count,
				fetch: count, 
				loading_text: $twitter.data('loading'),
				template: template
	        };
	        
	        
	
	        var widget = $twitter,
	          next =  $(this).find(".next"),
	          prev =  $(this).find(".prev");
	
	        var enable = function(el, yes) {
	          	return yes ? $(el).removeClass('disabled') : $(el).addClass('disabled');
	        };
	
	        var stepClick = function(incr) {
	          return function() {
	            options.page = options.page + incr;
	            enable(this, false);
	            widget.tweet(options);
	          };
	        };
	
	        next.on("checkstate", function() {
	          enable(this, widget.find("li").length === options.count);
	        }).click(stepClick(1));
	
	        prev.on("checkstate", function() {
	          enable(this, options.page > 1);
	        }).click(stepClick(-1));
	
	        widget.tweet(options).on("loaded", function() { next.add(prev).trigger("checkstate"); });
	       				
		}); 
	
	};

	XT.initStickySidebar = function () {

		if(!xt_vars.enable_sticky_sidebars) 
			return false;
		
		var args = {};
		
		var top_menu_fixed = '.top-menu.sticky';
		var main_menu_fixed = '.main-menu.sticky';
			
        $('.has-sticky-sidebar').each(function() {
	        
	        var $sidebar = $(this);
	        
	        if($sidebar.closest('.vc_row').length > 0) {
		        args.containerSelector = $sidebar.closest('.vc_row');
	        }else if($sidebar.closest('.row').length > 0) {
		        args.containerSelector = $sidebar.closest('.row');
	        }
	        
	        var margin_top = 0;
	        if($sidebar.data('margin_top')) {
		        margin_top = $sidebar.data('margin_top');
	        }
	        
	        var margin_bottom = 0;
	        if($sidebar.data('margin_bottom')) {
		        margin_bottom = $sidebar.data('margin_bottom');
	        }
	        
	        if($(top_menu_fixed).length > 0) {
				margin_top = margin_top + $(top_menu_fixed).find('.top-bar').data('sticky_height'); 
			}
			if($(main_menu_fixed).length > 0) {
				margin_top = margin_top + $(main_menu_fixed).find('.top-bar').data('sticky_height'); 
			}
	        
	        args.additionalMarginTop = margin_top;
	        args.additionalMarginBottom = margin_bottom;
	        
	        $(this).theiaStickySidebar(args);
	        
	    });
	
	};


	XT.initPostNav = function() {
		
		if($('.post-nav').length === 0)
			return false;
			
		
		if($('.post-nav').hasClass('show-on-scroll')) {
			
			$('.single .article-end').waypoint(function(direction){
			
				if(direction === 'down') {
					$('.post-nav').addClass('active');
				}else if(direction === 'up') {
					$('.post-nav').removeClass('active');
				}
	
			},{
				
				offset: '90%'
			});
		
		}else{
			$('.post-nav').addClass('active');
		}
		
	};
			
	XT.initContentHeight = function() {
		
		$(window).resize(function() {
			
			var content_height = $(window).height() - $('header').outerHeight() - $('footer').outerHeight();
			$('#inner_wrapper').css('min-height', content_height);
		
		});
		$(window).resize();

	}; 

	XT.hideWidgetTitleIfEmpty = function() {

		$('.widgettitle').each(function() {
			var title = $(this).text();
			if(title === '')
				$(this).hide();
		});
	};
	
	
	XT.initLights = function() {
		
		var lights = $('.lights_overlay');
		lights.off('click');
    	lights.on('click', function() {
	    	XT.lightsOn();
    	});
    	XT.lightsOn();
		
	};
	
	XT.lightsOff = function() {
		
		var lights = $('.lights_overlay');
		
		lights.removeClass('hidden');
		
		setTimeout(function () {
			lights.removeClass('visually-hidden');
    	}, 5);
	};
	
	XT.lightsOn = function() {
		
		var lights = $('.lights_overlay');
		
		lights.addClass('visually-hidden');
		
		if(XT.canDoAnimations()) {
				    
			lights.one(XT.vars.animationEnd, function(e) {
				
				if(lights.hasClass('visually-hidden'))
					lights.addClass('hidden');
	
	    	});
	    	
	    }else{
		    
		    if(lights.hasClass('visually-hidden'))
				lights.addClass('hidden');
	    }	

	};
	
	XT.initCustomJS = function() {
		
		if(xt_vars.custom_js.length > 0) {
			eval(xt_vars.custom_js);
		}
	};
	
		
	XT.updatePageInfo = function(title, url, html) {
	
		if (Modernizr.history) {
			if(title && title !== '') {
	 			document.title = title;
	 		}	
	 		window.history.pushState({"html":html,"pageTitle":document.title},"", url);
	 	}
	};
	
	XT.isTouch = function() {
		return !$("html").hasClass('no-touch');
	};
	
	XT.isRTL = function() {
		return $("body").hasClass('rtl');
	};
	
	XT.canDoAnimations = function() {
		return $('html').hasClass('cssanimations');	
	};

	
	$(document).on('replace', 'img', function (e, new_path, original_path) {
		
		var img = $(this);
	  	setTimeout(function(){
		  	img.addClass('loaded');
		}, 50);	
	  
	});
	
	$(document).ready(function() {
		
		XT.init();
		
	});


})(jQuery);