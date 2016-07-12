<?php

class Sleepz_Frontend_Block_Catalog_Product_TopProducts extends Mage_Catalog_Block_Product_List {

  private $category = null;
  private $itemscount = null;
  private $productcount = null;

  public function getCategory() {
    if($this->category == null) {
      $this->category = Mage::getModel('catalog/category')->load($this->getData('category_id'));
    }

    return $this->category;
  }

  public function getProductcount() {
    if($this->getData('size')) {
      return $this->getData('size');
    }

    return 6;
  }

  public function getItemsCount() {
    if($this->getData('items')) {
      return $this->getData('items');
    }

    return 5;
  }

    public function getProductsByCategory() {

      $storeId = Mage::app()->getStore()->getId();
      $products = Mage::getResourceModel('catalog/product_collection');
      if($this->getCategory()->getId()) {
          $products = $this->_addProductAttributesAndPrices($products)
          ->addAttributeToSort('created_at', 'desc')
          ->addCategoryFilter($this->getCategory())
          ->setStoreId($storeId)
          ->addStoreFilter($storeId);
      }
      else {
          $products = $this->_addProductAttributesAndPrices($products)
          ->addAttributeToSort('created_at', 'desc')
          ->setStoreId($storeId)
          ->addStoreFilter($storeId);
      }

      Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
      Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
      Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

      $products->setPageSize($this->getProductcount())->setCurPage(1);

      return $products;
    }

    public function getCarouselId() {
      return $this->getData('carousel_id');
    }

}
