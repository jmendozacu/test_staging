/*global redux_change, wp, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.networks = redux.field_objects.networks || {};

    $( document ).ready(
        function() {
            //redux.field_objects.networks.init();
        }
    );

    redux.field_objects.networks.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.redux-container-networks' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                
				$( el ).find('.network-color-field').not('.wp-color-picker').wpColorPicker();
				
                redux.field_objects.media.init(el);

                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                
                if ( parent.hasClass( 'redux-container-networks' ) ) {
                    parent.addClass( 'redux-field-init' );    
                }
                
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                el.find( '.redux-networks-remove' ).live(
                    'click', function() {
                        redux_change( $( this ) );

                        $( this ).parent().siblings().find( 'input[type="text"]' ).val( '' );
                        $( this ).parent().siblings().find( 'input[type="hidden"]' ).val( '' );

                        var slideCount = $( this ).parents( '.redux-container-networks:first' ).find( '.redux-networks-accordion-group' ).length;

                        if ( slideCount > 1 ) {
                            $( this ).parents( '.redux-networks-accordion-group:first' ).slideUp(
                                'medium', function() {
                                    $( this ).remove();
                                }
                            );
                        } else {
                            var content_new_title = $( this ).parent( '.redux-networks-accordion' ).data( 'new-content-name' );

                            $( this ).parents( '.redux-networks-accordion-group:first' ).find( '.remove-image' ).click();
                            $( this ).parents( '.redux-container-networks:first' ).find( '.redux-networks-accordion-group:last' ).find( '.redux-networks-header' ).text( content_new_title );
                        }
                    }
                );

                el.find( '.redux-networks-add' ).click(
                    function() {
                        var newNetwork = $( this ).prev().find( '.redux-networks-accordion-group:last' ).clone( true );

                        var slideCount = $( newNetwork ).find( '.slide-name' ).attr( "name" ).match( /[0-9]+(?!.*[0-9])/ );
                        var slideCount1 = slideCount * 1 + 1;

                        $( newNetwork ).find( 'input[type="text"], input[type="hidden"]' ).each(
                            function() {

                                $( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );
                                $( this ).val( '' );
                                if ( $( this ).hasClass( 'slide-sort' ) ) {
                                    $( this ).val( slideCount1 );
                                }
                            }
                        );

                        var content_new_title = $( this ).prev().data( 'new-content-name' );

                        $( newNetwork ).find( '.screenshot' ).removeAttr( 'style' );
                        $( newNetwork ).find( '.screenshot' ).addClass( 'hide' );
                        $( newNetwork ).find( '.screenshot a' ).attr( 'href', '' );
                        $( newNetwork ).find( '.remove-image' ).addClass( 'hide' );
                        $( newNetwork ).find( '.redux-networks-image' ).attr( 'src', '' ).removeAttr( 'id' );
                        $( newNetwork ).find( 'h3' ).text( '' ).append( '<span class="redux-networks-header">' + content_new_title + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );
                        $( newNetwork ).find('h4').html('');
                        
                        var colorfield = $( newNetwork ).find('.network-color-field.wp-color-picker').clone();
                        colorfield.removeClass('wp-color-picker');
                        $( newNetwork ).find('.wp-picker-container').replaceWith(colorfield);
                        $( newNetwork ).find('.network-color-field').not('.wp-color-picker').wpColorPicker();
                         
                        $( this ).prev().append( newNetwork );
                    }
                );

                el.find( '.slide-name' ).keyup(
                    function( event ) {
                        var newTitle = event.target.value;
                        $( this ).parents().eq( 3 ).find( '.redux-networks-header' ).text( newTitle );
                    }
                );


                el.find( ".redux-networks-accordion" )
                    .accordion(
                    {
                        header: "> div > fieldset > h3",
                        collapsible: true,
                        active: false,
                        heightStyle: "content",
                        icons: {
                            "header": "ui-icon-plus",
                            "activeHeader": "ui-icon-minus"
                        }
                    }
                )
                    .sortable(
                    {
                        axis: "y",
                        handle: "h3",
                        connectWith: ".redux-networks-accordion",
                        start: function( e, ui ) {
                            ui.placeholder.height( ui.item.height() );
                            ui.placeholder.width( ui.item.width() );
                        },
                        placeholder: "ui-state-highlight",
                        stop: function( event, ui ) {
                            // IE doesn't register the blur when sorting
                            // so trigger focusout handlers to remove .ui-state-focus
                            ui.item.children( "h3" ).triggerHandler( "focusout" );
                            var inputs = $( 'input.slide-sort' );
                            inputs.each(
                                function( idx ) {
                                    $( this ).val( idx );
                                }
                            );
                        }
                    }
                );
            }
        );
    };
})( jQuery );
