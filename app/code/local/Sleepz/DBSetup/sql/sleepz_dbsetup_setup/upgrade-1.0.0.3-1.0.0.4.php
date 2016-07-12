<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

/* Footer Styles */
$path = '';
$configSwitch->saveConfig('porto_settings/category_grid/show_addtocart', '0', $scope, $scopeId);
$configSwitch->saveConfig('quickview/general/enableview', '0', $scope, $scopeId);
$configSwitch->saveConfig('design/header/logo_src', 'images//logo_md.png', $scope, $scopeId);

// product view layout
$configSwitch->saveConfig('porto_settings/product_view/page_layout', 'two_column_right', $scope, $scopeId);
$configSwitch->saveConfig('porto_settings/_product_view_custom_tab/custom_tab', '0', $scope, $scopeId);


/*  Contact Block */
$page = Mage::getModel('cms/page')->load('kontakt', 'identifier');
if ($page->getId()) {$page->delete();}

$pagecontent = <<<EOF
<div class="contact-container">
<h2>Sie haben Fragen oder Wünsche? - Kontaktieren Sie uns</h2>
<p>
Sie erreichen uns telefonisch unter +49 (0) 30 200970011 (Mo. - Fr. von 10 bis 18 Uhr), oder nutzen Sie unser Kontaktformular am Ende der Seite und senden Sie uns eine Nachricht
<br/>
Wir freuen uns über Ihre Anfrage und melden uns umgehend bei Ihnen.
</p>

<h2>Besuchen Sie unseren Showroom im Herzen Berlins in der Kochstr. 29 (Nähe Checkpoint Charlie)</h2>
<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d9713.917981163351!2d13.386534!3d52.506661!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x47bc4ec178d35b79!2sMatratzen+Discount!5e0!3m2!1sen!2sde!4v1458573276803" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
<p style="margin-bottom: 35px;">Öffentliche Verkehrsanbindung:
U-Bahnhof Kochstraße (U6),
Bushaltestelle Wilhelmstr./Kochstr. (M29)
</p>


{{block type="core/template" name="contactForm" form_action="/contacts/index/post" template="contacts/form.phtml"}}
</div>
EOF;

$page = Mage::getModel('cms/page');
$page->setTitle('Kontakt');
$page->setIdentifier('kontakt');
$page->setContent($pagecontent);
$page->setIsActive(true);
$page->setRootTemplate('one_column');
$page->setStores(array($scope));
$page->save();

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
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="angebote" template="catalog/product/topproducts.phtml" category_id=3 size=4 items=4}}
  </div>
  <div class="col-sm-3">
<img src="{{skin url='img/homepage/null-prozent.jpg'}}" alt="Null Prozent" class="img-responsive" />
  </div>
</div>
<!-- Top Matratzen -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="matratzen" template="catalog/product/topproducts.phtml" category_id=3 size=6 items=5}}
  </div>
</div>
<!-- Top Lattenroste -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="lattenroste" template="catalog/product/topproducts.phtml" category_id=4 size=6 items=5}}
  </div>
</div>
<!-- Top Bettwaren -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="bettwaren" template="catalog/product/topproducts.phtml" category_id=132 size=6 items=5}}
  </div>
</div>
<!-- Top Bettwäsche -->
<div class="row">
  <div class="col-sm-12">
    {{block type="sleepz_frontend/catalog_product_topProducts" carousel_id="bettwaesche" template="catalog/product/topproducts.phtml" category_id=31 size=6 items=5}}
  </div>
</div>
</div>
<script>
  jQuery('.homepage-teaser').owlCarousel({
        autoPlay: 5000,
        items : 1,
        navigation : true, // Show next and prev buttons
        pagination : false, // Show next and prev buttons
        navigationText: ["",""],
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

// Add Block Permission for sleepz_frontend/catalog_product_topProducts
$whitelistBlock = Mage::getModel('admin/block')->load('sleepz_frontend/catalog_product_topProducts', 'block_name');
$whitelistBlock->setData('block_name', 'sleepz_frontend/catalog_product_topProducts');
$whitelistBlock->setData('is_allowed', 1);
$whitelistBlock->save();

// Set created Homepage as default
$configSwitch->saveConfig('web/default/cms_home_page', 'home', $scope, $scopeId);
