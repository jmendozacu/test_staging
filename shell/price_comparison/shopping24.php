<?php

require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_shopping24 extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'shopping24 Shooping Exporter';
	protected $_code = 'shopping24';
	protected $_csvDelimiter = ';';
	protected $_csvEnclosure = '"';
	
	const IN_STOCK = 'in stock';
	const OUT_OF_STOCK = 'out of stock';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	function getFields() {
		$returnValue=array(
				'art_number' => array('code' => 'sku'),
				'art_name' => array('code' => 'name'),
				'long_description' => array('function' => 'getDescription', 'fields' => array('short_description')),
				'image_url' => array('function' => 'getImageUrl'),
				'deep_link' => array('function' => 'getUrl_for_shopping24'),
				'price' => array('function' => 'calcPrice'),
				'currency' => array('static' => 'EUR'),
				'delivery_costs' => array('static' => '0,00'),
				'category' => array('function' => 'getCategory'),
				'brand' => array('function' => 'getManufacturer', 'fields' => array('abrand','manufacturer')),
				'gender_age' => array('static' => 'Unisex'),
				'color' => array('static' => ''),
				'clothing_size '=> array('static' => 'none'),
				'ean' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'delivery_time'  => array('code' => 'delivery_time'),

		);
		return $returnValue;
	}
	
	/**
	 * Get shopping24 availability for product
	 * 
	 * @param unknown $_product
	 */
	function getAvailability($_product) {
		if($_product->stock_item->getQty() > 0) {
			return self::IN_STOCK;
		}
		return self::OUT_OF_STOCK;
	}
	
	function calcPrice($_product) {
		if(!(float) $_product->getPrice() > 0) {
			throw new Exception('Product price is zero. ID: '.$_product->getId().' Name: '.$_product->getName().' Store: '.$this->_shell->currentStoreCode);
		}
		return str_replace(".",",",$this->_shell->_taxHelper->getPrice($_product, $_product->getPrice(), true));
	}
	

	function getUrl_for_shopping24($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=shopping24&utm_medium=cpc&utm_content=mp&utm_campaign=shopping24-mp&_\$ja=tsid:67953";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=shopping24&utm_medium=affiliate&utm_content=mp&utm_campaign=shopping24-mp&_\$ja=tsid:67953";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
	
}
