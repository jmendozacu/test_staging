<?php

/*
 * Adding the missing block "home-teaser-slider"
 *
 */


#$configSwitch = new Mage_Core_Model_Config();

/*
 * SET SCOPE
 */
#$scope = 'websites';
$scopeId = 1;



/*
 * IF BLOG EXISTS, DELETE IT...
 */
$cmsBlock = Mage::getModel('cms/block')->load('home-teaser-slider', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}



/*
 * SET THE CONTENT...
 */
$blockcontent = <<<EOF
{{block type="cms/block" block_id="home-teaser-slider-css"}}

<!-- Set up your HTML -->
<div id="slideshow">
    <div class="owl-carousel">
        {{block type="cms/block" block_id="home-teaser-slider-html"}}
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#slideshow .owl-carousel").owlCarousel({
            loop:true,
            items: 1,
            nav: true,
            navText: false,
            loop: true,
            autoplay: true,
            autoplayHoverPause: true
        });
    });
</script>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Slider');
$cmsBlock->setIdentifier('home-teaser-slider');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();