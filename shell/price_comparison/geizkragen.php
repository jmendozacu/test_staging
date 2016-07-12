<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Geizkragen extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Geizkragen Exporter';
	protected $_code = 'geizkragen';
	protected $_csvDelimiter = ';';
	protected $_csvEnclosure = '"';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ';';
		parent::__construct($shell);
		$this->_shipping_free_de = (float)Mage::getStoreConfig('iways_general/idealo_exports/shipping_free_de');
		$this->_shipping_cost_de = Mage::getStoreConfig('iways_general/idealo_exports/shipping_cost_de');
	}
	
	function getFields() {
		return array(
				'Artikelnummer' => array('code' => 'sku'),
				'Kategorie' => array('function' => 'getCategoryPath'),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Produktname' => array('code' => 'name'),
				'Deeplink' => array('function' => 'getUrl_for_geizkragen'),
				'Bildlink' => array('function' => 'getImageUrl'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'HerstellerArtNr.' => array('code' => 'sku'),
				'Preis' => array('function' => 'calcPrice'),
				'VersandkostenVK' => array('function' => 'getPrepaymentShipping'),
				'VersandkostenNN' => array('function' => 'getInvoiceShipping'),
				'Lieferzeit' => array('code' => 'delivery_time'),
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
	
	function getUrl_for_geizkragen($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=geizkragen&utm_medium=cpc&utm_content=pv&utm_campaign=geizkragen-pv&_\$ja=tsid:68178";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=geizkragen&utm_medium=cpc&utm_content=pv&utm_campaign=geizkragen-pv&_\$ja=tsid:68178";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
}