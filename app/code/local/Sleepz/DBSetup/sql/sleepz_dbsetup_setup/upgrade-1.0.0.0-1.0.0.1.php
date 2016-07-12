<?php
$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

/**
 * porto design panel setup for matrazendiscount
 */
 // colors
 $path = 'porto_design/colors/';
 $configSwitch->saveConfig($path.'custom', '1', $scope, $scopeId);
 $configSwitch->saveConfig($path.'text_color', '#333', $scope, $scopeId);
 $configSwitch->saveConfig($path.'link_color', '#4e5152', $scope, $scopeId);
 $configSwitch->saveConfig($path.'link_hover_color', '#d81c00', $scope, $scopeId);

 $configSwitch->saveConfig($path.'button_bg_color', '#ef2000', $scope, $scopeId);
 $configSwitch->saveConfig($path.'button_text_color', '#fff', $scope, $scopeId);
 $configSwitch->saveConfig($path.'button_hover_bg_color', '#ffa53c', $scope, $scopeId);
 $configSwitch->saveConfig($path.'button_hover_text_color', '#fff', $scope, $scopeId);

 $configSwitch->saveConfig($path.'addtowishlist_color', '#1a92c3', $scope, $scopeId);
 $configSwitch->saveConfig($path.'addtowishlist_color_hover', '#1a92c3', $scope, $scopeId);

 $configSwitch->saveConfig($path.'breadcrumbs_bg_color', '#fff', $scope, $scopeId);
 $configSwitch->saveConfig($path.'breadcrumbs_color', '#2f2f2f', $scope, $scopeId);
 $configSwitch->saveConfig($path.'breadcrumbs_links_color', '#0088cc', $scope, $scopeId);
 $configSwitch->saveConfig($path.'breadcrumbs_links_hover_color', '#0088cc', $scope, $scopeId);

 // font
 $path = 'porto_design/font/';
 $configSwitch->saveConfig($path.'custom', '1', $scope, $scopeId);
 $configSwitch->saveConfig($path.'font_size', '12px', $scope, $scopeId);
 $configSwitch->saveConfig($path.'primary_font_family_custom', 'Arial, sans-serif', $scope, $scopeId);

 // page
 $path = 'porto_design/page/';
 $configSwitch->saveConfig($path.'custom', '1', $scope, $scopeId);
 $configSwitch->saveConfig($path.'page_bgcolor', '#e6f0f5', $scope, $scopeId);

 // main
 $path = 'porto_design_main/';
 $configSwitch->saveConfig($path.'custom', '1', $scope, $scopeId);
 $configSwitch->saveConfig($path.'main_bgcolor', '#e6f0f5', $scope, $scopeId);

 /**
  * porto settings panel setup for matrazendiscount
  */
$configSwitch->saveConfig('porto_settings/general/layout', '1170', $scope, $scopeId);
