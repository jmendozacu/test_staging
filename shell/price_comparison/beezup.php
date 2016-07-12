<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Beezup extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'BeezUP Exporter';
	protected $_code = 'beezup';
	protected $_csvDelimiter = '|';
	protected $_csvEnclosure = '';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		parent::__construct($shell);
	}
	
	const IN_STOCK = 'in stock';
	const OUT_OF_STOCK = 'out of stock';
	
	function getFields() {
		return array(
				//Brand |Category 1|Category 2|availability|Name|Price VAT included|EAN|Id|Short Description|URL Images|URLs|Shipping costs|Delivery
				'Brand' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Category 1' => array('function' => 'get_category_1'),
				'Category 2' => array('function' => 'get_category_2'),
				'Category 3' => array('function' => 'get_category_3'),
				'availability' => array('function' => 'getAvailability'),
				'Name' => array('code' => 'name'),
				'Price VAT included' => array('function' => 'calcPrice'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Id' => array('code' => 'sku'),
				'Short Description' => array('function' => 'getDescription', 'fields' => array('description')),
				'URL Images' => array('function' => 'get_ImgUrl'),
				'URLs' => array('function' => 'getUrl'),
				'Shipping costs' => array('function' => 'calcShipping'),
				
				/*
				'Product reference' => array('code' => 'sku'),
				'Produktname' => array('code' => 'name'),
				'Produktbeschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Preis inkl. Mwst.' => array('function' => 'calcPrice'),
				'UVP inkl. Mwst.' => array('function' => 'calcUVPPrice'),
				'Produkt URL' => array('function' => 'getUrl'),
				'Bild URL' => array('function' => 'get_ImgUrl'),
				'Lieferkosten GER' => array('static' => '0'),
				'Kategorie 1' => array('function' => 'getCategory'),
				'Marke' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'EAN/UPC' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Zustand' => array('static' => 'new'),
				*/
		);
	}
	

	/**
	 * Get Google availability for product
	 * 
	 * @param unknown $_product
	 */
	
	function getEan($_product) {
		if($_product->getPsEan() && $_product->getPsEan() != 0) return $_product->getPsEan();
		return '';
	}
	
	/**
	 * Calc Price for Google
	 * @see Mage_Shell_PriceComparison_Abstract::calcPrice()
	 */
	function calcPrice($_product) {
		return number_format(parent::calcPrice($_product), 2, '.', '');
	}
	
	function calcUVPPrice($_product) {
		if($_product->getMsrp() && $_product->getMsrp() > 1) {
			return number_format($_product->getMsrp(), 2, '.', '');
		} else {
			return "";
		}
	}

	function identifierExists($_product) {
		if($this->getManufacturer($_product) && $this->getEan($_product)) {
			return 'TRUE';
		}
		return 'FALSE';
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
	
	function getAdditionalImage($_product) {
		$all_img = $this->get_all_ImageUrl($_product);
		$addition_img = "";
		$num = count($all_img); 
		for($i=1; $i<$num; $i++) {
			if(!empty($all_img[$i])) {
				$addition_img .= $all_img[$i]. ",";
			}
		}
		$addition_img = rtrim($addition_img, ",");
		return $addition_img;
	}
	
	function get_category($_product) {
		$deepestTreeCount = 0;
		$deepestTree = null;
		foreach($_product->getCategoryCollection() as $category) {
			$categoryPath = explode('/', $category->getPath());
			//Remove root cat id
			array_shift($categoryPath);
			//Remove store root cat;
			array_shift($categoryPath);
			$catTree = array();
			foreach($categoryPath as $cat) {
				if(isset($this->_shell->currentStoreCategories[$cat])) {
					$catTree[] = $this->_shell->currentStoreCategories[$cat];
				}
			}
			if(count($catTree) > $deepestTreeCount) {
				$deepestTreeCount = count($catTree);
				$deepestTree = $catTree;
			}
		}
	
		return $deepestTree;
	}
	
	function get_category_1($_product) {
		$category_array = $this->get_category($_product);
		if(!empty($category_array[0])) {
			return $category_array[0];
		} else {
			return "";
		}		
	}
	
	function get_category_2($_product) {
		$category_array = $this->get_category($_product);
		if(!empty($category_array[1])) {
			return $category_array[1];
		} else {
			return "";
		}
	}
	
	function get_category_3($_product) {
		$category_array = $this->get_category($_product);
		if(!empty($category_array[2])) {
			return $category_array[2];
		} else {
			return "";
		}
	}
}
