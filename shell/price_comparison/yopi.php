<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Yopi extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Yopi Exporter';
	protected $_code = 'yopi';
	protected $_csvDelimiter = ',';
	protected $_csvEnclosure = '"';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ',';
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Id' => array('code' => 'sku'),
				'Produktname' => array('code' => 'name'),
				'Preis' => array('function' => 'calcPrice'),
				'Produkt-Link' => array('function' => 'getUrl'),
				'Versandkosten' => array('function' => 'calcShipping'),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Beschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Bild-Link' => array('function' => 'getImageUrl'),
				'Kategorie' => array('function' => 'getCategoryPath'),
				'Verfügbarkeit' => array('code' => 'delivery_time'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Hersteller-Nr.' => array('code' => 'sku'),
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
	
}