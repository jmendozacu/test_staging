<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

/* Footer Styles */
$path = '';
$configSwitch->saveConfig('porto_settings/footer/footer_ribbon_enabled', '1', $scope, $scopeId);
$configSwitch->saveConfig('porto_settings/category_grid/columns', '4', $scope, $scopeId);
$configSwitch->saveConfig('porto_settings/custom_settings/custom_style','', $scope, $scopeId);
$configSwitch->saveConfig('porto_settings/footer/footer_logo','images/logo_footer.png',$scope, $scopeId);
$configSwitch->saveConfig('porto_settings/general/border_radius','0',$scope,$scopeId);
$configSwitch->saveConfig('porto_settings/general/layout','0',$scope,$scopeId);
$configSwitch->saveConfig('porto_design/font/primary_font_family_group','custom',$scope,$scopeId);
$configSwitch->saveConfig('web/default/cms_home_page', 'porto_home_4', $scope, $scopeId);

