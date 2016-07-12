<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 1;




/*  New CMS Block */
$page = Mage::getModel('cms/page')->load('home', 'identifier');
if ($page->getIdentifier()) {
    $page->delete();
}

$pagecontent = <<<EOF
{{block type="cms/block" block_id="home-micro-teaser"}}

{{block type="cms/block" block_id="home-teaser-slider"}}

<style type="text/css">
    .main.container{background-color:transparent;padding:0;margin-bottom:0;}
    .col-main{margin-top:15px;}
</style>

{{block type="cms/block" block_id="home-sub-teaser-boxes"}}

<div class="horizontal-line"></div>

{{block type="cms/block" block_id="home-aktionsboxen"}}

<div class="horizontal-line"></div>

{{block type="cms/block" block_id="home-info-teaser-1"}}

{{block type="cms/block" block_id="home-seo-product-1"}}

{{block type="cms/block" block_id="home-seo-marken"}}

{{block type="cms/block" block_id="home-seo-product-2"}}

{{block type="cms/block" block_id="home-seo-brands-carousel"}}

<div class="horizontal-line"></div>

{{block type="cms/block" block_id="home-categories"}}

<div class="horizontal-line"></div>

{{block type="cms/block" block_id="home-seo-text"}}
EOF;

$cmsPage = Mage::getModel('cms/page');
$cmsPage->setTitle('Perfekt-Schlafen: Matratzen, Lattenroste, Sofas, Betten');
$cmsPage->setIdentifier('home');
$cmsPage->setContent($pagecontent);
$cmsPage->setIsActive(true);
$cmsPage->setStores(array($scopeId));
$cmsPage->save();
