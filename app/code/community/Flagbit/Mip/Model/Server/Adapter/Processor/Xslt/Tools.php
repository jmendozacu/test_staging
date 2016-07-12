<?php
/**
 * This source file is subject to the Magento Integration Platform License
 * that is bundled with this package in the file LICENSE_MIP.txt.
 * It is also available through the world-wide-web at this URL:
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to magento@flagbit.de so we can send you a copy immediately.
 *
 * The Magento Integration Platform is a property of Flagbit GmbH & Co. KG.
 * It is NO part or deravative version of Magento and as such NOT published
 * as Open Source. It is NOT allowed to copy, distribute or change the
 * Magento Integration Platform or any of its parts. If you wish to adapt
 * the software to your individual needs, feel free to contact us at
 * http://www.flagbit.de or via e-mail (magento@flagbit.de) or phone
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * Dieser Quelltext unterliegt der Magento Integration Platform License,
 * welche in der Datei LICENSE_MIP.txt innerhalb des MIP Paket hinterlegt ist.
 * Sie ist außerdem über das World Wide Web abrufbar unter der Adresse:
 * http://www.flagbit.de/license/mip
 * Falls Sie keine Kopie der Lizenz erhalten haben und diese auch nicht über
 * das World Wide Web erhalten können, senden Sie uns bitte eine E-Mail an
 * magento@flagbit.de, so dass wir Ihnen eine Kopie zustellen können.
 *
 * Die Magento Integration Platform ist Eigentum der Flagbit GmbH & Co. KG.
 * Sie ist WEDER Bestandteil NOCH eine derivate Version von Magento und als
 * solche nicht als Open Source Softeware veröffentlicht. Es ist NICHT
 * erlaubt, die Software als Ganze oder in Einzelteilen zu kopieren,
 * verbreiten oder ändern. Wenn Sie eine Anpassung der Software an Ihre
 * individuellen Anforderungen wünschen, kontaktieren Sie uns unter
 * http://www.flagbit.de oder via E-Mail (magento@flagbit.de) oder Telefon
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 * @copyright   2009 by Flagbit GmbH & Co. KG
 * @author      Flagbit Magento Team <magento@flagbit.de>
 */


/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Tools {

    /**
     * Customergroups
     * @var null | DOMDocument
     */
    protected static $_customerGroupsDomXml = null;

    /**
     * Storeviews
     * @var null | DOMDocument
     */
    protected static $_storeviewsDomXml = null;

    /**
     * Mapping Config
     * @var null | DOMDocument
     */
    protected static $_mappingConfig = null;

    /**
     * Counter
     * @var array
     */
    protected static $_counter = array();

    /**
     * files by path
     * @var array
     */
    protected static $_filesByPath = array();


    /**
     * get Tools Model
     *
     * @return Flagbit_Mip_Model_Tools
     */
    protected static function _getToolsModel()
    {
        return Mage::getSingleton('mip/tools');
    }


    /**
     * get Attribute Value by product ID and attribute code
     *
     * @param $productId
     * @param $attributeCode
     * @param $storeId
     * @return mixed
     */
    public static function getProductAttributeValue($productId, $attributeCode, $storeId = Mage_Core_Model_App::ADMIN_STORE_ID)
    {
        /* @var $productObject Mage_Catalog_Model_Product */
        $productObject = Mage::getModel('catalog/product')->setId($productId);
        $productResource = $productObject->getResource();
        return $productResource
                    ->getAttribute($attributeCode)
                    ->getFrontend()
                    ->getValue(
                        $productObject->setData(
                            $attributeCode,
                            $productResource->getAttributeRawValue($productId, $attributeCode, $storeId)
                        )
            );

    }

    /**
     * counter
     *
     * @param string $file
     */
    public static function counter($number = 1, $reset = false)
    {
        if(!isset(self::$_counter[$number]) or $reset == true){
            self::$_counter[$number] = 0;
        }
        self::$_counter[$number] = self::$_counter[$number] + 1;
        return self::$_counter[$number];
    }

    /**
     * get Base dir
     *
     * @return string
     */
    public static function getBaseDir()
    {
        return Mage::getBaseDir();
    }

    /**
     * get Customergroups as DOMDocument which is accessible via XSL:
     *
     * Example Access: <xsl:copy-of select="mip:getcustomergroups()"/>
     *
     * <groups>
     *         <group id="1">Groupname1</group>
     *         <group id="2">Groupname2</group>
     * </group>
     *
     * @return DOMDocument
     */
    public static function getcustomergroups()
    {
           if(self::$_customerGroupsDomXml === null){
               $groups = Mage::getResourceModel('customer/group_collection')->toOptionHash();

            $xml = new DOMDocument("1.0", "UTF-8");
            $elem = $xml->createElement('groups');
            $elem->setAttribute('array', 'true');
            $xml->appendChild( $elem );

            foreach($groups as $id => $name){

                $subelem = $xml->createElement('group');
                $subelem->setAttribute('id', $id);
                $subelem->appendChild($xml->createTextNode( $name ));
                $elem->appendChild( $subelem );

            }
            self::$_customerGroupsDomXml = $xml;
           }
        return self::$_customerGroupsDomXml;
    }

    /**
     * Split a string by string
     * Result from explode(',' 'test1,test2') looks like:
     *
     * <explode>
     *      <item>test1</item>
     *      <item>test2</item>
     * </explode>
     *
     * @param $delimiter
     * @param $value
     * @return DOMDocument
     */
    public static function explode($delimiter, $value)
    {
        if($delimiter == '\n'){
            $delimiter = "\n";
        }elseif($delimiter == '\t'){
            $delimiter = "\t";
        }

        $xml = new DOMDocument("1.0", "UTF-8");
        $elem = $xml->createElement('explode');
        $elem->setAttribute('array', 'true');
        $xml->appendChild( $elem );

        foreach(explode($delimiter, $value) as $item){

            $subelem = $xml->createElement('item');
            $subelem->appendChild($xml->createTextNode( trim($item) ));
            $elem->appendChild($subelem);
        }

        return $xml;
   }

    /**
     * get Storviews as DOMDocument which is accessible via XSL:
     *
     * Example Access: <xsl:copy-of select="mip:getstoreviews()"/>
     *
     * <views>
     *         <view id="1">storecode1</group>
     *         <view id="2">storecode2</group>
     * </views>
     *
     * @return DOMDocument
     */
    public static function getstoreviews()
    {
           if(self::$_storeviewsDomXml === null){
               $storeviews = Mage::app()->getStores();
            $xml = new DOMDocument("1.0", "UTF-8");
            $elem = $xml->createElement('views');
            $elem->setAttribute('array', 'true');
            $xml->appendChild( $elem );

            foreach($storeviews as $storeview){

                $subelem = $xml->createElement('view');
                $subelem->setAttribute('id', $storeview->getStoreId());
                $subelem->appendChild($xml->createTextNode( $storeview->getCode() ));
                $elem->appendChild( $subelem );

            }
            self::$_storeviewsDomXml = $xml;
           }
        return self::$_storeviewsDomXml;
   }

    /**
     * get ID Filter
     * can handle products, attributes and attributesets
     *
     * @param string $type Data Type, for Example: product, attribute, attributeset
     * @param string $field Value Field Name
     * @param string $value string value which sould be converted to ID
     * @return int | null
     */
    public static function getid($type, $field, $value, $default = null)
    {
        return self::_getToolsModel()->getId($type, $field, $value, $default);
    }

    /**
     * get field value by entity id
     *
     * @param string $type Data Type, for Example: product, attribute, attributeset
     * @param string $field Value Field Name
     * @param int $id int entity_id which should be converted to $field value
     * @return string | null
     */
    public static function getFieldValueById($type, $field, $id)
    {
        return self::_getToolsModel()->getFieldValueById($type, $field, $id);
    }

    /**
     * get Settings (MIP Task)
     *
     * @param string|null $key
     * @return Mage_Core_Model_Config_Element | string
     */
    public static function getSettings($key = null)
    {
        $trigger = Mage::getSingleton('mip/server')->getTrigger();
        $settings = Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition();
        return $trigger->handleVariables($settings->getSettings($key));
    }

    /**
     * get mipmanager Config Value
     *
     * @param string $field
     * @param string $group
     * @param string $module
     * @param string $store
     * @param mixed $default
     * @return string
     */
    public static function config($field, $group = null, $module = null, $store = null, $default = 0)
    {
        $value = self::_getToolsModel()->getConfigValue($field, $group, $module, $store);
        if(!$value){
            $value = $default;
        }
        return $value;
    }

    /**
     * get Mapping from Config (mipmanager.xml) as XML Object
     *
     * Format:
     * <mapping>
     *         <item>Magento Attribute Code</item>
     * </mapping>
     *
     * @return DOMDocument
     */
    public static function getMappingConfig($field, $group = null, $module = null, $store = null)
    {

        $key = $field.'/'.$group.'/'.$module.'/'.$store;
        if(!isset(self::$_mappingConfig[$key])){

            $value = self::_getToolsModel()->getConfigValue($field, $group, $module, $store);

            $xml = new DOMDocument("1.0", "UTF-8");
            $elem = $xml->createElement('mapping');
            $elem->setAttribute('array', 'true');
            $xml->appendChild( $elem );

            $children = explode("\n", $value);
            foreach($children as $child){
                $child = explode('=', trim($child));
                if(empty($child[0])){
                    continue;
                }
                $subelem = $xml->createElement('item');
                $subelem->setAttribute('extkey', trim($child[0]));
                $subelem->appendChild($xml->createTextNode( trim($child[1]) ));
                $elem->appendChild( $subelem );
            }

            self::$_mappingConfig[$key] = $xml;
        }
        return self::$_mappingConfig[$key];
    }


    /**
     * get Mapping from Config (mipmanager.xml) as XML Object
     *
     * Format:
     * <mapping>
     *         <item>Magento Attribute Code</item>
     * </mapping>
     *
     * @param $field
     * @param null $group
     * @param null $module
     * @param null $store
     * @return DOMDocument
     */
    public static function getUniversalMappingConfig($field, $group = null, $module = null, $store = null)
    {

        $key = $field.'/'.$group.'/'.$module.'/'.$store;
        if(!isset(self::$_mappingConfig[$key])){

            try{
                $serializer = new Zend_Serializer_Adapter_PhpSerialize();
                $value = self::_getToolsModel()->getConfigValue($field, $group, $module, $store);
                $valueArray = $serializer->unserialize($value);

                $xml = new DOMDocument("1.0", "UTF-8");
                $elem = $xml->createElement('mapping');
                $elem->setAttribute('array', 'true');
                $xml->appendChild( $elem );

                foreach($valueArray as $mapping){
                    $subElem = $xml->createElement('item');

                    foreach($mapping as $field => $value){
                        $itemElem = $xml->createElement($field);
                        $itemElem->appendChild($xml->createTextNode( trim($value) ));
                        $subElem->appendChild( $itemElem );
                    }
                    $elem->appendChild( $subElem );
                }

                self::$_mappingConfig[$key] = $xml;
            }catch(Exception $e){
                Mage::helper('mip/log')->getWriter(new self)->error($e->getMessage(), $e);
                self::$_mappingConfig[$key] = null;
            }
        }
        return self::$_mappingConfig[$key];
    }

    /**
     * get filetime
     *
     * @param string $file
     * @return int
     */
    public static function filetime($file)
    {
        return self::_getToolsModel()->getFiletime($file);
    }

    /**
     * get sha1 from file
     *
     * @param string $file
     * @return string
     */
    public static function sha1_file($file)
    {
        return sha1_file($file);
    }

    /**
     * string to upper case
     *
     * @param string $file
     */
    public static function strtoupper($string)
    {
        return self::_getToolsModel()->strtoupper($string);
    }

    /**
     * normalize Characters
     * Example: ü -> ue
     *
     * @param string $string
     * @return string
     */
    public static function normalize($string)
    {
        return self::_getToolsModel()->normalize($string);
    }

    /**
     * normalize Characters
     * Example: ü -> ue
     *
     * @param string $string
     * @return string
     */
    public static function makeKeyName($string)
    {
        $string = self::_getToolsModel()->normalize($string);
        $string = str_replace(array(' ', '-'), '_', $string);
        return strtolower(preg_replace('([^A-Za-z_]*)', '', $string));
    }

    /**
     * round a float
     *
     * @return float
     */

    public static function round($val, $precision=2)
    {
        return round($val, $precision);
    }

    /**
     * get Attribute Definition Field Value
     *
     * @param $code
     * @param $field
     * @return string
     */
    public static function getAttributeField($code, $field)
    {
        $value = null;
        $attribute = self::_getToolsModel()->getAttribute($code);
        if($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract){
            $value = $attribute->getData($field);
        }
        return $value;
    }

    /**
     * get Attribute Option Values by Text
     *
     * @param $attributeCode
     * @param $text
     * @param null $default
     * @param int $store
     * @param null $compareType
     * @param bool $log
     * @param bool $multi
     * @return Ambiguous|string
     */
    public static function attributeOptionValue($attributeCode, $text, $default = null, $store = 0, $compareType = null, $log = false, $multi = false)
    {
        return self::_getToolsModel()->getAttributeOptionValues($attributeCode, $text, $default, $store, $compareType, $log, $multi);
    }

    /**
     * find all files by regex pattern recursively in a specified directory
     *
     * Example Result:
     *
     * <?xml version="1.0" encoding="UTF-8"?>
     * <files array="true">
     *   <file count="1" name="6215.png" size="50038" ctime="1412334379" mtime="1412334379">/var/mip/6000-6999/6215.png</file>
     * </files>
     *
     * @param $string (regex)
     * @param $path
     * @return DOMDocument
     */
    public static function getFilesRecursiveByPattern($string, $path)
    {
        $basedir = Mage::getBaseDir();
        if(!isset(self::$_filesByPath[$path])){
            $dir_iterator = new RecursiveDirectoryIterator($basedir.DS.trim($path, '/'));
            $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

            self::$_filesByPath[$path] = array();
            foreach ($iterator as $file) {
                if(!$file->isFile() || !$file->isReadable()){
                    continue;
                }
                self::$_filesByPath[$path][] = $file;
            }
        }

        $xml = new DOMDocument("1.0", "UTF-8");
        $elem = $xml->createElement('files');
        $elem->setAttribute('array', 'true');
        $xml->appendChild( $elem );
        $counter = 0;

        foreach(self::$_filesByPath[$path] as $file){
            /** @var $file SplFileInfo */
            if(!preg_match('#'.$string.'#', $file->getFilename())){
                continue;
            }

            $counter = $counter + 1;
            $subNode = $xml->createElement('file');
            $subNode->setAttribute('count', $counter);
            $subNode->setAttribute('name', $file->getFilename());
            $subNode->setAttribute('size', $file->getSize());
            $subNode->setAttribute('ctime', $file->getCTime());
            $subNode->setAttribute('mtime', $file->getMTime());
            $subNode->appendChild($xml->createTextNode(substr($file, strlen($basedir))));
            $elem->appendChild($subNode);
        }
        return $xml;
    }
}