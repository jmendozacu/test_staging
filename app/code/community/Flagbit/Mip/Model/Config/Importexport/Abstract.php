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
class Flagbit_Mip_Model_Config_Importexport_Abstract extends Varien_Object implements Flagbit_Mip_Model_Config_Importexport_Interface
{

    protected $_type = null;
    protected $_code = null;
    protected $_settings = null;
    protected $_mode = self::MODE_LIVE;

    const MODE_LIVE = 1;
    const MODE_QUEUE = 2;


    protected function _construct()
    {
    }

    public function getMode()
    {
        return $this->_mode;
    }

    public function setMode($mode)
    {
        switch($mode) {

            case self::MODE_LIVE:
                $this->_mode = $mode;
                break;

            case self::MODE_QUEUE:
                $this->_mode = $mode;
                $this->setSettings(array(
                    'trigger' => 'queue',
                    'que_only' => null,
                    'adapter' => 'default',
                    'is_queueable' => null
                ));
                break;
        }

        return $this;
    }

    /**
     * @return Flagbit_Mip_Model_Config_Importexport_Abstract
     */
    public function addLastTaskData()
    {
        /** @var $taskCollection Flagbit_Mip_Model_Mysql4_Task_Collection */
        $taskCollection = Mage::getResourceModel('mip/task_collection');
        $taskCollection->addFieldToFilter('type', $this->getType())
                       ->addFieldToFilter('code', $this->getCode())
                       ->addFieldToFilter('parent_task_id', 0)
                       ->setOrder('executed_at', Flagbit_Mip_Model_Mysql4_Task_Collection::SORT_ORDER_DESC)
                       ->getSelect()->limit(1);

        $parentTaskItem = $taskCollection->load()->getFirstItem();

        $taskCollection = Mage::getResourceModel('mip/task_collection');
        $taskCollection->addFieldToFilter('type', $this->getType())
                       ->addFieldToFilter('code', $this->getCode())
                       ->addFieldToFilter('parent_task_id', $parentTaskItem->getId())
                       ->setOrder('executed_at', Flagbit_Mip_Model_Mysql4_Task_Collection::SORT_ORDER_DESC);

        $taskItem = $taskCollection->load()->getFirstItem();

        // only one Task
        if($taskItem->hasData()){
            $this->addData(array(
                'status'        => $taskItem->getStatus(),
                'executed_at'   => $taskItem->getExecutedAt(),
                'finished_at'   => $taskItem->getFinishedAt(),
                'messages'      => $taskItem->getMessages(),
                'last_task_id'  => $taskItem->getId()
            ));

        // Task with Subtasks
        }else{
            $this->addData(array(
                'status'        => $taskItem->getStatus(),
                'executed_at'   => $parentTaskItem->getExecutedAt(),
                'finished_at'   => $taskItem->getFinishedAt(),
                'messages'      => $taskItem->getMessages(),
                'last_task_id'  => $taskItem->getId()
            ));
        }

        $queueSize = 0;
        if(is_object($taskItem) && $parentTaskItem->hasData()){
            /** @var $queueCollection Flagbit_Mip_Model_Mysql4_Data_Queue_Collection */
            $queueCollection = Mage::getResourceModel('mip/data_queue_collection');
            $queueSize = $queueCollection->addFieldToFilter('parent_task_id', $parentTaskItem->getId())->count();
        }
        $this->setQueueSize($queueSize);
        if($queueSize > 0){
            $this->setStatus('running');

            $leadTime = (time() - strtotime($this->getExecutedAt())) ;
            if($taskCollection->count() > 0){
                $leadTime = $leadTime / $taskCollection->count();
            }
            $leadTime = $leadTime * ($taskCollection->count()+$queueSize);

            $this->setFinishedAt(date('Y-m-d H:i:s', time()+$leadTime));
            $this->setLeadTime($leadTime);
        }

        return $this;
    }

    /**
     * get current Type
     *
     * @return null|string
     */
    public function getType()
    {
        if($this->_type === null){
            $this->_type = strtolower(ltrim(strrchr(get_class($this),"_"), '_'));
        }
        return $this->_type;
    }

    public function loadByCode($code)
    {
        $this->setCode($code);
        $this->setType($this->getType());
        $this->setInterface($this->getSettings('interface'));
        $this->_code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * is Action queueable
     *
     * @return boolean
     */
    public function isQueueable()
    {
        return $this->getSettings('is_queueable') ? true : false;
    }

    /**
     * set Settings
     *
     * @param $data
     * @return Flagbit_Mip_Model_Config_Importexport_Interface
     */
    public function setSettings(array $data)
    {
        if(is_array($this->getSettings())){
            $this->_settings = array_merge($this->getSettings(), $data);
        }else{
            $this->_settings = $data;
        }
        return $this;
    }

    /**
     * get Setting
     *
     * @param null $key
     * @return null
     */
    public function getSettings($key = null)
    {
        if($this->_settings === null){
            if($this->getCode()){
                $config = $this->_getConfig()->getXpath('./'.$this->getType().'/'.$this->getCode().'/settings');
                if(is_array($config)
                    && isset($config[0])
                    && $config[0] instanceof Mage_Core_Model_Config_Element){

                    $config = $config[0];
                    $this->_settings = $config->asArray();
                }
            }
        }
        $returnVal = $this->_settings;
        if($key !== null){
            $returnVal = isset($this->_settings[$key]) ? $this->_settings[$key] : null;
            if(is_string($returnVal)){
                $flag = strtolower($returnVal);
                if($flag == 'true'){
                    $returnVal = true;
                }elseif($flag == 'false'){
                    $returnVal = false;
                }
            }
        }
        return $returnVal;
    }

    /**
     * get all Codes from current Type
     *
     * @return array
     */
    public function getAllCodes()
    {
        $codes = array();
        if($this->_getConfig()->getNode($this->getType()) instanceof Mage_Core_Model_Config_Element
            && is_array($this->_getConfig()->getNode($this->getType())->asArray())){
            $codes = array_keys($this->_getConfig()->getNode($this->getType())->asArray());
        }
        return $codes;
    }


    /**
     * get MIP Config
     *
     * @return Flagbit_Mip_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('mip/config');
    }

}
