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

});