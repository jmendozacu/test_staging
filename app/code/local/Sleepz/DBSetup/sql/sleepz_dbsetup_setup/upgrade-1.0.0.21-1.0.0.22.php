<?php

/*
 * this update includes all cms blocks of the home page
 *
 * List of all the blocks:
 * home-micro-teaser
 *
 */


#$configSwitch = new Mage_Core_Model_Config();

/*
 * SET SCOPE
 */
# set scope
$scopeId = 1; // perfekt schlafen=1, matratzendiscount=4


/*
 * IF BLOG EXISTS, DELETE IT...
 */

# set identifier array
$identifierList[] = 'home-micro-teaser';

# delete blocks, if exist
foreach ($identifierList as $key => $identifier) {
    $cmsBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');
    if ($cmsBlock->getId()) {$cmsBlock->delete();}
}

/*
 * SET CMS/PAGES ...
 */



/*
 *  home-micro-teaser
 */
$blockContent = <<<EOF
<!-- microteaser -->
<div class="col-sm-12">
    <a href="/catalogsearch/result/?q=moebel+hasena" class="micro-teaser">Ihre Schlafzimmer-Empfehlung vom Osterhasen: <strong>Designer-Möbel von Hasena</strong></a>
    <div class="clearfix"></div>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Micro Teaser');
$cmsBlock->setIdentifier('home-micro-teaser');
$cmsBlock->setContent($blockContent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();
