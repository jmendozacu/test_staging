<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Amazon extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'amazon Exporter';
	protected $_code = 'amazon';
	protected $_csvDelimiter = '\t';
	protected $_csvEnclosure = '';
	
	const IN_STOCK = 'in stock';
	const OUT_OF_STOCK = 'out of stock';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(				
				'Product Type' => array('function' => 'getProductType'),
				'Link' => array('function' => 'getUrl'),
				'SKU' => array('code' => 'sku'),
				'Standard Product ID' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Product ID Type' => array('static' => 'EAN'),
				'Title' => array('code' => 'name'),
				'Brand' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Manufacturer' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Description' => array('function' => 'getDescription', 'fields' => array('description')),
				'Mfr part number' => array('static' => ''),
				'Model Number' => array('static' => ''),
				'Update Delete' => array('static' => ''),
				'Price' => array('function' => 'calcPrice'),
				'Shipping Cost' => array('function' => 'calcShipping'),
				'Quantity' => array('static' => ''),
				'Department' => array('static' => ''),
				'Item package quantity' => array('static' => ''),
				'item display weight' => array('static' => ''),
				'item display volume' => array('static' => ''),
				'item display length' => array('static' => ''),
				'unit count' => array('static' => ''),
				'Category' => array('function' => 'getCategory'),
				'Recommended Browse Node' => array('function' => 'getAmazonBrowseNode'),
				'Bullet point1' => array('static' => ''),
				'Bullet point2' => array('static' => ''),
				'Bullet point3' => array('static' => ''),
				'Bullet point4' => array('static' => ''),
				'Bullet point5' => array('static' => ''),
				'Keywords1' => array('static' => ''),
				'Keywords2' => array('static' => ''),
				'Keywords3' => array('static' => ''),
				'Keywords4' => array('static' => ''),
				'Keywords5' => array('static' => ''),
				'Image' => array('function' => 'getImageUrl'),
				'Other image-url1' => array('static' => ''),
				'Other image-url2' => array('static' => ''),
				'Other image-url3' => array('static' => ''),
				'Other image-url4' => array('static' => ''),
				'Other image-url5' => array('static' => ''),
				'Other image-url6' => array('static' => ''),
				'Other image-url7' => array('static' => ''),
				'Other image-url8' => array('static' => ''),
				'parent child' => array('static' => ''),
				'parent sku' => array('static' => ''),
				'relationship type' => array('static' => ''),
				'variation theme' => array('static' => ''),
				'Color' => array('static' => ''),
				'Color Map' => array('static' => ''),
				'Size' => array('function' => 'getSize'),
				'Size Map' => array('static' => ''),
				'Ringgröße' => array('static' => ''),
				'Height' => array('static' => ''),
				'Length' => array('static' => ''),
				'Width' => array('static' => ''),
				'Weight' => array('static' => ''),
				'Shipping Weight' => array('static' => ''),
				'product subtype' => array('static' => ''),
				'is adult product' => array('static' => ''),
				'Outer Material Type' => array('static' => ''),
				'collection name' => array('static' => ''),
				'style name' => array('static' => ''),
				'fabric type' => array('static' => ''),
				'season' => array('static' => ''),
				'sport' => array('static' => ''),
				'target audience base1' => array('static' => ''),
				'target audience base2' => array('static' => ''),
				'target audience base3' => array('static' => ''),
				'target audience base4' => array('static' => ''),
				'target audience base5' => array('static' => ''),
				'Scent' => array('static' => ''),
				'specific uses keywords1' => array('static' => ''),
				'specific uses keywords2' => array('static' => ''),
				'specific uses keywords3' => array('static' => ''),
				'specific uses keywords4' => array('static' => ''),
				'specific uses keywords5' => array('static' => ''),
				'special_features1' => array('static' => ''),
				'special_features2' => array('static' => ''),
				'special_features3' => array('static' => ''),
				'special_features4' => array('static' => ''),
				'special_features5' => array('static' => ''),
				'ingredients1' => array('static' => ''),
				'ingredients2' => array('static' => ''),
				'ingredients3' => array('static' => ''),
				'model year' => array('static' => ''),
				'target audience keywords1' => array('static' => ''),
				'target audience keywords2' => array('static' => ''),
				'target audience keywords3' => array('static' => ''),
				'display type' => array('static' => ''),
				'water resistance depth' => array('static' => ''),
				'watch movement' => array('static' => ''),
				'League and Team' => array('static' => ''),
				'volume capacity name' => array('static' => ''),
				'Size per pearl' => array('static' => ''),
				'Total Diamond Weight' => array('static' => ''),
				'band material' => array('static' => ''),
				'metal type' => array('static' => ''),
				'ring size' => array('static' => ''),
				'Computer CPU speed' => array('static' => ''),
				'Computer memory size' => array('static' => ''),
				'Hard disk size' => array('static' => ''),
				'Included RAM size' => array('static' => ''),
				'Flash drive size' => array('static' => ''),
				'Operating system' => array('static' => ''),
				'Display size' => array('static' => ''),
				'Screen Resolution' => array('static' => ''),
				'Display technology' => array('static' => ''),
				'Digital Camera Resolution' => array('static' => ''),
				'Optical zoom' => array('static' => ''),
				'Memory Card Type' => array('static' => ''),
				'Flavor' => array('static' => ''),
				'Specialty' => array('static' => ''),
				'Occasion' => array('static' => ''),
				'Cuisine' => array('static' => ''),
				'Maximum age' => array('static' => ''),
				'Minimum age' => array('static' => ''),
				'Gender' => array('static' => ''),
				'Color and finish' => array('static' => ''),
				'Material' => array('static' => ''),
				'country of origin' => array('static' => ''),
				'eu toys safety directive age warning' => array('static' => ''),
				'eu toys safety directive warning' => array('static' => ''),
				'eu toys safety directive language' => array('static' => ''),
				'legal disclaimer description' => array('static' => ''),
				'safety warning' => array('static' => ''),
				'fedas id' => array('static' => ''),				
		);
	}
	
	function getProductType($_product) {
		$att = $_product->getAttributeSetId();
		if($att == 19) {
			$type = "HOME_FURNITURE_AND_DECOR";
		} else {
			$type = "HOME_BED_AND_BATH";
		}
		return $type;
	}
	
	
	/**
	 * Get Google availability for product
	 * 
	 * @param unknown $_product
	 */
	function getQuantity($_product) {
		return $_product->getQty();
	}
	
	function getSize($_product) {
		$size = $_product->getPsSizeValue();
		return $size;
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
	
	function getAmazonBrowseNode($_product) {
		$shop_category = $this->getCategory($_product);
		$browse_node = "";
		if(stripos($shop_category, "Kindermatratzen") !== false) {
			$browse_node = "2810170031";
		}elseif(stripos($shop_category, "Bettwäsche") !== false) {
			$browse_node = "3340959031";
		}elseif (stripos($shop_category, "Bettdecken") !== false) {
			$browse_node = "10398681";
		}elseif (stripos($shop_category, "Sapnnbettlaken") !== false) {
			$browse_node = "3340973031";
		}elseif (stripos($shop_category, "Kopfkissen") !== false) {
			$browse_node = "391454011";
		}elseif (stripos($shop_category, "Kinderbettdecken") !== false) {
			$browse_node = "33409920331";
		}elseif (stripos($shop_category, "Accessoires") !== false) {
			$browse_node = "202876031";
		}elseif (stripos($shop_category, "Lattenroste") !== false) {
			$browse_node = "343481011";
		}elseif (stripos($shop_category, "Federkernmatratzen") !== false) {
			$browse_node = "526019031";
		}elseif (stripos($shop_category, "Taschenfederkernmatratzen") !== false) {
			$browse_node = "526019031";
		}elseif (stripos($shop_category, "kaltschaummatratzen") !== false) {
			$browse_node = "526018031";
		}elseif (stripos($shop_category, "Viscoschaummatratzen") !== false) {
			$browse_node = "528037031";
		}elseif (stripos($shop_category, "set-angebote") !== false) {
			$browse_node = "2864794031";
		}elseif (stripos($shop_category, "topper") !== false) {
			$browse_node = "2810150031";
		}elseif (stripos($shop_category, "Matratzenschoner") !== false) {
			$browse_node = "2810151031";
		}
		return $browse_node;		
	}
	
}