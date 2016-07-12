<?php
/**
 * @category    Mage
 * @package     Mage_Shell
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_PriceComparison extends Mage_Shell_Abstract {
    
	// Helper variables
	const SEARCH_VISIBILITY = '3';
	const CATALOG_VISIBILITY = '2';
	const SINGLE_NOT_VISIBILITY = '1';
	const CATALOG_SEARCH_VISIBILITY = '4';
	const HIDDEN_CATEGORY_ID = "224";
	const MATTRESS_ATTRIBUTE_SET_ID = '9';
	const HIDDEN_ATTRIBUTE_SET_ID = '18';
	const BED_BASES_ATTRIBUTE_SET_ID = '10';
	const MATTRESS_COVERS_ATTRIBUTE_SET_ID = '11';
	const BED_SPREADS_ATTRIBUTE_SET_ID = '12';
	const SET_MATTRESSES_BED_BASES_ATTRIBUTE_SET_ID = '17';
	const KINDER_BETTWAESCHE_CATEGORY_ID = '61';

	// The exclude products ids
	/** @const XML config Path */
	const DEFAULT_XML_CONFIG_FILE = "exclude_products.xml";

	/** @const The XML mail node */
	const DEFAULT_XML_NODE = "exclude";
	
    /**
     * Run script
     */
    public function run() {
    	ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 54000);

		/**
		 * @var  $excludeIds
		 * Are the products of which are not to be displayed
		 */
		$excludeIds = $this->_excludeProducts();

    	if($this->getArg('info')) {
    		var_dump($this->_parseExportString('all'));
    	}elseif($this->getArg('export') || $this->getArg('exportall')) {
    		if(!$this->getArg('store_view')) {
    			die('Please define store_view'.PHP_EOL);
    		}else {
    			$stores = $this->_parseStoreViewString($this->getArg('store_view'));
    		}
    		if($this->getArg('export')) {
    			$exports_from_arg = $this->_parseExportString($this->getArg('export'));
    		}else {
    			$exports_from_arg = $this->_parseExportString('all');
    		}
    		$this->_taxHelper = Mage::helper('tax');
    		
    		foreach($stores as $store) {
    			
    			$this->currentStoreCode = $store->getCode();
    			$this->currentStoreId = $store->getId();
    			$this->currentStore = $store;
    			
    			Mage::app()->setCurrentStore($store->getCode());
    			$this->currentStoreCategories = $this->getAllCategories($this->currentStoreId);
    			$this->__('Exporting Store: '.$this->currentStoreCode.PHP_EOL.'Plattforms: ');
    			
    			//no datafeed for billiger at ps_de
    			if($this->currentStoreCode == "ps_de") {
    				$exports = $exports_from_arg;
    				if (array_key_exists('billiger', $exports)) {
    					unset($exports['billiger']);
    				}    				
    			} else {
    				$exports = $exports_from_arg;
    			}
    			
    			//Init export files
    			foreach($exports as $export) {
    				$this->__($export->name.' ');
    				$export->initCsvFile($this->currentStoreCode);
    			}
    			$this->__(PHP_EOL);
    			$fields = $this->getFieldsFromExports($exports);
    			$limit = 1000;
    			$page = 1;
    			do {
    				$productsCollection = Mage::getModel('catalog/product')
    				->getCollection()
    				->addAttributeToSelect($fields)
    				->addStoreFilter($store->getId())
    				->addAttributeToFilter('status', 1)
					->addAttributeToFilter('sku', array('nin' => $excludeIds))
    				->addTaxPercents()
    				->joinField('qty',
    		      			'cataloginventory/stock_item',
    						'qty',
    						'product_id=entity_id',
    						'{{table}}.stock_id=1',
    						'left'
    				)->setPage($page, $limit)
					 ->load();
    				
    				foreach ($productsCollection as $product) {
    					
    					//configurable Products should not be showed in datafeed
    					$pid = $product->getId();
    					$status = $product->getStatus();
    					
    					if(($product->getAttributeSetId() != self::HIDDEN_ATTRIBUTE_SET_ID) && !($this->checkProductInHiddenCategory($product)) && ($this->getArg('ignore_unvisible')=='yes' || $this->isVisible($product->getVisibility()) == true)) {
    						foreach($exports as $code => $export) {
    							try {
    								
    								//for billiger MD only mattress articles
    								$to_export = true;
    								if ($code == "fsecommerce" || $code == "effiliation" || $code == "google" || $code == "seoosg") {
    									$to_export = true;
    								} elseif ($code == "stylefruits") {
    									if ($product->getVisibility() == 1 && $product->getTypeId() != "configurable") {
    										$to_export = false;
    									}    									
    									if (($product->getAttributeSetId() == self::MATTRESS_ATTRIBUTE_SET_ID) || ($product->getAttributeSetId() == self::BED_BASES_ATTRIBUTE_SET_ID) 
											|| ($product->getAttributeSetId() == self::MATTRESS_COVERS_ATTRIBUTE_SET_ID) || ($product->getAttributeSetId() == self::BED_SPREADS_ATTRIBUTE_SET_ID) 
											|| ($product->getAttributeSetId() == self::SET_MATTRESSES_BED_BASES_ATTRIBUTE_SET_ID)) {
    										$to_export = false;
    									}
    									if($this->checkProductInChildCategory($product)) {
    										$to_export = false;
    									}
    								} else {
    									if ($product->getTypeId() == "configurable") {
    										$to_export = false;
    									}

    									//for billiger MD only mattress articles
    									if (($code == "billiger") && ($this->currentStoreCode == "md_de") && ($product->getAttributeSetId() != self::MATTRESS_ATTRIBUTE_SET_ID)) {
    										$to_export = false;
    									}
    								}    								
    								
    								if ($to_export) {
    									$export->export($product);
    								}
    							}catch (Exception $e) {
    								echo 'Warning: '.$code.'could not be exported.'.PHP_EOL.'Message: '.$e->getMessage();
    							}
    						}
    					}    					
                        $product->clearInstance();
                        unset($product);    				
    				}
    				$page++;
    			} while(count($productsCollection) == $limit);
    		}
    	}else {
    		echo $this->usageHelp();
    	}
    }   
    
    /**
     * Get all fields for exports
     * @param array $exports
     * @return multitype:
     */
    function getFieldsFromExports($exports) {
    	$fields = array('delivery_time' => true,
    					'description' => true, 
    					'price' => true,
    					'ps_bettwaesche' => true,
    					'ps_brand' => true,
    					'ps_color' => true,
    					'ps_completion' => true,
    					'ps_cover' => true,
    					'ps_cover_washable' => true,
    					'ps_duvet_filling' => true,
    					'ps_duvet_filling_value' => true,
    					'ps_extras' => true,
    					'ps_feet' => true,
    					'ps_firmness' => true,
    					'ps_hardness_level' => true,
    					'ps_hardness_level_value' => true,
    					'ps_head' => true,
    					'ps_height' => true,
    					'ps_material' => true,
    					'ps_material_color' => true,
    					'ps_matress_one' => true,
    					'ps_matress_two' => true,
	    				'ps_multi_color' => true, 	
    					'ps_multi_pattern' => true,
    					'ps_remote_control' => true,
		    			'ps_size' => true, 
		    			'ps_size_value' => true, 
    					'ps_topper' => true,
    					'ps_washable' => true,
    					'sku' => true,
		    			'visibility' => true, 
		    			'tax_class_id' => true, 
		    			'tax_percent' => true, 
    					'url_path' => true,
		    			);
    	foreach($exports as $export) {
    		$exportFields = $export->getFields();
    		foreach($exportFields as $exportField) {
    			if(isset($exportField['code'])) {
    				$fields[$exportField['code']] = true;
    			}elseif($exportField['fields']) {
    				foreach($exportField['fields'] as $fieldsField) {
    					$fields[$fieldsField] = true;
    				}
    			}
    		}
    	}
    	return array_keys($fields);
    }
    
    /**
     * Parse store information from string
     * @param string $string
     * @return multitype:
     */
    private function _parseStoreViewString($string) {
    	$store_codes = explode(',', $string);
    	$stores = array();
    	foreach(Mage::app()->getStores() as $store) {
    		if($store->getCode() && in_array($store->getCode(), $store_codes)) {
    			$stores[] = $store;
    		}
    	}
    	return $stores;
    }
    
    /**
     * Parse plattforms from export string
     * Checks if export class exists
     * 
     * @param string $string
     * @return Ambigous <multitype:, multitype:string unknown >
     */
    private function _parseExportString($string) {
    	$exports = array();
    	$codes = array();
    	if ($string == 'all') {
    		$codes = array(	'amazon',
    						'beezup',
    						'billiger',
    				 		'ciao',
    						'ebaycommerce', 
    						'effiliation',
    						'evendi',
    						'fsecommerce',
    						'geizhals',
    						'geizkragen',
    						'google',
    						'guenstiger',
    						'idealo',
    						'moebel',
    						'preisde',
    						'preisroboter',
    						'preissuchmaschine',
    						'productsfeed',
    						'schottenland',
    						'seoosg',
    						'shopping',
    						'shopping24',
    						'shopzilla',
    						'stylight',
    						'trbo',
    						'webgains',
    						'yopi',
    						'zanox');    				
    	} else if (!empty($string)) {
    		$codes = explode(',', $string);
    	}
    	foreach ($codes as $code) {
    		try{
    			if(file_exists('price_comparison/'.$code.'.php')) {
    				require_once 'price_comparison/'.$code.'.php';
    				$class = 'Mage_Shell_PriceComparison_'.ucfirst($code);
    				$exports[$code] = new $class($this);
    			} else {
    				echo 'Warning: Exportdefinition not found for '.$code.PHP_EOL;
    			}
    		}catch (Exception $e) {
    			echo $e->getMessage();
    		}
    	}
    	return $exports;
    }
	
	function isVisible($visibility) {
		if (($visibility == self::CATALOG_VISIBILITY) || ($visibility == self::SINGLE_NOT_VISIBILITY) || ($visibility == self::CATALOG_SEARCH_VISIBILITY)) {
			return true;
		}
		return false;
	}
	
	function getAllCategories($storeId) {
		if(!isset($this->_categoryList[$storeId])) {
			$categories = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect('name')
			->addAttributeToSelect('is_active')->load();
			foreach($categories as $category) {
				$this->_categoryList[$storeId][$category->getId()] = $category->getName();
			}
		}
		return $this->_categoryList[$storeId];
	}
	
	function checkProductInHiddenCategory($product) {
		$check_result = false;
		$productCatIds = $product->getCategoryIds();
		foreach ($productCatIds as $cid) {
			if($cid == self::HIDDEN_CATEGORY_ID) {
				$check_result = true;
			}
		}
		return $check_result;
	}
	
	function checkProductInChildCategory($product) {
		$check_result = false;
		$productCatIds = $product->getCategoryIds();
		foreach ($productCatIds as $cid) {
			if($cid == self::KINDER_BETTWAESCHE_CATEGORY_ID) {
				$check_result = true;
			}
		}
		return $check_result;
	}
	
	
	/**
	 * Retrieve Usage Help Message
	 *
	 */
	public function usageHelp()
	{
		return <<<USAGE
Usage:  php -f price_comparison.php -- [options]
	
  --export <plattform> --store_view <store_code>   Export products for plattform for store
  exportall --store_view <store_code>              Export all available plattforms for store
  info                                             Show allowed plattforms
  help                                            This help
  ignore_unvisible						   Include unvisible products in feed
	
  <plattform>     Comma separated plattform codes or value "all" for all plattforms
  <store_code>    One store view code

USAGE;
	}
	
	protected function __($string) {
		echo $string;
	}

	/**
	 * Dirty Way of fix a bug - exclude products from collection
	 *
	 * @throws Exception $e
	 * @return string|null
	 */
	protected function _excludeProducts()
	{
		try {
			$exProdIds = new Zend_Config_Xml(self::DEFAULT_XML_CONFIG_FILE, self::DEFAULT_XML_NODE);
		} catch (Exception $e) {

		}

		if ("" != $exProdIds->ids) {
			return explode(',', $exProdIds->ids);
		}
		return null;
	}
}

$shell = new Mage_Shell_PriceComparison();
$shell->run();
