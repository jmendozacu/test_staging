<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Preisroboter extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Preisroboter Exporter';
	protected $_code = 'preisroboter';
	protected $_csvDelimiter = '|';
	protected $_csvEnclosure = '';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				'Artikel-Nr.' => array('code' => 'sku'),
				'Artikelname' => array('code' => 'name'),
				'Preis' => array('function' => 'calcPrice'),
				'Deeplink' => array('function' => 'getUrl_for_preisroboter'),
				'Bild-URL' => array('function' => 'getImageUrl'),
				'Kurzbeschr.' => array('function' => 'getDescription', 'fields' => array('description')),
				'Versandk.' => array('function' => 'calcShipping'),
				'Lieferzt.' => array('code' => 'delivery_time'),
				'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
				'Hersteller' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Hersteller-ArtNr' => array('code' => 'sku'),
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
		return number_format(parent::calcPrice($_product), 2,null,'');
	}
	
	function identifierExists($_product) {
		if($this->getManufacturer($_product) && $this->getEan($_product)) {
			return 'TRUE';
		}
		return 'FALSE';
	}
	

	function getUrl_for_preisroboter($_product) {
		try {
			$url = '';
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=preisroboter&utm_medium=cpc&utm_content=pv&utm_campaign=preisroboter-pv&_\$ja=tsid:68176";
					
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=preisroboter&utm_medium=cpc&utm_content=pv&utm_campaign=preisroboter-pv&_\$ja=tsid:68176";
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
}
