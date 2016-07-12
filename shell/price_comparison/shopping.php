<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Shopping extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Shopping.com Exporter';
	protected $_code = 'shopping';
	protected $_csvDelimiter = ',';
	protected $_csvEnclosure = '"';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ',';
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Eindeutige Händler-SKU' => array('code' => 'sku'),
				'Produktname ' => array('code' => 'name'),
				'Preis' => array('function' => 'calcPrice'),
				'Versandkosten' => array('function' => 'calcShipping'),
				'Produkt-URL ' => array('function' => 'getUrl'),
				'Produktbild-URL' => array('function' => 'getImageUrl'),
				'Verfügbarkeit' => array('code' => 'delivery_time'),
				'Produktbeschreibung ' => array('function' => 'getDescription', 'fields' => array('description')),
				'Marke/Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'UPC oder EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'MPN' => array('code' => 'sku'),
				'Kategoriename' => array('function' => 'getCategoryPath'),
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