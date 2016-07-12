<?php
/**
 * This source file is subject to the Magento Integration Platform License
 * that is bundled with this package in the file LICENSE_MIP.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.flagbit.de/license/mip
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
class Flagbit_Mip_Model_Resource_Importexport_Product extends Mage_Importexport_Model_Import_Entity_Product implements Flagbit_Mip_Model_Resource_Interface{

    protected $_currentItem = array();
    protected $countOutput = 0;
    protected $_dataCount = 0;
    protected $_attrSetIdToName = null;
    protected $_attributeBySetCache = array();

    protected $_systemAttributes = array('visibility', 'price');


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_dataSourceModel = self::getDataSourceModel();
    }


    public function saveItems($data)
    {
       $importData = array();
        foreach($data as $product){
            $productsData = array();
            $categoryIdsString = '';
            Zend_Debug::dump($product);

            foreach($this->_getAttributesByAttributeset($product['set']) as $attribute){
                if(!isset($product[$attribute->getAttributeCode()])){
                    continue;
                }
                $productsData[$attribute->getAttributeCode()] = $product[$attribute->getAttributeCode()];
            }
            foreach($this->_systemAttributes as $attributeCode){
                if(!isset($product[$attributeCode])){
                    continue;
                }
                $productsData[$attributeCode] = $product[$attributeCode];
            }

            $productsData['sku'] = $product['sku'];
            $productsData['_type'] = $product['type'];
            $productsData['_attribute_set'] = is_numeric($product['set']) ? $this->_getAttributesetNamebyId($product['set']) : $product['set'];
            $productsData['_store'] = $product['store_ids'][0];

            $this->_addDataRow($productsData);

            // handle Category Relations
            if(isset($product['category_ids'])){
                $categoryIdsString = implode(',',
                    $this->getRelationModel()->getCollection()->convert2MageIds(
                        Mage::helper('mip')->trimExplode(',', $product['category_ids']),
                        Flagbit_Mip_Model_Resource_Category::RELATION_TYPE
                    )
                );
            }
            if (isset($product['category_ids_default'])){
                $categoryIdsString .= ','.$product['category_ids_default'];
            }
            $this->_addDataRow(array('_category' => Mage::helper('mip')->trimExplode(',', $categoryIdsString)));

            // handle websites
            $this->_addDataRow(array('_product_websites' => $product['website_ids']));

        }

        $this->_dataCount = count($data);
        Zend_Debug::dump($this->_dataSourceModel->getDataBunch());
        #return;
        return $this->_importData();
    }

    protected function _getAttributesByAttributeset($setId)
    {
        if(!isset($this->_attributeBySetCache[$setId])){
            $this->_attributeBySetCache[$setId] = Mage::getResourceModel('eav/entity_attribute_collection')->setAttributeSetFilter($setId);
        }
        return $this->_attributeBySetCache[$setId];
    }

    protected function _addDataRow($data)
    {
        if(isset($data[key($data)]) && is_array($data[key($data)])){
            foreach($data as $key => $subKeyData){
                foreach($subKeyData as $subData){
                    $this->_dataSourceModel->addDataBunchRow(array('sku' => '', '_type' => '', '_attribute_set' => '', '_store' => '', $key => $subData));
                }
            }
        }else{
            $this->_dataSourceModel->addDataBunchRow(array_merge(array('sku' => '', '_type' => '', '_attribute_set' => '', '_store' => ''), $data));
        }
        return $this;
    }

    /**
     * Validate data rows and save bunches to DB.
     *
     * @return Mage_Importexport_Model_Import_Entity_Abstract
     */
    protected function _saveValidatedBunches()
    {
        return $this;
    }

    /**
     * data source model getter.
     *
     * @static
     * @return Flagbit_Mip_Model_Resource_Importexport_Import_Data
     */
    public static function getDataSourceModel()
    {
        return Mage::getSingleton('mip/resource_importexport_import_data');
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $result = parent::validateRow($rowData, $rowNum);
        $this->_currentItem = $rowData;
        $sku = isset($rowData[self::COL_SKU]) ? $rowData[self::COL_SKU] : 'unknown';
        $this->countOutput = ($rowNum + 1).'/'.$this->_dataCount.' - '.round(100/$this->_dataCount * ($rowNum + 1), 0).'% ';

        if($result){
            if (isset($this->_oldSku[$sku])){
                Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' UPDATE Product ('.$sku.')');
            }else{
                Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' INSERT Product ('.$sku.')');
            }
        }
        return $result;
    }

    /**
     * Add error with corresponding current data source row number.
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number.
     * @param string $colName OPTIONAL Column name.
     * @return Mage_Importexport_Model_Import_Adapter_Abstract
     */
    public function addRowError($errorCode, $errorRowNum, $colName = null)
    {
        $sku = isset($this->_currentItem[self::COL_SKU]) ? $this->_currentItem[self::COL_SKU] : 'unknown';

        if(isset($this->_messageTemplates[$errorCode])){
           $errorCode = sprintf($this->_messageTemplates[$errorCode], $colName);
        }

        Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Product ('.$sku.') Import Error: '.$errorCode.' '.$colName);
        return parent::addRowError($errorCode, $errorRowNum, $colName);
    }

    /**
    * Initialize categories text-path to ID hash.
    *
    * @return Mage_Importexport_Model_Import_Entity_Product
    */
    protected function _initCategories()
    {
        $collection = Mage::getResourceModel('catalog/category_collection');
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        foreach ($collection as $category) {
         $this->_categories[$category->getId()] = $category->getId();
        }
        return $this;
    }

    /**
    * Initialize website values.
    *
    * @return Mage_Importexport_Model_Import_Entity_Product
    */
    protected function _initWebsites()
    {
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites() as $website) {
            $this->_websiteCodeToId[$website->getId()] = $website->getId();
            $this->_websiteCodeToStoreIds[$website->getCode()] = array_flip($website->getStoreCodes());
        }
        return $this;
    }

   /**
    * Override for the Option to set the Temp Directory
    *
    * @param string $fileName
    * @return string
    */
    protected function _uploadMediaFiles($fileName)
    {
        if($this->_fileDirectory){
            $this->_getUploader()->setTmpDir($this->_fileDirectory);
        }

        try {
            $res = $this->_getUploader()->move($fileName);
            return $res['file'];
        } catch (Exception $e) {
            return '';
        }
    }

    /**
    * Initialize attribute sets id-to-code pairs.
    *
    * @return string
    */
    protected function _getAttributesetNamebyId($id)
    {
         if($this->_attrSetNameToId === null){
            foreach (Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($this->_entityTypeId) as $attributeSet) {
                $this->_attrSetIdToName[$attributeSet->getId()] = $attributeSet->getCode();
            }
         }
        return isset($this->_attrSetIdToName[$id]) ? $this->_attrSetIdToName[$id] : '';
    }

    /**
     * Enter description here...
     *
     * @return Flagbit_Mip_Model_Relation
     */
    protected function getRelationModel(){

        return Mage::getModel('mip/data_relation');

    }

}