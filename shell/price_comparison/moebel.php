<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Moebel extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Möbel.de Exporter';
	protected $_code = 'moebel';
	protected $_csvDelimiter = ';';
	protected $_csvEnclosure = '"';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ';';
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'art_nr' => array('code' => 'sku'),
				'art_name' => array('code' => 'name'),
				'art_kurztext' => array('function' => 'getShortDescription', 'fields' => array('short_description')),
				'art_preis' => array('function' => 'calcPrice'),
				'art_waehrung' => array('static' => 'EUR'),
				'art_beschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'art_img_url' => array('function' => 'get_ImgUrl'),
				'art_url' => array('function' => 'getUrl_for_moebel'),
				'art_marke' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'art_lieferkosten' => array('function' => 'calc_moebel_Shipping'),
				'art_ean' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'art_kategorie' => array('function' => 'getCategoryPath'),
				'art_lieferzeit' => array('code' => 'delivery_time'),
				'art_streichpreis' => array('function' => 'calcSalePrice'),
				'art_haertegrad_matratze' => array('code' => 'ps_hardness_level_value'),
				'art_masse' => array('function' => 'getSizeValue'),
				'art_matratzenmasse' => array('function' => 'getSizeValue'),
				'art_img_url2' => array('function' => 'get_ImgUrl_2'),
				'art_img_url3' => array('function' => 'get_ImgUrl_3'),
				'art_img_url4' => array('function' => 'get_ImgUrl_4'),
				'art_fuellmaterial'	=> array('code' => 'ps_duvet_filling_value'),
				'art_farbe' => array('function' => 'getColor'),
				'art_matratzenart' => array('function' => 'getMatratzenart'),
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
	/*
	function calcPrice($_product) {
		return number_format(parent::calcPrice($_product), 2, null, '');
	}
	*/
	
	function calcPrice($_product) {
		if($_product->getMsrp() && $_product->getMsrp() > 1) {
			return number_format($_product->getMsrp(), 2, ',', '');
		} else {
			return number_format(parent::calcPrice($_product), 2, ',', '');
		}
	}
	
	function calcSalePrice($_product) {
		if($_product->getMsrp() && $_product->getMsrp() > 1) {
			return number_format(parent::calcPrice($_product), 2, ',', '');
		} else {
			return "";
		}
	}
	
	function calc_moebel_Shipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		$shipping_free_de = (float)Mage::getStoreConfig('iways_general/idealo_exports/shipping_free_de',$store_id);
		$shipping_cost_de = Mage::getStoreConfig('iways_general/idealo_exports/shipping_cost_de',$store_id);
		$productprice = $this->_shell->_taxHelper->getPrice($_product, $_product->getPrice(), true);
		if((float) $productprice > $shipping_free_de) {
			return '0';
		}
		$shipping_cost_de = number_format($shipping_cost_de, 2, ',', '');
		return $shipping_cost_de;
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
	
	/**
	 * Get product url for moebel
	 * should like http://www.perfekt-schlafen.de/badenia-lattenrost-trendline-st28-starr/?referrer=moebel.de#182=3733
	 *
	 * @param unkown $_product
	 * @return string
	 */
	
	function getUrl_for_moebel($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?referrer=moebel.de&utm_source=moebel.de&utm_medium=cpc&utm_content=mp&utm_campaign=moebel-mp&_\$ja=tsid:67952";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?referrer=moebel.de&utm_source=moebel.de&utm_medium=affiliate&utm_content=mp&utm_campaign=moebel-mp&_\$ja=tsid:67952";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
	
	function getMatratzenart($_product) {
		$category = $this->getCategoryPath($_product);
		$deepestTree = explode(' > ',$category);
		$matratzenart = "";
		if(trim($deepestTree[0]) == "Matratzen") {
			$matratzenart = trim($deepestTree[count($deepestTree)-1]);
		}
		return $matratzenart;
	}
	
	function get_all_ImageUrl($_product) {
		$all_images = $_product->getMediaGalleryImages();
		$all_image_url = array();
		foreach($all_images as $image) {
			$all_image_url[] = $image->getUrl();
		}
		return $all_image_url;
	}
	
	function get_ImgUrl($_product) {
		$all_img = $this->get_all_ImageUrl($_product);
		if(isset($all_img[0]) && !empty($all_img[0])) {
			return $all_img[0];
		} else {
			return "";
		}
	}
	
	function get_ImgUrl_2($_product) {
		$all_img = $this->get_all_ImageUrl($_product);
		if(isset($all_img[1]) && !empty($all_img[1])) {
			return $all_img[1];
		} else {
			return "";
		}
	}
	
	function get_ImgUrl_3($_product) {
		$all_img = $this->get_all_ImageUrl($_product);
		if(isset($all_img[2]) && !empty($all_img[2])) {
			return $all_img[2];
		} else {
			return "";
		}
	}
	
	function get_ImgUrl_4($_product) {
		$all_img = $this->get_all_ImageUrl($_product);
		if(isset($all_img[3]) && !empty($all_img[3])) {
			return $all_img[3];
		} else {
			return "";
		}
	}
	
	function getSizeValue($_product) {
		$size = $_product->getPsSizeValue();
		return $size;
	}
	
	function getColor($_product) {
		$colors = $_product->getPsMultiColor();
		$color_array = explode(",", $colors);
		foreach ($color_array as $color) {
			$option_value = Mage::getModel('eav/entity_attribute_option')
							->getCollection()
							->setStoreFilter()
							->addFieldToFilter('main_table.option_id',array('eq'=>trim($color)))
							->getFirstItem();
			$color_name[] = $option_value->getValue();
		}
		return implode(",", $color_name);		
	}
}