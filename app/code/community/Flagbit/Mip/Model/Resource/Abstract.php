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
class Flagbit_Mip_Model_Resource_Abstract implements Flagbit_Mip_Model_Resource_Interface
{

    protected $_storeIdSessionField   = 'store_id';

    /**
     * Default ignored attribute codes per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array(
        'global'    =>  array('entity_id', 'attribute_set_id', 'entity_type_id')
    );

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array();

    /*
     * @var Flagbit_Mip_Model_Relation
     */
    protected $_relation = null;

    /**
     * Attributes map array per entity type
     *
     * @var google
     */
    protected $_attributesMap = array(
        'global'    => array()
    );

    protected $_datahashIdentifier = null;

    /**
     * Resource configuration
     *
     * @var Varien_Simplexml_Element
     */
    protected $_resourceConfig = null;

    /**
     * Retrieve webservice session
     *
     * @return Mage_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('mip/session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Flagbit_Mip_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('mip/config');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Flagbit_Mip_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('mip/server');
    }

    /**
     * get Interface Config
     *
     * @param string $key
     */
    protected function _getInterfaceConfig($key = null){

        $interfaces = $this->_getConfig()->getInterfaces();

        if($key == null){
            return $interfaces[$this->_getServer()->getTrigger()->getInterface()];
        }else{
            return $interfaces[$this->_getServer()->getTrigger()->getInterface()]->{$key};
        }
    }

    /**
     * Set configuration for api resource
     *
     * @param Varien_Simplexml_Element $xml
     * @return Mage_Api_Model_Resource_Abstract
     */
    public function setResourceConfig(Varien_Simplexml_Element $xml)
    {
        $this->_resourceConfig = $xml;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Flagbit_Mip_Model_Relation
     */
    protected function getRelationModel(){

        return Mage::getModel('mip/data_relation');

    }

    /**
     * Save Import Status
     *
     * @param string $status
     */
    protected function saveStatus($status){

        if($this->_relation instanceof Flagbit_Mip_Model_Relation){

            $this->_relation->setUpdatedAt(now())
                            ->setStatus($status)
                            ->save();
        }
    }

    /**
     * ger Relation by Resource
     *
     * @param int $resourceId
     * @param string $resourceType
     * @param int $mageId
     * @param string $mageType
     * @return Flagbit_Mip_Model_Relation
     */
    protected function getRelationbyResourceId($resourceId, $resourceType = null, $mageId = null, $mageType = null){

        if($resourceType === null){
            $resourceType = $this->getRelationType();
        }

        if($mageType === null){
            $mageType = $this->getRelationType();
        }

        if($mageId === null){
            $mageId = $resourceId;
        }

        return $this->getRelationModel()->loadRelationbyResourceId($resourceId, $resourceType, $mageId, $mageType);

    }

    /**
     * get Relation by external ID
     *
     * @param mixed $extId
     * @param string $resourceType
     * @param string $mageType
     * @param int $mageId
     * @return Flagbit_Mip_Model_Relation
     */
    protected function getRelationByExtId($extId, $resourceType = null, $mageType = null, $mageId = null){

        if($resourceType === null){
            $resourceType = $this->getRelationType();
        }

        if($mageType === null){
            $mageType = $this->getRelationType();
        }

        $relation = $this->getRelationModel()->loadRelationByExtId($extId, $resourceType, $mageType, $mageId);

        return $relation;
    }

    /**
     * create a new Relation
     *
     * @param mixed $extId
     * @param int $resourceId
     * @param string $resourceType
     * @param int $mageId
     * @param string $mageType
     * @param string $datahashIdentifier
     * @return Flagbit_Mip_Model_Relation
     */
    protected function createRelation($extId, $resourceId, $resourceType = null, $mageId = null, $mageType = null, $datahashIdentifier = null)
    {
        if($mageId === null){
            $mageId = $resourceId;
        }

        if($resourceType == null){
            $resourceType = $this->getRelationType();
        }

        if($mageType === null){
            $mageType = $this->getRelationType();
        }

        $relation =  $this->getRelationModel()->setData(
            array(
                'mage_id' => $mageId,
                'mage_type' => $mageType,
                'resource_id' => $resourceId,
                'resource_type' => $resourceType,
                'ext_id' => $extId,
                'interface' => $this->_getServer()->getTrigger()->getInterface(),
                'datahash_identifier' => $datahashIdentifier ? $datahashIdentifier : $this->_datahashIdentifier,
                'created_at' => now(),
                'updated_at' => now()
            )
        );

        try {
            $relation->save();
        }
        catch(Zend_Db_Statement_Exception $e) {
            if($e->getCode() == 1062) {
                $relation = $this->getRelationbyResourceId($resourceId, $resourceType, $mageId, $mageType);
            }else{
                throw $e;
            }
        }

        if($resourceType == $this->getRelationType()
            && $mageType == $this->getRelationType()){
            $this->_relation = $relation;
        }

        return $relation;
    }

    /**
     * Retrieve configuration for api resource
     *
     * @return Varien_Simplexml_Element
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Dispatches fault
     *
     * @param string $code
     */
    protected function _fault($code, $customMessage=null)
    {
        $e = new Flagbit_Mip_Model_Exception($code, $customMessage);
        Mage::helper('mip/log')->getWriter($this)->error($customMessage, $e);
        throw $e;
    }

    /**
     * Retrives store id from store code, if no store id specified,
     * it use seted session or admin store
     *
     * @param string|int $store
     * @return int
     */
    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                        ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault('store_not_exists');
        }

        return $storeId;
    }
} // Class Mage_Api_Model_Resource_Abstract End