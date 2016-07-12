<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Evendi extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Evendi Exporter';
	protected $_code = 'evendi';
	protected $_csvDelimiter;
	protected $_csvEnclosure = '"';
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	
	function getFields() {
		return array(
				'Eindeutige Händler-Artikelnummer' => array('code' => 'sku'),
				'Preis in Euro' => array('function' => 'calcPrice'),
				'Kategorie' => array('function' => 'getCategory'),
				'Produktbezeichnung' => array('code' => 'name'),
				'Produktbeschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Link auf Detailseite' => array('function' => 'getUrl'),
				'Lieferzeit' => array('code' => 'delivery_time'),
				'EAN-Nummer' => array('code' => 'ps_ean'),
				'Hersteller-Artikelnummer' => array('code' => 'sku'),
				'Link auf Produktbild' => array('function' => 'getImageUrl'),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'VersandVorkasse' => array('function' => 'getPrepaymentShipping'),
				'VersandNachnahme' => array('function' => 'getInvoiceShipping'),
				'VersandLastschrift' => array('function' => 'getDebitShipping'),
				'VersandKreditkarte' => array('function' => 'getCreditShipping'),
				'VersandRechnung' => array('function' => 'getInvoiceShipping'),
				'PayPal' => array('function' => 'getPayPalShipping'),
				);
	}	
} 