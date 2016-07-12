<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Yatego extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Yatego Exporter';
	protected $_code = 'yatego';
	protected $_csvDelimiter = '\t';
	protected $_csvEnclosure = '"';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'foreign_id' => array('code' => 'sku'),
				'article_nr' => array('code' => 'sku'),
				'title' => array('code' => 'name'),
				'tax' => array('function' => 'calcTax'),
				'price' => array('function' => 'calcPrice'),
				'delivery_calc_once' => array('function' => 'calcShipping'),
				'short_desc' => array('function' => 'getShortDescription', 'fields' => array('short_description')),
				'long_desc' => array('function' => 'getDescription', 'fields' => array('description')),
				'url' => array('function' => 'getUrl_for_yatego'),
				'picture' => array('function' => 'getImageUrl'),
				'categories' => array('function' => 'getCategoryPath'),
				'stock' => array('function' => 'getQty', 'fields' => array('qty')),
				'delivery_date' => array('code' => 'delivery_time'),
				'ean' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'manufacturer' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
		);
	}

	function getEan($_product) {
		if($_product->getPsEan() && $_product->getPsEan() != 0) return $_product->getPsEan();
		return '';
	}
	
	/**
	 * Calc Price for Google
	 * @see Mage_Shell_PriceComparison_Abstract::calcPrice()
	 */
	function calcPrice($_product) {
		return number_format(parent::calcPrice($_product), 2, null, '');
	}
	
	function getCategoryPath($_product){
		$currentCatIds = $_product->getCategoryIds();
		$categoryCollection = Mage::getResourceModel('catalog/category_collection')
													->addAttributeToSelect('name')
													->addAttributeToFilter('entity_id', $currentCatIds)
													->addIsActiveFilter();
		$return = array();
		foreach($categoryCollection as $cat){
			$return[]=$cat->getName();
		}
		return implode(' > ',$return);
	}
	
	function getUrl_for_yatego($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=yatego&utm_medium=affiliate&utm_content=mp&utm_campaign=yatego-mp&_\$ja=tsid:67954";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=yatego&utm_medium=affiliate&utm_content=mp&utm_campaign=yatego-mp&_\$ja=tsid:67954";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
	
}