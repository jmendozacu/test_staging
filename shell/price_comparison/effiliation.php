<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Effiliation extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'effiliation Exporter';
	protected $_code = 'effiliation';
	protected $_csvDelimiter = ';';
	protected $_csvEnclosure = '"';
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	
	function getFields() {
		return array(
				'Produktname' => array('code' => 'name'),
				'Produkt-Nummer' => array('function' => 'getMagentoPID'),
				'Produkt-Kategorie' => array('function' => 'getCategoryPath'),
				'Produktbeschreibung' => array('function' => 'get_nohtmlcode_description'),
				'Deeplink' => array('function' => 'getUrl_for_effiliation'),
				'Preis' => array('function' => 'calcPrice'),
				'Preis_AB' => array('function' => 'calcPriceAB'),
				'Währung' => array('static' => 'EUR'),
				'Produktbild' => array('function' => 'getImageUrl'),
				'Lieferzeit' => array('code' => 'delivery_time'),
				'Lieferkosten' => array('function' => 'calcShipping'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Herstellernummer' => array('code' => 'sku'),
				'Shopsiegel' => array('static' => 'Trusted Shop'),
				'Bezahlmoeglichkeiten' => array('static' => 'Rechnung, Kreditkarte, Sofortüberweisung, Vorkasse, Paypal, Barzahlen'),
				'Verfuegbarkeit' => array('static' => 'ja'),
				);
	}
	
	function getEan($_product) {
		if($_product->getPsEan() && $_product->getPsEan() != 0) return $_product->getPsEan();
		return '';
	}
	
	function get_nohtmlcode_description($_product) {
		$des = html_entity_decode(parent::getDescription($_product), ENT_COMPAT, 'UTF-8');
		return $des;
	}
	
	function calcPrice($_product) {
		if($_product->getTypeId() == "configurable"){
			$priceValue = $this->getLowestConfigPrice($_product);
    	} else {
    		$priceValue = $_product->getPrice();
    	}
    	$price = $this->_shell->_taxHelper->getPrice($_product, $priceValue, true);
    	if(!(float) $price > 0) {
    		throw new Exception('Product price is zero. ID: '.$_product->getId().' Name: '.$_product->getName().' Store: '.$this->_shell->currentStoreCode);
    	}
		return number_format($price, 2, null, '');
	}
	
	function calcPriceAB($_product) {
		$price_ab = "";
		if($_product->getTypeId() == "configurable"){
			$price_ab = "ab ";
		} 
		return $price_ab;
	}
	
	function getLowestConfigPrice( $product ) {
		$childProducts = Mage::getSingleton('catalog/product_type_configurable')->getUsedProducts( null, $product );
		$childPriceLowest = '';
		if ( $childProducts ) {
			foreach ( $childProducts as $child ) {
				$_child = Mage::getSingleton('catalog/product')->load( $child->getId() );
				if ( $childPriceLowest == '' || $childPriceLowest > $_child->getPrice() ) {
					$childPriceLowest =  $_child->getPrice();
				}
			}
		} else {
			$childPriceLowest = $product->getPrice();
		}
		return $childPriceLowest;
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
	
	function getUrl_for_effiliation($_product) {
		try {
			$url = '';
			$store = $this->_shell->currentStoreCode;
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					#$url .= "/?utm_source=effiliation&utm_medium=affiliate&utm_content=mp&utm_campaign=affiliate-effiliation&_\$ja=tsid:68695";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				#$url .= "/?utm_source=effiliation&utm_medium=affiliate&utm_content=mp&utm_campaign=affiliate-effiliation&_\$ja=tsid:68695";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;;
		}catch (Exception $e) {
			return '';
		}
	}
}