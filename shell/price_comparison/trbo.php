<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Trbo extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Trbo Exporter';
	protected $_code = 'trbo';
	protected $_csvDelimiter;
	protected $_csvEnclosure = '"';
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	
	function getFields() {
		return array(
				'Article' => array('code' => 'sku'),
				'Name' => array('code' => 'name'),
				'ImageURL' => array('function' => 'getImageUrl'),
				'Deeplink' => array('function' => 'getUrl'),
				'Currency' => array('function' => 'setCurrency'),
				'Price' => array('function' => 'calcPrice'),
				'Old-Preis' => array('code' => 'msrp'),
				'Shipping' => array('function' => 'calcShipping'),
				);
	}
	
	function setCurrency() {		
		return "euro";
	}
} 