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
class Flagbit_Mip_Model_Tools {

    protected $_attributeOptions;
    protected $_attributeByCode;
    protected $_attributeById;
    protected $_options;
    protected $_IdMapping;
    protected $_fieldToIdMapping;
    protected $_logHistory = array();
    protected $_storeId = null;

    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Wrapper for mb_strtoupper
     *
     * @param string $string
     */
    public function strtoupper($string)
    {
        return mb_strtoupper($string);
    }

    /**
     * normalize Characters
     * Example: ü -> ue
     *
     * @param string $string
     * @return string
     */
    public function normalize($string)
    {
        return Mage::helper('mip')->normalize($string);
    }

    /**
     * get field value by entity id
     *
     * @param string $type Data Type, for Example: product, attribute, attributeset
     * @param string $field Value Field Name
     * @param int $id int entity_id which should be converted to $field value
     * @return string | null
     */
    public function getFieldValueById($type, $field, $id)
    {
        if(!isset($this->_fieldToIdMapping[$type][$field])) {

            switch ($type) {

                case "customer":
                    $attribute = Mage::getModel('eav/entity_attribute')->loadbyCode($type, $field);
                    $customerCol = Mage::getResourceModel('customer/customer_collection');
                    $idsSelect = $customerCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);

                    if($attribute->getBackendType() == 'static' || $attribute->getBackendType() === null){
                        $idsSelect->from(null, array('e.'.$customerCol->getEntity()->getIdFieldName(), 'e.'.$field));
                        $idsSelect->resetJoinLeft();
                    }else{
                        $idsSelect->columns('e.'.$customerCol->getEntity()->getIdFieldName());
                        $customerCol->addAttributeToSelect($field, 'inner');
                    }
                    $this->_fieldToIdMapping[$type][$field] = $customerCol->getConnection()->fetchPairs($idsSelect, array());
                    break;

                case "product":
                    $attribute = Mage::getModel('eav/entity_attribute')->loadbyCode('catalog_'.$type, $field);
                    $productCol = Mage::getResourceModel('catalog/product_collection');
                    $idsSelect = $productCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);
                    $idsSelect->resetJoinLeft();

                    if($attribute->getBackendType() == 'static' || $attribute->getBackendType() === null){
                        $idsSelect->from(null, array('e.'.$productCol->getEntity()->getIdFieldName(), 'e.'.$field));
                        $idsSelect->resetJoinLeft();
                    }else{
                        $idsSelect->columns('e.'.$productCol->getEntity()->getIdFieldName());
                        $productCol->addAttributeToSelect($field, 'inner');
                    }

                    $result = $productCol->getConnection()->fetchPairs($idsSelect, array());
                    $result = array_map('trim', $result);
                    $this->_fieldToIdMapping[$type][$field] = $result;
                    break;

                case "attribute":
                    $attributeCol = Mage::getResourceModel('eav/entity_attribute_collection');

                    $idsSelect = clone $attributeCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);
                    $idsSelect->from(null, array('main_table.attribute_id', 'main_table.'.$field));
                    $idsSelect->resetJoinLeft();

                    $this->_fieldToIdMapping[$type][$field] = $attributeCol->getConnection()->fetchPairs($idsSelect, array());
                    break;

                case "attributeset":
                    $attributeCol = Mage::getResourceModel('eav/entity_attribute_set_collection');

                    $idsSelect = clone $attributeCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);
                    $idsSelect->from(null, array('main_table.attribute_set_id', 'main_table.'.$field));
                    $idsSelect->where('entity_type_id = ?', Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId());

                    $idsSelect->resetJoinLeft();

                    $this->_fieldToIdMapping[$type][$field] = $attributeCol->getConnection()->fetchPairs($idsSelect, array());
                    break;

                default:
                    throw new Flagbit_Mip_Model_Exception('filter_id_type_not_exists', 'Filter "getFieldValueById" Type '.$type.' not exists');
            }
        }

        if(isset($this->_fieldToIdMapping[$type][$field][$id])) {
            return $this->_fieldToIdMapping[$type][$field][$id];
        }
        return null;
    }

    /**
     * get ID Filter
     * can handle products, attributes and attributesets
     *
     * @param string $type Data Type, for Example: product, attribute, attributeset
     * @param string $field Value Field Name
     * @param string $value string value which should be converted to ID
     * @param string $default
     * @return int | null
     * @throws Flagbit_Mip_Model_Exception
     */
    public function getId($type, $field, $value, $default = null)
    {

        if(!isset($this->_IdMapping[$type][$field])) {

            switch ($type) {

                case "customer":
                    $attribute = Mage::getModel('eav/entity_attribute')->loadbyCode($type, $field);
                    $customerCol = Mage::getResourceModel('customer/customer_collection');
                    $idsSelect = $customerCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);

                    if($attribute->getBackendType() == 'static' || $attribute->getBackendType() === null){
                        $idsSelect->from(null, array('e.'.$customerCol->getEntity()->getIdFieldName(), 'e.'.$field));
                        $idsSelect->resetJoinLeft();
                    }else{
                        $idsSelect->columns('e.'.$customerCol->getEntity()->getIdFieldName());
                        $customerCol->addAttributeToSelect($field, 'inner');
                    }
                    $this->_IdMapping[$type][$field] = array_flip($customerCol->getConnection()->fetchPairs($idsSelect, array()));
                    break;

                case "product":
                    $attribute = Mage::getModel('eav/entity_attribute')->loadbyCode('catalog_'.$type, $field);
                    $productCol = Mage::getResourceModel('catalog/product_collection');
                    $idsSelect = $productCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);
                    $idsSelect->resetJoinLeft();

                    if($attribute->getBackendType() == 'static' || $attribute->getBackendType() === null){
                        $idsSelect->from(null, array('e.'.$productCol->getEntity()->getIdFieldName(), 'e.'.$field));
                        $idsSelect->resetJoinLeft();
                    }else{
                        $idsSelect->columns('e.'.$productCol->getEntity()->getIdFieldName());
                        $productCol->addAttributeToSelect($field, 'inner');
                    }

                    $result = $productCol->getConnection()->fetchPairs($idsSelect, array());
                    $result = array_map('trim', $result);
                    $this->_IdMapping[$type][$field] = array_flip($result);
                    break;

                case "attribute":
                    $attributeCol = Mage::getResourceModel('eav/entity_attribute_collection');

                    $idsSelect = clone $attributeCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);
                    $idsSelect->from(null, array('main_table.'.$field, 'main_table.attribute_id'));
                    $idsSelect->resetJoinLeft();

                    $this->_IdMapping[$type][$field] = $attributeCol->getConnection()->fetchPairs($idsSelect, array());
                    break;

                case "attributeset":
                    $attributeCol = Mage::getResourceModel('eav/entity_attribute_set_collection');

                    $idsSelect = clone $attributeCol->getSelect();
                    $idsSelect->reset(Zend_Db_Select::ORDER);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
                    $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
                    $idsSelect->reset(Zend_Db_Select::COLUMNS);
                    $idsSelect->from(null, array('main_table.'.$field, 'main_table.attribute_set_id'));
                    $idsSelect->where('entity_type_id = ?', Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId());

                    $idsSelect->resetJoinLeft();

                    $this->_IdMapping[$type][$field] = $attributeCol->getConnection()->fetchPairs($idsSelect, array());
                    break;

                default:
                    throw new Flagbit_Mip_Model_Exception('filter_id_type_not_exists', 'Filter "getid" Type '.$type.' not exists');
            }
        }

        if(isset($this->_IdMapping[$type][$field][$value])) {
            return $this->_IdMapping[$type][$field][$value];
        }
        else{
            if($default !== null){
                return $default;
            }
            Mage::helper('mip/log')->getWriter($this)->warn('FILTER getid ('.$type.') field="'.$field.'" value= "'.$value.'" not found');
        }

        return null;
    }


    /**
     * get mipmanager Config Value
     *
     * @param string $field
     * @param string $group
     * @param string $module
     * @param string $store
     * @return string
     */
    public function getConfigValue($field, $group = null, $module = null, $store = null)
    {
        $return = null;

        if (empty($module)) {
            $module = 'mip_core';
        }
        if (!empty($group) && !empty($field)) {
            if(empty($store)){
                $store = $this->_storeId;
            }

            $return = Mage::helper('mip/data')->getModuleConfig($module, $group, $field, $store);
        }

        return $return;
    }

    /**
     * get filetime
     *
     * @param array $construction
     */
    public function getFiletime($file)
    {

        $file = Mage::getBaseDir().trim($file);
        if(!file_exists($file)){
            return;
        }
        return filemtime($file);
    }


    /**
     * get Attribute Option Values by Text
     *
     * @param string $attributeCode
     * @param string $text
     * @param string $default
     * @param int $store
     * @param string $compareType
     * @param boolean $log
     * @return Ambiguous|string
     */
    public function getAttributeOptionValues($attributeCode, $text, $default = null, $store = 0, $compareType = null, $log = false, $multi = false)
    {

        /*@var $attribute Mage_Catalog_Model_Entity_Attribute*/
        $attribute = $this->getAttribute($attributeCode);

        if($attribute->getData('frontend_input') == 'select' or $attribute->getData('frontend_input') == 'multiselect') {

            $text2values = $this->getAttributeOptions($attribute->getId(), $store);
            $values2text = array_flip($text2values);

            if($multi){
                $textArray = Mage::helper('mip')->trimExplode(',', $text);
            }else{
                $textArray = array($text);
            }

            $returnValues = array();
            foreach($textArray as $textEntry) {

                switch($compareType) {

                    case 'search_attribute_text':
                        $resultArray = array_keys(preg_grep('/'.preg_quote($textEntry, '/').'/i',$values2text));

                        if(count($resultArray)) {
                            $returnValues = array_merge($returnValues, $resultArray);

                        }elseif($log == true) {
                            Mage::helper('mip/log')->getWriter($this)->warn('FILTER Attribute ('.$store.') "'.$attributeCode.'" value "'.$textEntry.'" not found');
                        }

                        break;

                    case 'in_value_text':
                        // not yet implemented
                        break;

                    default:
                        if(isset($text2values[$textEntry])) {
                            $returnValues[] = $text2values[$textEntry];

                        }elseif($log == true) {

                            if(!isset($this->_logHistory[__FUNCTION__][$attributeCode][$textEntry])){
                                $this->_logHistory[__FUNCTION__][$attributeCode][$textEntry] = true;
                                Mage::helper('mip/log')->getWriter($this)->warn('FILTER Attribute ('.$store.') "'.$attributeCode.'" value "'.$textEntry.'" not found');
                            }
                        }
                        break;
                }
            }

            if(!empty($default) && !count($returnValues)) {
                return $default;
            }

            return implode(',', array_unique($returnValues));
        }

        return $text;
    }


    /**
     * get Attribute
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttributeById($id) {

        if(!isset($this->_attributeById[$id])) {
            $this->_attributeById[$id] = Mage::getModel('catalog/resource_eav_attribute')->load($id);
            $this->_attributeByCode[$this->_attributeById[$id]->getAttributeCode()] = $this->_attributeById[$id];
        }
        return $this->_attributeById[$id];
    }

    /**
     * get Attribute
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($code) {

        if(!isset($this->_attributeByCode[$code])) {
            $this->_attributeByCode[$code] = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $code);
            $this->_attributeById[$this->_attributeByCode[$code]->getId()] = $this->_attributeByCode[$code];
        }
        return $this->_attributeByCode[$code];
    }

    /**
     * get Attribute Options
     *
     * @param int id
     * @param int $store
     * @return array
     */
    public function getAttributeOptions($id, $store=0) {

        if(!isset($this->_attributeOptions[$id][$store])) {

            if($store) {

                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($id)
                ->setStoreFilter($store, false)
                ->load();
            }else {

                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($id);

                $optionCollection->getSelect()
                ->join(array('store_default_value'=>Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value')),
            'store_default_value.option_id=main_table.option_id',
                array('default_value'=>'value'))
                ->joinLeft(array('store_value'=>Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value')),
            'store_value.option_id=main_table.option_id AND '.$optionCollection->getConnection()->quoteInto('store_value.store_id=?', 0),
                array('store_value'=>'value',
            'value' => new Zend_Db_Expr('IFNULL(store_value.value,store_default_value.value)')))
                ->where($optionCollection->getConnection()->quoteInto('store_default_value.store_id=?', 0));

                $optionCollection->load();
            }

            $text2values = array();
            foreach($optionCollection as $option) {
                $text2values[$option->getValue()] = $option->getOptionId();
            }

            $this->_attributeOptions[$id][$store] = $text2values;
        }
        return $this->_attributeOptions[$id][$store];
    }

    /**
     * get Options by Attribute Id
     *
     * @param int $id
     * @param int $store
     */
    protected function _getOption($id, $store = 0) {

        if (!isset($this->_options[$id][$store] )) {

            $this->_options[$id][$store] = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setIdFilter($id)
            ->setStoreFilter($store)
            ->getFirstItem();
        }

        return $this->_options[$id][$store];
    }
}
