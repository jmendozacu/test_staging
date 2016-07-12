<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Zanox extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Zanox Exporter';
	protected $_code = 'zanox';
	protected $_csvDelimiter = '\t';
	protected $_csvEnclosure = '"';
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	
	function getFields() {
		$my_array =  array(				
				'prod_number' => array('function' => 'getMagentoPID'),
				'prod_name' => array('code' => 'name'),
				'prod_price' => array('function' => 'calcPrice'),				
				'prod_price_old' => array('code' => 'msrp'),
				'bp' => array('function' => 'calcPrice'),
				'bt' => array('static' => 'St.'),
				'prod_url' => array('function' => 'getUrl_without_hashtag'),
				'category' => array('function' => 'getCategory'),
				'prod_description' => array('function' => 'getShortDescription'),
				'prod_description_long' => array('function' => 'getDescription', 'fields' => array('description')),
				'img_small' => array('function' => 'getImageUrl_small'),
				'img_medium' => array('function' => 'getImageUrl_medium'),
				'img_large' => array('function' => 'getImageUrl_large', 'fields' => array('image', 'image_url')),
				'manufacturer' => array('function' => 'getManufacturer', 'fields' => array('abrand','manufacturer')),
				'prod_ean' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'ShippingAndHandlingCost' => array('function' => 'calc_zanox_Shipping'),
				'delivery_time'  => array('code' => 'delivery_time'),
				);
		 return $my_array;
		
	}
	
	
	function getImageUrl_small($_product) {
		try {
			$image_url=(string) Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize(100,60);
			return $image_url;
		}catch (Exception $e) {
			return '';
		}
	}
	
	function getImageUrl_medium($_product) {
		try {
			$image_url=(string) Mage::helper('catalog/image')->init($_product, 'small_image')->resize(400,240);
			return $image_url;
		}catch (Exception $e) {
			return '';
		}
	}
	
	function getImageUrl_large($_product) {
		try {
			$_product->load('media_gallery');
			return Mage::helper('catalog/image')->init($_product, 'image');
		}catch (Exception $e) {
			return '';
		}
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
	
	function calc_zanox_Shipping($_product) {
		$shipping_cost = parent::calcShipping($_product);
		if(empty($shipping_cost)) {
			return '0';
		} else {
			$shipping_cost_de = $shipping_cost.' '.$this->_shell->currentStore->getCurrentCurrencyCode();
			return $shipping_cost_de;
		}
	}
	
	function getUrl_without_hashtag($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);					
					$url = $parentProduct->getUrlPath();					
				}
			} else {
				$url = $_product->getUrlPath();
			}
			$url .= "/?utm_source=zanox&utm_medium=affiliate&utm_content=mp&utm_campaign=affiliate-zanox&_\$ja=tsid:67951";
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
}
	