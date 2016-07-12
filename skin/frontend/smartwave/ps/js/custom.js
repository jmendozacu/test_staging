/**
 * Created by florian on 10.03.16.
 */

//document-ready function
jQuery( document ).ready(function() {

    //active Nav-Menu on CMS pages
    jQuery('#left-nav li').each(function () {
        var href = jQuery(this).find('a').attr('href');
        if (href === window.location.pathname) {
            //if (href.indexOf(window.location.pathname)) {
            jQuery(this).addClass('active');
            jQuery(this).parent().addClass('reveal');
        }
    });

    //active Nav-Menu on CMS pages
    jQuery('#nav li.cmslink').each(function () {
        var href = jQuery(this).find('a').attr('href');
        if (href === window.location.pathname) {
            jQuery(this).addClass('active');
        }
    });

    //active Nav-Menu on CMS pages
    jQuery('dt').click(function () {
        jQuery('dt').removeClass('active');
        jQuery('dd').removeClass('reveal');
        jQuery(this).addClass('active');
        jQuery(this).next().addClass('reveal');
    });

    //Berater menu
    jQuery("#left-nav .nav-sub1 span").click(function() {
        jQuery(this).next(".nav-sub1 ul").toggleClass('reveal');
    });

    // logout btn
    jQuery('.ut_konto').hover(function() {
       if (jQuery('#logout-btn').length) {
           jQuery('#logout-btn').clearQueue().animate({height:20},200);
       }
    });

    jQuery('.header-container').mouseleave(function() {
        if (jQuery('#logout-btn').length) {
            jQuery('#logout-btn').animate({height:0},200);
        }
    });


    // ### product view */
    jQuery(".add-to-cart .input-text.qty").on("input", function() {
        checkQtyVal(jQuery(".add-to-cart .input-text.qty"));
    });

    jQuery(".add-to-cart .icon-down-dir").click(function() {
        setTimeout(checkQtyVal, 100, jQuery(".add-to-cart .input-text.qty"));
    });

    function checkQtyVal(qty) {
        if (qty.val() < 1) {
            qty.val(1);
        }
    }

    /*jQuery("body").scrollTo("dt.active");*/
});




function showPayment() {
    if (window.location.hash.length) {
        var location = window.location.hash.substring(1);
        jQuery('dt').removeClass('active');
        jQuery('dd').removeClass('reveal');
        jQuery('#'+location).addClass('reveal');
        jQuery('#'+location).prev().addClass('active');
        jQuery(window).scrollTop(jQuery('#'+location).offset().top - 180);
    }
}

// facebook privacy popup
function showFacebookPrivacyPopup() {
    // set html
    var popup_html = '<div class="content"><p>Bei Aktivierung der „Gefällt mir“-Schaltfläche werden Daten an Facebook übertragen und dort gespeichert.</p><p>Möchten Sie die Schaltfläche jetzt aktivieren?</p><p><button type="button" onclick="activateFacebookLike()">Schaltfläche aktivieren</button><button type="button" class="cancel" onclick="jQuery(\'#fb-privacy-popup\').hide();">Abbrechen</button></p></div><div class="arrow-bottom"></div>';
    // add html
    jQuery('#fb-privacy-popup').fadeIn().html(popup_html);
}

// loading like button by replacing the dummy button
function activateFacebookLike() {
    // set html
    var fb_html = '<iframe frameborder="0" allowtransparency="true" style="border:none; overflow:hidden; width:130px; height:21px;" scrolling="no" src="http://www.facebook.com/plugins/like.php?locale=de_DE&amp;href=https%3A%2F%2Fwww.facebook.com%2Fperfektschlafen&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21"></iframe>';
    // replace the button with the facebook-iframe
    jQuery('#fb-like-btn .dummy').replaceWith(fb_html);
    // delete popup
    jQuery('#fb-privacy-popup').remove();
}