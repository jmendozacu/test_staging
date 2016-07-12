<?php
$configSwitch = new Mage_Core_Model_Config();
$scope = 'stores'; $scopeId = 1;

// for mip import
$configSwitch->saveConfig('mip_bware/category/root', '2', $scope, $scopeId);
$configSwitch->saveConfig('mip_bware/category/bware_parent_id', '2', $scope, $scopeId);


$scope = 'stores'; $scopeId = 4;

$configSwitch->saveConfig('mip_bware/category/root', '172', $scope, $scopeId);
$configSwitch->saveConfig('mip_bware/category/bware_parent_id', '172', $scope, $scopeId);