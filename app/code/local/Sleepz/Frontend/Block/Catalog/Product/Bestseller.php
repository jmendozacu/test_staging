<?php

class Sleepz_Frontend_Block_Catalog_Product_Bestseller extends Mage_Catalog_Block_Product_List {
//extends Mage_Core_Block_Template {

  public function getBestsellerProducts() {
    $storeId = (int) Mage::app()->getStore()->getId();

    // Date
    $date = new Zend_Date();
    $toDate = $date->setDay(1)->getDate()->get('Y-MM-dd');
    $fromDate = $date->subMonth(1)->getDate()->get('Y-MM-dd');

    $collection = Mage::getResourceModel('catalog/product_collection')
        ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
        ->addStoreFilter()
        ->addPriceData()
        ->addTaxPercents()
        ->addUrlRewrite()
        ->setPageSize($this->getSizeParameter())->setCurPage(1);

    // $collection->getSelect()
    //     ->joinLeft(
    //         array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
    //         "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId} AND aggregation.period BETWEEN '{$fromDate}' AND '{$toDate}'",
    //         array('SUM(aggregation.qty_ordered) AS sold_quantity')
    //     )
    //         ->group('e.entity_id')
    //         ->order(array('sold_quantity DESC', 'e.created_at'));

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        if($categoryId=$this->getData('category_id')) {
           $category = Mage::getModel('catalog/category')->load($categoryId);
           $collection->addCategoryFilter($category);
        }

        return $collection;
    }

    public function getProductsByCategory() {

      $products = Mage::getResourceModel('catalog/product_collection')
          ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
          ->addStoreFilter()
          ->addPriceData()
          ->addTaxPercents()
          ->addUrlRewrite()
          ->setPageSize($this->getSizeParameter())->setCurPage(1);

      Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
      Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
      Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

      if($categoryId=$this->getData('category_id')) {
         $category = Mage::getModel('catalog/category')->load($categoryId);
         $products->addCategoryFilter($category);
      }

      return $products;

      return $this->_productCollection;
    }

    protected function getSizeParameter() {
      //unset saved limits
      Mage::getSingleton('catalog/session')->unsLimitPage();
      return (isset($_REQUEST['size'])) ? intval($_REQUEST['size']) : 6;
    }
}
