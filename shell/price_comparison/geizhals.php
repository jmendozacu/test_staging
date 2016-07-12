<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Geizhals extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'geizhals.at Exporter';
	protected $_code = 'geizhals';
	protected $_csvEnclosure = '';
	protected $_csvDelimiter = ';';
	
	const IN_STOCK = 'lagernd';
	const OUT_OF_STOCK = 'nicht lagernd';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ';';
		parent::__construct($shell);
	}

	function getFields() {
		return array(
				'Produktbezeichnung' => array('code' => 'name'),
				'Herstellername' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Preis' => array('function' => 'calcPrice'),
				'Deeplink' => array('function' => 'getUrl'),
				'Herstellernummer' => array('code' => 'sku'),				
				'Beschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Verfügbarkeit' => array('function' => 'getAvailability'),
				'Versand Vorkasse' => array('function' => 'getPrepaymentShipping'),
				'Versand Nachnahme' => array('function' => 'getInvoiceShipping'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Produktbeschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
		);
	}
	
	/**
	 * Get Google availability for product
	 * 
	 * @param unknown $_product
	 */
	function getAvailability($_product) {
		if($_product->getQty() > 0) {
			return self::IN_STOCK;
		}
		return self::OUT_OF_STOCK;
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
		return number_format(parent::calcPrice($_product), 2, '.', '').' '.$this->_shell->currentStore->getCurrentCurrencyCode();
	}
	
	function identifierExists($_product) {
		if($this->getManufacturer($_product) && $this->getEan($_product)) {
			return 'TRUE';
		}
		return 'FALSE';
	}
	
}
