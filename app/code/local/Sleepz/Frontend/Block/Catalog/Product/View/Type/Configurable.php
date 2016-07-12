<?php

class Sleepz_Frontend_Block_Catalog_Product_View_Type_Configurable
    extends OrganicInternet_SimpleConfigurableProducts_Catalog_Block_Product_View_Type_Configurable
{
    public function getJsonConfig()
    {
        $config = Zend_Json::decode(parent::getJsonConfig());
        $p = $this->getProduct();

        $childProducts = $config['childProducts'];
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            $childProducts[$productId]["deliveryTime"] =
              $product->getResource()->getAttribute('delivery_time')->getFrontend()->getValue($product);
            $childProducts[$productId]["msrp"] = $p->getResource()->getAttribute('msrp')->getFrontend()->getValue($p);
        }

        $config['childProducts'] = $childProducts;

        return Zend_Json::encode($config);
        //parent getJsonConfig uses the following instead, but it seems to just break inline translate of this json?
        //return Mage::helper('core')->jsonEncode($config);
    }
}
