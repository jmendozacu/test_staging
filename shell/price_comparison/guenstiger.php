<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Guenstiger extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'günstiger.de Exporter';
	protected $_code = 'guenstiger';
	protected $_csvEnclosure = '';
	
	const IN_STOCK = 'Ja';
	const OUT_OF_STOCK = 'Nein';

	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ',';
		$this->_breadcrumbDelimiter = '/';
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Hersteller-Art.-Nr.' => array('code' => 'sku'),
				'Produktbezeichnung' => array('code' => 'name'),
				'Produktbeschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Klick-Out-URL' => array('function' => 'getUrl_for_guenstiger'),
				'Preis' => array('function' => 'calcPrice'),
				'Händler-Produktkat.' => array('function' => 'getCategory'),
				'Bild-URL' => array('function' => 'getImageUrl'),
				'Lagerstatus' => array('function' => 'getAvailability'),
				'Produktzustand' => array('static' => 'Neu'),
				'Standardversand' => array('function' => 'calcShipping'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
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
	
	function getUrl_for_guenstiger($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=guenstiger&utm_medium=cpc&utm_content=pv&utm_campaign=guenstiger-pv&_\$ja=tsid:68180";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=guenstiger&utm_medium=cpc&utm_content=pv&utm_campaign=guenstiger-pv&_\$ja=tsid:68180";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
}
