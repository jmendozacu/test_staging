<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Preisde extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Preis.de Exporter';
	protected $_code = 'preisde';
	protected $_csvDelimiter = '\t';
	protected $_csvEnclosure = '"';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Artikelnummer' => array('code' => 'sku'),
				'Artikelbezeichnung' => array('code' => 'name'),
				'Beschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Kategorie' => array('function' => 'getCategoryPath'),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Preis' => array('function' => 'calcPrice'),
				'Produktlink' => array('function' => 'getUrl_for_preisde'),
				'Bildlink' => array('function' => 'getImageUrl'),
				'Lieferbar' => array('code' => 'delivery_time'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Herstellernummer' => array('code' => 'sku'),
				'Versandkosten' => array('function' => 'calcShipping'),
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
	
	function identifierExists($_product) {
		if($this->getManufacturer($_product) && $this->getEan($_product)) {
			return 'TRUE';
		}
		return 'FALSE';
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
	

	function getUrl_for_preisde($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=preis-de&utm_medium=cpc&utm_content=pv&utm_campaign=preis-pv&_\$ja=tsid:68179";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=preis-de&utm_medium=cpc&utm_content=pv&utm_campaign=preis-pv&_\$ja=tsid:68179";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
}