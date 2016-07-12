<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

/* Disable Custom Tab on Product View */
$configSwitch->saveConfig('porto_settings/header/sticky_header', '0', $scope, $scopeId);
$configSwitch->saveConfig('homeslider/general/enabled', '0', $scope, $scopeId);
/* Disable Custom Tab on Product View */
$configSwitch->saveConfig('porto_settings/category_fullwidth/description', 0, $scope, $scopeId);


/*  Home Page Block */
$page = Mage::getModel('cms/page')->load('home', 'identifier');
if ($page->getId()) {$page->delete();}

$pagecontent = <<<EOF
<div class="homepage">
<!-- Teaser -->
<div class="row">
  <div class="col-sm-9">
<div class="homepage-teaser nav-bottom">
<div class="item"><img src="{{skin url='img/homepage/startseitenteaser-klappmatratzen.jpg'}}" alt="Teaser" class="img-responsive" /></div>
<div class="item"><img src="{{skin url='img/homepage/Suripur-Abverkauf.jpg'}}" alt="Teaser" class="img-responsive" /></div>
</div>
  </div>
  <div class="col-sm-3">
<img src="{{skin url='img/homepage/deal-des-monats.jpg'}}" alt="Deal des Monats" class="img-responsive" />
  </div>
</div>
<!-- Top Angebote -->
<div class="row">
  <div class="col-sm-9">
{{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="angebote" template="catalog/product/topproducts.phtml" category_id=227 size=4 items=4}}
  </div>
  <div class="col-sm-3">
<img src="{{skin url='img/homepage/null-prozent.jpg'}}" alt="Null Prozent" class="img-responsive" />
  </div>
</div>
<!-- Top Matratzen -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="matratzen" template="catalog/product/topproducts.phtml" category_id=173 size=6 items=5}}
  </div>
</div>
<!-- Top Lattenroste -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="lattenroste" template="catalog/product/topproducts.phtml" category_id=174 size=6 items=5}}
  </div>
</div>
<!-- Top Bettwaren -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="bettwaren" template="catalog/product/topproducts.phtml" category_id=175 size=6 items=5}}
  </div>
</div>
<!-- Top Bettwäsche -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="bettwaesche" template="catalog/product/topproducts.phtml" category_id=176 size=6 items=5}}
  </div>
</div>
</div>
<script>
  jQuery('.homepage-teaser').owlCarousel({
        autoPlay: true,
        items : 1,
        nav : true, // Show next and prev buttons
        pagination : false, // Show next and prev buttons
        navText: ["",""],
        loop:true,
      });
</script>
EOF;

$page = Mage::getModel('cms/page');
$page->setTitle('G&uuml;nstige Matratzen bei Matratzendiscount');
$page->setIdentifier('home');
$page->setContent($pagecontent);
$page->setIsActive(true);
$page->setRootTemplate('one_column');
$page->setStores(array($scope));
$page->save();


/*  Trusted Shop Banner in Cart */
$block = Mage::getModel('cms/block')->load('trusted_block_cart', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
<div class="trusted-imgs">
  <img src="{{skin url='img/ekomi.png'}}" alt="ekomi" width="55" height="55" />
  <img src="{{skin url='img/trusted-shop.png'}}" alt="trusted" width="55" height="55" />
</div>
<div class="trusted-list">
  <ul>
    <li>Kostenlose Lieferung schon ab 49 €</li>
    <li>kostenlose Rücklieferung</li>
    <li>Sichere Zahlung</li>
    <li>14 Tage Rückgaberecht</li>
    <li>Käuferschutz</li>
  </ul>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Trusted Shop Checkout Cart');
$cmsBlock->setIdentifier('trusted_block_cart');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setStores(array($scope));
$cmsBlock->setIsActive(true);
$cmsBlock->save();
