<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('catalog_product', 'disable_xtpayments', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Not available for purchase with XT Payments Checkout',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '0',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => '',
        'is_configurable'   => false
    )
);

$attributeId = $installer->getAttributeId('catalog_product', 'disable_xtpayments');

try {
    foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'Prices');
        $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
    }
} catch ( Exception $e ) {
}

$installer->endSetup();
