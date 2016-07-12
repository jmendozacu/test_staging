<?php

/*
 * Change title of homepage...
 * from:    Perfekt-Schlafen: Matratzen, Lattenroste, Sofas, Betten | perfekt-schlafen.de
 * to:      Matratzen, Lattenroste, Sofas, Betten | perfekt-schlafen.de
 *
 * the suffix | perfekt-schlafen.de is added by standard
 * the rest of the title is set by cms/page title
 *
 */


#$configSwitch = new Mage_Core_Model_Config();

/*
 * SET SCOPE
 *
 * perfekt-schlafen = 1
 * matratzendiscount = 4
 */
#$scope = 'websites';
$scopeId = 1;



/*
 * load page and change title
 */
$cmsPage = Mage::getModel('cms/page')->load('home', 'identifier');
if ($cmsPage->getIdentifier()) {
    $cmsPage->setTitle('Matratzen, Lattenroste, Sofas, Betten');
    $cmsPage->setRootTemplate('one_column');
    $cmsPage->save();
}