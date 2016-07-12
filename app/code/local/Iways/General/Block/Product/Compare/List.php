<?php 
/**
 * Catalog products compare block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Iways_General_Block_Product_Compare_List extends Mage_Catalog_Block_Product_Compare_List {

	public function getProductAttributeValue($product, $attribute)
    {
            if (!$product->hasData($attribute->getAttributeCode())) {
                return '';
            }

            if ($attribute->getSourceModel()
                || in_array($attribute->getFrontendInput(), array('select', 'boolean', 'multiselect'))
            ) {
                if (!$product->getAttributeText($attribute->getAttributeCode())) {
                    $value = "";
                } else {
                    $value = $attribute->getFrontend()->getValue($product);
                }
            } else {
                $value = $product->getData($attribute->getAttributeCode());
            }
            return
                ((string)trim($value) == '') ? '' : $value;
    }
}
