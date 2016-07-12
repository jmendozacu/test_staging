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
class Flagbit_Mip_Model_Data_Relation extends Mage_Core_Model_Abstract
{

    const EXCEPTION_DUPLICATE_RELATION = 1;

    protected function _construct()
    {
        $this->_init('mip/data_relation');
    }


    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        try{
            parent::save();
        }catch (Exception $e){
            if(strpos($e->getMessage(), 'Duplicate entry') !== false) {
                throw new Exception('Duplicate Relation '.$this->getMageType().'/'.$this->getResourceType().': '.$this->getExtId().' (ext_id) -> '.$this->getResourceId().' (res_id) '.$e->getMessage(), self::EXCEPTION_DUPLICATE_RELATION);
            }
        }
        return $this;
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
     * delete Relations by Datarecord (Id and Type)
     *
     * @param int|array $mageId
     * @param string $mageType
     */
    public function deleteByDatarecord($mageId, $mageType){

        $collection = $this->getCollection()
            ->addFieldToFilter('mage_id', is_array($mageId) ? array('in' => $mageId) : $mageId)
            ->addFieldToFilter('mage_type', $mageType)
            ->load()
            ->delete();
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function loadRelationbyResourceId($resourceId, $resourceType, $mageId = null, $mageType = null)
    {
        if($mageType === null){
            $mageType = $resourceType;
        }

        if($mageId === null){
            $mageId = $resourceId;
        }

        $this->_getResource()->loadRelationbyResourceId($this, $resourceId, $resourceType, $mageId, $mageType, $this->_getServer()->getTrigger()->getInterface());
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function loadRelationByExtId($extId, $resourceType, $mageType = null, $mageId = null)
    {
        if($mageType === null){
            $mageType = $resourceType;
        }

        $this->_getResource()->loadRelationByExtId($this, $extId, $resourceType, $mageType, $mageId, $this->_getServer()->getTrigger()->getInterface());
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Processing object before delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeDelete()
    {

        // delete Datahashes
        if($this->getDatahashIdentifier()){

            Mage::getSingleton('mip/data_hash')
                ->load($this->getDatahashIdentifier(), 'identifier')
                ->delete();
        }

        return parent::_beforeDelete();
    }

    /**
     * Processing object after delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDelete()
    {
        // delete all child relations
        if($this->getMageId() == $this->getResourceId()){
            $this->deleteByDatarecord($this->getMageId(), $this->getMageType());
        }

        return parent::_afterDelete();
    }

    /**
     * Data validation, before save
     *
     * @return Flagbit_Mip_Model_Data_Relation
     */
    protected function _beforeSave(){

        if($this->getId()){
            return parent::_beforeSave();
        }

        if(!$this->getData('resource_id')
            or !$this->getData('resource_type')
            or !$this->getData('ext_id') ){

            Mage::throwException('invalid_relation_data');

        }

        if(!$this->getData('mage_type')){
            $this->setData('mage_type', $this->getData('resource_type'));
        }

        if(!$this->getData('mage_id')){
            $this->setData('mage_id', $this->getData('resource_id'));
        }

        return parent::_beforeSave();
    }

}
