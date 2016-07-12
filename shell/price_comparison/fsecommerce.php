<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Fsecommerce extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'FS Ecommerce Exporter';
	protected $_code = 'fsecommerce';
	protected $_csvDelimiter = ';';
	protected $_csvEnclosure = '';
	
	const IN_STOCK = 'in stock';
	const OUT_OF_STOCK = 'out of stock';
	
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = ';';
		parent::__construct($shell);
	}
	
	function getFields() {
		return array(
				//'id' => array('function' => 'getMagentoPID'),
				//'parent_id' => array('function' => 'getParentMagentoPID'),
				'id' 												=> array('code' => 'sku'),
				'parent_id' 										=> array('function' => 'getParentSku'),
				'variant_attribute' 								=> array('function' => 'getVariantsFilterLabel'),
				'variant_attribute_value'	 						=> array('function' => 'getVariantsFilterValue'),
				'shipping_status' 									=> array('static' => ''),
				'shipping' 											=> array('static' => 'DHL/Spedition'),
				'title' 											=> array('code' => 'name'),
				'description' 										=> array('function' => 'getDescription'),
				'brand' 											=> array('function' => 'getManufacturer'),
				'gtin' 												=> array('function' => 'getEan'),
				'color'												=> array('function' => 'getColor'),
				'image_link'										=> array('function' => 'get_ImgUrl'),				
				'additional_image_link_1' 							=> array('function' => 'get_ImgUrl_2'),
				'additional_image_link_2' 							=> array('function' => 'get_ImgUrl_3'),
				'additional_image_link_3' 							=> array('function' => 'get_ImgUrl_4'),
				'additional_image_link_4' 							=> array('function' => 'get_ImgUrl_5'),
				'availability' 										=> array('static' => 'available'),
				'material' 											=> array('static' => ''),
				'google_product_category' 							=> array('static' => 'Heim & Garten'),
				'link' 												=> array('function' => 'getUrl'),
				'sale_price' 										=> array('function' => 'calcSalePrice'),
				'sale_price_effective_date' 						=> array('function' => 'get_sale_price_effective_date'),
				'price' 											=> array('function' => 'calcPrice'),
				'weight' 											=> array('static' => ''),
				'custom_attribute_Bezugsmaterial'					=> array('function'	=> 'getPsMaterialCover'),
				'custom_attribute_Höhe'								=> array('function'	=> 'getPsHeight'),
				'custom_attribute_Textilkennzeichnung'				=> array('function'	=> 'getPsTextileLabellingAct'),
				'custom_attribute_Marke'							=> array('function'	=> 'getPsBrand'),
				'custom_attribute_Härtegrad'						=> array('function'	=> 'getPsHardnessLevel'),
				'custom_attribute_Bezugwäsche'						=> array('function'	=> 'getPsCoverWashable'),
				'custom_attribute_waschbar'							=> array('function'	=> 'getPsWashable'),
				'custom_attribute_Füllung'							=> array('function'	=> 'getPsDuvetFilling'),
				'delivery_time'  									=> array('code' => 'delivery_time'),
				
		);
	}

	function getPsMaterialCover($_product){
		if($_product->getPsMaterialCover()){
			return $_product->getPsMaterialCover();
		}
		return '';
	}

	function getPsHeight($_product){
		if($_product->getPsHeight()){
			return $_product->getAttributeText('ps_height');
		}
		return '';
	}

	function getPsTextileLabellingAct($_product){
		return $_product->getPsTextileLabellingAct();
	}

	function getPsBrand($_product){
		if($_product->getPsBrand()){
			return $_product->getAttributeText('ps_brand');
		}
		return '';
	}

	function getPsHardnessLevel($_product){
		if($_product->getPsHardnessLevel()){
			return $_product->getAttributeText('ps_hardness_level');
		}
		return '';
	}

	function getPsCoverWashable($_product){
		if($_product->getPsCoverWashable()){
			return $_product->getAttributeText('ps_cover_washable');
		}
		return '';
	}

	function getPsWashable($_product){
		if($_product->getPsWashable()){
			return $_product->getAttributeText('ps_washable');
		}
		return '';
	}

	function getPsDuvetFilling($_product){
		if($_product->getPsDuvetFilling()){
			return $_product->getAttributeText('ps_duvet_filling');
		}
		return '';
	}

	
	/**
	 * Return image for product
	 *
	 * @param unknown $_product
	 * @return string
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
		if($_product->getMsrp() && $_product->getMsrp() > 1) {
			return number_format($_product->getMsrp(), 4, '.', '');
		} else {
			return number_format(parent::calcPrice($_product), 4, '.', '');
		}
	}
	
	function calcSalePrice($_product) {
		$sale_price = "";		
		if($_product->getMsrp() && $_product->getMsrp() > 1) {			
			$sale_price = number_format(parent::calcPrice($_product), 4, '.', '');
		}
		if ($sale_price >= $this->calcPrice($_product)) {
			$sale_price = "";
		}
		return $sale_price;
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
	
	function get_all_ImageUrl($_product) {
		/*
		 * $gallery = $product->getMediaGallery();
		 */
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
		//isset($gallery['images'][0]) ? $mediaUrl . 'catalog/product' . $gallery['images'][0]['file'] : '',
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
	
	function get_ImgUrl_5($_product) {
		$all_img = $this->get_all_ImageUrl($_product);
		if(isset($all_img[4]) && !empty($all_img[4])) {
			return $all_img[4];
		} else {
			return "";
		}
	}
		
	function getParentMagentoPID($_product) {
		$parentid = "";
		if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
			$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
			if(isset($parentId[0])){
				$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
				$parentid = $parentProduct->getId();
			}
		}
		return $parentid;
	}
	
	function getParentSku($_product) {
		$parent_sku = "";
		if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
			$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
			if(isset($parentId[0])){
				$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
				$parent_sku = $parentProduct->getSku();
			}
		}
		return $parent_sku;
	}
	

	function getGoogleShipping($_product){
	
		$attrCode	= Mage::helper('fsecommerce_convertizer')->getGoogleShipping();
		if($attrCode && $attrCode != "empty"){
			$result = $_product->getResource()->getAttribute($attrCode)
			->getFrontend()->getValue($_product);
		}else{
			return false;
		}
		if($result){
			return $result;
		}else{
			return false;
		}
	
	}
	
	function getGeneralShipping($_product){
	
		$attrCode	= Mage::helper('fsecommerce_convertizer')->getGeneralShipping();
		if($attrCode && $attrCode != "empty"){
			$result = $_product->getResource()->getAttribute($attrCode)
			->getFrontend()->getValue($_product);
		}else{
			return false;
		}
		if($result){
			return $result;
		}else{
			return false;
		}
	}
	
	function getParentProduct($_product){
		$parrentArray = array();
		if($_product->getTypeId() == "simple"){
			$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($_product->getId());
			if(!$parentIds){
				$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(!$parentIds){
					return false;
				}else{
					foreach ($parentIds as $parentID){
						$parent 	= Mage::getModel('catalog/product')->load($parentID);
						array_push($parrentArray,$parent);
						return $parrentArray;
					}
				}
			}
		}
	}
	
	function get_sale_price_effective_date($_product, $from=null,$to=null)
	{
		if($this->calcSalePrice($_product) > 1) {
			if($from) {
				$ts_from = strtotime($from);
			} else {
				$ts_from = strtotime("now");
			}
			$ts_to   = strtotime($to);
			$s_from  = date('Y-m-d', $ts_from) . 'T' . date('H:i', $ts_from) . '-0000';
			$s_to    = $ts_to != 0 ? date('Y-m-d', $ts_to) . 'T00:00-0000' : '2020-01-01T00:00-0000';
	
			return $s_from . '/' . $s_to;
		} else {
			return "";
		}
	}

	function getAttributOptionLabel($attri_code, $optionid) {
		$option_label = "";
		$product_attri = Mage::getModel('catalog/product')->setData($attri_code,$optionid);
		$option_label = $product_attri->getAttributeText($attri_code);
		return $option_label;		
	}
	
	function getVariantFilterAttribute($_product) {
		//http://www.perfekt-schlafen.de/fey-boxspringbett-coventry-160x200-200x220/#183=6085&234=6087&235=6088&182=3732&239=6101
/*
 * 
207	ps_bettwaesche	Bettwäsche
183	ps_color	Farbe
269	ps_completion	Ausführung
202	ps_cover	Bezug
219	ps_extras	Zusätze
270	ps_feet	Füße
283	ps_firmness	Festigkeit
137	ps_hardness_level	Härtegrad
230	ps_head	Kopfteil
220	ps_material_color	Material / Farbe
234	ps_matress_one	Matratze 1
235	ps_matress_two	Matratze 2
203	ps_remote_control	Fernbedienung
182	ps_size	Größe
239	ps_topper	Topper
 */
		$pid = $_product->getId();
		$sku = $_product->getSku();
		$_resource = $_product->getResource();
		
		$ps_bettwaesche = $_resource->getAttributeRawValue($pid,"ps_bettwaesche");
		$ps_color		= $_resource->getAttributeRawValue($pid,'ps_color');
		$ps_completion	= $_resource->getAttributeRawValue($pid,"ps_completion");
		$ps_cover		= $_resource->getAttributeRawValue($pid,"ps_cover");
		$ps_extras		= $_resource->getAttributeRawValue($pid,"ps_extras");
		$ps_feet		= $_resource->getAttributeRawValue($pid,"ps_feet");
		$ps_firmness	= $_resource->getAttributeRawValue($pid,"ps_firmness");
		$ps_hardness_level= $_resource->getAttributeRawValue($pid,"ps_hardness_level");
		$ps_head		= $_resource->getAttributeRawValue($pid,"ps_head");
		$ps_material_color= $_resource->getAttributeRawValue($pid,"ps_material_color");
		$ps_matress_one	= $_resource->getAttributeRawValue($pid,"ps_matress_one");
		$ps_matress_two	= $_resource->getAttributeRawValue($pid,"ps_matress_two");
		$ps_remote_control= $_resource->getAttributeRawValue($pid,"ps_remote_control");
		$ps_size		= $_resource->getAttributeRawValue($pid,"ps_size");
		$ps_topper		= $_resource->getAttributeRawValue($pid,"ps_topper");
		
		$filter_attribute = array();
		if($ps_bettwaesche) {
			$ps_bettwaesche_value = $this->getAttributOptionLabel("ps_bettwaesche", $ps_bettwaesche);
			if($ps_bettwaesche_value) {
				$filter_attribute['ps_bettwaesche']['label'] = "Bettwäsche";
				$filter_attribute['ps_bettwaesche']['option'] = $ps_bettwaesche_value;
			}
		}
		if($ps_color) {
			$ps_color_value = $this->getAttributOptionLabel('ps_color', $ps_color);
			if($ps_color_value) {
				$filter_attribute['ps_color']['label'] = "Farbe";
				$filter_attribute['ps_color']['option'] = $ps_color_value;
			}
		}
		if($ps_completion) {
			$ps_completion_value = $this->getAttributOptionLabel("ps_completion", $ps_completion);
			if($ps_completion_value) {
				$filter_attribute['ps_completion']['label'] = "Ausführung";
				$filter_attribute['ps_completion']['option'] = $ps_completion_value;
			}
		}
		if($ps_cover) {
			$ps_cover_value = $this->getAttributOptionLabel("ps_cover", $ps_cover);
			if($ps_cover_value) {
				$filter_attribute['ps_cover']['label'] = "Bezug";
				$filter_attribute['ps_cover']['option'] = $ps_cover_value;
			}
		}
		if($ps_extras) {
			$ps_extras_value = $this->getAttributOptionLabel("ps_extras", $ps_extras);
			if($ps_extras_value) {
				$filter_attribute['ps_extras']['label'] = "Zusätze";
				$filter_attribute['ps_extras']['option'] = $ps_extras_value;
			}
		}
		if($ps_feet) {
			$ps_feet_value = $this->getAttributOptionLabel("ps_feet", $ps_feet);
			if($ps_feet_value) {
				$filter_attribute['ps_feet']['label'] = "Füße";
				$filter_attribute['ps_feet']['option'] = $ps_feet_value;
			}
		}
		if($ps_firmness) {
			$ps_firmness_value = $this->getAttributOptionLabel("ps_firmness", $ps_firmness);
			if($ps_firmness_value) {
				$filter_attribute['ps_firmness']['label'] = "Festigkeit";
				$filter_attribute['ps_firmness']['option'] = $ps_firmness_value;
			}
		}
		if($ps_hardness_level) {
			$ps_hardness_level_value = $this->getAttributOptionLabel("ps_hardness_level", $ps_hardness_level);
			if($ps_hardness_level_value) {
				$filter_attribute['ps_hardness_level']['label'] = "Härtegrad";
				$filter_attribute['ps_hardness_level']['option'] = $ps_hardness_level_value;
			}
		}
		if($ps_head) {
			$ps_head_value = $this->getAttributOptionLabel("ps_head", $ps_head);
			if($ps_head_value) {
				$filter_attribute['ps_head']['label'] = "Kopfteil";
				$filter_attribute['ps_head']['option'] = $ps_head_value;
			}
		}
		if($ps_material_color) {
			$ps_material_color_value = $this->getAttributOptionLabel("ps_material_color", $ps_material_color);
			if($ps_material_color_value) {
				$filter_attribute['ps_material_color']['label'] = "Material / Farbe";
				$filter_attribute['ps_material_color']['option'] = $ps_material_color_value;
			}
		}
		if($ps_matress_one) {
			$ps_matress_one_value = $this->getAttributOptionLabel("ps_matress_one", $ps_matress_one);
			if($ps_matress_one_value) {
				$filter_attribute['ps_matress_one']['label'] = "Matratze 1";
				$filter_attribute['ps_matress_one']['option'] = $ps_matress_one_value;
			}
		}
		if($ps_matress_two) {
			$ps_matress_two_value = $this->getAttributOptionLabel("ps_matress_two", $ps_matress_two);
			if($ps_matress_two_value) {
				$filter_attribute['ps_matress_two']['label'] = "Matratze 2";
				$filter_attribute['ps_matress_two']['option'] = $ps_matress_two_value;
			}
		}
		if($ps_remote_control) {
			$ps_remote_control_value = $this->getAttributOptionLabel("ps_remote_control", $ps_remote_control);
			if($ps_remote_control_value) {
				$filter_attribute['ps_remote_control']['label'] = "Fernbedienung";
				$filter_attribute['ps_remote_control']['option'] = $ps_remote_control_value;
			}
		}
		if($ps_size) {
			$ps_size_value = $this->getAttributOptionLabel("ps_size", $ps_size);
			if($ps_size_value) {
				$filter_attribute['ps_size']['label'] = "Größe";
				$filter_attribute['ps_size']['option'] = $ps_size_value;
			}
		}
		if($ps_topper) {
			$ps_topper_value = $this->getAttributOptionLabel("ps_topper", $ps_topper);
			if($ps_topper_value) {
				$filter_attribute['ps_topper']['label'] = "Topper";
				$filter_attribute['ps_topper']['option'] = $ps_topper_value;
			}
		}
		return $filter_attribute;
	}
	
	function getVariantsFilterLabel($_product){
		$lable = "";
		$parent_sku = $this->getParentSku($_product);
		if(!empty($parent_sku)) {
			$filter_array = $this->getVariantFilterAttribute($_product);
			foreach ($filter_array as $filter) {
				if(empty($lable)){
					$lable .= $filter['label'];
				} else {
					$lable .= "|".$filter['label'];
				}
			}
		}
		return $lable;
	}
	
	function getVariantsFilterValue($_product){
		$attri_text = "";
		$parent_sku = $this->getParentSku($_product);
		if(!empty($parent_sku)) {
			$filter_array = $this->getVariantFilterAttribute($_product);
			foreach ($filter_array as $filter) {
				if(empty($attri_text)){
					$attri_text .= $filter['option'];
				} else {
					$attri_text .= "|".$filter['option'];
				}
			}
		}
		return $attri_text;
	}
}