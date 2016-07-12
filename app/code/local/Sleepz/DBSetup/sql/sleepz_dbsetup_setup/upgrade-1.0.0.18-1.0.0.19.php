<?php
$configSwitch = new Mage_Core_Model_Config();
$scope = 'default'; $scopeId = 0;

// for SEO 
$configSwitch->saveConfig('catalog/seo/product_url_suffix', '/', $scope, $scopeId);
$configSwitch->saveConfig('catalog/seo/category_url_suffix', '/', $scope, $scopeId);
