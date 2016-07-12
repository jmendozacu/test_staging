<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Preissuchmaschine extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Preissuchmaschine Exporter';
	protected $_code = 'preissuchmaschine';
	protected $_csvDelimiter = '|';
	protected $_csvEnclosure = '';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = '|';
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Bestellnummer' => array('code' => 'sku'),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Bezeichnung' => array('code' => 'name'),
				'Preis' => array('function' => 'calcPrice'),
				'Lieferzeit' => array('code' => 'delivery_time'),
				'ProduktLink' => array('function' => 'getUrl'),
				'FotoLink' => array('function' => 'getImageUrl'),
				'Beschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'VersandNachnahme' => array('function' => 'calcShipping'),
				'VersandKreditkarte' => array('function' => 'getCreditShipping'),
				'VersandLastschrift' => array('function' => 'getDebitShipping'),
				'VersandBankeinzug' => array('function' => 'getDebitShipping'),
				'VersandRechnung' => array('function' => 'getInvoiceShipping'),				
				'VersandVorkasse' => array('function' => 'getPrepaymentShipping'),
				'EANCode' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Gewicht' => array('code' => 'weight'),
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