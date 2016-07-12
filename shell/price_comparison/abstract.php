<?php
/**
 *
 * Mage_Shell_PriceComparison_Abstract
 *
 * Main Plattform export logic
 *
 * Define Fields and Mapping in getFields
 *
 * @abstract
 *
 */
abstract class Mage_Shell_PriceComparison_Abstract {
	protected $_shell;
	protected $_name;
	protected $_handler;
	protected $_file = '../export/price_comparison/tmp/{STORE_CODE}_{CODE}.csv';
	protected $_csvDelimiter = ';';
	protected $_csvEnclosure = '"';
	protected $_lineBreak = '"';
	protected $_breadcrumbDelimiter = '>';

	/**
	 * Construct
	 * @param Mage_Shell_PriceComparison $shell
	 */
	function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_shell = $shell;
	}

	/**
	 * Define Mapping Fields
	 */
	abstract function getFields();

	/**
	 *
	 * Export Product to csv file
	 *
	*/
	function export($_product) {
		$writeLine = true;
		try {
			$csvLineArray = array();			
			foreach($this->getFields() as $headerCode => $csvField) {				
				if(isset($csvField['code'])) {
						$csvLineArray[$headerCode] = $_product->getData($csvField['code']);
				} elseif (isset($csvField['function'])) {
					if(method_exists($this, $csvField['function'])) {
							$csvLineArray[$headerCode] = $this->{$csvField['function']}($_product);
						if(
							$headerCode == "art_img_url"			||
							$headerCode == 'image' 					|| 
							$headerCode == 'Image URL' 				||
							$headerCode == 'Link auf Produktbild'	||
							$headerCode == 'Bildlink' 				||
							$headerCode == 'image_link' 			||
							$headerCode == 'Bild-URL' 				||
							$headerCode == 'BildURL_1' 				||
							$headerCode == 'FotoLink' 				||
							$headerCode == 'Produktbild-URL' 		||
							$headerCode == 'picture' 				||
							$headerCode == 'image_url2' 			||
							$headerCode == 'image_url'	 			||
							$headerCode == 'Bild-Link'
						){
							if(strpos($csvLineArray[$headerCode],"placeholder/") || strpos($csvLineArray[$headerCode],"nopicture")){	
								$pid = $_product->getId();
								//echo "$pid nopicture"."\n";
								$writeLine=false;
							}
						}
					}else { 
							$csvLineArray[$headerCode] = '';
					}
				}elseif(isset($csvField['static'])) {
					$csvLineArray[$headerCode] = $csvField['static'];
				} else {
					$csvLineArray[$headerCode] = '';
				}
			}
			foreach($csvLineArray as &$val) {
				$val = $this->_removeCsvDivider($val);
			}
			if($writeLine){
				$this->_writeCsvLine($this->implodeLine($csvLineArray), true);
				$writeLine=true;
			}
		}catch (Exception $e) {
			$this->_printError($e->getMessage());
		}
	}

	/***************************************************
	 *
	* CSV Handling
	*
	***************************************************/

	/**
	 * Create file with Path of $this->getFile($store_code)
	 *
	 * @param string $store_code
	 * @return FileHandler $this->_handler
	 */
	function initCsvFile($store_code) {
		$fileName = $this->getFile($store_code);
		if(($this->_handler = fopen($fileName, 'w')) === false) {
			throw new Exception("Filename '" . $fileName . "' could not be opened.");
		}
		$this->_writeCsvHeader();
		return $this->_handler;
	}

	/**
	 * Get Filename for Store and Plattform
	 * @param string $store_code
	 * @throws Exception
	 * @return Ambigous <string;, mixed>
	 */
	function getFile($store_code) {
		if($this->_code) {
			
			if($this->_code == "amazon") {
				$this->_file = '../export/price_comparison/tmp/{STORE_CODE}_{CODE}.txt';
			}
			
			$file = $this->replaceToken('{CODE}', $this->_code, $this->_file);
			return $this->replaceToken('{STORE_CODE}', $store_code, $file);
		}else {
			throw new Exception('Code not set');
		}
		

		
	}

	/**
	 * Get csv header
	 * @return multitype:
	 */
	function _writeCsvHeader(){
		if($this->_code == "amazon") {
			foreach($this->amazon_header() as $header) {
				$this->_writeCsvLine(
						$this->implodeLine($header),true
				);
			}
		}
		$this->_writeCsvLine(
				$this->implodeLine(array_keys($this->getFields())),true
			);
	}

	/**
	 * Clean CSV String
	 * @param string $string
	 * @return mixed
	 */
	function _removeCsvDivider($string) {
		return str_replace($this->_csvDelimiter, '', str_replace($this->_csvEnclosure, '', $string));
	}

	/**
	 * Writes data to csv file
	 */
	function _writeCsvLine($data, $newLine = false) {
		if($this->_handler) {
			@fwrite($this->_handler, $newLine ? $data.PHP_EOL : $data);
		}
	}

	/**
	 * Implode Array to csv line with enclosure and delimiter
	 *
	 * @param array $array
	 * @return string
	 */
	function implodeLine($array) {
		$line = implode($this->_csvEnclosure.$this->_csvDelimiter.$this->_csvEnclosure, $array);
		return $this->_csvEnclosure.$line.$this->_csvEnclosure;
	}

	/***************************************************
	 *
	* Product data handling
	*
	***************************************************/

	/**
	 * Calc product price for export
	 *
	 * @param unknown $_product
	 */
	function calcPrice($_product) {
		if(!(float) $_product->getPrice() > 0) {
			throw new Exception('Product price is zero. ID: '.$_product->getId().' Name: '.$_product->getName().' Store: '.$this->_shell->currentStoreCode);
		}
		return $this->_shell->_taxHelper->getPrice($_product, $_product->getPrice(), true);
	}
	
	/**
	 * Calc product price for export
	 *
	 * @param unknown $_product
	 */
	function calcTax($_product) {
		if(!(float) $_product->getPrice() > 0) {
			throw new Exception('Product price is zero. ID: '.$_product->getId().' Name: '.$_product->getName().' Store: '.$this->_shell->currentStoreCode);
		}
		return $this->_shell->_taxHelper->getPrice($_product, $_product->getPrice(), true)-$this->_shell->_taxHelper->getPrice($_product, $_product->getPrice(), false);
	}
	
	/**
	 * Calc product shipping price for export
	 *
	 * @param unknown $_product
	 */
	function calcShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		$shipping_free_de = (float)Mage::getStoreConfig('iways_general/idealo_exports/shipping_free_de',$store_id);
		$shipping_cost_de = Mage::getStoreConfig('iways_general/idealo_exports/shipping_cost_de',$store_id);
		$productprice = $this->_shell->_taxHelper->getPrice($_product, $_product->getPrice(), true);
		if((float) $productprice > $shipping_free_de) {
			return '0';
		}
		$shipping_cost_de = number_format($shipping_cost_de, 2, '.', '');
		return $shipping_cost_de;
	}
	

	function getDebitShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		if(Mage::getStoreConfig('iways_general/idealo_exports/debit',$store_id)) {
			return $this->calcShipping($_product);
		}
		return '0';
	}
	
	function getSofortShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		if(Mage::getStoreConfig('iways_general/idealo_exports/sofort',$store_id)) {
			return $this->calcShipping($_product);
		}
		return '0';
	}
	
	function getCreditShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		if(Mage::getStoreConfig('iways_general/idealo_exports/credit_cart',$store_id)) {
			return $this->calcShipping($_product);
		}
		return '0';
	}
	
	function getPayPalShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		if(Mage::getStoreConfig('iways_general/idealo_exports/paypal',$store_id)) {
			return $this->calcShipping($_product);
		}
		return '0';
	}
	
	function getInvoiceShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		if(Mage::getStoreConfig('iways_general/idealo_exports/invoice',$store_id)) {
			return $this->calcShipping($_product);
		}
		return '0';
	}
	
	function getPrepaymentShipping($_product) {
		$store_id = $this->_shell->currentStoreId;
		if(Mage::getStoreConfig('iways_general/idealo_exports/prepayment',$store_id)) {
			return $this->calcShipping($_product);
		}
		return '0';
	}
	
	
	/**
	 * Remove line breaks in product description and replace them with $this->_lineBreak
	 *
	 * @param unknown $_product
	 * @return mixed
	 */
	function getDescription($_product) {
		$_product->load('description');
		$breaks = array("\r\n", "\n", "\r");
		return str_replace($this->_csvEnclosure, '', str_replace($breaks, $this->_lineBreak , $_product->getData('description')));
	}

	function getShortDescription($_product) {
		$_product->load('description');
		$breaks = array("\r\n", "\n", "\r");
		return str_replace($this->_csvEnclosure, '', str_replace($breaks, $this->_lineBreak , $_product->getData('short_description')));
	}
	
	function getMagentoPID($_product) {
		return $_product->getId();
	}
	
	/**
	 * Get product url for current store
	 *
	 * @param unkown $_product
	 * @return string
	 */
	function getUrl($_product) {
		try {
			$url = '';
			$pid = $_product->getId();
			$url = $_product->getUrlPath();
			
			if($_product->getVisibility() == 1)  {
				if ($_product->getTypeId() != "configurable") {
					$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
					if(isset($parentId[0])){
						$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
						$url = $parentProduct->getUrlPath();
						$variant_url = $this->getVariantUrl($_product);						
						$url .= $variant_url;	
						$parent_id = $parentProduct->getId();
					}					
				}
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
			
		}catch (Exception $e) {
			return '';
		}
	}

	/**
	 * Return image for product
	 *
	 * @param unknown $_product
	 * @return string
	 */
	/*
	function getImageUrl($_product) {
		try {
			$_product->load('media_gallery');
			return Mage::helper('catalog/image')->init($_product, 'image');
		}catch (Exception $e) {
			return '';
		}
	}
	*/
	
	function getAllImageUrl($_product) {
		$all_images = $_product->getMediaGalleryImages();
		$all_image_url = array();
		foreach($all_images as $image) {
			$all_image_url[] = $image->getUrl();
		}
		return $all_image_url;
	}
	
	function getImageUrl($_product) {
		$all_img = $this->getAllImageUrl($_product);
		if(isset($all_img[0]) && !empty($all_img[0])) {
			return $all_img[0];
		} else {
			return "";
		}
	}

	/**
	 * Return categories as tree for product
	 * @param unknown $_product
	 */
	function getCategory($_product) {
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
		
		return implode(' '.$this->_breadcrumbDelimiter.' ', $deepestTree);
	}
	
	function getManufacturer($_product) {
		try {
			if($_product->getPsBrand()) {
				return $_product->getAttributeText('ps_brand');
			}
		}catch (Exception $e) {
			return '';
		}
		return $_product->getManufacturer();
	}
	
	function getBasePrice($_product) {
		if (! ($productAmount = $_product->getBasePriceAmount())) return '';
		
		$this->_loadDefaultBasePriceValues($_product);
		
		if (! ($referenceAmount = $_product->getBasePriceBaseAmount())) return '';
		if (! ($productPrice = $_product->getFinalPrice())) return '';
		if (! is_numeric($productAmount) || ! is_numeric($referenceAmount) || ! is_numeric($productPrice)) return '';
		
		$productUnit = $_product->getBasePriceUnit();
		$referenceUnit = $_product->getBasePriceBaseUnit();

		$productPrice = Mage::helper('tax')->getPrice($_product, $productPrice, Mage::getStoreConfig('catalog/baseprice/base_price_incl_tax', Mage::app()->getStore()));
		$basePriceModel = Mage::getModel('baseprice/baseprice', array('reference_unit' => $referenceUnit, 'reference_amount' => $referenceAmount));
		$basePrice = $basePriceModel->getBasePrice($productAmount, $productUnit, $productPrice);
		
		if (is_string($labelFormat))
		{
			$label = $labelFormat;
		}
		else
		{
			$configKey = $labelFormat ? 'short_label' : 'frontend_label';
			$label = Mage::getStoreConfig('catalog/baseprice/'.$configKey, Mage::app()->getStore());
		}

		$label = str_replace('{{baseprice}}', Mage::helper('core')->currency($basePrice,true, false), $label);
		$label = str_replace('{{product_amount}}', $productAmount, $label);
		$label = str_replace('{{product_unit}}', $_product->getAttributeText('base_price_unit'), $label);
		$label = str_replace('{{product_unit_short}}', $_product->getAttributeText('base_price_unit'), $label);
		$label = str_replace('{{reference_amount}}', $referenceAmount, $label);
		$label = str_replace('{{reference_unit}}', $_product->getData('base_price_base_unit'), $label);
		$label = str_replace('{{reference_unit_short}}', $_product->getAttributeText('base_price_base_unit'), $label);
		
		return $label;
		
	}
	
	protected function _loadDefaultBasePriceValues($_product)
	{
		foreach (array('base_price_base_amount', 'base_price_unit', 'base_price_base_unit') as $attributeCode)
		{
			if (! $_product->getDataUsingMethod($attributeCode))
			{
				$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
				$_product->setDataUsingMethod($attributeCode, $attribute->getFrontend()->getValue($_product));
			}
		}
		return $this;
	}
	
	/***************************************************
	 *
	 * Helper methods
	 *
	 ***************************************************/

	/**
	 * Testfunction
	 * @return string
	 */
	function ping() {
		return $this->_name.PHP_EOL.$this->getFile('default').PHP_EOL;
	}

	/**
	 * Wrapper for str_replace
	 *
	 * @param string $token
	 * @param string $value
	 * @param string $string
	 * @return string;
	 */
	function replaceToken($token, $value, $string) {
		return str_replace($token, $value, $string);
	}

	/**
	 * Print Error to cli
	 * @param string $message
	 */
	protected function _printError($message) {
		echo 'Warning: '.$message.PHP_EOL;
	}
	
	function amazon_header() {
		$amazon_header = array(array('Amazon Product Ads Header','version=3.0','Die oberen drei Zeilen sind nur zur Verwendung durch Amazon.de vorgesehen. Verändern oder löschen Sie die obersten drei Zeilen nicht.',
				'','','','','','','','','','Angebot - Informationen zum Angebot','','','','','Grundpreise','','','','Artikelerkennungs - Artikelerkennungsinformationen - Diese Attribute haben einen Einfluss darauf, wie Kunden Ihr Produkt mittels Durchsuchen oder Suchfunktionen auf der Website finden.',
				'','','','','','','','','','','','Bild - Bildinformationen - Weitere Informationen finden Sie auf der Registerkarte Bildanweisungen.','','','','','','','','','Variations - Variationsinformationen','','','','','','','','','Abmessungen - Produktabmessungen - Diese Attribute geben die Größe und das Gewicht eines Produktes an.',
				'','','','','Weitere attribute','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',
				'Konformitäts - Konformitätsinformationen - Attribute, die zur Übereinstimmung mit Verbrauchergesetzen in dem Land oder in der Region erforderlich sind, wo der Artikel verkauft wird.','','','','','',''),
				array('Produkttyp','URL zur Produktdetailseite','Lagerhaltungsnummer','Hersteller-Barcode','Barcode-Typ','Produktname','Marke','Hersteller','Produktbeschreibung','Artikelnummer','Modellnummer','Update / Löschen','Preis','Versandkosten','Anzahl','Name der Abteilung','Packungseinheit','Verkaufsgewicht','Verkaufsvolumen','Länge',
						'Anzahl der Stücke pro Packung oder Quadratmeter für die Fläche','Kategorie','Produktkategorisierung (Suchpfad)','Attribut1','Attribut2','Attribut3','Attribut4','Attribut5','Allgemeine Schlüsselwörter1','Allgemeine Schlüsselwörter2','Allgemeine Schlüsselwörter3','Allgemeine Schlüsselwörter4','Allgemeine Schlüsselwörter5',
						'URL Hauptbild','URL Weiteres Produktbild1','URL Weiteres Produktbild2','URL Weiteres Produktbild3','URL Weiteres Produktbild4','URL Weiteres Produktbild5','URL Weiteres Produktbild6','URL Weiteres Produktbild7','URL Weiteres Produktbild8','Variantenbestandteil','SKU des übergeordneten Produkts','Produktbeziehungs-Typ',
						'Varianten-Design','Farbe','Farbfamilie','Größe','Größenzuordnung','Ring size','Artikelhöhe','Artikellänge','Artikelbreite','Artikelgewicht','Versandgewicht','Bekleidungsart','Ist Produkt für Erwachsene','Hauptmaterial','Kollektion (Saison+Jahr)','Stil/Form','Materialzusammensetzung','Jahreszeit','Sporttyp','Produkteignung1',
						'Produkteignung2','Produkteignung3','Produkteignung4','Produkteignung5','Duftname','Spezifische Verwendung Schlüsselwörter1','Spezifische Verwendung Schlüsselwörter2','Spezifische Verwendung Schlüsselwörter3','Spezifische Verwendung Schlüsselwörter4','Spezifische Verwendung Schlüsselwörter5','Besondere Merkmale1','Besondere Merkmale2',
						'Besondere Merkmale3','Besondere Merkmale4','Besondere Merkmale5','Zutaten1','Zutaten2','Zutaten3','Modelljahr','Zielgruppe1','Zielgruppe2','Zielgruppe3','Anzeige','Wasserdichte Tiefe','Uhrwerk-Typ','Liga und Teamname','Volumen oder Fassungsvermögen','Größe pro Perle','Gesamtgewicht Diamant','Bandmaterialtyp','Metalltyp','Ringgröße',
						'Computer CPU speed','Größe Festplatte1','Größe Festplatte2','RAM Größe','Flachspeicher-Größe','Betriebssystem1','Maßeinheit Bildschirmdiagonale','Maximale Auflösung','Display-Technologie','Effektive Standbildauflösung','Optischer Zoom','Externer Speicher','Geschmack','Spezialität1','Anlass','Küche','Hersteller vorgeschlagenes Alter Maximum',
						'Hersteller vorgeschlagenes Alter Minimum','Zielgruppe','Farbe und Lackierung','Material','Herkunftsland','Altersspezifischer Warnhinweis der  EU-Spielzeugrichtlinie','Alterunabhängiger Warnhinweis EU-Spielzeugrichtlinie','EU-Spielzeugrichtlinie Sprache Warnhinweis','Warn- und Haftungsausschluss-Hinweise','Sicherheitswarnung','FEDAS ID'));
		return $amazon_header;
	}
	
	function getVariantUrl($_product){
		$variant_url = "";
		$ps_bettwaesche = $_product->getData("ps_bettwaesche");
		$ps_color= $_product->getData("ps_color");
		$ps_completion= $_product->getData("ps_completion");
		$ps_cover= $_product->getData("ps_cover");
		$ps_extras= $_product->getData("ps_extras");
		$ps_feet= $_product->getData("ps_feet");
		$ps_firmness= $_product->getData("ps_firmness");
		$ps_hardness_level= $_product->getData("ps_hardness_level");
		$ps_head= $_product->getData("ps_head");
		$ps_material_color= $_product->getData("ps_material_color");
		$ps_matress_one= $_product->getData("ps_matress_one");
		$ps_matress_two= $_product->getData("ps_matress_two");
		$ps_remote_control= $_product->getData("ps_remote_control");
		$ps_size= $_product->getData("ps_size");
		$ps_topper= $_product->getData("ps_topper");			
			
		if(!empty($ps_bettwaesche)) {
			if (empty($variant_url)) {
				$variant_url .= "/#207=".$ps_bettwaesche;
			} else {
				$variant_url .= "&207=".$ps_bettwaesche;
			}
		}
		if(!empty($ps_color)) {
			if (empty($variant_url)) {
				$variant_url .= "/#183=".$ps_color;
			} else {
				$variant_url .= "&183=".$ps_color;
			}
		}
		if(!empty($ps_completion)) {
			if (empty($variant_url)) {
				$variant_url .= "/#269=".$ps_completion;
			} else {
				$variant_url .= "&269=".$ps_completion;
			}
		}
		if(!empty($ps_cover)) {
			if (empty($variant_url)) {
				$variant_url .= "/#202=".$ps_cover;
			} else {
				$variant_url .= "&202=".$ps_cover;
			}
		}
		if(!empty($ps_extras)) {
			if (empty($variant_url)) {
				$variant_url .= "/#219=".$ps_extras;
			} else {
				$variant_url .= "&219=".$ps_extras;
			}
		}
		if(!empty($ps_feet)) {
			if (empty($variant_url)) {
				$variant_url .= "/#270=".$ps_feet;
			} else {
				$variant_url .= "&270=".$ps_feet;
			}
		}
		if(!empty($ps_firmness)) {
			if (empty($variant_url)) {
				$variant_url .= "/#283=".$ps_firmness;
			} else {
				$variant_url .= "&283=".$ps_firmness;
			}
		}
		if(!empty($ps_hardness_level)) {
			if (empty($variant_url)) {
				$variant_url .= "/#137=".$ps_hardness_level;
			} else {
				$variant_url .= "&137=".$ps_hardness_level;
			}
		}
		if(!empty($ps_head)) {
			if (empty($variant_url)) {
				$variant_url .= "/#230=".$ps_head;
			} else {
				$variant_url .= "&230=".$ps_head;
			}
		}
		if(!empty($ps_material_color)) {
			if (empty($variant_url)) {
				$variant_url .= "/#220=".$ps_material_color;
			} else {
				$variant_url .= "&220=".$ps_material_color;
			}
		}
		if(!empty($ps_matress_one)) {
			if (empty($variant_url)) {
				$variant_url .= "/#234=".$ps_matress_one;
			} else {
				$variant_url .= "&234=".$ps_matress_one;
			}
		}
		if(!empty($ps_matress_two)) {
			if (empty($variant_url)) {
				$variant_url .= "/#235=".$ps_matress_two;
			} else {
				$variant_url .= "&235=".$ps_matress_two;
			}
		}
		if(!empty($ps_remote_control)) {
			if (empty($variant_url)) {
				$variant_url .= "/#203=".$ps_remote_control;
			} else {
				$variant_url .= "&203=".$ps_remote_control;
			}
		}
		if(!empty($ps_size)) {
			if (empty($variant_url)) {
				$variant_url .= "/#182=".$ps_size;
			} else {
				$variant_url .= "&182=".$ps_size;
			}
		}
		if(!empty($ps_topper)) {
			if (empty($variant_url)) {
				$variant_url .= "/#239=".$ps_topper;
			} else {
				$variant_url .= "&239=".$ps_topper;
			}
		}
		return $variant_url;	
		
	}
	
}