jQuery(document).ready(function() {

    var owl = jQuery(".footer-logos");

    owl.owlCarousel({
        autoPlay: 3000, //Set AutoPlay to 3 seconds
        items : 10, //10 items above 1000px browser width
        itemsDesktop : [1000,5], //5 items between 1000px and 901px
        itemsDesktopSmall : [900,3], // betweem 900px and 601px
        itemsTablet: [600,2], //2 items between 600 and 0
        navigation : false, // Show next and prev buttons
        pagination : false // Show next and prev buttons
    });


});