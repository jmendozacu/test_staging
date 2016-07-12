<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

/* Disable Custom Tab on Product View */
$configSwitch->saveConfig('megamenu/general/wide_style', 'narrow', $scope, $scopeId);