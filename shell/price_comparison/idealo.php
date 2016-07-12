<?php
require_once 'price_comparison/abstract.php';
		
class Mage_Shell_PriceComparison_Idealo extends Mage_Shell_PriceComparison_Abstract {
	public $name = 'Idealo Exporter';
	protected $_code = 'idealo';
	protected $_csvDelimiter;
	protected $_csvEnclosure = '"';
		
	public function __construct(Mage_Shell_PriceComparison $shell) {
		$this->_csvDelimiter = chr(9);
		parent::__construct($shell);
	}
	
	function getFields() {
		$return_array =  array(
				'Artikelnummer' => array('code' => 'sku'),
				'EAN' => array('code' => 'ps_ean'),
				'Herstellerartikelnummer' => array('code' => 'sku'),
				'Herstellername' => array('function' => 'getManufacturer', 'fields' => array('ps_brand','manufacturer')),
				'Produktname' => array('code' => 'name'),
				'Produktbeschreibung' => array('function' => 'getDescription', 'fields' => array('description')),
				'Preis' => array('function' => 'calcPrice'),
				'Lieferzeit' => array('code' => 'delivery_time'),
				'Produktgruppe' => array('function' => 'getCategory'),
				'Vorkasse' => array('function' => 'getPrepaymentShipping'),
				'PayPal' => array('function' => 'getPayPalShipping'),
				'Bankeinzug' => array('function' => 'getDebitShipping'),
				'Rechnung' => array('function' => 'getInvoiceShipping'),
				'Sofortüberweisung' => array('function' => 'getSofortShipping'),
				'Kreditkarte' => array('function' => 'getCreditShipping'),
				'ProduktURL' => array('function' => 'getUrl_for_idealo'),
				'BildURL_1' => array('function' => 'getImageUrl'),
				'Gutscheincode' => array('function' => 'getCouponCodeWithDes'),
				'Gutscheinpreis' => array('function' => 'getCouponPrice'),
		);
		if ($this->_shell->currentStoreCode == "ps_de") {
			$return_array['Direktkauf'] = array('static' => 'ja');
			$return_array['Verfügbarkeit'] = array('static' => '10');
			$return_array['Versandart'] = array('function' => 'getDispatchMode');
		}
		return $return_array;
	}
	
	function calcPrice($_product) {
		return number_format(parent::calcPrice($_product), 2, null, '');
	}

	function getUrl_for_idealo($_product) {
		try {
			$url = '';
			$coupon_code = $this->getCouponCode($_product);
			if (!empty($coupon_code)) {
				$pid = $_product->getId();
				//http://www.perfekt-schlafen.de/checkout/cart/add/product/54058?coupon_code=PS13NL01
				$url = "http://www.perfekt-schlafen.de/checkout/cart/add/product/";
				$url .= $pid."?coupon_code=".$coupon_code."&utm_source=idealo&utm_medium=cpc&utm_content=pv&utm_campaign=idealo-pv&_\$ja=tsid:68163";
				return $url;
			}
			
			//dc storm
			if ($this->_shell->currentStoreCode == "ps_de") {
				$tsid = 68163;
			} elseif ($this->_shell->currentStoreCode == "md_de") {
				$tsid = 73051;
			} else {
				$tsid = "";
			}
			
			if($_product->getVisibility() == 1 && $_product->getTypeId() != "configurable") {
				$parentId = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
				if(isset($parentId[0])){
					$parentProduct = Mage::getModel('catalog/product')->load($parentId[0]);
					$url = $parentProduct->getUrlPath();
					$url .= "/?utm_source=idealo&utm_medium=cpc&utm_content=pv&utm_campaign=idealo-pv&_\$ja=tsid:".$tsid;
					//function in abstract
					$variant_url = $this->getVariantUrl($_product);
					$url .= $variant_url;
				}
			} else {
				$url = $_product->getUrlPath();
				$url .= "/?utm_source=idealo&utm_medium=cpc&utm_content=pv&utm_campaign=idealo-pv&_\$ja=tsid:".$tsid;
				if (!empty($coupon_code)) {
					$url .= "&coupon_code=".$coupon_code;
				}
			}
			$url = Mage::getUrl($url);
			$url = rtrim($url, "/");
			return $url;
		}catch (Exception $e) {
			return '';
		}
	}
	

	/*
	 * Spedition und DHL. Mit Spedition werden alle Möbel, alle Sets, alle Betten, alle Lattenroste und alle Matratzen bewertet,
	* der Wert DHL gilt für alle Bettwaren und Bettwäschen.
	*
	
	*/
	function getDispatchMode($_product) {
		$check_spedition = false;
		$spedition_cat_array = array(	"Betten_ps" => "135",
				"Lattenroste_ps" => "4",
				"Matratzen_ps" => "3",
				"Moebel_ps" => "249",
				"Sets_ps" => "145",
	
				"Lattenroste_md" => "174",
				"Matratzen_md" => "173",
				"Sets_md" => "177",
				"Restposten_Matratzen_md" => "212",
				"Restposten_Lattenroste_md" => "260");
		$productCatIds = $_product->getCategoryIds();
		foreach ($productCatIds as $cid) {
			if(in_array($cid, $spedition_cat_array)) {
				$check_spedition = true;
			}
		}
		if ($check_spedition) {
			return "Spedition";
		} else {
			return "DHL";
		}
	
	}
	
	/*coupon for idealo
	 * 
	 */
	function getCouponCode($_product) {
		$code = "";
		if(($_product->getTypeId() != "configurable") && ($this->_shell->currentStoreCode == "ps_de")) {
			if(stristr($_product->getName(), 'bassetti') !== FALSE) {
				if( (parent::calcPrice($_product)) > 100) {
					$code = "IDEALO5";
				}
			}
			/*else{
				$psku= $_product->getSKU();
				$idealo_13 = $this->idealo_13_percent_discount_article();
				$idealo_16 = $this->idealo_16_percent_discount_article();
				if(in_array($psku, $idealo_13)) {
					$code = "IDEALO13";
				}elseif (in_array($psku, $idealo_16)) {
					$code = "IDEALO16";
				}				
			}
			*/
		}
		return $code;
	}
	
	function getCouponCodeWithDes($_product) {
		$code_description = "";
		if($this->getCouponCode($_product) == "IDEALO5") {
			$code_description = "IDEALO5 (5 € Rabatt / gilt nur bei Einstieg über Idealo)";
		}
		/*elseif($this->getCouponCode($_product) == "IDEALO10") {
			$code_description = "IDEALO10 (10% Rabatt auf ausgewählte Produkte / Preis gilt nur bei direktem Einstieg über Idealo)";
		}elseif($this->getCouponCode($_product) == "IDEALO13"){
			$code_description = "IDEALO13 (13% Rabatt auf ausgewählte Produkte / Preis gilt nur bei direktem Einstieg über Idealo)";
		}elseif($this->getCouponCode($_product) == "IDEALO16"){
			$code_description = "IDEALO16 (16% Rabatt auf ausgewählte Produkte / Preis gilt nur bei direktem Einstieg über Idealo)";
		}*/
				
		return $code_description;
	}
	
	function getCouponPrice($_product) {
		$coupon_price = "";
		if($this->getCouponCode($_product) == "IDEALO5") {
			$price = parent::calcPrice($_product) - 5;
			$coupon_price = number_format($price, 2, '.', '');
		}
		/*elseif($this->getCouponCode($_product) == "IDEALO10") {
			$price = parent::calcPrice($_product) * 0.9;
			$coupon_price = number_format($price, 2, '.', '');
		}elseif($this->getCouponCode($_product) == "IDEALO13") {
			$price = parent::calcPrice($_product) * 0.87;
			$coupon_price = number_format($price, 2, '.', '');
		}elseif($this->getCouponCode($_product) == "IDEALO16") {
			$price = parent::calcPrice($_product) * 0.84;
			$coupon_price = number_format($price, 2, '.', '');
		}
		*/
		return $coupon_price;		
	}
	
	function idealo_16_percent_discount_article() {
		$article_array = array("22454","22455","22456","22457","22458","22459","22460","22461","22462","22463",
						"22464","22465","22466","22467","22468","22469","22470","22471","22472","22473",
						"22474","22475","22476","22477","22478","22479","22480","22481","22482","22483",
						"22484","22485","22486","22487","22488","22489","22490","22491","22492","22493",
						"22494","22495","22496","22497","22498","22499","22500","22501","22502","22503",
						"22504","22505","22506","22507","35526","35527","35528","35529","35530","35531",
						"35532","35533","35534","35535","35536","35537","35538","35539","35540","35541",
						"35542","35543","35544","35545","35546","35547","35548","35549","35550","35551",
						"35552","35553","35554","35555","35556","35557","35558","35559","35560","35561",
						"35562","35563","35564","35565","35566","35567");
		return $article_array;
	}
	
	function idealo_13_percent_discount_article() {
		$article_array = array("22366","22368","22370","22372","22374","22379","22382","22384","22386","22388",
						"22390","22392","22397","22400","22402","22406","22410","22415","22418",
						"20671","20672","20673","20674","20675","20676","20677","20678","20679","20680",
						"20681","20682","20683","20684","20685","20686","20687","20688","20689","20690",
						"20692","20693","20694","20695","20696","20697","20698","20699","20700","20701",
						"20702","20703","20704","20706","20707","20708","20709","20710","20711","20712",
						"20713","20714","20715","20716","20717","20718","20720","20721","20722","20723",
						"20724","20725","20726","20727","20728","20729","20730","20731","20732","20734",
						"20735","20736","20737","20738","20739","20740","20741","20742","20743","20744",
						"20745","20758","20761","20762","20763","20764","20765","20766","20767","20768",
						"20769","20770","20771","20772","20775","20776","20777","20778","20779","20780",
						"20781","20782","20783","20784","20785","20786","20787","20789","20790","20791",
						"20792","20793","20794","20795","20796","20797","20798","20799","20800","20801",
						"20803","20804","20805","20806","20807","20808","20809","20810","20811","20812",
						"20813","20814","20815","20817","20819","20821","20823","20825","20827","20831",
						"20833","20835","20837","20839","20844","20845","20846","20847","20848","20849",
						"20850","20851","20852","20853","20854","20855","20880","20881","20882","20885",
						"20886","22421","22422","22423","22424","22425","22426","22427","22428","22429",
						"22430","22431","22432","22433","22434","22435","22436","22437","22438","22439",
						"22440","22441","22442","22443","22444","22445","22446","22447","22448","22449",
						"22450","22451","22452","28246","28247","28248","28249","28250","28251","28252",
						"28253","28254","28255","28256","28257","28258","28259","28260","28261","28262",
						"28263","28264","28265","28266","28267","28268","28269","28270","28271","28272",
						"28273","28274","28275","28276","28277","28278","28279","28280","28281","28282",
						"28283","28284","28285","28286","28287","28288","28289","28290","28291","28292",
						"28293","28294","28295","28296","28297","28298","28299","28300","28301","28302",
						"28303","28304","28305","28306","28307","28308","28309","28310","28311","28312",
						"28313","28314","28315","28316","28317","28318","28319","28320","28321","28322",
						"28323","28324","28325","28326","28327","28328","28329","28330","28331","28332",
						"28333","28334","28335","28336","28337","28338","28339","28340","28341","28342",
						"28343","28344","28345","28346","28347","28348","28349","28350","28351","28352",
						"28353","28354","28355","28356","28357","28358","28359","28360","28361","28362",
						"28363","28364","28365","28366","28367","28368","28369","28370","28371","28372",
						"28373","28374","28375","28376","28377","28378","28379","28380","28381","28382",
						"28383","28384","28385","28386","28387","28388","28389","28390","28391","28392",
						"28393","28394","28395","28396","28397","28398","28399","28400","28401","28415",
						"28416","28417","28418","28419","28420","28421","28422","28423","28424","28425",
						"28426","28427","28428","28429","28430","28431","28432","28434","28435","28436",
						"28437","28438","28440","28441","28442","28443","28444","28445","28446","28447",
						"28448","28449","28450","28451","28452","28453","28454","28455","28456","28457",
						"28516","28517","28518","28519","28520","28521","28522","28523","28524","28525",
						"28526","28527","28528","28529","28530","28531","28532","28533","28534","28535",
						"28536","28537","28538","28539","28540","28541","28542","28543","28544","28545",
						"28546","28547","28548","28549","28550","28551","28552","28553","28554","28555",
						"28556","28557","28558","28559","28560","28561","28562","28563","28564","28565",
						"28566","28567","28568","28569","28570","28571","28572","28573","28574","28575",
						"28576","28577","28578","28579","28580","28581","28582","28583","28584","28585",
						"28586","28587","28588","28589","28590","28591","28592","28593","28594","28595",
						"28596","28597","28598","28599","28600","28601","28602","28603","28604","28605",
						"28606","28607","28608","28609","28610","28611","28612","28613","28614","28615",
						"28616","28617","28618","28619","28621","28622","28623","28624","28625","28626",
						"28627","28628","28629","28630","28631","28632","28633","28634","28635","28636",
						"28637","28638","28639","28640","28641","28642","28643","28644","28645","28646",
						"28647","28648","28649","28650","28652","28653","28654","28655","28656","28657",
						"28658","28659","28660","28661","28662","28663","28664","28665","28666","28667",
						"28668","28669","28670","28671","28672","28673","28674","28675","28676","28677",
						"28678","28679","28680","28681","28682","28683","28684","28685","28686","28687",
						"28688","28689","28690","28691","28692","28693","28694","28695","28696","28697",
						"28698","28699","28700","28701","28702","28704","28705","28706","28707","28708",
						"28709","28710","28711","28712","28713","28714","28715","30074","30075","30076",
						"30077","30078","30079","30080","30081","30082","30083","30084","30085","30086",
						"30087","30088","30089","30090","30091","32497","32498","32499","32500","32501",
						"32502","32503","32504","32505","32506","32507","32508","32509","32510","32511",
						"32512","32513","32514","32515","32516","32517","32518","32519","32520","32521",
						"32522","32523","32524","32525","32526","32527","32528","32529","32530","32531",
						"32532","32533","32534","32535","32536","32537","32538","32539","32540","32541",
						"32542","32543","32544","32545","32546","32547","32548","32550","32551","32552",
						"32553","32554","32555","32556","32557","32558","32559","32560","32561","32562",
						"32563","32564","32565","32566","32567","32568","32569","32570","32571","32572",
						"32573","32574","32575","32576","32577","32578","32579","32580","32581","32582",
						"32583","32584","32585","32586","32587","32588","32589","32590","32591","32592",
						"32593","32594","32595","32596","32597","32598","32599","32600","32601","32602",
						"32603","32880","32881","32882","32883","32884","32885","32886","32887","32888",
						"32889","32890","32891","32892","32893","32894","32895","32896","32897","32898",
						"32899","32900","32901","32902","32903","32904","32905","32906","32907","32908",
						"32909","32910","32911","32912","32913","32914","32915","32916","32917","32918",
						"32919","32920","32921","32922","32923","32924","32925","32926","32927","36375",
						"36376","36377","36378","36379","36380","36381","36382","36383","36384","36385",
						"36386","36387","36388","36389","36390","36391","36392","43960","43961","43962",
						"43963","43964","43965","43966","43967","43968","43969","43970","43971","43973",
						"43974","43975","43976","43977","43978","43979","43980","43981","43982","43983",
						"43984","43986","43987","43988","43989","43990","43991","43992","43993","43994",
						"43995","43996","43997","43998","43999","44000","46003","46004","46005","56325",
						"62829","62830","62831","62832","62833","62834","62835","62838","62839","62840",
						"62841","62843","62844","62845","62847","62848","62850","62851","62852","21442",
						"21443","21444","21445","21446","21447","21448","21449","21450","21451","21452",
						"21453","21454","21455","21456","21457","21458","21459","21460","21461","21462",
						"21463","21464","21465","21466","21467","21468","21469","21470","21471","21472",
						"21473","21474","21475","21476","21477","34401","34402","34403","34404","34405",
						"34406","34407","34408","34409","34410","34411","34412","34413","34414","34415",
						"34416","34417","34418","34419","34420","34421","34422","34423","34424","34425",
						"34426","34427","34428");
		return $article_array;
	}
	
	
} 