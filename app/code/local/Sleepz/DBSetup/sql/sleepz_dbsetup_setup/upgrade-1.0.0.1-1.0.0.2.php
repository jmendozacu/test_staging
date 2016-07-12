<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

$configSwitch->saveConfig('design/package/name', 'smartwave', $scope, $scopeId);
$configSwitch->saveConfig('design/theme/template', 'md', $scope, $scopeId);
$configSwitch->saveConfig('design/theme/skin', 'md', $scope, $scopeId);
$configSwitch->saveConfig('design/theme/layout', 'md', $scope, $scopeId);
$configSwitch->saveConfig('design/theme/default', 'porto', $scope, $scopeId);

/* Header Styles */
$path = 'porto_design/header/';
$configSwitch->saveConfig($path.'custom', '1', $scope, $scopeId);
$configSwitch->saveConfig($path.'header_bgcolor', '#fff', $scope, $scopeId);
$configSwitch->saveConfig($path.'header_textcolor', '#333', $scope, $scopeId);
$configSwitch->saveConfig($path.'header_top_links_bgcolor', '#fff', $scope, $scopeId);
$configSwitch->saveConfig($path.'header_menu_bgcolor', '#ef2000', $scope, $scopeId);
$configSwitch->saveConfig($path.'header_menu_color', '#fff', $scope, $scopeId);
