<?php

/*
 * Deactivate custom html block on category left sidebar
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
$cmsBlock = Mage::getModel('cms/block')->load('porto_left_side_category', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->setIsActive(false);
    $cmsBlock->save();
}