<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Schottenland extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Schottenland Exporter';
	protected $_code = 'schottenland';
	protected $_csvDelimiter = '|';
	protected $_csvEnclosure = '';
	
	const IN_STOCK = 'lagernd';
	const OUT_OF_STOCK = 'nicht lagernd';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Produktbezeichnung' => array('code' => 'name'),
				'Preis' => array('function' => 'calcPrice'),
				'Verfügbarkeit' => array('function' => 'getAvailability'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Hersteller AN' => array('static' => ''),
				'Deeplink' => array('function' => 'getUrl_for_schottenland'),
				'Artikelnummer' => array('code' => 'sku'),
				'Versand Vorkasse' => array('function' => 'getPrepaymentShipping'),
				'Versand Nachnahme' => array('function' => 'getInvoiceShipping'),
				'Versand Kreditkarte' => array('function' => 'getCreditShipping'),
				'Versand Bankeinzug' => array('function' => 'getDebitShipping'),
		);
	}
	
	/**
	 * Get Google availability for product
	 * 
	 * @param unknown $_product
	 */
	function getAvailability($_product) {
		/*
		if($_product->getQty() > 0) {
			return self::IN_STOCK;
		}
		return self::OUT_OF_STOCK;
		*/
		$pid = $_product->getSku();
		$qty = $_product->getQty();
		$store = $this->_shell->currentStoreCode;
		$att = $_product->getAttributeSetId();
		$siz = $_product->getPsSizeValue();
		if(stripos($siz, "Sondergr") !== false) {
			$special_size = true;
		} else {
			$special_size = false;
		}
		$dlv = $_product->getDeliveryTime();//delivery_time
		$dlv_array = array("18-25 Werktage","18-25 Werktagen","4-6 Wochen","8 - 9 Wochen");
		$delivery_quick = true;
		foreach ($dlv_array as $search_string) {
			if ($dlv == $search_string) {
				$delivery_quick = false;
			}
		}
		$discount = $this->check_price_discount($_product,25);$return_value = "";
		if($store == "ps_de" && $att == 9){
			if($qty > 1) {
				$return_value = self::IN_STOCK;
			} else {
				if ($special_size || $delivery_quick || $discount) {
					$return_value = self::IN_STOCK;
				} else {
					$return_value = self::OUT_OF_STOCK;
				}
			}
		} else {
			$return_value = self::IN_STOCK;
		}
	
		return $return_value;
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
		return number_format(parent::calcPrice($_product), 2,null,'');
	}
	
	function getUrl_for_schottenland($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=schottenland&utm_medium=cpc&utm_content=pv&utm_campaign=schottenland-pv&_\$ja=tsid:68177";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=schottenland&utm_medium=cpc&utm_content=pv&utm_campaign=schottenland-pv&_\$ja=tsid:68177";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
		
	}
	
	function check_price_discount($_product,$discount=25) {
		$check = false;
		if ($this->calcSalePrice($_product) > 1) {
			if ($this->calcSalePrice($_product) < ($this->calcPrice_new($_product)*$discount/100)) {
				$check = true;
			}
		}
		return $check;
	}
	
	function calcPrice_new($_product) {
		if($_product->getMsrp() && $_product->getMsrp() > 1) {
			return number_format($_product->getMsrp(), 2);
		} else {
			return number_format(parent::calcPrice($_product), 2);
		}
	}
	
	function calcSalePrice($_product) {
		if($_product->getMsrp() && $_product->getMsrp() > 1) {
			return number_format(parent::calcPrice($_product), 2);
		} else {
			return "";
		}
	}
	
	
}