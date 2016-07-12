<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Shopzilla extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Shopzilla Exporter';
	protected $_code = 'shopzilla';
	protected $_csvDelimiter;
	protected $_csvEnclosure = '"';
	protected $_categoryMapping = array(
		'Matratzen'				=> '16.376',
		'Bettwäsche'				=> '15.681',
		'Lattenroste'				=> '16.376',
		'Bettdecken & Kissen'	=> '15.681',
		'Wohndecken'			=> '15.681',
		'default'						=> '16.376'
	);
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Kategorie' => array('function' => 'getCategory'),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Bezeichnung' => array('code' => 'name'),
				'Beschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Artikel-URL' => array('function' => 'getUrl'),
				'Bild-URL' => array('function' => 'getImageUrl'),
				'SKU' => array('code' => 'sku'),
				'Preis' => array('function' => 'calcPrice'),
				'EAN' => array('code' => 'ps_ean'),
				'Versandkosten' => array('function' => 'calcShipping'),
				);
	}
	
	function getCategory($_product) {
		$cat = $_product->getCategoryIds();
		Mage::getModel('catalog/category')->load($cat[0])->getName();
		if(isset($this->_categoryMapping[$cName]))
			return $this->_categoryMapping[$cName];
		else
			return $this->_categoryMapping['default'];
	}
} 