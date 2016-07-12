<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Ciao extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Ciao Exporter';
	protected $_code = 'ciao';
	protected $_csvDelimiter;
	protected $_csvEnclosure = '"';
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
		$this->_shipping_free_de = (float)Mage::getStoreConfig('iways_general/idealo_exports/shipping_free_de');
		$this->_shipping_cost_de = Mage::getStoreConfig('iways_general/idealo_exports/shipping_cost_de');
	}
	
	
	function getFields() {
		return array(
				'Offer ID' => array('code' => 'sku'),
				'Brand' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Produkt Name' => array('code' => 'name'),
				'Category' => array('function' => 'getCategory'),
				'Description' => array('function' => 'getDescription', 'fields' => array('description')),
				'Image URL' => array('function' => 'getImageUrl', 'fields' => array('image', 'image_url')),
				'Produkt URL' => array('function' => 'getUrl_for_ciao'),
				'Delivery' => array('code' => 'delivery_time'),
				'ShippingCost' => array('function' => 'calcShipping'),
				'Price' => array('function' => 'calcPrice'),
				'Product ID' => array('code' => 'sku'),
				);
	}

	function getUrl_for_ciao($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=ciao&utm_medium=cpc&utm_content=pv&utm_campaign=ciao-pv&_\$ja=tsid:68183";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=ciao&utm_medium=cpc&utm_content=pv&utm_campaign=ciao-pv&_\$ja=tsid:68183";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
} 