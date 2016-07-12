<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Billiger extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Billiger.de Exporter';
	protected $_code = 'billiger';
	protected $_csvDelimiter = '\t';
	protected $_csvEnclosure = '';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
		//$this->_shipping_free_de = (float)Mage::getStoreConfig('iways_general/idealo_exports/shipping_free_de');
		//$this->_shipping_cost_de = Mage::getStoreConfig('iways_general/idealo_exports/shipping_cost_de');
	}
	
	function getFields() {
		return array(
				'aid' => array('code' => 'sku'),
				'brand' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'mpnr' => array('code' => 'sku'),
				'ean' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'name' => array('code' => 'name'),
				'desc' => array('function' => 'getDescription', 'fields' => array('description')),
				'shop_cat' => array('function' => 'getCategoryPath'),
				'price' => array('function' => 'calcPrice'),
				'link' => array('function' => 'getUrl_for_billiger'),
				'image' => array('function' => 'getImageUrl'),
				'dlv_time' => array('code' => 'delivery_time'),
				'dlv_cost' => array('function' => 'calcShipping'),
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
	
	function getUrl_for_billiger($_product) {
		try {
			$url = '';
			$store = $this->_shell->currentStoreCode;
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					if($store == "ps_de") {
						$url .= "/?utm_source=billiger&utm_medium=cpc&utm_content=pv&utm_campaign=billiger-pv&_\$ja=tsid:68175";
					} elseif ($store == "md_de") {
						$url .= "/?utm_source=billiger&utm_medium=cpc&utm_content=pv&utm_campaign=billiger-pv&_\$ja=tsid:68694";
					} else {
						$url .= "/?utm_source=billiger&utm_medium=cpc&utm_content=pv&utm_campaign=billiger-pv&_\$ja=tsid:68175";
					}
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				if($store == "ps_de") {
					$url .= "/?utm_source=billiger&utm_medium=cpc&utm_content=pv&utm_campaign=billiger-pv&_\$ja=tsid:68175";
				} elseif ($store == "md_de") {
					$url .= "/?utm_source=billiger&utm_medium=cpc&utm_content=pv&utm_campaign=billiger-pv&_\$ja=tsid:68694";
				} else {
					$url .= "/?utm_source=billiger&utm_medium=cpc&utm_content=pv&utm_campaign=billiger-pv&_\$ja=tsid:68175";
				}
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
	
}